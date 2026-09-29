@extends('layouts.admin')

@section('title', 'Rapport des paiements de salaires')

@push('styles') <link
     rel="stylesheet"
     href="{{ asset('assets/css/admin/paiements-salaires/rapport.css') }}"
 >
@endpush

@section('content')

@php
/*
|--------------------------------------------------------------------------
| FILTRES
|--------------------------------------------------------------------------
*/


$mois = request('mois');
$dateDebut = request('date_debut');
$dateFin = request('date_fin');
$modeFiltre = request('mode_paiement');
$recherche = request('recherche');

/*
|--------------------------------------------------------------------------
| COLLECTION
|--------------------------------------------------------------------------
*/

$listePaiements = $paiements ?? collect();

/*
|--------------------------------------------------------------------------
| STATISTIQUES
|--------------------------------------------------------------------------
*/

$nombrePaiements = (int) (
    data_get($stats ?? [], 'nombre_paiements')
    ?? data_get($stats ?? [], 'nombre')
    ?? (method_exists($listePaiements, 'total')
        ? $listePaiements->total()
        : $listePaiements->count())
);

$montantTotal = (float) (
    data_get($stats ?? [], 'montant_total')
    ?? data_get($stats ?? [], 'total')
    ?? 0
);

$montantEspeces = (float) (
    data_get($stats ?? [], 'montant_especes')
    ?? data_get($stats ?? [], 'especes')
    ?? 0
);

$montantVirement = (float) (
    data_get($stats ?? [], 'montant_virement')
    ?? data_get($stats ?? [], 'virement')
    ?? 0
);

$montantMobileMoney = (float) (
    data_get($stats ?? [], 'montant_mobile_money')
    ?? data_get($stats ?? [], 'mobile_money')
    ?? 0
);

$montantCheque = (float) (
    data_get($stats ?? [], 'montant_cheque')
    ?? data_get($stats ?? [], 'cheque')
    ?? 0
);

/*
|--------------------------------------------------------------------------
| NOMBRE PAR MODE
|--------------------------------------------------------------------------
*/

$nombreEspeces = (int) (
    data_get($stats ?? [], 'nombre_especes')
    ?? 0
);

$nombreVirement = (int) (
    data_get($stats ?? [], 'nombre_virement')
    ?? 0
);

$nombreMobileMoney = (int) (
    data_get($stats ?? [], 'nombre_mobile_money')
    ?? 0
);

$nombreCheque = (int) (
    data_get($stats ?? [], 'nombre_cheque')
    ?? 0
);

/*
|--------------------------------------------------------------------------
| POURCENTAGES
|--------------------------------------------------------------------------
*/

$pourcentageEspeces = $montantTotal > 0
    ? min(100, max(0, ($montantEspeces / $montantTotal) * 100))
    : 0;

$pourcentageVirement = $montantTotal > 0
    ? min(100, max(0, ($montantVirement / $montantTotal) * 100))
    : 0;

$pourcentageMobileMoney = $montantTotal > 0
    ? min(100, max(0, ($montantMobileMoney / $montantTotal) * 100))
    : 0;

$pourcentageCheque = $montantTotal > 0
    ? min(100, max(0, ($montantCheque / $montantTotal) * 100))
    : 0;

/*
|--------------------------------------------------------------------------
| MODES
|--------------------------------------------------------------------------
*/

$modes = [
    'especes' => [
        'label' => 'Espèces',
        'icon' => 'fa-money-bill-wave',
        'montant' => $montantEspeces,
        'nombre' => $nombreEspeces,
        'pourcentage' => $pourcentageEspeces,
    ],

    'virement' => [
        'label' => 'Virement bancaire',
        'icon' => 'fa-building-columns',
        'montant' => $montantVirement,
        'nombre' => $nombreVirement,
        'pourcentage' => $pourcentageVirement,
    ],

    'mobile_money' => [
        'label' => 'Mobile Money',
        'icon' => 'fa-mobile-screen-button',
        'montant' => $montantMobileMoney,
        'nombre' => $nombreMobileMoney,
        'pourcentage' => $pourcentageMobileMoney,
    ],

    'cheque' => [
        'label' => 'Chèque',
        'icon' => 'fa-money-check',
        'montant' => $montantCheque,
        'nombre' => $nombreCheque,
        'pourcentage' => $pourcentageCheque,
    ],
];

