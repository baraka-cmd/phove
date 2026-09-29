@extends('layouts.admin')

@section('title', 'Statistiques des paiements de salaires')

{{-- ============================================================
CSS SPÉCIFIQUE À LA PAGE
============================================================ --}}
@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/paiements-salaires/statistiques.css') }}"
    >
@endsection

@section('content')

@php

/*
|--------------------------------------------------------------------------
| NORMALISATION DES STATISTIQUES
|--------------------------------------------------------------------------
|
| Le contrôleur fournit normalement :
|
| nombre
| montant_total
| nombre_especes
| montant_especes
| nombre_banque
| montant_banque
| nombre_mobile_money
| montant_mobile_money
| nombre_cheques
| montant_cheques
|
| Les valeurs par défaut évitent les erreurs si une statistique
| n'est pas disponible.
|--------------------------------------------------------------------------
*/

$nombrePaiements = (int) (
    $stats['nombre']
    ?? 0
);

$montantTotal = (float) (
    $stats['montant_total']
    ?? 0
);

$nombreEspeces = (int) (
    $stats['nombre_especes']
    ?? 0
);

$montantEspeces = (float) (
    $stats['montant_especes']
    ?? 0
);

$nombreBanque = (int) (
    $stats['nombre_banque']
    ?? 0
);

$montantBanque = (float) (
    $stats['montant_banque']
    ?? 0
);

$nombreMobileMoney = (int) (
    $stats['nombre_mobile_money']
    ?? 0
);

$montantMobileMoney = (float) (
    $stats['montant_mobile_money']
    ?? 0
);

$nombreCheques = (int) (
    $stats['nombre_cheques']
    ?? 0
);

$montantCheques = (float) (
    $stats['montant_cheques']
    ?? 0
);

/*
|--------------------------------------------------------------------------
| TOTAL DES PAIEMENTS ÉLECTRONIQUES
|--------------------------------------------------------------------------
*/

$montantElectronique =
    $montantBanque
    + $montantMobileMoney
    + $montantCheques;

$nombreElectronique =
    $nombreBanque
    + $nombreMobileMoney
    + $nombreCheques;

/*
|--------------------------------------------------------------------------
| POURCENTAGES
|--------------------------------------------------------------------------
*/

$pourcentageEspeces = $montantTotal > 0
    ? round(($montantEspeces / $montantTotal) * 100, 1)
    : 0;

$pourcentageBanque = $montantTotal > 0
    ? round(($montantBanque / $montantTotal) * 100, 1)
    : 0;

$pourcentageMobileMoney = $montantTotal > 0
    ? round(($montantMobileMoney / $montantTotal) * 100, 1)
    : 0;

$pourcentageCheques = $montantTotal > 0
    ? round(($montantCheques / $montantTotal) * 100, 1)
    : 0;

$pourcentageElectronique = $montantTotal > 0
    ? round(($montantElectronique / $montantTotal) * 100, 1)
    : 0;

/*
|--------------------------------------------------------------------------
| MOIS D'AFFICHAGE
|--------------------------------------------------------------------------
*/

$moisAffiche = $mois;

try {
    $moisAffiche = \Carbon\Carbon::createFromFormat(
        'Y-m',
        $mois
    )->translatedFormat('F Y');
} catch (\Throwable $exception) {
    $moisAffiche = $mois ?: 'Toutes les périodes';
}

/*
|--------------------------------------------------------------------------
| MONTANT MOYEN
|--------------------------------------------------------------------------
*/

$montantMoyen = $nombrePaiements > 0
    ? $montantTotal / $nombrePaiements
    : 0;

/*
|--------------------------------------------------------------------------
| MODE LE PLUS UTILISÉ
|--------------------------------------------------------------------------
*/

$repartition = [
    'Espèces' => [
        'nombre' => $nombreEspeces,
        'montant' => $montantEspeces,
        'pourcentage' => $pourcentageEspeces,
        'icon' => 'fa-money-bill-wave',
        'class' => 'cash',
    ],

    'Virement bancaire' => [
        'nombre' => $nombreBanque,
        'montant' => $montantBanque,
        'pourcentage' => $pourcentageBanque,
        'icon' => 'fa-university',
        'class' => 'bank',
    ],

    'Mobile Money' => [
        'nombre' => $nombreMobileMoney,
        'montant' => $montantMobileMoney,
        'pourcentage' => $pourcentageMobileMoney,
        'icon' => 'fa-mobile-alt',
        'class' => 'mobile',
    ],

    'Chèque' => [
        'nombre' => $nombreCheques,
        'montant' => $montantCheques,
        'pourcentage' => $pourcentageCheques,
        'icon' => 'fa-money-check',
        'class' => 'check',
    ],
];

