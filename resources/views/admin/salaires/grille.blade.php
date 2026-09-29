@extends('layouts.admin')

@section('title', 'Grille mensuelle des salaires')

{{-- ============================================================
CSS SPÉCIFIQUE À LA PAGE
============================================================ --}}
@push('styles') <link
     rel="stylesheet"
     href="{{ asset('assets/css/admin/salaires/grille.css') }}"
 >
@endpush

@section('content')

<div
    class="salary-grid"
    data-current-month="{{ $mois ?? request('mois') ?? now()->format('Y-m') }}"
    data-month="{{ $mois ?? request('mois') ?? now()->format('Y-m') }}"
>


{{-- ========================================================
     EN-TÊTE DE PAGE
========================================================= --}}
<header class="page-header">

    <div class="page-header-content">

        <div>
            <h1 class="page-title">
                <i class="fas fa-table" aria-hidden="true"></i>
                Grille mensuelle des salaires
            </h1>

            <p class="page-subtitle">
                Vue synthétique des salaires et de leur situation
                de paiement pour le mois sélectionné.
            </p>
        </div>

        <div class="page-actions">

            <a
                href="{{ route('admin.salaires.index') }}"
                class="btn btn-outline"
            >
                <i class="fas fa-list" aria-hidden="true"></i>
                Liste des salaires
            </a>

            <a
                href="{{ route('admin.salaires.grille', [
                    'mois' => $mois ?? request('mois') ?? now()->format('Y-m')
                ]) }}"
                class="btn btn-secondary"
                data-refresh
            >
                <i class="fas fa-sync-alt" aria-hidden="true"></i>
                Actualiser
            </a>

            <button
                type="button"
                class="btn btn-outline"
                data-print
            >
                <i class="fas fa-print" aria-hidden="true"></i>
                Imprimer
            </button>

            @can('create', App\Models\Salaire::class)

                <a
                    href="{{ route('admin.salaires.create') }}"
                    class="btn btn-primary"
                >
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    Nouveau salaire
                </a>

            @endcan

        </div>

    </div>

</header>


{{-- ========================================================
     MESSAGES
========================================================= --}}

@if(session('success'))

    <div
        class="alert alert-success"
        role="alert"
    >
        <i class="fas fa-check-circle" aria-hidden="true"></i>

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
        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>

        <span>
            {{ session('error') }}
        </span>
    </div>

@endif


{{-- ========================================================
     SÉLECTION DU MOIS
========================================================= --}}
<section class="month-selector">

    <form
        method="GET"
        action="{{ route('admin.salaires.grille') }}"
        data-month-form
    >

        <div class="month-selector-label">

            <i class="fas fa-calendar-alt" aria-hidden="true"></i>

            <label for="mois">
                Mois de la grille
            </label>

        </div>

        <input
            type="month"
            id="mois"
            name="mois"
            value="{{ $mois ?? request('mois') ?? now()->format('Y-m') }}"
            data-month-input
            required
            aria-label="Sélectionner le mois de la grille"
        >

        <button
            type="submit"
            class="btn btn-primary"
            data-month-submit
            data-loading-text="Chargement..."
        >
            <i class="fas fa-search" aria-hidden="true"></i>
            Afficher la grille
        </button>

    </form>

</section>


{{-- ========================================================
     BANNIÈRE DE PÉRIODE
========================================================= --}}
@php
    $moisGrille = $mois
        ?? request('mois')
        ?? now()->format('Y-m');

    try {
        $dateGrille = \Carbon\Carbon::createFromFormat(
            'Y-m',
            $moisGrille
        );
    } catch (\Throwable $e) {
        $moisGrille = now()->format('Y-m');

        $dateGrille = \Carbon\Carbon::createFromFormat(
            'Y-m',
            $moisGrille
        );
    }

    $isCurrentMonth = $moisGrille === now()->format('Y-m');

    $nombreSalaires = $totalSalaires ?? $salaires->count();

    $masseSalariale = (float) ($totalNet ?? 0);

    $montantPaye = (float) ($totalPaye ?? 0);

    $montantRestant = max(
        (float) ($resteAPayer ?? ($masseSalariale - $montantPaye)),
        0
    );

    $tauxPaiement = $masseSalariale > 0
        ? min(
            ($montantPaye / $masseSalariale) * 100,
            100
        )
        : 0;
@endphp


