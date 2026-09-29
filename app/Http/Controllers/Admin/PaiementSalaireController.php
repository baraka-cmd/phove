<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaiementSalaireRequest;
use App\Models\PaiementSalaire;
use App\Models\Salaire;
use App\Services\PaiementSalaireService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaiementSalaireController extends Controller
{
    /**
     * Service responsable de la logique métier
     * liée aux paiements de salaires.
     */
    protected PaiementSalaireService $paiementSalaireService;

    /**
     * Injection du service.
     */
    public function __construct(
        PaiementSalaireService $paiementSalaireService
    ) {
        $this->paiementSalaireService = $paiementSalaireService;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la liste générale des paiements.
     *
     * GET /admin/paiements-salaires
     */
    public function index(Request $request): View
    {
        $this->authorize(
            'viewAny',
            PaiementSalaire::class
        );

        /*
         * Filtres.
         */
        $mois = $request->filled('mois')
            ? trim((string) $request->input('mois'))
            : null;

        $modePaiement = $request->filled('mode_paiement')
            ? trim((string) $request->input('mode_paiement'))
            : null;

        $recherche = trim(
            (string) $request->input('recherche', '')
        );

        /*
         * Requête principale.
         */
        $query = PaiementSalaire::query()
            ->with([
                'salaire.membre',
                'salaire.organe',
                'payePar',
            ]);

        /*
         * Filtre par mois du salaire.
         *
         * IMPORTANT :
         * on ne doit jamais appeler duMois(null).
         */
        if ($mois !== null && $mois !== '') {
            $query->duMois($mois);
        }

        /*
         * Filtre par mode de paiement.
         */
        if ($modePaiement !== null && $modePaiement !== '') {
            $query->mode($modePaiement);
        }

        /*
         * Recherche.
         *
         * Recherche sur :
         * - référence du paiement ;
         * - référence du salaire ;
         * - nom ;
         * - post-nom ;
         * - prénom du membre.
         */
        $this->appliquerRecherche(
            $query,
            $recherche
        );

        /*
         * Pagination.
         */
        $paiements = $query
            ->recents()
            ->paginate(20)
            ->withQueryString();

        /*
         * Statistiques basées sur exactement
         * les mêmes filtres.
         */
        $stats = $this->calculerStatistiques(
            $query
        );

        /*
         * Modes disponibles.
         */
        $modesPaiement = PaiementSalaire::MODES_PAIEMENT;

        return view(
            'admin.paiements-salaires.index',
            compact(
                'paiements',
                'stats',
                'modesPaiement',
                'mois',
                'modePaiement',
                'recherche'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    /**
     * Formulaire de paiement d'un salaire.
     *
     * GET /admin/salaires/{salaire}/paiements/create
     */
    public function create(
        Salaire $salaire
    ): View {
        $this->authorize(
            'pay',
            $salaire
        );

        $salaire->load([
            'membre',
            'organe',
            'paiements' => function ($query) {
                $query
                    ->with('payePar')
                    ->orderByDesc('date_paiement')
                    ->orderByDesc('id');
            },
        ]);

        /*
         * Résumé financier.
         */
        $totalPaye = $this->paiementSalaireService
            ->totalPaye($salaire);

        $resteAPayer = $this->paiementSalaireService
            ->resteAPayer($salaire);

        $pourcentagePaye = $this->paiementSalaireService
            ->pourcentagePaye($salaire);

        /*
         * Modes disponibles.
         *
         * MODES_PAIEMENT est la source officielle
         * définie dans le modèle.
         */
        $modesPaiement = PaiementSalaire::MODES_PAIEMENT;

        return view(
            'admin.paiements-salaires.create',
            compact(
                'salaire',
                'totalPaye',
                'resteAPayer',
                'pourcentagePaye',
                'modesPaiement'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    /**
     * Enregistre un paiement.
     *
     * POST /admin/salaires/{salaire}/paiements
     */
    public function store(
        StorePaiementSalaireRequest $request,
        Salaire $salaire
    ): RedirectResponse {
        $this->authorize(
            'pay',
            $salaire
        );

        $this->paiementSalaireService->enregistrer(
            $salaire,
            $request->validatedData(),
            auth()->id()
        );

        /*
         * Actualisation du salaire après paiement.
         */
        $salaire->refresh();

        if ($salaire->estPaye()) {

            $message =
                'Le paiement a été enregistré avec succès. '
                . 'Le salaire est maintenant entièrement payé.';

        } elseif ($salaire->estPartiellementPaye()) {

            $message =
                'Le paiement a été enregistré avec succès. '
                . 'Le salaire est maintenant partiellement payé.';

        } else {

            $message =
                'Le paiement a été enregistré avec succès.';
        }

        return redirect()
            ->route(
                'admin.salaires.show',
                $salaire
            )
            ->with(
                'success',
                $message
            );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le détail d'un paiement.
     *
     * GET /admin/paiements-salaires/{paiement}
     */
    public function show(
        PaiementSalaire $paiement
    ): View {
        $this->authorize(
            'view',
            $paiement
        );

        $paiement->load([
            'salaire.membre',
            'salaire.organe',
            'salaire.createur',
            'salaire.validePar',
            'payePar',
        ]);

        $salaire = $paiement->salaire;

        /*
         * Historique des paiements du salaire.
         */
        $paiements = $this->paiementSalaireService
            ->paiementsDuSalaire(
                $salaire
            );

        /*
         * Résumé financier.
         */
        $totalPaye = $this->paiementSalaireService
            ->totalPaye($salaire);

        $resteAPayer = $this->paiementSalaireService
            ->resteAPayer($salaire);

        $pourcentagePaye = $this->paiementSalaireService
            ->pourcentagePaye($salaire);

        return view(
            'admin.paiements-salaires.show',
            compact(
                'paiement',
                'salaire',
                'paiements',
                'totalPaye',
                'resteAPayer',
                'pourcentagePaye'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAIEMENTS D'UN SALAIRE
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche l'historique des paiements d'un salaire.
     *
     * GET /admin/salaires/{salaire}/paiements
     */
    public function paiementsDuSalaire(
        Salaire $salaire
    ): View {
        $this->authorize(
            'viewPayments',
            $salaire
        );

        $salaire->load([
            'membre',
            'organe',
        ]);

        $paiements = $this->paiementSalaireService
            ->paiementsDuSalaire(
                $salaire
            );

        $totalPaye = $this->paiementSalaireService
            ->totalPaye($salaire);

        $resteAPayer = $this->paiementSalaireService
            ->resteAPayer($salaire);

        $pourcentagePaye = $this->paiementSalaireService
            ->pourcentagePaye($salaire);

        return view(
            'admin.paiements-salaires.salaire',
            compact(
                'salaire',
                'paiements',
                'totalPaye',
                'resteAPayer',
                'pourcentagePaye'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REÇU
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le reçu d'un paiement.
     *
     * GET /admin/paiements-salaires/{paiement}/recu
     */
    public function recu(
        PaiementSalaire $paiement
    ): View {
        $this->authorize(
            'view',
            $paiement
        );

        $paiement->load([
            'salaire.membre',
            'salaire.organe',
            'payePar',
        ]);

        $salaire = $paiement->salaire;

        return view(
            'admin.paiements-salaires.recu',
            compact(
                'paiement',
                'salaire'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STATISTIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche les statistiques mensuelles.
     *
     * GET /admin/paiements-salaires/statistiques
     */
    public function statistiques(
        Request $request
    ): View {
        $this->authorize(
            'viewAny',
            PaiementSalaire::class
        );

        $mois = $request->filled('mois')
            ? trim((string) $request->input('mois'))
            : now()->format('Y-m');

        /*
         * Requête de base.
         *
         * Ici $mois possède toujours une valeur valide
         * avant l'appel à duMois().
         */
        $query = PaiementSalaire::query()
            ->duMois($mois);

        /*
         * Statistiques générales.
         */
        $nombrePaiements = (clone $query)
            ->count();

        $montantTotal = (float) (
            (clone $query)
                ->sum('montant')
        );

        /*
         * Espèces.
         */
        $nombreEspeces = (clone $query)
            ->where(
                'mode_paiement',
                PaiementSalaire::MODE_ESPECES
            )
            ->count();

        $montantEspeces = (float) (
            (clone $query)
                ->where(
                    'mode_paiement',
                    PaiementSalaire::MODE_ESPECES
                )
                ->sum('montant')
        );

        /*
         * Virement bancaire.
         */
        $nombreBanque = (clone $query)
            ->where(
                'mode_paiement',
                PaiementSalaire::MODE_VIREMENT
            )
            ->count();

        $montantBanque = (float) (
            (clone $query)
                ->where(
                    'mode_paiement',
                    PaiementSalaire::MODE_VIREMENT
                )
                ->sum('montant')
        );

        /*
         * Mobile Money.
         */
        $nombreMobileMoney = (clone $query)
            ->where(
                'mode_paiement',
                PaiementSalaire::MODE_MOBILE_MONEY
            )
            ->count();

        $montantMobileMoney = (float) (
            (clone $query)
                ->where(
                    'mode_paiement',
                    PaiementSalaire::MODE_MOBILE_MONEY
                )
                ->sum('montant')
        );

        /*
         * Chèques.
         */
        $nombreCheques = (clone $query)
            ->where(
                'mode_paiement',
                PaiementSalaire::MODE_CHEQUE
            )
            ->count();

        $montantCheques = (float) (
            (clone $query)
                ->where(
                    'mode_paiement',
                    PaiementSalaire::MODE_CHEQUE
                )
                ->sum('montant')
        );

        /*
         * Tableau destiné à la vue.
         */
        $stats = [
            'nombre_paiements' => $nombrePaiements,

            'nombre' => $nombrePaiements,

            'montant_total' => $montantTotal,

            'montant_especes' => $montantEspeces,
            'nombre_especes' => $nombreEspeces,

            'montant_banque' => $montantBanque,
            'montant_virement' => $montantBanque,
            'nombre_banque' => $nombreBanque,
            'nombre_virement' => $nombreBanque,

            'montant_mobile_money' => $montantMobileMoney,
            'nombre_mobile_money' => $nombreMobileMoney,

            'montant_cheques' => $montantCheques,
            'montant_cheque' => $montantCheques,
            'nombre_cheques' => $nombreCheques,
            'nombre_cheque' => $nombreCheques,
        ];

        /*
         * Modes disponibles.
         */
        $modesPaiement = PaiementSalaire::MODES_PAIEMENT;

        return view(
            'admin.paiements-salaires.statistiques',
            compact(
                'mois',
                'stats',
                'modesPaiement'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | IMPRESSION
    |--------------------------------------------------------------------------
    */

    /**
     * Prépare l'impression d'un reçu.
     *
     * GET /admin/paiements-salaires/{paiement}/imprimer
     */
    public function imprimer(
        PaiementSalaire $paiement
    ): View {
        $this->authorize(
            'view',
            $paiement
        );

        $paiement->load([
            'salaire.membre',
            'salaire.organe',
            'payePar',
        ]);

        $salaire = $paiement->salaire;

        return view(
            'admin.paiements-salaires.imprimer',
            compact(
                'paiement',
                'salaire'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RAPPORT
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le rapport des paiements.
     *
     * GET /admin/paiements-salaires/rapport
     *
     * Filtres supportés :
     *
     * - mois
     * - date_debut
     * - date_fin
     * - mode_paiement
     * - recherche
     */
    public function rapport(
        Request $request
    ): View {
        $this->authorize(
            'viewAny',
            PaiementSalaire::class
        );

        /*
         * ------------------------------------------------------------
         * 1. RÉCUPÉRATION DES FILTRES
         * ------------------------------------------------------------
         */

        $mois = $request->filled('mois')
            ? trim((string) $request->input('mois'))
            : null;

        $dateDebut = $request->filled('date_debut')
            ? trim((string) $request->input('date_debut'))
            : null;

        $dateFin = $request->filled('date_fin')
            ? trim((string) $request->input('date_fin'))
            : null;

        $modePaiement = $request->filled('mode_paiement')
            ? trim((string) $request->input('mode_paiement'))
            : null;

        $recherche = trim(
            (string) $request->input('recherche', '')
        );

        /*
         * ------------------------------------------------------------
         * 2. REQUÊTE DE BASE
         * ------------------------------------------------------------
         *
         * On prépare une seule requête.
         *
         * Cette requête sera utilisée :
         *
         * - pour récupérer les paiements ;
         * - pour calculer les statistiques.
         *
         * Cela garantit que les statistiques correspondent
         * exactement aux résultats affichés.
         */
        $query = PaiementSalaire::query()
            ->with([
                'salaire.membre',
                'salaire.organe',
                'payePar',
            ]);

        /*
         * ------------------------------------------------------------
         * 3. FILTRE PAR MOIS
         * ------------------------------------------------------------
         *
         * IMPORTANT :
         * Ne jamais exécuter :
         *
         *     ->duMois(null)
         *
         * car le scope duMois() attend obligatoirement
         * une chaîne de caractères.
         */
        if ($mois !== null && $mois !== '') {
            $query->duMois($mois);
        }

        /*
         * ------------------------------------------------------------
         * 4. FILTRE PAR DATE DE DÉBUT
         * ------------------------------------------------------------
         */
        if ($dateDebut !== null && $dateDebut !== '') {
            $query->whereDate(
                'date_paiement',
                '>=',
                $dateDebut
            );
        }

        /*
         * ------------------------------------------------------------
         * 5. FILTRE PAR DATE DE FIN
         * ------------------------------------------------------------
         */
        if ($dateFin !== null && $dateFin !== '') {
            $query->whereDate(
                'date_paiement',
                '<=',
                $dateFin
            );
        }

        /*
         * ------------------------------------------------------------
         * 6. FILTRE PAR MODE DE PAIEMENT
         * ------------------------------------------------------------
         */
        if (
            $modePaiement !== null
            && $modePaiement !== ''
        ) {
            $query->where(
                'mode_paiement',
                $modePaiement
            );
        }

        /*
         * ------------------------------------------------------------
         * 7. RECHERCHE
         * ------------------------------------------------------------
         *
         * Recherche sur :
         *
         * - référence du paiement ;
         * - référence du salaire ;
         * - nom du membre ;
         * - post-nom ;
         * - prénom ;
         * - matricule si la colonne existe.
         */
        $this->appliquerRecherche(
            $query,
            $recherche
        );

        /*
         * ------------------------------------------------------------
         * 8. TRI
         * ------------------------------------------------------------
         *
         * Les paiements les plus récents apparaissent
         * en premier dans le rapport.
         */
        $query
            ->orderByDesc('date_paiement')
            ->orderByDesc('id');

        /*
         * ------------------------------------------------------------
         * 9. RÉCUPÉRATION DES PAIEMENTS
         * ------------------------------------------------------------
         *
         * Le rapport reste volontairement sous forme de Collection,
         * car la vue actuelle affiche l'ensemble du rapport.
         */
        $paiements = $query->get();

        /*
         * ------------------------------------------------------------
         * 10. STATISTIQUES DU RAPPORT
         * ------------------------------------------------------------
         *
         * Les statistiques sont calculées sur les mêmes paiements
         * que ceux affichés dans le tableau.
         */
        $nombrePaiements = $paiements->count();

        $montantTotal = (float) $paiements->sum(
            'montant'
        );

        /*
         * Paiement moyen.
         */
        $montantMoyen = $nombrePaiements > 0
            ? round(
                $montantTotal / $nombrePaiements,
                2
            )
            : 0;

        /*
         * ------------------------------------------------------------
         * 11. STATISTIQUES PAR MODE
         * ------------------------------------------------------------
         */

        /*
         * Espèces.
         */
        $paiementsEspeces = $paiements->where(
            'mode_paiement',
            PaiementSalaire::MODE_ESPECES
        );

        $nombreEspeces = $paiementsEspeces->count();

        $montantEspeces = (float) $paiementsEspeces->sum(
            'montant'
        );

        /*
         * Virement.
         */
        $paiementsVirement = $paiements->where(
            'mode_paiement',
            PaiementSalaire::MODE_VIREMENT
        );

        $nombreVirement = $paiementsVirement->count();

        $montantVirement = (float) $paiementsVirement->sum(
            'montant'
        );

        /*
         * Mobile Money.
         */
        $paiementsMobileMoney = $paiements->where(
            'mode_paiement',
            PaiementSalaire::MODE_MOBILE_MONEY
        );

        $nombreMobileMoney = $paiementsMobileMoney->count();

        $montantMobileMoney = (float) $paiementsMobileMoney->sum(
            'montant'
        );

        /*
         * Chèque.
         */
        $paiementsCheque = $paiements->where(
            'mode_paiement',
            PaiementSalaire::MODE_CHEQUE
        );

        $nombreCheque = $paiementsCheque->count();

        $montantCheque = (float) $paiementsCheque->sum(
            'montant'
        );

        /*
         * ------------------------------------------------------------
         * 12. POURCENTAGES
         * ------------------------------------------------------------
         *
         * Les pourcentages sont calculés sur le montant total.
         */
        $pourcentageEspeces = $montantTotal > 0
            ? round(
                ($montantEspeces / $montantTotal) * 100,
                2
            )
            : 0;

        $pourcentageVirement = $montantTotal > 0
            ? round(
                ($montantVirement / $montantTotal) * 100,
                2
            )
            : 0;

        $pourcentageMobileMoney = $montantTotal > 0
            ? round(
                ($montantMobileMoney / $montantTotal) * 100,
                2
            )
            : 0;

        $pourcentageCheque = $montantTotal > 0
            ? round(
                ($montantCheque / $montantTotal) * 100,
                2
            )
            : 0;

        /*
         * ------------------------------------------------------------
         * 13. MODE PRINCIPAL
         * ------------------------------------------------------------
         *
         * Le mode principal est celui qui représente
         * le montant financier le plus élevé.
         */
        $modes = [
            'especes' => [
                'label' => 'Espèces',
                'montant' => $montantEspeces,
            ],

            'virement' => [
                'label' => 'Virement',
                'montant' => $montantVirement,
            ],

            'mobile_money' => [
                'label' => 'Mobile Money',
                'montant' => $montantMobileMoney,
            ],

            'cheque' => [
                'label' => 'Chèque',
                'montant' => $montantCheque,
            ],
        ];

        $modePrincipal = collect($modes)
            ->sortByDesc('montant')
            ->first();

        /*
         * Si aucun paiement n'existe, on garde
         * une valeur propre pour la vue.
         */
        $modePrincipalLabel = $modePrincipal['label']
            ?? 'Aucun';

        $modePrincipalMontant = (float) (
            $modePrincipal['montant']
            ?? 0
        );

        /*
         * ------------------------------------------------------------
         * 14. TABLEAU STATS
         * ------------------------------------------------------------
         *
         * Les noms couvrent les différentes attentes
         * de la vue rapport.
         */
        $stats = [
            /*
             * Général.
             */
            'nombre' => $nombrePaiements,
            'nombre_paiements' => $nombrePaiements,

            'montant_total' => $montantTotal,
            'total' => $montantTotal,

            'montant_moyen' => $montantMoyen,
            'paiement_moyen' => $montantMoyen,

            /*
             * Mode principal.
             */
            'mode_principal' => $modePrincipalLabel,
            'montant_mode_principal' => $modePrincipalMontant,

            /*
             * Espèces.
             */
            'nombre_especes' => $nombreEspeces,
            'montant_especes' => $montantEspeces,
            'pourcentage_especes' => $pourcentageEspeces,

            /*
             * Virement.
             */
            'nombre_virement' => $nombreVirement,
            'montant_virement' => $montantVirement,
            'pourcentage_virement' => $pourcentageVirement,

            /*
             * Alias "banque" pour compatibilité
             * avec d'éventuelles anciennes vues.
             */
            'nombre_banque' => $nombreVirement,
            'montant_banque' => $montantVirement,

            /*
             * Mobile Money.
             */
            'nombre_mobile_money' => $nombreMobileMoney,
            'montant_mobile_money' => $montantMobileMoney,
            'pourcentage_mobile_money' => $pourcentageMobileMoney,

            /*
             * Chèque.
             */
            'nombre_cheque' => $nombreCheque,
            'montant_cheque' => $montantCheque,
            'pourcentage_cheque' => $pourcentageCheque,

            /*
             * Alias pluriels.
             */
            'nombre_cheques' => $nombreCheque,
            'montant_cheques' => $montantCheque,
        ];

        /*
         * ------------------------------------------------------------
         * 15. MODES DISPONIBLES
         * ------------------------------------------------------------
         */
        $modesPaiement = PaiementSalaire::MODES_PAIEMENT;

        /*
         * ------------------------------------------------------------
         * 16. RETOUR DE LA VUE
         * ------------------------------------------------------------
         */
        return view(
            'admin.paiements-salaires.rapport',
            compact(
                'paiements',
                'mois',
                'dateDebut',
                'dateFin',
                'modePaiement',
                'recherche',
                'stats',
                'modesPaiement'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Les paiements enregistrés ne sont pas modifiables.
     *
     * PUT/PATCH /admin/paiements-salaires/{paiement}
     */
    public function update(
        Request $request,
        PaiementSalaire $paiement
    ): RedirectResponse {
        return redirect()
            ->back()
            ->with(
                'error',
                'Un paiement de salaire enregistré ne peut pas être modifié. Utilisez une procédure de correction ou de contrepassation.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    /**
     * Les paiements historiques ne sont pas supprimables.
     *
     * DELETE /admin/paiements-salaires/{paiement}
     */
    public function destroy(
        PaiementSalaire $paiement
    ): RedirectResponse {
        $this->authorize(
            'delete',
            $paiement
        );

        return redirect()
            ->back()
            ->with(
                'error',
                'Un paiement de salaire enregistré ne peut pas être supprimé.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PRIVÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Applique la recherche textuelle à une requête
     * de paiements.
     *
     * Recherche sur :
     *
     * - référence du paiement ;
     * - référence du salaire ;
     * - nom ;
     * - post-nom ;
     * - prénom ;
     * - matricule si disponible.
     */
    private function appliquerRecherche(
        Builder $query,
        string $recherche
    ): void {
        if ($recherche === '') {
            return;
        }

        $like = '%' . $recherche . '%';

        $query->where(function (Builder $q) use ($like) {

            /*
             * Référence du paiement.
             */
            $q->where(
                'reference',
                'like',
                $like
            );

            /*
             * Informations provenant du salaire
             * et du membre bénéficiaire.
             */
            $q->orWhereHas(
                'salaire',
                function (Builder $salaryQuery) use ($like) {

                    /*
                     * Référence du salaire.
                     */
                    $salaryQuery->where(
                        'reference',
                        'like',
                        $like
                    );

                    /*
                     * Recherche du bénéficiaire.
                     */
                    $salaryQuery->orWhereHas(
                        'membre',
                        function (Builder $memberQuery) use ($like) {

                            $memberQuery
                                ->where(
                                    'nom',
                                    'like',
                                    $like
                                )
                                ->orWhere(
                                    'post_nom',
                                    'like',
                                    $like
                                )
                                ->orWhere(
                                    'prenom',
                                    'like',
                                    $like
                                );
                        }
                    );
                }
            );
        });
    }

    /**
     * Calcule les statistiques d'une requête de paiements.
     *
     * La requête reçue ne doit pas être paginée.
     */
    private function calculerStatistiques(
        Builder $query
    ): array {
        /*
         * Nombre total d'opérations.
         */
        $nombre = (clone $query)
            ->count();

        /*
         * Montant total.
         */
        $montantTotal = (float) (
            (clone $query)
                ->sum('montant')
        );

        /*
         * Espèces.
         */
        $nombreEspeces = (clone $query)
            ->where(
                'mode_paiement',
                PaiementSalaire::MODE_ESPECES
            )
            ->count();

        $montantEspeces = (float) (
            (clone $query)
                ->where(
                    'mode_paiement',
                    PaiementSalaire::MODE_ESPECES
                )
                ->sum('montant')
        );

        /*
         * Virement.
         */
        $nombreVirement = (clone $query)
            ->where(
                'mode_paiement',
                PaiementSalaire::MODE_VIREMENT
            )
            ->count();

        $montantVirement = (float) (
            (clone $query)
                ->where(
                    'mode_paiement',
                    PaiementSalaire::MODE_VIREMENT
                )
                ->sum('montant')
        );

        /*
         * Mobile Money.
         */
        $nombreMobileMoney = (clone $query)
            ->where(
                'mode_paiement',
                PaiementSalaire::MODE_MOBILE_MONEY
            )
            ->count();

        $montantMobileMoney = (float) (
            (clone $query)
                ->where(
                    'mode_paiement',
                    PaiementSalaire::MODE_MOBILE_MONEY
                )
                ->sum('montant')
        );

        /*
         * Chèque.
         */
        $nombreCheque = (clone $query)
            ->where(
                'mode_paiement',
                PaiementSalaire::MODE_CHEQUE
            )
            ->count();

        $montantCheque = (float) (
            (clone $query)
                ->where(
                    'mode_paiement',
                    PaiementSalaire::MODE_CHEQUE
                )
                ->sum('montant')
        );

        /*
         * Montant moyen.
         */
        $montantMoyen = $nombre > 0
            ? round(
                $montantTotal / $nombre,
                2
            )
            : 0;

        return [
            /*
             * Général.
             */
            'nombre' => $nombre,
            'nombre_paiements' => $nombre,

            'montant_total' => $montantTotal,
            'total' => $montantTotal,

            'montant_moyen' => $montantMoyen,
            'paiement_moyen' => $montantMoyen,

            /*
             * Espèces.
             */
            'nombre_especes' => $nombreEspeces,
            'montant_especes' => $montantEspeces,

            /*
             * Virement.
             */
            'nombre_virement' => $nombreVirement,
            'montant_virement' => $montantVirement,

            /*
             * Compatibilité ancienne terminologie.
             */
            'nombre_banque' => $nombreVirement,
            'montant_banque' => $montantVirement,

            /*
             * Mobile Money.
             */
            'nombre_mobile_money' => $nombreMobileMoney,
            'montant_mobile_money' => $montantMobileMoney,

            /*
             * Chèques.
             */
            'nombre_cheque' => $nombreCheque,
            'montant_cheque' => $montantCheque,

            'nombre_cheques' => $nombreCheque,
            'montant_cheques' => $montantCheque,
        ];
    }
}