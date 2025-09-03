<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Envoie une notification pour un mot de passe sécurisé.
     *
     * @param string $service
     * @param string $identifiant
     * @return void
     */
    public function envoyerMotDePasseValide(string $service, string $identifiant)
    {
        $user = Auth::user();

        $htmlContent = view('emails.mot_de_passe_valide', [
            'user' => $user,
            'service' => $service,
            'identifiant' => $identifiant,
        ])->render();

        Mail::send([], [], function ($message) use ($user, $htmlContent, $service) {
            $message->to($user->email)
                    ->subject("✅ Mot de passe sécurisé pour votre identifiant $service")
                    ->html($htmlContent); // ✅ Correction ici
        });

        Log::info("Notification mot de passe sécurisé envoyée pour $service à " . $user->email);
    }

    /**
     * Envoie une notification pour un mot de passe faible.
     *
     * @param array $erreurs
     * @param string $service
     * @param string $nom_utilisateur
     * @return void
     */
    public function envoyerMotDePasseInsecure(array $erreurs, string $service, string $nom_utilisateur)
    {
        $user = Auth::user();

        $htmlContent = view('emails.mot_de_passe_faible', [
            'user' => $user,
            'service' => $service,
            'nom_utilisateur' => $nom_utilisateur,
            'erreurs' => $erreurs,
        ])->render();

        Mail::send([], [], function ($message) use ($user, $htmlContent, $service) {
            $message->to($user->email)
                    ->subject("⚠️ Mot de passe faible détecté pour $service")
                    ->html($htmlContent); // ✅ Correction ici
        });

        Log::info("Notification mot de passe faible envoyée pour $service à " . $user->email);
    }

public function envoyerMotDePasseValideModifie(string $service, string $identifiant)
{
    $user = Auth::user();
    $htmlContent = view('emails.modification_valide', compact('user', 'service', 'identifiant'))->render();

    Mail::send([], [], function ($message) use ($user, $htmlContent, $service) {
        $message->to($user->email)
                ->subject("✅ Modification réussie pour votre identifiant $service")
                ->html($htmlContent);
    });
}

public function envoyerMotDePasseInsecureModifie(array $erreurs, string $service, string $nom_utilisateur)
{
    $user = Auth::user();
    $htmlContent = view('emails.modification_faible', compact('user', 'service', 'nom_utilisateur', 'erreurs'))->render();
    Mail::send([], [], function ($message) use ($user, $htmlContent, $service) {
        $message->to($user->email)
                ->subject("⚠️ Mot de passe faible après modification sur $service")
                ->html($htmlContent);
    });
}

public function envoyerMotDePasseValideImport(string $service, string $identifiant)
{
    $user = Auth::user();
    $htmlContent = view('emails.import_valide', compact('user', 'service', 'identifiant'))->render();

    Mail::send([], [], function ($message) use ($user, $htmlContent, $service) {
        $message->to($user->email)
                ->subject("✅ Identifiant importé sécurisé : $service")
                ->html($htmlContent);
    });
}

public function envoyerMotDePasseInsecureImport(array $erreurs, string $service, string $nom_utilisateur)
{
    $user = Auth::user();
    $htmlContent = view('emails.import_faible', compact('user', 'service', 'nom_utilisateur', 'erreurs'))->render();

    Mail::send([], [], function ($message) use ($user, $htmlContent, $service) {
        $message->to($user->email)
                ->subject("⚠️ Faiblesse détectée dans l’importation : $service")
                ->html($htmlContent);
    });
}
    public function destroy($id)
{
    $notification = Notification::findOrFail($id);

    if ($notification->user_id !== Auth::id()) {
        abort(403, 'Vous n’êtes pas autorisé à supprimer cette notification.');
    }

    $notification->delete();

    return back()->with('success', 'Notification supprimée avec succès.');
}
public function destroyAll()
{
    $userId = Auth::id();
    Notification::where('user_id', $userId)->delete();

    return back()->with('success', 'Toutes les notifications ont été supprimées avec succès.');
}
}