/*
|--------------------------------------------------------------------------
| PÉRIODE
|--------------------------------------------------------------------------
*/

$periodeLibelle = 'Toutes les périodes';

if ($mois) {
    try {
        $periodeLibelle = \Carbon\Carbon::createFromFormat('Y-m', $mois)
            ->translatedFormat('F Y');
    } catch (\Throwable $e) {
        $periodeLibelle = $mois;
    }
} elseif ($dateDebut || $dateFin) {

    $debut = $dateDebut ?: 'Début';
    $fin = $dateFin ?: "Aujourd’hui";

    if ($dateDebut) {
        try {
            $debut = \Carbon\Carbon::parse($dateDebut)
                ->format('d/m/Y');
        } catch (\Throwable $e) {
            $debut = $dateDebut;
        }
    }

    if ($dateFin) {
        try {
            $fin = \Carbon\Carbon::parse($dateFin)
                ->format('d/m/Y');
        } catch (\Throwable $e) {
            $fin = $dateFin;
        }
    }

    $periodeLibelle = $debut . ' → ' . $fin;
}

/*
|--------------------------------------------------------------------------
| DATE DE GÉNÉRATION
|--------------------------------------------------------------------------
*/

try {
    $dateGeneration = now()->translatedFormat('d F Y à H:i');
} catch (\Throwable $e) {
    $dateGeneration = now()->format('d/m/Y H:i');
}

/*
|--------------------------------------------------------------------------
| MODE PRINCIPAL
|--------------------------------------------------------------------------
*/

$modePrincipal = collect($modes)
    ->sortByDesc('montant')
    ->first();

/*
|--------------------------------------------------------------------------
| NUMÉROTATION
|--------------------------------------------------------------------------
*/

$ligneDepart = method_exists($listePaiements, 'firstItem')
    ? ($listePaiements->firstItem() ?? 1)
    : 1;


@endphp

<div class="salary-payment-report payment-report">


{{-- =========================================================
     EN-TÊTE DE PAGE
     ========================================================= --}}

<header class="report-page-header">

    <div class="report-title-area">

        <h1 class="report-title">
            <i class="fas fa-file-invoice-dollar"></i>
            Rapport des paiements
        </h1>

        <p class="report-subtitle">
            Analyse et suivi des paiements de salaires
        </p>

    </div>

    <div class="report-actions">

        <a
            href="{{ route('admin.paiements-salaires.index') }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left"></i>
            <span>Paiements</span>
        </a>

        <button
            type="button"
            class="btn btn-primary"
            data-print
        >
            <i class="fas fa-print"></i>
            <span>Imprimer</span>
        </button>

    </div>

</header>


{{-- =========================================================
     ALERTES
     ========================================================= --}}

@if(session('success'))

    <div class="alert alert-success" role="alert">

        <i class="fas fa-circle-check"></i>

        <div>
            <strong>Opération réussie</strong>

            <div>
                {{ session('success') }}
            </div>
        </div>

    </div>

@endif


@if(session('error'))

    <div class="alert alert-danger" role="alert">

        <i class="fas fa-circle-exclamation"></i>

        <div>
            <strong>Opération impossible</strong>

            <div>
                {{ session('error') }}
            </div>
        </div>

    </div>

@endif


@if(session('warning'))

    <div class="alert alert-warning" role="alert">

        <i class="fas fa-triangle-exclamation"></i>

        <div>
            {{ session('warning') }}
        </div>

    </div>

@endif


{{-- =========================================================
     EN-TÊTE OFFICIEL DU DOCUMENT
     ========================================================= --}}

<section class="report-document-header">

    <div class="organization-block">

        <div class="organization-logo">

            @if(isset($settings) && !empty($settings->logo))

                <img
                    src="{{ asset('storage/' . $settings->logo) }}"
                    alt="{{ $settings->nom ?? 'PHOVE' }}"
                >

            @else

                <span>
                    PH
                </span>

            @endif

        </div>

        <div>

            <div class="organization-name">
                {{ $settings->nom ?? 'PHOVE' }}
            </div>

            <div class="organization-meta">
                Gestion financière — Paiements des salaires
            </div>

        </div>

    </div>


    <div class="report-document-info">

        <span class="report-document-label">
            Document
        </span>

        <strong class="report-document-name">
            RAPPORT DES PAIEMENTS
        </strong>

        <span class="report-generated-date">
            Généré le {{ $dateGeneration }}
        </span>

    </div>

