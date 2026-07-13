<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\ContactNotificationMail;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Motifs de contact disponibles.
     */
    protected array $motifs = [
        'information' => 'Demande d\'information',
        'partenariat' => 'Proposition de partenariat',
        'don' => 'Faire un don',
        'adhesion' => 'Adhésion',
        'benevolat' => 'Bénévolat',
        'presse' => 'Contact presse',
        'plainte' => 'Réclamation',
        'autre' => 'Autre',
    ];

    /**
     * Affiche la page contact.
     */
    public function index()
    {
        return view('public.contact', [
            'motifs' => $this->motifs,
        ]);
    }

    /**
     * Traite la soumission du formulaire de contact.
     */
    public function store(Request $request)
    {
        // 1. Validation des données
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'motif' => ['required', 'in:information,partenariat,don,adhesion,benevolat,presse,plainte,autre'],
            'sujet' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'nom.required' => 'Veuillez indiquer votre nom complet.',
            'email.required' => 'Veuillez indiquer votre adresse email.',
            'email.email' => 'Veuillez indiquer une adresse email valide.',
            'motif.required' => 'Veuillez sélectionner le motif de votre message.',
            'sujet.required' => 'Veuillez indiquer le sujet de votre message.',
            'message.required' => 'Veuillez rédiger votre message.',
            'message.max' => 'Le message ne doit pas dépasser 5000 caractères.',
        ]);

        // 2. Enregistrer le message dans la base de données
        $contact = Contact::create([
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'organisation' => $validated['organisation'] ?? null,
            'motif' => $validated['motif'],
            'sujet' => $validated['sujet'],
            'message' => $validated['message'],
            'statut' => 'nouveau',
        ]);

        // 3. Envoyer l'email de notification à l'admin
        try {
            $adminEmail = env('MAIL_FROM_ADDRESS', 'contact@ahd-rdc.org');
            Mail::to($adminEmail)->send(new ContactNotificationMail($contact));
        } catch (\Exception $e) {
            // L'email a échoué, mais le message est bien enregistré
        }

        // 4. Rediriger selon le motif
        $motif = $validated['motif'];

        if ($motif === 'benevolat') {
            return redirect()->route('devenir-benevole.confirmation')
                ->with('success', 'Votre candidature bénévole a été enregistrée avec succès. Nous vous contacterons dans les plus brefs délais.');
        }

        if ($motif === 'partenariat') {
            return redirect()->route('devenir-partenaire.confirmation')
                ->with('success', 'Votre proposition de partenariat a été enregistrée avec succès. Notre équipe va l\'examiner et reviendra vers vous.');
        }

        // Par défaut : page contact
        return redirect()->route('contact')
            ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
    }
}