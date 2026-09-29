@extends('layouts.admin')

@section('title', 'Mouvement #' . $mouvement->id)

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/mouvements-show.css') }}"
    >
@endsection

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Type de mouvement
    |--------------------------------------------------------------------------
    */

    $typeValue = $mouvement->type instanceof \App\Enums\TypeMouvement
        ? $mouvement->type->value
        : (string) $mouvement->type;

    $typeLabel = $mouvement->type instanceof \App\Enums\TypeMouvement
        ? $mouvement->type->libelle()
        : ucfirst(str_replace('_', ' ', $typeValue));

    $isEntree     = $typeValue === 'entree';
    $isSortie     = $typeValue === 'sortie';
    $isTransfert  = $typeValue === 'transfert';
    $isAjustement = $typeValue === 'ajustement';

    /*
    |--------------------------------------------------------------------------
    | Nature
    |--------------------------------------------------------------------------
    */

    $natureLabel = $mouvement->nature instanceof \App\Enums\NatureMouvement
        ? $mouvement->nature->libelle()
        : ($mouvement->nature
            ? ucfirst(str_replace('_', ' ', (string) $mouvement->nature))
            : '—');

    /*
    |--------------------------------------------------------------------------
    | Quantité signée
    |--------------------------------------------------------------------------
    */

    $quantiteSignee = (float) $mouvement->quantite * (int) ($mouvement->sens ?? 1);

    /*
    |--------------------------------------------------------------------------
    | Document source
    |--------------------------------------------------------------------------
    */

    $documentReference = $mouvement->reference_document;
@endphp


