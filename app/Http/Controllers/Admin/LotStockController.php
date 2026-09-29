<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BloquerLotRequest;
use App\Models\LotStock;
use App\Models\Stock;
use App\Services\Stock\LotStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller des lots de stock.
 *
 * PÉRIMÈTRE :
 *
 *   Ce controller gère :
 *     - la liste et la consultation des lots ;
 *     - le blocage / déblocage manuel ;
 *     - l'écran et le traitement des expirations.
 *
 *   La CRÉATION d'un lot n'est PAS exposée :
 *     un lot se crée toujours via une entrée de stock
 *     (EntreeStockService::obtenirOuCreerLot()).
 *
 * SÉPARATION :
 *
 *   Toute la logique métier est dans LotStockService.
 */
class LotStockController extends Controller
{
    public function __construct(
        private readonly LotStockService $lotStockService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des lots.
     *
     * GET /admin/stocks/lots
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', LotStock::class);

        $query = $this->lotStockService->requete(
            recherche:        $request->input('recherche'),
            statut:           $request->input('statut'),
            stockId:          $request->filled('stock_id')
                ? $request->integer('stock_id')
                : null,
            expires:          $request->boolean('expires'),
            expirationProche: $request->boolean('expiration_proche')
        );

        $lots = $query
            ->orderByRaw('CASE WHEN date_expiration IS NULL THEN 1 ELSE 0 END')
            ->orderBy('date_expiration')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        /*
         * Statistiques globales.
         */
        $stats = $this->lotStockService->statistiques();

        return view('admin.stocks.lots.index', compact(
            'lots',
            'stats'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Détail d'un lot.
     *
     * GET /admin/stocks/lots/{lot}
     */
    public function show(LotStock $lot): View
    {
        $this->authorize('view', $lot);

        $lot->load([
            'stock',
            'mouvements' => function ($query) {
                $query
                    ->with([
                        'utilisateur',
                        'projet',
                        'emplacement',
                    ])
                    ->orderByDesc('date_mouvement')
                    ->orderByDesc('id');
            },
        ]);

        return view('admin.stocks.lots.show', compact('lot'));
    }

    /*
    |--------------------------------------------------------------------------
    | BLOCAGE / DÉBLOCAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Bloque un lot.
     *
     * POST /admin/stocks/lots/{lot}/bloquer
     */
    public function bloquer(
        BloquerLotRequest $request,
        LotStock $lot
    ): RedirectResponse {
        $this->lotStockService->bloquer($lot);

        return redirect()
            ->route('admin.stocks.lots.show', $lot)
            ->with(
                'success',
                'Le lot a été bloqué avec succès.'
            );
    }

    /**
     * Débloque un lot.
     *
     * POST /admin/stocks/lots/{lot}/debloquer
     */
    public function debloquer(LotStock $lot): RedirectResponse
    {
        $this->authorize('unblock', $lot);

        $this->lotStockService->debloquer($lot);

        return redirect()
            ->route('admin.stocks.lots.show', $lot)
            ->with(
                'success',
                'Le lot a été débloqué avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPIRATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Écran de surveillance des expirations.
     *
     * GET /admin/stocks/lots/expiration
     */
    public function expiration(Request $request): View
    {
        $this->authorize('viewExpiration', LotStock::class);

        $periode = $request->input('periode', '30');

        /*
         * Construction de la requête selon la période.
         */
        $query = LotStock::query()
            ->with('stock')
            ->whereNotNull('date_expiration');

        match ($periode) {
            'expired' => $query->expires(),
            'all'     => null,
            default   => $query->expirantBientot((int) $periode),
        };

        /*
         * Filtre de recherche.
         */
        if ($request->filled('recherche')) {
            $r = trim($request->input('recherche'));

            $query->where(function ($q) use ($r) {
                $q->where('numero_lot', 'like', "%{$r}%")
                  ->orWhereHas('stock', function ($sq) use ($r) {
                      $sq->where('designation', 'like', "%{$r}%")
                         ->orWhere('reference', 'like', "%{$r}%");
                  });
            });
        }

        $lots = $query
            ->orderBy('date_expiration')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.stocks.lots.expiration', compact(
            'lots',
            'periode'
        ));
    }

    /**
     * Traite les lots arrivés à expiration.
     *
     * POST /admin/stocks/lots/expiration
     */
    public function traiterExpirations(): RedirectResponse
    {
        $this->authorize('processExpirations', LotStock::class);

        $nombre = $this->lotStockService->traiterExpirations();

        return redirect()
            ->route('admin.stocks.lots.index')
            ->with(
                'success',
                sprintf(
                    '%d lot(s) arrivé(s) à expiration ont été traité(s).',
                    $nombre
                )
            );
    }
}