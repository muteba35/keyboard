<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OtpPromptController extends Controller
{
    /**
     * Affiche le formulaire OTP si l'utilisateur n'a pas encore validé l'OTP.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->otp_expires_at== null
            ? redirect()->intended(route('dashboard'))
            : view('auth.otp-form');
    }
    
}