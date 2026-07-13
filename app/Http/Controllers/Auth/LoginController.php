<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Nombre maximum de tentatives de connexion.
     */
    protected int $maxAttempts = 5;

    /**
     * Durée de blocage en minutes après dépassement.
     */
    protected int $decayMinutes = 15;

    /**
     * Affiche le formulaire de connexion.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion.
     */
    public function login(Request $request)
    {
        // 1. Validation des champs
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'mot_de_passe' => ['required', 'string', 'min:8'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // 2. Vérification du rate limiting (anti brute-force)
        $key = 'login:' . $request->ip() . ':' . $request->input('email');

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => 'Trop de tentatives. Veuillez réessayer dans ' . ceil($seconds / 60) . ' minutes.',
            ]);
        }

        // 3. Recherche de l'utilisateur
        $user = User::where('email', $credentials['email'])->first();

        // 4. Vérification des identifiants
        if (!$user || !Hash::check($credentials['mot_de_passe'], $user->mot_de_passe)) {
            RateLimiter::hit($key, $this->decayMinutes * 60);

            $attemptsLeft = $this->maxAttempts - RateLimiter::attempts($key);

            if ($attemptsLeft > 0) {
                throw ValidationException::withMessages([
                    'email' => 'Email ou mot de passe incorrect. Il vous reste ' . $attemptsLeft . ' tentative(s).',
                ]);
            } else {
                throw ValidationException::withMessages([
                    'email' => 'Compte bloqué temporairement. Veuillez réessayer plus tard.',
                ]);
            }
        }

        // 5. Vérification du statut du compte
        if ($user->statut === 'suspendu') {
            throw ValidationException::withMessages([
                'email' => 'Votre compte a été suspendu. Veuillez contacter l\'administrateur.',
            ]);
        }

        if ($user->statut === 'inactif') {
            throw ValidationException::withMessages([
                'email' => 'Votre compte est inactif. Veuillez contacter l\'administrateur.',
            ]);
        }

        // 6. Connexion réussie
        Auth::login($user, $request->boolean('remember'));

        // 7. Régénération de la session (sécurité)
        $request->session()->regenerate();

        // 8. Mise à jour de la dernière connexion
        $user->update([
            'derniere_connexion' => now(),
        ]);

        // 9. Réinitialisation du compteur de tentatives
        RateLimiter::clear($key);

        // 10. Redirection selon le rôle
        return $this->redirectByRole($user);
    }

    /**
     * Redirige l'utilisateur selon son rôle.
     */
    protected function redirectByRole(User $user)
    {
        return match ($user->role) {
            'super_admin', 'admin' => redirect()->intended(route('admin.dashboard')),
            'editeur' => redirect()->intended(route('admin.articles.index')),
            default => redirect()->intended(route('admin.dashboard')),
        };
    }
}