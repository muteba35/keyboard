<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Kreait\Firebase\Factory; // <-- Important pour Firebase

class RegisteredUserController extends Controller
{
    /**
     * Affiche la vue d’inscription.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Gère une requête d’inscription avec fragmentation du mot de passe.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^(?![\d]+$)(?!.*[\s])(?=.{2,})(?=(?:.*[a-zA-Z]){2,})^[a-zA-Z0-9]+$/',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'phone' => [
                'required',
                'digits:9',
                'regex:/^[0-9]{9}$/',
                'unique:users,phone'
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(10)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                function ($attribute, $value, $fail) use ($request) {
                    $emailPrefix = Str::before($request->email, '@');
                    if (Str::contains($value, $emailPrefix)) {
                        $fail('Le mot de passe ne doit pas contenir une partie de votre email.');
                    }
                },
            ],
        ], [
            'name.regex' => 'Le nom doit contenir au moins 2 lettres sans espace.',
            'phone.regex' => 'Le numéro doit être 9 chiffres valides après +243.',
            'phone.unique' => 'Ce numéro est déjà utilisé.',
        ]);

        // 2. Cryptage complet du mot de passe
        $encryptedPassword = Crypt::encrypt($request->password);

        // 3. Fragmentation en trois parties égales
        $length = strlen($encryptedPassword);
        $part1 = substr($encryptedPassword, 0, intdiv($length, 3));
        $part2 = substr($encryptedPassword, intdiv($length, 3), intdiv($length, 3));
        $part3 = substr($encryptedPassword, intdiv($length, 3) * 2);

        // 4. Création de l'utilisateur en stockant part1 dans la base de données
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => '+243' . $request->phone,
            'password' => $part1, // On stocke le premier fragment dans MySQL
        ]);

        // 5. Sauvegarde du deuxième fragment dans storage/
        $path = storage_path('app/password_parts/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        File::put($path . "user_{$user->id}_part2.txt", $part2);

        // 6. Sauvegarde du troisième fragment dans Firebase
        $this->storeInFirebase($user->id, $part3);

        // 7. Finalisation
        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('login');
    }

    /**
     * Stocke le troisième fragment du mot de passe dans Firebase.
     */
    protected function storeInFirebase($userId, $fragment3): void
    {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))
                                ->withDatabaseUri(env( 'FIREBASE_DATABASE_URL'));

        $database = $factory->createDatabase();
    
        $database->getReference('password_fragments/' . $userId)
                 ->set(['fragment3' => $fragment3]);
    }
}