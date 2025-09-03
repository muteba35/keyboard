<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Identifiants;
use App\Models\Notification;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Dossier;
use App\Models\User_logs;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Helpers\LoggerHelper;


class IdentifiantsController extends Controller
{

public function creation(): View{
     $userId = Auth::id();

    // Total pour les 4 boîtes
    $totalIdentifiants = Identifiants::count();
    $totalPasswords = Identifiants::count(); // si tu as une table à part, change-la
    $identifiantsForts = DB::table('notifications')
        ->where('user_id', $userId)
        ->whereIn('type', ['info', 'info modification'])
        ->count();
    $identifiantsFaibles = DB::table('notifications')
        ->where('user_id', $userId)
        ->whereIn('type', ['alert', 'alert modification'])
        ->count();

    // 🔷 Area chart: évolution par jour (label = date, data = nombre)
    $dataParJour = Identifiants::selectRaw('DATE(created_at) as date, COUNT(*) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $areaLabels = $dataParJour->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M j'))->toArray();
    $areaData = $dataParJour->pluck('total')->toArray();

    // 🔷 Bar chart: nombre par service
    $dataParService = Identifiants::select('service', DB::raw('COUNT(*) as total'))
        ->groupBy('service')
        ->get();

    $barLabels = $dataParService->pluck('service')->toArray();
    $barData = $dataParService->pluck('total')->toArray();

    // 🔷 Pie chart: fort vs faible
    $pieLabels = ['Forts', 'Faibles'];
    $pieData = [$identifiantsForts, $identifiantsFaibles];

    return view('onglets.fond-noir', compact(
        'totalIdentifiants',
        'totalPasswords',
        'identifiantsForts',
        'identifiantsFaibles',
        'areaLabels',
        'areaData',
        'barLabels',
        'barData',
        'pieLabels',
        'pieData'
    ));
}

    public function create(): View
    {
        return view('onglets.identifiants');
    }
   public function dashboard()
{
   $userId = Auth::id();

    // Total des identifiants actifs
    $totalIdentifiants = Identifiants::where('user_id', $userId)->count();

    $totalPasswords=Identifiants::where('user_id', $userId)->count();

    // Notifications liées uniquement aux identifiants encore existants
    $identifiantsIdsActifs = Identifiants::where('user_id', $userId)->pluck('id')->toArray();

    // Important : ici on suppose qu'il y a un champ identifiant_id dans ta table notifications
    $identifiantsForts = DB::table('notifications')
        ->where('user_id', $userId)
        ->whereIn('type', ['info', 'info modification'])
        ->whereIn('identifiant_id', $identifiantsIdsActifs)
        ->count();

    $identifiantsFaibles = DB::table('notifications')
        ->where('user_id', $userId)
        ->whereIn('type', ['alerte', 'alert modification'])
        ->whereIn('identifiant_id', $identifiantsIdsActifs)
        ->count();

    // Graphiques
    $dataParJour = Identifiants::where('user_id', $userId)
        ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $areaLabels = $dataParJour->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M j'))->toArray();
    $areaData = $dataParJour->pluck('total')->toArray();

    $dataParService = Identifiants::where('user_id', $userId)
        ->select('service', DB::raw('COUNT(*) as total'))
        ->groupBy('service')
        ->get();

    $barLabels = $dataParService->pluck('service')->toArray();
    $barData = $dataParService->pluck('total')->toArray();

    $pieLabels = ['Forts', 'Faibles'];
    $pieData = [$identifiantsForts, $identifiantsFaibles];

    return view('dashboard', compact(
        'totalIdentifiants',
        'totalPasswords',
        'identifiantsForts',
        'identifiantsFaibles',
        'areaLabels',
        'areaData',
        'barLabels',
        'barData',
        'pieLabels',
        'pieData'
    ));
}
    public function showImportPage()
{
    return view('onglets.import-excel');
}
public function tester(Request $request)
    {
        $password = $request->input('mot_de_passe');
        $analyse = $this->analyseMotDePasse($password);
        $user = Auth::user();

        // 1. CAS MOT DE PASSE COMPROMIS
       if (in_array("Compromis détecté ou hacké par les pirates", $analyse['messages'])) {
    // Mot de passe compromis
    Mail::send('emails.mdp_compromis', [
        'user' => $user,
    ], function ($message) use ($user) {
        $message->to($user->email)
                ->subject('🚨Mot de passe compromis');
    });

    return back()->with('analyse_result', [
        'status' => 'compromis',
        'message' => "Mot de passe compromis détecté 🚨."
    ]);

} elseif ($analyse['est_faible']) {
    // Mot de passe faible
    Mail::send('emails.mdp_faible', [
        'user' => $user,
        'messages' => $analyse['messages']
    ], function ($message) use ($user) {
        $message->to($user->email)
                ->subject('⚠️ Mot de passe faible');
    });

    return back()->with('analyse_result', [
        'status' => 'faible',
        'message' => $analyse['messages']
    ]);

} else {
    // Mot de passe fort
    Mail::send('emails.mdp_fort', [
        'user' => $user,
    ], function ($message) use ($user) {
        $message->to($user->email)
                ->subject('🔒 Mot de passe robuste');
    });

    return back()->with('analyse_result', [
        'status' => 'fort',
        'message' => 'Mot de passe robuste 💪.'
    ]);
}
    }
