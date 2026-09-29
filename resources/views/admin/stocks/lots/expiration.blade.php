@extends('layouts.admin')

@section('title', 'Lots — Expiration')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/lots-expiration.css') }}"
    >
@endsection

@section('content')

<div class="stock-lots-expiration-page">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="stock-lots-expiration-page__header">

        <div class="stock-lots-expiration-page__header-main">

            <nav
                class="stock-lots-expiration-page__breadcrumb"
                aria-label="Fil d’Ariane"
            >

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span class="stock-lots-expiration-page__breadcrumb-separator">
                    /
                </span>

                <a href="{{ route('admin.stocks.lots.index') }}">
                    Lots
                </a>

                <span class="stock-lots-expiration-page__breadcrumb-separator">
                    /
                </span>

                <span aria-current="page">
                    Expiration
                </span>

            </nav>

            <div class="stock-lots-expiration-page__title-row">

                <div>

                    <h1 class="stock-lots-expiration-page__title">
                        Lots à expiration
                    </h1>

                    <p class="stock-lots-expiration-page__subtitle">
                        Surveillez les lots proches de leur date d’expiration
                        et sécurisez leur traitement.
                    </p>

                </div>

            </div>

        </div>


        <div class="stock-lots-expiration-page__header-actions">

            <a
                href="{{ route('admin.stocks.lots.index') }}"
                class="stock-lots-expiration-page__button stock-lots-expiration-page__button--secondary"
            >

                <i class="fas fa-boxes-stacked" aria-hidden="true"></i>

                <span>Tous les lots</span>

            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES FLASH
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-lots-expiration-page__alert stock-lots-expiration-page__alert--success" role="alert">

            <div class="stock-lots-expiration-page__alert-icon">
                <i class="fas fa-check" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-expiration-page__alert-content">

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-lots-expiration-page__alert stock-lots-expiration-page__alert--danger" role="alert">

            <div class="stock-lots-expiration-page__alert-icon">
                <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-expiration-page__alert-content">

                <strong>Opération impossible</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    {{-- =========================================================
         INDICATEURS
    ========================================================== --}}
    <div class="stock-lots-expiration-page__stats">

        {{-- LOTS CONCERNÉS --}}
        <div class="stock-lots-expiration-page__stat-card">

            <div class="stock-lots-expiration-page__stat-icon">
                <i class="fas fa-calendar-xmark" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-expiration-page__stat-content">

                <span class="stock-lots-expiration-page__stat-label">
                    Lots concernés
                </span>

                <strong class="stock-lots-expiration-page__stat-value">
                    {{ number_format($lots->total(), 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- EXPIRATION PROCHE --}}
        <div class="stock-lots-expiration-page__stat-card">

            <div class="stock-lots-expiration-page__stat-icon stock-lots-expiration-page__stat-icon--warning">
                <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-expiration-page__stat-content">

                <span class="stock-lots-expiration-page__stat-label">
                    Expiration proche
                </span>

                <strong class="stock-lots-expiration-page__stat-value">

                    {{ number_format(
                        $lots->getCollection()->filter(function ($lot) {
                            return $lot->date_expiration
                                && now()->diffInDays($lot->date_expiration, false) >= 0
                                && now()->diffInDays($lot->date_expiration, false) <= 30;
                        })->count(),
                        0,
                        ',',
                        ' '
                    ) }}

                </strong>

                <small class="stock-lots-expiration-page__stat-help">
                    30 prochains jours
                </small>

            </div>

        </div>


        {{-- DÉJÀ EXPIRÉS --}}
        <div class="stock-lots-expiration-page__stat-card">

            <div class="stock-lots-expiration-page__stat-icon stock-lots-expiration-page__stat-icon--danger">
                <i class="fas fa-calendar-minus" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-expiration-page__stat-content">

                <span class="stock-lots-expiration-page__stat-label">
                    Déjà expirés
                </span>

                <strong class="stock-lots-expiration-page__stat-value">

                    {{ number_format(
                        $lots->getCollection()->filter(function ($lot) {
                            return $lot->date_expiration
                                && \Carbon\Carbon::parse($lot->date_expiration)->isPast();
                        })->count(),
                        0,
                        ',',
                        ' '
                    ) }}

                </strong>

            </div>

        </div>


        {{-- QUANTITÉ RESTANTE --}}
        <div class="stock-lots-expiration-page__stat-card">

            <div class="stock-lots-expiration-page__stat-icon stock-lots-expiration-page__stat-icon--success">
                <i class="fas fa-boxes-stacked" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-expiration-page__stat-content">

                <span class="stock-lots-expiration-page__stat-label">
                    Quantité restante
                </span>

                <strong class="stock-lots-expiration-page__stat-value">

                    {{ number_format(
                        (float) $lots->getCollection()->sum('quantite_actuelle'),
                        2,
                        ',',
                        ' '
                    ) }}

                </strong>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}
    <section class="stock-lots-expiration-page__card">

        <div class="stock-lots-expiration-page__card-header">

            <div>

                <h2 class="stock-lots-expiration-page__card-title">
                    Filtrer les lots
                </h2>

                <p class="stock-lots-expiration-page__card-description">
                    Affinez la liste selon la période d’expiration.
                </p>

            </div>

            <div class="stock-lots-expiration-page__card-icon">
                <i class="fas fa-filter" aria-hidden="true"></i>
            </div>

        </div>


        <div class="stock-lots-expiration-page__card-body">

            <form
                method="GET"
                action="{{ route('admin.stocks.lots.expiration') }}"
                class="stock-lots-expiration-page__filter-form"
            >

                {{-- PÉRIODE --}}
                <div class="stock-lots-expiration-page__filter-group">

                    <label for="periode" class="stock-lots-expiration-page__filter-label">
                        Période
                    </label>

                    <select
                        id="periode"
                        name="periode"
                        class="stock-lots-expiration-page__filter-input"
                    >

                        <option value="30" @selected($periode === '30')>
                            30 prochains jours
                        </option>

                        <option value="60" @selected($periode === '60')>
                            60 prochains jours
                        </option>

                        <option value="90" @selected($periode === '90')>
                            90 prochains jours
                        </option>

                        <option value="expired" @selected($periode === 'expired')>
                            Déjà expirés
                        </option>

                        <option value="all" @selected($periode === 'all')>
                            Tous les lots concernés
                        </option>

                    </select>

                </div>


                {{-- RECHERCHE --}}
                <div class="stock-lots-expiration-page__filter-group">

                    <label for="recherche" class="stock-lots-expiration-page__filter-label">
                        Recherche
                    </label>

                    <div class="stock-lots-expiration-page__search-wrapper">

                        <i class="fas fa-search" aria-hidden="true"></i>

                        <input
                            type="search"
                            id="recherche"
                            name="recherche"
                            value="{{ request('recherche') }}"
                            class="stock-lots-expiration-page__filter-input stock-lots-expiration-page__filter-input--search"
                            placeholder="N° lot, article, référence..."
                            autocomplete="off"
                        >

                    </div>

                </div>


                {{-- ACTIONS --}}
                <div class="stock-lots-expiration-page__filter-actions">

                    <button
                        type="submit"
                        class="stock-lots-expiration-page__button stock-lots-expiration-page__button--primary"
                    >

                        <i class="fas fa-filter" aria-hidden="true"></i>

                        <span>Filtrer</span>

                    </button>

                    <a
                        href="{{ route('admin.stocks.lots.expiration') }}"
                        class="stock-lots-expiration-page__button stock-lots-expiration-page__button--secondary"
                    >

                        <i class="fas fa-rotate-left" aria-hidden="true"></i>

                        <span>Réinitialiser</span>

                    </a>

                </div>

            </form>

        </div>

    </section>


    {{-- =========================================================
         TABLEAU
    ========================================================== --}}
    <section class="stock-lots-expiration-page__card">

        <div class="stock-lots-expiration-page__card-header">

            <div>

                <h2 class="stock-lots-expiration-page__card-title">
                    Surveillance des expirations
                </h2>

                <p class="stock-lots-expiration-page__card-description">
                    Les lots sont affichés par ordre de date d’expiration.
                </p>

            </div>

            <span class="stock-lots-expiration-page__count">

                {{ $lots->total() }}
                {{ $lots->total() > 1 ? 'lots' : 'lot' }}

            </span>

        </div>


        @if($lots->count())

            <div class="stock-lots-expiration-page__table-wrapper">

                <table class="stock-lots-expiration-page__table">

                    <thead>

                        <tr>

                            <th scope="col">Lot</th>
                            <th scope="col">Article</th>
                            <th scope="col">Expiration</th>
                            <th scope="col">Quantité</th>
                            <th scope="col">État</th>
                            <th scope="col">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($lots as $lot)

                            @php
                                $dateExpiration = $lot->date_expiration
                                    ? \Carbon\Carbon::parse($lot->date_expiration)
                                    : null;

                                $joursRestants = $dateExpiration
                                    ? now()->startOfDay()->diffInDays(
                                        $dateExpiration->startOfDay(),
                                        false
                                    )
                                    : null;

                                $estExpire = $dateExpiration && $joursRestants < 0;

                                $expirationProche =
                                    $dateExpiration
                                    && !$estExpire
                                    && $joursRestants <= 30;

                                $quantite = (float) $lot->quantite_actuelle;
                            @endphp

                            <tr>

                                {{-- LOT --}}
                                <td>

                                    <div class="stock-lots-expiration-page__lot">

                                        <div class="stock-lots-expiration-page__lot-icon">
                                            <i class="fas fa-box" aria-hidden="true"></i>
                                        </div>

                                        <div class="stock-lots-expiration-page__lot-content">

                                            <a
                                                href="{{ route('admin.stocks.lots.show', $lot) }}"
                                                class="stock-lots-expiration-page__lot-number"
                                            >
                                                {{ $lot->numero_lot ?? ('LOT-' . $lot->id) }}
                                            </a>

                                        </div>

                                    </div>

                                </td>


                                {{-- ARTICLE --}}
                                <td>

                                    @if($lot->stock)

                                        <div class="stock-lots-expiration-page__article">

                                            <span class="stock-lots-expiration-page__article-name">
                                                {{ $lot->stock->designation }}
                                            </span>

                                            @if($lot->stock->reference)

                                                <span class="stock-lots-expiration-page__article-reference">
                                                    {{ $lot->stock->reference }}
                                                </span>

                                            @endif

                                        </div>

                                    @else

                                        <span class="stock-lots-expiration-page__muted">
                                            Article introuvable
                                        </span>

                                    @endif

                                </td>


                                {{-- DATE EXPIRATION --}}
                                <td>

                                    @if($dateExpiration)

                                        <div class="stock-lots-expiration-page__expiration">

                                            <strong>
                                                {{ $dateExpiration->format('d/m/Y') }}
                                            </strong>

                                            @if($estExpire)

                                                <span class="stock-lots-expiration-page__expiration-detail stock-lots-expiration-page__expiration-detail--danger">

                                                    Expiré depuis
                                                    {{ abs($joursRestants) }}
                                                    {{ abs($joursRestants) > 1 ? 'jours' : 'jour' }}

                                                </span>

                                            @elseif($joursRestants === 0)

                                                <span class="stock-lots-expiration-page__expiration-detail stock-lots-expiration-page__expiration-detail--danger">
                                                    Expire aujourd'hui
                                                </span>

                                            @elseif($joursRestants <= 30)

                                                <span class="stock-lots-expiration-page__expiration-detail stock-lots-expiration-page__expiration-detail--warning">

                                                    Dans
                                                    {{ $joursRestants }}
                                                    {{ $joursRestants > 1 ? 'jours' : 'jour' }}

                                                </span>

                                            @else

                                                <span class="stock-lots-expiration-page__expiration-detail">

                                                    Dans {{ $joursRestants }} jours

                                                </span>

                                            @endif

                                        </div>

                                    @else

                                        <span class="stock-lots-expiration-page__muted">
                                            Non définie
                                        </span>

                                    @endif

                                </td>


                                {{-- QUANTITÉ --}}
                                <td>

                                    <div class="stock-lots-expiration-page__quantity">

                                        <strong>
                                            {{ number_format($quantite, 2, ',', ' ') }}
                                        </strong>

                                        @if($lot->stock?->unite)

                                            <span>
                                                {{ $lot->stock->unite }}
                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- ÉTAT --}}
                                <td>

                                    @if($estExpire)

                                        <span class="stock-lots-expiration-page__status stock-lots-expiration-page__status--danger">

                                            <span class="stock-lots-expiration-page__status-dot"></span>

                                            Expiré

                                        </span>

                                    @elseif($expirationProche)

                                        <span class="stock-lots-expiration-page__status stock-lots-expiration-page__status--warning">

                                            <span class="stock-lots-expiration-page__status-dot"></span>

                                            Expiration proche

                                        </span>

                                    @else

                                        <span class="stock-lots-expiration-page__status stock-lots-expiration-page__status--success">

                                            <span class="stock-lots-expiration-page__status-dot"></span>

                                            À surveiller

                                        </span>

                                    @endif

                                </td>


                                {{-- ACTION --}}
                                <td>

                                    <div class="stock-lots-expiration-page__row-actions">

                                        <a
                                            href="{{ route('admin.stocks.lots.show', $lot) }}"
                                            class="stock-lots-expiration-page__row-action"
                                            title="Voir le lot"
                                            aria-label="Voir le lot"
                                        >
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if($lots->hasPages())

                <div class="stock-lots-expiration-page__pagination">

                    {{ $lots->withQueryString()->links() }}

                </div>

            @endif

        @else

            {{-- ÉTAT VIDE --}}
            <div class="stock-lots-expiration-page__empty">

                <div class="stock-lots-expiration-page__empty-icon">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i>
                </div>

                <h3>
                    Aucun lot à expiration
                </h3>

                <p>
                    Aucun lot correspondant aux critères sélectionnés
                    ne nécessite actuellement votre attention.
                </p>

                <a
                    href="{{ route('admin.stocks.lots.index') }}"
                    class="stock-lots-expiration-page__button stock-lots-expiration-page__button--secondary"
                >

                    <i class="fas fa-boxes-stacked" aria-hidden="true"></i>

                    Voir tous les lots

                </a>

            </div>

        @endif

    </section>


    {{-- =========================================================
         INFORMATION MÉTIER
    ========================================================== --}}
    <div class="stock-lots-expiration-page__info-card">

        <div class="stock-lots-expiration-page__info-icon">
            <i class="fas fa-shield-halved" aria-hidden="true"></i>
        </div>

        <div class="stock-lots-expiration-page__info-content">

            <h3>
                Gestion sécurisée des lots
            </h3>

            <p>
                L’expiration d’un lot ne modifie pas directement la quantité
                disponible. Toute modification physique du stock doit passer
                par un mouvement de stock enregistré et traçable.
            </p>

        </div>

    </div>

</div>

@endsection