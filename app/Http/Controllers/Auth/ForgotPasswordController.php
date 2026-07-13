<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Affiche le formulaire de demande de réinitialisation.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoie le lien de réinitialisation par email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validation de l'email
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
        ]);

        // 2. Envoi du lien de réinitialisation
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // 3. Retour selon le résultat
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Un lien de réinitialisation vous a été envoyé par email. Veuillez vérifier votre boîte de réception.');
        }

        // 4. Si l'email n'existe pas
        throw ValidationException::withMessages([
            'email' => 'Aucun compte n\'est associé à cette adresse email.',
        ]);
    }
}