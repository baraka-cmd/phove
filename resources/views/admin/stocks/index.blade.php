@extends('layouts.admin')

@section('title', 'Gestion des stocks')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/index.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stocks-page">

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-text">

                <div class="page-header-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>

                <div>
                    <h1 class="page-title">
                        Gestion des stocks
                    </h1>

                    <p class="page-subtitle">
                        Consultez et gérez les articles, leurs disponibilités
                        et leur état dans les différents stocks de PHOVE.
                    </p>
                </div>

            </div>

            <div class="page-header-actions">

                @can('create', App\Models\Stock::class)

                    <a
                        href="{{ route('admin.stocks.create') }}"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-plus"></i>

                        <span>
                            Nouvel article
                        </span>
                    </a>

                @endcan

            </div>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES DE SESSION
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success" role="alert">

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

        <div class="alert alert-danger" role="alert">

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
         STATISTIQUES
         ------------------------------------------------
         $stats est fourni par StockController::index()
         via StockService::statistiques().

         Clés attendues :
           - articles_actifs
           - articles_inactifs
           - articles_archives
           - ruptures
           - stocks_faibles
           - quantite_physique
           - quantite_reservee
           - quantite_disponible
    ========================================================== --}}
    <section
        class="stock-kpi-grid"
        aria-label="Statistiques des stocks"
    >

        {{-- ARTICLES ACTIFS --}}
        <article class="stock-kpi-card">

            <div class="stock-kpi-icon">
                <i class="fas fa-box"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Articles actifs
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format($stats['articles_actifs'] ?? 0, 0, ',', ' ') }}
                </strong>

            </div>

        </article>


        {{-- STOCK FAIBLE --}}
        <article class="stock-kpi-card stock-kpi-warning">

            <div class="stock-kpi-icon">
                <i class="fas fa-triangle-exclamation"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Stock faible
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format($stats['stocks_faibles'] ?? 0, 0, ',', ' ') }}
                </strong>

            </div>

        </article>


        {{-- RUPTURES --}}
        <article class="stock-kpi-card stock-kpi-danger">

            <div class="stock-kpi-icon">
                <i class="fas fa-circle-xmark"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Ruptures
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format($stats['ruptures'] ?? 0, 0, ',', ' ') }}
                </strong>

            </div>

        </article>


        {{-- QUANTITÉ PHYSIQUE --}}
        <article class="stock-kpi-card">

            <div class="stock-kpi-icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Quantité physique
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format(
                        (float) ($stats['quantite_physique'] ?? 0),
                        2,
                        ',',
                        ' '
                    ) }}
                </strong>

            </div>

        </article>


        {{-- QUANTITÉ RÉSERVÉE --}}
        <article class="stock-kpi-card stock-kpi-info">

            <div class="stock-kpi-icon">
                <i class="fas fa-bookmark"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Quantité réservée
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format(
                        (float) ($stats['quantite_reservee'] ?? 0),
                        2,
                        ',',
                        ' '
                    ) }}
                </strong>

            </div>

        </article>


        {{-- QUANTITÉ DISPONIBLE --}}
        <article class="stock-kpi-card stock-kpi-success">

            <div class="stock-kpi-icon">
                <i class="fas fa-box-open"></i>
            </div>

            <div class="stock-kpi-content">

                <span class="stock-kpi-label">
                    Quantité disponible
                </span>

                <strong class="stock-kpi-value">
                    {{ number_format(
                        (float) ($stats['quantite_disponible'] ?? 0),
                        2,
                        ',',
                        ' '
                    ) }}
                </strong>

            </div>

        </article>

    </section>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}
    <section class="stock-filter-card">

        <div class="stock-filter-header">

            <div class="stock-filter-title">

                <i class="fas fa-filter"></i>

                <div>

                    <h2>
                        Rechercher dans les stocks
                    </h2>

                    <p>
                        Filtrez les articles selon vos besoins.
                    </p>

                </div>

            </div>


            @if(
                request()->filled('recherche')
                || request()->filled('categorie')
                || request()->filled('statut')
            )

                <a
                    href="{{ route('admin.stocks.index') }}"
                    class="btn btn-light btn-reset-filters"
                >
                    <i class="fas fa-rotate-left"></i>

                    Réinitialiser
                </a>

            @endif

        </div>


        <form
            action="{{ route('admin.stocks.index') }}"
            method="GET"
            class="stock-filter-form"
            id="stock-filter-form"
        >

            {{-- RECHERCHE --}}
            <div class="form-group form-group-search">

                <label for="recherche">
                    Recherche
                </label>

                <div class="input-icon-wrapper">

                    <i class="fas fa-magnifying-glass"></i>

                    <input
                        type="search"
                        name="recherche"
                        id="recherche"
                        class="form-control"
                        value="{{ request('recherche') }}"
                        placeholder="Référence ou désignation..."
                        autocomplete="off"
                    >

                </div>

            </div>


            {{-- CATÉGORIE --}}
            <div class="form-group">

                <label for="categorie">
                    Catégorie
                </label>

                <select
                    name="categorie"
                    id="categorie"
                    class="form-control"
                >

                    <option value="">
                        Toutes les catégories
                    </option>

                    @foreach($categories as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected(request('categorie') === $value)
                        >
                            {{ $label }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- STATUT --}}
            <div class="form-group">

                <label for="statut">
                    Statut
                </label>

                <select
                    name="statut"
                    id="statut"
                    class="form-control"
                >

                    <option value="">
                        Tous les statuts
                    </option>

                    @foreach($statuts as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected(request('statut') === $value)
                        >
                            {{ $label }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- BOUTONS --}}
            <div class="stock-filter-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="fas fa-magnifying-glass"></i>

                    Rechercher
                </button>

            </div>

        </form>

    </section>


    {{-- =========================================================
         LISTE DES ARTICLES
    ========================================================== --}}
    <section class="stock-table-card">

        <div class="stock-table-header">

            <div>

                <h2 class="stock-table-title">
                    Articles en stock
                </h2>

                <p class="stock-table-subtitle">

                    @if($stocks->total() > 0)

                        {{ number_format($stocks->total(), 0, ',', ' ') }}
                        article(s) trouvé(s).

                    @else

                        Aucun article trouvé.

                    @endif

                </p>

            </div>


            <div class="stock-table-tools">

                <span class="stock-result-count">

                    Page {{ $stocks->currentPage() }}
                    sur {{ $stocks->lastPage() }}

                </span>

            </div>

        </div>


        @if($stocks->count() > 0)

            <div class="table-responsive">

                <table class="data-table stocks-table">

                    <thead>

                        <tr>

                            <th scope="col">
                                Référence
                            </th>

                            <th scope="col">
                                Article
                            </th>

                            <th scope="col">
                                Catégorie
                            </th>

                            <th scope="col">
                                Stock physique
                            </th>

                            <th scope="col">
                                Seuil d'alerte
                            </th>

                            <th scope="col">
                                État
                            </th>

                            <th scope="col">
                                Statut
                            </th>

                            <th
                                scope="col"
                                class="text-right"
                            >
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($stocks as $stock)

                            @php
                                /*
                                 * Quantité physique : fournie par
                                 * withSum('lots', 'quantite_actuelle')
                                 * dans StockController::index().
                                 *
                                 * StockService::paginer() applique cet agrégat.
                                 *
                                 * L'attribut retourné par Eloquent est :
                                 *   lots_sum_quantite_actuelle
                                 */
                                $quantitePhysique = (float) (
                                    $stock->lots_sum_quantite_actuelle ?? 0
                                );

                                /*
                                 * Statut de l'article : casté en
                                 * App\Enums\StatutStock par le modèle.
                                 */
                                $statut = $stock->statut;

                                /*
                                 * État calculé du stock : rupture,
                                 * faible ou disponible.
                                 *
                                 * ATTENTION : ici on ne connaît PAS la
                                 * quantité réservée (pas de withSum).
                                 * On utilise donc une approximation :
                                 * si quantité physique <= seuil → faible,
                                 * si quantité physique = 0 → rupture.
                                 *
                                 * Pour un état exact, il faut afficher la
                                 * quantité réservée via une sous-requête
                                 * ou un calcul dans le controller.
                                 */
                                $seuil = $stock->seuil_alerte !== null
                                    ? (float) $stock->seuil_alerte
                                    : null;

                                if ($quantitePhysique <= 0) {
                                    $etatStock = 'rupture';
                                } elseif ($seuil !== null && $quantitePhysique <= $seuil) {
                                    $etatStock = 'faible';
                                } else {
                                    $etatStock = 'disponible';
                                }
                            @endphp


                            <tr>

                                {{-- RÉFÉRENCE --}}
                                <td>

                                    <a
                                        href="{{ route('admin.stocks.show', $stock) }}"
                                        class="stock-reference"
                                    >
                                        {{ $stock->reference }}
                                    </a>

                                </td>


                                {{-- ARTICLE --}}
                                <td>

                                    <div class="stock-article-cell">

                                        <div class="stock-article-icon">
                                            <i class="fas fa-box"></i>
                                        </div>

                                        <div class="stock-article-info">

                                            <a
                                                href="{{ route('admin.stocks.show', $stock) }}"
                                                class="stock-article-name"
                                            >
                                                {{ $stock->designation }}
                                            </a>


                                            @if($stock->emplacementPrincipal)

                                                <span class="stock-location">

                                                    <i class="fas fa-location-dot"></i>

                                                    {{ $stock->emplacementPrincipal->nom }}

                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- CATÉGORIE --}}
                                <td>

                                    <span class="category-badge">

                                        {{ ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                (string) $stock->categorie
                                            )
                                        ) }}

                                    </span>

                                </td>


                                {{-- STOCK PHYSIQUE --}}
                                <td>

                                    <span class="quantity-value">

                                        {{ number_format(
                                            $quantitePhysique,
                                            2,
                                            ',',
                                            ' '
                                        ) }}

                                    </span>

                                    <span class="quantity-unit">
                                        {{ $stock->unite }}
                                    </span>

                                </td>


                                {{-- SEUIL D'ALERTE --}}
                                <td>

                                    @if($seuil !== null)

                                        <span class="quantity-value quantity-threshold">

                                            {{ number_format(
                                                $seuil,
                                                2,
                                                ',',
                                                ' '
                                            ) }}

                                        </span>

                                        <span class="quantity-unit">
                                            {{ $stock->unite }}
                                        </span>

                                    @else

                                        <span class="empty-value">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- ÉTAT DU STOCK --}}
                                <td>

                                    @switch($etatStock)

                                        @case('disponible')

                                            <span class="status-badge status-success">

                                                <i class="fas fa-circle-check"></i>

                                                Disponible

                                            </span>

                                            @break


                                        @case('faible')

                                            <span class="status-badge status-warning">

                                                <i class="fas fa-triangle-exclamation"></i>

                                                Stock faible

                                            </span>

                                            @break


                                        @case('rupture')

                                            <span class="status-badge status-danger">

                                                <i class="fas fa-circle-xmark"></i>

                                                Rupture

                                            </span>

                                            @break

                                    @endswitch

                                </td>


                                {{-- STATUT ARTICLE --}}
                                <td>

                                    @php
                                        $statutValue = $statut instanceof \App\Enums\StatutStock
                                            ? $statut->value
                                            : (string) $statut;
                                    @endphp

                                    @switch($statutValue)

                                        @case('actif')

                                            <span class="status-badge status-success">

                                                <i class="fas fa-circle-check"></i>

                                                {{ $statut->libelle() }}

                                            </span>

                                            @break


                                        @case('inactif')

                                            <span class="status-badge status-warning">

                                                <i class="fas fa-circle-pause"></i>

                                                {{ $statut->libelle() }}

                                            </span>

                                            @break


                                        @case('archive')

                                            <span class="status-badge status-neutral">

                                                <i class="fas fa-box-archive"></i>

                                                {{ $statut->libelle() }}

                                            </span>

                                            @break

                                    @endswitch

                                </td>


                                {{-- ACTIONS --}}
                                <td class="text-right">

                                    <div class="stock-actions">

                                        {{-- VOIR --}}
                                        <a
                                            href="{{ route('admin.stocks.show', $stock) }}"
                                            class="action-btn action-btn-view"
                                            title="Consulter"
                                            aria-label="Consulter {{ $stock->designation }}"
                                        >

                                            <i class="fas fa-eye"></i>

                                        </a>


                                        {{-- MODIFIER --}}
                                        @can('update', $stock)

                                            <a
                                                href="{{ route('admin.stocks.edit', $stock) }}"
                                                class="action-btn action-btn-edit"
                                                title="Modifier"
                                                aria-label="Modifier {{ $stock->designation }}"
                                            >

                                                <i class="fas fa-pen"></i>

                                            </a>

                                        @endcan


                                        {{-- ARCHIVER --}}
                                        @can('archive', $stock)

                                            @if($statutValue !== 'archive')

                                                <button
                                                    type="button"
                                                    class="action-btn action-btn-archive"
                                                    title="Archiver"
                                                    aria-label="Archiver {{ $stock->designation }}"
                                                    data-open-archive-modal
                                                    data-stock-id="{{ $stock->id }}"
                                                    data-stock-name="{{ $stock->designation }}"
                                                >

                                                    <i class="fas fa-box-archive"></i>

                                                </button>

                                            @endif

                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            <div class="stock-pagination">

                {{ $stocks->withQueryString()->links() }}

            </div>


        @else

            {{-- ÉTAT VIDE --}}
            <div class="stock-empty-state">

                <div class="stock-empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>

                <h3>
                    Aucun article trouvé
                </h3>

                <p>
                    Aucun article ne correspond aux critères
                    de recherche actuels.
                </p>


                @if(
                    request()->filled('recherche')
                    || request()->filled('categorie')
                    || request()->filled('statut')
                )

                    <a
                        href="{{ route('admin.stocks.index') }}"
                        class="btn btn-light"
                    >

                        <i class="fas fa-rotate-left"></i>

                        Réinitialiser les filtres

                    </a>

                @else

                    @can('create', App\Models\Stock::class)

                        <a
                            href="{{ route('admin.stocks.create') }}"
                            class="btn btn-primary"
                        >

                            <i class="fas fa-plus"></i>

                            Créer le premier article

                        </a>

                    @endcan

                @endif

            </div>

        @endif

    </section>