</section>


{{-- =========================================================
     FILTRES
     ========================================================= --}}

<section class="report-card filters-card">

    <div class="report-card-header">

        <h2 class="report-card-title">
            <i class="fas fa-filter"></i>
            Filtres du rapport
        </h2>

    </div>


    <div class="report-card-body">

        <form
            method="GET"
            action="{{ route('admin.paiements-salaires.rapport') }}"
            data-filter-form
            data-reset-url="{{ route('admin.paiements-salaires.rapport') }}"
        >

            <div class="filters-grid">

                {{-- MOIS --}}

                <div
                    class="filter-field"
                    data-field
                >

                    <label
                        for="mois"
                        class="filter-label"
                    >
                        Mois
                    </label>

                    <input
                        type="month"
                        id="mois"
                        name="mois"
                        class="filter-control"
                        value="{{ $mois }}"
                        data-month-filter
                    >

                </div>


                {{-- DATE DÉBUT --}}

                <div
                    class="filter-field"
                    data-field
                >

                    <label
                        for="date_debut"
                        class="filter-label"
                    >
                        Date de début
                    </label>

                    <input
                        type="date"
                        id="date_debut"
                        name="date_debut"
                        class="filter-control"
                        value="{{ $dateDebut }}"
                        data-date-from
                    >

                </div>


                {{-- DATE FIN --}}

                <div
                    class="filter-field"
                    data-field
                >

                    <label
                        for="date_fin"
                        class="filter-label"
                    >
                        Date de fin
                    </label>

                    <input
                        type="date"
                        id="date_fin"
                        name="date_fin"
                        class="filter-control"
                        value="{{ $dateFin }}"
                        data-date-to
                    >

                </div>


                {{-- MODE --}}

                <div class="filter-field">

                    <label
                        for="mode_paiement"
                        class="filter-label"
                    >
                        Mode de paiement
                    </label>

                    <select
                        id="mode_paiement"
                        name="mode_paiement"
                        class="filter-control"
                        data-mode-filter
                        data-auto-submit
                    >

                        <option value="">
                            Tous les modes
                        </option>

                        <option
                            value="especes"
                            @selected($modeFiltre === 'especes')
                        >
                            Espèces
                        </option>

                        <option
                            value="virement"
                            @selected($modeFiltre === 'virement')
                        >
                            Virement bancaire
                        </option>

                        <option
                            value="mobile_money"
                            @selected($modeFiltre === 'mobile_money')
                        >
                            Mobile Money
                        </option>

                        <option
                            value="cheque"
                            @selected($modeFiltre === 'cheque')
                        >
                            Chèque
                        </option>

                    </select>

                </div>


                {{-- RECHERCHE --}}

                <div
                    class="filter-field search-field"
                    data-field
                >

                    <label
                        for="recherche"
                        class="filter-label"
                    >
                        Recherche
                    </label>

                    <input
                        type="search"
                        id="recherche"
                        name="recherche"
                        class="filter-control"
                        value="{{ $recherche }}"
                        placeholder="Référence, bénéficiaire, matricule..."
                        autocomplete="off"
                        data-search-input
                    >

                </div>


                {{-- ACTIONS --}}

                <div class="filter-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-submit-filter
                    >
                        <i class="fas fa-magnifying-glass"></i>
                        Appliquer
                    </button>

                    <button
                        type="button"
                        class="btn btn-outline"
                        data-reset-filters
                    >
                        <i class="fas fa-rotate-left"></i>
                        Réinitialiser
                    </button>

                </div>

            </div>

        </form>

    </div>

</section>


{{-- =========================================================
     FILTRES ACTIFS
     ========================================================= --}}

