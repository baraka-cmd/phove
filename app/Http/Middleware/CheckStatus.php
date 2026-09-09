<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Vérifie que le compte utilisateur est actif.
     *
     * Gère les statuts : actif, inactif, suspendu.
     * Si le compte n'est pas actif, l'utilisateur est déconnecté
     * et redirigé vers la page de connexion avec un message clair.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (Auth::check()) {
            $user = Auth::user();
            $statut = $user->statut;

            // Si le compte n'est pas actif
            if ($statut !== 'actif') {
                // Déterminer le message selon le statut
                $message = match($statut) {
                    'inactif' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
                    'suspendu' => 'Votre compte a été suspendu. Veuillez contacter l\'administrateur.',
                    default => 'Votre compte n\'est pas actif. Veuillez contacter l\'administrateur.',
                };

                // Déconnexion
                Auth::logout();

                // Invalider la session
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirection avec message d'erreur
                return redirect()->route('login')->withErrors([
                    'email' => $message,
                ]);
            }

            // Mettre à jour la dernière connexion (optionnel)
            $user->update(['derniere_connexion' => now()]);
        }

        return $next($request);
    }
}