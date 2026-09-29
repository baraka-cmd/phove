<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEntreeStockRequest;
use App\Models\EmplacementStock;
use App\Models\Projet;
use App\Models\Stock;
use App\Services\Stock\EntreeStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller des entrées de stock.
 *
 * PÉRIMÈTRE :
 *
 *   Ce controller gère :
 *     - la liste des entrées (historique des mouvements ENTREE) ;
 *     - le formulaire de réception ;
 *     - l'enregistrement d'une entrée ;
 *     - l'affichage du détail d'une entrée.
 *
 * SÉPARATION :
 *
 *   Toute la logique métier est dans EntreeStockService.
 *   Le controller ne fait qu'orchestrer :
 *     - autorisation via Policy ;
 *     - validation via FormRequest ;
 *     - appel du Service ;
 *     - redirection avec message.
 */
class EntreeStockController extends Controller
{
    public function __construct(
        private readonly EntreeStockService $entreeStockService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE
    |--------------------------------------------------------------------------
    */

    /**
     * Historique des entrées de stock.
     *
     * GET /admin/stocks/entrees
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Stock::class);

        /*
         * Requête préparée par le Service.
         */
        $query = $this->entreeStockService->requete(
            recherche:  $request->input('recherche'),
            dateDebut:  $request->input('date_debut'),
            dateFin:    $request->input('date_fin'),
            nature:     $request->input('nature')
        );

        /*
         * Pagination + relations.
         */
        $entrees = $query
            ->with([
                'stock',
                'lotStock',
                'emplacement',
                'utilisateur',
                'projet',
            ])
            ->orderByDesc('date_mouvement')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        /*
         * Filtres exposés à la vue.
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

        return view('admin.stocks.entrees.index', compact(
            'entrees',
            'categories'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    */

    /**
     * Formulaire de réception.
     *
     * GET /admin/stocks/entrees/create
     */
    public function create(): View
    {
        $this->authorize('create', Stock::class);

        /*
         * Articles actifs uniquement.
         */
        $stocks = Stock::query()
            ->actifs()
            ->orderBy('designation')
            ->get();

        /*
         * Emplacements actifs.
         */
        $emplacements = EmplacementStock::query()
            ->where('actif', true)
            ->orderBy('titre')
            ->get();

        /*
         * Projets disponibles.
         */
        $projets = Projet::query()
            ->orderBy('id')
            ->get();

        return view('admin.stocks.entrees.create', compact(
            'stocks',
            'emplacements',
            'projets'
        ));
    }

    /**
     * Enregistre une entrée de stock.
     *
     * POST /admin/stocks/entrees
     */
    public function store(StoreEntreeStockRequest $request): RedirectResponse
    {
        $entree = $this->entreeStockService->enregistrer(
            $request->validatedData(),
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.entrees.show', $entree)
            ->with(
                'success',
                'L’entrée de stock a été enregistrée avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Détail d'une entrée de stock.
     *
     * GET /admin/stocks/entrees/{entree}
     */
    public function show(int $entree): View
    {
        $this->authorize('viewAny', Stock::class);

        $mouvement = $this->entreeStockService->trouver($entree);

        abort_if(
            $mouvement === null,
            404,
            'L’entrée de stock demandée est introuvable.'
        );

        return view('admin.stocks.entrees.show', compact('mouvement'));
    }
}