@if($mois || $dateDebut || $dateFin || $modeFiltre || $recherche)

    <div class="active-filters">

        <span class="active-filters-label">
            <i class="fas fa-filter"></i>
            Filtres actifs :
        </span>


        @if($mois)

            <span class="filter-chip">

                <strong>
                    Mois :
                </strong>

                {{ $periodeLibelle }}

                <a
                    href="{{ route(
                        'admin.paiements-salaires.rapport',
                        array_merge(
                            request()->except('mois', 'page'),
                            ['page' => null]
                        )
                    ) }}"
                    aria-label="Supprimer le filtre mois"
                >
                    <i class="fas fa-xmark"></i>
                </a>

            </span>

        @endif


        @if($dateDebut)

            <span class="filter-chip">

                <strong>
                    Dès :
                </strong>

                {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}

                <a
                    href="{{ route(
                        'admin.paiements-salaires.rapport',
                        array_merge(
                            request()->except('date_debut', 'page'),
                            ['page' => null]
                        )
                    ) }}"
                    aria-label="Supprimer la date de début"
                >
                    <i class="fas fa-xmark"></i>
                </a>

            </span>

        @endif


        @if($dateFin)

            <span class="filter-chip">

                <strong>
                    Jusqu'au :
                </strong>

                {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}

                <a
                    href="{{ route(
                        'admin.paiements-salaires.rapport',
                        array_merge(
                            request()->except('date_fin', 'page'),
                            ['page' => null]
                        )
                    ) }}"
                    aria-label="Supprimer la date de fin"
                >
                    <i class="fas fa-xmark"></i>
                </a>

            </span>

        @endif


        @if($modeFiltre)

            <span class="filter-chip">

                <strong>
                    Mode :
                </strong>

                {{ $modes[$modeFiltre]['label'] ?? $modeFiltre }}

                <a
                    href="{{ route(
                        'admin.paiements-salaires.rapport',
                        array_merge(
                            request()->except('mode_paiement', 'page'),
                            ['page' => null]
                        )
                    ) }}"
                    aria-label="Supprimer le filtre mode"
                >
                    <i class="fas fa-xmark"></i>
                </a>

            </span>

        @endif


        @if($recherche)

            <span class="filter-chip">

                <strong>
                    Recherche :
                </strong>

                {{ $recherche }}

                <a
                    href="{{ route(
                        'admin.paiements-salaires.rapport',
                        array_merge(
                            request()->except('recherche', 'page'),
                            ['page' => null]
                        )
                    ) }}"
                    aria-label="Supprimer la recherche"
                >
                    <i class="fas fa-xmark"></i>
                </a>

            </span>

        @endif

    </div>

@endif


{{-- =========================================================
     STATISTIQUES
     ========================================================= --}}

<section class="report-stats">

    {{-- TOTAL --}}

    <article class="stat-card">

        <div class="stat-card-header">

            <span class="stat-label">
                Total payé
            </span>

            <span class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </span>

        </div>

        <div class="stat-value tabular">
            {{ number_format($montantTotal, 2, ',', ' ') }}
            <small>USD</small>
        </div>

        <div class="stat-description">
            Montant total des paiements
        </div>

    </article>


    {{-- OPÉRATIONS --}}

    <article class="stat-card success">

        <div class="stat-card-header">

            <span class="stat-label">
                Opérations
            </span>

            <span class="stat-icon">
                <i class="fas fa-receipt"></i>
            </span>

        </div>

        <div class="stat-value tabular">
            {{ number_format($nombrePaiements, 0, ',', ' ') }}
        </div>

        <div class="stat-description">
            Paiements enregistrés
        </div>

    </article>


    {{-- MOYENNE --}}

    <article class="stat-card warning">

        <div class="stat-card-header">

            <span class="stat-label">
                Paiement moyen
            </span>

            <span class="stat-icon">
                <i class="fas fa-calculator"></i>
            </span>

        </div>

        <div class="stat-value tabular">

            {{
                number_format(
                    $nombrePaiements > 0
                        ? $montantTotal / $nombrePaiements
                        : 0,
                    2,
                    ',',
                    ' '
                )
            }}

            <small>USD</small>

        </div>

        <div class="stat-description">
            Moyenne par opération
        </div>

    </article>


    {{-- MODE PRINCIPAL --}}

    <article class="stat-card danger">

        <div class="stat-card-header">

            <span class="stat-label">
                Mode principal
            </span>

            <span class="stat-icon">
                <i class="fas fa-chart-pie"></i>
            </span>

        </div>

        <div class="stat-value">
            {{ $modePrincipal['label'] ?? '—' }}
        </div>

        <div class="stat-description">

            {{ number_format(
                $modePrincipal['montant'] ?? 0,
                2,
                ',',
                ' '
            ) }}
            USD

        </div>

    </article>

</section>


{{-- =========================================================
     RÉPARTITION DES PAIEMENTS
     ========================================================= --}}

