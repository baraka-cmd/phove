<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    /**
     * Affiche le formulaire de réinitialisation.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Réinitialise le mot de passe.
     */
    public function reset(Request $request)
    {
        // 1. Validation
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'mot_de_passe' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'token.required' => 'Le token est requis.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
            'mot_de_passe.confirmed' => 'Les mots de passe ne correspondent pas.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // 2. Réinitialisation via le broker de Password
        $status = Password::reset(
            $request->only('email', 'mot_de_passe', 'mot_de_passe_confirmation', 'token'),
            function (User $user, string $password) {
                $user->update([
                    'mot_de_passe' => Hash::make($password),
                ]);
            }
        );

        // 3. Redirection selon le résultat
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
        }

        // 4. En cas d'échec
        throw ValidationException::withMessages([
            'email' => 'Le lien de réinitialisation est invalide ou a expiré. Veuillez faire une nouvelle demande.',
        ]);
    }
}