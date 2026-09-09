<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProjetController extends Controller
{
    /**
     * Nombre de projets par page.
     *
     * @var int
     */
    protected int $perPage = 15;

    /**
     * Domaines d'intervention PHOVE (Articles 8 et 9 des Statuts).
     *
     * @var array<string, string>
     */
    protected array $domaines = [
        'sante_nutrition' => 'Santé & Nutrition',
        'education' => 'Éducation & Formation',
        'protection' => 'Protection Sociale',
        'wash' => 'Eau, Hygiène & Assainissement',
        'securite_alimentaire' => 'Sécurité Alimentaire & Agriculture',
        'communication' => 'Communication',
        'environnement' => 'Protection de l\'environnement',
        'paix' => 'Promotion de la Paix',
        'genre' => 'Sensibilité au genre',
        'spirituel' => 'Accompagnement Spirituel',
        'infrastructure' => 'Infrastructures',
        'economique' => 'Activités Socio-Économiques',
        'judiciaire' => 'Assistance Judiciaire',
    ];

    /**
     * Statuts des projets.
     *
     * @var array<string, string>
     */
    protected array $statuts = [
        'en_preparation' => 'En préparation',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'suspendu' => 'Suspendu',
    ];

    /**
     * Affiche la liste paginée et filtrée des projets PHOVE.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Projet::query();

        // Filtre par domaine
        if ($request->filled('domaine')) {
            $query->where('domaine', $request->domaine);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par zone
        if ($request->filled('zone')) {
            $query->where('zone_geographique', 'like', '%' . $request->zone . '%');
        }

        // Recherche textuelle (titre, code, description, zone, bailleur)
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                  ->orWhere('code_projet', 'like', '%' . $recherche . '%')
                  ->orWhere('description', 'like', '%' . $recherche . '%')
                  ->orWhere('zone_geographique', 'like', '%' . $recherche . '%')
                  ->orWhere('bailleur', 'like', '%' . $recherche . '%');
            });
        }

        $projets = $query->orderBy('date_debut', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $totalProjets = Projet::count();
        $projetsEnCours = Projet::where('statut', 'en_cours')->count();
        $projetsTermines = Projet::where('statut', 'termine')->count();
        $projetsEnPreparation = Projet::where('statut', 'en_preparation')->count();
        $totalBudget = Projet::sum('budget_total');

        // Zones distinctes pour le filtre
        $zones = Projet::select('zone_geographique')
            ->distinct()
            ->pluck('zone_geographique')
            ->map(fn($z) => trim($z))
            ->unique()
            ->sort()
            ->values();

        return view('admin.projets.index', compact(
            'projets',
            'totalProjets',
            'projetsEnCours',
            'projetsTermines',
            'projetsEnPreparation',
            'totalBudget',
            'zones'
        ));
    }

    /**
     * Affiche le formulaire de création d'un projet.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.projets.create', [
            'domaines' => $this->domaines,
            'statuts' => $this->statuts,
        ]);
    }

    /**
     * Enregistre un nouveau projet.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'code_projet' => ['nullable', 'string', 'max:50', 'unique:projets,code_projet'],
            'domaine' => ['required', 'in:' . implode(',', array_keys($this->domaines))],
            'zone_geographique' => ['required', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:100'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'budget_total' => ['nullable', 'numeric', 'min:0'],
            'budget_utilise' => ['nullable', 'numeric', 'min:0', 'lte:budget_total'],
            'bailleur' => ['nullable', 'string', 'max:255'],
            'source_financement' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'in:' . implode(',', array_keys($this->statuts))],
            'description' => ['required', 'string'],
            'objectifs' => ['nullable', 'string'],
            'resultats_attendus' => ['nullable', 'string'],
            'image_principale' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'publier' => ['nullable', 'boolean'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'code_projet.unique' => 'Ce code projet est déjà utilisé.',
            'domaine.required' => 'Le domaine est obligatoire.',
            'zone_geographique.required' => 'La zone géographique est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'budget_utilise.lte' => 'Le budget utilisé ne peut pas dépasser le budget total.',
            'statut.required' => 'Le statut est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'image_principale.image' => 'Le fichier doit être une image.',
            'image_principale.mimes' => 'L\'image doit être au format JPG, PNG ou WebP.',
            'image_principale.max' => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);

        // Générer le slug
        $validated['slug'] = Str::slug($validated['titre']);

        // Gérer l'upload de l'image
        if ($request->hasFile('image_principale')) {
            $validated['image_principale'] = $request->file('image_principale')->store('projets/images', 'public');
        }

        $validated['publier'] = $request->boolean('publier');

        $projet = Projet::create($validated);

        return redirect()->route('admin.projets.index')
            ->with('success', 'Le projet « ' . $projet->titre . ' » a été créé avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un projet (prévisualisation admin).
     *
     * @param Projet $projet
     * @return \Illuminate\View\View
     */
    public function show(Projet $projet)
    {
        $projet->load(['indicateurs', 'partenaires', 'medias']);

        return view('admin.projets.show', compact('projet'));
    }

    /**
     * Affiche le formulaire de modification d'un projet.
     *
     * @param Projet $projet
     * @return \Illuminate\View\View
     */
    public function edit(Projet $projet)
    {
        return view('admin.projets.edit', [
            'projet' => $projet,
            'domaines' => $this->domaines,
            'statuts' => $this->statuts,
        ]);
    }

    /**
     * Met à jour un projet.
     *
     * @param Request $request
     * @param Projet $projet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Projet $projet)
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'code_projet' => ['nullable', 'string', 'max:50', 'unique:projets,code_projet,' . $projet->id],
            'domaine' => ['required', 'in:' . implode(',', array_keys($this->domaines))],
            'zone_geographique' => ['required', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:100'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'budget_total' => ['nullable', 'numeric', 'min:0'],
            'budget_utilise' => ['nullable', 'numeric', 'min:0', 'lte:budget_total'],
            'bailleur' => ['nullable', 'string', 'max:255'],
            'source_financement' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'in:' . implode(',', array_keys($this->statuts))],
            'description' => ['required', 'string'],
            'objectifs' => ['nullable', 'string'],
            'resultats_attendus' => ['nullable', 'string'],
            'image_principale' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'publier' => ['nullable', 'boolean'],
        ], [
            'titre.required' => 'Le titre est obligatoire.',
            'code_projet.unique' => 'Ce code projet est déjà utilisé.',
            'domaine.required' => 'Le domaine est obligatoire.',
            'zone_geographique.required' => 'La zone géographique est obligatoire.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'budget_utilise.lte' => 'Le budget utilisé ne peut pas dépasser le budget total.',
            'statut.required' => 'Le statut est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'image_principale.image' => 'Le fichier doit être une image.',
            'image_principale.mimes' => 'L\'image doit être au format JPG, PNG ou WebP.',
            'image_principale.max' => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);

        // Mettre à jour le slug si le titre a changé
        if ($projet->titre !== $validated['titre']) {
            $validated['slug'] = Str::slug($validated['titre']);
        }

        // Gérer l'upload de l'image
        if ($request->hasFile('image_principale')) {
            // Supprimer l'ancienne image
            if ($projet->image_principale) {
                if (Storage::disk('public')->exists($projet->image_principale)) {
                    Storage::disk('public')->delete($projet->image_principale);
                }
            }
            $validated['image_principale'] = $request->file('image_principale')->store('projets/images', 'public');
        }

        $validated['publier'] = $request->boolean('publier');

        $projet->update($validated);

        return redirect()->route('admin.projets.index')
            ->with('success', 'Le projet « ' . $projet->titre . ' » a été mis à jour avec succès.');
    }

    /**
     * Supprime un projet.
     *
     * @param Projet $projet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Projet $projet)
    {
        // Supprimer l'image
        if ($projet->image_principale) {
            if (Storage::disk('public')->exists($projet->image_principale)) {
                Storage::disk('public')->delete($projet->image_principale);
            }
        }

        // Détacher les partenaires
        $projet->partenaires()->detach();

        $projet->delete();

        return redirect()->route('admin.projets.index')
            ->with('success', 'Le projet « ' . $projet->titre . ' » a été supprimé définitivement.');
    }

    /**
     * Change le statut d'un projet rapidement.
     *
     * @param Request $request
     * @param Projet $projet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus(Request $request, Projet $projet)
    {
        $request->validate([
            'statut' => ['required', 'in:en_cours,termine,suspendu,en_preparation'],
        ]);

        $projet->update(['statut' => $request->statut]);

        return redirect()->back()
            ->with('success', 'Le statut du projet a été changé en "' . $this->statuts[$request->statut] . '".');
    }

    /**
     * Active/désactive la publication d'un projet.
     *
     * @param Projet $projet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function togglePublier(Projet $projet)
    {
        $projet->update(['publier' => !$projet->publier]);

        $etat = $projet->publier ? 'publié' : 'dépublié';

        return redirect()->back()
            ->with('success', 'Le projet « ' . $projet->titre . ' » a été ' . $etat . ' avec succès.');
    }

    /**
     * Affiche la vue d'ensemble des budgets de tous les projets.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function budgets(Request $request)
    {
        $query = Projet::query();

        // Filtre par domaine
        if ($request->filled('domaine')) {
            $query->where('domaine', $request->domaine);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('titre', 'like', '%' . $recherche . '%')
                  ->orWhere('code_projet', 'like', '%' . $recherche . '%')
                  ->orWhere('zone_geographique', 'like', '%' . $recherche . '%');
            });
        }

        $projets = $query->orderBy('budget_total', 'desc')
            ->paginate(15)
            ->withQueryString();

        // ============================================
        // STATISTIQUES KPI
        // ============================================
        $totalBudgetPrevisionnel = Projet::sum('budget_total');
        $totalBudgetUtilise = Projet::sum('budget_utilise');
        $tauxConsommationGlobal = $totalBudgetPrevisionnel > 0 
            ? round(($totalBudgetUtilise / $totalBudgetPrevisionnel) * 100) 
            : 0;
        $nombreProjetsAvecBudget = Projet::where('budget_total', '>', 0)->count();
        $projetsEnDepassement = Projet::whereRaw('budget_utilise > budget_total')
            ->where('budget_total', '>', 0)
            ->count();

        // Budget restant global
        $budgetRestant = $totalBudgetPrevisionnel - $totalBudgetUtilise;

        // ============================================
        // DONNÉES GRAPHIQUE BARRES (Top 10 projets)
        // ============================================
        $topProjets = Projet::where('budget_total', '>', 0)
            ->orderBy('budget_total', 'desc')
            ->take(10)
            ->get();

        $graphLabels = $topProjets->pluck('titre')->map(fn($t) => Str::limit($t, 35));
        $graphBudgetTotal = $topProjets->pluck('budget_total');
        $graphBudgetUtilise = $topProjets->pluck('budget_utilise');

        // ============================================
        // DONNÉES GRAPHIQUE ANNEAU (par domaine)
        // ============================================
        $domainesStats = Projet::selectRaw('domaine, SUM(budget_utilise) as total')
            ->where('budget_utilise', '>', 0)
            ->groupBy('domaine')
            ->get();

        $donutLabels = [];
        $donutValues = [];
        $domainesColors = [
            'sante_nutrition' => '#1A5B9C',
            'education' => '#10B981',
            'protection' => '#8B5CF6',
            'wash' => '#3B82F6',
            'securite_alimentaire' => '#F59E0B',
            'communication' => '#06B6D4',
            'environnement' => '#22C55E',
            'paix' => '#EF4444',
            'genre' => '#EC4899',
            'spirituel' => '#6366F1',
            'infrastructure' => '#64748B',
            'economique' => '#84CC16',
            'judiciaire' => '#A855F7',
        ];

        foreach ($domainesStats as $stat) {
            $projet = Projet::where('domaine', $stat->domaine)->first();
            $donutLabels[] = $projet ? $projet->domaineLibelle() : $stat->domaine;
            $donutValues[] = (float) $stat->total;
        }

        return view('admin.projets.budgets', compact(
            'projets',
            'totalBudgetPrevisionnel',
            'totalBudgetUtilise',
            'tauxConsommationGlobal',
            'nombreProjetsAvecBudget',
            'projetsEnDepassement',
            'budgetRestant',
            'graphLabels',
            'graphBudgetTotal',
            'graphBudgetUtilise',
            'donutLabels',
            'donutValues',
            'domainesColors'
        ));
    }

    /**
     * Met à jour le budget utilisé d'un projet.
     *
     * @param Request $request
     * @param Projet $projet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBudget(Request $request, Projet $projet)
    {
        $validated = $request->validate([
            'budget_utilise' => ['required', 'numeric', 'min:0'],
        ], [
            'budget_utilise.required' => 'Le budget utilisé est obligatoire.',
            'budget_utilise.numeric' => 'Le budget utilisé doit être un nombre.',
            'budget_utilise.min' => 'Le budget utilisé ne peut pas être négatif.',
        ]);

        $projet->update(['budget_utilise' => $validated['budget_utilise']]);

        // Message d'alerte si dépassement
        if ($projet->budget_utilise > $projet->budget_total && $projet->budget_total > 0) {
            $depassement = $projet->budget_utilise - $projet->budget_total;
            return redirect()->back()
                ->with('warning', '⚠️ Attention : le budget est dépassé de ' . number_format($depassement, 0, ',', ' ') . ' $ pour le projet "' . Str::limit($projet->titre, 50) . '".');
        }

        return redirect()->back()
            ->with('success', 'Le budget du projet « ' . Str::limit($projet->titre, 50) . ' » a été mis à jour avec succès.');
    }
}