<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\AdhesionApprouveeMail;
use App\Mail\AdhesionRejeteeMail;
use Illuminate\Support\Facades\Mail;

class MembreController extends Controller
{
    /**
     * Nombre de membres par page.
     */
    protected int $perPage = 15;

    /**
     * Catégories de membres disponibles.
     */
    protected array $categories = [
        'initiateur' => 'Initiateur',
        'fondateur' => 'Fondateur',
        'adherent' => 'Adhérent',
        'honneur' => 'Honneur',
        'sympathisant' => 'Sympathisant',
    ];

    /**
     * Statuts des membres.
     */
    protected array $statuts = [
        'actif' => 'Actif',
        'en_attente' => 'En attente',
        'suspendu' => 'Suspendu',
        'exclu' => 'Exclu',
        'deserteur' => 'Déserteur',
        'demissionnaire' => 'Démissionnaire',
    ];

    /**
     * Affiche la liste paginée et filtrée des membres.
     */
    public function index(Request $request)
    {
        $query = Membre::query();

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par zone (adresse)
        if ($request->filled('zone')) {
            $query->where('adresse', 'like', '%' . $request->zone . '%');
        }

        // Recherche textuelle
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('post_nom', 'like', '%' . $recherche . '%')
                  ->orWhere('prenom', 'like', '%' . $recherche . '%')
                  ->orWhere('email', 'like', '%' . $recherche . '%')
                  ->orWhere('telephone', 'like', '%' . $recherche . '%');
            });
        }

        $membres = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $totalMembres = Membre::count();
        $membresActifs = Membre::where('statut', 'actif')->count();
        $membresEnAttente = Membre::where('statut', 'en_attente')->count();
        $membresSuspendus = Membre::where('statut', 'suspendu')->count();

        // Zones distinctes pour le filtre
        $zones = Membre::select('adresse')
            ->distinct()
            ->pluck('adresse')
            ->map(function ($adresse) {
                return trim($adresse);
            })
            ->unique()
            ->sort()
            ->values();

        return view('admin.membres.index', compact(
            'membres',
            'totalMembres',
            'membresActifs',
            'membresEnAttente',
            'membresSuspendus',
            'zones'
        ));
    }

    /**
     * Affiche le formulaire de création d'un membre.
     */
    public function create()
    {
        return view('admin.membres.create', [
            'categories' => $this->categories,
            'statuts' => $this->statuts,
        ]);
    }

    /**
     * Enregistre un nouveau membre.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'post_nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'sexe' => ['required', 'in:M,F'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:191', 'unique:membres,email'],
            'categorie' => ['required', 'in:initiateur,fondateur,adherent,honneur,sympathisant'],
            'statut' => ['required', 'in:actif,en_attente,suspendu,exclu,deserteur,demissionnaire'],
            'date_adhesion' => ['required', 'date'],
            'frais_adhesion' => ['nullable', 'numeric', 'min:0'],
            'cotisation_mensuelle' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'post_nom.required' => 'Le post-nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('membres/photos', 'public');
        }

        // Valeurs par défaut
        $validated['frais_adhesion'] = $validated['frais_adhesion'] ?? 50.00;
        $validated['cotisation_mensuelle'] = $validated['cotisation_mensuelle'] ?? 50.00;

        Membre::create($validated);

        return redirect()->route('admin.membres.index')
            ->with('success', 'Le membre a été ajouté avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un membre.
     */
    public function show(Membre $membre)
    {
        $membre->load(['cotisations' => function ($query) {
            $query->orderBy('mois', 'desc');
        }, 'organes']);

        // Total des cotisations payées
        $totalCotisations = $membre->cotisations->sum('montant');

        return view('admin.membres.show', compact('membre', 'totalCotisations'));
    }

    /**
     * Affiche le formulaire de modification d'un membre.
     */
    public function edit(Membre $membre)
    {
        return view('admin.membres.edit', [
            'membre' => $membre,
            'categories' => $this->categories,
            'statuts' => $this->statuts,
        ]);
    }

    /**
     * Met à jour un membre.
     */
    public function update(Request $request, Membre $membre)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'post_nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'sexe' => ['required', 'in:M,F'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:191', 'unique:membres,email,' . $membre->id],
            'categorie' => ['required', 'in:initiateur,fondateur,adherent,honneur,sympathisant'],
            'statut' => ['required', 'in:actif,en_attente,suspendu,exclu,deserteur,demissionnaire'],
            'date_adhesion' => ['required', 'date'],
            'frais_adhesion' => ['nullable', 'numeric', 'min:0'],
            'cotisation_mensuelle' => ['nullable', 'numeric', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'post_nom.required' => 'Le post-nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
        ]);

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if ($membre->photo) {
                Storage::disk('public')->delete($membre->photo);
            }
            $validated['photo'] = $request->file('photo')->store('membres/photos', 'public');
        }

        $membre->update($validated);

        return redirect()->route('admin.membres.index')
            ->with('success', 'Le membre a été modifié avec succès.');
    }

    /**
     * Supprime un membre.
     */
    public function destroy(Membre $membre)
    {
        // Supprimer la photo
        if ($membre->photo) {
            Storage::disk('public')->delete($membre->photo);
        }

        $membre->delete();

        return redirect()->route('admin.membres.index')
            ->with('success', 'Le membre a été supprimé définitivement.');
    }

    /**
     * Change le statut d'un membre rapidement.
     */
    public function changeStatus(Request $request, Membre $membre)
    {
        $request->validate([
            'statut' => ['required', 'in:actif,suspendu,exclu'],
        ]);

        $membre->update([
            'statut' => $request->statut,
            'motif_sanction' => $request->statut !== 'actif' ? $request->motif : null,
            'date_sanction' => $request->statut !== 'actif' ? now() : null,
        ]);

        $statutLibelle = $this->statuts[$request->statut] ?? $request->statut;

        return redirect()->back()
            ->with('success', 'Le statut du membre a été changé en "' . $statutLibelle . '".');
    }


    