</div>


{{-- =============================================================
     MODALE D'ARCHIVAGE
============================================================== --}}
<div
    class="modal-overlay"
    id="archive-stock-modal"
    data-modal
    hidden
>

    <div
        class="modal-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="archive-stock-title"
    >

        {{-- EN-TÊTE MODALE --}}
        <div class="modal-header">

            <div class="modal-title-wrapper">

                <div class="modal-icon modal-icon-warning">

                    <i class="fas fa-box-archive"></i>

                </div>

                <div>

                    <h2 id="archive-stock-title">
                        Archiver l'article
                    </h2>

                    <p>
                        Cette action désactive l'article
                        sans supprimer son historique.
                    </p>

                </div>

            </div>


            <button
                type="button"
                class="modal-close"
                data-close-modal
                aria-label="Fermer"
            >

                <i class="fas fa-xmark"></i>

            </button>

        </div>


        {{-- CORPS MODALE --}}
        <div class="modal-body">

            <p>

                Voulez-vous vraiment archiver

                <strong data-archive-stock-name>
                    cet article
                </strong>

                ?

            </p>


            <p class="modal-warning-text">

                L'article ne sera pas supprimé physiquement.
                Son historique de stock restera conservé.

            </p>

        </div>


        {{-- PIED MODALE --}}
        <div class="modal-footer">

            <button
                type="button"
                class="btn btn-light"
                data-close-modal
            >

                Annuler

            </button>


            <form
                method="POST"
                id="archive-stock-form"
                action=""
            >

                @csrf

                @method('PATCH')


                <button
                    type="submit"
                    class="btn btn-warning"
                >

                    <i class="fas fa-box-archive"></i>

                    Archiver

                </button>

            </form>

        </div>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/index.js') }}"
        defer>
    </script>

@endsection