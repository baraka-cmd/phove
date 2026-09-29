<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnulerSalaireRequest;
use App\Http\Requests\StoreSalaireRequest;
use App\Http\Requests\UpdateSalaireRequest;
use App\Http\Requests\ValiderSalaireRequest;
use App\Models\Membre;
use App\Models\Organe;
use App\Models\Salaire;
use App\Services\SalaireService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaireController extends Controller
{
    /**
     * Service métier responsable de la gestion des salaires.
     */
    public function __construct(
        protected SalaireService $salaireService
    ) {
    }

    /**
     * Affiche la liste des salaires.
     *
     * GET /admin/salaires
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Salaire::class);

        /*
         * Filtres.
         *
         * Si aucun mois n'est fourni, on utilise le mois courant.
         * Cela évite de transmettre null aux méthodes du service
         * qui exigent un mois au format Y-m.
         */
        $mois = $request->input(
            'mois',
            now()->format('Y-m')
        );

        $statut = $request->input('statut');

        $recherche = $request->input('recherche');

        /*
         * Construction de la requête via le service métier.
         */
        $query = $this->salaireService->requete(
            $mois,
            $statut,
            $recherche
        );

        /*
         * Liste paginée.
         *
         * Les paiements sont agrégés avec withSum()
         * afin d'éviter de charger inutilement toute
         * la collection des paiements.
         */
        $salaires = $query
            ->with([
                'membre',
                'organe',
            ])
            ->withSum('paiements', 'montant')
            ->orderByDesc('mois')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        /*
         * Statistiques du mois sélectionné.
         */
        $stats = [
            'total' => $this->salaireService->nombreDuMois(
                $mois
            ),

            'montant_total' => $this->salaireService->totalDuMois(
                $mois
            ),

            'montant_valide' => $this->salaireService->totalValideDuMois(
                $mois
            ),

            'reste_a_payer' => $this->calculerResteGlobal(
                $mois
            ),
        ];

        /*
         * Statuts disponibles dans le filtre.
         */
        $statuts = [
            Salaire::STATUT_BROUILLON => 'Brouillon',
            Salaire::STATUT_VALIDE => 'Validé',
            Salaire::STATUT_PARTIELLEMENT_PAYE => 'Partiellement payé',
            Salaire::STATUT_PAYE => 'Payé',
            Salaire::STATUT_ANNULE => 'Annulé',
        ];

        return view(
            'admin.salaires.index',
            compact(
                'salaires',
                'stats',
                'statuts',
                'mois',
                'statut',
                'recherche'
            )
        );
    }

    /**
     * Affiche la grille salariale.
     *
     * GET /admin/salaires/grille
     */
    public function grille(Request $request): View
    {
        $this->authorize('viewAny', Salaire::class);

        /*
         * Mois affiché dans la grille.
         *
         * Par défaut : mois courant.
         */
        $mois = $request->input(
            'mois',
            now()->format('Y-m')
        );

        /*
         * Organes et membres.
         *
         * IMPORTANT :
         * La table "organes" de ta base ne possède pas
         * de colonne "nom".
         *
         * On utilise donc "id" pour l'ordre afin de
         * ne pas dépendre d'un nom de colonne inexistant.
         */
        $organes = Organe::query()
            ->orderBy('id')
            ->get();
        /*
         * Salaires du mois.
         *
         * Les paiements sont agrégés afin de connaître
         * rapidement le montant déjà payé pour chaque salaire.
         */
        $salaires = Salaire::query()
            ->with([
                'membre',
                'organe',
            ])
            ->withSum('paiements', 'montant')
            ->where('mois', $mois)
            ->get()
            ->keyBy('membre_id');

        /*
         * Total des salaires nets du mois.
         */
        $total = $salaires->sum(
            fn (Salaire $salaire): float =>
                (float) $salaire->salaire_net
        );

        /*
         * Total effectivement payé.
         */
        $totalPaye = $salaires->sum(
            fn (Salaire $salaire): float =>
                (float) (
                    $salaire->paiements_sum_montant ?? 0
                )
        );

        /*
         * Reste global à payer.
         */
        $reste = max(
            0,
            $total - $totalPaye
        );

        return view(
            'admin.salaires.grille',
            compact(
                'organes',
                'salaires',
                'mois',
                'total',
                'totalPaye',
                'reste'
            )
        );
    }

    /**
     * Affiche le formulaire de création.
     *
     * GET /admin/salaires/create
     */
    public function create(): View
{
    $this->authorize('create', Salaire::class);

    /*
     * Membres actifs uniquement.
     */
    $membres = Membre::query()
        ->where(function ($query) {
            $query
                ->where('statut', 'actif')
                ->orWhere('statut', 'active');
        })
        ->orderBy('nom')
        ->orderBy('post_nom')
        ->get();

    /*
     * Organes disponibles.
     */
    $organes = Organe::query()
        ->orderBy('id')
        ->get();

    /*
     * Mois proposé par défaut.
     */
    $moisCourant = now()->format('Y-m');

    /*
     * Référence générée automatiquement.
     */
    $referenceAuto = 'SAL-' . now()->format('Ym') . '-' . str_pad(
        (Salaire::where('mois', $moisCourant)->count() + 1),
        4,
        '0',
        STR_PAD_LEFT
    );

    return view(
        'admin.salaires.create',
        compact(
            'membres',
            'organes',
            'moisCourant',
            'referenceAuto'
        )
    );
}

    /**
     * Enregistre un nouveau salaire.
     *
     * POST /admin/salaires
     */
    public function store(
        StoreSalaireRequest $request
    ): RedirectResponse {
        /*
         * La validation métier est assurée par le FormRequest.
         * La création réelle est déléguée au service.
         */
        $salaire = $this->salaireService->creer(
            $request->validatedData(),
            auth()->id()
        );

        return redirect()
            ->route(
                'admin.salaires.show',
                $salaire
            )
            ->with(
                'success',
                'Le salaire a été créé avec succès en brouillon.'
            );
    }

    /**
     * Affiche les détails d'un salaire.
     *
     * GET /admin/salaires/{salaire}
     */
    public function show(
        Salaire $salaire
    ): View {
        $this->authorize(
            'view',
            $salaire
        );

        /*
         * Chargement des relations nécessaires.
         */
        $salaire->load([
            'membre',
            'organe',
            'createur',
            'validePar',
            'annulePar',
            'paiements' => function ($query) {
                $query
                    ->with('payePar')
                    ->orderByDesc('date_paiement')
                    ->orderByDesc('id');
            },
        ]);

        /*
         * Historique des paiements.
         */
        $paiements = $salaire->paiements;

        /*
         * Informations financières calculées par le modèle.
         */
        $totalPaye = $salaire->totalPaye;

        $resteAPayer = $salaire->resteAPayer();

        $pourcentagePaye = $salaire->pourcentagePaye;

        return view(
            'admin.salaires.show',
            compact(
                'salaire',
                'paiements',
                'totalPaye',
                'resteAPayer',
                'pourcentagePaye'
            )
        );
    }

    /**
     * Affiche le formulaire de modification.
     *
     * GET /admin/salaires/{salaire}/edit
     */
    public function edit(
        Salaire $salaire
    ): View {
        $this->authorize(
            'update',
            $salaire
        );

        /*
         * Membres actifs uniquement.
         */
        $membres = Membre::query()
            ->where(function ($query) {
                $query
                    ->where('statut', 'actif')
                    ->orWhere('statut', 'active');
            })
            ->orderBy('nom')
            ->orderBy('post_nom')
            ->get();

        /*
         * Organes disponibles.
         */
        $organes = Organe::query()
            ->orderBy('id')
            ->get();

        return view(
            'admin.salaires.edit',
            compact(
                'salaire',
                'membres',
                'organes'
            )
        );
    }

    /**
     * Met à jour un salaire.
     *
     * PUT/PATCH /admin/salaires/{salaire}
     */
    public function update(
        UpdateSalaireRequest $request,
        Salaire $salaire
    ): RedirectResponse {
        /*
         * Autorisation.
         *
         * Le service vérifiera également les règles métier,
         * notamment l'état du salaire.
         */
        $this->authorize(
            'update',
            $salaire
        );

        $this->salaireService->modifier(
            $salaire,
            $request->validatedData()
        );

        return redirect()
            ->route(
                'admin.salaires.show',
                $salaire
            )
            ->with(
                'success',
                'Le salaire a été modifié avec succès.'
            );
    }

    /**
     * Valide un salaire.
     *
     * PUT /admin/salaires/{salaire}/valider
     */
    public function valider(
        ValiderSalaireRequest $request,
        Salaire $salaire
    ): RedirectResponse {
        /*
         * Défense en profondeur :
         *
         * 1. Policy
         * 2. FormRequest
         * 3. Service métier
         */
        $this->authorize(
            'validate',
            $salaire
        );

        $this->salaireService->valider(
            $salaire,
            auth()->id()
        );

        return redirect()
            ->route(
                'admin.salaires.show',
                $salaire
            )
            ->with(
                'success',
                'Le salaire a été validé. Il est maintenant disponible pour paiement.'
            );
    }

    /**
     * Annule un salaire.
     *
     * PUT /admin/salaires/{salaire}/annuler
     */
    public function annuler(
        AnnulerSalaireRequest $request,
        Salaire $salaire
    ): RedirectResponse {
        $this->authorize(
            'cancel',
            $salaire
        );

        $data = $request->validatedData();

        $this->salaireService->annuler(
            $salaire,
            auth()->id(),
            $data['motif_annulation']
        );

        return redirect()
            ->route(
                'admin.salaires.show',
                $salaire
            )
            ->with(
                'success',
                'Le salaire a été annulé avec succès.'
            );
    }

    /**
     * Supprime un salaire en brouillon.
     *
     * DELETE /admin/salaires/{salaire}
     */
    public function destroy(
        Salaire $salaire
    ): RedirectResponse {
        $this->authorize(
            'delete',
            $salaire
        );

        /*
         * Sécurité supplémentaire :
         * un salaire ayant un historique financier
         * ne doit jamais être supprimé.
         */
        if ($salaire->paiements()->exists()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Ce salaire possède un historique de paiement et ne peut pas être supprimé.'
                );
        }

        /*
         * Seul un brouillon peut être supprimé.
         */
        if (!$salaire->estBrouillon()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Seul un salaire en brouillon peut être supprimé.'
                );
        }

        $salaire->delete();

        return redirect()
            ->route(
                'admin.salaires.index'
            )
            ->with(
                'success',
                'Le brouillon de salaire a été supprimé.'
            );
    }

    /**
     * Génère le bulletin de salaire.
     *
     * GET /admin/salaires/{salaire}/bulletin
     */
    public function bulletin(
        Salaire $salaire
    ) {
        $this->authorize(
            'view',
            $salaire
        );

        /*
         * Relations nécessaires au bulletin.
         */
        $salaire->load([
            'membre',
            'organe',
            'createur',
            'validePar',
            'annulePar',
            'paiements.payePar',
        ]);

        /*
         * La génération PDF sera branchée ultérieurement.
         */
        abort(
            501,
            'La génération PDF du bulletin sera activée ultérieurement.'
        );
    }

    /**
     * Calcule le reste global à payer pour un mois.
     */
    private function calculerResteGlobal(
        ?string $mois
    ): float {
        /*
         * Protection supplémentaire.
         *
         * Normalement index() transmet toujours un mois.
         * Mais cette valeur par défaut rend la méthode
         * robuste si elle est réutilisée ailleurs.
         */
        $mois ??= now()->format('Y-m');

        return $this->salaireService
            ->salairesAvecReste($mois)
            ->sum(
                fn (Salaire $salaire): float =>
                    (float) $salaire->resteAPayer()
            );
    }
}
