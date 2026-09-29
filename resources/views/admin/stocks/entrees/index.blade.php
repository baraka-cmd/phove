@extends('layouts.admin')

@section('title', 'Entrées de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/entrees-index.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-entrees-page">

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-text">

                <div class="page-breadcrumb">

                    <a href="{{ route('admin.stocks.index') }}">
                        Stocks
                    </a>

                    <span class="breadcrumb-separator">/</span>

                    <span>Entrées</span>

                </div>

                <h1 class="page-title">
                    Entrées de stock
                </h1>

                <p class="page-subtitle">
                    Consultez et suivez les opérations de réception
                    ayant augmenté le stock.
                </p>

            </div>


            <div class="page-header-actions">

                @can('create', App\Models\Stock::class)

                    <a
                        href="{{ route('admin.stocks.entrees.create') }}"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        <span>Nouvelle entrée</span>
                    </a>

                @endcan

                <a
                    href="{{ route('admin.stocks.index') }}"
                    class="btn btn-secondary"
                >
                    <i class="fas fa-boxes-stacked" aria-hidden="true"></i>
                    <span>Stocks</span>
                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES DE SESSION
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success" role="alert">

            <div class="alert-icon">
                <i class="fas fa-circle-check" aria-hidden="true"></i>
            </div>

            <div class="alert-content">
                {{ session('success') }}
            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger" role="alert">

            <div class="alert-icon">
                <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
            </div>

            <div class="alert-content">
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         INDICATEURS
         ------------------------------------------------
         Les valeurs sont préparées côté controller/service.
         La Blade ne calcule aucune donnée métier.
    ========================================================== --}}
    <div class="stats-grid">

        {{-- TOTAL --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-arrow-down" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    Total des entrées
                </span>

                <strong class="stat-card-value">
                    {{ number_format((int) ($stats['total'] ?? $entrees->total()), 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- QUANTITÉ REÇUE --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-box-open" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    Quantité reçue
                </span>

                <strong class="stat-card-value">
                    {{ number_format((float) ($stats['quantite'] ?? 0), 2, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- ENTRÉES CE MOIS --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-calendar-day" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    Entrées ce mois
                </span>

                <strong class="stat-card-value">
                    {{ number_format((int) ($stats['ce_mois'] ?? 0), 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- DERNIÈRE ENTRÉE --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-clock" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    Dernière entrée
                </span>

                <strong class="stat-card-value stat-card-value-small">

                    @if(isset($stats['derniere_date']) && $stats['derniere_date'])

                        {{ \Illuminate\Support\Carbon::parse($stats['derniere_date'])->format('d/m/Y') }}

                    @else

                        —

                    @endif

                </strong>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}
    <section class="content-card filters-card">

        <div class="content-card-header">

            <div>

                <h2 class="content-card-title">
                    Rechercher une entrée
                </h2>

                <p class="content-card-description">
                    Utilisez les filtres pour retrouver rapidement
                    une opération de réception.
                </p>

            </div>

        </div>


        <div class="content-card-body">

            <form
                action="{{ route('admin.stocks.entrees.index') }}"
                method="GET"
                class="filters-form"
            >

                {{-- RECHERCHE --}}
                <div class="form-group filter-search">

                    <label for="recherche" class="form-label">
                        Recherche
                    </label>

                    <div class="input-with-icon">

                        <i class="fas fa-search" aria-hidden="true"></i>

                        <input
                            type="search"
                            id="recherche"
                            name="recherche"
                            value="{{ request('recherche') }}"
                            class="form-control"
                            placeholder="Référence, article, lot..."
                            autocomplete="off"
                        >

                    </div>

                </div>


                {{-- DATE DÉBUT --}}
                <div class="form-group">

                    <label for="date_debut" class="form-label">
                        Du
                    </label>

                    <input
                        type="date"
                        id="date_debut"
                        name="date_debut"
                        value="{{ request('date_debut') }}"
                        class="form-control"
                    >

                </div>


                {{-- DATE FIN --}}
                <div class="form-group">

                    <label for="date_fin" class="form-label">
                        Au
                    </label>

                    <input
                        type="date"
                        id="date_fin"
                        name="date_fin"
                        value="{{ request('date_fin') }}"
                        class="form-control"
                    >

                </div>


                {{-- NATURE --}}
                <div class="form-group">

                    <label for="nature" class="form-label">
                        Nature
                    </label>

                    <select
                        id="nature"
                        name="nature"
                        class="form-control"
                    >

                        <option value="">
                            Toutes les natures
                        </option>

                        @foreach(($natures ?? []) as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(request('nature') === $value)
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ACTIONS --}}
                <div class="filter-actions">

                    <button type="submit" class="btn btn-primary">

                        <i class="fas fa-filter" aria-hidden="true"></i>

                        <span>Filtrer</span>

                    </button>

                    @if(
                        request()->filled('recherche')
                        || request()->filled('date_debut')
                        || request()->filled('date_fin')
                        || request()->filled('nature')
                    )

                        <a
                            href="{{ route('admin.stocks.entrees.index') }}"
                            class="btn btn-secondary"
                        >

                            <i class="fas fa-rotate-left" aria-hidden="true"></i>

                            <span>Réinitialiser</span>

                        </a>

                    @endif

                </div>

            </form>

        </div>

    </section>


    {{-- =========================================================
         TABLEAU DES ENTRÉES
    ========================================================== --}}
    <section class="content-card entries-card">

        <div class="content-card-header entries-card-header">

            <div>

                <h2 class="content-card-title">
                    Historique des entrées
                </h2>

                <p class="content-card-description">
                    {{ $entrees->total() }}
                    opération{{ $entrees->total() > 1 ? 's' : '' }}
                    enregistrée{{ $entrees->total() > 1 ? 's' : '' }}.
                </p>

            </div>

            <div class="entries-card-meta">

                <span class="results-count">
                    Page {{ $entrees->currentPage() }}
                    sur {{ $entrees->lastPage() }}
                </span>

            </div>

        </div>


        <div class="content-card-body table-container">

            @if($entrees->count())

                <div class="table-responsive">

                    <table class="data-table">

                        <thead>
                            <tr>

                                <th scope="col">Référence</th>
                                <th scope="col">Article</th>
                                <th scope="col">Lot</th>
                                <th scope="col">Quantité</th>
                                <th scope="col">Nature</th>
                                <th scope="col">Date</th>
                                <th scope="col">Opérateur</th>
                                <th scope="col" class="text-right">Actions</th>

                            </tr>
                        </thead>


                        <tbody>

                            @foreach($entrees as $entree)

                                @php
                                    /*
                                     * Normalisation des enums.
                                     */
                                    $natureValue = $entree->nature instanceof \App\Enums\NatureMouvement
                                        ? $entree->nature->value
                                        : (string) $entree->nature;

                                    $natureLabel = $entree->nature instanceof \App\Enums\NatureMouvement
                                        ? $entree->nature->libelle()
                                        : ucfirst(str_replace('_', ' ', $natureValue));
                                @endphp

                                <tr>

                                    {{-- RÉFÉRENCE --}}
                                    <td>

                                        <div class="reference-cell">

                                            <span class="reference-value">
                                                {{ $entree->reference_document ?? '—' }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- ARTICLE --}}
                                    <td>

                                        <div class="product-cell">

                                            <div class="product-icon">
                                                <i class="fas fa-box" aria-hidden="true"></i>
                                            </div>

                                            <div class="product-info">

                                                @if($entree->stock)

                                                    <a
                                                        href="{{ route('admin.stocks.show', $entree->stock) }}"
                                                        class="product-name"
                                                    >
                                                        {{ $entree->stock->designation }}
                                                    </a>

                                                    @if($entree->stock->reference)

                                                        <span class="product-reference">
                                                            {{ $entree->stock->reference }}
                                                        </span>

                                                    @endif

                                                @else

                                                    <span class="product-name">
                                                        Article supprimé
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- LOT --}}
                                    <td>

                                        @if($entree->lotStock)

                                            <div class="lot-cell">

                                                <span class="lot-number">
                                                    {{ $entree->lotStock->numero_lot ?? ('LOT-' . $entree->lotStock->id) }}
                                                </span>

                                                @if($entree->lotStock->date_expiration)

                                                    <span class="lot-expiration">
                                                        Exp.
                                                        {{ \Illuminate\Support\Carbon::parse($entree->lotStock->date_expiration)->format('d/m/Y') }}
                                                    </span>

                                                @endif

                                            </div>

                                        @else

                                            <span class="empty-value">
                                                Sans lot
                                            </span>

                                        @endif

                                    </td>


                                    {{-- QUANTITÉ --}}
                                    <td>

                                        <div class="quantity-cell">

                                            <strong class="quantity-value">
                                                +
                                                {{ number_format((float) $entree->quantite, 2, ',', ' ') }}
                                            </strong>

                                            <span class="quantity-unit">
                                                {{ $entree->stock?->unite ?? '' }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- NATURE --}}
                                    <td>

                                        <span class="badge badge-nature">
                                            {{ $natureLabel }}
                                        </span>

                                    </td>


                                    {{-- DATE --}}
                                    <td>

                                        <div class="date-cell">

                                            <span class="date-main">
                                                {{ $entree->date_mouvement
                                                    ? \Illuminate\Support\Carbon::parse($entree->date_mouvement)->format('d/m/Y')
                                                    : '—'
                                                }}
                                            </span>

                                            @if($entree->created_at)

                                                <span class="date-time">
                                                    {{ $entree->created_at->format('H:i') }}
                                                </span>

                                            @endif

                                        </div>

                                    </td>


                                    {{-- OPÉRATEUR --}}
                                    <td>

                                        @if($entree->utilisateur)

                                            <div class="user-cell">

                                                <div class="user-avatar">
                                                    {{ strtoupper(
                                                        mb_substr(
                                                            $entree->utilisateur->name ?? 'U',
                                                            0,
                                                            1
                                                        )
                                                    ) }}
                                                </div>

                                                <div class="user-info">

                                                    <span class="user-name">
                                                        {{ $entree->utilisateur->name ?? 'Utilisateur' }}
                                                    </span>

                                                </div>

                                            </div>

                                        @else

                                            <span class="empty-value">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTIONS --}}
                                    <td class="text-right">

                                        <div class="table-actions">

                                            {{-- VOIR L'ENTRÉE --}}
                                            <a
                                                href="{{ route('admin.stocks.entrees.show', $entree) }}"
                                                class="action-button action-view"
                                                title="Voir l’entrée"
                                                aria-label="Voir l’entrée"
                                            >
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </a>


                                            {{-- VOIR L'ARTICLE --}}
                                            @if($entree->stock)

                                                <a
                                                    href="{{ route('admin.stocks.show', $entree->stock) }}"
                                                    class="action-button action-stock"
                                                    title="Voir l’article"
                                                    aria-label="Voir l’article"
                                                >
                                                    <i class="fas fa-box" aria-hidden="true"></i>
                                                </a>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                {{-- ÉTAT VIDE --}}
                <div class="empty-state">

                    <div class="empty-state-icon">
                        <i class="fas fa-inbox" aria-hidden="true"></i>
                    </div>

                    <h3 class="empty-state-title">
                        Aucune entrée trouvée
                    </h3>

                    <p class="empty-state-description">

                        @if(
                            request()->filled('recherche')
                            || request()->filled('date_debut')
                            || request()->filled('date_fin')
                            || request()->filled('nature')
                        )

                            Aucune entrée ne correspond aux critères
                            de recherche sélectionnés.

                        @else

                            Aucune opération d’entrée de stock
                            n’a encore été enregistrée.

                        @endif

                    </p>

                    @if(
                        request()->filled('recherche')
                        || request()->filled('date_debut')
                        || request()->filled('date_fin')
                        || request()->filled('nature')
                    )

                        <a
                            href="{{ route('admin.stocks.entrees.index') }}"
                            class="btn btn-secondary"
                        >

                            <i class="fas fa-rotate-left" aria-hidden="true"></i>

                            <span>Réinitialiser les filtres</span>

                        </a>

                    @else

                        @can('create', App\Models\Stock::class)

                            <a
                                href="{{ route('admin.stocks.entrees.create') }}"
                                class="btn btn-primary"
                            >

                                <i class="fas fa-plus" aria-hidden="true"></i>

                                <span>Enregistrer une entrée</span>

                            </a>

                        @endcan

                    @endif

                </div>

            @endif

        </div>


        {{-- PAGINATION --}}
        @if($entrees->hasPages())

            <div class="content-card-footer pagination-container">

                <div class="pagination-summary">
                    Affichage de
                    <strong>{{ $entrees->firstItem() }}</strong>
                    à
                    <strong>{{ $entrees->lastItem() }}</strong>
                    sur
                    <strong>{{ $entrees->total() }}</strong>
                    entrées
                </div>

                <div class="pagination-links">
                    {{ $entrees->withQueryString()->links() }}
                </div>

            </div>

        @endif

    </section>


    {{-- =========================================================
         INFORMATION MÉTIER
    ========================================================== --}}
    <section class="information-panel">

        <div class="information-panel-icon">
            <i class="fas fa-circle-info" aria-hidden="true"></i>
        </div>

        <div class="information-panel-content">

            <h3>
                Traçabilité des entrées
            </h3>

            <p>
                Chaque entrée correspond à une opération métier
                enregistrée dans l’historique des mouvements.
                Les quantités affichées sont issues des données
                validées par le serveur.
            </p>

            <p>
                Une entrée peut être associée à un lot, une référence
                documentaire, un utilisateur et une nature d’opération
                afin de garantir la traçabilité du stock.
            </p>

        </div>

    </section>

</div>

@endsection