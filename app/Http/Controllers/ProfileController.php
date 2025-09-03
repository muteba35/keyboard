<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http; // Pour Firebase
use Kreait\Firebase\Factory; 

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Mise à jour des informations
        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
{
    $request->validateWithBag('userDeletion', [
        'password' => [
            'required',
            function ($attribute, $value, $fail) use ($request) {
                $user = $request->user();
                $userId = $user->id;

                // Lecture des 3 parties
                $part1 = $user->password;

                // Lecture de la 2e partie depuis le fichier
                $part2Path = storage_path("app/password_parts/user_{$userId}_part2.txt");
                if (!File::exists($part2Path)) {
                    return $fail("Fragment de mot de passe manquant (Part 2).");
                }
                $part2 = File::get($part2Path);

                // Lecture de la 3e partie depuis Firebase
                try {
                    $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))
                                            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
                    $firebase = $factory->createDatabase();
                    $snapshot = $firebase->getReference("password_fragments/{$userId}")->getSnapshot();

                    if (!$snapshot->exists() || !isset($snapshot->getValue()['fragment3'])) {
                        return $fail("Fragment de mot de passe manquant (Part 3).");
                    }

                    $part3 = $snapshot->getValue()['fragment3'];
                } catch (\Throwable $e) {
                    return $fail("Erreur d'accès à Firebase.");
                }

                // Reconstruction du mot de passe crypté
                $encryptedPassword = $part1 . $part2 . $part3;

                try {
                    $decryptedPassword = Crypt::decrypt($encryptedPassword);
                    if ($value !== $decryptedPassword) {
                        $fail("Le mot de passe est incorrect.");
                    }
                } catch (\Exception $e) {
                    $fail("Impossible de vérifier le mot de passe.");
                }
            }
        ],
    ]);

    $user = $request->user();

    Auth::logout();

    // Supprimer l’utilisateur
    $user->delete();

    // Supprimer les fragments du mot de passe
    File::delete(storage_path("app/password_parts/user_{$user->id}_part2.txt"));
    try {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))
                                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        $firebase = $factory->createDatabase();
        $firebase->getReference("password_fragments/{$user->id}")->remove();
    } catch (\Throwable $e) {
        // Optionnel : log erreur Firebase
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to('/login')->with('status', 'account-deleted');
}
}