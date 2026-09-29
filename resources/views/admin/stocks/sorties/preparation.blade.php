@extends('layouts.admin')

@section('title', 'Préparation du bon de sortie ' . $bonSortie->numero)

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/sorties-preparation.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-sortie-preparation">

    @php
        /*
         * Normalisation du statut.
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
    <div class="stock-sortie-preparation__header">

        <div class="stock-sortie-preparation__header-content">

            {{-- FIL D'ARIANE --}}
            <div class="stock-sortie-preparation__breadcrumb">

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span aria-hidden="true">/</span>

                <a href="{{ route('admin.stocks.sorties.index') }}">
                    Bons de sortie
                </a>

                <span aria-hidden="true">/</span>

                <a href="{{ route('admin.stocks.sorties.show', $bonSortie) }}">
                    {{ $bonSortie->numero }}
                </a>

                <span aria-hidden="true">/</span>

                <span>Préparation</span>

            </div>

            <h1 class="stock-sortie-preparation__title">
                Préparation du bon de sortie
            </h1>

            <p class="stock-sortie-preparation__subtitle">
                Préparez physiquement les articles avant l’enregistrement
                définitif de la sortie du stock.
            </p>

        </div>


        <div class="stock-sortie-preparation__header-actions">

            <a
                href="{{ route('admin.stocks.sorties.show', $bonSortie) }}"
                class="btn btn--secondary"
            >

                <i class="fas fa-arrow-left" aria-hidden="true"></i>

                <span>Retour au bon</span>

            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-sortie-preparation__alert stock-sortie-preparation__alert--success">

            <div class="stock-sortie-preparation__alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <div class="stock-sortie-preparation__alert-content">

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-sortie-preparation__alert stock-sortie-preparation__alert--danger">

            <div class="stock-sortie-preparation__alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <div class="stock-sortie-preparation__alert-content">

                <strong>Impossible de poursuivre</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    @if($errors->any())

        <div class="stock-sortie-preparation__alert stock-sortie-preparation__alert--danger">

            <div class="stock-sortie-preparation__alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

            <div class="stock-sortie-preparation__alert-content">

                <strong>Certaines vérifications ont échoué</strong>

                <ul class="stock-sortie-preparation__error-list">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =========================================================
         STATUT DU BON
    ========================================================== --}}
    <div class="stock-sortie-preparation__status-card">

        <div class="stock-sortie-preparation__status-main">

            <div class="stock-sortie-preparation__status-icon">
                <i class="fas fa-box-open"></i>
            </div>

            <div>

                <span class="stock-sortie-preparation__status-label">
                    État actuel
                </span>

                <div class="stock-sortie-preparation__status-value">

                    <span class="stock-sortie-preparation__status
                        stock-sortie-preparation__status--{{ $statutValue }}">

                        {{ $statutLabel }}

                    </span>

                </div>

            </div>

        </div>


        <div class="stock-sortie-preparation__status-reference">

            <span>Référence</span>

            <strong>{{ $bonSortie->numero }}</strong>

        </div>

    </div>


    {{-- =========================================================
         GRILLE PRINCIPALE
    ========================================================== --}}
    <div class="stock-sortie-preparation__grid">

        {{-- =====================================================
             COLONNE PRINCIPALE
        ====================================================== --}}
        <div class="stock-sortie-preparation__main">

            {{-- INFORMATIONS DU BON --}}
            <section class="stock-sortie-preparation__card">

                <div class="stock-sortie-preparation__card-header">

                    <div>

                        <h2 class="stock-sortie-preparation__card-title">
                            Informations du bon
                        </h2>

                        <p class="stock-sortie-preparation__card-description">
                            Vérifiez le contexte de la sortie avant de préparer
                            les articles.
                        </p>

                    </div>

                </div>

                <div class="stock-sortie-preparation__card-body">

                    <div class="stock-sortie-preparation__details">

                        <div class="stock-sortie-preparation__detail">

                            <span class="stock-sortie-preparation__detail-label">
                                Numéro
                            </span>

                            <span class="stock-sortie-preparation__detail-value">
                                {{ $bonSortie->numero }}
                            </span>

                        </div>


                        <div class="stock-sortie-preparation__detail">

                            <span class="stock-sortie-preparation__detail-label">
                                Date de demande
                            </span>

                            <span class="stock-sortie-preparation__detail-value">

                                {{ $bonSortie->date_demande
                                    ? \Illuminate\Support\Carbon::parse($bonSortie->date_demande)->format('d/m/Y')
                                    : '—'
                                }}

                            </span>

                        </div>


                        <div class="stock-sortie-preparation__detail">

                            <span class="stock-sortie-preparation__detail-label">
                                Projet
                            </span>

                            <span class="stock-sortie-preparation__detail-value">

                                @if($bonSortie->projet)

                                    {{ $bonSortie->projet->nom
                                        ?? $bonSortie->projet->titre
                                        ?? 'Projet' }}

                                @else

                                    Aucun projet associé

                                @endif

                            </span>

                        </div>


                        <div class="stock-sortie-preparation__detail">

                            <span class="stock-sortie-preparation__detail-label">
                                Destinataire
                            </span>

                            <span class="stock-sortie-preparation__detail-value">

                                {{ $bonSortie->destinataire ?: '—' }}

                            </span>

                        </div>


                        <div class="stock-sortie-preparation__detail">

                            <span class="stock-sortie-preparation__detail-label">
                                Demandeur
                            </span>

                            <span class="stock-sortie-preparation__detail-value">

                                {{ $bonSortie->demandeur?->name ?? '—' }}

                            </span>

                        </div>


                        <div class="stock-sortie-preparation__detail">

                            <span class="stock-sortie-preparation__detail-label">
                                Préparateur
                            </span>

                            <span class="stock-sortie-preparation__detail-value">

                                {{ $bonSortie->preparateur?->name ?? 'Non affecté' }}

                            </span>

                        </div>


                        @if($bonSortie->motif)

                            <div class="stock-sortie-preparation__detail
                                        stock-sortie-preparation__detail--full">

                                <span class="stock-sortie-preparation__detail-label">
                                    Motif
                                </span>

                                <div class="stock-sortie-preparation__text-block">
                                    {{ $bonSortie->motif }}
                                </div>

                            </div>

                        @endif


                        @if($bonSortie->notes)

                            <div class="stock-sortie-preparation__detail
                                        stock-sortie-preparation__detail--full">

                                <span class="stock-sortie-preparation__detail-label">
                                    Notes
                                </span>

                                <div class="stock-sortie-preparation__text-block">
                                    {{ $bonSortie->notes }}
                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </section>


            {{-- ARTICLES À PRÉPARER --}}
            <section class="stock-sortie-preparation__card">

                <div class="stock-sortie-preparation__card-header">

                    <div>

                        <h2 class="stock-sortie-preparation__card-title">
                            Articles à préparer
                        </h2>

                        <p class="stock-sortie-preparation__card-description">
                            Vérifiez les quantités physiquement préparées
                            avant l’enregistrement définitif.
                        </p>

                    </div>

                    <span class="stock-sortie-preparation__count">

                        {{ $lignes->count() }}
                        {{ $lignes->count() > 1 ? 'articles' : 'article' }}

                    </span>

                </div>


                <div class="stock-sortie-preparation__card-body">

                    @if($lignes->isNotEmpty())

                        <div class="stock-sortie-preparation__table-wrapper">

                            <table class="stock-sortie-preparation__table">

                                <thead>

                                    <tr>

                                        <th>Article</th>
                                        <th>Quantité validée</th>
                                        <th>Unité</th>
                                        <th>Disponibilité</th>
                                        <th>État</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($lignes as $ligne)

                                        @php
                                            $stock = $ligne->stock;

                                            $quantiteValidee = (float) $ligne->quantite_validee;

                                            $disponible = $stock
                                                ? (float) $stock->quantiteDisponible()
                                                : 0.0;

                                            $suffisant = $disponible >= $quantiteValidee;
                                        @endphp

                                        <tr>

                                            {{-- ARTICLE --}}
                                            <td>

                                                <div class="stock-sortie-preparation__article">

                                                    <div class="stock-sortie-preparation__article-icon">
                                                        <i class="fas fa-box"></i>
                                                    </div>

                                                    <div class="stock-sortie-preparation__article-info">

                                                        <span class="stock-sortie-preparation__article-name">

                                                            {{ $stock?->designation ?? 'Article supprimé' }}

                                                        </span>

                                                        <span class="stock-sortie-preparation__article-reference">

                                                            {{ $stock?->reference ?? '—' }}

                                                        </span>

                                                    </div>

                                                </div>

                                            </td>


                                            {{-- QUANTITÉ VALIDÉE --}}
                                            <td>

                                                <span class="stock-sortie-preparation__quantity">

                                                    {{ number_format(
                                                        $quantiteValidee,
                                                        2,
                                                        ',',
                                                        ' '
                                                    ) }}

                                                </span>

                                            </td>


                                            {{-- UNITÉ --}}
                                            <td>

                                                <span class="stock-sortie-preparation__unit">
                                                    {{ $ligne->unite ?? $stock?->unite ?? '—' }}
                                                </span>

                                            </td>


                                            {{-- DISPONIBILITÉ --}}
                                            <td>

                                                @if($suffisant)

                                                    <span class="stock-sortie-preparation__availability
                                                        stock-sortie-preparation__availability--available">

                                                        <i class="fas fa-check-circle"></i>

                                                        Disponible

                                                        <small>
                                                            {{ number_format($disponible, 2, ',', ' ') }}
                                                        </small>

                                                    </span>

                                                @else

                                                    <span class="stock-sortie-preparation__availability
                                                        stock-sortie-preparation__availability--insufficient">

                                                        <i class="fas fa-exclamation-circle"></i>

                                                        Insuffisant

                                                        <small>
                                                            {{ number_format($disponible, 2, ',', ' ') }}
                                                        </small>

                                                    </span>

                                                @endif

                                            </td>


                                            {{-- ÉTAT --}}
                                            <td>

                                                @if($suffisant)

                                                    <span class="stock-sortie-preparation__line-status
                                                        stock-sortie-preparation__line-status--complete">

                                                        <i class="fas fa-check"></i>

                                                        Prêt

                                                    </span>

                                                @else

                                                    <span class="stock-sortie-preparation__line-status
                                                        stock-sortie-preparation__line-status--pending">

                                                        <i class="fas fa-clock"></i>

                                                        À vérifier

                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="stock-sortie-preparation__empty">

                            <div class="stock-sortie-preparation__empty-icon">
                                <i class="fas fa-box-open"></i>
                            </div>

                            <h3>
                                Aucun article
                            </h3>

                            <p>
                                Ce bon de sortie ne contient actuellement
                                aucune ligne.
                            </p>

                        </div>

                    @endif

                </div>

            </section>


            {{-- CONSIGNES DE PRÉPARATION --}}
            <section class="stock-sortie-preparation__card">

                <div class="stock-sortie-preparation__card-header">

                    <div>

                        <h2 class="stock-sortie-preparation__card-title">
                            Consignes de préparation
                        </h2>

                        <p class="stock-sortie-preparation__card-description">
                            Points à contrôler avant l’enregistrement
                            définitif de la sortie.
                        </p>

                    </div>

                </div>

                <div class="stock-sortie-preparation__card-body">

                    <ul class="stock-sortie-preparation__checks">

                        <li class="stock-sortie-preparation__check">

                            <span class="stock-sortie-preparation__check-icon">
                                <i class="fas fa-boxes"></i>
                            </span>

                            <div>

                                <strong>
                                    Vérifier les quantités physiques
                                </strong>

                                <p>
                                    Les quantités réellement préparées
                                    doivent correspondre aux quantités
                                    validées avant la sortie définitive.
                                </p>

                            </div>

                        </li>


                        <li class="stock-sortie-preparation__check">

                            <span class="stock-sortie-preparation__check-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>

                            <div>

                                <strong>
                                    Respecter la sélection FEFO
                                </strong>

                                <p>
                                    Pour les articles gérés par lots,
                                    les lots arrivant à expiration le plus
                                    tôt doivent être privilégiés.
                                </p>

                            </div>

                        </li>


                        <li class="stock-sortie-preparation__check">

                            <span class="stock-sortie-preparation__check-icon">
                                <i class="fas fa-shield-alt"></i>
                            </span>

                            <div>

                                <strong>
                                    Vérifier l’intégrité du bon
                                </strong>

                                <p>
                                    Aucun article, lot ou quantité ne doit
                                    être modifié directement depuis cette
                                    interface.
                                </p>

                            </div>

                        </li>

                    </ul>

                </div>

            </section>

        </div>


        {{-- =====================================================
             COLONNE LATÉRALE
        ====================================================== --}}
        <aside class="stock-sortie-preparation__sidebar">

            {{-- RÉSUMÉ --}}
            <section class="stock-sortie-preparation__card">

                <div class="stock-sortie-preparation__card-header">

                    <div>

                        <h2 class="stock-sortie-preparation__card-title">
                            Résumé
                        </h2>

                    </div>

                </div>


                <div class="stock-sortie-preparation__card-body">

                    @php
                        $totalLignes = $lignes->count();

                        /*
                         * Nombre de lignes dont la disponibilité
                         * couvre la quantité validée.
                         */
                        $lignesPretes = $lignes->filter(function ($ligne) {
                            $stock = $ligne->stock;

                            if (!$stock) {
                                return false;
                            }

                            $validee    = (float) $ligne->quantite_validee;
                            $disponible = (float) $stock->quantiteDisponible();

                            return $disponible >= $validee;
                        })->count();

                        $progression = $totalLignes > 0
                            ? round(($lignesPretes / $totalLignes) * 100)
                            : 0;
                    @endphp

                    <div class="stock-sortie-preparation__summary">

                        <div class="stock-sortie-preparation__summary-row">

                            <span>
                                Nombre de lignes
                            </span>

                            <strong>
                                {{ $totalLignes }}
                            </strong>

                        </div>

                        <div class="stock-sortie-preparation__summary-row">

                            <span>
                                Lignes prêtes
                            </span>

                            <strong>
                                {{ $lignesPretes }}
                            </strong>

                        </div>

                        <div class="stock-sortie-preparation__summary-row">

                            <span>
                                Progression
                            </span>

                            <strong>
                                {{ $progression }} %
                            </strong>

                        </div>

                    </div>


                    <div class="stock-sortie-preparation__progress"
                         aria-label="Progression de la préparation">

                        <div class="stock-sortie-preparation__progress-bar"
                             style="width: {{ $progression }}%;"
                             role="progressbar"
                             aria-valuemin="0"
                             aria-valuemax="100"
                             aria-valuenow="{{ $progression }}">
                        </div>

                    </div>

                </div>

            </section>


            {{-- ACTION DE SORTIE --}}
            <section class="stock-sortie-preparation__card
                            stock-sortie-preparation__card--action">

                <div class="stock-sortie-preparation__card-header">

                    <div>

                        <h2 class="stock-sortie-preparation__card-title">
                            Enregistrer la sortie
                        </h2>

                    </div>

                </div>


                <div class="stock-sortie-preparation__card-body">

                    @if($totalLignes > 0)

                        <p class="stock-sortie-preparation__action-description">

                            Lorsque la préparation physique est terminée,
                            vous pouvez effectuer la sortie. Le système
                            vérifiera une nouvelle fois la disponibilité
                            réelle du stock et sélectionnera les lots
                            conformément aux règles métier (FEFO).

                        </p>


                        @can('release', $bonSortie)

                            <form
                                method="POST"
                                action="{{ route('admin.stocks.sorties.sortir', $bonSortie) }}"
                                class="stock-sortie-preparation__form"
                                data-confirm="Voulez-vous réellement effectuer la sortie de ce bon ?"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn--primary btn--full"
                                >

                                    <i class="fas fa-check-circle" aria-hidden="true"></i>

                                    <span>
                                        Effectuer la sortie
                                    </span>

                                </button>

                            </form>

                            <p class="stock-sortie-preparation__security-note">

                                <i class="fas fa-lock" aria-hidden="true"></i>

                                Cette opération sera vérifiée et exécutée
                                côté serveur dans une transaction.

                            </p>

                        @else

                            <div class="stock-sortie-preparation__locked">

                                <div class="stock-sortie-preparation__locked-icon">
                                    <i class="fas fa-lock"></i>
                                </div>

                                <h3>
                                    Sortie non disponible
                                </h3>

                                <p>
                                    Vous n’êtes pas autorisé à effectuer
                                    cette opération ou le bon n’est pas
                                    dans un état permettant sa sortie.
                                </p>

                            </div>

                        @endcan

                    @else

                        <div class="stock-sortie-preparation__locked">

                            <div class="stock-sortie-preparation__locked-icon">
                                <i class="fas fa-ban"></i>
                            </div>

                            <h3>
                                Sortie impossible
                            </h3>

                            <p>
                                Le bon ne contient aucune ligne à sortir.
                            </p>

                        </div>

                    @endif

                </div>

            </section>


            {{-- RETOUR --}}
            <a
                href="{{ route('admin.stocks.sorties.show', $bonSortie) }}"
                class="stock-sortie-preparation__back-link"
            >

                <i class="fas fa-arrow-left" aria-hidden="true"></i>

                Retour au détail du bon

            </a>

        </aside>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/sorties-preparation.js') }}"
        defer>
    </script>

@endsection