<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatutLot;
use App\Enums\StatutReservation;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Models\EmplacementStock;
use App\Models\InventaireStock;
use App\Models\LotStock;
use App\Models\MouvementStock;
use App\Models\ReservationStock;
use App\Models\Stock;
use App\Services\Stock\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller du catalogue d'articles de stock.
 *
 * PÉRIMÈTRE :
 *
 *   Ce controller gère UNIQUEMENT le catalogue (fiche article) :
 *     - liste, création, modification, affichage ;
 *     - archivage.
 *
 *   Il ne touche JAMAIS aux quantités. Les opérations de stock
 *   (entrées, sorties, inventaires, réservations) sont portées
 *   par leurs propres controllers.
 *
 * SÉPARATION :
 *
 *   Toute la logique métier est dans StockService.
 *   Ce controller ne fait que :
 *     - autoriser via Policy ;
 *     - valider via FormRequest ;
 *     - appeler le Service ;
 *     - rediriger avec un message de session.
 */
class StockController extends Controller
{
    public function __construct(
        private readonly StockService $stockService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des articles de stock.
     *
     * GET /admin/stocks
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Stock::class);

        /*
         * Construction de la requête via le Service.
         */
        $query = $this->stockService->requete(
            recherche: $request->input('recherche'),
            categorie: $request->input('categorie'),
            statut:    $request->input('statut'),
            actif:     $request->has('actif')
                ? $request->boolean('actif')
                : null
        );

        /*
         * Pagination + relations + agrégats.
         */
        $stocks = $this->stockService->paginer($query, 15);

        /*
         * Statistiques globales.
         */
        $stats = $this->stockService->statistiques();

        /*
         * Listes pour les filtres.
         */
        $categories = [
            'vivres'       => 'Vivres',
            'medicaments'  => 'Médicaments',
            'habillement'  => 'Habillement',
            'abris'        => 'Abris',
            'materiel'     => 'Matériel',
            'semences'     => 'Semences',
            'outils'       => 'Outils',
            'autre'        => 'Autre',
        ];

        $statuts = [
            'actif'   => 'Actif',
            'inactif' => 'Inactif',
            'archive' => 'Archivé',
        ];

        return view('admin.stocks.index', compact(
            'stocks',
            'stats',
            'categories',
            'statuts'
        ));
    }


        /*
    |--------------------------------------------------------------------------
    | VUE D'ENSEMBLE
    |--------------------------------------------------------------------------
    |
    | Tableau de bord synthétique du module Stock.
    | Fournit les données pour la page admin.stocks.overview.
    */

    /**
     * Vue d'ensemble du module Stock.
     *
     * @return View
     */
    public function overview(): View
    {
        $this->authorize('viewAny', Stock::class);

        /*
         * Statistiques globales du catalogue.
         */
        $stats = $this->stockService->statistiques();

        /*
         * Derniers mouvements (10).
         */
        $derniersMouvements = MouvementStock::query()
            ->with(['stock', 'utilisateur'])
            ->orderByDesc('date_mouvement')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        /*
         * Lots expirant bientôt (30 jours).
         */
        $lotsExpirantBientot = LotStock::query()
            ->with('stock')
            ->where('statut', StatutLot::ACTIF->value)
            ->where('quantite_actuelle', '>', 0)
            ->whereNotNull('date_expiration')
            ->whereDate('date_expiration', '>=', now()->toDateString())
            ->whereDate('date_expiration', '<=', now()->addDays(30)->toDateString())
            ->orderBy('date_expiration')
            ->limit(10)
            ->get();

        /*
         * Réservations actives (confirmées ou partielles).
         */
        $reservationsActives = ReservationStock::query()
            ->with(['demandeur', 'projet'])
            ->whereIn('statut', [
                StatutReservation::CONFIRMEE->value,
                StatutReservation::PARTIELLE->value,
            ])
            ->withCount('lignes')
            ->orderByDesc('date_reservation')
            ->limit(10)
            ->get();

        /*
         * Inventaires en cours (brouillon ou en_cours).
         */
        $inventairesEnCours = InventaireStock::query()
            ->with('emplacement')
            ->whereIn('statut', ['brouillon', 'en_cours'])
            ->withCount('lignes')
            ->orderByDesc('date_debut')
            ->limit(10)
            ->get();

        return view('admin.stocks.overview', compact(
            'stats',
            'derniersMouvements',
            'lotsExpirantBientot',
            'reservationsActives',
            'inventairesEnCours'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    */

    /**
     * Formulaire de création d'un article.
     *
     * GET /admin/stocks/create
     */
    public function create(): View
    {
        $this->authorize('create', Stock::class);

        $emplacements = EmplacementStock::query()
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('admin.stocks.create', compact('emplacements'));
    }

    /**
     * Enregistre un nouvel article.
     *
     * POST /admin/stocks
     */
    public function store(StoreStockRequest $request): RedirectResponse
    {
        $stock = $this->stockService->creer(
            $request->validatedData()
        );

        return redirect()
            ->route('admin.stocks.show', $stock)
            ->with(
                'success',
                'L’article de stock a été créé avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Fiche détaillée d'un article.
     *
     * GET /admin/stocks/{stock}
     */
    public function show(Stock $stock): View
    {
        $this->authorize('view', $stock);

        /*
         * Chargement des relations nécessaires à la fiche.
         */
        $stock->load([
            'emplacementPrincipal',
            'lots' => function ($query) {
                $query->fefo();
            },
        ]);

        /*
         * Calculs via le Service.
         */
        $quantites     = $this->stockService->quantites($stock);
        $statistiques  = $this->stockService->statistiquesArticle($stock);

        return view('admin.stocks.show', compact(
            'stock',
            'quantites',
            'statistiques'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION
    |--------------------------------------------------------------------------
    */

    /**
     * Formulaire de modification d'un article.
     *
     * GET /admin/stocks/{stock}/edit
     */
    public function edit(Stock $stock): View
    {
        $this->authorize('update', $stock);

        $emplacements = EmplacementStock::query()
            ->where('actif', true)
            ->orderBy('nom')
            ->get();

        return view('admin.stocks.edit', compact(
            'stock',
            'emplacements'
        ));
    }

    /**
     * Mise à jour des informations descriptives.
     *
     * PUT/PATCH /admin/stocks/{stock}
     */
    public function update(
        UpdateStockRequest $request,
        Stock $stock
    ): RedirectResponse {
        $this->stockService->modifier(
            $stock,
            $request->validatedData()
        );

        return redirect()
            ->route('admin.stocks.show', $stock)
            ->with(
                'success',
                'L’article de stock a été modifié avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Archive un article.
     *
     * PATCH /admin/stocks/{stock}/archive
     */
    public function archive(Stock $stock): RedirectResponse
    {
        $this->authorize('archive', $stock);

        $this->stockService->archiver($stock);

        return redirect()
            ->route('admin.stocks.show', $stock)
            ->with(
                'success',
                'L’article de stock a été archivé avec succès.'
            );
    }
}