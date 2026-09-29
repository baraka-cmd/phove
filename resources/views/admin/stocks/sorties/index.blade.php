@extends('layouts.admin')

@section('title', 'Bons de sortie')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/sorties-index.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-sorties-page">

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-title">

                <div class="page-header-icon">
                    <i class="fas fa-file-export" aria-hidden="true"></i>
                </div>

                <div>

                    <h1>
                        Bons de sortie
                    </h1>

                    <p>
                        Gestion des demandes, validations et sorties
                        physiques de stock.
                    </p>

                </div>

            </div>


            <div class="page-header-actions">

                @can('create', App\Models\BonSortie::class)

                    <a
                        href="{{ route('admin.stocks.sorties.create') }}"
                        class="btn btn-primary"
                    >

                        <i class="fas fa-plus" aria-hidden="true"></i>

                        <span>Nouveau bon de sortie</span>

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
                <i class="fas fa-check-circle" aria-hidden="true"></i>
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
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger" role="alert">

            <div class="alert-icon">
                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
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
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>

        </div>

    @endif


    {{-- =========================================================
         STATISTIQUES
         ------------------------------------------------
         $stats est fourni par BonSortieController::index().

         Clés attendues :
           - total
           - brouillon
           - soumis
           - valide
           - en_preparation
           - sorti
           - annule
    ========================================================== --}}
    <div class="stats-grid">

        {{-- TOTAL --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-file-alt" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    Total
                </span>

                <strong class="stat-card-value">
                    {{ number_format($stats['total'] ?? $bonsSortie->total(), 0, ',', ' ') }}
                </strong>

                <span class="stat-card-description">
                    Bons enregistrés
                </span>

            </div>

        </div>


        {{-- BROUILLONS --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-edit" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    Brouillons
                </span>

                <strong class="stat-card-value">
                    {{ number_format($stats['brouillon'] ?? 0, 0, ',', ' ') }}
                </strong>

                <span class="stat-card-description">
                    En préparation
                </span>

            </div>

        </div>


        {{-- À VALIDER --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-hourglass-half" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    À valider
                </span>

                <strong class="stat-card-value">
                    {{ number_format($stats['soumis'] ?? 0, 0, ',', ' ') }}
                </strong>

                <span class="stat-card-description">
                    En attente de validation
                </span>

            </div>

        </div>


        {{-- EN PRÉPARATION --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-box-open" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    En préparation
                </span>

                <strong class="stat-card-value">
                    {{ number_format($stats['en_preparation'] ?? 0, 0, ',', ' ') }}
                </strong>

                <span class="stat-card-description">
                    Sorties en cours
                </span>

            </div>

        </div>


        {{-- SORTIS --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-check-double" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    Sortis
                </span>

                <strong class="stat-card-value">
                    {{ number_format($stats['sorti'] ?? 0, 0, ',', ' ') }}
                </strong>

                <span class="stat-card-description">
                    Sorties réalisées
                </span>

            </div>

        </div>


        {{-- ANNULÉS --}}
        <div class="stat-card">

            <div class="stat-card-icon">
                <i class="fas fa-ban" aria-hidden="true"></i>
            </div>

            <div class="stat-card-content">

                <span class="stat-card-label">
                    Annulés
                </span>

                <strong class="stat-card-value">
                    {{ number_format($stats['annule'] ?? 0, 0, ',', ' ') }}
                </strong>

                <span class="stat-card-description">
                    Bons annulés
                </span>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}
    <div class="filters-card">

        <div class="filters-header">

            <div class="filters-title">

                <i class="fas fa-filter" aria-hidden="true"></i>

                <h2>
                    Rechercher et filtrer
                </h2>

            </div>

            <a
                href="{{ route('admin.stocks.sorties.index') }}"
                class="filters-reset"
            >

                <i class="fas fa-undo" aria-hidden="true"></i>

                Réinitialiser

            </a>

        </div>


        <form
            method="GET"
            action="{{ route('admin.stocks.sorties.index') }}"
            class="filters-form"
        >

            {{-- RECHERCHE --}}
            <div class="form-group">

                <label for="recherche">
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
                        placeholder="Numéro, destinataire, motif..."
                        autocomplete="off"
                    >

                </div>

            </div>


            {{-- STATUT --}}
            <div class="form-group">

                <label for="statut">
                    Statut
                </label>

                <select
                    id="statut"
                    name="statut"
                    class="form-control"
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


            {{-- PROJET --}}
            <div class="form-group">

                <label for="projet_id">
                    Projet
                </label>

                <select
                    id="projet_id"
                    name="projet_id"
                    class="form-control"
                >

                    <option value="">
                        Tous les projets
                    </option>

                    @foreach(($projets ?? []) as $projet)

                        <option
                            value="{{ $projet->id }}"
                            @selected(
                                (string) request('projet_id')
                                === (string) $projet->id
                            )
                        >
                            {{ $projet->nom ?? $projet->titre ?? ('Projet #' . $projet->id) }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- DATE DÉBUT --}}
            <div class="form-group">

                <label for="date_debut">
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

                <label for="date_fin">
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


            {{-- BOUTON --}}
            <div class="filters-submit">

                <button type="submit" class="btn btn-secondary">

                    <i class="fas fa-search" aria-hidden="true"></i>

                    Rechercher

                </button>

            </div>

        </form>

    </div>


    {{-- =========================================================
         TABLEAU
    ========================================================== --}}
    <div class="table-card">

        <div class="table-card-header">

            <div>

                <h2>
                    Liste des bons de sortie
                </h2>

                <p>
                    {{ $bonsSortie->total() }}
                    bon(s) trouvé(s)
                </p>

            </div>

        </div>


        @if($bonsSortie->count() > 0)

            <div class="table-responsive">

                <table class="data-table">

                    <thead>

                        <tr>

                            <th scope="col">
                                Numéro
                            </th>

                            <th scope="col">
                                Date
                            </th>

                            <th scope="col">
                                Demandeur
                            </th>

                            <th scope="col">
                                Destinataire
                            </th>

                            <th scope="col">
                                Projet
                            </th>

                            <th scope="col">
                                Articles
                            </th>

                            <th scope="col">
                                Statut
                            </th>

                            <th scope="col">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($bonsSortie as $bon)

                            @php
                                /*
                                 * Normalisation du statut.
                                 */
                                $statutValue = $bon->statut instanceof \App\Enums\StatutBonSortie
                                    ? $bon->statut->value
                                    : (string) $bon->statut;

                                $statutLabel = $bon->statut instanceof \App\Enums\StatutBonSortie
                                    ? $bon->statut->libelle()
                                    : ucfirst(str_replace('_', ' ', $statutValue));
                            @endphp

                            <tr>

                                {{-- NUMÉRO --}}
                                <td>

                                    <a
                                        href="{{ route('admin.stocks.sorties.show', $bon) }}"
                                        class="document-reference"
                                    >

                                        <strong>
                                            {{ $bon->numero }}
                                        </strong>

                                    </a>

                                </td>


                                {{-- DATE --}}
                                <td>

                                    <div class="date-cell">

                                        <span class="date-primary">
                                            {{ $bon->date_demande
                                                ? \Carbon\Carbon::parse($bon->date_demande)->format('d/m/Y')
                                                : '—'
                                            }}
                                        </span>

                                        @if($bon->created_at)

                                            <span class="date-secondary">
                                                Créé le
                                                {{ $bon->created_at->format('d/m/Y H:i') }}
                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- DEMANDEUR --}}
                                <td>

                                    @if($bon->demandeur)

                                        <div class="person-cell">

                                            <div class="person-avatar">

                                                {{ strtoupper(
                                                    mb_substr(
                                                        $bon->demandeur->name ?? 'U',
                                                        0,
                                                        1
                                                    )
                                                ) }}

                                            </div>

                                            <div class="person-info">

                                                <strong>
                                                    {{ $bon->demandeur->name
                                                        ?? trim(
                                                            ($bon->demandeur->prenom ?? '')
                                                            . ' '
                                                            . ($bon->demandeur->nom ?? '')
                                                        )
                                                    }}
                                                </strong>

                                            </div>

                                        </div>

                                    @else

                                        <span class="empty-value">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- DESTINATAIRE --}}
                                <td>

                                    <span class="destination-cell">
                                        {{ $bon->destinataire ?: '—' }}
                                    </span>

                                </td>


                                {{-- PROJET --}}
                                <td>

                                    @if($bon->projet)

                                        <span class="project-badge">

                                            <i class="fas fa-project-diagram" aria-hidden="true"></i>

                                            {{ $bon->projet->nom
                                                ?? $bon->projet->titre
                                                ?? 'Projet' }}

                                        </span>

                                    @else

                                        <span class="empty-value">
                                            Aucun projet
                                        </span>

                                    @endif

                                </td>


                                {{-- ARTICLES --}}
                                <td>

                                    <div class="items-cell">

                                        <strong>
                                            {{ $bon->lignes_count ?? $bon->lignes?->count() ?? 0 }}
                                        </strong>

                                        <span>
                                            article(s)
                                        </span>

                                    </div>

                                </td>


                                {{-- STATUT --}}
                                <td>

                                    <span class="status-badge status-{{ $statutValue }}">

                                        {{ $statutLabel }}

                                    </span>

                                </td>


                                {{-- ACTIONS --}}
                                <td>

                                    <div class="table-actions">

                                        {{-- VOIR --}}
                                        <a
                                            href="{{ route('admin.stocks.sorties.show', $bon) }}"
                                            class="action-button action-view"
                                            title="Voir le bon"
                                            aria-label="Voir le bon {{ $bon->numero }}"
                                        >

                                            <i class="fas fa-eye" aria-hidden="true"></i>

                                        </a>


                                        {{-- SOUMETTRE --}}
                                        @if($statutValue === 'brouillon')

                                            @can('submit', $bon)

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'admin.stocks.sorties.soumettre',
                                                        $bon
                                                    ) }}"
                                                    class="inline-action-form"
                                                >

                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="action-button action-submit"
                                                        title="Soumettre le bon"
                                                        aria-label="Soumettre {{ $bon->numero }}"
                                                    >

                                                        <i class="fas fa-paper-plane" aria-hidden="true"></i>

                                                    </button>

                                                </form>

                                            @endcan

                                        @endif


                                        {{-- VALIDER --}}
                                        @if($statutValue === 'soumis')

                                            @can('validate', $bon)

                                                <a
                                                    href="{{ route(
                                                        'admin.stocks.sorties.validation',
                                                        $bon
                                                    ) }}"
                                                    class="action-button action-validate"
                                                    title="Valider le bon"
                                                    aria-label="Valider {{ $bon->numero }}"
                                                >

                                                    <i class="fas fa-check" aria-hidden="true"></i>

                                                </a>

                                            @endcan

                                        @endif


                                        {{-- PRÉPARER --}}
                                        @if($statutValue === 'valide')

                                            @can('prepare', $bon)

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'admin.stocks.sorties.preparation.post',
                                                        $bon
                                                    ) }}"
                                                    class="inline-action-form"
                                                >

                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="action-button action-prepare"
                                                        title="Préparer la sortie"
                                                        aria-label="Préparer {{ $bon->numero }}"
                                                    >

                                                        <i class="fas fa-box-open" aria-hidden="true"></i>

                                                    </button>

                                                </form>

                                            @endcan

                                        @endif


                                        {{-- SORTIR --}}
                                        @if($statutValue === 'en_preparation')

                                            @can('release', $bon)

                                                <a
                                                    href="{{ route(
                                                        'admin.stocks.sorties.preparation',
                                                        $bon
                                                    ) }}"
                                                    class="action-button action-out"
                                                    title="Effectuer la sortie"
                                                    aria-label="Effectuer la sortie {{ $bon->numero }}"
                                                >

                                                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>

                                                </a>

                                            @endcan

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if($bonsSortie->hasPages())

                <div class="pagination-wrapper">

                    {{ $bonsSortie->withQueryString()->links() }}

                </div>

            @endif

        @else

            {{-- ÉTAT VIDE --}}
            <div class="empty-state">

                <div class="empty-state-icon">

                    <i class="fas fa-file-export" aria-hidden="true"></i>

                </div>

                <h3>
                    Aucun bon de sortie trouvé
                </h3>

                @if(request()->hasAny([
                    'recherche',
                    'statut',
                    'projet_id',
                    'date_debut',
                    'date_fin'
                ]))

                    <p>
                        Aucun bon ne correspond aux critères
                        de recherche sélectionnés.
                    </p>

                    <a
                        href="{{ route('admin.stocks.sorties.index') }}"
                        class="btn btn-secondary"
                    >

                        <i class="fas fa-undo" aria-hidden="true"></i>

                        Réinitialiser les filtres

                    </a>

                @else

                    <p>
                        Aucun bon de sortie n’a encore été enregistré.
                    </p>

                    @can('create', App\Models\BonSortie::class)

                        <a
                            href="{{ route('admin.stocks.sorties.create') }}"
                            class="btn btn-primary"
                        >

                            <i class="fas fa-plus" aria-hidden="true"></i>

                            Créer le premier bon

                        </a>

                    @endcan

                @endif

            </div>

        @endif

    </div>

</div>

@endsection