public function index()
    {
        return view('historique.index');
    }
public function securite_test(): View
    {
        return view('onglets.securite_test');
    }


    public function generatorindex()
    {
        return view('password.generator');
    }



    public function identifants_create(): View
    {
    
   $motDePasseGenere = session('mot_de_passe_genere');

    // Supprimer immédiatement après récupération (affichage unique)
    Session::forget('mot_de_passe_genere');
    return view('onglets.identifiants-create',compact('motDePasseGenere'));
    }

    public function showIdentifiants()
    {
        $identifiants = Identifiants::with('dossier')->where('user_id', Auth::id())->get();
       
        return view('onglets.identifiants', compact('identifiants'));
    }

    public function edit($id)
    {
        $identifiant = Identifiants::findOrFail($id);
        return view('onglets.identifiants-edit', compact('identifiant'));
    }

    public function messages_create(): View
    {
        $userId = Auth::id();

        Notification::where('user_id', $userId)->where('est_lu', false)->update(['est_lu' => true]);
        Session::put('notifications_non_lues', 0);

        $notifications = Notification::where('user_id', $userId)->whereNotNull('identifiant_id')->latest()->get();
        return view('onglets.messages', compact('notifications'));
    }

    public function index_logs()
{
    $logs = User_logs::where('user_id', Auth::id())
               ->orderBy('created_at', 'desc')
               ->paginate(10);

    return view('logs.index', compact('logs'));
}



