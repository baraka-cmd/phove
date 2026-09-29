@extends('layouts.admin')

@section('title', 'Lots de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/lots-index.css') }}"
    >
@endsection

@section('content')

<div class="stock-lots-page">

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="stock-lots-page__header">

        <div class="stock-lots-page__header-main">

            <div class="stock-lots-page__breadcrumb">

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span class="stock-lots-page__breadcrumb-separator">
                    /
                </span>

                <span>Lots</span>

            </div>

            <h1 class="stock-lots-page__title">
                Gestion des lots
            </h1>

            <p class="stock-lots-page__subtitle">
                Consultez, surveillez et gérez les lots associés aux articles
                du stock.
            </p>

        </div>


        <div class="stock-lots-page__header-actions">

            <a
                href="{{ route('admin.stocks.index') }}"
                class="stock-lots-page__button stock-lots-page__button--secondary"
            >
                <i class="fas fa-boxes" aria-hidden="true"></i>
                <span>Articles</span>
            </a>

            <a
                href="{{ route('admin.stocks.lots.expiration') }}"
                class="stock-lots-page__button stock-lots-page__button--warning"
            >
                <i class="fas fa-calendar-times" aria-hidden="true"></i>
                <span>Expiration</span>
            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES FLASH
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-lots-page__alert stock-lots-page__alert--success" role="alert">

            <div class="stock-lots-page__alert-icon">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-page__alert-content">

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-lots-page__alert stock-lots-page__alert--danger" role="alert">

            <div class="stock-lots-page__alert-icon">
                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-page__alert-content">

                <strong>Opération impossible</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    @if(session('warning'))

        <div class="stock-lots-page__alert stock-lots-page__alert--warning" role="alert">

            <div class="stock-lots-page__alert-icon">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-page__alert-content">

                <strong>Attention</strong>

                <p>{{ session('warning') }}</p>

            </div>

        </div>

    @endif


    {{-- =========================================================
         INDICATEURS
         ------------------------------------------------
         $stats est fourni par LotStockController::index()
         via LotStockService::statistiques().

         Clés attendues :
           - total
           - disponibles
           - bloques
           - expires
    ========================================================== --}}
    <div class="stock-lots-page__stats">

        {{-- TOTAL --}}
        <div class="stock-lots-page__stat-card">

            <div class="stock-lots-page__stat-icon stock-lots-page__stat-icon--primary">
                <i class="fas fa-layer-group" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-page__stat-content">

                <span class="stock-lots-page__stat-label">
                    Total des lots
                </span>

                <strong class="stock-lots-page__stat-value">
                    {{ number_format((int) ($stats['total'] ?? 0), 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- DISPONIBLES --}}
        <div class="stock-lots-page__stat-card">

            <div class="stock-lots-page__stat-icon stock-lots-page__stat-icon--success">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-page__stat-content">

                <span class="stock-lots-page__stat-label">
                    Disponibles
                </span>

                <strong class="stock-lots-page__stat-value">
                    {{ number_format((int) ($stats['disponibles'] ?? 0), 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- BLOQUÉS --}}
        <div class="stock-lots-page__stat-card">

            <div class="stock-lots-page__stat-icon stock-lots-page__stat-icon--warning">
                <i class="fas fa-lock" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-page__stat-content">

                <span class="stock-lots-page__stat-label">
                    Bloqués
                </span>

                <strong class="stock-lots-page__stat-value">
                    {{ number_format((int) ($stats['bloques'] ?? 0), 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- EXPIRÉS --}}
        <div class="stock-lots-page__stat-card">

            <div class="stock-lots-page__stat-icon stock-lots-page__stat-icon--danger">
                <i class="fas fa-calendar-times" aria-hidden="true"></i>
            </div>

            <div class="stock-lots-page__stat-content">

                <span class="stock-lots-page__stat-label">
                    Expirés
                </span>

                <strong class="stock-lots-page__stat-value">
                    {{ number_format((int) ($stats['expires'] ?? 0), 0, ',', ' ') }}
                </strong>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}
    <div class="stock-lots-page__card">

        <div class="stock-lots-page__card-header">

            <div>

                <h2 class="stock-lots-page__card-title">
                    Rechercher un lot
                </h2>

                <p class="stock-lots-page__card-description">
                    Utilisez les filtres pour retrouver rapidement un lot.
                </p>

            </div>

        </div>


        <div class="stock-lots-page__card-body">

            <form
                method="GET"
                action="{{ route('admin.stocks.lots.index') }}"
                class="stock-lots-page__filters"
            >

                {{-- RECHERCHE --}}
                <div class="stock-lots-page__field stock-lots-page__field--search">

                    <label for="recherche" class="stock-lots-page__label">
                        Recherche
                    </label>

                    <div class="stock-lots-page__input-wrapper">

                        <i class="fas fa-search" aria-hidden="true"></i>

                        <input
                            type="search"
                            id="recherche"
                            name="recherche"
                            class="stock-lots-page__input stock-lots-page__input--with-icon"
                            value="{{ request('recherche') }}"
                            placeholder="Numéro de lot, article..."
                            autocomplete="off"
                        >

                    </div>

                </div>


                {{-- STATUT --}}
                <div class="stock-lots-page__field">

                    <label for="statut" class="stock-lots-page__label">
                        Statut
                    </label>

                    <select
                        id="statut"
                        name="statut"
                        class="stock-lots-page__select"
                    >

                        <option value="">
                            Tous les statuts
                        </option>

                        @foreach(($statuts ?? []) as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(request('statut') === $value)
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ARTICLE --}}
                <div class="stock-lots-page__field">

                    <label for="stock_id" class="stock-lots-page__label">
                        Article
                    </label>

                    <select
                        id="stock_id"
                        name="stock_id"
                        class="stock-lots-page__select"
                    >

                        <option value="">
                            Tous les articles
                        </option>

                        @foreach(($stocks ?? []) as $stock)

                            <option
                                value="{{ $stock->id }}"
                                @selected(
                                    (string) request('stock_id')
                                    === (string) $stock->id
                                )
                            >
                                {{ $stock->designation }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ACTIONS --}}
                <div class="stock-lots-page__filter-actions">

                    <button
                        type="submit"
                        class="stock-lots-page__button stock-lots-page__button--primary"
                    >
                        <i class="fas fa-filter" aria-hidden="true"></i>
                        <span>Filtrer</span>
                    </button>

                    @if(request()->hasAny([
                        'recherche',
                        'statut',
                        'stock_id'
                    ]))

                        <a
                            href="{{ route('admin.stocks.lots.index') }}"
                            class="stock-lots-page__button stock-lots-page__button--secondary"
                        >
                            <i class="fas fa-times" aria-hidden="true"></i>
                            <span>Réinitialiser</span>
                        </a>

                    @endif

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         LISTE DES LOTS
    ========================================================== --}}
    <div class="stock-lots-page__card">

        <div class="stock-lots-page__card-header">

            <div>

                <h2 class="stock-lots-page__card-title">
                    Liste des lots
                </h2>

                <p class="stock-lots-page__card-description">
                    Les quantités affichées correspondent à l'état actuel
                    du lot.
                </p>

            </div>

            <span class="stock-lots-page__count">

                {{ $lots->total() }}
                {{ $lots->total() > 1 ? 'lots' : 'lot' }}

            </span>

        </div>


        @if($lots->count() > 0)

            <div class="stock-lots-page__table-wrapper">

                <table class="stock-lots-page__table">

                    <thead>

                        <tr>

                            <th scope="col">Lot</th>
                            <th scope="col">Article</th>
                            <th scope="col">Entrée</th>
                            <th scope="col">Expiration</th>
                            <th scope="col" class="stock-lots-page__table-number">Quantité actuelle</th>
                            <th scope="col">État</th>
                            <th scope="col" class="stock-lots-page__table-actions">Actions</th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($lots as $lot)

                            @php
                                /*
                                 * Normalisation du statut de l'enum.
                                 *
                                 * $lot->statut est casté en App\Enums\StatutLot
                                 * par le modèle. On récupère sa valeur string
                                 * pour l'utiliser dans @switch et éviter
                                 * les erreurs de cast d'enum en PHP 8.5.
                                 */
                                $statutLotValue = $lot->statut instanceof \App\Enums\StatutLot
                                    ? $lot->statut->value
                                    : (string) $lot->statut;

                                $statutLotLabel = $lot->statut instanceof \App\Enums\StatutLot
                                    ? $lot->statut->libelle()
                                    : ucfirst(str_replace('_', ' ', $statutLotValue));

                                /*
                                 * Article associé.
                                 */
                                $article = $lot->stock;

                                /*
                                 * Date d'entrée.
                                 */
                                $dateEntree = $lot->date_entree;

                                /*
                                 * Expiration.
                                 */
                                $dateExpiration = $lot->date_expiration;

                                $expirationCarbon = $dateExpiration
                                    ? \Carbon\Carbon::parse($dateExpiration)
                                    : null;

                                $expirationEstPassee = $expirationCarbon
                                    ? $expirationCarbon->isPast()
                                    : false;

                                $expirationBientot = $expirationCarbon
                                    && !$expirationEstPassee
                                    && now()->diffInDays($expirationCarbon, false) <= 30;
                            @endphp

                            <tr>

                                {{-- LOT --}}
                                <td>

                                    <div class="stock-lots-page__lot">

                                        <div class="stock-lots-page__lot-icon">
                                            <i class="fas fa-box" aria-hidden="true"></i>
                                        </div>

                                        <div class="stock-lots-page__lot-content">

                                            <strong>
                                                {{ $lot->numero_lot ?? ('LOT-' . $lot->id) }}
                                            </strong>

                                            <span>
                                                ID #{{ $lot->id }}
                                            </span>

                                        </div>

                                    </div>

                                </td>


                                {{-- ARTICLE --}}
                                <td>

                                    @if($article)

                                        <div class="stock-lots-page__article">

                                            <strong>
                                                {{ $article->designation }}
                                            </strong>

                                            @if($article->reference)

                                                <span>
                                                    {{ $article->reference }}
                                                </span>

                                            @endif

                                        </div>

                                    @else

                                        <span class="stock-lots-page__muted">
                                            Article indisponible
                                        </span>

                                    @endif

                                </td>


                                {{-- DATE ENTRÉE --}}
                                <td>

                                    @if($dateEntree)

                                        <span class="stock-lots-page__date">
                                            {{ \Carbon\Carbon::parse($dateEntree)->format('d/m/Y') }}
                                        </span>

                                    @else

                                        <span class="stock-lots-page__muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- EXPIRATION --}}
                                <td>

                                    @if($expirationCarbon)

                                        <div class="stock-lots-page__expiration
                                            {{ $expirationEstPassee
                                                ? 'is-expired'
                                                : ($expirationBientot
                                                    ? 'is-soon'
                                                    : 'is-valid') }}">

                                            <i
                                                class="fas {{
                                                    $expirationEstPassee
                                                        ? 'fa-calendar-times'
                                                        : ($expirationBientot
                                                            ? 'fa-calendar-day'
                                                            : 'fa-calendar-check')
                                                }}"
                                                aria-hidden="true"
                                            ></i>

                                            <span>
                                                {{ $expirationCarbon->format('d/m/Y') }}
                                            </span>

                                        </div>

                                    @else

                                        <span class="stock-lots-page__muted">
                                            Sans expiration
                                        </span>

                                    @endif

                                </td>


                                {{-- QUANTITÉ ACTUELLE --}}
                                <td class="stock-lots-page__table-number">

                                    <strong class="stock-lots-page__quantity">
                                        {{ number_format(
                                            (float) $lot->quantite_actuelle,
                                            2,
                                            ',',
                                            ' '
                                        ) }}
                                    </strong>

                                    <span class="stock-lots-page__quantity-unit">
                                        {{ $article?->unite ?? '' }}
                                    </span>

                                </td>


                                {{-- ÉTAT --}}
                                <td>

                                    @switch($statutLotValue)

                                        @case('actif')

                                            <span class="stock-lots-page__status stock-lots-page__status--success">

                                                <span class="stock-lots-page__status-dot"></span>

                                                Actif

                                            </span>

                                            @break


                                        @case('bloque')

                                            <span class="stock-lots-page__status stock-lots-page__status--warning">

                                                <span class="stock-lots-page__status-dot"></span>

                                                Bloqué

                                            </span>

                                            @break


                                        @case('expire')

                                            <span class="stock-lots-page__status stock-lots-page__status--danger">

                                                <span class="stock-lots-page__status-dot"></span>

                                                Expiré

                                            </span>

                                            @break


                                        @case('epuise')

                                            <span class="stock-lots-page__status stock-lots-page__status--default">

                                                <span class="stock-lots-page__status-dot"></span>

                                                Épuisé

                                            </span>

                                            @break


                                        @default

                                            <span class="stock-lots-page__status stock-lots-page__status--default">

                                                <span class="stock-lots-page__status-dot"></span>

                                                {{ $statutLotLabel }}

                                            </span>

                                    @endswitch

                                </td>


                                {{-- ACTIONS --}}
                                <td class="stock-lots-page__table-actions">

                                    <div class="stock-lots-page__actions">

                                        <a
                                            href="{{ route('admin.stocks.lots.show', $lot) }}"
                                            class="stock-lots-page__action"
                                            title="Voir le lot"
                                            aria-label="Voir le lot {{ $lot->numero_lot ?? $lot->id }}"
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

                <div class="stock-lots-page__pagination">

                    <div class="stock-lots-page__pagination-info">

                        Affichage de
                        <strong>{{ $lots->firstItem() }}</strong>
                        à
                        <strong>{{ $lots->lastItem() }}</strong>
                        sur
                        <strong>{{ $lots->total() }}</strong>
                        lots

                    </div>

                    <div class="stock-lots-page__pagination-links">

                        {{ $lots->withQueryString()->links() }}

                    </div>

                </div>

            @endif


        @else

            {{-- ÉTAT VIDE --}}
            <div class="stock-lots-page__empty">

                <div class="stock-lots-page__empty-icon">
                    <i class="fas fa-box-open" aria-hidden="true"></i>
                </div>

                <h3>
                    Aucun lot trouvé
                </h3>

                @if(request()->hasAny([
                    'recherche',
                    'statut',
                    'stock_id'
                ]))

                    <p>
                        Aucun lot ne correspond aux critères de recherche
                        sélectionnés.
                    </p>

                    <a
                        href="{{ route('admin.stocks.lots.index') }}"
                        class="stock-lots-page__button stock-lots-page__button--secondary"
                    >
                        <i class="fas fa-times" aria-hidden="true"></i>
                        Réinitialiser les filtres
                    </a>

                @else

                    <p>
                        Aucun lot n'est actuellement enregistré dans
                        le système.
                    </p>

                @endif

            </div>

        @endif

    </div>


    {{-- =========================================================
         INFORMATION FEFO
    ========================================================== --}}
    <div class="stock-lots-page__info-card">

        <div class="stock-lots-page__info-icon">
            <i class="fas fa-sort-amount-down" aria-hidden="true"></i>
        </div>

        <div class="stock-lots-page__info-content">

            <h3>
                Gestion FEFO
            </h3>

            <p>
                Lors d’une sortie de stock, les lots disponibles sont
                sélectionnés selon le principe <strong>FEFO</strong>
                (« First Expired, First Out ») afin de privilégier les
                lots dont la date d’expiration est la plus proche.
            </p>

        </div>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/lots-index.js') }}"
        defer
    ></script>

@endsection