<section class="report-card">

    <div class="report-card-header">

        <h2 class="report-card-title">
            <i class="fas fa-chart-column"></i>
            Répartition des paiements
        </h2>

    </div>


    <div class="report-card-body">

        <div class="payment-distribution">

            @foreach($modes as $key => $mode)

                @php
                    $distributionClass = $key === 'mobile_money'
                        ? 'mobile-money'
                        : $key;
                @endphp

                <article class="distribution-card">

                    <div class="distribution-header">

                        <div class="distribution-name">

                            <i class="fas {{ $mode['icon'] }}"></i>

                            {{ $mode['label'] }}

                        </div>

                        <strong class="distribution-value tabular">

                            {{ number_format(
                                $mode['montant'],
                                2,
                                ',',
                                ' '
                            ) }}

                            USD

                        </strong>

                    </div>


                    <div class="distribution-track">

                        <div
                            class="distribution-bar"
                            data-progress="{{ $mode['pourcentage'] }}"
                            aria-label="Part {{ $mode['label'] }}"
                        ></div>

                    </div>


                    <div class="distribution-footer">

                        <span class="distribution-count">

                            {{ number_format(
                                $mode['nombre'],
                                0,
                                ',',
                                ' '
                            ) }}

                            opération{{ $mode['nombre'] > 1 ? 's' : '' }}

                        </span>

                        <strong class="distribution-percent">

                            {{ number_format(
                                $mode['pourcentage'],
                                1,
                                ',',
                                ' '
                            ) }}
                            %

                        </strong>

                    </div>

                </article>

            @endforeach

        </div>

    </div>

</section>


{{-- =========================================================
     TABLEAU
     ========================================================= --}}

