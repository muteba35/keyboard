<?php

namespace App\Jobs;

use App\Models\Identifiants;
use App\Models\Notification;
use App\Http\Controllers\NotificationController;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Factory;

class AnalyseMotDePasseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $identifiant;

    public function __construct(Identifiants $identifiant)
    {
        $this->identifiant = $identifiant;
    }

    public function middleware()
    {
        return [
            new WithoutOverlapping($this->identifiant->id),
            new ThrottlesExceptions(3, 1),
        ];
    }

    public function handle(): void
    {
        try {
            $identifiant = $this->identifiant;
            $userId = $identifiant->user_id; // Auth::id() ne marche pas dans un Job
            $service = strtolower($identifiant->service);
            $fragment1 = $identifiant->mot_de_passe;

            // Lire part2 (fichier local)
            $part2Path = storage_path("app/Identifiants_services/{$service}-identifiant-password-{$userId}.txt");
            if (!File::exists($part2Path)) {
                throw ValidationException::withMessages([
                    'mot_de_passe' => 'Fragment manquant (part2).',
                ]);
            }
            $fragment2 = File::get($part2Path);

            // Lire part3 (depuis Firebase)
            $fragment3 = $this->getFragmentFromFirebase($userId, $service);
            if (is_null($fragment3)) {
                throw ValidationException::withMessages([
                    'mot_de_passe' => 'Fragment manquant (part3).',
                ]);
            }

            // Reconstruction et déchiffrement
            $encryptedPassword = $fragment1 . $fragment2 . $fragment3;
            try {
                $decryptedPassword = Crypt::decrypt($encryptedPassword);
            } catch (\Exception $e) {
                Log::error("Décryptage échoué : " . $e->getMessage());
                throw ValidationException::withMessages([
                    'mot_de_passe' => 'Mot de passe invalide ou corrompu.',
                ]);
            }

            $result = $this->analyseMotDePasse($decryptedPassword);
            $notificationController = new NotificationController();

            if ($result['est_faible']) {
                $messageGlobal = "Bonjour, récemment, vous avez ajouté l’identifiant pour le service \"{$identifiant->service}\" avec le nom d’utilisateur \"{$identifiant->nom_utilisateur}\". ";
                $messageGlobal .= "Cependant, ce mot de passe présente des faiblesses : " . implode(', ', $result['messages']) . ". ";
                $messageGlobal .= "Nous vous recommandons de le modifier directement dans votre compte {$identifiant->service}.";

                $existe = Notification::where('user_id', $userId)
                    ->where('identifiant_id', $identifiant->id)
                    ->where('message', $messageGlobal)
                    ->where('type', 'alerte')
                    ->exists();

                if (!$existe) {
                    Notification::create([
                        'user_id' => $userId,
                        'identifiant_id' => $identifiant->id,
                        'message' => $messageGlobal,
                        'type' => 'alerte',
                        'est_lu' => false,
                    ]);
                }

$notificationController->envoyerMotDePasseInsecure($result['messages'], $identifiant->service, $identifiant->nom_utilisateur);
            } else {
                $message = "Bonjour, vous avez récemment ajouté l’identifiant pour le service \"{$identifiant->service}\" avec le nom d’utilisateur \"{$identifiant->nom_utilisateur}\". ";
                $message .= "Le mot de passe est sécurisé et ne présente aucune faiblesse connue.";

                $existe = Notification::where('user_id', $userId)
                    ->where('identifiant_id', $identifiant->id)
                    ->where('message', $message)
                    ->where('type', 'info')
                    ->exists();

                if (!$existe) {
                    Notification::create([
                        'user_id' => $userId,
                        'identifiant_id' => $identifiant->id,
                        'message' => $message,
                        'type' => 'info',
                        'est_lu' => false,
                    ]);
                }

                $notificationController->envoyerMotDePasseValide($identifiant->service, $identifiant->nom_utilisateur);
            }

            // Compter les notifications non lues
            $notifications_non_lues = Notification::where('user_id', $userId)
                ->where('est_lu', false)
                ->count();

            Session::put('notifications_non_lues', $notifications_non_lues);
        } catch (\Exception $e) {
            Log::error("Erreur dans AnalyseMotDePasseJob : " . $e->getMessage());
            throw $e; // relancer l’erreur pour que Laravel le signale
        }
    }

    protected function analyseMotDePasse(string $password): array
    {
        $faible = false;
        $messages = [];

        if (strlen($password) < 10) {
            $faible = true;
            $messages[] = "Mot de passe trop court.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $faible = true;
            $messages[] = "Ajoutez une majuscule.";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $faible = true;
            $messages[] = "Ajoutez une minuscule.";
        }
        if (!preg_match('/\d/', $password)) {
            $faible = true;
            $messages[] = "Ajoutez un chiffre.";
        }
        if (!preg_match('/[^a-zA-Z\d]/', $password)) {
            $faible = true;
            $messages[] = "Ajoutez un caractère spécial.";
        }

        if ($this->motDePasseCompromis($password)) {
            $faible = true;
            $messages[] = "Ce mot de passe a été compromis.";
        }

        return ['est_faible' => $faible, 'messages' => $messages];
    }

    protected function motDePasseCompromis(string $password): bool
    {
        $sha1 = strtoupper(sha1($password));
        $prefix = substr($sha1, 0, 5);
        $suffix = substr($sha1, 5);

        $response = Http::get("https://api.pwnedpasswords.com/range/{$prefix}");

        return $response->successful() && str_contains($response->body(), $suffix);
    }

    protected function getFragmentFromFirebase($userId, $service)
    {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'))
                                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $database = $factory->createDatabase();
        $service = strtolower($service);

        $snapshot = $database->getReference("Identifiants_services/{$service}/{$userId}")->getSnapshot();

        if ($snapshot->exists()) {
            return $snapshot->getValue()['fragment3'] ?? null;
        }

        return null;
    }
}