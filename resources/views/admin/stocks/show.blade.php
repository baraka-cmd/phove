@extends('layouts.admin')

@section('title', $stock->designation)

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/show.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-show-page">

    {{-- =========================================================
         EN-TÊTE DE LA PAGE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-text">

                {{-- FIL D'ARIANE --}}
                <div class="breadcrumb">

                    <a href="{{ route('admin.stocks.index') }}">
                        Stocks
                    </a>

                    <span class="breadcrumb-separator">
                        /
                    </span>

                    <span>
                        {{ $stock->designation }}
                    </span>

                </div>


                {{-- TITRE + STATUT --}}
                <div class="page-title-row">

                    <div>

                        <h1 class="page-title">
                            {{ $stock->designation }}
                        </h1>

                        <p class="page-subtitle">
                            Référence :
                            <strong>
                                {{ $stock->reference }}
                            </strong>
                        </p>

                    </div>


                    {{-- STATUT ARTICLE --}}
                    <div class="stock-status-wrapper">

                        @php
                            $statutValue = $stock->statut instanceof \App\Enums\StatutStock
                                ? $stock->statut->value
                                : (string) $stock->statut;
                        @endphp

                        @switch($statutValue)

                            @case('actif')

                                <span class="status-badge status-active">
                                    <span class="status-dot"></span>
                                    {{ $stock->statut->libelle() }}
                                </span>

                                @break


                            @case('inactif')

                                <span class="status-badge status-inactive">
                                    <span class="status-dot"></span>
                                    {{ $stock->statut->libelle() }}
                                </span>

                                @break


                            @case('archive')

                                <span class="status-badge status-archived">
                                    <span class="status-dot"></span>
                                    {{ $stock->statut->libelle() }}
                                </span>

                                @break

                        @endswitch

                    </div>

                </div>

            </div>


            {{-- ACTIONS PRINCIPALES --}}
            <div class="page-header-actions">

                @can('update', $stock)

                    <a
                        href="{{ route('admin.stocks.edit', $stock) }}"
                        class="btn btn-secondary"
                    >

                        <i class="fas fa-edit"></i>

                        <span>
                            Modifier
                        </span>

                    </a>

                @endcan


                @can('archive', $stock)

                    @if($statutValue !== 'archive')

                        <form
                            action="{{ route('admin.stocks.archive', $stock) }}"
                            method="POST"
                            class="archive-form"
                            data-confirm-archive
                        >

                            @csrf

                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-danger"
                            >

                                <i class="fas fa-box-archive"></i>

                                <span>
                                    Archiver
                                </span>

                            </button>

                        </form>

                    @endif

                @endcan


                <a
                    href="{{ route('admin.stocks.index') }}"
                    class="btn btn-light"
                >

                    <i class="fas fa-arrow-left"></i>

                    <span>
                        Retour
                    </span>

                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ALERTES
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success">

            <div class="alert-icon">
                <i class="fas fa-circle-check"></i>
            </div>

            <div class="alert-content">
                {{ session('success') }}
            </div>

            <button
                type="button"
                class="alert-close"
                data-dismiss-alert
                aria-label="Fermer"
            >

                <i class="fas fa-xmark"></i>

            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">

            <div class="alert-icon">
                <i class="fas fa-circle-exclamation"></i>
            </div>

            <div class="alert-content">
                {{ session('error') }}
            </div>

            <button
                type="button"
                class="alert-close"
                data-dismiss-alert
                aria-label="Fermer"
            >

                <i class="fas fa-xmark"></i>

            </button>

        </div>

    @endif


    {{-- =========================================================
         CARTES DE QUANTITÉS
         ------------------------------------------------
         $quantites est fourni par StockController::show()
         via StockService::quantites($stock).

         Clés attendues :
           - physique
           - reservee
           - disponible
           - seuil_alerte
           - en_rupture
           - sous_seuil
           - est_disponible
    ========================================================== --}}
    <section class="stock-overview">

        {{-- STOCK PHYSIQUE --}}
        <article class="stock-kpi-card">

            <div class="stock-kpi-icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Stock physique
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format(
                        (float) $quantites['physique'],
                        2,
                        ',',
                        ' '
                    ) }}
                </strong>

                <span class="stock-kpi-unit">
                    {{ $stock->unite }}
                </span>

            </div>

        </article>


        {{-- STOCK RÉSERVÉ --}}
        <article class="stock-kpi-card">

            <div class="stock-kpi-icon">
                <i class="fas fa-lock"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Stock réservé
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format(
                        (float) $quantites['reservee'],
                        2,
                        ',',
                        ' '
                    ) }}
                </strong>

                <span class="stock-kpi-unit">
                    {{ $stock->unite }}
                </span>

            </div>

        </article>


        {{-- STOCK DISPONIBLE --}}
        <article class="stock-kpi-card stock-kpi-card-highlight">

            <div class="stock-kpi-icon">
                <i class="fas fa-circle-check"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Stock disponible
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format(
                        (float) $quantites['disponible'],
                        2,
                        ',',
                        ' '
                    ) }}
                </strong>

                <span class="stock-kpi-unit">
                    {{ $stock->unite }}
                </span>

            </div>

        </article>


        {{-- SEUIL --}}
        <article class="stock-kpi-card">

            <div class="stock-kpi-icon">
                <i class="fas fa-triangle-exclamation"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Seuil d'alerte
                </span>

                <strong class="stock-kpi-value">

                    @if($quantites['seuil_alerte'] !== null)

                        {{ number_format(
                            (float) $quantites['seuil_alerte'],
                            2,
                            ',',
                            ' '
                        ) }}

                    @else

                        —

                    @endif

                </strong>

                <span class="stock-kpi-unit">
                    {{ $stock->unite }}
                </span>

            </div>

        </article>

    </section>


    {{-- =========================================================
         CONTENU PRINCIPAL
    ========================================================== --}}
    <div class="stock-show-grid">

        {{-- =====================================================
             INFORMATIONS DE L'ARTICLE
        ====================================================== --}}
        <section class="card stock-information-card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Informations de l'article
                    </h2>

                    <p class="card-subtitle">
                        Données générales du produit stocké.
                    </p>
                </div>

                <div class="card-header-icon">
                    <i class="fas fa-box"></i>
                </div>

            </div>


            <div class="card-body">

                <div class="information-grid">

                    {{-- RÉFÉRENCE --}}
                    <div class="information-item">

                        <span class="information-label">
                            Référence
                        </span>

                        <strong class="information-value information-reference">
                            {{ $stock->reference }}
                        </strong>

                    </div>


                    {{-- DÉSIGNATION --}}
                    <div class="information-item">

                        <span class="information-label">
                            Désignation
                        </span>

                        <strong class="information-value">
                            {{ $stock->designation }}
                        </strong>

                    </div>


                    {{-- CATÉGORIE --}}
                    <div class="information-item">

                        <span class="information-label">
                            Catégorie
                        </span>

                        <span class="information-value">
                            {{ ucfirst((string) $stock->categorie) }}
                        </span>

                    </div>


                    {{-- UNITÉ --}}
                    <div class="information-item">

                        <span class="information-label">
                            Unité de mesure
                        </span>

                        <span class="information-value">
                            {{ $stock->unite }}
                        </span>

                    </div>


                    {{-- EMPLACEMENT --}}
                    <div class="information-item">

                        <span class="information-label">
                            Emplacement principal
                        </span>

                        <span class="information-value">

                            @if($stock->emplacementPrincipal)

                                <strong>
                                    {{ $stock->emplacementPrincipal->nom }}
                                </strong>

                                @if($stock->emplacementPrincipal->code)

                                    <small class="information-secondary">
                                        {{ $stock->emplacementPrincipal->code }}
                                    </small>

                                @endif

                            @else

                                <span class="empty-value">
                                    Non défini
                                </span>

                            @endif

                        </span>

                    </div>


                    {{-- SEUIL --}}
                    <div class="information-item">

                        <span class="information-label">
                            Seuil d'alerte
                        </span>

                        <span class="information-value">

                            @if($quantites['seuil_alerte'] !== null)

                                {{ number_format(
                                    (float) $quantites['seuil_alerte'],
                                    2,
                                    ',',
                                    ' '
                                ) }}
                                {{ $stock->unite }}

                            @else

                                <span class="empty-value">
                                    Non défini
                                </span>

                            @endif

                        </span>

                    </div>


                    {{-- CRÉATION --}}
                    <div class="information-item">

                        <span class="information-label">
                            Créé le
                        </span>

                        <span class="information-value">

                            @if($stock->created_at)
                                {{ $stock->created_at->format('d/m/Y à H:i') }}
                            @else
                                —
                            @endif

                        </span>

                    </div>


                    {{-- MODIFICATION --}}
                    <div class="information-item">

                        <span class="information-label">
                            Dernière modification
                        </span>

                        <span class="information-value">

                            @if($stock->updated_at)
                                {{ $stock->updated_at->format('d/m/Y à H:i') }}
                            @else
                                —
                            @endif

                        </span>

                    </div>

                </div>


                {{-- DESCRIPTION --}}
                @if($stock->description)

                    <div class="description-section">

                        <h3 class="section-mini-title">
                            Description
                        </h3>

                        <div class="description-content">
                            {!! nl2br(e($stock->description)) !!}
                        </div>

                    </div>

                @endif

            </div>

        </section>


        {{-- =====================================================
             ÉTAT DU STOCK
        ====================================================== --}}
        <section class="card stock-state-card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        État du stock
                    </h2>

                    <p class="card-subtitle">
                        Situation actuelle de l'article.
                    </p>
                </div>

                <div class="card-header-icon">
                    <i class="fas fa-chart-simple"></i>
                </div>

            </div>


            <div class="card-body">

                @php
                    $physique   = (float) $quantites['physique'];
                    $reserve    = (float) $quantites['reservee'];
                    $disponible = (float) $quantites['disponible'];
                    $seuil      = $quantites['seuil_alerte'] !== null
                        ? (float) $quantites['seuil_alerte']
                        : null;

                    $pourcentage = $seuil && $seuil > 0
                        ? min(100, max(0, ($disponible / $seuil) * 100))
                        : null;
                @endphp

                <div class="stock-state-summary">

                    <div class="state-main-value">
                        <strong>
                            {{ number_format($disponible, 2, ',', ' ') }}
                        </strong>

                        <span>
                            {{ $stock->unite }} disponibles
                        </span>
                    </div>


                    @if($seuil !== null)

                        @if($disponible <= 0)

                            <span class="availability-badge availability-danger">
                                <i class="fas fa-circle-xmark"></i>
                                Rupture
                            </span>

                        @elseif($disponible <= $seuil)

                            <span class="availability-badge availability-warning">
                                <i class="fas fa-triangle-exclamation"></i>
                                Stock faible
                            </span>

                        @else

                            <span class="availability-badge availability-success">
                                <i class="fas fa-circle-check"></i>
                                Stock disponible
                            </span>

                        @endif

                    @else

                        <span class="availability-badge availability-neutral">
                            <i class="fas fa-circle-info"></i>
                            Seuil non défini
                        </span>

                    @endif

                </div>


                {{-- BARRE DE DISPONIBILITÉ --}}
                @if($seuil !== null && $seuil > 0)

                    <div class="stock-progress-section">

                        <div class="progress-header">

                            <span>
                                Niveau disponible
                            </span>

                            <strong>
                                {{ number_format($pourcentage, 0) }} %
                            </strong>

                        </div>

                        <div class="progress-bar">

                            <div
                                class="progress-fill"
                                style="width: {{ $pourcentage }}%;"
                                data-progress="{{ $pourcentage }}"
                            ></div>

                        </div>

                        <div class="progress-footer">

                            <span>
                                0
                            </span>

                            <span>
                                Seuil :
                                {{ number_format($seuil, 2, ',', ' ') }}
                                {{ $stock->unite }}
                            </span>

                        </div>

                    </div>

                @endif


                {{-- RÉCAPITULATIF --}}
                <div class="stock-calculation">

                    <div class="calculation-row">

                        <span>
                            Stock physique
                        </span>

                        <strong>
                            {{ number_format($physique, 2, ',', ' ') }}
                            {{ $stock->unite }}
                        </strong>

                    </div>

                    <div class="calculation-row calculation-row-minus">

                        <span>
                            Stock réservé
                        </span>

                        <strong>
                            −
                            {{ number_format($reserve, 2, ',', ' ') }}
                            {{ $stock->unite }}
                        </strong>

                    </div>

                    <div class="calculation-divider"></div>

                    <div class="calculation-row calculation-row-total">

                        <span>
                            Stock disponible
                        </span>

                        <strong>
                            {{ number_format($disponible, 2, ',', ' ') }}
                            {{ $stock->unite }}
                        </strong>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             LOTS
        ====================================================== --}}
        <section class="card stock-lots-card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Lots
                    </h2>

                    <p class="card-subtitle">
                        Lots associés à cet article et état de leur stock.
                    </p>
                </div>

                <div class="card-header-actions">

                    <span class="count-badge">
                        {{ $stock->lots->count() }}
                    </span>

                </div>

            </div>


            <div class="card-body card-body-table">

                @if($stock->lots->count())

                    <div class="responsive-table">

                        <table class="data-table stock-lots-table">

                            <thead>
                                <tr>

                                    <th>
                                        Lot
                                    </th>

                                    <th>
                                        Entrée
                                    </th>

                                    <th>
                                        Expiration
                                    </th>

                                    <th>
                                        Quantité
                                    </th>

                                    <th>
                                        Statut
                                    </th>

                                    <th class="text-right">
                                        Action
                                    </th>

                                </tr>
                            </thead>

                            <tbody>

                                @foreach($stock->lots->sortBy(function ($lot) {
                                    return $lot->date_expiration ?? '9999-12-31';
                                }) as $lot)

                                    @php
                                        $lotStatut = $lot->statut instanceof \App\Enums\StatutLot
                                            ? $lot->statut->value
                                            : $lot->statut;
                                    @endphp

                                    <tr>

                                        <td>

                                            <a
                                                href="{{ route('admin.stocks.lots.show', $lot) }}"
                                                class="table-primary-link"
                                            >

                                                {{ $lot->numero_lot ?? ('LOT-' . $lot->id) }}

                                            </a>

                                        </td>


                                        <td>
                                            {{ $lot->date_entree
                                                ? \Carbon\Carbon::parse($lot->date_entree)->format('d/m/Y')
                                                : '—'
                                            }}
                                        </td>


                                        <td>

                                            @if($lot->date_expiration)

                                                @php
                                                    $expiration = \Carbon\Carbon::parse($lot->date_expiration);
                                                    $joursRestants = now()->startOfDay()->diffInDays($expiration, false);
                                                @endphp

                                                <span class="expiration-value
                                                    @if($joursRestants < 0)
                                                        expiration-expired
                                                    @elseif($joursRestants <= 30)
                                                        expiration-warning
                                                    @else
                                                        expiration-normal
                                                    @endif">

                                                    {{ $expiration->format('d/m/Y') }}

                                                </span>

                                            @else
                                                —
                                            @endif

                                        </td>


                                        <td>

                                            <strong>
                                                {{ number_format((float) $lot->quantite_actuelle, 2, ',', ' ') }}
                                            </strong>

                                            <span class="table-unit">
                                                {{ $stock->unite }}
                                            </span>

                                        </td>


                                        <td>

                                            @if($lotStatut === 'actif')

                                                <span class="table-status status-active">
                                                    Actif
                                                </span>

                                            @elseif($lotStatut === 'expire')

                                                <span class="table-status status-danger">
                                                    Expiré
                                                </span>

                                            @elseif($lotStatut === 'bloque')

                                                <span class="table-status status-warning">
                                                    Bloqué
                                                </span>

                                            @elseif($lotStatut === 'epuise')

                                                <span class="table-status status-neutral">
                                                    Épuisé
                                                </span>

                                            @else

                                                <span class="table-status status-neutral">
                                                    {{ ucfirst((string) $lotStatut) }}
                                                </span>

                                            @endif

                                        </td>


                                        <td class="text-right">

                                            <a
                                                href="{{ route('admin.stocks.lots.show', $lot) }}"
                                                class="table-action-button"
                                                title="Voir le lot"
                                                aria-label="Voir le lot"
                                            >

                                                <i class="fas fa-eye"></i>

                                            </a>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="empty-state">

                        <div class="empty-state-icon">
                            <i class="fas fa-box-open"></i>
                        </div>

                        <h3>
                            Aucun lot enregistré
                        </h3>

                        <p>
                            Aucun lot n'est actuellement associé à cet article.
                        </p>

                    </div>

                @endif

            </div>

        </section>


        {{-- =====================================================
             RÉSERVATIONS ACTIVES
        ====================================================== --}}
        <section class="card stock-reservations-card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Réservations
                    </h2>

                    <p class="card-subtitle">
                        Réservations actives concernant cet article.
                    </p>
                </div>

                <div class="card-header-icon">
                    <i class="fas fa-lock"></i>
                </div>

            </div>


            <div class="card-body card-body-table">

                @php
                    $reservations = collect();

                    if ($stock->relationLoaded('lignesReservations')) {
                        $reservations = $stock->lignesReservations
                            ->map(fn ($ligne) => $ligne->reservation)
                            ->filter()
                            ->unique('id')
                            ->values();
                    }
                @endphp

                @if($reservations->count())

                    <div class="responsive-table">

                        <table class="data-table">

                            <thead>
                                <tr>

                                    <th>
                                        Numéro
                                    </th>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Projet
                                    </th>

                                    <th>
                                        Quantité réservée
                                    </th>

                                    <th>
                                        Statut
                                    </th>

                                    <th class="text-right">
                                        Action
                                    </th>

                                </tr>
                            </thead>

                            <tbody>

                                @foreach($reservations as $reservation)

                                    @php
                                        $reservationStatut = $reservation->statut instanceof \App\Enums\StatutReservation
                                            ? $reservation->statut->value
                                            : $reservation->statut;
                                    @endphp

                                    <tr>

                                        <td>

                                            <a
                                                href="{{ route('admin.stocks.reservations.show', $reservation) }}"
                                                class="table-primary-link"
                                            >

                                                {{ $reservation->numero }}

                                            </a>

                                        </td>

                                        <td>
                                            {{ $reservation->date_reservation
                                                ? \Carbon\Carbon::parse($reservation->date_reservation)->format('d/m/Y')
                                                : '—'
                                            }}
                                        </td>

                                        <td>

                                            @if($reservation->projet)
                                                {{ $reservation->projet->nom ?? '—' }}
                                            @else
                                                —
                                            @endif

                                        </td>

                                        <td>

                                            @php
                                                $ligneReservation = $reservation->lignes
                                                    ? $reservation->lignes->firstWhere('stock_id', $stock->id)
                                                    : null;
                                            @endphp

                                            @if($ligneReservation)

                                                <strong>
                                                    {{ number_format(
                                                        (float) $ligneReservation->quantite_reservee,
                                                        2,
                                                        ',',
                                                        ' '
                                                    ) }}
                                                </strong>

                                                <span class="table-unit">
                                                    {{ $stock->unite }}
                                                </span>

                                            @else
                                                —
                                            @endif

                                        </td>

                                        <td>

                                            @switch($reservationStatut)

                                                @case('brouillon')
                                                    <span class="table-status status-neutral">
                                                        Brouillon
                                                    </span>
                                                    @break

                                                @case('confirmee')
                                                    <span class="table-status status-active">
                                                        Confirmée
                                                    </span>
                                                    @break

                                                @case('partielle')
                                                    <span class="table-status status-warning">
                                                        Partielle
                                                    </span>
                                                    @break

                                                @case('consommee')
                                                    <span class="table-status status-neutral">
                                                        Consommée
                                                    </span>
                                                    @break

                                                @case('expiree')
                                                    <span class="table-status status-danger">
                                                        Expirée
                                                    </span>
                                                    @break

                                                @case('annulee')
                                                    <span class="table-status status-danger">
                                                        Annulée
                                                    </span>
                                                    @break

                                            @endswitch

                                        </td>

                                        <td class="text-right">

                                            <a
                                                href="{{ route('admin.stocks.reservations.show', $reservation) }}"
                                                class="table-action-button"
                                                title="Voir la réservation"
                                                aria-label="Voir la réservation"
                                            >

                                                <i class="fas fa-eye"></i>

                                            </a>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="empty-state empty-state-small">

                        <div class="empty-state-icon">
                            <i class="fas fa-lock-open"></i>
                        </div>

                        <h3>
                            Aucune réservation
                        </h3>

                        <p>
                            Cet article n'est associé à aucune réservation active.
                        </p>

                    </div>

                @endif

            </div>

        </section>


        {{-- =====================================================
             DERNIERS MOUVEMENTS
        ====================================================== --}}
        <section class="card stock-movements-card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Derniers mouvements
                    </h2>

                    <p class="card-subtitle">
                        Historique récent des opérations de stock.
                    </p>

                </div>

                <div class="card-header-actions">

                    <a
                        href="{{ route(
                            'admin.stocks.mouvements.index',
                            ['stock_id' => $stock->id]
                        ) }}"
                        class="btn btn-small btn-light"
                    >

                        Voir tout

                        <i class="fas fa-arrow-right"></i>

                    </a>

                </div>

            </div>


            <div class="card-body card-body-table">

                @if($stock->relationLoaded('mouvements') && $stock->mouvements->count())

                    <div class="responsive-table">

                        <table class="data-table movements-table">

                            <thead>

                                <tr>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Type
                                    </th>

                                    <th>
                                        Nature
                                    </th>

                                    <th>
                                        Quantité
                                    </th>

                                    <th>
                                        Référence
                                    </th>

                                    <th>
                                        Utilisateur
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach($stock->mouvements
                                    ->sortByDesc('date_mouvement')
                                    ->take(10) as $mouvement)

                                    @php
                                        $typeMouvement = $mouvement->type instanceof \App\Enums\TypeMouvement
                                            ? $mouvement->type->value
                                            : $mouvement->type;

                                        $natureMouvement = $mouvement->nature instanceof \App\Enums\NatureMouvement
                                            ? $mouvement->nature->value
                                            : $mouvement->nature;
                                    @endphp

                                    <tr>

                                        <td>

                                            {{ $mouvement->date_mouvement
                                                ? \Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m/Y')
                                                : '—'
                                            }}

                                        </td>

                                        <td>

                                            @if($typeMouvement === 'entree')

                                                <span class="movement-type movement-entry">
                                                    <i class="fas fa-arrow-down"></i>
                                                    Entrée
                                                </span>

                                            @elseif($typeMouvement === 'sortie')

                                                <span class="movement-type movement-exit">
                                                    <i class="fas fa-arrow-up"></i>
                                                    Sortie
                                                </span>

                                            @elseif($typeMouvement === 'transfert')

                                                <span class="movement-type movement-transfer">
                                                    <i class="fas fa-right-left"></i>
                                                    Transfert
                                                </span>

                                            @elseif($typeMouvement === 'ajustement')

                                                <span class="movement-type movement-adjustment">
                                                    <i class="fas fa-sliders"></i>
                                                    Ajustement
                                                </span>

                                            @else

                                                <span class="movement-type">
                                                    {{ ucfirst((string) $typeMouvement) }}
                                                </span>

                                            @endif

                                        </td>

                                        <td>
                                            {{ ucfirst(str_replace('_', ' ', (string) $natureMouvement)) }}
                                        </td>

                                        <td>

                                            <strong>
                                                {{ number_format(
                                                    abs((float) $mouvement->quantite),
                                                    2,
                                                    ',',
                                                    ' '
                                                ) }}
                                            </strong>

                                            <span class="table-unit">
                                                {{ $stock->unite }}
                                            </span>

                                        </td>

                                        <td>

                                            @if($mouvement->reference_document)

                                                <span class="document-reference">
                                                    {{ $mouvement->reference_document }}
                                                </span>

                                            @else
                                                —
                                            @endif

                                        </td>

                                        <td>

                                            @if($mouvement->utilisateur)
                                                {{ $mouvement->utilisateur->name }}
                                            @else
                                                —
                                            @endif

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <div class="empty-state empty-state-small">

                        <div class="empty-state-icon">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>

                        <h3>
                            Aucun mouvement
                        </h3>

                        <p>
                            Aucun mouvement n'est actuellement enregistré
                            pour cet article.
                        </p>

                    </div>

                @endif

            </div>

        </section>


        {{-- =====================================================
             DESCRIPTION (bas de page)
        ====================================================== --}}
        @if($stock->description)

            <section class="card stock-description-card">

                <div class="card-header">

                    <div>
                        <h2 class="card-title">
                            Description
                        </h2>

                        <p class="card-subtitle">
                            Informations complémentaires sur l'article.
                        </p>
                    </div>

                    <div class="card-header-icon">
                        <i class="fas fa-align-left"></i>
                    </div>

                </div>

                <div class="card-body">

                    <div class="long-description">
                        {!! nl2br(e($stock->description)) !!}
                    </div>

                </div>

            </section>

        @endif

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/show.js') }}"
        defer>
    </script>

@endsection