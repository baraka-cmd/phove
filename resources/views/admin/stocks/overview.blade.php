@extends('layouts.admin')

@section('title', 'Vue d’ensemble des stocks')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/overview.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-overview-page">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-text">

                <div class="page-header-icon">
                    <i class="fas fa-warehouse"></i>
                </div>

                <div>
                    <h1 class="page-title">
                        Vue d’ensemble des stocks
                    </h1>

                    <p class="page-subtitle">
                        Synthèse générale du stock : articles, alertes,
                        mouvements récents, réservations et inventaires.
                    </p>
                </div>

            </div>

            <div class="page-header-actions">

                @can('create', App\Models\Stock::class)

                    <a
                        href="{{ route('admin.stocks.create') }}"
                        class="btn-primary"
                    >
                        <i class="fas fa-plus"></i>
                        <span>Nouvel article</span>
                    </a>

                @endcan

                @can('create', App\Models\BonSortie::class)

                    <a
                        href="{{ route('admin.stocks.sorties.create') }}"
                        class="btn-secondary"
                    >
                        <i class="fas fa-arrow-up"></i>
                        <span>Nouvelle sortie</span>
                    </a>

                @endcan

            </div>

        </div>

    </div>


    {{-- =========================================================
         KPI PRINCIPAUX
    ========================================================== --}}
    <section
        class="overview-kpi-grid"
        aria-label="Indicateurs clés du stock"
    >

        {{-- ARTICLES ACTIFS --}}
        <article class="overview-kpi-card">

            <div class="overview-kpi-icon">
                <i class="fas fa-box"></i>
            </div>

            <div class="overview-kpi-content">

                <span class="overview-kpi-label">
                    Articles actifs
                </span>

                <strong class="overview-kpi-value">
                    {{ number_format($stats['articles_actifs'] ?? 0, 0, ',', ' ') }}
                </strong>

                <a
                    href="{{ route('admin.stocks.index') }}"
                    class="overview-kpi-link"
                >
                    Voir tous les articles
                    <i class="fas fa-arrow-right"></i>
                </a>

            </div>

        </article>


        {{-- RUPTURES --}}
        <article class="overview-kpi-card overview-kpi-danger">

            <div class="overview-kpi-icon">
                <i class="fas fa-circle-xmark"></i>
            </div>

            <div class="overview-kpi-content">

                <span class="overview-kpi-label">
                    Ruptures
                </span>

                <strong class="overview-kpi-value">
                    {{ number_format($stats['ruptures'] ?? 0, 0, ',', ' ') }}
                </strong>

                @if(($stats['ruptures'] ?? 0) > 0)
                    <span class="overview-kpi-alert">
                        Articles à réapprovisionner
                    </span>
                @endif

            </div>

        </article>


        {{-- STOCK FAIBLE --}}
        <article class="overview-kpi-card overview-kpi-warning">

            <div class="overview-kpi-icon">
                <i class="fas fa-triangle-exclamation"></i>
            </div>

            <div class="overview-kpi-content">

                <span class="overview-kpi-label">
                    Stock faible
                </span>

                <strong class="overview-kpi-value">
                    {{ number_format($stats['stocks_faibles'] ?? 0, 0, ',', ' ') }}
                </strong>

                @if(($stats['stocks_faibles'] ?? 0) > 0)
                    <span class="overview-kpi-alert">
                        Sous le seuil d'alerte
                    </span>
                @endif

            </div>

        </article>


        {{-- QUANTITÉ PHYSIQUE --}}
        <article class="overview-kpi-card overview-kpi-success">

            <div class="overview-kpi-icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>

            <div class="overview-kpi-content">

                <span class="overview-kpi-label">
                    Quantité physique
                </span>

                <strong class="overview-kpi-value">
                    {{ number_format(
                        (float) ($stats['quantite_physique'] ?? 0),
                        2,
                        ',',
                        ' '
                    ) }}
                </strong>

                <span class="overview-kpi-unit">
                    toutes unités confondues
                </span>

            </div>

        </article>

    </section>


    {{-- =========================================================
         LIGNE 1 — DERNIERS MOUVEMENTS + LOTS À EXPIRER
    ========================================================== --}}
    <div class="overview-grid-2">

        {{-- DERNIERS MOUVEMENTS --}}
        <section class="overview-card">

            <div class="overview-card-header">

                <div class="overview-card-title">
                    <i class="fas fa-exchange-alt"></i>
                    <div>
                        <h2>Derniers mouvements</h2>
                        <p>Les 10 dernières opérations de stock.</p>
                    </div>
                </div>

                <a
                    href="{{ route('admin.stocks.mouvements.index') }}"
                    class="overview-card-link"
                >
                    Voir tout
                    <i class="fas fa-arrow-right"></i>
                </a>

            </div>


            <div class="overview-card-body">

                @if($derniersMouvements->count() > 0)

                    <div class="overview-list">

                        @foreach($derniersMouvements as $mouvement)

                            @php
                                $type = $mouvement->type instanceof \App\Enums\TypeMouvement
                                    ? $mouvement->type->value
                                    : (string) $mouvement->type;

                                $isEntree = $type === 'entree';
                                $isSortie = $type === 'sortie';
                                $isAjust  = $type === 'ajustement';

                                $sens = (int) ($mouvement->sens ?? 1);
                            @endphp

                            <div class="overview-list-item">

                                <div class="overview-list-icon
                                    @if($isEntree) is-entree
                                    @elseif($isSortie) is-sortie
                                    @elseif($isAjust) is-ajustement
                                    @endif"
                                >
                                    <i class="fas
                                        @if($isEntree) fa-arrow-down
                                        @elseif($isSortie) fa-arrow-up
                                        @else fa-sliders-h
                                        @endif"
                                    ></i>
                                </div>

                                <div class="overview-list-content">

                                    <strong>
                                        {{ $mouvement->stock?->designation ?? 'Article supprimé' }}
                                    </strong>

                                    <span>
                                        {{ $mouvement->date_mouvement
                                            ? \Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m/Y')
                                            : '—'
                                        }}

                                        @if($mouvement->utilisateur)
                                            · {{ $mouvement->utilisateur->nom }}
                                        @endif
                                    </span>

                                </div>

                                <div class="overview-list-value
                                    @if($isEntree) is-positive
                                    @elseif($isSortie) is-negative
                                    @endif"
                                >
                                    @if($sens > 0) +@endif{{ number_format((float) $mouvement->quantite * $sens, 2, ',', ' ') }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="overview-empty">
                        <i class="fas fa-inbox"></i>
                        <p>Aucun mouvement enregistré.</p>
                    </div>

                @endif

            </div>

        </section>


        {{-- LOTS À EXPIRER --}}
        <section class="overview-card">

            <div class="overview-card-header">

                <div class="overview-card-title">
                    <i class="fas fa-calendar-times"></i>
                    <div>
                        <h2>Lots à expirer</h2>
                        <p>Lots expirant dans les 30 prochains jours.</p>
                    </div>
                </div>

                <a
                    href="{{ route('admin.stocks.lots.expiration') }}"
                    class="overview-card-link"
                >
                    Voir tout
                    <i class="fas fa-arrow-right"></i>
                </a>

            </div>


            <div class="overview-card-body">

                @if($lotsExpirantBientot->count() > 0)

                    <div class="overview-list">

                        @foreach($lotsExpirantBientot as $lot)

                            @php
                                $joursRestants = $lot->date_expiration
                                    ? now()->startOfDay()->diffInDays(
                                        \Carbon\Carbon::parse($lot->date_expiration)->startOfDay(),
                                        false
                                    )
                                    : null;
                            @endphp

                            <div class="overview-list-item">

                                <div class="overview-list-icon is-warning">
                                    <i class="fas fa-clock"></i>
                                </div>

                                <div class="overview-list-content">

                                    <strong>
                                        {{ $lot->stock?->designation ?? 'Article supprimé' }}
                                    </strong>

                                    <span>
                                        Lot {{ $lot->numero_lot ?? ('#' . $lot->id) }}

                                        @if($lot->date_expiration)
                                            · Exp. {{ \Carbon\Carbon::parse($lot->date_expiration)->format('d/m/Y') }}
                                        @endif
                                    </span>

                                </div>

                                <div class="overview-list-value
                                    @if($joursRestants !== null && $joursRestants <= 7) is-danger
                                    @else is-warning
                                    @endif"
                                >
                                    @if($joursRestants !== null)
                                        {{ $joursRestants }} j
                                    @endif
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="overview-empty">
                        <i class="fas fa-calendar-check"></i>
                        <p>Aucun lot proche de l'expiration.</p>
                    </div>

                @endif

            </div>

        </section>

    </div>


    {{-- =========================================================
         LIGNE 2 — RÉSERVATIONS ACTIVES + INVENTAIRES EN COURS
    ========================================================== --}}
    <div class="overview-grid-2">

        {{-- RÉSERVATIONS ACTIVES --}}
        <section class="overview-card">

            <div class="overview-card-header">

                <div class="overview-card-title">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <h2>Réservations actives</h2>
                        <p>Réservations confirmées ou partielles.</p>
                    </div>
                </div>

                <a
                    href="{{ route('admin.stocks.reservations.index') }}"
                    class="overview-card-link"
                >
                    Voir tout
                    <i class="fas fa-arrow-right"></i>
                </a>

            </div>


            <div class="overview-card-body">

                @if($reservationsActives->count() > 0)

                    <div class="overview-list">

                        @foreach($reservationsActives as $reservation)

                            <div class="overview-list-item">

                                <div class="overview-list-icon is-info">
                                    <i class="fas fa-lock"></i>
                                </div>

                                <div class="overview-list-content">

                                    <strong>
                                        {{ $reservation->numero }}
                                    </strong>

                                    <span>
                                        {{ $reservation->destinataire ?: 'Sans destinataire' }}

                                        @if($reservation->date_reservation)
                                            · {{ \Carbon\Carbon::parse($reservation->date_reservation)->format('d/m/Y') }}
                                        @endif
                                    </span>

                                </div>

                                <div class="overview-list-value is-info">
                                    {{ $reservation->lignes_count ?? 0 }} article(s)
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="overview-empty">
                        <i class="fas fa-check-circle"></i>
                        <p>Aucune réservation active.</p>
                    </div>

                @endif

            </div>

        </section>


        {{-- INVENTAIRES EN COURS --}}
        <section class="overview-card">

            <div class="overview-card-header">

                <div class="overview-card-title">
                    <i class="fas fa-clipboard-list"></i>
                    <div>
                        <h2>Inventaires en cours</h2>
                        <p>Inventaires non finalisés.</p>
                    </div>
                </div>

                <a
                    href="{{ route('admin.stocks.inventaires.index') }}"
                    class="overview-card-link"
                >
                    Voir tout
                    <i class="fas fa-arrow-right"></i>
                </a>

            </div>


            <div class="overview-card-body">

                @if($inventairesEnCours->count() > 0)

                    <div class="overview-list">

                        @foreach($inventairesEnCours as $inventaire)

                            <div class="overview-list-item">

                                <div class="overview-list-icon is-warning">
                                    <i class="fas fa-clipboard"></i>
                                </div>

                                <div class="overview-list-content">

                                    <strong>
                                        {{ $inventaire->numero }}
                                    </strong>

                                    <span>
                                        {{ $inventaire->emplacement?->nom ?? 'Tous les emplacements' }}

                                        @if($inventaire->date_debut)
                                            · {{ \Carbon\Carbon::parse($inventaire->date_debut)->format('d/m/Y') }}
                                        @endif
                                    </span>

                                </div>

                                <div class="overview-list-value is-warning">
                                    {{ $inventaire->lignes_count ?? 0 }} ligne(s)
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="overview-empty">
                        <i class="fas fa-check-circle"></i>
                        <p>Aucun inventaire en cours.</p>
                    </div>

                @endif

            </div>

        </section>

    </div>


    {{-- =========================================================
         ACCÈS RAPIDES
    ========================================================== --}}
    <section class="overview-card">

        <div class="overview-card-header">

            <div class="overview-card-title">
                <i class="fas fa-bolt"></i>
                <div>
                    <h2>Accès rapides</h2>
                    <p>Naviguez vers les modules les plus utilisés.</p>
                </div>
            </div>

        </div>


        <div class="overview-card-body">

            <div class="overview-shortcuts">

                <a
                    href="{{ route('admin.stocks.entrees.index') }}"
                    class="overview-shortcut"
                >
                    <i class="fas fa-arrow-down"></i>
                    <span>Entrées</span>
                </a>

                <a
                    href="{{ route('admin.stocks.sorties.index') }}"
                    class="overview-shortcut"
                >
                    <i class="fas fa-arrow-up"></i>
                    <span>Sorties</span>
                </a>

                <a
                    href="{{ route('admin.stocks.reservations.index') }}"
                    class="overview-shortcut"
                >
                    <i class="fas fa-calendar-check"></i>
                    <span>Réservations</span>
                </a>

                <a
                    href="{{ route('admin.stocks.lots.index') }}"
                    class="overview-shortcut"
                >
                    <i class="fas fa-layer-group"></i>
                    <span>Lots</span>
                </a>

                <a
                    href="{{ route('admin.stocks.inventaires.index') }}"
                    class="overview-shortcut"
                >
                    <i class="fas fa-clipboard-list"></i>
                    <span>Inventaires</span>
                </a>

                <a
                    href="{{ route('admin.stocks.mouvements.index') }}"
                    class="overview-shortcut"
                >
                    <i class="fas fa-exchange-alt"></i>
                    <span>Mouvements</span>
                </a>

            </div>

        </div>

    </section>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/overview.js') }}"
        defer
    ></script>

@endsection