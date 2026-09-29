@extends('layouts.admin')

@section('title', 'Bon de sortie ' . $bonSortie->numero)

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/sorties-show.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-sortie-show">

    @php
        /*
         * Normalisation du statut.
         *
         * Le modèle caste `statut` en StatutBonSortie (BackedEnum).
         * On récupère la valeur string pour l'utiliser partout
         * dans la vue.
         */
        $statutValue = $bonSortie->statut instanceof \App\Enums\StatutBonSortie
            ? $bonSortie->statut->value
            : (string) $bonSortie->statut;

        $statutLabel = $bonSortie->statut instanceof \App\Enums\StatutBonSortie
            ? $bonSortie->statut->libelle()
            : ucfirst(str_replace('_', ' ', $statutValue));
    @endphp


    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-main">

                <div class="page-header-icon">
                    <i class="fas fa-file-export"></i>
                </div>

                <div>

                    <div class="page-header-eyebrow">
                        Gestion des stocks
                    </div>

                    <h1 class="page-title">
                        Bon de sortie
                    </h1>

                    <p class="page-description">
                        Consultation et suivi du bon de sortie
                        {{ $bonSortie->numero }}.
                    </p>

                </div>

            </div>

            <div class="page-header-actions">

                <a
                    href="{{ route('admin.stocks.sorties.index') }}"
                    class="btn btn-secondary"
                >

                    <i class="fas fa-arrow-left"></i>

                    <span>Retour</span>

                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ALERTES SESSION
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success">

            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <div class="alert-content">
                {{ session('success') }}
            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">

            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <div class="alert-content">
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         RÉSUMÉ
         ------------------------------------------------
         $resume est fourni par BonSortieController::show()
         via SortieStockService::resumeBon($bonSortie).

         Clés attendues :
           - nombre_lignes
           - statut
           - numero
           - date_demande
           - date_sortie
           - quantite_demandee
           - quantite_validee
           - quantite_sortie
           - reste_a_sortir
           - pourcentage_execution
           - entierement_sorti
           - partiellement_sorti
           - non_commence
    ========================================================== --}}
    <div class="summary-grid">

        {{-- RÉFÉRENCE --}}
        <div class="summary-card">

            <div class="summary-card-icon">
                <i class="fas fa-hashtag"></i>
            </div>

            <div class="summary-card-content">

                <span class="summary-label">
                    Référence
                </span>

                <strong class="summary-value">
                    {{ $bonSortie->numero }}
                </strong>

            </div>

        </div>


        {{-- DATE DE DEMANDE --}}
        <div class="summary-card">

            <div class="summary-card-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>

            <div class="summary-card-content">

                <span class="summary-label">
                    Date de demande
                </span>

                <strong class="summary-value">

                    {{ $bonSortie->date_demande
                        ? \Carbon\Carbon::parse($bonSortie->date_demande)->format('d/m/Y')
                        : '—'
                    }}

                </strong>

            </div>

        </div>


        {{-- ARTICLES --}}
        <div class="summary-card">

            <div class="summary-card-icon">
                <i class="fas fa-boxes"></i>
            </div>

            <div class="summary-card-content">

                <span class="summary-label">
                    Articles
                </span>

                <strong class="summary-value">
                    {{ $resume['nombre_lignes'] }}
                </strong>

            </div>

        </div>


        {{-- QUANTITÉ DEMANDÉE --}}
        <div class="summary-card">

            <div class="summary-card-icon">
                <i class="fas fa-cubes"></i>
            </div>

            <div class="summary-card-content">

                <span class="summary-label">
                    Quantité demandée
                </span>

                <strong class="summary-value">
                    {{ number_format(
                        (float) $resume['quantite_demandee'],
                        2,
                        ',',
                        ' '
                    ) }}
                </strong>

            </div>

        </div>

    </div>


    {{-- =========================================================
         STATUT COURANT
    ========================================================== --}}
    <div class="status-card status-card--{{ $statutValue }}">

        <div class="status-card-main">

            <div class="status-icon">

                @switch($statutValue)

                    @case('brouillon')
                        <i class="fas fa-pencil-alt"></i>
                        @break

                    @case('soumis')
                        <i class="fas fa-paper-plane"></i>
                        @break

                    @case('valide')
                        <i class="fas fa-check"></i>
                        @break

                    @case('en_preparation')
                        <i class="fas fa-box-open"></i>
                        @break

                    @case('sorti')
                        <i class="fas fa-truck"></i>
                        @break

                    @case('annule')
                        <i class="fas fa-ban"></i>
                        @break

                @endswitch

            </div>

            <div class="status-content">

                <span class="status-eyebrow">
                    État actuel
                </span>

                <strong class="status-value">
                    {{ $statutLabel }}
                </strong>

            </div>

        </div>


        {{-- BARRE DE PROGRESSION --}}
        @if($resume['quantite_validee'] > 0)

            <div class="status-progress">

                <div class="progress-header">

                    <span>
                        Exécution
                    </span>

                    <strong>
                        {{ number_format($resume['pourcentage_execution'], 0) }} %
                    </strong>

                </div>

                <div class="progress-bar">

                    <div
                        class="progress-fill"
                        style="width: {{ $resume['pourcentage_execution'] }}%;"
                    ></div>

                </div>

                <div class="progress-footer">

                    <span>

                        Sorti :
                        {{ number_format((float) $resume['quantite_sortie'], 2, ',', ' ') }}

                    </span>

                    <span>

                        Reste :
                        {{ number_format((float) $resume['reste_a_sortir'], 2, ',', ' ') }}

                    </span>

                </div>

            </div>

        @endif

    </div>


    {{-- =========================================================
         INFORMATIONS DOCUMENTAIRES
    ========================================================== --}}
    <div class="card information-card">

        <div class="card-header">

            <div>

                <span class="card-eyebrow">
                    Informations
                </span>

                <h2 class="card-title">
                    Détails du document
                </h2>

            </div>

            <i class="fas fa-info-circle card-header-icon"></i>

        </div>


        <div class="information-list">

            {{-- DEMANDEUR --}}
            <div class="information-row">

                <span class="information-label">

                    <i class="fas fa-user"></i>

                    Demandeur

                </span>

                <strong class="information-value">
                    {{ $bonSortie->demandeur?->name ?? '—' }}
                </strong>

            </div>


            {{-- PROJET --}}
            <div class="information-row">

                <span class="information-label">

                    <i class="fas fa-project-diagram"></i>

                    Projet

                </span>

                <strong class="information-value">

                    @if($bonSortie->projet)

                        {{ $bonSortie->projet->nom
                            ?? $bonSortie->projet->titre
                            ?? 'Projet' }}

                    @else

                        Aucun projet

                    @endif

                </strong>

            </div>


            {{-- VALIDATEUR --}}
            <div class="information-row">

                <span class="information-label">

                    <i class="fas fa-user-check"></i>

                    Validateur

                </span>

                <strong class="information-value">
                    {{ $bonSortie->validateur?->name ?? '—' }}
                </strong>

            </div>


            {{-- PRÉPARATEUR --}}
            <div class="information-row">

                <span class="information-label">

                    <i class="fas fa-user-cog"></i>

                    Préparateur

                </span>

                <strong class="information-value">
                    {{ $bonSortie->preparateur?->name ?? '—' }}
                </strong>

            </div>


            {{-- DATE DE VALIDATION --}}
            <div class="information-row">

                <span class="information-label">

                    <i class="fas fa-calendar-check"></i>

                    Date de validation

                </span>

                <strong class="information-value">

                    {{ $bonSortie->date_validation
                        ? \Carbon\Carbon::parse($bonSortie->date_validation)->format('d/m/Y')
                        : '—'
                    }}

                </strong>

            </div>


            {{-- DATE DE SORTIE --}}
            <div class="information-row">

                <span class="information-label">

                    <i class="fas fa-truck-loading"></i>

                    Date de sortie

                </span>

                <strong class="information-value">

                    {{ $bonSortie->date_sortie
                        ? \Carbon\Carbon::parse($bonSortie->date_sortie)->format('d/m/Y')
                        : '—'
                    }}

                </strong>

            </div>

        </div>

    </div>


    {{-- =========================================================
         CONTEXTE
    ========================================================== --}}
    <div class="card document-context-card">

        <div class="card-header">

            <div>

                <span class="card-eyebrow">
                    Contexte
                </span>

                <h2 class="card-title">
                    Destination de la sortie
                </h2>

            </div>

            <i class="fas fa-map-marker-alt card-header-icon"></i>

        </div>


        <div class="context-grid">

            {{-- DESTINATAIRE --}}
            <div class="context-item">

                <span class="context-label">
                    Destinataire
                </span>

                <div class="context-value">
                    {{ $bonSortie->destinataire ?: '—' }}
                </div>

            </div>


            {{-- MOTIF --}}
            <div class="context-item context-item-wide">

                <span class="context-label">
                    Motif
                </span>

                <div class="context-value context-text">
                    {{ $bonSortie->motif ?: 'Aucun motif renseigné.' }}
                </div>

            </div>


            {{-- NOTES --}}
            @if($bonSortie->notes)

                <div class="context-item context-item-wide">

                    <span class="context-label">
                        Notes
                    </span>

                    <div class="context-value context-text">
                        {!! nl2br(e($bonSortie->notes)) !!}
                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
         LIGNES DU BON
    ========================================================== --}}
    <div class="card lines-card">

        <div class="card-header">

            <div>

                <span class="card-eyebrow">
                    Articles demandés
                </span>

                <h2 class="card-title">
                    Lignes du bon de sortie
                </h2>

            </div>

            <span class="line-count">
                {{ $bonSortie->lignes->count() }}
                article(s)
            </span>

        </div>


        <div class="table-wrapper">

            <table class="data-table">

                <thead>
                    <tr>

                        <th>#</th>
                        <th>Article</th>
                        <th>Référence</th>
                        <th>Unité</th>
                        <th class="text-right">Demandée</th>
                        <th class="text-right">Validée</th>
                        <th class="text-right">Sortie</th>
                        <th>État</th>

                    </tr>
                </thead>


                <tbody>

                    @forelse($bonSortie->lignes as $index => $ligne)

                        @php
                            $stock = $ligne->stock;

                            $demandee = (float) $ligne->quantite_demandee;
                            $validee  = (float) $ligne->quantite_validee;
                            $sortie   = (float) $ligne->quantite_sortie;

                            if ($sortie > 0 && $sortie >= $validee && $validee > 0) {
                                $ligneStatut = 'sortie';
                                $ligneLabel  = 'Sortie';
                            } elseif ($validee > 0 && $sortie > 0) {
                                $ligneStatut = 'partielle';
                                $ligneLabel  = 'Partielle';
                            } elseif ($validee > 0) {
                                $ligneStatut = 'validee';
                                $ligneLabel  = 'Validée';
                            } else {
                                $ligneStatut = 'attente';
                                $ligneLabel  = 'En attente';
                            }
                        @endphp

                        <tr>

                            <td class="row-number">
                                {{ $index + 1 }}
                            </td>


                            <td>

                                <div class="article-cell">

                                    <div class="article-icon">
                                        <i class="fas fa-box"></i>
                                    </div>

                                    <div class="article-details">

                                        <strong>
                                            {{ $stock?->designation ?? 'Article supprimé' }}
                                        </strong>

                                        @if($ligne->notes)

                                            <small>
                                                {{ $ligne->notes }}
                                            </small>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            <td>

                                <span class="reference-code">
                                    {{ $stock?->reference ?? '—' }}
                                </span>

                            </td>


                            <td>
                                {{ $ligne->unite ?? $stock?->unite ?? '—' }}
                            </td>


                            <td class="text-right quantity-cell">
                                {{ number_format($demandee, 2, ',', ' ') }}
                            </td>


                            <td class="text-right quantity-cell">
                                {{ number_format($validee, 2, ',', ' ') }}
                            </td>


                            <td class="text-right quantity-cell quantity-highlight">
                                {{ number_format($sortie, 2, ',', ' ') }}
                            </td>


                            <td>

                                <span class="line-status line-status-{{ $ligneStatut }}">

                                    @if($ligneStatut === 'sortie')
                                        <i class="fas fa-check-circle"></i>
                                    @elseif($ligneStatut === 'partielle')
                                        <i class="fas fa-hourglass-half"></i>
                                    @elseif($ligneStatut === 'validee')
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-clock"></i>
                                    @endif

                                    {{ $ligneLabel }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="empty-table">

                                <div class="empty-state">

                                    <div class="empty-icon">
                                        <i class="fas fa-box-open"></i>
                                    </div>

                                    <h3>
                                        Aucun article
                                    </h3>

                                    <p>
                                        Ce bon de sortie ne contient encore aucune ligne.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>


                @if($bonSortie->lignes->count() > 0)

                    <tfoot>

                        <tr>

                            <td colspan="4">
                                <strong>Total</strong>
                            </td>

                            <td class="text-right">
                                <strong>
                                    {{ number_format(
                                        (float) $bonSortie->lignes->sum('quantite_demandee'),
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </strong>
                            </td>

                            <td class="text-right">
                                <strong>
                                    {{ number_format(
                                        (float) $bonSortie->lignes->sum('quantite_validee'),
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </strong>
                            </td>

                            <td class="text-right">
                                <strong>
                                    {{ number_format(
                                        (float) $bonSortie->lignes->sum('quantite_sortie'),
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </strong>
                            </td>

                            <td></td>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>


    {{-- =========================================================
         ACTIONS DE WORKFLOW
    ========================================================== --}}
    <div class="card actions-card">

        <div class="card-header">

            <div>

                <span class="card-eyebrow">
                    Actions
                </span>

                <h2 class="card-title">
                    Gestion du bon
                </h2>

            </div>

            <i class="fas fa-tasks card-header-icon"></i>

        </div>


        <div class="actions-grid">

            {{-- SOUMETTRE --}}
            @if($statutValue === 'brouillon')

                @can('submit', $bonSortie)

                    <form
                        method="POST"
                        action="{{ route('admin.stocks.sorties.soumettre', $bonSortie) }}"
                        class="action-form"
                        data-confirm="Voulez-vous soumettre ce bon de sortie ?"
                    >

                        @csrf

                        <button type="submit" class="action-button action-submit">

                            <i class="fas fa-paper-plane"></i>

                            <span>Soumettre</span>

                            <small>Envoyer pour validation</small>

                        </button>

                    </form>

                @endcan

            @endif


            {{-- VALIDER --}}
            @if($statutValue === 'soumis')

                @can('validate', $bonSortie)

                    <a
                        href="{{ route('admin.stocks.sorties.validation', $bonSortie) }}"
                        class="action-button action-validate"
                    >

                        <i class="fas fa-check-circle"></i>

                        <span>Valider</span>

                        <small>Autoriser la sortie</small>

                    </a>

                @endcan

            @endif


            {{-- PRÉPARER --}}
            @if($statutValue === 'valide')

                @can('prepare', $bonSortie)

                    <form
                        method="POST"
                        action="{{ route('admin.stocks.sorties.preparation.post', $bonSortie) }}"
                        class="action-form"
                        data-confirm="Voulez-vous mettre ce bon en préparation ?"
                    >

                        @csrf

                        <button type="submit" class="action-button action-prepare">

                            <i class="fas fa-box-open"></i>

                            <span>Préparer</span>

                            <small>Préparer les articles</small>

                        </button>

                    </form>

                @endcan

            @endif


            {{-- EFFECTUER LA SORTIE --}}
            @if($statutValue === 'en_preparation')

                @can('release', $bonSortie)

                    <a
                        href="{{ route('admin.stocks.sorties.preparation', $bonSortie) }}"
                        class="action-button action-output"
                    >

                        <i class="fas fa-truck"></i>

                        <span>Effectuer la sortie</span>

                        <small>Enregistrer la sortie réelle</small>

                    </a>

                @endcan

            @endif


            {{-- ANNULER --}}
            @if(in_array($statutValue, ['brouillon', 'soumis', 'valide'], true))

                @can('cancel', $bonSortie)

                    <form
                        method="POST"
                        action="{{ route('admin.stocks.sorties.annuler', $bonSortie) }}"
                        class="action-form"
                        data-confirm="Voulez-vous vraiment annuler ce bon de sortie ?"
                    >

                        @csrf

                        <button type="submit" class="action-button action-cancel">

                            <i class="fas fa-ban"></i>

                            <span>Annuler</span>

                            <small>Annuler le bon</small>

                        </button>

                    </form>

                @endcan

            @endif


            {{-- BON TERMINÉ --}}
            @if($statutValue === 'sorti')

                <div class="workflow-complete">

                    <div class="complete-icon">
                        <i class="fas fa-check-double"></i>
                    </div>

                    <div>

                        <strong>
                            Sortie terminée
                        </strong>

                        <span>
                            Les mouvements de stock ont été enregistrés.
                        </span>

                    </div>

                </div>

            @endif


            {{-- BON ANNULÉ --}}
            @if($statutValue === 'annule')

                <div class="workflow-complete workflow-cancelled">

                    <div class="complete-icon">
                        <i class="fas fa-ban"></i>
                    </div>

                    <div>

                        <strong>
                            Bon annulé
                        </strong>

                        <span>
                            Aucune opération de sortie ne peut être effectuée.
                        </span>

                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
         TRAÇABILITÉ
    ========================================================== --}}
    <div class="card audit-card">

        <div class="card-header">

            <div>

                <span class="card-eyebrow">
                    Traçabilité
                </span>

                <h2 class="card-title">
                    Informations système
                </h2>

            </div>

            <i class="fas fa-history card-header-icon"></i>

        </div>


        <div class="audit-grid">

            <div class="audit-item">

                <span>
                    Créé le
                </span>

                <strong>
                    {{ $bonSortie->created_at
                        ? $bonSortie->created_at->format('d/m/Y H:i')
                        : '—'
                    }}
                </strong>

            </div>


            <div class="audit-item">

                <span>
                    Dernière modification
                </span>

                <strong>
                    {{ $bonSortie->updated_at
                        ? $bonSortie->updated_at->format('d/m/Y H:i')
                        : '—'
                    }}
                </strong>

            </div>


            <div class="audit-item">

                <span>
                    Identifiant interne
                </span>

                <strong>
                    #{{ $bonSortie->id }}
                </strong>

            </div>


            <div class="audit-item">

                <span>
                    Référence documentaire
                </span>

                <strong>
                    {{ $bonSortie->reference ?? '—' }}
                </strong>

            </div>

        </div>

    </div>

</div>

@endsection