<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\View;



class OtpNotificationController extends Controller
{
    public function send(Request $request)
    {
        /** @var User $user */
    $user = Auth::user();

    $otp = random_int(100000, 999999);

    $user->update([
        'otp' => Hash::make($otp),
        'otp_expires_at' => Carbon::now()->addMinutes(5), // 5 minutes de validité
    ]);

    // Envoi avec une vue HTML
    Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
        $message->to($user->email)
                ->subject('🔐 Votre code de vérification (OTP)');
    });

    return response()->json(['message' => 'OTP envoyé avec succès.']);
}
}