<div class="stock-mouvement-show-page">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="stock-mouvement-show-header">

        <div class="stock-mouvement-show-header__content">

            <div class="stock-mouvement-show-breadcrumb">

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span>/</span>

                <a href="{{ route('admin.stocks.mouvements.index') }}">
                    Mouvements
                </a>

                <span>/</span>

                <span>Détail</span>

            </div>


            <div class="stock-mouvement-show-title-row">

                <div class="stock-mouvement-show-title-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>

                <div>

                    <h1 class="stock-mouvement-show-title">
                        Détail du mouvement
                    </h1>

                    <p class="stock-mouvement-show-subtitle">
                        Consultez les informations et la traçabilité
                        de cette opération de stock.
                    </p>

                </div>

            </div>

        </div>


        <div class="stock-mouvement-show-header__actions">

            <a
                href="{{ route('admin.stocks.mouvements.index') }}"
                class="stock-mouvement-show-button stock-mouvement-show-button--secondary"
            >
                <i class="fas fa-arrow-left"></i>
                <span>Retour aux mouvements</span>
            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-mouvement-show-alert stock-mouvement-show-alert--success">

            <div class="stock-mouvement-show-alert__icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <div class="stock-mouvement-show-alert__content">

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-mouvement-show-alert stock-mouvement-show-alert--danger">

            <div class="stock-mouvement-show-alert__icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <div class="stock-mouvement-show-alert__content">

                <strong>Opération impossible</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    {{-- =========================================================
         RÉSUMÉ
    ========================================================== --}}
    <div class="stock-mouvement-show-summary">

        <div class="stock-mouvement-show-summary__main">

            <div class="stock-mouvement-show-summary__label">
                Mouvement
            </div>

            <div class="stock-mouvement-show-summary__reference">

                @if($documentReference)

                    <span class="stock-mouvement-show-reference">
                        {{ $documentReference }}
                    </span>

                @else

                    <span class="stock-mouvement-show-reference">
                        Mouvement #{{ $mouvement->id }}
                    </span>

                @endif

            </div>

            <div class="stock-mouvement-show-summary__meta">

                @if($mouvement->date_mouvement)

                    <span>
                        <i class="far fa-calendar"></i>

                        {{ \Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m/Y') }}
                    </span>

                @endif

                @if($mouvement->created_at)

                    <span>
                        <i class="far fa-clock"></i>

                        {{ $mouvement->created_at->format('H:i') }}
                    </span>

                @endif

            </div>

        </div>


        <div class="stock-mouvement-show-summary__type">

            @if($isEntree)

                <span class="stock-mouvement-show-type stock-mouvement-show-type--entry">
                    <i class="fas fa-arrow-down"></i>
                    Entrée
                </span>

            @elseif($isSortie)

                <span class="stock-mouvement-show-type stock-mouvement-show-type--exit">
                    <i class="fas fa-arrow-up"></i>
                    Sortie
                </span>

            @elseif($isAjustement)

                <span class="stock-mouvement-show-type stock-mouvement-show-type--adjustment">
                    <i class="fas fa-sliders-h"></i>
                    Ajustement
                </span>

            @elseif($isTransfert)

                <span class="stock-mouvement-show-type stock-mouvement-show-type--neutral">
                    <i class="fas fa-exchange-alt"></i>
                    Transfert
                </span>

            @else

                <span class="stock-mouvement-show-type stock-mouvement-show-type--neutral">
                    {{ $typeLabel }}
                </span>

            @endif

        </div>

    </div>


    {{-- =========================================================
         CONTENU PRINCIPAL
    ========================================================== --}}
    <div class="stock-mouvement-show-layout">

        {{-- =====================================================
             COLONNE PRINCIPALE
        ====================================================== --}}
        <div class="stock-mouvement-show-main-column">

            {{-- ARTICLE --}}
            <section class="stock-mouvement-show-card">

                <div class="stock-mouvement-show-card__header">

                    <div>

                        <h2>Article concerné</h2>

                        <p>Article impacté par ce mouvement.</p>

                    </div>

                    <div class="stock-mouvement-show-card__icon">
                        <i class="fas fa-box"></i>
                    </div>

                </div>


                <div class="stock-mouvement-show-card__body">

                    @if($mouvement->stock)

                        <div class="stock-mouvement-show-product">

                            <div class="stock-mouvement-show-product__icon">
                                <i class="fas fa-box-open"></i>
                            </div>

                            <div class="stock-mouvement-show-product__content">

                                <h3>
                                    {{ $mouvement->stock->designation }}
                                </h3>

                                <div class="stock-mouvement-show-product__meta">

                                    @if($mouvement->stock->reference)

                                        <span>
                                            Référence :
                                            <strong>
                                                {{ $mouvement->stock->reference }}
                                            </strong>
                                        </span>

                                    @endif

                                    <span>
                                        ID :
                                        <strong>
                                            #{{ $mouvement->stock->id }}
                                        </strong>
                                    </span>

                                </div>

                            </div>

                        </div>

                    @else

                        <div class="stock-mouvement-show-empty-inline">

                            <i class="fas fa-box-open"></i>

                            <span>
                                L’article associé à ce mouvement
                                n’est pas disponible.
                            </span>

                        </div>

                    @endif

                </div>

            </section>


            {{-- QUANTITÉ --}}
            <section class="stock-mouvement-show-card">

                <div class="stock-mouvement-show-card__header">

                    <div>

                        <h2>Quantité mouvementée</h2>

                        <p>Variation enregistrée lors de l’opération.</p>

                    </div>

                    <div class="stock-mouvement-show-card__icon">
                        <i class="fas fa-sort-amount-up"></i>
                    </div>

                </div>


                <div class="stock-mouvement-show-card__body">

                    <div class="stock-mouvement-show-quantity-panel">

                        <div class="stock-mouvement-show-quantity-panel__icon
                            {{ $isEntree
                                ? 'stock-mouvement-show-quantity-panel__icon--entry'
                                : ($isSortie
                                    ? 'stock-mouvement-show-quantity-panel__icon--exit'
                                    : '') }}">

                            @if($isEntree)

                                <i class="fas fa-arrow-down"></i>

                            @elseif($isSortie)

                                <i class="fas fa-arrow-up"></i>

                            @else

                                <i class="fas fa-sliders-h"></i>

                            @endif

                        </div>


                        <div class="stock-mouvement-show-quantity-panel__content">

                            <span>Quantité</span>

                            <strong>

                                @if($quantiteSignee > 0)
                                    +
                                @elseif($quantiteSignee < 0)
                                    -
                                @endif

                                {{ number_format(abs($quantiteSignee), 2, ',', ' ') }}

                            </strong>

                            <small>
                                {{ $mouvement->stock?->unite ?? 'unité(s)' }}
                            </small>

                        </div>

                    </div>

                </div>

            </section>


            {{-- LOT --}}
            @if($mouvement->lotStock)

                <section class="stock-mouvement-show-card">

                    <div class="stock-mouvement-show-card__header">

                        <div>

                            <h2>Lot concerné</h2>

                            <p>Lot physique impacté par ce mouvement.</p>

                        </div>

                        <div class="stock-mouvement-show-card__icon">
                            <i class="fas fa-layer-group"></i>
                        </div>

                    </div>


                    <div class="stock-mouvement-show-card__body">

                        <div class="stock-mouvement-show-details-grid">

                            <div class="stock-mouvement-show-detail">

                                <span>Numéro de lot</span>

                                <strong>
                                    {{ $mouvement->lotStock->numero_lot ?? ('LOT-' . $mouvement->lotStock->id) }}
                                </strong>

                            </div>


                            @if($mouvement->lotStock->date_entree)

                                <div class="stock-mouvement-show-detail">

                                    <span>Date d'entrée</span>

                                    <strong>
                                        {{ \Carbon\Carbon::parse($mouvement->lotStock->date_entree)->format('d/m/Y') }}
                                    </strong>

                                </div>

                            @endif


                            @if($mouvement->lotStock->date_expiration)

                                <div class="stock-mouvement-show-detail">

                                    <span>Date d'expiration</span>

                                    <strong>
                                        {{ \Carbon\Carbon::parse($mouvement->lotStock->date_expiration)->format('d/m/Y') }}
                                    </strong>

                                </div>

                            @endif


                            <div class="stock-mouvement-show-detail">

                                <span>Quantité actuelle du lot</span>

                                <strong>
                                    {{ number_format((float) $mouvement->lotStock->quantite_actuelle, 2, ',', ' ') }}
                                    {{ $mouvement->stock?->unite ?? '' }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </section>

            @endif


            {{-- EMPLACEMENT --}}
            @if($mouvement->emplacement)

                <section class="stock-mouvement-show-card">

                    <div class="stock-mouvement-show-card__header">

                        <div>

                            <h2>Emplacement</h2>

                            <p>Localisation du mouvement.</p>

                        </div>

                        <div class="stock-mouvement-show-card__icon">
                            <i class="fas fa-warehouse"></i>
                        </div>

                    </div>


                    <div class="stock-mouvement-show-card__body">

                        <div class="stock-mouvement-show-details-grid">

                            <div class="stock-mouvement-show-detail">

                                <span>Emplacement</span>

                                <strong>
                                    {{ $mouvement->emplacement->nom ?? '—' }}
                                </strong>

                            </div>


                            @if($mouvement->emplacement->code)

                                <div class="stock-mouvement-show-detail">

                                    <span>Code</span>

                                    <strong>
                                        {{ $mouvement->emplacement->code }}
                                    </strong>

                                </div>

                            @endif

                        </div>

                    </div>

                </section>

            @endif


            {{-- NOTES --}}
            @if($mouvement->notes)

                <section class="stock-mouvement-show-card">

                    <div class="stock-mouvement-show-card__header">

                        <div>

                            <h2>Notes</h2>

                            <p>Informations complémentaires associées au mouvement.</p>

                        </div>

                        <div class="stock-mouvement-show-card__icon">
                            <i class="fas fa-sticky-note"></i>
                        </div>

                    </div>


                    <div class="stock-mouvement-show-card__body">

                        <div class="stock-mouvement-show-notes">

                            {!! nl2br(e($mouvement->notes)) !!}

                        </div>

                    </div>

                </section>

            @endif


            {{-- ORIGINE / DOCUMENT --}}
            @if($documentReference || $mouvement->bonSortie || $mouvement->projet)

                <section class="stock-mouvement-show-card">

                    <div class="stock-mouvement-show-card__header">

                        <div>

                            <h2>Origine de l’opération</h2>

                            <p>Informations permettant de retrouver la source du mouvement.</p>

                        </div>

                        <div class="stock-mouvement-show-card__icon">
                            <i class="fas fa-file-alt"></i>
                        </div>

                    </div>


                    <div class="stock-mouvement-show-card__body">

                        <div class="stock-mouvement-show-details-grid">

                            @if($documentReference)

                                <div class="stock-mouvement-show-detail">

                                    <span>Référence document</span>

                                    <strong>
                                        {{ $documentReference }}
                                    </strong>

                                </div>

                            @endif


                            @if($mouvement->bonSortie)

                                <div class="stock-mouvement-show-detail">

                                    <span>Bon de sortie</span>

                                    <a
                                        href="{{ route('admin.stocks.sorties.show', $mouvement->bonSortie) }}"
                                        class="stock-mouvement-show-link"
                                    >
                                        {{ $mouvement->bonSortie->numero }}
                                    </a>

                                </div>

                            @endif


                            @if($mouvement->projet)

                                <div class="stock-mouvement-show-detail">

                                    <span>Projet</span>

                                    <strong>
                                        {{ $mouvement->projet->nom
                                            ?? $mouvement->projet->titre
                                            ?? ('Projet #' . $mouvement->projet->id) }}
                                    </strong>

                                </div>

                            @endif

                        </div>

                    </div>

                </section>

            @endif

        </div>


        {{-- =====================================================
             COLONNE LATÉRALE
        ====================================================== --}}
        <aside class="stock-mouvement-show-sidebar">

            {{-- INFORMATIONS GÉNÉRALES --}}
            <section class="stock-mouvement-show-card">

                <div class="stock-mouvement-show-card__header">

                    <div>
                        <h2>Informations</h2>
                    </div>

                    <div class="stock-mouvement-show-card__icon">
                        <i class="fas fa-info-circle"></i>
                    </div>

                </div>


                <div class="stock-mouvement-show-card__body">

                    <div class="stock-mouvement-show-info-list">

                        <div class="stock-mouvement-show-info-item">

                            <span>Identifiant</span>

                            <strong>#{{ $mouvement->id }}</strong>

                        </div>


                        <div class="stock-mouvement-show-info-item">

                            <span>Type</span>

                            <strong>{{ $typeLabel }}</strong>

                        </div>


                        <div class="stock-mouvement-show-info-item">

                            <span>Nature</span>

                            <strong>{{ $natureLabel }}</strong>

                        </div>


                        @if($mouvement->date_mouvement)

                            <div class="stock-mouvement-show-info-item">

                                <span>Date</span>

                                <strong>
                                    {{ \Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m/Y') }}
                                </strong>

                            </div>

                        @endif


                        @if($mouvement->created_at)

                            <div class="stock-mouvement-show-info-item">

                                <span>Enregistré le</span>

                                <strong>
                                    {{ $mouvement->created_at->format('d/m/Y H:i') }}
                                </strong>

                            </div>

                        @endif

                    </div>

                </div>

            </section>


            {{-- UTILISATEUR --}}
            <section class="stock-mouvement-show-card">

                <div class="stock-mouvement-show-card__header">

                    <div>

                        <h2>Traçabilité</h2>

                        <p>Responsable de l’opération.</p>

                    </div>

                    <div class="stock-mouvement-show-card__icon">
                        <i class="fas fa-user-shield"></i>
                    </div>

                </div>


                <div class="stock-mouvement-show-card__body">

                    @if($mouvement->utilisateur)

                        <div class="stock-mouvement-show-user">

                            <div class="stock-mouvement-show-user__avatar">

                                {{ strtoupper(
                                    mb_substr(
                                        $mouvement->utilisateur->name ?? 'U',
                                        0,
                                        1
                                    )
                                ) }}

                            </div>

                            <div class="stock-mouvement-show-user__content">

                                <strong>
                                    {{ $mouvement->utilisateur->name }}
                                </strong>

                                @if($mouvement->utilisateur->email)

                                    <span>
                                        {{ $mouvement->utilisateur->email }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    @else

                        <div class="stock-mouvement-show-system-user">

                            <div class="stock-mouvement-show-system-user__icon">
                                <i class="fas fa-cogs"></i>
                            </div>

                            <div>

                                <strong>Système</strong>

                                <span>Aucun utilisateur associé.</span>

                            </div>

                        </div>

                    @endif

                </div>

            </section>


            {{-- RÉFÉRENCE --}}
            <section class="stock-mouvement-show-card">

                <div class="stock-mouvement-show-card__header">

                    <div>
                        <h2>Référence</h2>
                    </div>

                    <div class="stock-mouvement-show-card__icon">
                        <i class="fas fa-hashtag"></i>
                    </div>

                </div>


                <div class="stock-mouvement-show-card__body">

                    <div class="stock-mouvement-show-reference-box">

                        <span>Référence document</span>

                        <strong>

                            {{ $documentReference
                                ?? 'MVT-' . str_pad($mouvement->id, 6, '0', STR_PAD_LEFT) }}

                        </strong>

                    </div>

                </div>

            </section>


            {{-- TRAÇABILITÉ --}}
            <div class="stock-mouvement-show-traceability">

                <div class="stock-mouvement-show-traceability__icon">
                    <i class="fas fa-shield-alt"></i>
                </div>

                <div class="stock-mouvement-show-traceability__content">

                    <strong>Traçabilité</strong>

                    <p>
                        Ce mouvement fait partie de l’historique
                        du stock. Toute correction doit passer
                        par une opération métier autorisée.
                    </p>

                </div>

            </div>

        </aside>

    </div>


    {{-- =========================================================
         RETOUR
    ========================================================== --}}
    <div class="stock-mouvement-show-footer">

        <a
            href="{{ route('admin.stocks.mouvements.index') }}"
            class="stock-mouvement-show-button stock-mouvement-show-button--secondary"
        >
            <i class="fas fa-arrow-left"></i>
            <span>Retour à l'historique</span>
        </a>

    </div>

</div>

@endsection