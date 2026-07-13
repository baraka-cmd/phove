<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\AdhesionConfirmationMail;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MembreController extends Controller
{
    /**
     * Catégories de membres disponibles (Article 13 des Statuts).
     */
    protected array $categories = [
        'adherent' => 'Membre adhérent',
        'sympathisant' => 'Membre sympathisant',
        'honneur' => 'Membre d\'honneur',
    ];

    /**
     * Affiche la page "Devenir membre".
     */
    public function index()
    {
        return view('public.devenir-membre', [
            'categories' => $this->categories,
        ]);
    }

    /**
     * Traite la soumission du formulaire d'adhésion.
     */
    public function store(Request $request)
    {
        // 1. Validation des données (conforme au ROI et Statuts)
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'post_nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'sexe' => ['required', 'in:M,F'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:191'],
            'categorie' => ['required', 'in:adherent,sympathisant,honneur'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'motivation' => ['nullable', 'string', 'max:2000'],
            'statuts_acceptes' => ['required', 'accepted'],
            'roi_accepte' => ['required', 'accepted'],
            'honnete' => ['required', 'accepted'],
            'conditions_acceptees' => ['required', 'accepted'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'post_nom.required' => 'Le post-nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'sexe.required' => 'Veuillez indiquer votre sexe.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'categorie.required' => 'Veuillez choisir une catégorie de membre.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'La photo doit être au format jpeg, jpg ou png.',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
            'statuts_acceptes.required' => 'Vous devez accepter les statuts.',
            'statuts_acceptes.accepted' => 'Vous devez accepter les statuts.',
            'roi_accepte.required' => 'Vous devez accepter le règlement intérieur.',
            'roi_accepte.accepted' => 'Vous devez accepter le règlement intérieur.',
            'honnete.required' => 'Vous devez attester de votre bonne foi.',
            'honnete.accepted' => 'Vous devez attester de votre bonne foi.',
            'conditions_acceptees.required' => 'Vous devez accepter les conditions.',
            'conditions_acceptees.accepted' => 'Vous devez accepter les conditions.',
        ]);

        // 2. Gérer l'upload de la photo
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('membres/photos', 'public');
        }

        // 3. Créer le membre dans la base de données
        $membre = Membre::create([
            'nom' => $validated['nom'],
            'post_nom' => $validated['post_nom'],
            'prenom' => $validated['prenom'],
            'sexe' => $validated['sexe'],
            'adresse' => $validated['adresse'],
            'telephone' => $validated['telephone'],
            'email' => $validated['email'] ?? null,
            'categorie' => $validated['categorie'],
            'photo' => $photoPath,
            'date_adhesion' => now(),
            'frais_adhesion' => 50.00,
            'cotisation_mensuelle' => 50.00,
            'statut' => 'en_attente',
        ]);

        // 4. Envoyer l'email de confirmation
        if ($membre->email) {
            try {
                Mail::to($membre->email)->send(new AdhesionConfirmationMail($membre));
            } catch (\Exception $e) {
                // L'email a échoué, mais l'adhésion est bien enregistrée
            }
        }

        // 5. Rediriger vers la page de confirmation
        return redirect()->route('devenir-membre.confirmation', ['id' => $membre->id])
            ->with('success', 'Votre demande d\'adhésion a été enregistrée avec succès.');
    }

    /**
     * Affiche la page de confirmation après adhésion.
     */
    public function confirmation(Request $request)
    {
        $membreId = $request->input('id');

        if (!$membreId) {
            return redirect()->route('devenir-membre');
        }

        $membre = Membre::findOrFail($membreId);

        return view('public.membre-confirmation', compact('membre'));
    }
}