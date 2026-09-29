<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmerReservationStockRequest;
use App\Http\Requests\StoreReservationStockRequest;
use App\Models\Projet;
use App\Models\ReservationStock;
use App\Models\Stock;
use App\Models\User;
use App\Services\Stock\ReservationStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller des réservations de stock.
 *
 * PÉRIMÈTRE :
 *
 *   Ce controller gère le WORKFLOW des réservations :
 *     - liste, création, consultation ;
 *     - confirmation (blocage réel des quantités) ;
 *     - annulation (libération des quantités).
 *
 *   Aucune opération ne modifie les quantités physiques :
 *   la réservation est un BLOCAGE LOGIQUE.
 *
 * SÉPARATION :
 *
 *   Toute la logique métier est dans ReservationStockService.
 */
class ReservationStockController extends Controller
{
    public function __construct(
        private readonly ReservationStockService $reservationStockService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des réservations.
     *
     * GET /admin/stocks/reservations
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ReservationStock::class);

        $query = $this->reservationStockService->requete(
            recherche: $request->input('recherche'),
            statut:    $request->input('statut'),
            projetId:  $request->filled('projet_id')
                ? $request->integer('projet_id')
                : null,
            dateDebut: $request->input('date_debut'),
            dateFin:   $request->input('date_fin')
        );

        $reservations = $query
            ->with([
                'demandeur',
                'projet',
            ])
            ->withCount('lignes')
            ->orderByDesc('date_reservation')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        /*
         * Listes pour les filtres.
         */
        $statuts = [
            'brouillon'  => 'Brouillon',
            'confirmee'  => 'Confirmée',
            'partielle'  => 'Partielle',
            'consommee'  => 'Consommée',
            'expiree'    => 'Expirée',
            'annulee'    => 'Annulée',
        ];

        $projets = Projet::query()
            ->orderBy('titre')
            ->get();

        return view('admin.stocks.reservations.index', compact(
            'reservations',
            'statuts',
            'projets'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉATION
    |--------------------------------------------------------------------------
    */

    /**
     * Formulaire de création d'une réservation.
     *
     * GET /admin/stocks/reservations/create
     */
    public function create(): View
    {
        $this->authorize('create', ReservationStock::class);

        $stocks = Stock::query()->actifs()->orderBy('designation')->get();

        $demandeurs = User::query()
            ->where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        $projets = Projet::query()
            ->orderBy('titre')
            ->get();

        return view('admin.stocks.reservations.create', compact(
            'stocks',
            'demandeurs',
            'projets'
        ));
    }

    /**
     * Enregistre une nouvelle réservation.
     *
     * POST /admin/stocks/reservations
     */
    public function store(
        StoreReservationStockRequest $request
    ): RedirectResponse {
        $reservation = $this->reservationStockService->creer(
            $request->validatedData(),
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.reservations.show', $reservation)
            ->with(
                'success',
                'La réservation de stock a été créée avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Détail d'une réservation.
     *
     * GET /admin/stocks/reservations/{reservation}
     */
    public function show(ReservationStock $reservation): View
    {
        $this->authorize('view', $reservation);

        $reservation->load([
            'demandeur',
            'projet',
            'lignes.stock',
        ]);

        /*
         * Résumé calculé par le Service.
         */
        $resume = $this->reservationStockService->resume($reservation);

        return view('admin.stocks.reservations.show', compact(
            'reservation',
            'resume'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRMATION
    |--------------------------------------------------------------------------
    */

    /**
     * Confirme une réservation.
     *
     * POST /admin/stocks/reservations/{reservation}/confirmer
     */
    public function confirmer(
        ConfirmerReservationStockRequest $request,
        ReservationStock $reservation
    ): RedirectResponse {
        $this->reservationStockService->confirmer(
            $reservation,
            $request->validatedData()['lignes'],
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.reservations.show', $reservation)
            ->with(
                'success',
                'La réservation a été confirmée avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    /**
     * Annule une réservation.
     *
     * POST /admin/stocks/reservations/{reservation}/annuler
     */
    public function annuler(ReservationStock $reservation): RedirectResponse
    {
        $this->authorize('cancel', $reservation);

        $this->reservationStockService->annuler($reservation);

        return redirect()
            ->route('admin.stocks.reservations.show', $reservation)
            ->with(
                'success',
                'La réservation a été annulée et les quantités ont été libérées.'
            );
    }
}