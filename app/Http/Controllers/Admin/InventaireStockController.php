<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComptageInventaireRequest;
use App\Http\Requests\StoreInventaireRequest;
use App\Http\Requests\ValiderInventaireRequest;
use App\Models\EmplacementStock;
use App\Models\InventaireStock;
use App\Models\Stock;
use App\Models\User;
use App\Services\Stock\InventaireStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller des inventaires de stock.
 *
 * PÉRIMÈTRE :
 *
 *   Ce controller gère :
 *     - la liste et la consultation des inventaires ;
 *     - la création d'un inventaire ;
 *     - la saisie des comptages ;
 *     - la validation (déclenche les ajustements).
 *
 *   La validation est la SEULE action qui modifie le stock
 *   (via génération de mouvements d'ajustement).
 *
 * SÉPARATION :
 *
 *   Toute la logique métier est dans InventaireStockService.
 */
class InventaireStockController extends Controller
{
    public function __construct(
        private readonly InventaireStockService $inventaireStockService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des inventaires.
     *
     * GET /admin/stocks/inventaires
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', InventaireStock::class);

        $query = $this->inventaireStockService->requete(
            recherche: $request->input('recherche'),
            statut:    $request->input('statut'),
            dateDebut: $request->input('date_debut'),
            dateFin:   $request->input('date_fin')
        );

        $inventaires = $query
            ->withCount('lignes')
            ->orderByDesc('date_debut')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        /*
         * Statistiques.
         */
        $stats = $this->inventaireStockService->statistiques();

        /*
         * Filtres.
         */
        $statuts = [
            'brouillon' => 'Brouillon',
            'en_cours'  => 'En cours',
            'termine'   => 'Terminé',
            'valide'    => 'Validé',
            'annule'    => 'Annulé',
        ];

        return view('admin.stocks.inventaires.index', compact(
            'inventaires',
            'stats',
            'statuts'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    */

    /**
     * Formulaire de création.
     *
     * GET /admin/stocks/inventaires/create
     */
    public function create(): View
    {
        $this->authorize('create', InventaireStock::class);

        $emplacements = EmplacementStock::query()
            ->where('actif', true)
            ->orderBy('nom')            // ← CORRIGÉ
            ->get();

        $stocks = Stock::query()
            ->actifs()
            ->orderBy('designation')
            ->get();

        $utilisateurs = User::query()
            ->where('statut', 'actif')  // ← CORRIGÉ
            ->orderBy('nom')
            ->get();

        return view('admin.stocks.inventaires.create', compact(
            'emplacements',
            'stocks',
            'utilisateurs'
        ));
    }

    /**
     * Enregistre un nouvel inventaire.
     *
     * POST /admin/stocks/inventaires
     */
    public function store(
        StoreInventaireRequest $request
    ): RedirectResponse {
        $inventaire = $this->inventaireStockService->creer(
            $request->validatedData(),
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.inventaires.show', $inventaire)
            ->with(
                'success',
                'L’inventaire a été créé avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Détail d'un inventaire.
     *
     * GET /admin/stocks/inventaires/{inventaire}
     */
    public function show(InventaireStock $inventaire): View
    {
        $this->authorize('view', $inventaire);

        $inventaire->load([
            'emplacement',
            'responsable',
            'validePar',
            'lignes.stock',
        ]);

        $resume = $this->inventaireStockService->resume($inventaire);

        return view('admin.stocks.inventaires.show', compact(
            'inventaire',
            'resume'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | COMPTAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistre les comptages physiques.
     *
     * POST /admin/stocks/inventaires/{inventaire}/compter
     */
    public function compter(
        ComptageInventaireRequest $request,
        InventaireStock $inventaire
    ): RedirectResponse {
        $this->inventaireStockService->compterLot(
            $inventaire,
            $request->validatedData()
        );

        return redirect()
            ->route('admin.stocks.inventaires.show', $inventaire)
            ->with(
                'success',
                'Les comptages ont été enregistrés. Les écarts peuvent maintenant être contrôlés.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * Valide l'inventaire et applique les ajustements.
     *
     * POST /admin/stocks/inventaires/{inventaire}/valider
     */
    public function valider(
        ValiderInventaireRequest $request,
        InventaireStock $inventaire
    ): RedirectResponse {
        $this->inventaireStockService->valider(
            $inventaire,
            $request->validatedData()['lignes_a_ajuster'],
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.inventaires.show', $inventaire)
            ->with(
                'success',
                'L’inventaire a été validé et les écarts ont été enregistrés.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    /**
     * Annule un inventaire.
     *
     * POST /admin/stocks/inventaires/{inventaire}/annuler
     */
    public function annuler(InventaireStock $inventaire): RedirectResponse
    {
        $this->authorize('cancel', $inventaire);

        $this->inventaireStockService->annuler($inventaire);

        return redirect()
            ->route('admin.stocks.inventaires.show', $inventaire)
            ->with(
                'success',
                'L’inventaire a été annulé avec succès.'
            );
    }
}