$modePrincipal = collect($repartition)
    ->sortByDesc('montant')
    ->first();

@endphp

<div class="payment-statistics">

    {{-- ========================================================
         EN-TÊTE DE PAGE
    ========================================================= --}}
    <section class="statistics-header">

        <div class="statistics-header-content">

            <div class="statistics-heading">

                <div class="statistics-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>

                <div>
                    <span class="statistics-eyebrow">
                        Analyse financière
                    </span>

                    <h1 class="statistics-title">
                        Statistiques des paiements
                    </h1>

                    <p class="statistics-subtitle">
                        Vue synthétique des paiements de salaires
                        et de leur répartition par mode de paiement.
                    </p>
                </div>

            </div>

            <div class="statistics-period">

                <span class="period-label">
                    <i class="far fa-calendar-alt"></i>
                    Période analysée
                </span>

                <strong class="period-value">
                    {{ ucfirst($moisAffiche) }}
                </strong>

            </div>

        </div>

    </section>


    {{-- ========================================================
         FILTRE DE PÉRIODE
    ========================================================= --}}
    <section class="statistics-filter-card">

        <form
            method="GET"
            action="{{ route('admin.paiements-salaires.statistiques') }}"
            class="statistics-filter-form"
            data-statistics-filter
        >

            <div class="filter-group">

                <label for="mois" class="filter-label">
                    <i class="far fa-calendar"></i>
                    Mois
                </label>

                <input
                    type="month"
                    id="mois"
                    name="mois"
                    value="{{ $mois }}"
                    class="filter-input"
                    data-month-filter
                >

            </div>

            <div class="filter-actions">

                <button
                    type="submit"
                    class="statistics-btn statistics-btn-primary"
                    data-submit-filter
                >
                    <i class="fas fa-filter"></i>
                    <span>Actualiser</span>
                </button>

                <a
                    href="{{ route('admin.paiements-salaires.statistiques') }}"
                    class="statistics-btn statistics-btn-secondary"
                    data-reset-filters
                >
                    <i class="fas fa-undo"></i>
                    <span>Réinitialiser</span>
                </a>

            </div>

        </form>

    </section>


    {{-- ========================================================
         INDICATEURS PRINCIPAUX
    ========================================================= --}}
    <section class="statistics-kpis">

        {{-- TOTAL --}}
        <article class="statistics-kpi-card kpi-total">

            <div class="kpi-top">

                <div class="kpi-icon">
                    <i class="fas fa-wallet"></i>
                </div>

                <span class="kpi-label">
                    Total payé
                </span>

            </div>

            <div class="kpi-value">
                {{ number_format($montantTotal, 2, ',', ' ') }}
                <span>USD</span>
            </div>

            <div class="kpi-footer">
                <i class="fas fa-receipt"></i>

                <span>
                    {{ number_format($nombrePaiements) }}
                    paiement{{ $nombrePaiements > 1 ? 's' : '' }}
                </span>
            </div>

        </article>


        {{-- ESPÈCES --}}
        <article class="statistics-kpi-card kpi-cash">

            <div class="kpi-top">

                <div class="kpi-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>

                <span class="kpi-label">
                    Espèces
                </span>

            </div>

            <div class="kpi-value">
                {{ number_format($montantEspeces, 2, ',', ' ') }}
                <span>USD</span>
            </div>

            <div class="kpi-footer">

                <span>
                    {{ $pourcentageEspeces }} %
                    du total
                </span>

                <span>
                    {{ $nombreEspeces }}
                    paiement{{ $nombreEspeces > 1 ? 's' : '' }}
                </span>

            </div>

        </article>


        {{-- VIREMENT --}}
        <article class="statistics-kpi-card kpi-bank">

            <div class="kpi-top">

                <div class="kpi-icon">
                    <i class="fas fa-university"></i>
                </div>

                <span class="kpi-label">
                    Virement bancaire
                </span>

            </div>

            <div class="kpi-value">
                {{ number_format($montantBanque, 2, ',', ' ') }}
                <span>USD</span>
            </div>

            <div class="kpi-footer">

                <span>
                    {{ $pourcentageBanque }} %
                    du total
                </span>

                <span>
                    {{ $nombreBanque }}
                    paiement{{ $nombreBanque > 1 ? 's' : '' }}
                </span>

            </div>

        </article>


        {{-- MOBILE MONEY --}}
        <article class="statistics-kpi-card kpi-mobile">

            <div class="kpi-top">

                <div class="kpi-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>

                <span class="kpi-label">
                    Mobile Money
                </span>

            </div>

            <div class="kpi-value">
                {{ number_format($montantMobileMoney, 2, ',', ' ') }}
                <span>USD</span>
            </div>

            <div class="kpi-footer">

                <span>
                    {{ $pourcentageMobileMoney }} %
                    du total
                </span>

                <span>
                    {{ $nombreMobileMoney }}
                    paiement{{ $nombreMobileMoney > 1 ? 's' : '' }}
                </span>

            </div>

        </article>


        {{-- CHÈQUES --}}
        <article class="statistics-kpi-card kpi-check">

            <div class="kpi-top">

                <div class="kpi-icon">
                    <i class="fas fa-money-check"></i>
                </div>

                <span class="kpi-label">
                    Chèques
                </span>

            </div>

            <div class="kpi-value">
                {{ number_format($montantCheques, 2, ',', ' ') }}
                <span>USD</span>
            </div>

            <div class="kpi-footer">

                <span>
                    {{ $pourcentageCheques }} %
                    du total
                </span>

                <span>
                    {{ $nombreCheques }}
                    paiement{{ $nombreCheques > 1 ? 's' : '' }}
                </span>

            </div>

        </article>

    </section>


    {{-- ========================================================
         ANALYSE GÉNÉRALE
    ========================================================= --}}
    <section class="statistics-analysis-grid">

        <article class="statistics-panel overview-panel">

            <div class="panel-header">

                <div>
                    <span class="panel-eyebrow">
                        Synthèse
                    </span>

                    <h2 class="panel-title">
                        Vue financière
                    </h2>
                </div>

                <div class="panel-header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>

            </div>

            <div class="overview-list">

                <div class="overview-item">

                    <div class="overview-item-icon">
                        <i class="fas fa-receipt"></i>
                    </div>

                    <div class="overview-item-content">

                        <span class="overview-item-label">
                            Nombre total de paiements
                        </span>

                        <strong class="overview-item-value">
                            {{ number_format($nombrePaiements) }}
                        </strong>

                    </div>

                </div>


                <div class="overview-item">

                    <div class="overview-item-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>

                    <div class="overview-item-content">

                        <span class="overview-item-label">
                            Montant total payé
                        </span>

                        <strong class="overview-item-value">
                            {{ number_format($montantTotal, 2, ',', ' ') }}
                            USD
                        </strong>

                    </div>

                </div>


                <div class="overview-item">

                    <div class="overview-item-icon">
                        <i class="fas fa-calculator"></i>
                    </div>

                    <div class="overview-item-content">

                        <span class="overview-item-label">
                            Montant moyen par paiement
                        </span>

                        <strong class="overview-item-value">
                            {{ number_format($montantMoyen, 2, ',', ' ') }}
                            USD
                        </strong>

                    </div>

                </div>


                <div class="overview-item">

                    <div class="overview-item-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>

                    <div class="overview-item-content">

                        <span class="overview-item-label">
                            Paiements électroniques
                        </span>

                        <strong class="overview-item-value">
                            {{ number_format($montantElectronique, 2, ',', ' ') }}
                            USD
                        </strong>

                    </div>

                </div>

            </div>

        </article>


        {{-- MODE PRINCIPAL --}}
        <article class="statistics-panel dominant-panel">

            <div class="panel-header">

                <div>
                    <span class="panel-eyebrow">
                        Analyse
                    </span>

                    <h2 class="panel-title">
                        Mode de paiement dominant
                    </h2>
                </div>

                <div class="panel-header-icon">
                    <i class="fas fa-trophy"></i>
                </div>

            </div>

            @if($modePrincipal && $modePrincipal['montant'] > 0)

                <div class="dominant-content">

                    <div class="dominant-icon {{ $modePrincipal['class'] }}">
                        <i class="fas {{ $modePrincipal['icon'] }}"></i>
                    </div>

                    <div class="dominant-info">

                        <span class="dominant-label">
                            Mode principal
                        </span>

                        <strong class="dominant-name">
                            {{ array_search($modePrincipal, $repartition, true) }}
                        </strong>

                        <div class="dominant-amount">
                            {{ number_format($modePrincipal['montant'], 2, ',', ' ') }}
                            <span>USD</span>
                        </div>

                        <span class="dominant-percentage">
                            {{ $modePrincipal['pourcentage'] }} %
                            du montant total
                        </span>

                    </div>

                </div>

            @else

                <div class="statistics-empty">

                    <div class="empty-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>

                    <strong>
                        Aucune donnée disponible
                    </strong>

                    <p>
                        Aucun paiement n'a été enregistré
                        pour la période sélectionnée.
                    </p>

                </div>

            @endif

        </article>

    </section>


    {{-- ========================================================
         RÉPARTITION PAR MODE DE PAIEMENT
    ========================================================= --}}
    <section class="statistics-panel distribution-panel">

        <div class="panel-header">

            <div>
                <span class="panel-eyebrow">
                    Répartition
                </span>

                <h2 class="panel-title">
                    Paiements par mode
                </h2>

                <p class="panel-description">
                    Répartition des montants effectivement payés
                    selon le moyen utilisé.
                </p>
            </div>

            <div class="panel-header-icon">
                <i class="fas fa-chart-pie"></i>
            </div>

        </div>


        <div class="distribution-list">

            {{-- ESPÈCES --}}
            <div class="distribution-item">

                <div class="distribution-main">

                    <div class="distribution-icon cash">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>

                    <div class="distribution-info">

                        <div class="distribution-title-row">

                            <strong>
                                Espèces
                            </strong>

                            <span>
                                {{ $nombreEspeces }}
                                paiement{{ $nombreEspeces > 1 ? 's' : '' }}
                            </span>

                        </div>

                        <div class="distribution-track">

                            <div
                                class="distribution-progress cash"
                                data-progress="{{ $pourcentageEspeces }}"
                            ></div>

                        </div>

                    </div>

                    <div class="distribution-values">

                        <strong>
                            {{ number_format($montantEspeces, 2, ',', ' ') }}
                            USD
                        </strong>

                        <span>
                            {{ $pourcentageEspeces }} %
                        </span>

                    </div>

                </div>

            </div>


            {{-- VIREMENT --}}
            <div class="distribution-item">

                <div class="distribution-main">

                    <div class="distribution-icon bank">
                        <i class="fas fa-university"></i>
                    </div>

                    <div class="distribution-info">

                        <div class="distribution-title-row">

                            <strong>
                                Virement bancaire
                            </strong>

                            <span>
                                {{ $nombreBanque }}
                                paiement{{ $nombreBanque > 1 ? 's' : '' }}
                            </span>

                        </div>

                        <div class="distribution-track">

                            <div
                                class="distribution-progress bank"
                                data-progress="{{ $pourcentageBanque }}"
                            ></div>

                        </div>

                    </div>

                    <div class="distribution-values">

                        <strong>
                            {{ number_format($montantBanque, 2, ',', ' ') }}
                            USD
                        </strong>

                        <span>
                            {{ $pourcentageBanque }} %
                        </span>

                    </div>

                </div>

            </div>


            {{-- MOBILE MONEY --}}
            <div class="distribution-item">

                <div class="distribution-main">

                    <div class="distribution-icon mobile">
                        <i class="fas fa-mobile-alt"></i>
                    </div>

                    <div class="distribution-info">

                        <div class="distribution-title-row">

                            <strong>
                                Mobile Money
                            </strong>

                            <span>
                                {{ $nombreMobileMoney }}
                                paiement{{ $nombreMobileMoney > 1 ? 's' : '' }}
                            </span>

                        </div>

                        <div class="distribution-track">

                            <div
                                class="distribution-progress mobile"
                                data-progress="{{ $pourcentageMobileMoney }}"
                            ></div>

                        </div>

                    </div>

                    <div class="distribution-values">

                        <strong>
                            {{ number_format($montantMobileMoney, 2, ',', ' ') }}
                            USD
                        </strong>

                        <span>
                            {{ $pourcentageMobileMoney }} %
                        </span>

                    </div>

                </div>

            </div>


            {{-- CHÈQUE --}}
            <div class="distribution-item">

                <div class="distribution-main">

                    <div class="distribution-icon check">
                        <i class="fas fa-money-check"></i>
                    </div>

                    <div class="distribution-info">

                        <div class="distribution-title-row">

                            <strong>
                                Chèque
                            </strong>

                            <span>
                                {{ $nombreCheques }}
                                paiement{{ $nombreCheques > 1 ? 's' : '' }}
                            </span>

                        </div>

                        <div class="distribution-track">

                            <div
                                class="distribution-progress check"
                                data-progress="{{ $pourcentageCheques }}"
                            ></div>

                        </div>

                    </div>

                    <div class="distribution-values">

                        <strong>
                            {{ number_format($montantCheques, 2, ',', ' ') }}
                            USD
                        </strong>

                        <span>
                            {{ $pourcentageCheques }} %
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================
         TABLEAU RÉCAPITULATIF
    ========================================================= --}}
    <section class="statistics-panel summary-panel">

        <div class="panel-header">

            <div>
                <span class="panel-eyebrow">
                    Récapitulatif
                </span>

                <h2 class="panel-title">
                    Synthèse des paiements
                </h2>
            </div>

            <div class="panel-header-icon">
                <i class="fas fa-table"></i>
            </div>

        </div>


        <div class="summary-table-wrapper">

            <table class="summary-table">

                <thead>

                    <tr>
                        <th>Mode de paiement</th>
                        <th class="text-center">Nombre</th>
                        <th class="text-right">Montant</th>
                        <th class="text-right">Part</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>
                            <div class="table-mode">

                                <span class="table-mode-icon cash">
                                    <i class="fas fa-money-bill-wave"></i>
                                </span>

                                <strong>
                                    Espèces
                                </strong>

                            </div>
                        </td>

                        <td class="text-center">
                            {{ $nombreEspeces }}
                        </td>

                        <td class="text-right">
                            {{ number_format($montantEspeces, 2, ',', ' ') }}
                            USD
                        </td>

                        <td class="text-right">
                            {{ $pourcentageEspeces }} %
                        </td>

                    </tr>


                    <tr>

                        <td>
                            <div class="table-mode">

                                <span class="table-mode-icon bank">
                                    <i class="fas fa-university"></i>
                                </span>

                                <strong>
                                    Virement bancaire
                                </strong>

                            </div>
                        </td>

                        <td class="text-center">
                            {{ $nombreBanque }}
                        </td>

                        <td class="text-right">
                            {{ number_format($montantBanque, 2, ',', ' ') }}
                            USD
                        </td>

                        <td class="text-right">
                            {{ $pourcentageBanque }} %
                        </td>

                    </tr>


                    <tr>

                        <td>
                            <div class="table-mode">

                                <span class="table-mode-icon mobile">
                                    <i class="fas fa-mobile-alt"></i>
                                </span>

                                <strong>
                                    Mobile Money
                                </strong>

                            </div>
                        </td>

                        <td class="text-center">
                            {{ $nombreMobileMoney }}
                        </td>

                        <td class="text-right">
                            {{ number_format($montantMobileMoney, 2, ',', ' ') }}
                            USD
                        </td>

                        <td class="text-right">
                            {{ $pourcentageMobileMoney }} %
                        </td>

                    </tr>


                    <tr>

                        <td>
                            <div class="table-mode">

                                <span class="table-mode-icon check">
                                    <i class="fas fa-money-check"></i>
                                </span>

                                <strong>
                                    Chèque
                                </strong>

                            </div>
                        </td>

                        <td class="text-center">
                            {{ $nombreCheques }}
                        </td>

                        <td class="text-right">
                            {{ number_format($montantCheques, 2, ',', ' ') }}
                            USD
                        </td>

                        <td class="text-right">
                            {{ $pourcentageCheques }} %
                        </td>

                    </tr>

                </tbody>

                <tfoot>

                    <tr>

                        <td>
                            <strong>
                                Total
                            </strong>
                        </td>

                        <td class="text-center">
                            <strong>
                                {{ $nombrePaiements }}
                            </strong>
                        </td>

                        <td class="text-right">
                            <strong>
                                {{ number_format($montantTotal, 2, ',', ' ') }}
                                USD
                            </strong>
                        </td>

                        <td class="text-right">
                            <strong>
                                100 %
                            </strong>
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </section>


    {{-- ========================================================
         INFORMATION FINANCIÈRE
    ========================================================= --}}
    <section class="statistics-information">

        <div class="information-icon">
            <i class="fas fa-info-circle"></i>
        </div>

        <div class="information-content">

            <strong>
                À propos de ces statistiques
            </strong>

            <p>
                Les montants présentés correspondent aux paiements
                enregistrés pour la période sélectionnée.
                Les statistiques sont calculées à partir des
                paiements effectivement enregistrés dans le système.
            </p>

        </div>

    </section>

</div>

@endsection


{{-- ============================================================
JAVASCRIPT SPÉCIFIQUE À LA PAGE
============================================================ --}}
@section('scripts')
    <script
        src="{{ asset('assets/js/admin/paiements-salaires/statistiques.js') }}"
    ></script>
@endsection