// Ajouter ces imports en haut du fichier après les autres "use"

// Ajouter ces méthodes dans la classe :

/**
 * Affiche la liste des demandes d'adhésion en attente.
 */
public function demandes(Request $request)
{
    $query = Membre::where('statut', 'en_attente');

    if ($request->filled('recherche')) {
        $recherche = $request->recherche;
        $query->where(function ($q) use ($recherche) {
            $q->where('nom', 'like', '%' . $recherche . '%')
              ->orWhere('post_nom', 'like', '%' . $recherche . '%')
              ->orWhere('prenom', 'like', '%' . $recherche . '%')
              ->orWhere('email', 'like', '%' . $recherche . '%')
              ->orWhere('telephone', 'like', '%' . $recherche . '%');
        });
    }

    if ($request->filled('categorie')) {
        $query->where('categorie', $request->categorie);
    }

    $demandes = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

    $totalDemandes = Membre::where('statut', 'en_attente')->count();
    $demandesAujourdhui = Membre::where('statut', 'en_attente')->whereDate('created_at', today())->count();

    return view('admin.membres.demandes', compact('demandes', 'totalDemandes', 'demandesAujourdhui'));
}

/**
 * Approuve une demande d'adhésion.
 */
public function approuver(Membre $membre)
{
    if ($membre->statut !== 'en_attente') {
        return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
    }

    $membre->update(['statut' => 'actif', 'motif_sanction' => null, 'date_sanction' => null]);

    if ($membre->email) {
        try {
            Mail::to($membre->email)->send(new AdhesionApprouveeMail($membre));
        } catch (\Exception $e) {}
    }

    return redirect()->route('admin.membres.demandes')
        ->with('success', 'La demande de ' . $membre->nomComplet() . ' a été approuvée.');
}

