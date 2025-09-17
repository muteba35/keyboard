<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Factory;
use Illuminate\View\View;

class AuthController extends Controller
{
    // 🔹 API Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^(?![\d]+$)(?!.*[\s])(?=.{2,})(?=(?:.*[a-zA-Z]){2,})^[a-zA-Z0-9]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|digits:9|regex:/^[0-9]{9}$/|unique:users,phone',
            'password' => 'required|string|confirmed|min:10',
        ]);

        // 1. Cryptage complet du mot de passe
        $encryptedPassword = Crypt::encrypt($request->password);

        // 2. Fragmentation
        $length = strlen($encryptedPassword);
        $part1 = substr($encryptedPassword, 0, intdiv($length, 3));
        $part2 = substr($encryptedPassword, intdiv($length, 3), intdiv($length, 3));
        $part3 = substr($encryptedPassword, intdiv($length, 3) * 2);

        // 3. Création utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => '+243'.$request->phone,
            'password' => $part1, // fragment1
        ]);

        // 4. Sauvegarde fragment2 dans storage
        $path = storage_path('app/password_parts/');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        File::put($path."user_{$user->id}_part2.txt", $part2);

        // 5. Sauvegarde fragment3 dans Firebase
        $this->storeInFirebase($user->id, $part3);

        // 6. Création du token Sanctum
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    // 🔹 API Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            throw ValidationException::withMessages(['email' => 'Identifiants invalides']);
        }

        // Reconstruction
        $fragment1 = $user->password;
        $path = storage_path("app/password_parts/user_{$user->id}_part2.txt");
        if (! File::exists($path)) {
            throw ValidationException::withMessages(['email' => 'Fragment 2 manquant']);
        }
        $fragment2 = File::get($path);

        $fragment3 = $this->getFragmentFromFirebase($user->id);
        if (is_null($fragment3)) {
            throw ValidationException::withMessages(['email' => 'Fragment 3 manquant']);
        }

        $encryptedPassword = $fragment1.$fragment2.$fragment3;
        $decryptedPassword = Crypt::decrypt($encryptedPassword);

        if ($request->password !== $decryptedPassword) {
            throw ValidationException::withMessages(['email' => 'Mot de passe incorrect']);
        }

        // Auth + token
        Auth::login($user);
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => $user,
        ]);
    }

    // 🔹 Déconnexion
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Déconnecté']);
    }

    // 🔹 Récupération des fragments dans Firebase
    private function getFragmentFromFirebase($userId): ?string
    {
        $factory = (new Factory())
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $database = $factory->createDatabase();
        $snapshot = $database->getReference("password_fragments/{$userId}")->getSnapshot();

        return $snapshot->exists() ? ($snapshot->getValue()['fragment3'] ?? null) : null;
    }

    private function storeInFirebase($userId, $fragment3): void
    {
        $factory = (new Factory())
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $database = $factory->createDatabase();
        $database->getReference("password_fragments/{$userId}")
                 ->set(['fragment3' => $fragment3]);
    }
}