<section class="report-card">

    <div class="report-card-header">

        <h2 class="report-card-title">
            <i class="fas fa-table-list"></i>
            Opérations de paiement
        </h2>

        <span class="traceability-badge">
            <i class="fas fa-shield-halved"></i>
            Traçabilité
        </span>

    </div>


    <div class="report-card-body">

        @if($listePaiements->count())

            <div class="table-wrapper">

                <table
                    class="report-table"
                    data-payment-table
                >

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Référence
                            </th>

                            <th>
                                Bénéficiaire
                            </th>

                            <th>
                                Salaire
                            </th>

                            <th>
                                Mode
                            </th>

                            <th class="text-right">
                                Montant
                            </th>

                            <th>
                                Enregistré par
                            </th>

                            <th class="text-center">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($listePaiements as $index => $paiement)

                            @php

                                /*
                                |--------------------------------------------------
                                | BÉNÉFICIAIRE
                                |--------------------------------------------------
                                */

                                $membre = $paiement->salaire?->membre;

                                $nomBeneficiaire =
                                    $membre?->nomComplet
                                    ?? $membre?->nom
                                    ?? trim(
                                        ($membre?->prenom ?? '') . ' ' .
                                        ($membre?->post_nom ?? '')
                                    )
                                    ?? 'Membre';

                                $nomBeneficiaire =
                                    trim($nomBeneficiaire) ?: 'Membre';

                                $initiale = mb_strtoupper(
                                    mb_substr(
                                        $nomBeneficiaire,
                                        0,
                                        1
                                    )
                                );

                                /*
                                |--------------------------------------------------
                                | MODE
                                |--------------------------------------------------
                                */

                                $mode = $paiement->mode_paiement ?? '';

                                $modeLabels = [
                                    'especes' => 'Espèces',
                                    'virement' => 'Virement',
                                    'mobile_money' => 'Mobile Money',
                                    'cheque' => 'Chèque',
                                ];

                                $modeIcons = [
                                    'especes' => 'fa-money-bill-wave',
                                    'virement' => 'fa-building-columns',
                                    'mobile_money' => 'fa-mobile-screen-button',
                                    'cheque' => 'fa-money-check',
                                ];

                                $modeLabel =
                                    $modeLabels[$mode]
                                    ?? ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $mode
                                        )
                                    );

                                $modeIcon =
                                    $modeIcons[$mode]
                                    ?? 'fa-wallet';

                                $modeClass =
                                    in_array(
                                        $mode,
                                        [
                                            'especes',
                                            'virement',
                                            'mobile_money',
                                            'cheque',
                                        ],
                                        true
                                    )
                                        ? $mode
                                        : 'especes';

                                /*
                                |--------------------------------------------------
                                | DATE
                                |--------------------------------------------------
                                */

                                try {
                                    $datePaiement =
                                        \Carbon\Carbon::parse(
                                            $paiement->date_paiement
                                        );
                                } catch (\Throwable $e) {
                                    $datePaiement = null;
                                }

                                /*
                                |--------------------------------------------------
                                | SALAIRE
                                |--------------------------------------------------
                                */

                                $salaire = $paiement->salaire;

                                $moisSalaire = null;

                                if ($salaire?->mois) {
                                    try {
                                        $moisSalaire =
                                            \Carbon\Carbon::createFromFormat(
                                                'Y-m',
                                                $salaire->mois
                                            )->translatedFormat('F Y');
                                    } catch (\Throwable $e) {
                                        $moisSalaire =
                                            $salaire->mois;
                                    }
                                }

                                /*
                                |--------------------------------------------------
                                | STATUT DU SALAIRE
                                |--------------------------------------------------
                                */

                                $statutBrut =
                                    $salaire?->statut
                                    ?? $salaire?->status
                                    ?? null;

                                $statutNormalise = strtolower(
                                    str_replace(
                                        [
                                            ' ',
                                            '_',
                                            '-',
                                        ],
                                        '',
                                        (string) $statutBrut
                                    )
                                );

                                if (
                                    in_array(
                                        $statutNormalise,
                                        [
                                            'annule',
                                            'annulé',
                                            'cancelled',
                                        ],
                                        true
                                    )
                                ) {
                                    $statutClasse = 'annule';
                                    $statutLabel = 'Annulé';

                                } elseif (
                                    in_array(
                                        $statutNormalise,
                                        [
                                            'partiellementpaye',
                                            'partiellementpayé',
                                            'partial',
                                        ],
                                        true
                                    )
                                ) {
                                    $statutClasse =
                                        'partiellement-paye';

                                    $statutLabel =
                                        'Partiellement payé';

                                } elseif (
                                    in_array(
                                        $statutNormalise,
                                        [
                                            'paye',
                                            'payé',
                                            'paid',
                                            'valide',
                                            'validé',
                                        ],
                                        true
                                    )
                                ) {
                                    $statutClasse = 'paye';
                                    $statutLabel = 'Payé';

                                } else {
                                    $statutClasse = 'valide';
                                    $statutLabel = 'Validé';
                                }

                                /*
                                |--------------------------------------------------
                                | PAYEUR
                                |--------------------------------------------------
                                */

                                $payeur =
                                    $paiement->payePar?->name
                                    ?? $paiement->payePar?->nom
                                    ?? 'Utilisateur';

                                /*
                                |--------------------------------------------------
                                | NUMÉRO
                                |--------------------------------------------------
                                */

                                $numeroLigne =
                                    $ligneDepart + $index;

                            @endphp


                            <tr data-payment-row>

                                {{-- NUMÉRO --}}

                                <td class="tabular">
                                    {{ $numeroLigne }}
                                </td>


                                {{-- DATE --}}

                                <td>

                                    <div class="date-cell">

                                        @if($datePaiement)

                                            <span class="date-main">
                                                {{ $datePaiement->format('d/m/Y') }}
                                            </span>

                                            <span class="date-time">
                                                {{ $datePaiement->format('H:i') }}
                                            </span>

                                        @else

                                            <span class="date-main">
                                                —
                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- RÉFÉRENCE --}}

                                <td>

                                    <button
                                        type="button"
                                        class="payment-reference font-mono"
                                        data-copy
                                        data-copy-value="{{ $paiement->reference }}"
                                        title="Copier la référence"
                                    >
                                        {{ $paiement->reference ?? '—' }}
                                    </button>

                                </td>


                                {{-- BÉNÉFICIAIRE --}}

                                <td>

                                    <div class="employee-cell">

                                        <div class="employee-avatar">
                                            {{ $initiale }}
                                        </div>

                                        <div class="employee-info">

                                            <strong class="employee-name">
                                                {{ $nomBeneficiaire }}
                                            </strong>

                                            <span class="employee-meta">

                                                @if($membre?->matricule)

                                                    {{ $membre->matricule }}

                                                @else

                                                    Membre

                                                @endif

                                            </span>

                                        </div>

                                    </div>

                                </td>


                                {{-- SALAIRE --}}

                                <td>

                                    @if($salaire)

                                        <a
                                            href="{{ route('admin.salaires.show', $salaire) }}"
                                            class="salary-reference"
                                        >
                                            {{ $salaire->reference ?? 'Salaire' }}
                                        </a>

                                        @if($moisSalaire)

                                            <span class="salary-status {{ $statutClasse }}">
                                                {{ $statutLabel }}
                                            </span>

                                            <small class="text-muted">
                                                {{ $moisSalaire }}
                                            </small>

                                        @else

                                            <span class="salary-status {{ $statutClasse }}">
                                                {{ $statutLabel }}
                                            </span>

                                        @endif

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- MODE --}}

                                <td>

                                    <span class="payment-mode {{ $modeClass }}">

                                        <i class="fas {{ $modeIcon }}"></i>

                                        {{ $modeLabel }}

                                    </span>

                                </td>


                                {{-- MONTANT --}}

                                <td class="text-right">

                                    <div class="amount-cell">

                                        <strong class="tabular">
                                            {{ number_format(
                                                (float) ($paiement->montant ?? 0),
                                                2,
                                                ',',
                                                ' '
                                            ) }}
                                        </strong>

                                        <span class="currency">
                                            USD
                                        </span>

                                    </div>

                                </td>


                                {{-- PAYEUR --}}

                                <td>

                                    <div class="payer-cell">

                                        <i class="fas fa-user-check"></i>

                                        <span>
                                            {{ $payeur }}
                                        </span>

                                    </div>

                                </td>


                                {{-- ACTIONS --}}

                                <td>

                                    <div class="table-actions">

                                        <a
                                            href="{{ route('admin.paiements-salaires.show', $paiement) }}"
                                            class="table-action"
                                            title="Voir le paiement"
                                            aria-label="Voir le paiement"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a
                                            href="{{ route('admin.paiements-salaires.recu', $paiement) }}"
                                            class="table-action"
                                            title="Voir le reçu"
                                            aria-label="Voir le reçu"
                                            target="_blank"
                                        >
                                            <i class="fas fa-receipt"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>


                    <tfoot>

                        <tr>

                            <td
                                colspan="6"
                                class="text-right"
                            >
                                <strong>
                                    Total de la période
                                </strong>
                            </td>

                            <td class="text-right">

                                <div class="amount-cell">

                                    <strong class="tabular">
                                        {{ number_format(
                                            $montantTotal,
                                            2,
                                            ',',
                                            ' '
                                        ) }}
                                    </strong>

                                    <span class="currency">
                                        USD
                                    </span>

                                </div>

                            </td>

                            <td colspan="2"></td>

                        </tr>

                    </tfoot>

                </table>

            </div>


            {{-- =================================================
                 RÉSUMÉ DES RÉSULTATS
                 ================================================= --}}

            <div class="result-summary">

                <div class="result-count">

                    <i class="fas fa-list-check"></i>

                    <span>

                        {{ number_format(
                            $nombrePaiements,
                            0,
                            ',',
                            ' '
                        ) }}

                        opération{{ $nombrePaiements > 1 ? 's' : '' }}

                    </span>

                </div>


                <div class="result-total">

                    Total :
                    <strong class="tabular">

                        {{ number_format(
                            $montantTotal,
                            2,
                            ',',
                            ' '
                        ) }}

                        USD

                    </strong>

                </div>

            </div>


            {{-- =================================================
                 PAGINATION
                 ================================================= --}}

            @if(method_exists($listePaiements, 'links'))

                @if($listePaiements->hasPages())

                    <div class="pagination-wrapper">

                        <div class="pagination">

                            @if($listePaiements->onFirstPage())

                                <span class="disabled">
                                    <i class="fas fa-chevron-left"></i>
                                </span>

                            @else

                                <a
                                    href="{{ $listePaiements->previousPageUrl() }}"
                                    aria-label="Page précédente"
                                >
                                    <i class="fas fa-chevron-left"></i>
                                </a>

                            @endif


                            @php
                                $currentPage =
                                    $listePaiements->currentPage();

                                $lastPage =
                                    $listePaiements->lastPage();

                                $startPage =
                                    max(
                                        1,
                                        $currentPage - 2
                                    );

                                $endPage =
                                    min(
                                        $lastPage,
                                        $currentPage + 2
                                    );
                            @endphp


                            @if($startPage > 1)

                                <a
                                    href="{{ $listePaiements->url(1) }}"
                                >
                                    1
                                </a>

                                @if($startPage > 2)
                                    <span>…</span>
                                @endif

                            @endif


                            @for($page = $startPage; $page <= $endPage; $page++)

                                @if($page === $currentPage)

                                    <span
                                        class="active"
                                        aria-current="page"
                                    >
                                        {{ $page }}
                                    </span>

                                @else

                                    <a
                                        href="{{ $listePaiements->url($page) }}"
                                    >
                                        {{ $page }}
                                    </a>

                                @endif

                            @endfor


                            @if($endPage < $lastPage)

                                @if($endPage < $lastPage - 1)
                                    <span>…</span>
                                @endif

                                <a
                                    href="{{ $listePaiements->url($lastPage) }}"
                                >
                                    {{ $lastPage }}
                                </a>

                            @endif


                            @if($listePaiements->hasMorePages())

                                <a
                                    href="{{ $listePaiements->nextPageUrl() }}"
                                    aria-label="Page suivante"
                                >
                                    <i class="fas fa-chevron-right"></i>
                                </a>

                            @else

                                <span class="disabled">
                                    <i class="fas fa-chevron-right"></i>
                                </span>

                            @endif

                        </div>

                    </div>

                @endif

            @endif

        @else

            {{-- =================================================
                 ÉTAT VIDE
                 ================================================= --}}

            <div class="empty-state">

                <div class="empty-icon">
                    <i class="fas fa-file-circle-xmark"></i>
                </div>

                <h3 class="empty-title">
                    Aucune opération trouvée
                </h3>

                <p class="empty-text">
                    Aucun paiement ne correspond aux critères
                    sélectionnés pour cette période.
                </p>

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-reset-filters
                >
                    <i class="fas fa-rotate-left"></i>
                    Réinitialiser les filtres
                </button>

            </div>

        @endif

    </div>

