<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Mail;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = \App\Models\User::where('email', $this->email)->first();

          // 👇 AJOUT ICI
        if ($user && $user->is_blocked) {
            throw ValidationException::withMessages([
                'email' => 'Ce compte est bloqué suite à plusieurs tentatives échouées.',
            ]);
        }

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Reconstruction des fragments
        $fragment1 = $user->password;

        $path = storage_path('app/password_parts/user_' . $user->id . '_part2.txt');
        if (! File::exists($path)) {
            throw ValidationException::withMessages([
                'email' => 'Password fragment missing (part2)',
            ]);
        }
        $fragment2 = File::get($path);

        $fragment3 = $this->getFragmentFromFirebase($user->id);
        if (is_null($fragment3)) {
            throw ValidationException::withMessages([
                'email' => 'Password fragment missing (part3)',
            ]);
        }

        // Reconstruction complète
        $encryptedPassword = $fragment1 . $fragment2 . $fragment3;

        // Décryptage
        $decryptedPassword = Crypt::decrypt($encryptedPassword);

        // Vérification
        if ($this->password !== $decryptedPassword) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

       Auth::login($user);

        // ✅ Remise à zéro des tentatives si login réussi
        $user->failed_attempts = 0;
        $user->last_failed_attempt_at = null;
        $user->save();

        RateLimiter::clear($this->throttleKey());
    }

    private function getFragmentFromFirebase($userId): ?string
    {
        $factory = (new Factory())
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $database = $factory->createDatabase();

        $snapshot = $database->getReference('password_fragments/' . $userId)->getSnapshot();

        if ($snapshot->exists()) {
            return $snapshot->getValue()['fragment3'] ?? null;
        }

        return null;
    }

public function ensureIsNotRateLimited(): void
{
    $user = \App\Models\User::where('email', $this->email)->first();

    // 1️⃣ Vérifie si l'utilisateur est déjà bloqué
    if ($user && $user->is_blocked) {
        if ($user->blocked_until && now()->lt($user->blocked_until)) {
            session()->flash('blocked_user', true);
            throw ValidationException::withMessages([
                'email' => 'Compte bloqué. Réessayez après 1 heure.',
            ]);
        } else {
            $user->is_blocked = false;
            $user->blocked_until = null;
            $user->failed_attempts = 0;
            $user->save();
        }
    }

    // 2️⃣ Comptage des tentatives échouées
    if ($user) {
        $user->failed_attempts += 1;
        $user->last_failed_attempt_at = now();
        $user->save();

        // 3️⃣ Alerte à la 3ème tentative + envoi email
        if ($user->failed_attempts === 3) {
            $seconds = 60;

            if (!RateLimiter::tooManyAttempts($this->throttleKey(), 1)) {
                RateLimiter::clear($this->throttleKey());
                RateLimiter::hit($this->throttleKey(), $seconds);
            }

            session()->flash('locked', RateLimiter::availableIn($this->throttleKey()));
            session()->flash('warning_attempts', "Vous avez fait 3 tentatives. Si ce n'est pas vous, modifiez votre mot de passe.");

            // ✉️ Envoi de l'email d’alerte
            Mail::send('emails.alert_login_attempt', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('⚠️ Tentatives de connexion suspectes détectées');
            });
        }

        // 4️⃣ Blocage à la 6e tentative
        if ($user->failed_attempts >= 6) {
            $user->is_blocked = true;
            $user->blocked_until = now()->addHour();
            $user->save();

            session()->flash('blocked_user', true);

            throw ValidationException::withMessages([
                'email' => '🚫 Compte bloqué après plusieurs tentatives. Réessayez après 1 heure.',
            ]);
        }
    }
}
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}