/**
 * Rejette une demande d'adhésion.
 */
public function rejeter(Request $request, Membre $membre)
{
    if ($membre->statut !== 'en_attente') {
        return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
    }

    $validated = $request->validate([
        'motif_rejet' => ['required', 'string', 'max:1000'],
    ]);

    $membre->update([
        'statut' => 'exclu',
        'motif_sanction' => $validated['motif_rejet'],
        'date_sanction' => now(),
    ]);

    if ($membre->email) {
        try {
            Mail::to($membre->email)->send(new AdhesionRejeteeMail($membre, $validated['motif_rejet']));
        } catch (\Exception $e) {}
    }

    return redirect()->route('admin.membres.demandes')
        ->with('success', 'La demande de ' . $membre->nomComplet() . ' a été rejetée.');
}


/**
 * Affiche la liste des membres sanctionnés (suspendus, exclus).
 */
public function sanctions(Request $request)
{
    $query = Membre::whereIn('statut', ['suspendu', 'exclu']);

    // Filtre par type de sanction
    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }

    // Recherche
    if ($request->filled('recherche')) {
        $recherche = $request->recherche;
        $query->where(function ($q) use ($recherche) {
            $q->where('nom', 'like', '%' . $recherche . '%')
              ->orWhere('post_nom', 'like', '%' . $recherche . '%')
              ->orWhere('prenom', 'like', '%' . $recherche . '%');
        });
    }

    $sanctions = $query->orderBy('date_sanction', 'desc')
        ->paginate(15)
        ->withQueryString();

    // Statistiques
    $totalSuspendus = Membre::where('statut', 'suspendu')->count();
    $totalExclus = Membre::where('statut', 'exclu')->count();
    $totalSanctions = $totalSuspendus + $totalExclus;

    return view('admin.membres.sanctions', compact(
        'sanctions',
        'totalSuspendus',
        'totalExclus',
        'totalSanctions'
    ));
}

/**
 * Réintègre un membre suspendu ou exclu.
 */
public function reintegrer(Membre $membre)
{
    if (!in_array($membre->statut, ['suspendu', 'exclu'])) {
        return redirect()->back()
            ->with('error', 'Ce membre n\'est pas suspendu ni exclu.');
    }

    $ancienStatut = $membre->statut;
    
    $membre->update([
        'statut' => 'actif',
        'motif_sanction' => null,
        'date_sanction' => null,
    ]);

    $statutLibelle = $ancienStatut === 'suspendu' ? 'suspendu' : 'exclu';

    return redirect()->back()
        ->with('success', $membre->nomComplet() . ' a été réintégré avec succès. Le statut est passé de "' . $statutLibelle . '" à "Actif".');
}

/**
 * Suspend un membre avec motif obligatoire.
 */
/**
 * Suspend un membre avec motif obligatoire.
 */
public function suspendre(Request $request, Membre $membre)
{
    $validated = $request->validate([
        'motif_sanction' => ['required', 'string', 'max:1000'],
    ], [
        'motif_sanction.required' => 'Le motif de suspension est obligatoire.',
    ]);

    $membre->update([
        'statut' => 'suspendu',
        'motif_sanction' => $validated['motif_sanction'],
        'date_sanction' => now(),
    ]);

    return redirect()->back()
        ->with('success', $membre->nomComplet() . ' a été suspendu.');
}

/**
 * Exclut définitivement un membre.
 */
public function exclure(Request $request, Membre $membre)
{
    $validated = $request->validate([
        'motif_sanction' => ['required', 'string', 'max:1000'],
    ], [
        'motif_sanction.required' => 'Le motif d\'exclusion est obligatoire.',
    ]);

    $membre->update([
        'statut' => 'exclu',
        'motif_sanction' => $validated['motif_sanction'],
        'date_sanction' => now(),
    ]);

    return redirect()->back()
        ->with('success', $membre->nomComplet() . ' a été exclu définitivement.');
}

}