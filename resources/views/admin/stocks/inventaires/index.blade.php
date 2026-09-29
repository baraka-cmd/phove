@extends('layouts.admin')

@section('title', 'Inventaires de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/inventaires-index.css') }}"
    >
@endsection

@section('content')

<div class="admin-content">

    <div class="stock-inventaires-page">

        {{-- =========================================================
             EN-TÊTE
        ========================================================== --}}
        <header class="stock-inventaires-header">

            <div class="stock-inventaires-header__main">

                <nav
                    class="stock-inventaires-breadcrumb"
                    aria-label="Fil d’Ariane"
                >

                    <a href="{{ route('admin.stocks.index') }}">
                        Stocks
                    </a>

                    <span aria-hidden="true">/</span>

                    <span>Inventaires</span>

                </nav>


                <div class="stock-inventaires-title-wrapper">

                    <div class="stock-inventaires-title-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>

                    <div>

                        <h1 class="stock-inventaires-title">
                            Inventaires de stock
                        </h1>

                        <p class="stock-inventaires-subtitle">
                            Contrôlez les quantités physiques, identifiez les écarts
                            et validez les ajustements de stock.
                        </p>

                    </div>

                </div>

            </div>


            <div class="stock-inventaires-header__actions">

                @can('create', App\Models\InventaireStock::class)

                    <a
                        href="{{ route('admin.stocks.inventaires.create') }}"
                        class="stock-inventaires-button stock-inventaires-button--primary"
                    >

                        <i class="fas fa-plus"></i>

                        <span>Nouvel inventaire</span>

                    </a>

                @endcan

            </div>

        </header>


        {{-- =========================================================
             MESSAGES DE SESSION
        ========================================================== --}}
        @if(session('success'))

            <div
                class="stock-inventaires-alert stock-inventaires-alert--success"
                data-auto-dismiss="6000"
                role="alert"
            >

                <div class="stock-inventaires-alert__icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="stock-inventaires-alert__content">

                    <strong>Opération réussie</strong>

                    <p>{{ session('success') }}</p>

                </div>

                <button
                    type="button"
                    class="stock-inventaires-alert__close"
                    aria-label="Fermer"
                    onclick="this.closest('.stock-inventaires-alert').hidden = true;"
                >
                    <i class="fas fa-times"></i>
                </button>

            </div>

        @endif


        @if(session('error'))

            <div
                class="stock-inventaires-alert stock-inventaires-alert--danger"
                data-auto-dismiss="8000"
                role="alert"
            >

                <div class="stock-inventaires-alert__icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>

                <div class="stock-inventaires-alert__content">

                    <strong>Opération impossible</strong>

                    <p>{{ session('error') }}</p>

                </div>

                <button
                    type="button"
                    class="stock-inventaires-alert__close"
                    aria-label="Fermer"
                    onclick="this.closest('.stock-inventaires-alert').hidden = true;"
                >
                    <i class="fas fa-times"></i>
                </button>

            </div>

        @endif


        {{-- =========================================================
             ERREURS DE VALIDATION
        ========================================================== --}}
        @if($errors->any())

            <div
                class="stock-inventaires-alert stock-inventaires-alert--danger"
                role="alert"
            >

                <div class="stock-inventaires-alert__icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div class="stock-inventaires-alert__content">

                    <strong>Vérifiez les informations</strong>

                    <ul class="stock-inventaires-error-list">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            </div>

        @endif


        {{-- =========================================================
             STATISTIQUES
             ------------------------------------------------
             $stats est fourni par InventaireStockController::index()
             via InventaireStockService::statistiques().

             Clés attendues :
               - total
               - en_cours
               - avec_ecarts
               - valides
        ========================================================== --}}
        <section
            class="stock-inventaires-stats"
            aria-label="Statistiques des inventaires"
        >

            {{-- TOTAL --}}
            <article class="stock-inventaires-stat-card">

                <div class="stock-inventaires-stat-card__icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>

                <div class="stock-inventaires-stat-card__content">

                    <span class="stock-inventaires-stat-card__label">
                        Total inventaires
                    </span>

                    <strong class="stock-inventaires-stat-card__value">
                        {{ number_format((int) ($stats['total'] ?? $inventaires->total()), 0, ',', ' ') }}
                    </strong>

                    <span class="stock-inventaires-stat-card__help">
                        Toutes périodes
                    </span>

                </div>

            </article>


            {{-- EN COURS --}}
            <article class="stock-inventaires-stat-card">

                <div class="stock-inventaires-stat-card__icon stock-inventaires-stat-card__icon--warning">
                    <i class="fas fa-hourglass-half"></i>
                </div>

                <div class="stock-inventaires-stat-card__content">

                    <span class="stock-inventaires-stat-card__label">
                        En cours
                    </span>

                    <strong class="stock-inventaires-stat-card__value">
                        {{ number_format((int) ($stats['en_cours'] ?? 0), 0, ',', ' ') }}
                    </strong>

                    <span class="stock-inventaires-stat-card__help">
                        Comptage ou préparation
                    </span>

                </div>

            </article>


            {{-- AVEC ÉCARTS --}}
            <article class="stock-inventaires-stat-card">

                <div class="stock-inventaires-stat-card__icon stock-inventaires-stat-card__icon--danger">
                    <i class="fas fa-exchange-alt"></i>
                </div>

                <div class="stock-inventaires-stat-card__content">

                    <span class="stock-inventaires-stat-card__label">
                        Avec écarts
                    </span>

                    <strong class="stock-inventaires-stat-card__value">
                        {{ number_format((int) ($stats['avec_ecarts'] ?? 0), 0, ',', ' ') }}
                    </strong>

                    <span class="stock-inventaires-stat-card__help">
                        Nécessitent une analyse
                    </span>

                </div>

            </article>


            {{-- VALIDÉS --}}
            <article class="stock-inventaires-stat-card">

                <div class="stock-inventaires-stat-card__icon stock-inventaires-stat-card__icon--success">
                    <i class="fas fa-check-double"></i>
                </div>

                <div class="stock-inventaires-stat-card__content">

                    <span class="stock-inventaires-stat-card__label">
                        Validés
                    </span>

                    <strong class="stock-inventaires-stat-card__value">
                        {{ number_format((int) ($stats['valides'] ?? 0), 0, ',', ' ') }}
                    </strong>

                    <span class="stock-inventaires-stat-card__help">
                        Ajustements enregistrés
                    </span>

                </div>

            </article>

        </section>


        {{-- =========================================================
             FILTRES
        ========================================================== --}}
        <section class="stock-inventaires-card">

            <div class="stock-inventaires-card__header">

                <div>

                    <h2 class="stock-inventaires-card__title">
                        Rechercher un inventaire
                    </h2>

                    <p class="stock-inventaires-card__description">
                        Filtrez les inventaires par référence, période ou statut.
                    </p>

                </div>

                <div class="stock-inventaires-card__header-icon">
                    <i class="fas fa-filter"></i>
                </div>

            </div>


            <div class="stock-inventaires-card__body">

                <form
                    action="{{ route('admin.stocks.inventaires.index') }}"
                    method="GET"
                    class="stock-inventaires-filter-form"
                >

                    {{-- RECHERCHE --}}
                    <div class="stock-inventaires-filter-group">

                        <label for="recherche" class="stock-inventaires-filter-label">
                            Recherche
                        </label>

                        <div class="stock-inventaires-search-wrapper">

                            <i class="fas fa-search"></i>

                            <input
                                type="search"
                                id="recherche"
                                name="recherche"
                                value="{{ request('recherche') }}"
                                class="stock-inventaires-filter-input stock-inventaires-filter-input--search"
                                placeholder="Numéro, référence..."
                                autocomplete="off"
                            >

                        </div>

                    </div>


                    {{-- STATUT --}}
                    <div class="stock-inventaires-filter-group">

                        <label for="statut" class="stock-inventaires-filter-label">
                            Statut
                        </label>

                        <select
                            id="statut"
                            name="statut"
                            class="stock-inventaires-filter-input"
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


                    {{-- DATE DÉBUT --}}
                    <div class="stock-inventaires-filter-group">

                        <label for="date_debut" class="stock-inventaires-filter-label">
                            Du
                        </label>

                        <input
                            type="date"
                            id="date_debut"
                            name="date_debut"
                            value="{{ request('date_debut') }}"
                            class="stock-inventaires-filter-input"
                        >

                    </div>


                    {{-- DATE FIN --}}
                    <div class="stock-inventaires-filter-group">

                        <label for="date_fin" class="stock-inventaires-filter-label">
                            Au
                        </label>

                        <input
                            type="date"
                            id="date_fin"
                            name="date_fin"
                            value="{{ request('date_fin') }}"
                            class="stock-inventaires-filter-input"
                        >

                    </div>


                    {{-- ACTIONS --}}
                    <div class="stock-inventaires-filter-actions">

                        <button
                            type="submit"
                            class="stock-inventaires-button stock-inventaires-button--primary"
                        >

                            <i class="fas fa-search"></i>

                            <span>Rechercher</span>

                        </button>

                        <a
                            href="{{ route('admin.stocks.inventaires.index') }}"
                            class="stock-inventaires-button stock-inventaires-button--secondary"
                        >

                            <i class="fas fa-undo"></i>

                            <span>Réinitialiser</span>

                        </a>

                    </div>

                </form>

            </div>

        </section>


        {{-- =========================================================
             LISTE DES INVENTAIRES
        ========================================================== --}}
        <section class="stock-inventaires-card">

            <div class="stock-inventaires-card__header">

                <div>

                    <h2 class="stock-inventaires-card__title">
                        Liste des inventaires
                    </h2>

                    <p class="stock-inventaires-card__description">
                        Consultez le cycle complet des inventaires et leurs écarts.
                    </p>

                </div>

                <span class="stock-inventaires-count">

                    {{ $inventaires->total() }}
                    inventaire{{ $inventaires->total() > 1 ? 's' : '' }}

                </span>

            </div>


            @if($inventaires->count())

                <div class="stock-inventaires-table-wrapper">

                    <table class="stock-inventaires-table">

                        <thead>

                            <tr>

                                <th scope="col">Inventaire</th>
                                <th scope="col">Date</th>
                                <th scope="col">Responsable</th>
                                <th scope="col">Lignes</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($inventaires as $inventaire)

                                @php
                                    $statutInventaire = $inventaire->statut;

                                    $statutLabel = match ($statutInventaire) {
                                        'brouillon' => 'Brouillon',
                                        'en_cours'  => 'En cours',
                                        'termine'   => 'Terminé',
                                        'valide'    => 'Validé',
                                        'annule'    => 'Annulé',
                                        default     => ucfirst(str_replace('_', ' ', (string) $statutInventaire)),
                                    };

                                    $statutClass = match ($statutInventaire) {
                                        'brouillon' => 'neutral',
                                        'en_cours'  => 'warning',
                                        'termine'   => 'info',
                                        'valide'    => 'success',
                                        'annule'    => 'danger',
                                        default     => 'neutral',
                                    };

                                    $nombreLignes = $inventaire->lignes_count ?? 0;
                                @endphp

                                <tr>

                                    {{-- INVENTAIRE --}}
                                    <td>

                                        <div class="stock-inventaires-identity">

                                            <div class="stock-inventaires-identity__icon">
                                                <i class="fas fa-clipboard-check"></i>
                                            </div>

                                            <div class="stock-inventaires-identity__content">

                                                <a
                                                    href="{{ route('admin.stocks.inventaires.show', $inventaire) }}"
                                                    class="stock-inventaires-identity__number"
                                                >
                                                    {{ $inventaire->numero }}
                                                </a>

                                                <span class="stock-inventaires-muted">
                                                    ID #{{ $inventaire->id }}
                                                </span>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- DATE --}}
                                    <td>

                                        <div class="stock-inventaires-date">

                                            <strong>
                                                {{ $inventaire->date_debut
                                                    ? \Carbon\Carbon::parse($inventaire->date_debut)->format('d/m/Y')
                                                    : '—'
                                                }}
                                            </strong>

                                            @if($inventaire->created_at)

                                                <span>
                                                    Créé le
                                                    {{ $inventaire->created_at->format('d/m/Y') }}
                                                </span>

                                            @endif

                                        </div>

                                    </td>


                                    {{-- RESPONSABLE --}}
                                    <td>

                                        @if($inventaire->responsable)

                                            <div class="stock-inventaires-person">

                                                <div class="stock-inventaires-person__avatar">
                                                    <i class="fas fa-user"></i>
                                                </div>

                                                <div class="stock-inventaires-person__content">

                                                    <strong>
                                                        {{ $inventaire->responsable->name }}
                                                    </strong>

                                                    @if($inventaire->responsable->email)

                                                        <span>
                                                            {{ $inventaire->responsable->email }}
                                                        </span>

                                                    @endif

                                                </div>

                                            </div>

                                        @else

                                            <span class="stock-inventaires-muted">
                                                Non renseigné
                                            </span>

                                        @endif

                                    </td>


                                    {{-- LIGNES --}}
                                    <td>

                                        <div class="stock-inventaires-metric">

                                            <strong>
                                                {{ $nombreLignes }}
                                            </strong>

                                            <span>
                                                article{{ $nombreLignes > 1 ? 's' : '' }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- STATUT --}}
                                    <td>

                                        <span
                                            class="stock-inventaires-status stock-inventaires-status--{{ $statutClass }}"
                                        >

                                            <span class="stock-inventaires-status__dot"></span>

                                            {{ $statutLabel }}

                                        </span>

                                    </td>


                                    {{-- ACTIONS --}}
                                    <td>

                                        <div class="stock-inventaires-row-actions">

                                            <a
                                                href="{{ route('admin.stocks.inventaires.show', $inventaire) }}"
                                                class="stock-inventaires-row-action"
                                                title="Voir l'inventaire"
                                                aria-label="Voir l'inventaire {{ $inventaire->numero }}"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @can('validate', $inventaire)

                                                @if(!in_array($statutInventaire, ['valide', 'annule'], true))

                                                    <a
                                                        href="{{ route('admin.stocks.inventaires.show', $inventaire) }}"
                                                        class="stock-inventaires-row-action stock-inventaires-row-action--validate"
                                                        title="Contrôler et valider"
                                                        aria-label="Contrôler et valider {{ $inventaire->numero }}"
                                                    >
                                                        <i class="fas fa-check"></i>
                                                    </a>

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
                @if($inventaires->hasPages())

                    <div class="stock-inventaires-pagination">

                        {{ $inventaires->withQueryString()->links() }}

                    </div>

                @endif

            @else

                {{-- ÉTAT VIDE --}}
                <div class="stock-inventaires-empty">

                    <div class="stock-inventaires-empty__icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>

                    <h3>
                        Aucun inventaire trouvé
                    </h3>

                    <p>
                        Aucun inventaire ne correspond aux critères actuels.
                        Vous pouvez créer un nouvel inventaire pour commencer
                        un contrôle physique du stock.
                    </p>

                    @can('create', App\Models\InventaireStock::class)

                        <a
                            href="{{ route('admin.stocks.inventaires.create') }}"
                            class="stock-inventaires-button stock-inventaires-button--primary"
                        >

                            <i class="fas fa-plus"></i>

                            <span>Créer un inventaire</span>

                        </a>

                    @endcan

                </div>

            @endif

        </section>


        {{-- =========================================================
             INFORMATION MÉTIER
        ========================================================== --}}
        <aside class="stock-inventaires-info">

            <div class="stock-inventaires-info__icon">
                <i class="fas fa-shield-alt"></i>
            </div>

            <div class="stock-inventaires-info__content">

                <h3>
                    Contrôle sécurisé des stocks
                </h3>

                <p>
                    La quantité système n’est jamais modifiée directement
                    depuis cette interface. Après le comptage physique,
                    les écarts sont contrôlés puis validés avant qu’un
                    mouvement d’ajustement ne soit enregistré dans
                    l’historique du stock.
                </p>

            </div>

        </aside>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/inventaires-index.js') }}"
        defer>
    </script>

@endsection