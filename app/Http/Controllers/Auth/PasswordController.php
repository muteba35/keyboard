<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Kreait\Firebase\Factory;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
     public function update(Request $request): RedirectResponse
    {
       $request->validateWithBag('updatePassword', [
        'current_password' => ['required'],
        'password' => [
            'required',
            'confirmed',
            PasswordRule::min(10)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised(),
        ],
    ]);

        $user = $request->user();

        // Récupération des fragments du mot de passe actuel
        $fragment1 = $user->password;

        $fragment2Path = storage_path('app/password_parts/user_' . $user->id . '_part2.txt');
        $fragment2 = File::get($fragment2Path);

        $fragment3 = $this->getFragmentFromFirebase($user->id);

        $fullEncryptedPassword = $fragment1 . $fragment2 . $fragment3;
        $decryptedPassword = Crypt::decrypt($fullEncryptedPassword);

        if ($request->current_password !== $decryptedPassword) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ])->with('status', 'password-update-failed');
        }

        // Nouveau mot de passe
        $newPassword = $request->password;
        $encryptedNewPassword = Crypt::encrypt($newPassword);

        // Fragmentation
        $fragment1 = substr($encryptedNewPassword, 0, 100);
        $fragment2 = substr($encryptedNewPassword, 100, 100);
        $fragment3 = substr($encryptedNewPassword, 200);

        // Mise à jour base MySQL
        $user->update([
            'password' => $fragment1,
        ]);

        // Mise à jour Storage (part2)
        $path = storage_path('app/password_parts/user_' . $user->id . '_part2.txt');
        File::put($path, $fragment2);

        // Mise à jour Firebase (part3)
        $this->updateFragmentToFirebase($user->id, $fragment3);

        return back()->with('status', 'password-updated');
    }

    private function getFragmentFromFirebase($userId)
    {
        $factory = (new Factory())
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $database = $factory->createDatabase();

        $snapshot = $database
            ->getReference('password_fragments/' . $userId)
            ->getSnapshot();

        return $snapshot->getValue()['fragment3'] ?? '';
    }

    private function updateFragmentToFirebase($userId, $fragment)
    {
        $factory = (new Factory())
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $database = $factory->createDatabase();

        $database->getReference('password_fragments/' . $userId)
            ->set([
                'fragment3' => $fragment,
            ]);
    }
    
}