</section>


{{-- =========================================================
     OBSERVATIONS
     ========================================================= --}}

<section class="observations-box">

    <h2 class="observations-title">

        <i class="fas fa-circle-info"></i>

        Lecture du rapport

    </h2>

    <p class="observations-text">

        Le montant total présenté correspond aux paiements
        effectivement enregistrés dans le système pour les
        critères sélectionnés.

        Les paiements sont indépendants de l'obligation salariale :
        un même salaire peut apparaître dans plusieurs opérations
        lorsqu'il est réglé en plusieurs fois.

        Les références des opérations constituent les identifiants
        de traçabilité utilisés pour retrouver les justificatifs
        correspondants.

    </p>

</section>


{{-- =========================================================
     SIGNATURES
     ========================================================= --}}

<section class="signatures">

    <div class="signature-box">

        <span class="signature-title">
            Préparé par
        </span>

        <div class="signature-line"></div>

        <strong class="signature-name">
            Responsable financier
        </strong>

        <span class="signature-role">
            Préparation du rapport
        </span>

    </div>


    <div class="signature-box">

        <span class="signature-title">
            Vérifié par
        </span>

        <div class="signature-line"></div>

        <strong class="signature-name">
            Direction
        </strong>

        <span class="signature-role">
            Vérification et validation
        </span>

    </div>


    <div class="signature-box">

        <span class="signature-title">
            Date
        </span>

        <div class="signature-line"></div>

        <strong class="signature-name">
            {{ now()->format('d/m/Y') }}
        </strong>

        <span class="signature-role">
            Date d'établissement
        </span>

    </div>

</section>


{{-- =========================================================
     PIED DU RAPPORT
     ========================================================= --}}

<footer class="report-footer">

    <div class="footer-text">

        <strong>
            {{ $settings->nom ?? 'PHOVE' }}
        </strong>

        <span>
            — Rapport des paiements de salaires
        </span>

    </div>

    <div class="footer-reference">

        <span>
            Période :
        </span>

        <strong>
            {{ $periodeLibelle }}
        </strong>

        <span>
            • Généré le {{ $dateGeneration }}
        </span>

    </div>

</footer>


{{-- =========================================================
     ACTIONS FINALES
     ========================================================= --}}

<div class="report-actions">

    <a
        href="{{ route('admin.paiements-salaires.index') }}"
        class="btn btn-secondary"
    >
        <i class="fas fa-arrow-left"></i>
        Retour aux paiements
    </a>

    <button
        type="button"
        class="btn btn-primary"
        data-print
    >
        <i class="fas fa-print"></i>
        Imprimer le rapport
    </button>

</div>


</div>

@endsection

@push('scripts') <script
     src="{{ asset('assets/js/admin/paiements-salaires/rapport.js') }}"
 ></script>
@endpush
