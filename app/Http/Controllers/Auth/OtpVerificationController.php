<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use App\Models\User;
use Jenssegers\Agent\Agent;

class OtpVerificationController extends Controller
{
    /**
     * Récupère la vraie IP client en regardant d'abord le header X-Forwarded-For.
     */
    

    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Vérifier si un OTP est en cours
        if (!$user || !$user->otp_expires_at) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['otp' => 'Aucun code OTP actif. Veuillez vous reconnecter.']);
        }

        // Vérifier si le code a expiré
        if (Carbon::now()->gt($user->otp_expires_at)) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['otp' => 'Ce code OTP a expiré.']);
        }

        // Vérifie si l’OTP entré correspond au hash stocké
        if (! Hash::check($request->otp, $user->otp)) {
            return back()->withErrors(['otp' => 'Le code OTP est incorrect.']);
        }

      // Récupération IP + Navigateur a partir de user_agent
    $ip = $request->header('X-Forwarded-For') ?? $request->ip();
    $agent = new Agent();
    $agent->setUserAgent($request->header('User-Agent'));
    $browser = $agent->browser();
    $version = $agent->version($browser);
    $browserInfo = $browser . ' ' . $version;
 

    $user->update([
        'otp' => null,
        'otp_expires_at' => null,
        'otp_verified_at' => now(),
        'last_ip' => $ip,                          // IP réelle
        'user_agent' => $request->header('User-Agent'), // UA complet
        'browser' => $browserInfo,            // Chrome 125.0
        'last_login_at' => now(),
    ]);

    // Supprimer les anciens tokens
    $user->tokens()->delete();

    // Générer un nouveau token
    $newToken = $user->createToken('chrome_extension_token')->plainTextToken;

    // Enregistrer ce token dans la session ou logger si besoin
    session()->put('extension_token', $newToken);

    // (Optionnel) Le retourner à la vue ou logger pour test
    logger("Nouveau token généré pour l'extension : $newToken");

    return redirect()->route('dashboard')->with('status', 'Bienvenue, vous êtes connecté avec succès !');
    }
}