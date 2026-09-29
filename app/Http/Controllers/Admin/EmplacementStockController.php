<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmplacementStockRequest;
use App\Http\Requests\UpdateEmplacementStockRequest;
use App\Models\EmplacementStock;
use App\Services\Stock\EmplacementStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller des emplacements de stock.
 *
 * PÉRIMÈTRE :
 *
 *   Ce controller gère le RÉFÉRENTIEL des emplacements :
 *     - liste, création, consultation, modification ;
 *     - activation / désactivation ;
 *     - suppression (uniquement si non utilisé).
 *
 *   Un emplacement est une RÉFÉRENCE ORGANISATIONNELLE :
 *     - emplacement principal d'un article ;
 *     - localisation d'un mouvement ;
 *     - périmètre d'un inventaire.
 *
 * INVARIANTS :
 *
 *   - Un emplacement utilisé par des mouvements, des
 *     inventaires ou rattaché à un article NE PEUT PAS
 *     être supprimé physiquement : on le désactive.
 *
 * SÉPARATION :
 *
 *   Toute la logique métier est dans EmplacementStockService.
 */
class EmplacementStockController extends Controller
{
    public function __construct(
        private readonly EmplacementStockService $emplacementStockService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des emplacements.
     *
     * GET /admin/stocks/emplacements
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', EmplacementStock::class);

        $query = $this->emplacementStockService->requete(
            recherche: $request->input('recherche'),
            actif:     $request->has('actif')
                ? $request->boolean('actif')
                : null
        );

        $emplacements = $query
            ->withCount('stocksPrincipaux')
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        /*
         * Statistiques.
         */
        $stats = $this->emplacementStockService->statistiques();

        return view('admin.stocks.emplacements.index', compact(
            'emplacements',
            'stats'
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
     * GET /admin/stocks/emplacements/create
     */
    public function create(): View
    {
        $this->authorize('create', EmplacementStock::class);

        return view('admin.stocks.emplacements.create');
    }

    /**
     * Enregistre un nouvel emplacement.
     *
     * POST /admin/stocks/emplacements
     */
    public function store(
        StoreEmplacementStockRequest $request
    ): RedirectResponse {
        $emplacement = $this->emplacementStockService->creer(
            $request->validatedData()
        );

        return redirect()
            ->route('admin.stocks.emplacements.show', $emplacement)
            ->with(
                'success',
                'L’emplacement a été créé avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Détail d'un emplacement.
     *
     * GET /admin/stocks/emplacements/{emplacement}
     */
    public function show(EmplacementStock $emplacement): View
    {
        $this->authorize('view', $emplacement);

        $emplacement->load([
            'stocksPrincipaux' => function ($query) {
                $query->orderBy('designation');
            },
        ]);

        /*
         * Derniers mouvements localisés dans cet emplacement.
         */
        $derniersMouvements = $emplacement->mouvements()
            ->with(['stock', 'utilisateur'])
            ->orderByDesc('date_mouvement')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('admin.stocks.emplacements.show', compact(
            'emplacement',
            'derniersMouvements'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION
    |--------------------------------------------------------------------------
    */

    /**
     * Formulaire de modification.
     *
     * GET /admin/stocks/emplacements/{emplacement}/edit
     */
    public function edit(EmplacementStock $emplacement): View
    {
        $this->authorize('update', $emplacement);

        return view(
            'admin.stocks.emplacements.edit',
            compact('emplacement')
        );
    }

    /**
     * Met à jour un emplacement.
     *
     * PUT/PATCH /admin/stocks/emplacements/{emplacement}
     */
    public function update(
        UpdateEmplacementStockRequest $request,
        EmplacementStock $emplacement
    ): RedirectResponse {
        $this->emplacementStockService->modifier(
            $emplacement,
            $request->validatedData()
        );

        return redirect()
            ->route('admin.stocks.emplacements.show', $emplacement)
            ->with(
                'success',
                'L’emplacement a été modifié avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CYCLE DE VIE
    |--------------------------------------------------------------------------
    */

    /**
     * Active un emplacement.
     *
     * POST /admin/stocks/emplacements/{emplacement}/activer
     */
    public function activer(EmplacementStock $emplacement): RedirectResponse
    {
        $this->authorize('activate', $emplacement);

        $this->emplacementStockService->activer($emplacement);

        return redirect()
            ->route('admin.stocks.emplacements.show', $emplacement)
            ->with(
                'success',
                'L’emplacement a été activé avec succès.'
            );
    }

    /**
     * Désactive un emplacement.
     *
     * POST /admin/stocks/emplacements/{emplacement}/desactiver
     */
    public function desactiver(EmplacementStock $emplacement): RedirectResponse
    {
        $this->authorize('deactivate', $emplacement);

        $this->emplacementStockService->desactiver($emplacement);

        return redirect()
            ->route('admin.stocks.emplacements.show', $emplacement)
            ->with(
                'success',
                'L’emplacement a été désactivé avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION
    |--------------------------------------------------------------------------
    */

    /**
     * Supprime un emplacement.
     *
     * DELETE /admin/stocks/emplacements/{emplacement}
     *
     * Refusé si l'emplacement est utilisé (mouvements,
     * inventaires ou articles rattachés).
     */
    public function destroy(EmplacementStock $emplacement): RedirectResponse
    {
        $this->authorize('delete', $emplacement);

        $this->emplacementStockService->supprimer($emplacement);

        return redirect()
            ->route('admin.stocks.emplacements.index')
            ->with(
                'success',
                'L’emplacement a été supprimé avec succès.'
            );
    }
}