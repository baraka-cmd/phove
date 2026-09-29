<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EffectuerSortieRequest;
use App\Http\Requests\StoreBonSortieRequest;
use App\Http\Requests\ValiderBonSortieRequest;
use App\Models\BonSortie;
use App\Models\Projet;
use App\Models\Stock;
use App\Models\User;
use App\Services\Stock\SortieStockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller des bons de sortie.
 *
 * PÉRIMÈTRE :
 *
 *   Ce controller gère le WORKFLOW documentaire des bons
 *   de sortie :
 *     - liste, création, consultation ;
 *     - soumission, validation, préparation, exécution, annulation.
 *
 *   Les transitions documentaires (soumettre, valider, préparer,
 *   annuler) ne modifient PAS le stock.
 *
 *   Seule l'action "sortir" (release) déclenche l'exécution
 *   physique via SortieStockService::effectuerSortie().
 *
 * SÉPARATION :
 *
 *   Toute la logique métier est dans SortieStockService.
 */
class BonSortieController extends Controller
{
    public function __construct(
        private readonly SortieStockService $sortieStockService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | LISTE
    |--------------------------------------------------------------------------
    */

    /**
     * Liste des bons de sortie.
     *
     * GET /admin/stocks/sorties
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', BonSortie::class);

        $query = $this->sortieStockService->requeteBons(
            recherche: $request->input('recherche'),
            statut:    $request->input('statut'),
            projetId:  $request->filled('projet_id')
                ? $request->integer('projet_id')
                : null,
            dateDebut: $request->input('date_debut'),
            dateFin:   $request->input('date_fin')
        );

        $bonsSortie = $query
            ->with([
                'demandeur',
                'validateur',
                'preparateur',
                'projet',
            ])
            ->withCount('lignes')
            ->orderByDesc('date_demande')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        /*
         * Statistiques (comptage par statut sur le périmètre filtré).
         */
        $stats = $this->sortieStockService->statistiques(
            $request->input('statut')
        );

        /*
         * Listes pour les filtres.
         */
        $statuts = [
            'brouillon'      => 'Brouillon',
            'soumis'         => 'Soumis',
            'valide'         => 'Validé',
            'en_preparation' => 'En préparation',
            'sorti'          => 'Sorti',
            'annule'         => 'Annulé',
        ];

        $projets = Projet::query()
            ->orderBy('titre')
            ->get();

        return view('admin.stocks.sorties.index', compact(
            'bonsSortie',
            'stats',
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
     * Formulaire de création d'un bon de sortie.
     *
     * GET /admin/stocks/sorties/create
     */
    public function create(): View
    {
        $this->authorize('create', BonSortie::class);

        $stocks = Stock::query()
            ->actifs()
            ->orderBy('designation')
            ->get();

        $utilisateurs = User::query()
            ->where('statut', 'actif')  // ← CORRIGÉ
            ->orderBy('nom')
            ->get();

        $projets = Projet::query()
            ->orderBy('titre')
            ->get();

        return view('admin.stocks.sorties.create', compact(
            'stocks',
            'utilisateurs',
            'projets'
        ));
    }

    /**
     * Enregistre un nouveau bon de sortie.
     *
     * POST /admin/stocks/sorties
     */
    public function store(StoreBonSortieRequest $request): RedirectResponse
    {
        $bon = $this->sortieStockService->creerBon(
            $request->validatedData(),
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.sorties.show', $bon)
            ->with(
                'success',
                'Le bon de sortie a été créé avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE
    |--------------------------------------------------------------------------
    */

    /**
     * Détail d'un bon de sortie.
     *
     * GET /admin/stocks/sorties/{bonSortie}
     */
    public function show(BonSortie $bonSortie): View
    {
        $this->authorize('view', $bonSortie);

        $bonSortie->load([
            'demandeur',
            'validateur',
            'preparateur',
            'projet',
            'lignes.stock',
        ]);

        $resume = $this->sortieStockService->resumeBon($bonSortie);

        return view('admin.stocks.sorties.show', compact(
            'bonSortie',
            'resume'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | WORKFLOW — TRANSITIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Soumet un bon (brouillon → soumis).
     *
     * POST /admin/stocks/sorties/{bonSortie}/soumettre
     */
    public function soumettre(BonSortie $bonSortie): RedirectResponse
    {
        $this->authorize('submit', $bonSortie);

        $this->sortieStockService->soumettreBon(
            $bonSortie,
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.sorties.show', $bonSortie)
            ->with(
                'success',
                'Le bon de sortie a été soumis avec succès.'
            );
    }

    /**
     * Valide un bon (soumis → valide).
     *
     * POST /admin/stocks/sorties/{bonSortie}/valider
     */
    public function valider(
        ValiderBonSortieRequest $request,
        BonSortie $bonSortie
    ): RedirectResponse {
        $this->sortieStockService->validerBon(
            $bonSortie,
            $request->validatedData()['lignes'],
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.sorties.show', $bonSortie)
            ->with(
                'success',
                'Le bon de sortie a été validé avec succès.'
            );
    }

    /**
     * Affiche l'écran de validation.
     *
     * GET /admin/stocks/sorties/{bonSortie}/validation
     */
    public function validation(BonSortie $bonSortie): View
    {
        $this->authorize('validate', $bonSortie);

        $bonSortie->load([
            'demandeur',
            'projet',
            'lignes.stock',
        ]);

        return view('admin.stocks.sorties.validation', compact('bonSortie'));
    }

    /**
     * Met un bon en préparation (valide → en_preparation).
     *
     * POST /admin/stocks/sorties/{bonSortie}/preparation
     */
    public function preparation(BonSortie $bonSortie): RedirectResponse
    {
        $this->authorize('prepare', $bonSortie);

        $this->sortieStockService->mettreEnPreparation(
            $bonSortie,
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.sorties.show', $bonSortie)
            ->with(
                'success',
                'Le bon de sortie est maintenant en préparation.'
            );
    }

    /**
     * Affiche l'écran de préparation.
     *
     * GET /admin/stocks/sorties/{bonSortie}/preparation
     */
    public function preparationEcran(BonSortie $bonSortie): View
    {
        $this->authorize('release', $bonSortie);

        $bonSortie->load([
            'demandeur',
            'projet',
            'lignes.stock',
        ]);

        $lignes = $bonSortie->lignes;

        return view(
            'admin.stocks.sorties.preparation',
            compact('bonSortie', 'lignes')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXÉCUTION PHYSIQUE
    |--------------------------------------------------------------------------
    */

    /**
     * Effectue la sortie physique.
     *
     * POST /admin/stocks/sorties/{bonSortie}/sortir
     */
    public function sortir(
        EffectuerSortieRequest $request,
        BonSortie $bonSortie
    ): RedirectResponse {
        $this->sortieStockService->effectuerSortie(
            $bonSortie,
            $request->validatedData()['lignes'],
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.sorties.show', $bonSortie)
            ->with(
                'success',
                'La sortie de stock a été effectuée avec succès.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    /**
     * Annule un bon de sortie.
     *
     * POST /admin/stocks/sorties/{bonSortie}/annuler
     */
    public function annuler(BonSortie $bonSortie): RedirectResponse
    {
        $this->authorize('cancel', $bonSortie);

        $this->sortieStockService->annulerBon(
            $bonSortie,
            auth()->id()
        );

        return redirect()
            ->route('admin.stocks.sorties.show', $bonSortie)
            ->with(
                'success',
                'Le bon de sortie a été annulé avec succès.'
            );
    }
}