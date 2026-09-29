@extends('layouts.admin')

@section('title', 'Mouvements de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/mouvements-index.css') }}"
    >
@endsection

@section('content')

<div class="stock-mouvements-page">

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="stock-mouvements-header">

        <div class="stock-mouvements-header__content">

            <nav
                class="stock-mouvements-breadcrumb"
                aria-label="Fil d’Ariane"
            >

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span class="stock-mouvements-breadcrumb__separator">
                    /
                </span>

                <span>
                    Mouvements
                </span>

            </nav>


            <div class="stock-mouvements-title-row">

                <div class="stock-mouvements-title-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>

                <div>

                    <h1 class="stock-mouvements-title">
                        Mouvements de stock
                    </h1>

                    <p class="stock-mouvements-subtitle">
                        Consultez l’historique des entrées, sorties,
                        ajustements et autres mouvements enregistrés
                        dans le stock.
                    </p>

                </div>

            </div>

        </div>


        <div class="stock-mouvements-header__actions">

            <a
                href="{{ route('admin.stocks.index') }}"
                class="stock-mouvements-button stock-mouvements-button--secondary"
            >
                <i class="fas fa-boxes"></i>
                <span>Stocks</span>
            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES DE SESSION
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-mouvements-alert stock-mouvements-alert--success">

            <div class="stock-mouvements-alert__icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <div class="stock-mouvements-alert__content">

                <strong>
                    Opération réussie
                </strong>

                <p>
                    {{ session('success') }}
                </p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-mouvements-alert stock-mouvements-alert--danger">

            <div class="stock-mouvements-alert__icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <div class="stock-mouvements-alert__content">

                <strong>
                    Opération impossible
                </strong>

                <p>
                    {{ session('error') }}
                </p>

            </div>

        </div>

    @endif


    {{-- =========================================================
         STATISTIQUES
         ------------------------------------------------
         Ces variables sont fournies par le controller.
         Si ce n’est pas le cas, on retombe sur un calcul local.
    ========================================================== --}}
    <div class="stock-mouvements-statistics">

        {{-- TOTAL --}}
        <div class="stock-mouvements-stat-card">

            <div class="stock-mouvements-stat-card__icon">
                <i class="fas fa-exchange-alt"></i>
            </div>

            <div class="stock-mouvements-stat-card__content">

                <span>
                    Total des mouvements
                </span>

                <strong>
                    {{ number_format($mouvements->total(), 0, ',', ' ') }}
                </strong>

                <small>
                    mouvements enregistrés
                </small>

            </div>

        </div>


        {{-- ENTRÉES --}}
        <div class="stock-mouvements-stat-card stock-mouvements-stat-card--entry">

            <div class="stock-mouvements-stat-card__icon">
                <i class="fas fa-arrow-down"></i>
            </div>

            <div class="stock-mouvements-stat-card__content">

                <span>
                    Entrées
                </span>

                <strong>
                    {{ number_format($totalEntrees ?? 0, 0, ',', ' ') }}
                </strong>

                <small>
                    mouvements entrants
                </small>

            </div>

        </div>


        {{-- SORTIES --}}
        <div class="stock-mouvements-stat-card stock-mouvements-stat-card--exit">

            <div class="stock-mouvements-stat-card__icon">
                <i class="fas fa-arrow-up"></i>
            </div>

            <div class="stock-mouvements-stat-card__content">

                <span>
                    Sorties
                </span>

                <strong>
                    {{ number_format($totalSorties ?? 0, 0, ',', ' ') }}
                </strong>

                <small>
                    mouvements sortants
                </small>

            </div>

        </div>


        {{-- AJUSTEMENTS --}}
        <div class="stock-mouvements-stat-card stock-mouvements-stat-card--adjustment">

            <div class="stock-mouvements-stat-card__icon">
                <i class="fas fa-sliders-h"></i>
            </div>

            <div class="stock-mouvements-stat-card__content">

                <span>
                    Ajustements
                </span>

                <strong>
                    {{ number_format($totalAjustements ?? 0, 0, ',', ' ') }}
                </strong>

                <small>
                    corrections de stock
                </small>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}
    <div class="stock-mouvements-card">

        <div class="stock-mouvements-card__header">

            <div>

                <h2>
                    Filtres
                </h2>

                <p>
                    Affinez l’historique des mouvements.
                </p>

            </div>

            <div class="stock-mouvements-card__header-icon">
                <i class="fas fa-filter"></i>
            </div>

        </div>


        <div class="stock-mouvements-card__body">

            <form
                method="GET"
                action="{{ route('admin.stocks.mouvements.index') }}"
                class="stock-mouvements-filter-form"
                id="stock-mouvements-filter-form"
            >

                <div class="stock-mouvements-filter-grid">

                    {{-- RECHERCHE --}}
                    <div class="stock-mouvements-form-group">

                        <label for="recherche">
                            Recherche
                        </label>

                        <div class="stock-mouvements-input-wrapper">

                            <i class="fas fa-search"></i>

                            <input
                                type="search"
                                id="recherche"
                                name="recherche"
                                value="{{ request('recherche') }}"
                                placeholder="Article, référence..."
                                autocomplete="off"
                            >

                        </div>

                    </div>


                    {{-- TYPE --}}
                    <div class="stock-mouvements-form-group">

                        <label for="type">
                            Type de mouvement
                        </label>

                        <select
                            id="type"
                            name="type"
                        >

                            <option value="">
                                Tous les types
                            </option>

                            @foreach(($types ?? []) as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected(request('type') === (string) $value)
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DATE DÉBUT --}}
                    <div class="stock-mouvements-form-group">

                        <label for="date_debut">
                            Date début
                        </label>

                        <input
                            type="date"
                            id="date_debut"
                            name="date_debut"
                            value="{{ request('date_debut') }}"
                        >

                    </div>


                    {{-- DATE FIN --}}
                    <div class="stock-mouvements-form-group">

                        <label for="date_fin">
                            Date fin
                        </label>

                        <input
                            type="date"
                            id="date_fin"
                            name="date_fin"
                            value="{{ request('date_fin') }}"
                        >

                    </div>


                    {{-- ARTICLE --}}
                    <div class="stock-mouvements-form-group">

                        <label for="stock_id">
                            Article
                        </label>

                        <select
                            id="stock_id"
                            name="stock_id"
                        >

                            <option value="">
                                Tous les articles
                            </option>

                            @foreach(($stocks ?? []) as $stock)

                                <option
                                    value="{{ $stock->id }}"
                                    @selected(
                                        (string) request('stock_id')
                                        ===
                                        (string) $stock->id
                                    )
                                >
                                    {{ $stock->designation }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- UTILISATEUR --}}
                    <div class="stock-mouvements-form-group">

                        <label for="utilisateur_id">
                            Utilisateur
                        </label>

                        <select
                            id="utilisateur_id"
                            name="utilisateur_id"
                        >

                            <option value="">
                                Tous les utilisateurs
                            </option>

                            @foreach(($utilisateurs ?? []) as $utilisateur)

                                <option
                                    value="{{ $utilisateur->id }}"
                                    @selected(
                                        (string) request('utilisateur_id')
                                        ===
                                        (string) $utilisateur->id
                                    )
                                >
                                    {{ $utilisateur->name ?? 'Utilisateur' }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- ACTIONS --}}
                <div class="stock-mouvements-filter-actions">

                    <button
                        type="submit"
                        class="stock-mouvements-button stock-mouvements-button--primary"
                    >
                        <i class="fas fa-search"></i>
                        <span>Filtrer</span>
                    </button>


                    <a
                        href="{{ route('admin.stocks.mouvements.index') }}"
                        class="stock-mouvements-button stock-mouvements-button--secondary"
                    >
                        <i class="fas fa-undo"></i>
                        <span>Réinitialiser</span>
                    </a>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         HISTORIQUE
    ========================================================== --}}
    <div class="stock-mouvements-card">

        <div class="stock-mouvements-card__header">

            <div>

                <h2>
                    Historique des mouvements
                </h2>

                <p>
                    Liste chronologique des opérations enregistrées.
                </p>

            </div>

            <div class="stock-mouvements-results-count">

                {{ number_format($mouvements->total(), 0, ',', ' ') }}

                résultat(s)

            </div>

        </div>


        @if($mouvements->count() > 0)

            <div class="stock-mouvements-table-wrapper">

                <table class="stock-mouvements-table">

                    <thead>

                        <tr>

                            <th>
                                Date
                            </th>

                            <th>
                                Référence
                            </th>

                            <th>
                                Article
                            </th>

                            <th>
                                Type
                            </th>

                            <th class="text-right">
                                Quantité
                            </th>

                            <th>
                                Utilisateur
                            </th>

                            <th class="text-right">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($mouvements as $mouvement)

                            @php
                                $typeMouvement = $mouvement->type instanceof \App\Enums\TypeMouvement
                                    ? $mouvement->type->value
                                    : (string) $mouvement->type;

                                $isEntree     = $typeMouvement === 'entree';
                                $isSortie     = $typeMouvement === 'sortie';
                                $isAjustement = $typeMouvement === 'ajustement';
                                $isTransfert  = $typeMouvement === 'transfert';
                            @endphp


                            <tr>

                                {{-- DATE --}}
                                <td>

                                    <div class="stock-mouvements-date">

                                        <strong>
                                            {{ $mouvement->date_mouvement
                                                ? \Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m/Y')
                                                : '—'
                                            }}
                                        </strong>

                                        @if($mouvement->created_at)

                                            <small>
                                                {{ $mouvement->created_at->format('H:i') }}
                                            </small>

                                        @endif

                                    </div>

                                </td>


                                {{-- RÉFÉRENCE --}}
                                <td>

                                    <span class="stock-mouvements-reference">

                                        {{ $mouvement->reference_document ?? '—' }}

                                    </span>

                                </td>


                                {{-- ARTICLE --}}
                                <td>

                                    <div class="stock-mouvements-article">

                                        <div class="stock-mouvements-article__icon">
                                            <i class="fas fa-box"></i>
                                        </div>

                                        <div class="stock-mouvements-article__content">

                                            <strong>

                                                {{ $mouvement->stock?->designation ?? 'Article supprimé' }}

                                            </strong>


                                            @if($mouvement->stock?->reference)

                                                <small>
                                                    {{ $mouvement->stock->reference }}
                                                </small>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- TYPE --}}
                                <td>

                                    @if($isEntree)

                                        <span class="stock-mouvements-badge stock-mouvements-badge--entry">

                                            <i class="fas fa-arrow-down"></i>

                                            Entrée

                                        </span>

                                    @elseif($isSortie)

                                        <span class="stock-mouvements-badge stock-mouvements-badge--exit">

                                            <i class="fas fa-arrow-up"></i>

                                            Sortie

                                        </span>

                                    @elseif($isAjustement)

                                        <span class="stock-mouvements-badge stock-mouvements-badge--adjustment">

                                            <i class="fas fa-sliders-h"></i>

                                            Ajustement

                                        </span>

                                    @elseif($isTransfert)

                                        <span class="stock-mouvements-badge stock-mouvements-badge--neutral">

                                            <i class="fas fa-exchange-alt"></i>

                                            Transfert

                                        </span>

                                    @else

                                        <span class="stock-mouvements-badge stock-mouvements-badge--neutral">

                                            {{ ucfirst((string) $typeMouvement) }}

                                        </span>

                                    @endif

                                </td>


                                {{-- QUANTITÉ --}}
                                <td class="text-right">

                                    @php
                                        $quantiteSignee = (float) $mouvement->quantite
                                            * (int) ($mouvement->sens ?? 1);
                                    @endphp

                                    <span
                                        class="stock-mouvements-quantity
                                            {{ $isEntree ? 'stock-mouvements-quantity--positive' : '' }}
                                            {{ $isSortie ? 'stock-mouvements-quantity--negative' : '' }}"
                                    >

                                        @if($quantiteSignee > 0)
                                            +
                                        @elseif($quantiteSignee < 0)
                                            -
                                        @endif

                                        {{ number_format(
                                            abs($quantiteSignee),
                                            2,
                                            ',',
                                            ' '
                                        ) }}

                                    </span>

                                </td>


                                {{-- UTILISATEUR --}}
                                <td>

                                    @if($mouvement->utilisateur)

                                        <div class="stock-mouvements-user">

                                            <div class="stock-mouvements-user__avatar">

                                                {{ strtoupper(
                                                    mb_substr(
                                                        $mouvement->utilisateur->name ?? 'U',
                                                        0,
                                                        1
                                                    )
                                                ) }}

                                            </div>


                                            <div class="stock-mouvements-user__content">

                                                <strong>
                                                    {{ $mouvement->utilisateur->name ?? 'Utilisateur' }}
                                                </strong>

                                                @if($mouvement->utilisateur->email)

                                                    <small>
                                                        {{ $mouvement->utilisateur->email }}
                                                    </small>

                                                @endif

                                            </div>

                                        </div>

                                    @else

                                        <span class="stock-mouvements-muted">
                                            Système
                                        </span>

                                    @endif

                                </td>


                                {{-- ACTION --}}
                                <td class="text-right">

                                    <a
                                        href="{{ route(
                                            'admin.stocks.mouvements.show',
                                            $mouvement
                                        ) }}"
                                        class="stock-mouvements-table-action"
                                        title="Voir le mouvement"
                                        aria-label="Voir le mouvement"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            <div class="stock-mouvements-pagination">

                {{ $mouvements->withQueryString()->links() }}

            </div>


        @else

            {{-- ÉTAT VIDE --}}
            <div class="stock-mouvements-empty">

                <div class="stock-mouvements-empty__icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>

                <h3>
                    Aucun mouvement trouvé
                </h3>

                <p>
                    Aucun mouvement ne correspond aux critères
                    sélectionnés.
                </p>


                @if(request()->hasAny([
                    'recherche',
                    'type',
                    'date_debut',
                    'date_fin',
                    'stock_id',
                    'utilisateur_id'
                ]))

                    <a
                        href="{{ route('admin.stocks.mouvements.index') }}"
                        class="stock-mouvements-button stock-mouvements-button--secondary"
                    >
                        <i class="fas fa-undo"></i>
                        Réinitialiser les filtres
                    </a>

                @endif

            </div>

        @endif

    </div>


    {{-- =========================================================
         INFORMATION MÉTIER
    ========================================================== --}}
    <div class="stock-mouvements-information">

        <div class="stock-mouvements-information__icon">
            <i class="fas fa-info-circle"></i>
        </div>

        <div class="stock-mouvements-information__content">

            <strong>
                Historique de traçabilité
            </strong>

            <p>
                Les mouvements constituent l’historique des opérations
                ayant modifié ou documenté le stock. Les corrections
                doivent être effectuées à travers les procédures
                prévues par le module afin de préserver la traçabilité.
            </p>

        </div>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/mouvements-index.js') }}"
        defer>
    </script>

@endsection