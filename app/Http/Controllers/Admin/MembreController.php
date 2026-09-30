<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdhesionApprouveeMail;
use App\Mail\AdhesionRejeteeMail;
use App\Models\Membre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MembreController extends Controller
{
    /**
     * Nombre de membres par page.
     */
    protected int $perPage = 15;

    /**
     * Catégories de membres disponibles.
     *
     * @var array<string, string>
     */
    protected array $categories = [
        'initiateur'   => 'Initiateur',
        'fondateur'    => 'Fondateur',
        'adherent'     => 'Adhérent',
        'honneur'      => 'Honneur',
        'sympathisant' => 'Sympathisant',
    ];

    /**
     * Statuts des membres.
     *
     * @var array<string, string>
     */
    protected array $statuts = [
        'actif'          => 'Actif',
        'en_attente'     => 'En attente',
        'suspendu'       => 'Suspendu',
        'exclu'          => 'Exclu',
        'deserteur'      => 'Déserteur',
        'demissionnaire' => 'Démissionnaire',
    ];

    /*
    |--------------------------------------------------------------------------
    | INDEX / CREATE / STORE
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la liste paginée et filtrée des membres.
     */
    public function index(Request $request): View
    {
        $query = Membre::query();

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('zone')) {
            $query->where('adresse', 'like', '%' . $request->zone . '%');
        }

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

        $totalMembres      = Membre::count();
        $membresActifs     = Membre::where('statut', 'actif')->count();
        $membresEnAttente  = Membre::where('statut', 'en_attente')->count();
        $membresSuspendus  = Membre::where('statut', 'suspendu')->count();

        $zones = Membre::select('adresse')
            ->distinct()
            ->pluck('adresse')
            ->map(fn ($adresse) => trim((string) $adresse))
            ->filter()
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
    public function create(): View
    {
        return view('admin.membres.create', [
            'categories' => $this->categories,
            'statuts'    => $this->statuts,
        ]);
    }

    /**
     * Enregistre un nouveau membre.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom'                  => ['required', 'string', 'max:100'],
            'post_nom'             => ['required', 'string', 'max:100'],
            'prenom'               => ['required', 'string', 'max:100'],
            'sexe'                 => ['required', 'in:M,F'],
            'adresse'              => ['required', 'string', 'max:255'],
            'telephone'            => ['required', 'string', 'max:20'],
            'email'                => ['nullable', 'email', 'max:191', 'unique:membres,email'],
            'categorie'            => ['required', 'in:initiateur,fondateur,adherent,honneur,sympathisant'],
            'statut'               => ['required', 'in:actif,en_attente,suspendu,exclu,deserteur,demissionnaire'],
            'date_adhesion'        => ['required', 'date'],
            'frais_adhesion'       => ['nullable', 'numeric', 'min:0'],
            'cotisation_mensuelle' => ['nullable', 'numeric', 'min:0'],
            'photo'                => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ], [
            'nom.required'       => 'Le nom est obligatoire.',
            'post_nom.required'  => 'Le post-nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'adresse.required'   => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = media_store($request->file('photo'), 'membres/photos');
        }

        $validated['frais_adhesion']       = $validated['frais_adhesion'] ?? 50.00;
        $validated['cotisation_mensuelle'] = $validated['cotisation_mensuelle'] ?? 50.00;

        Membre::create($validated);

        return redirect()->route('admin.membres.index')
            ->with('success', 'Le membre a été ajouté avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW / EDIT / UPDATE / DESTROY
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la fiche détaillée d'un membre.
     */
    public function show(Membre $membre): View
    {
        $membre->load([
            'cotisations' => fn ($q) => $q->orderBy('mois', 'desc'),
            'organes',
        ]);

        $totalCotisations = $membre->cotisations->sum('montant');

        return view('admin.membres.show', compact('membre', 'totalCotisations'));
    }

    /**
     * Affiche le formulaire de modification d'un membre.
     */
    public function edit(Membre $membre): View
    {
        return view('admin.membres.edit', [
            'membre'     => $membre,
            'categories' => $this->categories,
            'statuts'    => $this->statuts,
        ]);
    }

    /**
     * Met à jour un membre.
     */
    public function update(Request $request, Membre $membre): RedirectResponse
    {
        $validated = $request->validate([
            'nom'                  => ['required', 'string', 'max:100'],
            'post_nom'             => ['required', 'string', 'max:100'],
            'prenom'               => ['required', 'string', 'max:100'],
            'sexe'                 => ['required', 'in:M,F'],
            'adresse'              => ['required', 'string', 'max:255'],
            'telephone'            => ['required', 'string', 'max:20'],
            'email'                => ['nullable', 'email', 'max:191', 'unique:membres,email,' . $membre->id],
            'categorie'            => ['required', 'in:initiateur,fondateur,adherent,honneur,sympathisant'],
            'statut'               => ['required', 'in:actif,en_attente,suspendu,exclu,deserteur,demissionnaire'],
            'date_adhesion'        => ['required', 'date'],
            'frais_adhesion'       => ['nullable', 'numeric', 'min:0'],
            'cotisation_mensuelle' => ['nullable', 'numeric', 'min:0'],
            'photo'                => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
        ], [
            'nom.required'       => 'Le nom est obligatoire.',
            'post_nom.required'  => 'Le post-nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'adresse.required'   => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
        ]);

        if ($request->hasFile('photo')) {
            if ($membre->photo) {
                media_delete($membre->photo);
            }
            $validated['photo'] = media_store($request->file('photo'), 'membres/photos');
        }

        $membre->update($validated);

        return redirect()->route('admin.membres.index')
            ->with('success', 'Le membre a été modifié avec succès.');
    }

    /**
     * Supprime un membre.
     */
    public function destroy(Membre $membre): RedirectResponse
    {
        if ($membre->photo) {
            media_delete($membre->photo);
        }

        $membre->delete();

        return redirect()->route('admin.membres.index')
            ->with('success', 'Le membre a été supprimé définitivement.');
    }

    /*
    |--------------------------------------------------------------------------
    | STATUT / SANCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Change le statut d'un membre rapidement.
     */
    public function changeStatus(Request $request, Membre $membre): RedirectResponse
    {
        $request->validate([
            'statut' => ['required', 'in:actif,suspendu,exclu'],
        ]);

        $membre->update([
            'statut'         => $request->statut,
            'motif_sanction' => $request->statut !== 'actif' ? $request->motif : null,
            'date_sanction'  => $request->statut !== 'actif' ? now() : null,
        ]);

        $statutLibelle = $this->statuts[$request->statut] ?? $request->statut;

        return redirect()->back()
            ->with('success', 'Le statut du membre a été changé en "' . $statutLibelle . '".');
    }

    /**
     * Affiche la liste des membres sanctionnés (suspendus, exclus).
     */
    public function sanctions(Request $request): View
    {
        $query = Membre::whereIn('statut', ['suspendu', 'exclu']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

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

        $totalSuspendus = Membre::where('statut', 'suspendu')->count();
        $totalExclus    = Membre::where('statut', 'exclu')->count();
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
    public function reintegrer(Membre $membre): RedirectResponse
    {
        if (!in_array($membre->statut, ['suspendu', 'exclu'], true)) {
            return redirect()->back()
                ->with('error', 'Ce membre n\'est pas suspendu ni exclu.');
        }

        $ancienStatut = $membre->statut;

        $membre->update([
            'statut'         => 'actif',
            'motif_sanction' => null,
            'date_sanction'  => null,
        ]);

        return redirect()->back()
            ->with('success', $membre->nomComplet() . ' a été réintégré avec succès (ancien statut : "' . $ancienStatut . '").');
    }

    /**
     * Suspend un membre avec motif obligatoire.
     */
    public function suspendre(Request $request, Membre $membre): RedirectResponse
    {
        $validated = $request->validate([
            'motif_sanction' => ['required', 'string', 'max:1000'],
        ], [
            'motif_sanction.required' => 'Le motif de suspension est obligatoire.',
        ]);

        $membre->update([
            'statut'         => 'suspendu',
            'motif_sanction' => $validated['motif_sanction'],
            'date_sanction'  => now(),
        ]);

        return redirect()->back()
            ->with('success', $membre->nomComplet() . ' a été suspendu.');
    }

    /**
     * Exclut définitivement un membre.
     */
    public function exclure(Request $request, Membre $membre): RedirectResponse
    {
        $validated = $request->validate([
            'motif_sanction' => ['required', 'string', 'max:1000'],
        ], [
            'motif_sanction.required' => 'Le motif d\'exclusion est obligatoire.',
        ]);

        $membre->update([
            'statut'         => 'exclu',
            'motif_sanction' => $validated['motif_sanction'],
            'date_sanction'  => now(),
        ]);

        return redirect()->back()
            ->with('success', $membre->nomComplet() . ' a été exclu définitivement.');
    }

    /*
    |--------------------------------------------------------------------------
    | DEMANDES D'ADHÉSION (en attente)
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la liste des demandes d'adhésion en attente.
     */
    public function demandes(Request $request): View
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

        $demandes = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalDemandes = Membre::where('statut', 'en_attente')->count();
        $demandesAujourdhui = Membre::where('statut', 'en_attente')
            ->whereDate('created_at', today())
            ->count();

        return view('admin.membres.demandes', compact(
            'demandes',
            'totalDemandes',
            'demandesAujourdhui'
        ));
    }

    /**
     * Approuve une demande d'adhésion.
     */
    public function approuver(Membre $membre): RedirectResponse
    {
        if ($membre->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $membre->update([
            'statut'         => 'actif',
            'motif_sanction' => null,
            'date_sanction'  => null,
        ]);

        if ($membre->email) {
            try {
                Mail::to($membre->email)->send(new AdhesionApprouveeMail($membre));
            } catch (\Throwable $e) {
                // Silencieux
            }
        }

        return redirect()->route('admin.membres.demandes')
            ->with('success', 'La demande de ' . $membre->nomComplet() . ' a été approuvée.');
    }

    /**
     * Rejette une demande d'adhésion.
     */
    public function rejeter(Request $request, Membre $membre): RedirectResponse
    {
        if ($membre->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $validated = $request->validate([
            'motif_rejet' => ['required', 'string', 'max:1000'],
        ], [
            'motif_rejet.required' => 'Le motif de rejet est obligatoire.',
        ]);

        $membre->update([
            'statut'         => 'exclu',
            'motif_sanction' => $validated['motif_rejet'],
            'date_sanction'  => now(),
        ]);

        if ($membre->email) {
            try {
                Mail::to($membre->email)->send(new AdhesionRejeteeMail($membre, $validated['motif_rejet']));
            } catch (\Throwable $e) {
                // Silencieux
            }
        }

        return redirect()->route('admin.membres.demandes')
            ->with('success', 'La demande de ' . $membre->nomComplet() . ' a été rejetée.');
    }
}