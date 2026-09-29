@extends('layouts.admin')

@section('title', 'Paiements des salaires')

{{-- ============================================================
CSS SPÉCIFIQUE À LA PAGE
============================================================ --}}
@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/paiements-salaires/index.css') }}"
    >
@endsection

@section('content')

@php

/*
|--------------------------------------------------------------------------
| STATISTIQUES
|--------------------------------------------------------------------------
*/

$nombrePaiements = $stats['nombre'] ?? $paiements->total();

$montantTotal = (float) (
    $stats['montant_total'] ?? 0
);

$montantEspeces = (float) (
    $stats['montant_especes'] ?? 0
);

$montantVirement = (float) (
    $stats['montant_banque'] ?? 0
);

$montantMobileMoney = (float) (
    $stats['montant_mobile_money'] ?? 0
);

$montantCheque = (float) (
    $stats['montant_cheques'] ?? 0
);

$montantElectronique =
    $montantVirement
    + $montantMobileMoney
    + $montantCheque;

$hasFilters =
    request()->filled('mois')
    || request()->filled('mode_paiement')
    || request()->filled('recherche')
    || request()->filled('date');

@endphp

<div class="admin-content">

```
{{-- ========================================================
     RACINE DU MODULE
========================================================= --}}
<div class="payment-index">

    {{-- ====================================================
         EN-TÊTE
    ===================================================== --}}
    <header class="page-header">

        <div class="page-header-content">

            <div>

                <h1 class="page-title">

                    <span class="page-title-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </span>

                    <span>
                        Paiements des salaires
                    </span>

                </h1>

                <p class="page-subtitle">
                    Consultez et suivez l'ensemble des paiements
                    de salaires enregistrés.
                </p>

            </div>

            <div class="page-actions">

                <a
                    href="{{ route('admin.paiements-salaires.statistiques') }}"
                    class="btn btn-outline"
                >
                    <i class="fas fa-chart-bar"></i>
                    <span>Statistiques</span>
                </a>

                <a
                    href="{{ route('admin.paiements-salaires.rapport') }}"
                    class="btn btn-outline"
                >
                    <i class="fas fa-file-alt"></i>
                    <span>Rapport</span>
                </a>

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-print
                    title="Imprimer la liste des paiements"
                    aria-label="Imprimer la liste des paiements"
                >
                    <i class="fas fa-print"></i>
                    <span>Imprimer</span>
                </button>

            </div>

        </div>

    </header>


    {{-- ====================================================
         MESSAGES SESSION
    ===================================================== --}}

    @if(session('success'))

        <div
            class="alert alert-success"
            role="alert"
        >

            <i class="fas fa-check-circle"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    @if(session('error'))

        <div
            class="alert alert-danger"
            role="alert"
        >

            <i class="fas fa-exclamation-circle"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    @if(session('warning'))

        <div
            class="alert alert-warning"
            role="alert"
        >

            <i class="fas fa-exclamation-triangle"></i>

            <span>
                {{ session('warning') }}
            </span>

        </div>

    @endif


    {{-- ====================================================
         ERREURS DE VALIDATION
    ===================================================== --}}

    @if($errors->any())

        <div
            class="alert alert-danger"
            role="alert"
            aria-live="assertive"
        >

            <i class="fas fa-exclamation-triangle"></i>

            <div>

                <strong>
                    Une erreur est survenue.
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>

            </div>

        </div>

    @endif


    {{-- ====================================================
         STATISTIQUES
    ===================================================== --}}

    <section
        class="stats-grid"
        aria-label="Statistiques des paiements"
    >

        {{-- NOMBRE DE PAIEMENTS --}}
        <article class="stat-card">

            <div class="stat-icon">

                <i class="fas fa-receipt"></i>

            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Paiements enregistrés
                </span>

                <strong class="stat-value">

                    {{ number_format(
                        $nombrePaiements,
                        0,
                        ',',
                        ' '
                    ) }}

                </strong>

            </div>

        </article>


        {{-- MONTANT TOTAL --}}
        <article class="stat-card success">

            <div class="stat-icon">

                <i class="fas fa-wallet"></i>

            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Montant total payé
                </span>

                <strong class="stat-value">

                    {{ number_format(
                        $montantTotal,
                        2,
                        ',',
                        ' '
                    ) }}

                    <small>USD</small>

                </strong>

            </div>

        </article>


        {{-- ESPÈCES --}}
        <article class="stat-card warning">

            <div class="stat-icon">

                <i class="fas fa-money-bill"></i>

            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Paiements en espèces
                </span>

                <strong class="stat-value">

                    {{ number_format(
                        $montantEspeces,
                        2,
                        ',',
                        ' '
                    ) }}

                    <small>USD</small>

                </strong>

            </div>

        </article>


        {{-- VIREMENT --}}
        <article class="stat-card info">

            <div class="stat-icon">

                <i class="fas fa-university"></i>

            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Virements bancaires
                </span>

                <strong class="stat-value">

                    {{ number_format(
                        $montantVirement,
                        2,
                        ',',
                        ' '
                    ) }}

                    <small>USD</small>

                </strong>

            </div>

        </article>


        {{-- MOBILE MONEY --}}
        <article class="stat-card info">

            <div class="stat-icon">

                <i class="fas fa-mobile-alt"></i>

            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Mobile Money
                </span>

                <strong class="stat-value">

                    {{ number_format(
                        $montantMobileMoney,
                        2,
                        ',',
                        ' '
                    ) }}

                    <small>USD</small>

                </strong>

            </div>

        </article>


        {{-- CHÈQUES --}}
        <article class="stat-card danger">

            <div class="stat-icon">

                <i class="fas fa-file-invoice"></i>

            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Paiements par chèque
                </span>

                <strong class="stat-value">

                    {{ number_format(
                        $montantCheque,
                        2,
                        ',',
                        ' '
                    ) }}

                    <small>USD</small>

                </strong>

            </div>

        </article>

    </section>


    {{-- ====================================================
         FILTRES
    ===================================================== --}}

    <section class="filter-card">

        <div class="filter-header">

            <div class="filter-heading">

                <i class="fas fa-filter"></i>

                <div>

                    <h2 class="filter-title">
                        Filtrer les paiements
                    </h2>

                </div>

            </div>

            @if($hasFilters)

                <a
                    href="{{ route('admin.paiements-salaires.index') }}"
                    class="filter-reset"
                    data-reset-filters
                >
                    <i class="fas fa-times"></i>
                    Réinitialiser
                </a>

            @endif

        </div>


        <div class="filter-body">

            <form
                method="GET"
                action="{{ route('admin.paiements-salaires.index') }}"
                class="filter-form"
                data-filter-form
                id="payment-filter-form"
            >

                <div class="filter-grid">

                    {{-- RECHERCHE --}}
                    <div class="form-group">

                        <label
                            for="recherche"
                            class="form-label"
                        >
                            Recherche
                        </label>

                        <div class="search-field">

                            <i class="fas fa-search"></i>

                            <input
                                type="search"
                                id="recherche"
                                name="recherche"
                                class="form-control"
                                value="{{ request('recherche') }}"
                                placeholder="Référence, membre, matricule..."
                                autocomplete="off"
                                data-search-input
                                aria-label="Rechercher un paiement"
                            >

                        </div>

                    </div>


                    {{-- MOIS --}}
                    <div class="form-group">

                        <label
                            for="mois"
                            class="form-label"
                        >
                            Mois
                        </label>

                        <input
                            type="month"
                            id="mois"
                            name="mois"
                            class="form-control"
                            value="{{ request('mois') }}"
                            data-month-filter
                        >

                    </div>


                    {{-- MODE --}}
                    <div class="form-group">

                        <label
                            for="mode_paiement"
                            class="form-label"
                        >
                            Mode de paiement
                        </label>

                        <select
                            id="mode_paiement"
                            name="mode_paiement"
                            class="form-control"
                            data-mode-filter
                            data-auto-submit
                        >

                            <option value="">
                                Tous les modes
                            </option>

                            <option
                                value="especes"
                                {{ request('mode_paiement') === 'especes' ? 'selected' : '' }}
                            >
                                Espèces
                            </option>

                            <option
                                value="virement"
                                {{ request('mode_paiement') === 'virement' ? 'selected' : '' }}
                            >
                                Virement bancaire
                            </option>

                            <option
                                value="mobile_money"
                                {{ request('mode_paiement') === 'mobile_money' ? 'selected' : '' }}
                            >
                                Mobile Money
                            </option>

                            <option
                                value="cheque"
                                {{ request('mode_paiement') === 'cheque' ? 'selected' : '' }}
                            >
                                Chèque
                            </option>

                        </select>

                    </div>


                    {{-- DATE --}}
                    <div class="form-group">

                        <label
                            for="date"
                            class="form-label"
                        >
                            Date
                        </label>

                        <input
                            type="date"
                            id="date"
                            name="date"
                            class="form-control"
                            value="{{ request('date') }}"
                            data-date-filter
                        >

                    </div>


                    {{-- ACTIONS --}}
                    <div class="filter-actions">

                        <button
                            type="submit"
                            class="btn btn-primary"
                            data-submit-filter
                        >

                            <i class="fas fa-search"></i>

                            <span>
                                Rechercher
                            </span>

                        </button>

                        @if($hasFilters)

                            <button
                                type="button"
                                class="btn btn-outline"
                                data-clear-search
                                title="Effacer les critères de recherche"
                            >

                                <i class="fas fa-eraser"></i>

                                <span>
                                    Effacer
                                </span>

                            </button>

                        @endif

                    </div>

                </div>

            </form>

        </div>

    </section>


    {{-- ====================================================
         RÉSULTAT ACTUEL
    ===================================================== --}}

    <div class="result-summary">

        <span class="result-dot"></span>

        <span>

            <strong>
                {{ number_format($paiements->total(), 0, ',', ' ') }}
            </strong>

            paiement{{ $paiements->total() > 1 ? 's' : '' }}
            trouvé{{ $paiements->total() > 1 ? 's' : '' }}

        </span>

    </div>


    {{-- ====================================================
         TABLEAU DES PAIEMENTS
    ===================================================== --}}

    <section class="table-card">

        <div class="table-card-header">

            <div class="table-card-heading">

                <i class="fas fa-list"></i>

                <h2 class="table-card-title">
                    Liste des paiements
                </h2>

                <span class="table-count">
                    {{ number_format($paiements->total(), 0, ',', ' ') }}
                </span>

            </div>

        </div>


        <div class="table-responsive">

            @if($paiements->isNotEmpty())

                <table
                    class="payments-table"
                    data-payment-table
                >

                    <thead>

                        <tr>

                            <th>
                                Paiement
                            </th>

                            <th>
                                Bénéficiaire
                            </th>

                            <th>
                                Salaire
                            </th>

                            <th>
                                Période
                            </th>

                            <th>
                                Montant
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Mode
                            </th>

                            <th>
                                Payé par
                            </th>

                            <th class="action-column">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($paiements as $paiement)

                            @php

                                $salairePaiement =
                                    $paiement->salaire;

                                $membrePaiement =
                                    $salairePaiement?->membre;

                                $nomBeneficiaire =
                                    $membrePaiement?->nom_complet
                                    ?? trim(
                                        ($membrePaiement?->nom ?? '') . ' ' .
                                        ($membrePaiement?->post_nom ?? '') . ' ' .
                                        ($membrePaiement?->prenom ?? '')
                                    );

                                $modeLabels = [
                                    'especes' => 'Espèces',
                                    'virement' => 'Virement bancaire',
                                    'mobile_money' => 'Mobile Money',
                                    'cheque' => 'Chèque',
                                ];

                                $modeLabel =
                                    $modeLabels[$paiement->mode_paiement]
                                    ?? ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $paiement->mode_paiement
                                        )
                                    );

                                $modeClass = match(
                                    $paiement->mode_paiement
                                ) {
                                    'especes' => 'mode-especes',
                                    'virement' => 'mode-virement',
                                    'mobile_money' => 'mode-mobile-money',
                                    'cheque' => 'mode-cheque',
                                    default => 'mode-especes',
                                };

                                $modeIcon = match(
                                    $paiement->mode_paiement
                                ) {
                                    'especes' => 'fa-money-bill',
                                    'virement' => 'fa-university',
                                    'mobile_money' => 'fa-mobile-alt',
                                    'cheque' => 'fa-file-invoice',
                                    default => 'fa-wallet',
                                };

                                $moisPaiement =
                                    $salairePaiement?->mois;

                                if ($moisPaiement) {

                                    try {

                                        $moisFormate =
                                            \Carbon\Carbon::createFromFormat(
                                                'Y-m',
                                                $moisPaiement
                                            )->translatedFormat('F Y');

                                    } catch (\Throwable $exception) {

                                        $moisFormate =
                                            $moisPaiement;

                                    }

                                } else {

                                    $moisFormate = '—';

                                }

                            @endphp


                            <tr
                                data-payment-row
                                tabindex="0"
                                aria-label="Paiement {{ $paiement->reference }}"
                            >

                                {{-- ==================================
                                     RÉFÉRENCE
                                =================================== --}}
                                <td>

                                    <div class="payment-reference">

                                        <button
                                            type="button"
                                            data-copy
                                            data-copy-value="{{ $paiement->reference }}"
                                            title="Copier la référence"
                                            aria-label="Copier la référence {{ $paiement->reference }}"
                                        >
                                            {{ $paiement->reference }}
                                        </button>

                                    </div>

                                    <div class="payment-date-time">

                                        Enregistré le
                                        {{ optional($paiement->created_at)->format('d/m/Y H:i') }}

                                    </div>

                                </td>


                                {{-- ==================================
                                     BÉNÉFICIAIRE
                                =================================== --}}
                                <td>

                                    @if($membrePaiement)

                                        <a
                                            href="{{ route(
                                                'admin.membres.show',
                                                $membrePaiement
                                            ) }}"
                                            class="employee-link"
                                        >

                                            <div class="employee-cell">

                                                <div class="employee-avatar">

                                                    <i class="fas fa-user"></i>

                                                </div>

                                                <div class="employee-info">

                                                    <span class="employee-name">
                                                        {{ $nomBeneficiaire }}
                                                    </span>

                                                    @if($membrePaiement->matricule)

                                                        <span class="employee-meta">
                                                            {{ $membrePaiement->matricule }}
                                                        </span>

                                                    @endif

                                                </div>

                                            </div>

                                        </a>

                                    @else

                                        <div class="employee-cell">

                                            <div class="employee-avatar">

                                                <i class="fas fa-user-slash"></i>

                                            </div>

                                            <div class="employee-info">

                                                <span class="employee-name">
                                                    Membre introuvable
                                                </span>

                                            </div>

                                        </div>

                                    @endif

                                </td>


                                {{-- ==================================
                                     SALAIRE
                                =================================== --}}
                                <td>

                                    @if($salairePaiement)

                                        <div class="salary-reference">

                                            <a
                                                href="{{ route(
                                                    'admin.salaires.show',
                                                    $salairePaiement
                                                ) }}"
                                                class="salary-reference-link"
                                            >

                                                <strong>
                                                    {{ $salairePaiement->reference }}
                                                </strong>

                                            </a>

                                        </div>

                                    @else

                                        <span class="text-muted">
                                            Salaire introuvable
                                        </span>

                                    @endif

                                </td>


                                {{-- ==================================
                                     PÉRIODE
                                =================================== --}}
                                <td>

                                    <span class="payment-period">

                                        <i class="fas fa-calendar-alt"></i>

                                        <span>
                                            {{ $moisFormate }}
                                        </span>

                                    </span>

                                </td>


                                {{-- ==================================
                                     MONTANT
                                =================================== --}}
                                <td>

                                    <strong class="payment-amount">

                                        {{ number_format(
                                            (float) $paiement->montant,
                                            2,
                                            ',',
                                            ' '
                                        ) }}

                                        <span class="payment-currency">
                                            USD
                                        </span>

                                    </strong>

                                </td>


                                {{-- ==================================
                                     DATE
                                =================================== --}}
                                <td>

                                    <div class="payment-date">

                                        <span class="payment-date-main">

                                            {{ optional(
                                                $paiement->date_paiement
                                            )->format('d/m/Y') }}

                                        </span>

                                        @if($paiement->created_at)

                                            <span class="payment-date-time">

                                                {{ $paiement->created_at->format('H:i') }}

                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- ==================================
                                     MODE
                                =================================== --}}
                                <td>

                                    <span
                                        class="mode-badge {{ $modeClass }}"
                                    >

                                        <i class="fas {{ $modeIcon }}"></i>

                                        {{ $modeLabel }}

                                    </span>

                                </td>


                                {{-- ==================================
                                     PAYEUR
                                =================================== --}}
                                <td>

                                    @if($paiement->payePar)

                                        @php

                                            $nomPayeur =
                                                $paiement->payePar->name
                                                ?? $paiement->payePar->nom
                                                ?? 'Utilisateur';

                                        @endphp

                                        <div class="payer-cell">

                                            <i class="fas fa-user-shield"></i>

                                            <span class="payer-name">
                                                {{ $nomPayeur }}
                                            </span>

                                        </div>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- ==================================
                                     ACTIONS
                                =================================== --}}
                                <td>

                                    <div class="row-actions">

                                        {{-- VOIR --}}
                                        <a
                                            href="{{ route(
                                                'admin.paiements-salaires.show',
                                                $paiement
                                            ) }}"
                                            class="action-btn view"
                                            title="Voir le paiement"
                                            aria-label="Voir le paiement {{ $paiement->reference }}"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </a>


                                        {{-- REÇU --}}
                                        <a
                                            href="{{ route(
                                                'admin.paiements-salaires.recu',
                                                $paiement
                                            ) }}"
                                            class="action-btn receipt"
                                            title="Voir le reçu"
                                            aria-label="Voir le reçu {{ $paiement->reference }}"
                                        >
                                            <i class="fas fa-receipt"></i>
                                        </a>


                                        {{-- IMPRESSION --}}
                                        <a
                                            href="{{ route(
                                                'admin.paiements-salaires.imprimer',
                                                $paiement
                                            ) }}"
                                            class="action-btn print"
                                            title="Imprimer le paiement"
                                            aria-label="Imprimer le paiement {{ $paiement->reference }}"
                                            target="_blank"
                                        >
                                            <i class="fas fa-print"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>


                    {{-- ==============================================
                         TOTAL DE LA PAGE
                    =============================================== --}}

                    <tfoot>

                        @php
                            $totalPage = $paiements->sum('montant');
                        @endphp

                        <tr>

                            <td colspan="4">
                                <strong>
                                    Total des paiements affichés
                                </strong>
                            </td>

                            <td class="text-right">

                                <strong class="payment-amount">

                                    {{ number_format(
                                        $totalPage,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    <span class="payment-currency">
                                        USD
                                    </span>

                                </strong>

                            </td>

                            <td colspan="4"></td>

                        </tr>

                    </tfoot>

                </table>

            @else

                {{-- ==============================================
                     ÉTAT VIDE
                =============================================== --}}

                <div class="empty-state">

                    <div class="empty-state-icon">

                        <i class="fas fa-receipt"></i>

                    </div>

                    <h3 class="empty-state-title">
                        Aucun paiement trouvé
                    </h3>

                    <p class="empty-state-text">

                        @if($hasFilters)

                            Aucun paiement ne correspond aux critères
                            de recherche sélectionnés.

                        @else

                            Aucun paiement de salaire n'a encore été enregistré.

                        @endif

                    </p>

                    @if($hasFilters)

                        <a
                            href="{{ route('admin.paiements-salaires.index') }}"
                            class="btn btn-outline"
                        >

                            <i class="fas fa-times"></i>

                            Réinitialiser les filtres

                        </a>

                    @else

                        <a
                            href="{{ route('admin.salaires.index') }}"
                            class="btn btn-primary"
                        >

                            <i class="fas fa-file-invoice-dollar"></i>

                            Consulter les salaires

                        </a>

                    @endif

                </div>

            @endif

        </div>


        {{-- ====================================================
             PAGINATION
        ===================================================== --}}

        @if($paiements->hasPages())

            <div class="pagination-wrapper">

                <div class="pagination-info">

                    Affichage de
                    <strong>{{ $paiements->firstItem() }}</strong>
                    à
                    <strong>{{ $paiements->lastItem() }}</strong>
                    sur
                    <strong>{{ $paiements->total() }}</strong>
                    paiements

                </div>

                <nav
                    class="pagination"
                    aria-label="Pagination des paiements"
                >

                    @if($paiements->onFirstPage())

                        <span class="disabled" aria-disabled="true">
                            <i class="fas fa-chevron-left"></i>
                        </span>

                    @else

                        <a
                            href="{{ $paiements->previousPageUrl() }}"
                            aria-label="Page précédente"
                        >
                            <i class="fas fa-chevron-left"></i>
                        </a>

                    @endif


                    @foreach($paiements->getUrlRange(
                        max(1, $paiements->currentPage() - 2),
                        min($paiements->lastPage(), $paiements->currentPage() + 2)
                    ) as $page => $url)

                        @if($page == $paiements->currentPage())

                            <span
                                class="active"
                                aria-current="page"
                            >
                                {{ $page }}
                            </span>

                        @else

                            <a href="{{ $url }}">
                                {{ $page }}
                            </a>

                        @endif

                    @endforeach


                    @if($paiements->hasMorePages())

                        <a
                            href="{{ $paiements->nextPageUrl() }}"
                            aria-label="Page suivante"
                        >
                            <i class="fas fa-chevron-right"></i>
                        </a>

                    @else

                        <span class="disabled" aria-disabled="true">
                            <i class="fas fa-chevron-right"></i>
                        </span>

                    @endif

                </nav>

            </div>

        @endif

    </section>


    {{-- ====================================================
         RÉPARTITION DES MODES DE PAIEMENT
    ===================================================== --}}

    <section class="mode-distribution">

        {{-- ESPÈCES --}}
        @php
            $pourcentageEspeces =
                $montantTotal > 0
                ? min(100, ($montantEspeces / $montantTotal) * 100)
                : 0;
        @endphp

        <article class="mode-card especes">

            <div class="mode-card-header">

                <span class="mode-card-name">

                    <i class="fas fa-money-bill"></i>

                    Espèces

                </span>

                <strong class="mode-card-value">

                    {{ number_format(
                        $montantEspeces,
                        2,
                        ',',
                        ' '
                    ) }}
                    USD

                </strong>

            </div>

            <div
                class="mode-progress"
                role="progressbar"
                aria-valuenow="{{ round($pourcentageEspeces, 1) }}"
                aria-valuemin="0"
                aria-valuemax="100"
            >

                <div
                    class="mode-progress-bar"
                    style="width: {{ $pourcentageEspeces }}%;"
                ></div>

            </div>

        </article>


        {{-- VIREMENT --}}
        @php
            $pourcentageVirement =
                $montantTotal > 0
                ? min(100, ($montantVirement / $montantTotal) * 100)
                : 0;
        @endphp

        <article class="mode-card virement">

            <div class="mode-card-header">

                <span class="mode-card-name">

                    <i class="fas fa-university"></i>

                    Virement bancaire

                </span>

                <strong class="mode-card-value">

                    {{ number_format(
                        $montantVirement,
                        2,
                        ',',
                        ' '
                    ) }}
                    USD

                </strong>

            </div>

            <div
                class="mode-progress"
                role="progressbar"
                aria-valuenow="{{ round($pourcentageVirement, 1) }}"
                aria-valuemin="0"
                aria-valuemax="100"
            >

                <div
                    class="mode-progress-bar"
                    style="width: {{ $pourcentageVirement }}%;"
                ></div>

            </div>

        </article>


        {{-- MOBILE MONEY --}}
        @php
            $pourcentageMobileMoney =
                $montantTotal > 0
                ? min(100, ($montantMobileMoney / $montantTotal) * 100)
                : 0;
        @endphp

        <article class="mode-card mobile-money">

            <div class="mode-card-header">

                <span class="mode-card-name">

                    <i class="fas fa-mobile-alt"></i>

                    Mobile Money

                </span>

                <strong class="mode-card-value">

                    {{ number_format(
                        $montantMobileMoney,
                        2,
                        ',',
                        ' '
                    ) }}
                    USD

                </strong>

            </div>

            <div
                class="mode-progress"
                role="progressbar"
                aria-valuenow="{{ round($pourcentageMobileMoney, 1) }}"
                aria-valuemin="0"
                aria-valuemax="100"
            >

                <div
                    class="mode-progress-bar"
                    style="width: {{ $pourcentageMobileMoney }}%;"
                ></div>

            </div>

        </article>


        {{-- CHÈQUE --}}
        @php
            $pourcentageCheque =
                $montantTotal > 0
                ? min(100, ($montantCheque / $montantTotal) * 100)
                : 0;
        @endphp

        <article class="mode-card cheque">

            <div class="mode-card-header">

                <span class="mode-card-name">

                    <i class="fas fa-file-invoice"></i>

                    Chèque

                </span>

                <strong class="mode-card-value">

                    {{ number_format(
                        $montantCheque,
                        2,
                        ',',
                        ' '
                    ) }}
                    USD

                </strong>

            </div>

            <div
                class="mode-progress"
                role="progressbar"
                aria-valuenow="{{ round($pourcentageCheque, 1) }}"
                aria-valuemin="0"
                aria-valuemax="100"
            >

                <div
                    class="mode-progress-bar"
                    style="width: {{ $pourcentageCheque }}%;"
                ></div>

            </div>

        </article>

    </section>

</div>
```

</div>

@endsection

{{-- ============================================================
JAVASCRIPT SPÉCIFIQUE À LA PAGE
============================================================ --}}
@push('scripts') <script src="{{ asset('assets/js/admin/paiements-salaires/index.js') }}"></script>
@endpush
