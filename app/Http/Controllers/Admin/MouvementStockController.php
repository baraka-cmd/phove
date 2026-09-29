<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MouvementStock;
use App\Models\Projet;
use App\Models\Stock;
use App\Models\User;
use App\Services\Stock\EntreeStockService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller des mouvements de stock.
 *
 * PÉRIMÈTRE :
 *
 *   Ce controller est en LECTURE SEULE.
 *
 *   Les mouvements sont un JOURNAL IMMUABLE :
 *     - aucune création directe ;
 *     - aucune modification ;
 *     - aucune suppression.
 *
 *   Ils sont créés EXCLUSIVEMENT par les Services métier.
 *
 *   Seules les consultations et les exports sont permis.
 */
class MouvementStockController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE
    |--------------------------------------------------------------------------
    */

    /**
     * Historique des mouvements.
     *
     * GET /admin/stocks/mouvements
     */
        public function index(Request $request): View
    {
        $this->authorize('viewAny', MouvementStock::class);

        $query = MouvementStock::query()
            ->with([
                'stock',
                'lotStock',
                'emplacement',
                'utilisateur',
                'projet',
                'bonSortie',
            ]);

        /*
         * Recherche générale.
         */
        if ($request->filled('recherche')) {
            $r = trim($request->input('recherche'));

            $query->where(function ($q) use ($r) {
                $q->where('reference_document', 'like', "%{$r}%")
                  ->orWhere('motif', 'like', "%{$r}%")
                  ->orWhereHas('stock', function ($sq) use ($r) {
                      $sq->where('designation', 'like', "%{$r}%")
                         ->orWhere('reference', 'like', "%{$r}%");
                  })
                  ->orWhereHas('lotStock', function ($lq) use ($r) {
                      $lq->where('numero_lot', 'like', "%{$r}%");
                  });
            });
        }

        /*
         * Filtres.
         */
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('nature')) {
            $query->where('nature', $request->input('nature'));
        }

        if ($request->filled('stock_id')) {
            $query->where('stock_id', $request->integer('stock_id'));
        }

        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->integer('projet_id'));
        }

        if ($request->filled('utilisateur_id')) {
            $query->where('utilisateur_id', $request->integer('utilisateur_id'));
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_mouvement', '>=', $request->input('date_debut'));
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_mouvement', '<=', $request->input('date_fin'));
        }

        $mouvements = $query
            ->orderByDesc('date_mouvement')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        /*
         * Statistiques globales.
         */
        $totalEntrees = (clone $query)->entrees()->count();
        $totalSorties = (clone $query)->sorties()->count();
        $totalAjustements = (clone $query)->ajustements()->count();

        /*
         * Listes pour les filtres.
         */
        $types = [
            'entree'     => 'Entrée',
            'sortie'     => 'Sortie',
            'transfert'  => 'Transfert',
            'ajustement' => 'Ajustement',
        ];

        $stocks = Stock::query()
            ->orderBy('designation')
            ->get();

        $utilisateurs = User::query()
            ->orderBy('nom')
            ->get();

        $projets = Projet::query()
            ->orderBy('titre')   // ✅ CORRIGÉ (avant : 'nom')
            ->get();

        return view('admin.stocks.mouvements.index', compact(
            'mouvements',
            'totalEntrees',
            'totalSorties',
            'totalAjustements',
            'types',
            'stocks',
            'utilisateurs',
            'projets'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Détail d'un mouvement.
     *
     * GET /admin/stocks/mouvements/{mouvement}
     */
    public function show(MouvementStock $mouvement): View
    {
        $this->authorize('view', $mouvement);

        $mouvement->load([
            'stock',
            'lotStock',
            'emplacement',
            'utilisateur',
            'projet',
            'bonSortie',
        ]);

        return view('admin.stocks.mouvements.show', compact('mouvement'));
    }
}