<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\DonConfirmationMail;
use App\Models\Don;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DonController extends Controller
{
    /**
     * Montants suggérés pour les dons (en USD).
     */
    protected array $montantsSuggeres = [10, 25, 50, 100, 250, 500];

    /**
     * Modes de paiement disponibles.
     */
    protected array $modesPaiement = [
        'mobile_money' => 'Mobile Money (Airtel, Orange, M-Pesa)',
        'virement_bancaire' => 'Virement bancaire',
        'carte_bancaire' => 'Carte bancaire',
        'paypal' => 'PayPal',
        'especes' => 'Espèces',
    ];

    /**
     * Affiche la page "Faire un don".
     */
    public function index()
    {
        // Récupérer les projets actifs pour l'affectation
        $projets = Projet::where('publier', true)
            ->where('statut', 'en_cours')
            ->orderBy('date_debut', 'desc')
            ->get();

        return view('public.don', [
            'montantsSuggeres' => $this->montantsSuggeres,
            'modesPaiement' => $this->modesPaiement,
            'projets' => $projets,
        ]);
    }

    /**
     * Traite la soumission du formulaire de don.
     */
    public function store(Request $request)
    {
        // 1. Validation des données
        $validated = $request->validate([
            'montant' => ['required', 'numeric', 'min:1', 'max:50000'],
            'montant_type' => ['nullable', 'in:suggere,personnalise'],
            'devise' => ['required', 'in:USD,CDF'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'type_don' => ['required', 'in:ponctuel,recurrent'],
            'donateur_nom' => ['required', 'string', 'max:255'],
            'donateur_email' => ['required', 'email', 'max:255'],
            'donateur_telephone' => ['nullable', 'string', 'max:20'],
            'donateur_adresse' => ['nullable', 'string', 'max:255'],
            'mode_paiement' => ['required', 'in:mobile_money,virement_bancaire,carte_bancaire,paypal,especes'],
            'message_donateur' => ['nullable', 'string', 'max:1000'],
            'conditions_acceptees' => ['required', 'accepted'],
        ], [
            'montant.required' => 'Veuillez indiquer un montant.',
            'montant.min' => 'Le montant minimum est de 1 $.',
            'montant.max' => 'Le montant maximum est de 50 000 $.',
            'donateur_nom.required' => 'Veuillez indiquer votre nom complet.',
            'donateur_email.required' => 'Veuillez indiquer votre adresse email.',
            'donateur_email.email' => 'Veuillez indiquer une adresse email valide.',
            'mode_paiement.required' => 'Veuillez choisir un mode de paiement.',
            'conditions_acceptees.required' => 'Vous devez accepter les conditions.',
            'conditions_acceptees.accepted' => 'Vous devez accepter les conditions.',
        ]);

        // 2. Créer le don dans la base de données
        $don = Don::create([
            'donateur_nom' => $validated['donateur_nom'],
            'donateur_email' => $validated['donateur_email'],
            'donateur_telephone' => $validated['donateur_telephone'] ?? null,
            'donateur_adresse' => $validated['donateur_adresse'] ?? null,
            'type_donateur' => 'particulier',
            'montant' => $validated['montant'],
            'devise' => $validated['devise'],
            'type_don' => $validated['type_don'],
            'projet_id' => $validated['projet_id'] ?? null,
            'mode_paiement' => $validated['mode_paiement'],
            'message_donateur' => $validated['message_donateur'] ?? null,
            'date_don' => now(),
            'statut' => 'en_attente',
        ]);

        // 3. Générer un numéro de reçu unique
        $don->update([
            'numero_recu' => $don->genererNumeroRecu(),
        ]);

        // 4. Envoyer l'email de confirmation
        try {
            Mail::to($don->donateur_email)->send(new DonConfirmationMail($don));
        } catch (\Exception $e) {
            // L'email a échoué, mais le don est bien enregistré
            // En production, on loggerait l'erreur
        }

        // 5. Rediriger vers la page de remerciement
        return redirect()->route('don.merci', ['don' => $don->id, 'ref' => $don->numero_recu]);
    }

    /**
     * Affiche la page de remerciement après un don.
     */
    public function merci(Request $request)
    {
        $donId = $request->input('don');
        $reference = $request->input('ref');

        if (!$donId || !$reference) {
            return redirect()->route('don');
        }

        $don = Don::findOrFail($donId);

        // Vérifier que la référence correspond
        if ($don->numero_recu !== $reference) {
            return redirect()->route('don');
        }

        return view('public.don-merci', compact('don'));
    }
}