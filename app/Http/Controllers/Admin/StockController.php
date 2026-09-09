<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MouvementStock;
use App\Models\Projet;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Nombre d'articles par page.
     *
     * @var int
     */
    protected int $perPage = 15;

    /**
     * Catégories de produits.
     *
     * @var array<string, string>
     */
    protected array $categories = [
        'vivres' => 'Vivres',
        'medicaments' => 'Médicaments',
        'habillement' => 'Habillement',
        'abris' => 'Abris',
        'materiel' => 'Matériel',
        'semences' => 'Semences',
        'outils' => 'Outils aratoires',
        'autre' => 'Autre',
    ];

    /**
     * Unités de mesure.
     *
     * @var array<string, string>
     */
    protected array $unites = [
        'kg' => 'Kilogrammes (kg)',
        'g' => 'Grammes (g)',
        'l' => 'Litres (l)',
        'ml' => 'Millilitres (ml)',
        'pièce' => 'Pièce',
        'sac' => 'Sac',
        'boîte' => 'Boîte',
        'carton' => 'Carton',
        'lot' => 'Lot',
        'mètre' => 'Mètre',
        'm²' => 'Mètre carré (m²)',
        'unité' => 'Unité',
    ];

    /**
     * Affiche la liste paginée et filtrée des stocks.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Stock::withCount('mouvements');

        // Filtre par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('reference', 'like', '%' . $recherche . '%')
                  ->orWhere('designation', 'like', '%' . $recherche . '%')
                  ->orWhere('emplacement', 'like', '%' . $recherche . '%');
            });
        }

        $stocks = $query->orderBy('designation')
            ->paginate($this->perPage)
            ->withQueryString();

        // Statistiques
        $totalArticles = Stock::count();
        $articlesDisponibles = Stock::where('statut', 'disponible')->count();
        $articlesFaibles = Stock::where('statut', 'faible')->count();
        $articlesRupture = Stock::where('statut', 'rupture')->count();

        return view('admin.stocks.index', compact(
            'stocks',
            'totalArticles',
            'articlesDisponibles',
            'articlesFaibles',
            'articlesRupture'
        ));
    }

    /**
     * Affiche le formulaire de création d'un article.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $referenceAuto = Stock::genererReference();

        return view('admin.stocks.create', [
            'categories' => $this->categories,
            'unites' => $this->unites,
            'referenceAuto' => $referenceAuto,
        ]);
    }

    /**
     * Enregistre un nouvel article en stock.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:50', 'unique:stocks,reference'],
            'designation' => ['required', 'string', 'max:255'],
            'categorie' => ['required', 'in:' . implode(',', array_keys($this->categories))],
            'unite' => ['required', 'string', 'max:20'],
            'quantite_initiale' => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['nullable', 'numeric', 'min:0'],
            'emplacement' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ], [
            'reference.required' => 'La référence est obligatoire.',
            'reference.unique' => 'Cette référence est déjà utilisée.',
            'designation.required' => 'La désignation est obligatoire.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'unite.required' => 'L\'unité est obligatoire.',
            'quantite_initiale.required' => 'La quantité initiale est obligatoire.',
            'quantite_initiale.min' => 'La quantité ne peut pas être négative.',
        ]);

        // Définir la quantité actuelle = quantité initiale
        $validated['quantite_actuelle'] = $validated['quantite_initiale'];

        // Calculer le statut initial
        if ($validated['quantite_actuelle'] <= 0) {
            $validated['statut'] = 'rupture';
        } elseif (isset($validated['seuil_alerte']) && $validated['quantite_actuelle'] <= $validated['seuil_alerte']) {
            $validated['statut'] = 'faible';
        } else {
            $validated['statut'] = 'disponible';
        }

        $stock = Stock::create($validated);

        // Si quantité initiale > 0, créer un mouvement d'entrée
        if ($validated['quantite_initiale'] > 0) {
            MouvementStock::create([
                'stock_id' => $stock->id,
                'type_mouvement' => 'entree',
                'quantite' => $validated['quantite_initiale'],
                'date_mouvement' => now()->format('Y-m-d'),
                'reference_document' => 'INIT-' . $stock->reference,
                'motif' => 'Stock initial',
                'effectue_par' => auth()->id(),
                'notes' => 'Mouvement automatique de création du stock initial',
            ]);
        }

        return redirect()->route('admin.stocks.show', $stock)
            ->with('success', 'L\'article « ' . $stock->designation . ' » a été créé avec succès.');
    }

    /**
     * Affiche la fiche détaillée d'un article.
     *
     * @param Stock $stock
     * @return \Illuminate\View\View
     */
    public function show(Stock $stock)
    {
        $stock->load(['mouvements' => function ($query) {
            $query->orderBy('date_mouvement', 'desc')
                  ->orderBy('created_at', 'desc')
                  ->with(['effectuePar', 'projet']);
        }]);

        return view('admin.stocks.show', compact('stock'));
    }

    /**
     * Affiche le formulaire de modification d'un article.
     *
     * @param Stock $stock
     * @return \Illuminate\View\View
     */
    public function edit(Stock $stock)
    {
        return view('admin.stocks.edit', [
            'stock' => $stock,
            'categories' => $this->categories,
            'unites' => $this->unites,
        ]);
    }

    /**
     * Met à jour un article en stock.
     *
     * @param Request $request
     * @param Stock $stock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:50', 'unique:stocks,reference,' . $stock->id],
            'designation' => ['required', 'string', 'max:255'],
            'categorie' => ['required', 'in:' . implode(',', array_keys($this->categories))],
            'unite' => ['required', 'string', 'max:20'],
            'quantite_initiale' => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['nullable', 'numeric', 'min:0'],
            'emplacement' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ], [
            'reference.required' => 'La référence est obligatoire.',
            'reference.unique' => 'Cette référence est déjà utilisée.',
            'designation.required' => 'La désignation est obligatoire.',
        ]);

        $stock->update($validated);

        // Recalculer le statut
        $stock->calculerStatut();

        return redirect()->route('admin.stocks.show', $stock)
            ->with('success', 'L\'article « ' . $stock->designation . ' » a été mis à jour avec succès.');
    }

    /**
     * Affiche le formulaire d'entrée de stock.
     *
     * @param Stock $stock
     * @return \Illuminate\View\View
     */
    public function formEntree(Stock $stock)
    {
        $projets = Projet::where('statut', 'en_cours')->orderBy('titre')->get();

        return view('admin.stocks.entree', compact('stock', 'projets'));
    }

    /**
     * Enregistre une entrée de stock.
     *
     * @param Request $request
     * @param Stock $stock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function entree(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantite' => ['required', 'numeric', 'min:0.01'],
            'date_mouvement' => ['required', 'date'],
            'reference_document' => ['nullable', 'string', 'max:50'],
            'motif' => ['nullable', 'string', 'max:255'],
            'destinataire' => ['nullable', 'string', 'max:255'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.min' => 'La quantité doit être supérieure à 0.',
            'date_mouvement.required' => 'La date est obligatoire.',
        ]);

        try {
            $stock->entrer(
                $validated['quantite'],
                $validated['motif'] ?? null,
                $validated['destinataire'] ?? null,
                $validated['reference_document'] ?? null,
                $validated['projet_id'] ?? null,
                $validated['notes'] ?? null
            );

            return redirect()->route('admin.stocks.show', $stock)
                ->with('success', 'Entrée de ' . $validated['quantite'] . ' ' . $stock->unite . ' enregistrée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de sortie de stock.
     *
     * @param Stock $stock
     * @return \Illuminate\View\View
     */
    public function formSortie(Stock $stock)
    {
        $projets = Projet::where('statut', 'en_cours')->orderBy('titre')->get();

        return view('admin.stocks.sortie', compact('stock', 'projets'));
    }

    /**
     * Enregistre une sortie de stock.
     *
     * @param Request $request
     * @param Stock $stock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sortie(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'quantite' => ['required', 'numeric', 'min:0.01'],
            'date_mouvement' => ['required', 'date'],
            'reference_document' => ['nullable', 'string', 'max:50'],
            'motif' => ['nullable', 'string', 'max:255'],
            'destinataire' => ['nullable', 'string', 'max:255'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.min' => 'La quantité doit être supérieure à 0.',
            'date_mouvement.required' => 'La date est obligatoire.',
        ]);

        try {
            $stock->sortir(
                $validated['quantite'],
                $validated['motif'] ?? null,
                $validated['destinataire'] ?? null,
                $validated['reference_document'] ?? null,
                $validated['projet_id'] ?? null,
                $validated['notes'] ?? null
            );

            return redirect()->route('admin.stocks.show', $stock)
                ->with('success', 'Sortie de ' . $validated['quantite'] . ' ' . $stock->unite . ' enregistrée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Affiche tous les mouvements de stock.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function mouvements(Request $request)
    {
        $query = MouvementStock::with(['stock', 'effectuePar', 'projet']);

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type_mouvement', $request->type);
        }

        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('date_mouvement', $request->date);
        }

        // Filtre par stock
        if ($request->filled('stock_id')) {
            $query->where('stock_id', $request->stock_id);
        }

        // Recherche
        if ($request->filled('recherche')) {
            $recherche = $request->recherche;
            $query->where(function ($q) use ($recherche) {
                $q->where('reference_document', 'like', '%' . $recherche . '%')
                  ->orWhere('motif', 'like', '%' . $recherche . '%')
                  ->orWhere('destinataire', 'like', '%' . $recherche . '%');
            });
        }

        $mouvements = $query->orderBy('date_mouvement', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage)
            ->withQueryString();

        $stocks = Stock::orderBy('designation')->get();

        // Statistiques
        $totalEntrees = MouvementStock::where('type_mouvement', 'entree')->sum('quantite');
        $totalSorties = MouvementStock::where('type_mouvement', 'sortie')->sum('quantite');

        return view('admin.stocks.mouvements', compact(
            'mouvements',
            'stocks',
            'totalEntrees',
            'totalSorties'
        ));
    }

    /**
     * Supprime un article en stock.
     *
     * @param Stock $stock
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Stock $stock)
    {
        // Vérifier s'il y a des mouvements
        $nombreMouvements = $stock->mouvements()->count();

        // Supprimer les mouvements (cascade)
        $stock->mouvements()->delete();

        $stock->delete();

        $message = 'L\'article « ' . $stock->designation . ' » a été supprimé.';
        if ($nombreMouvements > 0) {
            $message .= ' ' . $nombreMouvements . ' mouvement(s) supprimé(s).';
        }

        return redirect()->route('admin.stocks.index')
            ->with('success', $message);
    }
}