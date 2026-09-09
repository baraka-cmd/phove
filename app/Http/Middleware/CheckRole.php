<?php

namespace App\Http\Middleware;

use App\Enums\Roles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Vérifie que l'utilisateur a le bon rôle.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Les rôles autorisés (codes PHOVE)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role;

        // Vérifier si l'utilisateur est actif
        if (!$user->estActif()) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte a été désactivé. Contactez l\'administrateur.');
        }

        // Si aucun rôle spécifié, autoriser tout utilisateur connecté
        if (empty($roles)) {
            return $next($request);
        }

        // Vérifier si le rôle est autorisé
        if (!in_array($userRole, $roles)) {
            // Si c'est une requête AJAX/API
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires.',
                ], 403);
            }

            abort(403, 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
        }

        return $next($request);
    }
}