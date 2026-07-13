<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Vérifie que le compte utilisateur est actif.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->statut !== 'actif') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été désactivé ou suspendu. Veuillez contacter l\'administrateur.',
            ]);
        }

        return $next($request);
    }
}