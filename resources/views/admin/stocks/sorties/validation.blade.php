@extends('layouts.admin')

@section('title', 'Validation du bon de sortie ' . $bonSortie->numero)

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/sorties-validation.css') }}"
    >
@endsection

@section('content')

<div class="stock-sortie-validation">

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
    <div class="stock-sortie-validation__header">

        <div class="stock-sortie-validation__header-content">

            {{-- FIL D'ARIANE --}}
            <div class="stock-sortie-validation__breadcrumb">

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

                <span>Validation</span>

            </div>

            <h1 class="stock-sortie-validation__title">
                Validation du bon de sortie
            </h1>

            <p class="stock-sortie-validation__subtitle">
                Vérifiez les informations et les quantités avant
                de valider définitivement la demande.
            </p>

        </div>


        <div class="stock-sortie-validation__header-actions">

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

        <div class="stock-sortie-validation__alert stock-sortie-validation__alert--success">

            <div class="stock-sortie-validation__alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <div class="stock-sortie-validation__alert-content">

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-sortie-validation__alert stock-sortie-validation__alert--danger">

            <div class="stock-sortie-validation__alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <div class="stock-sortie-validation__alert-content">

                <strong>Impossible de poursuivre</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    @if($errors->any())

        <div class="stock-sortie-validation__alert stock-sortie-validation__alert--danger">

            <div class="stock-sortie-validation__alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

            <div class="stock-sortie-validation__alert-content">

                <strong>La validation ne peut pas être effectuée.</strong>

                <ul class="stock-sortie-validation__error-list">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =========================================================
         STATUT
    ========================================================== --}}
    <div class="stock-sortie-validation__status-card">

        <div class="stock-sortie-validation__status-main">

            <div class="stock-sortie-validation__status-icon">
                <i class="fas fa-file-signature"></i>
            </div>

            <div>

                <span class="stock-sortie-validation__status-label">
                    État actuel
                </span>

                <div class="stock-sortie-validation__status-value">

                    <span class="stock-sortie-validation__status
                        stock-sortie-validation__status--{{ $statutValue }}">

                        {{ $statutLabel }}

                    </span>

                </div>

            </div>

        </div>


        <div class="stock-sortie-validation__status-reference">

            <span>Référence</span>

            <strong>{{ $bonSortie->numero }}</strong>

        </div>

    </div>


    {{-- =========================================================
         FORMULAIRE DE VALIDATION
    ========================================================== --}}
    <form
        method="POST"
        action="{{ route('admin.stocks.sorties.valider', $bonSortie) }}"
        class="stock-sortie-validation__form"
        id="bon-sortie-validation-form"
    >

        @csrf


        {{-- =====================================================
             CONTENU PRINCIPAL
        ====================================================== --}}
        <div class="stock-sortie-validation__grid">

            {{-- =============================================
                 COLONNE PRINCIPALE
            ============================================== --}}
            <main>

                {{-- INFORMATIONS DU BON --}}
                <section class="stock-sortie-validation__card">

                    <div class="stock-sortie-validation__card-header">

                        <div>

                            <h2 class="stock-sortie-validation__card-title">
                                Informations du bon
                            </h2>

                            <p class="stock-sortie-validation__card-description">
                                Informations générales de la demande de sortie.
                            </p>

                        </div>

                    </div>


                    <div class="stock-sortie-validation__card-body">

                        <div class="stock-sortie-validation__details">

                            <div class="stock-sortie-validation__detail">

                                <span class="stock-sortie-validation__detail-label">
                                    Numéro
                                </span>

                                <strong class="stock-sortie-validation__detail-value">
                                    {{ $bonSortie->numero }}
                                </strong>

                            </div>


                            <div class="stock-sortie-validation__detail">

                                <span class="stock-sortie-validation__detail-label">
                                    Date de demande
                                </span>

                                <span class="stock-sortie-validation__detail-value">

                                    {{ $bonSortie->date_demande
                                        ? \Carbon\Carbon::parse($bonSortie->date_demande)->format('d/m/Y')
                                        : '—'
                                    }}

                                </span>

                            </div>


                            <div class="stock-sortie-validation__detail">

                                <span class="stock-sortie-validation__detail-label">
                                    Demandeur
                                </span>

                                <span class="stock-sortie-validation__detail-value">
                                    {{ $bonSortie->demandeur?->name ?? '—' }}
                                </span>

                            </div>


                            <div class="stock-sortie-validation__detail">

                                <span class="stock-sortie-validation__detail-label">
                                    Projet
                                </span>

                                <span class="stock-sortie-validation__detail-value">

                                    @if($bonSortie->projet)

                                        {{ $bonSortie->projet->nom
                                            ?? $bonSortie->projet->titre
                                            ?? 'Projet' }}

                                    @else

                                        Aucun projet

                                    @endif

                                </span>

                            </div>


                            <div class="stock-sortie-validation__detail stock-sortie-validation__detail--full">

                                <span class="stock-sortie-validation__detail-label">
                                    Destinataire
                                </span>

                                <span class="stock-sortie-validation__detail-value">
                                    {{ $bonSortie->destinataire ?: '—' }}
                                </span>

                            </div>


                            @if($bonSortie->motif)

                                <div class="stock-sortie-validation__detail stock-sortie-validation__detail--full">

                                    <span class="stock-sortie-validation__detail-label">
                                        Motif
                                    </span>

                                    <div class="stock-sortie-validation__text-block">
                                        {{ $bonSortie->motif }}
                                    </div>

                                </div>

                            @endif


                            @if($bonSortie->notes)

                                <div class="stock-sortie-validation__detail stock-sortie-validation__detail--full">

                                    <span class="stock-sortie-validation__detail-label">
                                        Notes
                                    </span>

                                    <div class="stock-sortie-validation__text-block">
                                        {{ $bonSortie->notes }}
                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </section>


                {{-- LIGNES À VALIDER --}}
                <section class="stock-sortie-validation__card">

                    <div class="stock-sortie-validation__card-header">

                        <div>

                            <h2 class="stock-sortie-validation__card-title">
                                Articles demandés
                            </h2>

                            <p class="stock-sortie-validation__card-description">
                                Ajustez la quantité validée si nécessaire,
                                puis confirmez la validation du bon.
                            </p>

                        </div>

                        <span class="stock-sortie-validation__count">

                            {{ $bonSortie->lignes->count() }}
                            {{ $bonSortie->lignes->count() > 1 ? 'articles' : 'article' }}

                        </span>

                    </div>


                    <div class="stock-sortie-validation__table-wrapper">

                        @if($bonSortie->lignes->isNotEmpty())

                            <table class="stock-sortie-validation__table">

                                <thead>

                                    <tr>

                                        <th>Article</th>
                                        <th class="text-right">Demandé</th>
                                        <th class="text-right">Validé</th>
                                        <th>Unité</th>
                                        <th>Disponibilité</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($bonSortie->lignes as $index => $ligne)

                                        @php
                                            $stock = $ligne->stock;

                                            $quantiteDemandee = (float) $ligne->quantite_demandee;

                                            /*
                                             * Valeur par défaut : quantité demandée.
                                             * Le valideur peut la réduire.
                                             */
                                            $quantiteValideeDefault = old(
                                                "lignes.{$index}.quantite_validee",
                                                (float) $ligne->quantite_demandee
                                            );

                                            $disponible = $stock
                                                ? (float) $stock->quantiteDisponible()
                                                : 0.0;

                                            $suffisant = $disponible >= $quantiteDemandee;
                                        @endphp

                                        <tr>

                                            {{-- ARTICLE --}}
                                            <td>

                                                <input
                                                    type="hidden"
                                                    name="lignes[{{ $index }}][ligne_id]"
                                                    value="{{ $ligne->id }}"
                                                >

                                                <div class="stock-sortie-validation__article">

                                                    <div class="stock-sortie-validation__article-icon">
                                                        <i class="fas fa-box"></i>
                                                    </div>

                                                    <div class="stock-sortie-validation__article-info">

                                                        <strong class="stock-sortie-validation__article-name">
                                                            {{ $stock?->designation ?? 'Article supprimé' }}
                                                        </strong>

                                                        @if($stock?->reference)

                                                            <span class="stock-sortie-validation__article-reference">
                                                                {{ $stock->reference }}
                                                            </span>

                                                        @endif

                                                    </div>

                                                </div>

                                            </td>


                                            {{-- QUANTITÉ DEMANDÉE --}}
                                            <td class="text-right">

                                                <strong class="stock-sortie-validation__quantity">

                                                    {{ number_format($quantiteDemandee, 2, ',', ' ') }}

                                                </strong>

                                            </td>


                                            {{-- QUANTITÉ VALIDÉE (input) --}}
                                            <td class="text-right">

                                                <input
                                                    type="number"
                                                    name="lignes[{{ $index }}][quantite_validee]"
                                                    class="stock-sortie-validation__input
                                                        @error("lignes.{$index}.quantite_validee") is-invalid @enderror"
                                                    value="{{ $quantiteValideeDefault }}"
                                                    min="0"
                                                    max="{{ $quantiteDemandee }}"
                                                    step="0.01"
                                                    inputmode="decimal"
                                                    required
                                                >

                                                @error("lignes.{$index}.quantite_validee")

                                                    <small class="stock-sortie-validation__input-error">
                                                        {{ $message }}
                                                    </small>

                                                @enderror

                                            </td>


                                            {{-- UNITÉ --}}
                                            <td>

                                                <span class="stock-sortie-validation__unit">
                                                    {{ $ligne->unite ?? $stock?->unite ?? '—' }}
                                                </span>

                                            </td>


                                            {{-- DISPONIBILITÉ --}}
                                            <td>

                                                @if($suffisant)

                                                    <span class="stock-sortie-validation__availability
                                                        stock-sortie-validation__availability--available">

                                                        <i class="fas fa-check-circle" aria-hidden="true"></i>

                                                        Disponible

                                                        <small>
                                                            {{ number_format($disponible, 2, ',', ' ') }}
                                                        </small>

                                                    </span>

                                                @else

                                                    <span class="stock-sortie-validation__availability
                                                        stock-sortie-validation__availability--insufficient">

                                                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>

                                                        Insuffisant

                                                        <small>
                                                            {{ number_format($disponible, 2, ',', ' ') }}
                                                        </small>

                                                    </span>

                                                @endif

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        @else

                            <div class="stock-sortie-validation__empty">

                                <div class="stock-sortie-validation__empty-icon">
                                    <i class="fas fa-box-open" aria-hidden="true"></i>
                                </div>

                                <h3>
                                    Aucun article
                                </h3>

                                <p>
                                    Ce bon de sortie ne contient aucune ligne.
                                </p>

                            </div>

                        @endif

                    </div>

                </section>


                {{-- RÈGLES DE VALIDATION --}}
                <section class="stock-sortie-validation__card">

                    <div class="stock-sortie-validation__card-header">

                        <div>

                            <h2 class="stock-sortie-validation__card-title">
                                Contrôles avant validation
                            </h2>

                            <p class="stock-sortie-validation__card-description">
                                Ces contrôles permettent d’éviter une sortie
                                supérieure au stock réellement disponible.
                            </p>

                        </div>

                    </div>


                    <div class="stock-sortie-validation__card-body">

                        <ul class="stock-sortie-validation__checks">

                            <li class="stock-sortie-validation__check">

                                <span class="stock-sortie-validation__check-icon">
                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                </span>

                                <div>

                                    <strong>
                                        Vérification transactionnelle
                                    </strong>

                                    <p>
                                        La disponibilité définitive sera vérifiée
                                        par le service métier avec verrouillage
                                        transactionnel.
                                    </p>

                                </div>

                            </li>


                            <li class="stock-sortie-validation__check">

                                <span class="stock-sortie-validation__check-icon">
                                    <i class="fas fa-layer-group" aria-hidden="true"></i>
                                </span>

                                <div>

                                    <strong>
                                        Gestion des lots
                                    </strong>

                                    <p>
                                        Les lots disponibles seront sélectionnés
                                        selon les règles métier, notamment FEFO
                                        lorsque cela est applicable.
                                    </p>

                                </div>

                            </li>


                            <li class="stock-sortie-validation__check">

                                <span class="stock-sortie-validation__check-icon">
                                    <i class="fas fa-history" aria-hidden="true"></i>
                                </span>

                                <div>

                                    <strong>
                                        Traçabilité
                                    </strong>

                                    <p>
                                        La validation ne supprime aucun historique.
                                        Les mouvements seront créés lors de la
                                        sortie physique.
                                    </p>

                                </div>

                            </li>

                        </ul>

                    </div>

                </section>

            </main>


            {{-- =============================================
                 COLONNE LATÉRALE
            ============================================== --}}
            <aside>

                {{-- RÉSUMÉ --}}
                <section class="stock-sortie-validation__card">

                    <div class="stock-sortie-validation__card-header">

                        <h2 class="stock-sortie-validation__card-title">
                            Résumé
                        </h2>

                    </div>


                    <div class="stock-sortie-validation__card-body">

                        <div class="stock-sortie-validation__summary">

                            <div class="stock-sortie-validation__summary-row">

                                <span>Articles</span>

                                <strong>
                                    {{ $bonSortie->lignes->count() }}
                                </strong>

                            </div>


                            <div class="stock-sortie-validation__summary-row">

                                <span>Quantité demandée</span>

                                <strong>

                                    {{ number_format(
                                        (float) $bonSortie->lignes->sum('quantite_demandee'),
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                </strong>

                            </div>

                        </div>

                    </div>

                </section>


                {{-- DÉCISION --}}
                @if(in_array($statutValue, ['soumis'], true))

                    @can('validate', $bonSortie)

                        <section class="stock-sortie-validation__card
                                        stock-sortie-validation__card--action">

                            <div class="stock-sortie-validation__card-header">

                                <h2 class="stock-sortie-validation__card-title">
                                    Décision
                                </h2>

                            </div>


                            <div class="stock-sortie-validation__card-body">

                                <p class="stock-sortie-validation__action-description">

                                    La validation confirme que la demande peut
                                    poursuivre son workflow vers la préparation.

                                </p>


                                <button
                                    type="submit"
                                    class="btn btn--primary btn--full"
                                    data-validation-submit
                                >

                                    <i class="fas fa-check-circle" aria-hidden="true"></i>

                                    <span>
                                        Valider le bon
                                    </span>

                                </button>


                                <p class="stock-sortie-validation__security-note">

                                    <i class="fas fa-shield-alt" aria-hidden="true"></i>

                                    La disponibilité définitive sera contrôlée
                                    côté serveur.

                                </p>

                            </div>

                        </section>

                    @endcan

                @else

                    <section class="stock-sortie-validation__card">

                        <div class="stock-sortie-validation__card-body">

                            <div class="stock-sortie-validation__locked">

                                <div class="stock-sortie-validation__locked-icon">
                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                </div>

                                <h3>
                                    Validation indisponible
                                </h3>

                                <p>
                                    Ce bon ne se trouve plus dans un état permettant
                                    cette opération.
                                </p>

                            </div>

                        </div>

                    </section>

                @endif


                {{-- RETOUR --}}
                <a
                    href="{{ route('admin.stocks.sorties.show', $bonSortie) }}"
                    class="stock-sortie-validation__back-link"
                >

                    <i class="fas fa-arrow-left" aria-hidden="true"></i>

                    Retour au détail du bon

                </a>

            </aside>

        </div>

    </form>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/sorties-validation.js') }}"
        defer>
    </script>

@endsection