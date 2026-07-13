<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request)
    {
        // Récupérer l'utilisateur avant déconnexion pour le message
        $userName = Auth::user()->nom ?? 'Utilisateur';

        // Déconnexion
        Auth::logout();

        // Invalidation de la session
        $request->session()->invalidate();

        // Régénération du token CSRF
        $request->session()->regenerateToken();

        // Redirection avec message
        return redirect()->route('login')->with('logout', 'À bientôt ' . $userName . ' ! Vous avez été déconnecté(e) avec succès.');
    }
}