public function import(Request $request)
{
    $request->validate([
        'excel_file' => 'required|file|mimes:csv,txt',
    ]);

    // 📁 Déplacement manuel dans le dossier 'storage/app/temp'
    $uploaded = $request->file('excel_file');
    $filename = time() . '-' . $uploaded->getClientOriginalName();
    $uploaded->move(storage_path('app/temp'), $filename);
    $fullPath = storage_path('app/temp/' . $filename);

    $userId = Auth::id();
    $user = Auth::user();

    $importés = 0;
    $erreurs = [];

    try {
        if (!file_exists($fullPath) || !is_readable($fullPath)) {
            throw new \Exception("Fichier non lisible.");
        }

        $handle = fopen($fullPath, 'r');
        if ($handle === false) throw new \Exception("Impossible d’ouvrir le fichier.");

        $header = fgetcsv($handle); // ligne d'en-tête
        if (!$header || count($header) < 7) {
            throw new \Exception("⚠️ Format invalide : 7 colonnes attendues (nom_utilisateur,email,mot_de_passe,service,url_service,nom_dossier,description).");
        }

        while (($row = fgetcsv($handle)) !== false) {
            $index = $importés + 2;

            if (count($row) < 7) {
                $erreurs[] = "Ligne $index : Données incomplètes (7 colonnes obligatoires).";
                continue;
            }

            [$nom_utilisateur, $email, $mot_de_passe, $service, $url_service, $nom_dossier, $description] = array_map('trim', $row);

            // ✅ Validation stricte
            if (empty($nom_utilisateur) || empty($email) || empty($mot_de_passe) ||  empty($service) || empty($url_service) || empty($nom_dossier) || empty($description)) {
                $erreurs[] = "Ligne $index : Tous les champs doivent être remplis.";
                continue;
            }

            // 🌐 Validation URL
            $expectedDomains = [
                'instagram' => 'instagram.com',
                'facebook' => 'facebook.com',
                'twitter' => 'twitter.com',
                'linkedin' => 'linkedin.com',
                'gmail' => 'mail.google.com',
                'yahoo' => 'yahoo.com',
                'github' => 'github.com',
            ];

            $url_service = strtolower($url_service);
            if (!Str::startsWith($url_service, 'https://') ||
                (isset($expectedDomains[$service]) && !Str::contains($url_service, $expectedDomains[$service]))) {
                $erreurs[] = "Ligne $index : URL incorrecte pour le service $service.";
                continue;
            }

            // 🔁 Vérification doublon
            $exists = Identifiants::where('user_id', $userId)
                ->where('service', $service)
                ->where('nom_utilisateur', $nom_utilisateur)
                ->where('url_service', $url_service)
                ->exists();

            if ($exists) {
                $erreurs[] = "Ligne $index : Identifiant déjà existant.";
                continue;
            }

            // 🔒 Cryptage
            $encrypted = Crypt::encrypt($mot_de_passe);
            $len = strlen($encrypted);
            $part1 = substr($encrypted, 0, intdiv($len, 3));
            $part2 = substr($encrypted, intdiv($len, 3), intdiv($len, 3));
            $part3 = substr($encrypted, intdiv($len, 3) * 2);

            // 📁 Création ou récupération du dossier
            $dossier = Dossier::firstOrCreate(
                ['nom' => $nom_dossier, 'user_id' => $userId],
                ['description' => $description]
            );

            // 📝 Insertion de l’identifiant
            $identifiant = Identifiants::create([
                'user_id' => $userId,
                'dossier_id' => $dossier->id,
                'nom_utilisateur' => $nom_utilisateur,
                'email' => $email,
                'service' => $service,
                'url_service' => $url_service,
'mot_de_passe' => $part1,
                'description' => $description,
            ]);

            // 📂 Sauvegarde du part2
            $storagePath = storage_path("app/Identifiants_services/");
            if (!File::exists($storagePath)) File::makeDirectory($storagePath, 0755, true);
            File::put("{$storagePath}{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt", $part2);

            // ☁️ Envoi Firebase
            $this->storeInFirebase($userId, $part3, $service, $identifiant->id);

            // 🔍 Analyse
            $this->reconstruireEtAnalyserMotDePasse($identifiant, 'import');

            $importés++;
        }

        fclose($handle);

        if ($importés === 0 && count($erreurs) === 0) {
            return back()->withErrors(['import_error' => "⚠️ Fichier vide ou non traité correctement. Vérifie le contenu du fichier CSV."]);
        }

        // 📧 Rapport
        $message = "📥 Rapport d’importation CSV : $importés ligne(s) insérée(s).\n";
        foreach ($erreurs as $e) {
            $message .= "- $e\n";
            Log::warning("[IMPORT CSV] $e");
        }

        Mail::raw($message, function ($mail) use ($user) {
            $mail->to($user->email)->subject("📁 Rapport d’importation");
        });

        return back()
            ->with('import_success', "$importés identifiant(s) importé(s) avec succès ✅")
            ->with('import_errors', $erreurs);

    } catch (\Throwable $e) {
        Log::error("[IMPORT CSV] Erreur critique : " . $e->getMessage());
        return back()->withErrors(['import_error' => "🚨 Erreur critique : " . $e->getMessage()]);
    }
}
    public function afficherIdentifiants()
{
    $userId = Auth::id();
    $identifiants = Identifiants::where('user_id', $userId)->with('dossier')->get();

    // 🔁 Initialisation unique de Firebase (hors de la boucle)
    try {
        $firebase = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'))
            ->createDatabase();
    } catch (\Throwable $e) {
        Log::error("Erreur d'initialisation Firebase : " . $e->getMessage());
        return view('erreurs.firebase', ['message' => "Impossible de se connecter à Firebase."]);
    }

    foreach ($identifiants as $identifiant) {
        $service = strtolower($identifiant->service);
        $fragment1 = $identifiant->mot_de_passe;

        // 🔍 Lecture du fichier (fragment2)
        $filePath = storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt");
        $fragment2 = File::exists($filePath) ? File::get($filePath) : null;

        // 🔥 Récupération Firebase (fragment3)
        $fragment3 = null;
        try {
            $snapshot = $firebase
                ->getReference("Identifiants_services/{$service}/{$userId}/{$identifiant->id}")
                ->getValue();
            $fragment3 = $snapshot['fragment3'] ?? null;
        } catch (\Throwable $e) {
            Log::error("Erreur Firebase (récupération fragment3) : " . $e->getMessage());
        }

        // 🧩 Vérification des fragments
        if (!$fragment1 || !$fragment2 || !$fragment3) {
            $identifiant->mot_de_passe_clair = 'Fragments manquants';
            continue;
        }

        // 🔓 Décryptage du mot de passe
        try {
            $crypted = $fragment1 . $fragment2 . $fragment3;
            $decrypted = Crypt::decrypt($crypted);
            $identifiant->mot_de_passe_clair = $decrypted;
        } catch (\Throwable $e) {
            Log::error("Erreur décryptage identifiant ID={$identifiant->id} : " . $e->getMessage());
            $identifiant->mot_de_passe_clair = 'Erreur de décryptage';
        }
    }

    return view('onglets.identifiants', compact('identifiants'));
}
     public function destroy_identifiants(Request $request, $id)
    {
        $userId = Auth::id();
        $identifiant = Identifiants::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $service = strtolower($identifiant->service);

        $part2Path = storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt");
        if (File::exists($part2Path)) File::delete($part2Path);

        try {
            $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
            $factory->createDatabase()->getReference("Identifiants_services/{$service}/{$userId}/{$identifiant->id}")->remove();
        } catch (\Throwable $e) {
            Log::error("Erreur Firebase: " . $e->getMessage());
        }

        $identifiant->delete();
LoggerHelper::logAction(
    'delete',
    'Suppression de l’identifiant "' . $identifiant->nom_utilisateur . '" pour le service "' . $identifiant->service . '"',
    'Identifiants',
    $identifiant->id
    );
        return redirect()->route('identifiants')->with('identifiant_supp', 'Identifiant supprimé avec succès.');
    }
    public function exporter(Request $request)
    {
        $format = $request->get('format');
        $user = Auth::user();
        $userId = $user->id;

        $identifiants = Identifiants::where('user_id', $userId)->with('dossier')->get();

        foreach ($identifiants as $identifiant) {
            $service = strtolower($identifiant->service);
            $fragment1 = $identifiant->mot_de_passe;
            $fragment2 = File::get(storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt"));
            $fragment3 = $this->getFragmentFromFirebase($userId, $service, $identifiant->id);

            try {
                $decrypted = Crypt::decrypt($fragment1 . $fragment2 . $fragment3);
            } catch (\Throwable $e) {
                $decrypted = 'Erreur de décryptage';
            }

            $identifiant->mot_de_passe_clair = $decrypted ?? 'Fragments manquants';
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.identifiants_pdf', compact('user', 'identifiants'));
            return $pdf->download('mes_identifiants.pdf');
        }

        if ($format === 'word') {
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $section->addTitle("🔐 Identifiants confidentiels de {$user->name}", 1);
            $section->addTextBreak(1);

            $phpWord->addTableStyle('IdentifiantsTable', ['borderSize' => 6, 'borderColor' => '999999'], ['bgColor' => 'cccccc']);
            $table = $section->addTable('IdentifiantsTable');

            $table->addRow();
            $table->addCell(1800)->addText('Service');
            $table->addCell(1800)->addText("Nom d'utilisateur");
            $table->addCell(2500)->addText('Email');
            $table->addCell(2500)->addText('URL');
            $table->addCell(3000)->addText('Mot de passe');
            $table->addCell(2500)->addText('Dossier');
            $table->addCell(2000)->addText('Description');
            $table->addCell(2000)->addText('Ajouté le');

            foreach ($identifiants as $identifiant) {
                $table->addRow();
                $table->addCell(1800)->addText($identifiant->service);
                $table->addCell(1800)->addText($identifiant->nom_utilisateur);
                $table->addCell(2500)->addText($identifiant->email);
                $table->addCell(2500)->addText($identifiant->url_service);
                $table->addCell(3000)->addText($identifiant->mot_de_passe_clair);
                $table->addCell(2500)->addText($identifiant->dossier->nom ?? 'Aucun');
                $table->addCell(2000)->addText($identifiant->description);
                $table->addCell(2000)->addText($identifiant->created_at->format('d/m/Y'));
            }

            $section->addTextBreak(2);
            $section->addText("Fichier généré automatiquement par le Gestionnaire d'identifiants Junior Muteba.", ['italic' => true]);

            $tempFile = tempnam(sys_get_temp_dir(), 'identifiants_word');
            $phpWord->save($tempFile, 'Word2007');
            return response()->download($tempFile, 'identifiants_'.$user->name.'.docx')->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Format non supporté.');
    }

    public function motDePasseGenere(Request $request)
{
    $request->validate([
        'mot_de_passe_genere' => 'required|string|min:8',
    ]);

    Session::put('mot_de_passe_genere', $request->mot_de_passe_genere);
    return redirect()->route('identifiants-create');
}

    public function getToken(Request $request)
    {
        $token = session('extension_token');
        if (!$token) return response()->json(['message' => 'Token non trouvé en session'], 404);

        return response()->json([
            'token' => $token,
            'user_id' => $request->user()->id,
        ]);
    }

    public function parSite(Request $request)
    {
        $bearerToken = $request->bearerToken();
        if (!$bearerToken) return response()->json(['message' => 'Token manquant'], 401);
        $plainToken = explode('|', $bearerToken)[1] ?? $bearerToken;
        $hashedToken = hash('sha256', $plainToken);

$tokenRecord = DB::table('personal_access_tokens')->where('token', $hashedToken)->first();
        if (!$tokenRecord) return response()->json(['message' => 'Token invalide'], 401);

        $userModel = User::find($tokenRecord->tokenable_id);
        if (!$userModel) return response()->json(['message' => 'Utilisateur non trouvé'], 404);

        $userId = $userModel->id;
        $site = $request->query('site');
        if (!$site) return response()->json(['message' => 'Paramètre site manquant'], 400);

        $domainParts = explode('.', $site);
        $serviceName = strtolower($domainParts[count($domainParts) - 2]);

        $correspondances = [
        'youtube' => 'google',
        'meta' => 'facebook',
        'outlook' => 'microsoft',
        'hotmail' => 'microsoft',
    ];
        $serviceName = $correspondances[$serviceName] ?? $serviceName;

        $identifiant = Identifiants::where('user_id', $userId)
            ->whereRaw('LOWER(service) LIKE ?', ["%$serviceName%"])
            ->first();

        if (!$identifiant) return response()->json(['message' => "Aucun identifiant trouvé pour : $serviceName"], 404);

        $service = strtolower($identifiant->service);
        $fragment1 = $identifiant->mot_de_passe;
        $fragment2 = File::get(storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt"));
        $fragment3 = $this->getFragmentFromFirebase($userId, $service, $identifiant->id);

        try {
            $motDePasseClair = Crypt::decrypt($fragment1 . $fragment2 . $fragment3);
        } catch (\Throwable $e) {
            $motDePasseClair = 'Erreur de décryptage';
        }

        return response()->json([
            'email' => $identifiant->email ?? $identifiant->nom_utilisateur,
            'mot_de_passe' => $motDePasseClair,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_utilisateur' => ['required', 'string', 'max:255'],
            'mot_de_passe' => 'required|string|min:10',
            'email' => ['required', 'string', 'email', 'max:250'],
            'service' => ['required', 'string', 'max:255'],
            'url_service' => ['nullable', 'url'],
            'nom_dossier' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $identifiant = Identifiants::findOrFail($id);
        $userId = Auth::id();
        $service = strtolower($request->service);
        $encryptedPassword = Crypt::encrypt($request->mot_de_passe);

        $length = strlen($encryptedPassword);
        $part1 = substr($encryptedPassword, 0, intdiv($length, 3));
        $part2 = substr($encryptedPassword, intdiv($length, 3), intdiv($length, 3));
        $part3 = substr($encryptedPassword, intdiv($length, 3) * 2);

        $filePath = storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt");
        File::put($filePath, $part2);
        $this->storeInFirebase($userId, $part3, $service, $identifiant->id);

        $identifiant->update([
            'nom_utilisateur' => $request->nom_utilisateur,
            'email' => $request->email,
            'service' => $request->service,
            'url_service' => $request->url_service,
            'mot_de_passe' => $part1,
            'description' => $request->description,
        ]);

        $this->reconstruireEtAnalyserMotDePasse($identifiant, 'modification');


    LoggerHelper::logAction(
    'modification',
    'Modification de l’identifiant "' . $identifiant->nom_utilisateur . '" pour le service "' . $identifiant->service . '"',
    'Identifiants',
    $identifiant->id
       
);
 return back()->with('identifiant_modif', 'Identifiant modifié avec succès !');

 
    
 }
 
public function store(Request $request)
{
    // ✅ Validation de base
    $request->validate([
        'nom_utilisateur' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_\-\.@]{5,}$/'],
        'mot_de_passe' => 'required|string|min:10',
        'email' => ['required', 'string', 'email', 'max:250'],
        'service' => ['required', 'string', 'max:255'],
        'url_service' => ['nullable', 'url'],
        'nom_dossier' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-_]{3,}$/'],
        'description' => ['nullable', 'string'],
    ]);

    $service = strtolower($request->service);
    $url = strtolower($request->url_service);
    $userId = Auth::id();

    // ✅ Dictionnaire des services attendus
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

    // ✅ Vérification stricte de l’URL
    if (!empty($url)) {
        // L’URL doit commencer par « https:// »
        if (!Str::startsWith($url, 'https://')) {
            return back()->withErrors(['url_service' => "L’URL doit commencer par « https:// »"])->withInput();
        }

        // L’URL doit être valide
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return back()->withErrors(['url_service' => "L’URL n’est pas valide."])->withInput();
        }

        // L’URL doit contenir le domaine du service attendu
        if (isset($expectedDomains[$service]) && !Str::contains($url, $expectedDomains[$service])) {
            return back()->withErrors([
                'url_service' => "L’URL ne correspond pas au service « $service » attendu (« {$expectedDomains[$service]} »)."
            ])->withInput();
        }
    }

    // 🛡️ Vérifie que l'e-mail correspond au domaine du service (pour les services de messagerie uniquement)
    $servicesAvecEmailsPropriete = ['gmail', 'yahoo', 'hotmail', 'outlook'];

    if (in_array($service, $servicesAvecEmailsPropriete) && !empty($request->email)) {
        $expectedDomain = explode('.', $expectedDomains[$service])[0]; // ex: "gmail"
        if (!Str::contains(strtolower($request->email), $expectedDomain)) {
            return back()->withErrors([
                'email' => "L’adresse e-mail ne semble pas correspondre au service « $service » attendu (ex: @{$expectedDomain}.com)."
            ])->withInput();
        }
    }

    // ✅ Empêcher les doublons (même utilisateur, service, e-mail, URL)
    $exists = Identifiants::where('user_id', $userId)
        ->where('service', $request->service)
        ->where('email', $request->email)
        ->where('url_service', $request->url_service)
        ->exists();

    if ($exists) {
        return back()->withErrors(['nom_utilisateur' => "Cet identifiant existe déjà."])->withInput();
    }

    // ✅ Cryptage et séparation du mot de passe
    $encryptedPassword = Crypt::encrypt($request->mot_de_passe);
    $length = strlen($encryptedPassword);
    $part1 = substr($encryptedPassword, 0, intdiv($length, 3));
    $part2 = substr($encryptedPassword, intdiv($length, 3), intdiv($length, 3));
    $part3 = substr($encryptedPassword, intdiv($length, 3) * 2);

    // ✅ Création ou récupération du dossier
    $nomDossier = $request->nom_dossier ?? 'Par défaut';
    $dossier = Dossier::firstOrCreate(
        ['nom' => $nomDossier, 'user_id' => $userId],
        ['description' => $request->description]
    );

    // ✅ Création de l’identifiant
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

    // ✅ Enregistrement sécurisé du fragment 2 dans le stockage local
$storagePath = storage_path("app/Identifiants_services/");
    if (!File::exists($storagePath)) {
        File::makeDirectory($storagePath, 0755, true);
    }
    File::put($storagePath . "{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt", $part2);

    // ✅ Enregistrement du fragment 3 dans Firebase
    $this->storeInFirebase($userId, $part3, $service, $identifiant->id);

    // ✅ Analyse automatique du mot de passe
    $this->reconstruireEtAnalyserMotDePasse($identifiant, 'ajout');

    // ✅ Nettoyage de session
    Session::forget('mot_de_passe_genere');

    // ✅ Logging de l'action
    LoggerHelper::logAction(
        'ajout',
        'Ajout de l’identifiant "' . $identifiant->nom_utilisateur . '" pour le service "' . $identifiant->service . '"',
        'Identifiants',
        $identifiant->id
    );

    return back()->with('identifiant_succes', 'Identifiant ajouté avec succès !');
}

    public function storeInFirebase($userId, $fragment3, $service, $identifiantId): void
    {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        $factory->createDatabase()->getReference("Identifiants_services/{$service}/{$userId}/{$identifiantId}")->set(['fragment3' => $fragment3]);
    }

    public function getFragmentFromFirebase($userId, $service, $identifiantId)
    {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

$snapshot = $factory->createDatabase()->getReference("Identifiants_services/{$service}/{$userId}/{$identifiantId}")->getSnapshot();
return $snapshot->exists() ? ($snapshot->getValue()['fragment3'] ?? null) : null;
    }

 public function reconstruireEtAnalyserMotDePasse(Identifiants $identifiant, $contexte = 'ajout')
{
    $userId = Auth::id();
    $service = strtolower($identifiant->service);
    $fragment1 = $identifiant->mot_de_passe;
    $fragment2 = File::get(storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}-{$identifiant->id}.txt"));
    $fragment3 = $this->getFragmentFromFirebase($userId, $service, $identifiant->id);

    $crypted = $fragment1 . $fragment2 . $fragment3;
    $motDePasse = Crypt::decrypt($crypted);
    $result = $this->analyseMotDePasse($motDePasse);

    $notificationController = new NotificationController();
    $now = now()->format('d/m/Y à H:i');

    $typeNotif = match ($contexte) {
        'modification' => $result['est_faible'] ? 'alerte_modification' : 'info_modification',
        'import'       => $result['est_faible'] ? 'alerte_import' : 'info_import',
        default        => $result['est_faible'] ? 'alerte' : 'info',
    };

    $existe = Notification::where('user_id', $userId)
        ->where('identifiant_id', $identifiant->id)
        ->where('type', $typeNotif)
        ->exists();

    if (!$existe) {
        $message = $result['est_faible']
            ? "🔧 {$contexte} du {$now} — Le mot de passe pour le service \"{$identifiant->service}\" (utilisateur : {$identifiant->nom_utilisateur}) est faible : " . implode(', ', $result['messages']) . " — Junior Muteba"
            : "🔧 {$contexte} du {$now} — Le mot de passe pour \"{$identifiant->service}\" (utilisateur : {$identifiant->nom_utilisateur}) est fort et sécurisé. — Junior Muteba";

        Notification::create([
            'user_id' => $userId,
            'identifiant_id' => $identifiant->id,
            'message' => $message,
            'type' => $typeNotif,
            'est_lu' => false,
        ]);

        // 📬 Envoi de mail entouré de try/catch pour éviter le blocage
        try {
            if ($result['est_faible']) {
                match ($contexte) {
                    'modification' => $notificationController->envoyerMotDePasseInsecureModifie($result['messages'], $identifiant->service, $identifiant->nom_utilisateur),
                    'import'       => $notificationController->envoyerMotDePasseInsecureImport($result['messages'], $identifiant->service, $identifiant->nom_utilisateur),
                    default        => $notificationController->envoyerMotDePasseInsecure($result['messages'], $identifiant->service, $identifiant->nom_utilisateur),
                };
            } else {
                match ($contexte) {
                    'modification' => $notificationController->envoyerMotDePasseValideModifie($identifiant->service, $identifiant->nom_utilisateur),
                    'import'       => $notificationController->envoyerMotDePasseValideImport($identifiant->service, $identifiant->nom_utilisateur),
                    default        => $notificationController->envoyerMotDePasseValide($identifiant->service, $identifiant->nom_utilisateur),
                };
            }
        } catch (\Exception $e) {
            // 💥 Log de l’erreur au lieu de planter
            Log::error("Erreur lors de l'envoi d'email : " . $e->getMessage());
        }

        Session::put('notifications_non_lues', Notification::where('user_id', $userId)->where('est_lu', false)->count());
    }
}
     protected function analyseMotDePasse(string $password): array
    {
        $faible = false;
        $messages = [];

        if (strlen($password) < 10) $messages[] = "Trop court";
        if (!preg_match('/[A-Z]/', $password)) $messages[] = "Pas de majuscule";
        if (!preg_match('/[a-z]/', $password)) $messages[] = "Pas de minuscule";
        if (!preg_match('/\d/', $password)) $messages[] = "Pas de chiffre";
        if (!preg_match('/[^a-zA-Z\d]/', $password)) $messages[] = "Pas de caractère spécial";
        if ($this->motDePasseCompromis($password)) $messages[] = "Compromis détecté ou hacké par les pirates";

        return ['est_faible' => count($messages) > 0, 'messages' => $messages];
    }

    protected function motDePasseCompromis(string $password): bool
    {
        $sha1 = strtoupper(sha1($password));
        $prefix = substr($sha1, 0, 5);
        $suffix = substr($sha1, 5);
        $response = Http::get("https://api.pwnedpasswords.com/range/{$prefix}");

        return $response->successful() && str_contains($response->body(), $suffix);
    }
}