<section class="period-banner">

    <div class="period-information">

        <div class="period-icon">
            <i class="fas fa-calendar-check" aria-hidden="true"></i>
        </div>

        <div class="period-content">

            <span class="period-label">
                Période sélectionnée
            </span>

            <strong class="period-value">
                {{ $dateGrille->translatedFormat('F Y') }}
            </strong>

            <span class="period-description">
                @if($isCurrentMonth)
                    Mois courant — situation salariale actuelle.
                @else
                    Consultation de la grille salariale pour la période sélectionnée.
                @endif
            </span>

        </div>

    </div>

</section>


{{-- ========================================================
     RÉSUMÉ DU MOIS
========================================================= --}}
<section class="summary-grid">

    {{-- Nombre de salaires --}}
    <article class="summary-card">

        <div class="summary-icon">
            <i class="fas fa-users" aria-hidden="true"></i>
        </div>

        <div class="summary-content">

            <span class="summary-label">
                Nombre de salaires
            </span>

            <strong class="summary-value">
                {{ number_format($nombreSalaires, 0, ',', ' ') }}
            </strong>

            <span class="summary-meta">
                salaire(s) enregistré(s)
            </span>

        </div>

    </article>


    {{-- Masse salariale --}}
    <article class="summary-card">

        <div class="summary-icon">
            <i class="fas fa-coins" aria-hidden="true"></i>
        </div>

        <div class="summary-content">

            <span class="summary-label">
                Masse salariale
            </span>

            <strong class="summary-value">
                {{ number_format($masseSalariale, 2, ',', ' ') }}
            </strong>

            <span class="summary-meta">
                montant net total
            </span>

        </div>

    </article>


    {{-- Total payé --}}
    <article class="summary-card">

        <div class="summary-icon">
            <i class="fas fa-money-bill-wave" aria-hidden="true"></i>
        </div>

        <div class="summary-content">

            <span class="summary-label">
                Total payé
            </span>

            <strong class="summary-value">
                {{ number_format($montantPaye, 2, ',', ' ') }}
            </strong>

            <span class="summary-meta">
                {{ number_format($tauxPaiement, 0, ',', ' ') }} %
                de la masse salariale
            </span>

        </div>

    </article>


    {{-- Reste à payer --}}
    <article class="summary-card">

        <div class="summary-icon">
            <i class="fas fa-hourglass-half" aria-hidden="true"></i>
        </div>

        <div class="summary-content">

            <span class="summary-label">
                Reste à payer
            </span>

            <strong class="summary-value">
                {{ number_format($montantRestant, 2, ',', ' ') }}
            </strong>

            <span class="summary-meta">
                montant encore dû
            </span>

        </div>

    </article>

</section>


