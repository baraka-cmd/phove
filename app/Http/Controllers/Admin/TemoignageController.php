<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Temoignage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemoignageController extends Controller
{
    /**
     * Nombre de témoignages par page.
     */
    protected int $perPage = 15;

    /**
     * Catégories de bénéficiaires disponibles.
     */
    protected array $categories = [
        'femme' => 'Femme',
        'enfant' => 'Enfant',
        'handicape' => 'Personne handicapée',
        'troisieme_age' => '3ème âge',
        'orphelin' => 'Orphelin',
        'deplace_interne' => 'Déplacé interne',
        'refugie' => 'Réfugié',
        'rapatrie' => 'Rapatrié',
        'autre' => 'Autre',
    ];

    /**
     * Statuts disponibles.
     */
    protected array $statuts = [
        'en_attente' => 'En attente',
        'approuve' => 'Approuvé',
        'rejete' => 'Rejeté',
        'archive' => 'Archivé',
    ];

    /**
     * Affiche la liste paginée et filtrée des témoignages.
     */
    public function index(Request $request)
    {
        $query = Temoignage::with(['projet', 'traitePar']);

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        // Filtre par province
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        // Filtre par projet
        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }

        // Recherche textuelle
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('post_nom', 'like', '%' . $recherche . '%')
                  ->orWhere('prenom', 'like', '%' . $recherche . '%')
                  ->orWhere('contenu', 'like', '%' . $recherche . '%')
                  ->orWhere('localisation', 'like', '%' . $recherche . '%');
            });
        }

        $temoignages = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // ============================================
        // STATISTIQUES KPI
        // ============================================
        $totalTemoignages = Temoignage::count();
        $temoignagesEnAttente = Temoignage::where('statut', 'en_attente')->count();
        $temoignagesApprouves = Temoignage::where('statut', 'approuve')->count();
        $temoignagesRejetes = Temoignage::where('statut', 'rejete')->count();
        $temoignagesArchives = Temoignage::where('statut', 'archive')->count();

        // Projets pour le filtre
        $projets = Projet::orderBy('titre')->get();

        // Provinces distinctes pour le filtre
        $provinces = Temoignage::select('province')
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        return view('admin.temoignages.index', compact(
            'temoignages',
            'totalTemoignages',
            'temoignagesEnAttente',
            'temoignagesApprouves',
            'temoignagesRejetes',
            'temoignagesArchives',
            'projets',
            'provinces'
        ));
    }

    /**
     * Affiche le formulaire de création d'un témoignage.
     */
    public function create()
    {
        $projets = Projet::orderBy('titre')->get();
        return view('admin.temoignages.create', compact('projets'));
    }

    /**
     * Enregistre un nouveau témoignage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'projet_id' => ['nullable', 'exists:projets,id'],
            'nom' => ['required', 'string', 'max:100'],
            'post_nom' => ['nullable', 'string', 'max:100'],
            'prenom' => ['nullable', 'string', 'max:100'],
            'sexe' => ['required', 'in:M,F'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'categorie' => ['required', 'in:' . implode(',', array_keys($this->categories))],
            'province' => ['nullable', 'string', 'max:100'],
            'localisation' => ['nullable', 'string', 'max:255'],
            'contenu' => ['required', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'statut' => ['required', 'in:en_attente,approuve,rejete,archive'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'contenu.required' => 'Le contenu du témoignage est obligatoire.',
            'contenu.max' => 'Le témoignage ne doit pas dépasser 5000 caractères.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'La photo doit être au format JPG, PNG ou WebP.',
            'photo.max' => 'La photo ne doit pas dépasser 5 Mo.',
        ]);

        // Upload photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('temoignages/photos', 'public');
        }

        // Date de soumission
        if (empty($validated['date_soumission'])) {
            $validated['date_soumission'] = now();
        }

        // Si approuvé, définir la date de validation
        if ($validated['statut'] === 'approuve') {
            $validated['date_validation'] = now();
            $validated['traite_par'] = auth()->id();
        }

        Temoignage::create($validated);

        return redirect()->route('admin.temoignages.index')
            ->with('success', 'Le témoignage a été créé avec succès.');
    }

    /**
     * Affiche le détail d'un témoignage.
     */
    public function show(Temoignage $temoignage)
    {
        $temoignage->load(['projet', 'traitePar']);

        // Témoignages précédent et suivant
        $precedent = Temoignage::where('id', '<', $temoignage->id)
            ->orderBy('id', 'desc')
            ->first();

        $suivant = Temoignage::where('id', '>', $temoignage->id)
            ->orderBy('id', 'asc')
            ->first();

        return view('admin.temoignages.show', compact('temoignage', 'precedent', 'suivant'));
    }

    /**
     * Affiche le formulaire de modification d'un témoignage.
     */
    public function edit(Temoignage $temoignage)
    {
        $projets = Projet::orderBy('titre')->get();
        return view('admin.temoignages.edit', compact('temoignage', 'projets'));
    }

    /**
     * Met à jour un témoignage.
     */
    public function update(Request $request, Temoignage $temoignage)
    {
        $validated = $request->validate([
            'projet_id' => ['nullable', 'exists:projets,id'],
            'nom' => ['required', 'string', 'max:100'],
            'post_nom' => ['nullable', 'string', 'max:100'],
            'prenom' => ['nullable', 'string', 'max:100'],
            'sexe' => ['required', 'in:M,F'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            'categorie' => ['required', 'in:' . implode(',', array_keys($this->categories))],
            'province' => ['nullable', 'string', 'max:100'],
            'localisation' => ['nullable', 'string', 'max:255'],
            'contenu' => ['required', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'statut' => ['required', 'in:en_attente,approuve,rejete,archive'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'contenu.required' => 'Le contenu du témoignage est obligatoire.',
            'contenu.max' => 'Le témoignage ne doit pas dépasser 5000 caractères.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'La photo doit être au format JPG, PNG ou WebP.',
            'photo.max' => 'La photo ne doit pas dépasser 5 Mo.',
        ]);

        // Upload photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if ($temoignage->photo) {
                Storage::disk('public')->delete($temoignage->photo);
            }
            $validated['photo'] = $request->file('photo')->store('temoignages/photos', 'public');
        }

        // Si le statut passe à "approuve", enregistrer la validation
        if ($validated['statut'] === 'approuve' && $temoignage->statut !== 'approuve') {
            $validated['date_validation'] = now();
            $validated['traite_par'] = auth()->id();
            $validated['motif_rejet'] = null;
        }

        // Si le statut passe à "rejete"
        if ($validated['statut'] === 'rejete' && $temoignage->statut !== 'rejete') {
            $validated['date_validation'] = now();
            $validated['traite_par'] = auth()->id();
        }

        $temoignage->update($validated);

        return redirect()->route('admin.temoignages.index')
            ->with('success', 'Le témoignage a été mis à jour avec succès.');
    }

    /**
     * Supprime un témoignage.
     */
    public function destroy(Temoignage $temoignage)
    {
        // Supprimer la photo
        if ($temoignage->photo) {
            Storage::disk('public')->delete($temoignage->photo);
        }

        $temoignage->delete();

        return redirect()->route('admin.temoignages.index')
            ->with('success', 'Le témoignage a été supprimé définitivement.');
    }

    /**
     * Approuve un témoignage.
     */
    public function approuver(Temoignage $temoignage)
    {
        if ($temoignage->estApprouve()) {
            return redirect()->back()
                ->with('warning', 'Ce témoignage est déjà approuvé.');
        }

        $temoignage->approuver(auth()->id());

        return redirect()->back()
            ->with('success', 'Le témoignage de ' . $temoignage->nomComplet() . ' a été approuvé et est maintenant visible publiquement.');
    }

    /**
     * Rejette un témoignage.
     */
    public function rejeter(Request $request, Temoignage $temoignage)
    {
        $validated = $request->validate([
            'motif_rejet' => ['required', 'string', 'max:1000'],
        ], [
            'motif_rejet.required' => 'Le motif de rejet est obligatoire.',
        ]);

        $temoignage->rejeter(auth()->id(), $validated['motif_rejet']);

        return redirect()->back()
            ->with('success', 'Le témoignage de ' . $temoignage->nomComplet() . ' a été rejeté.');
    }

    /**
     * Archive un témoignage.
     */
    public function archiver(Temoignage $temoignage)
    {
        $temoignage->archiver();

        return redirect()->back()
            ->with('success', 'Le témoignage a été archivé.');
    }

    /**
     * Désarchive un témoignage (le repasse en attente).
     */
    public function desarchiver(Temoignage $temoignage)
    {
        $temoignage->update(['statut' => 'en_attente']);

        return redirect()->back()
            ->with('success', 'Le témoignage a été désarchivé et remis en attente.');
    }
}