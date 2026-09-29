@extends('layouts.admin')

@section('title', 'Gestion des salaires')

{{-- ============================================================
     CSS SPÉCIFIQUE À LA PAGE
============================================================ --}}
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/salaires/index.css') }}">
@endsection


{{-- ============================================================
     CONTENU
============================================================ --}}
@section('content')

<div class="salary-index">

    {{-- ========================================================
         EN-TÊTE DE PAGE
    ========================================================= --}}
    <div class="page-header">

        <div class="page-header-content">

            <h1 class="page-title">
                <i class="fas fa-money-check-alt"></i>
                Gestion des salaires
            </h1>

            <p class="page-subtitle">
                Gestion des obligations salariales et suivi des paiements.
            </p>

        </div>


        <div class="page-actions">

            @can('create', App\Models\Salaire::class)

                <a href="{{ route('admin.salaires.create') }}"
                   class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nouveau salaire
                </a>

            @endcan


            <a href="{{ route('admin.salaires.grille') }}"
               class="btn btn-outline">
                <i class="fas fa-table"></i>
                Grille mensuelle
            </a>

        </div>

    </div>


    {{-- ========================================================
         MESSAGES DE SESSION
    ========================================================= --}}

    @if(session('success'))

        <div class="alert alert-success">

            <i class="fas fa-check-circle"></i>

            <span>
                {{ session('success') }}
            </span>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">

            <i class="fas fa-exclamation-circle"></i>

            <span>
                {{ session('error') }}
            </span>

        </div>

    @endif


    {{-- ========================================================
         ERREURS DE VALIDATION
    ========================================================= --}}

    @if($errors->any())

        <div class="validation-errors">

            <div class="validation-errors-title">

                <i class="fas fa-exclamation-triangle"></i>

                <strong>
                    Impossible d'effectuer l'opération.
                </strong>

            </div>


            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================
         STATISTIQUES
    ========================================================= --}}

    <div class="stats-grid">

        {{-- Nombre de salaires --}}
        <div class="stat-card">

            <div class="stat-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Salaires
                </span>

                <strong class="stat-value">
                    {{ number_format($stats['total'] ?? 0, 0, ',', ' ') }}
                </strong>

                <span class="stat-meta">
                    Période sélectionnée
                </span>

            </div>

        </div>


        {{-- Masse salariale --}}
        <div class="stat-card">

            <div class="stat-icon">
                <i class="fas fa-coins"></i>
            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Masse salariale
                </span>

                <strong class="stat-value">
                    {{ number_format($stats['montant_total'] ?? 0, 2, ',', ' ') }}
                </strong>

                <span class="stat-meta">
                    Montant net
                </span>

            </div>

        </div>


        {{-- Montant validé --}}
        <div class="stat-card">

            <div class="stat-icon">
                <i class="fas fa-check-double"></i>
            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Montant validé
                </span>

                <strong class="stat-value">
                    {{ number_format($stats['montant_valide'] ?? 0, 2, ',', ' ') }}
                </strong>

                <span class="stat-meta">
                    Validé
                </span>

            </div>

        </div>


        {{-- Reste à payer --}}
        <div class="stat-card">

            <div class="stat-icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>

            <div class="stat-content">

                <span class="stat-label">
                    Reste à payer
                </span>

                <strong class="stat-value">
                    {{ number_format($stats['reste_a_payer'] ?? 0, 2, ',', ' ') }}
                </strong>

                <span class="stat-meta">
                    À décaisser
                </span>

            </div>

        </div>

    </div>


    {{-- ========================================================
         FILTRES
    ========================================================= --}}

    <div class="filters-card">

        <div class="table-header">

            <h2 class="table-title">
                <i class="fas fa-filter"></i>
                Filtres
            </h2>

            <a href="{{ route('admin.salaires.index') }}"
               class="btn btn-sm btn-outline">
                <i class="fas fa-undo"></i>
                Réinitialiser
            </a>

        </div>


        <form method="GET"
              action="{{ route('admin.salaires.index') }}"
              class="filters-form">

            {{-- Mois --}}
            <div class="filter-group">

                <label for="mois" class="filter-label">
                    Mois
                </label>

                <input
                    type="month"
                    id="mois"
                    name="mois"
                    value="{{ request('mois') }}"
                    class="filter-input"
                >

            </div>


            {{-- Statut --}}
            <div class="filter-group">

                <label for="statut" class="filter-label">
                    Statut
                </label>

                <select
                    id="statut"
                    name="statut"
                    class="filter-select"
                >

                    <option value="">
                        Tous les statuts
                    </option>

                    <option value="brouillon"
                        @selected(request('statut') === 'brouillon')>
                        Brouillon
                    </option>

                    <option value="valide"
                        @selected(request('statut') === 'valide')>
                        Validé
                    </option>

                    <option value="partiellement_paye"
                        @selected(request('statut') === 'partiellement_paye')>
                        Partiellement payé
                    </option>

                    <option value="paye"
                        @selected(request('statut') === 'paye')>
                        Payé
                    </option>

                    <option value="annule"
                        @selected(request('statut') === 'annule')>
                        Annulé
                    </option>

                </select>

            </div>


            {{-- Organe --}}
            <div class="filter-group">

                <label for="organe_id" class="filter-label">
                    Organe
                </label>

                <select
                    id="organe_id"
                    name="organe_id"
                    class="filter-select"
                >

                    <option value="">
                        Tous les organes
                    </option>

                    @isset($organes)

                        @foreach($organes as $organe)

                            <option
                                value="{{ $organe->id }}"
                                @selected(
                                    (string) request('organe_id')
                                    ===
                                    (string) $organe->id
                                )
                            >
                                {{ $organe->nom }}
                            </option>

                        @endforeach

                    @endisset

                </select>

            </div>


            {{-- Recherche --}}
            <div class="filter-group">

                <label for="recherche" class="filter-label">
                    Recherche
                </label>

                <input
                    type="text"
                    id="recherche"
                    name="recherche"
                    value="{{ request('recherche') }}"
                    class="filter-input"
                    placeholder="Nom, matricule, référence..."
                >

            </div>


            {{-- Actions --}}
            <div class="filter-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                    title="Rechercher"
                >
                    <i class="fas fa-search"></i>
                    Rechercher
                </button>

            </div>

        </form>

    </div>


    {{-- ========================================================
         LISTE DES SALAIRES
    ========================================================= --}}

    <div class="table-card">

        <div class="table-header">

            <div>

                <h2 class="table-title">
                    <i class="fas fa-list"></i>
                    Liste des salaires
                </h2>

                <span class="table-count">
                    {{ $salaires->total() ?? 0 }} résultat(s)
                </span>

            </div>

        </div>


        @if($salaires->count())

            <div class="table-wrapper">

                <table class="salary-table">

                    <thead>

                        <tr>

                            <th>
                                Référence
                            </th>

                            <th>
                                Membre
                            </th>

                            <th>
                                Organe
                            </th>

                            <th>
                                Mois
                            </th>

                            <th class="text-right">
                                Salaire net
                            </th>

                            <th>
                                Paiement
                            </th>

                            <th>
                                Statut
                            </th>

                            <th class="text-right">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($salaires as $salaire)

                            @php

                                $totalPaye = (float) (
                                    $salaire->paiements_sum_montant ?? 0
                                );

                                $net = (float) (
                                    $salaire->salaire_net ?? 0
                                );

                                $reste = max(
                                    $net - $totalPaye,
                                    0
                                );

                                $pourcentage = $net > 0
                                    ? min(
                                        ($totalPaye / $net) * 100,
                                        100
                                    )
                                    : 0;

                            @endphp


                            <tr>

                                {{-- Référence --}}
                                <td>

                                    <a
                                        href="{{ route('admin.salaires.show', $salaire) }}"
                                        class="salary-reference"
                                    >
                                        {{ $salaire->reference }}
                                    </a>

                                    <span class="text-muted">

                                        Créé le
                                        {{ optional($salaire->created_at)->format('d/m/Y') }}

                                    </span>

                                </td>


                                {{-- Membre --}}
                                <td>

                                    <div class="member-cell">

                                        <div class="member-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>

                                        <div class="member-info">

                                            @if($salaire->membre)

                                                <span class="member-name">

                                                    {{ $salaire->membre->nom_complet
                                                        ?? trim(
                                                            ($salaire->membre->nom ?? '') .
                                                            ' ' .
                                                            ($salaire->membre->post_nom ?? '') .
                                                            ' ' .
                                                            ($salaire->membre->prenom ?? '')
                                                        )
                                                    }}

                                                </span>


                                                @if(!empty($salaire->membre->telephone))

                                                    <span class="member-phone">
                                                        {{ $salaire->membre->telephone }}
                                                    </span>

                                                @elseif(!empty($salaire->membre->matricule))

                                                    <span class="member-phone">
                                                        {{ $salaire->membre->matricule }}
                                                    </span>

                                                @endif

                                            @else

                                                <span class="member-name">
                                                    Membre supprimé
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- Organe --}}
                                <td>

                                    @if($salaire->organe)

                                        <span class="organe-name">
                                            {{ $salaire->organe->nom }}
                                        </span>

                                    @else

                                        <span class="organe-empty">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Mois --}}
                                <td>

                                    @if($salaire->mois)

                                        {{ \Carbon\Carbon::createFromFormat(
                                            'Y-m',
                                            $salaire->mois
                                        )->translatedFormat('F Y') }}

                                    @else

                                        —

                                    @endif

                                </td>


                                {{-- Salaire net --}}
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


                                {{-- Paiement --}}
                                <td>

                                    <div class="payment-progress">

                                        <div class="payment-progress-header">

                                            <span class="payment-progress-label">
                                                Payé
                                            </span>

                                            <span class="payment-progress-value">
                                                {{ number_format($pourcentage, 0) }}%
                                            </span>

                                        </div>


                                        <div
                                            class="payment-progress-bar"
                                            role="progressbar"
                                            aria-valuenow="{{ $pourcentage }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                            data-percentage="{{ $pourcentage }}"
                                        >

                                            <div
                                                class="payment-progress-fill"
                                                data-width="{{ $pourcentage }}"
                                            ></div>

                                        </div>


                                        <div class="payment-progress-header">

                                            <span class="payment-progress-label">
                                                Payé
                                            </span>

                                            <span class="amount-paid">
                                                {{ number_format(
                                                    $totalPaye,
                                                    2,
                                                    ',',
                                                    ' '
                                                ) }}
                                            </span>

                                        </div>


                                        @if($reste > 0)

                                            <span class="amount-rest">

                                                Reste :
                                                {{ number_format(
                                                    $reste,
                                                    2,
                                                    ',',
                                                    ' '
                                                ) }}

                                            </span>

                                        @else

                                            <span class="amount-rest is-zero">

                                                <i class="fas fa-check"></i>
                                                Soldé

                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- Statut --}}
                                <td>

                                    @switch($salaire->statut)

                                        @case('brouillon')

                                            <span class="status-badge status-brouillon">
                                                <i class="fas fa-pencil-alt"></i>
                                                Brouillon
                                            </span>

                                            @break


                                        @case('valide')

                                            <span class="status-badge status-valide">
                                                <i class="fas fa-check"></i>
                                                Validé
                                            </span>

                                            @break


                                        @case('partiellement_paye')

                                            <span class="status-badge status-partiellement-paye">
                                                <i class="fas fa-hourglass-half"></i>
                                                Partiellement payé
                                            </span>

                                            @break


                                        @case('paye')

                                            <span class="status-badge status-paye">
                                                <i class="fas fa-check-double"></i>
                                                Payé
                                            </span>

                                            @break


                                        @case('annule')

                                            <span class="status-badge status-annule">
                                                <i class="fas fa-ban"></i>
                                                Annulé
                                            </span>

                                            @break


                                        @default

                                            <span class="status-badge">
                                                {{ $salaire->statut ?? 'Inconnu' }}
                                            </span>

                                    @endswitch

                                </td>


                                {{-- Actions --}}
                                <td class="text-right">

                                    <div class="row-actions">

                                        {{-- Voir --}}
                                        <a
                                            href="{{ route('admin.salaires.show', $salaire) }}"
                                            class="action-link action-view"
                                            title="Voir le salaire"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </a>


                                        {{-- Modifier --}}
                                        @can('update', $salaire)

                                            <a
                                                href="{{ route('admin.salaires.edit', $salaire) }}"
                                                class="action-link action-edit"
                                                title="Modifier"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </a>

                                        @endcan


                                        {{-- Paiement --}}
                                        @can('pay', $salaire)

                                            @if(
                                                in_array(
                                                    $salaire->statut,
                                                    [
                                                        'valide',
                                                        'partiellement_paye'
                                                    ],
                                                    true
                                                )
                                                && $reste > 0
                                            )

                                                <a
                                                    href="{{ route(
                                                        'admin.salaires.paiements.create',
                                                        $salaire
                                                    ) }}"
                                                    class="action-link action-pay"
                                                    title="Enregistrer un paiement"
                                                >
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </a>

                                            @endif

                                        @endcan


                                        {{-- Bulletin --}}
                                        @if($salaire->statut !== 'annule')

                                            <a
                                                href="{{ route(
                                                    'admin.salaires.bulletin',
                                                    $salaire
                                                ) }}"
                                                class="action-link"
                                                title="Bulletin"
                                            >
                                                <i class="fas fa-file-pdf"></i>
                                            </a>

                                        @endif


                                        {{-- Supprimer --}}
                                        @can('delete', $salaire)

                                            @if($salaire->statut === 'brouillon')

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'admin.salaires.destroy',
                                                        $salaire
                                                    ) }}"
                                                    onsubmit="return confirm('Voulez-vous vraiment supprimer ce brouillon ? Cette action est irréversible.');"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="action-link action-delete"
                                                        title="Supprimer"
                                                    >
                                                        <i class="fas fa-trash"></i>
                                                    </button>

                                                </form>

                                            @endif

                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


        @else

            {{-- ==================================================
                 ÉTAT VIDE
            =================================================== --}}

            <div class="empty-state">

                <div class="empty-state-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>

                <h3 class="empty-state-title">
                    Aucun salaire trouvé
                </h3>

                <p class="empty-state-text">
                    Aucun salaire ne correspond aux critères sélectionnés.
                </p>

                @can('create', App\Models\Salaire::class)

                    <a
                        href="{{ route('admin.salaires.create') }}"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-plus"></i>
                        Créer un salaire
                    </a>

                @endcan

            </div>

        @endif


        {{-- ========================================================
             PAGINATION
        ========================================================= --}}

        @if($salaires->hasPages())

            <div class="pagination-wrapper">

                <div class="pagination-info">
                    {{ $salaires->total() }} résultat(s)
                </div>

                {{ $salaires->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>

@endsection


{{-- ============================================================
     JAVASCRIPT SPÉCIFIQUE À LA PAGE
============================================================ --}}
@section('scripts')
    <script src="{{ asset('assets/js/admin/salaires/index.js') }}"></script>
@endsection