{{-- ========================================================
     GRILLE SALARIALE
========================================================= --}}
<section class="grid-card">

    {{-- En-tête de la grille --}}
    <div class="grid-card-header">

        <div>

            <h2 class="grid-card-title">

                <i
                    class="fas fa-money-check-alt"
                    aria-hidden="true"
                ></i>

                Salaires du
                {{ $dateGrille->translatedFormat('F Y') }}

            </h2>

            <p class="grid-card-subtitle">

                {{ $salaires->count() }}
                salaire(s) affiché(s) pour cette période.

            </p>

        </div>


        <div class="grid-card-actions">

            <a
                href="{{ route('admin.salaires.index', [
                    'mois' => $moisGrille
                ]) }}"
                class="btn btn-sm btn-outline"
            >
                <i class="fas fa-list" aria-hidden="true"></i>
                Voir la liste
            </a>

        </div>

    </div>


    {{-- ====================================================
         CONTENU
    ===================================================== --}}

    @if($salaires->count())

        <div class="table-wrapper">

            <table class="salary-grid-table">

                <caption class="text-muted">
                    Grille des salaires pour
                    {{ $dateGrille->translatedFormat('F Y') }}
                </caption>

                <thead>

                    <tr>

                        <th scope="col">
                            N°
                        </th>

                        <th scope="col">
                            Membre
                        </th>

                        <th scope="col">
                            Organe
                        </th>

                        <th scope="col">
                            Mois
                        </th>

                        <th scope="col">
                            Référence
                        </th>

                        <th
                            scope="col"
                            class="text-right"
                        >
                            Salaire de base
                        </th>

                        <th
                            scope="col"
                            class="text-right"
                        >
                            Primes
                        </th>

                        <th
                            scope="col"
                            class="text-right"
                        >
                            Retenues
                        </th>

                        <th
                            scope="col"
                            class="text-right"
                        >
                            Net
                        </th>

                        <th
                            scope="col"
                            class="text-right"
                        >
                            Payé
                        </th>

                        <th
                            scope="col"
                            class="text-right"
                        >
                            Reste
                        </th>

                        <th scope="col">
                            Paiement
                        </th>

                        <th scope="col">
                            Statut
                        </th>

                        <th
                            scope="col"
                            class="text-right"
                        >
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($salaires as $index => $salaire)

                        @php

                            $salaireBase = (float) (
                                $salaire->salaire_base ?? 0
                            );

                            $primes = (float) (
                                $salaire->primes ?? 0
                            );

                            $retenues = (float) (
                                $salaire->retenues ?? 0
                            );

                            $net = (float) (
                                $salaire->salaire_net ?? 0
                            );

                            $totalPayeLigne = (float) (
                                $salaire->paiements_sum_montant ?? 0
                            );

                            $reste = max(
                                $net - $totalPayeLigne,
                                0
                            );

                            $pourcentage = $net > 0
                                ? min(
                                    ($totalPayeLigne / $net) * 100,
                                    100
                                )
                                : 0;

                            $membreNom = $salaire->membre
                                ? (
                                    $salaire->membre->nom_complet
                                    ?? trim(
                                        ($salaire->membre->nom ?? '') .
                                        ' ' .
                                        ($salaire->membre->post_nom ?? '') .
                                        ' ' .
                                        ($salaire->membre->prenom ?? '')
                                    )
                                )
                                : 'Membre supprimé';

                            $initiales = '';

                            if ($salaire->membre) {
                                $parts = preg_split(
                                    '/\s+/',
                                    trim($membreNom)
                                );

                                foreach (array_slice($parts, 0, 2) as $part) {
                                    if ($part !== '') {
                                        $initiales .= mb_strtoupper(
                                            mb_substr($part, 0, 1)
                                        );
                                    }
                                }
                            }

                            $progressClass = $pourcentage >= 100
                                ? 'is-complete'
                                : ($pourcentage > 0
                                    ? 'is-partial'
                                    : '');

                        @endphp


                        <tr
                            data-salary-row
                            data-selectable-row
                            data-salary-id="{{ $salaire->id }}"
                            data-month="{{ $moisGrille }}"
                            tabindex="0"
                            aria-label="Salaire de {{ $membreNom }}"
                        >

                            {{-- N° --}}
                            <td>
                                {{ $index + 1 }}
                            </td>


                            {{-- Membre --}}
                            <td>

                                <div class="member-cell">

                                    <div
                                        class="member-avatar"
                                        aria-hidden="true"
                                    >
                                        @if($salaire->membre && !empty($salaire->membre->photo))
                                            <img
                                                src="{{ asset('storage/' . $salaire->membre->photo) }}"
                                                alt=""
                                            >
                                        @else
                                            {{ $initiales ?: '?' }}
                                        @endif
                                    </div>

                                    <div class="member-info">

                                        @if($salaire->membre)

                                            <a
                                                href="{{ route(
                                                    'admin.salaires.show',
                                                    $salaire
                                                ) }}"
                                                class="member-name"
                                            >
                                                {{ $membreNom }}
                                            </a>

                                            @if(!empty($salaire->membre->matricule))

                                                <span class="member-role">
                                                    {{ $salaire->membre->matricule }}
                                                </span>

                                            @endif

                                        @else

                                            <span class="member-name">
                                                Membre supprimé
                                            </span>

                                            <span class="member-role">
                                                —
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Organe --}}
                            <td>

                                <div class="organe-cell">

                                    @if($salaire->organe)

                                        <span class="organe-name">
                                            {{ $salaire->organe->nom }}
                                        </span>

                                    @else

                                        <span class="organe-empty">
                                            —
                                        </span>

                                    @endif

                                </div>

                            </td>


                            {{-- Mois --}}
                            <td>

                                <div class="month-cell">

                                    <span class="month-value">
                                        {{ $dateGrille->translatedFormat('M Y') }}
                                    </span>

                                </div>

                            </td>


                            {{-- Référence --}}
                            <td>

                                <a
                                    href="{{ route(
                                        'admin.salaires.show',
                                        $salaire
                                    ) }}"
                                    class="month-value"
                                >
                                    {{ $salaire->reference }}
                                </a>

                            </td>


                            {{-- Salaire de base --}}
                            <td class="text-right">

                                <span class="amount amount-base">
                                    {{ number_format(
                                        $salaireBase,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </span>

                            </td>


                            {{-- Primes --}}
                            <td class="text-right">

                                <span class="amount amount-primes">
                                    {{ number_format(
                                        $primes,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </span>

                            </td>


                            {{-- Retenues --}}
                            <td class="text-right">

                                <span class="amount amount-retenues">
                                    {{ number_format(
                                        $retenues,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </span>

                            </td>


                            {{-- Net --}}
                            <td class="text-right">

                                <strong class="amount amount-net">
                                    {{ number_format(
                                        $net,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </strong>

                            </td>


                            {{-- Payé --}}
                            <td class="text-right">

                                <span class="amount amount-paid">
                                    {{ number_format(
                                        $totalPayeLigne,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </span>

                            </td>


                            {{-- Reste --}}
                            <td class="text-right">

                                @if($reste > 0)

                                    <span class="amount amount-rest">
                                        {{ number_format(
                                            $reste,
                                            2,
                                            ',',
                                            ' '
                                        ) }}
                                    </span>

                                @else

                                    <span class="amount amount-rest is-zero">
                                        0,00
                                    </span>

                                @endif

                            </td>


                            {{-- Progression du paiement --}}
                            <td>

                                <div class="payment-progress">

                                    <div class="payment-progress-top">

                                        <span class="payment-progress-label">
                                            Paiement
                                        </span>

                                        <span class="payment-progress-value">
                                            {{ number_format(
                                                $pourcentage,
                                                0,
                                                ',',
                                                ' '
                                            ) }} %
                                        </span>

                                    </div>

                                    <div
                                        class="payment-progress-bar"
                                        aria-hidden="true"
                                    >
                                        <span
                                            class="payment-progress-fill {{ $progressClass }}"
                                            data-progress="{{ $pourcentage }}"
                                            style="width: {{ $pourcentage }}%;"
                                        ></span>
                                    </div>

                                </div>

                            </td>


                            {{-- Statut --}}
                            <td>

                                @switch($salaire->statut)

                                    @case('brouillon')

                                        <span class="badge badge-neutral">
                                            <i
                                                class="fas fa-pencil-alt"
                                                aria-hidden="true"
                                            ></i>
                                            Brouillon
                                        </span>

                                        @break


                                    @case('valide')

                                        <span class="badge badge-info">
                                            <i
                                                class="fas fa-check"
                                                aria-hidden="true"
                                            ></i>
                                            Validé
                                        </span>

                                        @break


                                    @case('partiellement_paye')

                                        <span class="badge badge-warning">
                                            <i
                                                class="fas fa-hourglass-half"
                                                aria-hidden="true"
                                            ></i>
                                            Partiellement payé
                                        </span>

                                        @break


                                    @case('paye')

                                        <span class="badge badge-success">
                                            <i
                                                class="fas fa-check-double"
                                                aria-hidden="true"
                                            ></i>
                                            Payé
                                        </span>

                                        @break


                                    @case('annule')

                                        <span class="badge badge-danger">
                                            <i
                                                class="fas fa-ban"
                                                aria-hidden="true"
                                            ></i>
                                            Annulé
                                        </span>

                                        @break


                                    @default

                                        <span class="badge badge-neutral">
                                            {{ $salaire->statut ?? 'Inconnu' }}
                                        </span>

                                @endswitch

                            </td>


                            {{-- Action --}}
                            <td class="text-right">

                                <a
                                    href="{{ route(
                                        'admin.salaires.show',
                                        $salaire
                                    ) }}"
                                    class="btn btn-sm btn-outline"
                                    title="Voir le salaire"
                                    aria-label="Voir le salaire de {{ $membreNom }}"
                                >
                                    <i
                                        class="fas fa-eye"
                                        aria-hidden="true"
                                    ></i>
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>


                {{-- =================================================
                     TOTAL
                ================================================== --}}
                <tfoot>

                    @php

                        $totalBase = $salaires->sum(
                            fn ($salaire) =>
                                (float) ($salaire->salaire_base ?? 0)
                        );

                        $totalPrimes = $salaires->sum(
                            fn ($salaire) =>
                                (float) ($salaire->primes ?? 0)
                        );

                        $totalRetenues = $salaires->sum(
                            fn ($salaire) =>
                                (float) ($salaire->retenues ?? 0)
                        );

                        $totalNetGrille = $salaires->sum(
                            fn ($salaire) =>
                                (float) ($salaire->salaire_net ?? 0)
                        );

                        $totalPayeGrille = $salaires->sum(
                            fn ($salaire) =>
                                (float) ($salaire->paiements_sum_montant ?? 0)
                        );

                        $totalResteGrille = $salaires->sum(
                            fn ($salaire) =>
                                max(
                                    (float) ($salaire->salaire_net ?? 0)
                                    -
                                    (float) ($salaire->paiements_sum_montant ?? 0),
                                    0
                                )
                        );

                    @endphp

                    <tr class="total-row">

                        <th
                            colspan="5"
                            class="total-label"
                        >
                            TOTAL
                        </th>

                        <th class="text-right total-value">
                            {{ number_format(
                                $totalBase,
                                2,
                                ',',
                                ' '
                            ) }}
                        </th>

                        <th class="text-right total-value">
                            {{ number_format(
                                $totalPrimes,
                                2,
                                ',',
                                ' '
                            ) }}
                        </th>

                        <th class="text-right total-value">
                            {{ number_format(
                                $totalRetenues,
                                2,
                                ',',
                                ' '
                            ) }}
                        </th>

                        <th class="text-right total-value">
                            {{ number_format(
                                $totalNetGrille,
                                2,
                                ',',
                                ' '
                            ) }}
                        </th>

                        <th class="text-right total-paid">
                            {{ number_format(
                                $totalPayeGrille,
                                2,
                                ',',
                                ' '
                            ) }}
                        </th>

                        <th class="text-right total-rest {{ $totalResteGrille <= 0 ? 'is-zero' : '' }}">
                            {{ number_format(
                                $totalResteGrille,
                                2,
                                ',',
                                ' '
                            ) }}
                        </th>

                        <th colspan="3"></th>

                    </tr>

                </tfoot>

            </table>

        </div>


        {{-- ====================================================
             PIED DE GRILLE
        ===================================================== --}}
        <div class="grid-card-footer">

            <div class="grid-footer-info">

                <i
                    class="fas fa-info-circle"
                    aria-hidden="true"
                ></i>

                <span>
                    Les montants affichés correspondent à la période
                    sélectionnée.
                </span>

            </div>

        </div>


        {{-- ====================================================
             PAGINATION
        ===================================================== --}}
        @if(method_exists($salaires, 'links'))

            <div class="pagination-wrapper">

                <div class="pagination-info">

                    @if($salaires->total() > 0)

                        Affichage de
                        <strong>{{ $salaires->firstItem() }}</strong>
                        à
                        <strong>{{ $salaires->lastItem() }}</strong>
                        sur
                        <strong>{{ $salaires->total() }}</strong>
                        salaire(s)

                    @endif

                </div>

                <nav aria-label="Pagination des salaires">
                    {{ $salaires->appends([
                        'mois' => $moisGrille
                    ])->links() }}
                </nav>

            </div>

        @endif


    @else

        {{-- ==================================================
             ÉTAT VIDE
        =================================================== --}}
        <div class="empty-state">

            <div
                class="empty-state-icon"
                aria-hidden="true"
            >
                <i class="fas fa-calendar-times"></i>
            </div>

            <h3 class="empty-state-title">
                Aucun salaire pour cette période
            </h3>

            <p class="empty-state-text">
                Aucun salaire n'a été enregistré pour le mois
                sélectionné.
            </p>

            @can('create', App\Models\Salaire::class)

                <a
                    href="{{ route('admin.salaires.create') }}"
                    class="btn btn-primary"
                >
                    <i
                        class="fas fa-plus"
                        aria-hidden="true"
                    ></i>
                    Créer un salaire
                </a>

            @endcan

        </div>

    @endif

</section>


</div>

@endsection

{{-- ============================================================
JAVASCRIPT SPÉCIFIQUE À LA PAGE
============================================================ --}}
@push('scripts') <script
     src="{{ asset('assets/js/admin/salaires/grille.js') }}"
 ></script>
@endpush
