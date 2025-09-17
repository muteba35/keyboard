<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Identifiants;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;

class IdentifiantsApiController extends Controller
{
    // 🔹 Liste des identifiants de l'utilisateur
    public function index()
    {
        $userId = Auth::id();
        $identifiants = Identifiants::where('user_id', $userId)->get();
        return response()->json($identifiants);
    }

    // 🔹 Création d'un nouvel identifiant
    public function store(Request $request)
    {
        $request->validate([
            'nom_utilisateur' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_\-\.@]{5,}$/'],
            'mot_de_passe' => 'required|string|min:10',
            'email' => ['required', 'string', 'email', 'max:250'],
            'service' => ['required', 'string', 'max:255'],
            'url_service' => ['nullable', 'url'],
            'nom_dossier' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-_]{3,}$/'],
            'description' => ['nullable', 'string'],
        ]);

        $userId = Auth::id();
        $service = strtolower($request->service);
        $url = strtolower($request->url_service);

        $expectedDomains = [
            'instagram' => 'instagram.com',
            'facebook' => 'facebook.com',
            'twitter' => 'twitter.com',
            'linkedin' => 'linkedin.com',
            'gmail' => 'gmail.com',
            'yahoo' => 'yahoo.com',
            'hotmail' => 'hotmail.com',
            'outlook' => 'outlook.com',
        ];

        // Vérification URL
        if (!empty($url)) {
            if (!Str::startsWith($url, 'https://')) {
                return response()->json(['error' => "L’URL doit commencer par « https:// »"], 422);
            }
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return response()->json(['error' => "L’URL n’est pas valide"], 422);
            }
            if (isset($expectedDomains[$service]) && !Str::contains($url, $expectedDomains[$service])) {
                return response()->json(['error' => "L’URL ne correspond pas au service « $service » attendu"], 422);
            }
        }

        // Vérification email pour services de messagerie
        $servicesAvecEmails = ['gmail', 'yahoo', 'hotmail', 'outlook'];
        if (in_array($service, $servicesAvecEmails) && !empty($request->email)) {
            $expectedDomain = explode('.', $expectedDomains[$service])[0];
            if (!Str::contains(strtolower($request->email), $expectedDomain)) {
                return response()->json(['error' => "L’adresse e-mail ne correspond pas au service « $service »"], 422);
            }
        }

        // Empêcher doublons
        $exists = Identifiants::where('user_id', $userId)
            ->where('service', $request->service)
            ->where('email', $request->email)
            ->where('url_service', $request->url_service)
            ->exists();
        if ($exists) {
            return response()->json(['error' => "Cet identifiant existe déjà"], 422);
        }

        // 🔹 Cryptage et fragmentation du mot de passe
        $encryptedPassword = Crypt::encrypt($request->mot_de_passe);
        $length = strlen($encryptedPassword);
        $part1 = substr($encryptedPassword, 0, intdiv($length, 3));
        $part2 = substr($encryptedPassword, intdiv($length, 3), intdiv($length, 3));
        $part3 = substr($encryptedPassword, intdiv($length, 3) * 2);

        // Création ou récupération du dossier
        $nomDossier = $request->nom_dossier ?? 'Par défaut';
        $dossier = Dossier::firstOrCreate(
            ['nom' => $nomDossier, 'user_id' => $userId],
            ['description' => $request->description]
        );

        // Création de l’identifiant
        $identifiant = Identifiants::create([
            'user_id' => $userId,
            'dossier_id' => $dossier->id,
            'nom_utilisateur' => $request->nom_utilisateur,
            'email' => $request->email,
            'service' => $request->service,
            'url_service' => $request->url_service,
            'mot_de_passe' => $part1,
            'description' => $request->description,
        ]);

        // Stockage fragment2 local
        $storagePath = storage_path("app/Identifiants_services/");
        if (!File::exists($storagePath)) File::makeDirectory($storagePath, 0755, true);
        File::put($storagePath . "{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt", $part2);

        // Stockage fragment3 dans Firebase
        $this->storeInFirebase($userId, $part3, $service, $identifiant->id);

        return response()->json(['message' => 'Identifiant ajouté avec succès', 'identifiant' => $identifiant], 201);
    }

    // 🔹 Affichage d’un identifiant
    public function show($id)
    {
        $userId = Auth::id();
        $identifiant = Identifiants::where('user_id', $userId)->findOrFail($id);

        // Reconstruction mot de passe
        $service = strtolower($identifiant->service);
        $fragment1 = $identifiant->mot_de_passe;
        $fragment2 = File::get(storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt"));
        $fragment3 = $this->getFragmentFromFirebase($userId, $service, $identifiant->id);

        $motDePasse = Crypt::decrypt($fragment1.$fragment2.$fragment3);

        return response()->json([
            'identifiant' => $identifiant,
            'mot_de_passe_clair' => $motDePasse
        ]);
    }

    // 🔹 Mise à jour
    public function update(Request $request, $id)
    {
        $identifiant = Identifiants::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nom_utilisateur' => ['required', 'string', 'max:255'],
            'mot_de_passe' => 'required|string|min:10',
            'email' => ['required', 'string', 'email', 'max:250'],
            'service' => ['required', 'string', 'max:255'],
            'url_service' => ['nullable', 'url'],
            'nom_dossier' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        // 🔹 Fragmentation du nouveau mot de passe
        $encryptedPassword = Crypt::encrypt($request->mot_de_passe);
        $length = strlen($encryptedPassword);
        $part1 = substr($encryptedPassword, 0, intdiv($length, 3));
        $part2 = substr($encryptedPassword, intdiv($length, 3), intdiv($length, 3));
        $part3 = substr($encryptedPassword, intdiv($length, 3) * 2);

        $service = strtolower($request->service);
        $userId = Auth::id();

        File::put(storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt"), $part2);
        $this->storeInFirebase($userId, $part3, $service, $identifiant->id);

        $identifiant->update([
            'nom_utilisateur' => $request->nom_utilisateur,
            'email' => $request->email,
            'service' => $request->service,
            'url_service' => $request->url_service,
            'mot_de_passe' => $part1,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Identifiant mis à jour', 'identifiant' => $identifiant]);
    }

    // 🔹 Suppression
    public function destroy($id)
    {
        $identifiant = Identifiants::where('user_id', Auth::id())->findOrFail($id);
        $service = strtolower($identifiant->service);
        $userId = Auth::id();

        File::delete(storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt"));
        // Supprimer Firebase
        $factory = (new Factory())->withServiceAccount(env('FIREBASE_CREDENTIALS'))->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        $factory->createDatabase()->getReference("Identifiants_services/{$service}/{$userId}/{$identifiant->id}")->remove();

        $identifiant->delete();

        return response()->json(['message' => 'Identifiant supprimé']);
    }

    // 🔹 Firebase
    private function storeInFirebase($userId, $fragment3, $service, $identifiantId): void
    {
        $factory = (new Factory())->withServiceAccount(env('FIREBASE_CREDENTIALS'))->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        $factory->createDatabase()->getReference("Identifiants_services/{$service}/{$userId}/{$identifiantId}")->set(['fragment3' => $fragment3]);
    }

    private function getFragmentFromFirebase($userId, $service, $identifiantId)
    {
        $factory = (new Factory())->withServiceAccount(env('FIREBASE_CREDENTIALS'))->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        $snapshot = $factory->createDatabase()->getReference("Identifiants_services/{$service}/{$userId}/{$identifiantId}")->getSnapshot();
        return $snapshot->exists() ? ($snapshot->getValue()['fragment3'] ?? null) : null;
    }
}
