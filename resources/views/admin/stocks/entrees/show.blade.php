@extends('layouts.admin')

@section('title', 'Détail de l’entrée de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/entrees-show.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-entree-show">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-text">

                <div class="breadcrumb">

                    <a href="{{ route('admin.stocks.index') }}">
                        Stocks
                    </a>

                    <span class="breadcrumb-separator">/</span>

                    <a href="{{ route('admin.stocks.entrees.index') }}">
                        Entrées
                    </a>

                    <span class="breadcrumb-separator">/</span>

                    <span>Détail</span>

                </div>

                <h1 class="page-title">
                    Détail de l’entrée
                </h1>

                <p class="page-description">
                    Consultez les informations de réception,
                    le lot enregistré, les quantités et la traçabilité
                    de cette opération.
                </p>

            </div>

            <div class="page-header-actions">

                <a
                    href="{{ route('admin.stocks.entrees.index') }}"
                    class="btn btn-secondary"
                >

                    <i class="fas fa-arrow-left"></i>

                    <span>Retour</span>

                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success" role="alert">

            <i class="fas fa-check-circle"></i>

            <div>
                {{ session('success') }}
            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger" role="alert">

            <i class="fas fa-exclamation-circle"></i>

            <div>
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         INFORMATIONS GÉNÉRALES
    ========================================================== --}}
    <section class="content-section">

        <div class="section-header">

            <div>

                <h2 class="section-title">
                    Informations de l’entrée
                </h2>

                <p class="section-description">
                    Informations générales relatives à cette réception.
                </p>

            </div>

            @php
                $statut = $mouvement->statut ?? null;
                $statutLabel = $statut instanceof \BackedEnum
                    ? $statut->value
                    : $statut;
            @endphp

            @if($statutLabel)

                <span class="status-badge status-{{ \Illuminate\Support\Str::slug($statutLabel) }}">

                    {{ ucfirst(str_replace('_', ' ', $statutLabel)) }}

                </span>

            @endif

        </div>


        <div class="information-grid">

            {{-- RÉFÉRENCE --}}
            <div class="information-card">

                <span class="information-label">
                    Référence
                </span>

                <strong class="information-value">

                    {{ $mouvement->reference_document
                        ?? ('ENT-' . str_pad($mouvement->id, 6, '0', STR_PAD_LEFT)) }}

                </strong>

            </div>


            {{-- DATE DE RÉCEPTION --}}
            <div class="information-card">

                <span class="information-label">
                    Date de réception
                </span>

                <strong class="information-value">

                    @if($mouvement->lotStock?->date_entree)

                        {{ \Carbon\Carbon::parse(
                            $mouvement->lotStock->date_entree
                        )->format('d/m/Y') }}

                    @elseif($mouvement->date_mouvement)

                        {{ \Carbon\Carbon::parse(
                            $mouvement->date_mouvement
                        )->format('d/m/Y') }}

                    @else

                        —

                    @endif

                </strong>

            </div>


            {{-- DATE DU MOUVEMENT --}}
            <div class="information-card">

                <span class="information-label">
                    Date du mouvement
                </span>

                <strong class="information-value">

                    {{ $mouvement->date_mouvement
                        ? \Carbon\Carbon::parse(
                            $mouvement->date_mouvement
                        )->format('d/m/Y')
                        : '—' }}

                </strong>

            </div>


            {{-- RÉFÉRENCE DOCUMENT --}}
            <div class="information-card">

                <span class="information-label">
                    Référence document
                </span>

                <strong class="information-value">

                    {{ $mouvement->reference_document ?? '—' }}

                </strong>

            </div>


            {{-- NATURE --}}
            <div class="information-card">

                <span class="information-label">
                    Nature de l’entrée
                </span>

                <strong class="information-value">

                    @php
                        $nature = $mouvement->nature ?? null;
                        $natureLabel = $nature instanceof \App\Enums\NatureMouvement
                            ? $nature->libelle()
                            : ($nature
                                ? ucfirst(str_replace('_', ' ', (string) $nature))
                                : '—');
                    @endphp

                    {{ $natureLabel }}

                </strong>

            </div>


            {{-- FOURNISSEUR --}}
            <div class="information-card">

                <span class="information-label">
                    Fournisseur
                </span>

                <strong class="information-value">

                    {{ $mouvement->lotStock?->fournisseur ?? '—' }}

                </strong>

            </div>

        </div>

    </section>


    {{-- =========================================================
         ARTICLE
    ========================================================== --}}
    <section class="content-section">

        <div class="section-header">

            <div>

                <h2 class="section-title">
                    Article concerné
                </h2>

                <p class="section-description">
                    Article ajouté au stock lors de cette réception.
                </p>

            </div>

        </div>


        @if($mouvement->stock)

            <div class="stock-summary">

                <div class="stock-summary-main">

                    <div class="stock-icon">
                        <i class="fas fa-box"></i>
                    </div>

                    <div class="stock-summary-content">

                        <span class="stock-reference">
                            {{ $mouvement->stock->reference }}
                        </span>

                        <h3 class="stock-designation">
                            {{ $mouvement->stock->designation }}
                        </h3>

                        <span class="stock-category">
                            {{ ucfirst($mouvement->stock->categorie ?? 'Autre') }}
                        </span>

                    </div>

                </div>


                <div class="stock-summary-action">

                    <a
                        href="{{ route('admin.stocks.show', $mouvement->stock) }}"
                        class="btn btn-outline"
                    >

                        <i class="fas fa-eye"></i>

                        Voir l'article

                    </a>

                </div>

            </div>

        @else

            <div class="empty-state compact">

                <i class="fas fa-box-open"></i>

                <h3>
                    Article introuvable
                </h3>

                <p>
                    L’article associé à cette entrée
                    n’est plus disponible.
                </p>

            </div>

        @endif

    </section>


    {{-- =========================================================
         QUANTITÉS
    ========================================================== --}}
    <section class="content-section">

        <div class="section-header">

            <div>

                <h2 class="section-title">
                    Quantités
                </h2>

                <p class="section-description">
                    Quantité enregistrée lors de cette réception.
                </p>

            </div>

        </div>


        <div class="quantity-grid">

            {{-- QUANTITÉ REÇUE --}}
            <div class="quantity-card">

                <span class="quantity-label">
                    Quantité reçue
                </span>

                <strong class="quantity-value">

                    {{ number_format(
                        (float) ($mouvement->quantite ?? 0),
                        2,
                        ',',
                        ' '
                    ) }}

                </strong>

                <span class="quantity-unit">
                    {{ $mouvement->stock?->unite ?? '' }}
                </span>

            </div>


            {{-- QUANTITÉ ACTUELLE DU LOT --}}
            @if($mouvement->lotStock)

                <div class="quantity-card">

                    <span class="quantity-label">
                        Quantité actuelle du lot
                    </span>

                    <strong class="quantity-value">

                        {{ number_format(
                            (float) (
                                $mouvement->lotStock->quantite_actuelle ?? 0
                            ),
                            2,
                            ',',
                            ' '
                        ) }}

                    </strong>

                    <span class="quantity-unit">
                        {{ $mouvement->stock?->unite ?? '' }}
                    </span>

                </div>

            @endif

        </div>

    </section>


    {{-- =========================================================
         LOT
    ========================================================== --}}
    @if($mouvement->lotStock)

        <section class="content-section">

            <div class="section-header">

                <div>

                    <h2 class="section-title">
                        Lot de stock
                    </h2>

                    <p class="section-description">
                        Informations de traçabilité du lot réceptionné.
                    </p>

                </div>

            </div>


            <div class="lot-card">

                <div class="lot-header">

                    <div class="lot-number">

                        <span class="information-label">
                            Numéro de lot
                        </span>

                        <strong>
                            {{ $mouvement->lotStock->numero_lot ?? ('LOT-' . $mouvement->lotStock->id) }}
                        </strong>

                    </div>


                    @php
                        $statutLot = $mouvement->lotStock->statut ?? null;
                        $statutLotLabel = $statutLot instanceof \App\Enums\StatutLot
                            ? $statutLot->libelle()
                            : ($statutLot
                                ? ucfirst(str_replace('_', ' ', (string) $statutLot))
                                : null);
                    @endphp

                    @if($statutLotLabel)

                        <span class="status-badge status-{{ \Illuminate\Support\Str::slug($statutLotLabel) }}">

                            {{ $statutLotLabel }}

                        </span>

                    @endif

                </div>


                <div class="lot-grid">

                    {{-- FABRICATION --}}
                    <div class="lot-information">

                        <span class="information-label">
                            Date de fabrication
                        </span>

                        <strong>

                            {{ $mouvement->lotStock->date_fabrication
                                ? \Carbon\Carbon::parse(
                                    $mouvement->lotStock->date_fabrication
                                )->format('d/m/Y')
                                : '—' }}

                        </strong>

                    </div>


                    {{-- ENTRÉE --}}
                    <div class="lot-information">

                        <span class="information-label">
                            Date d'entrée
                        </span>

                        <strong>

                            {{ $mouvement->lotStock->date_entree
                                ? \Carbon\Carbon::parse(
                                    $mouvement->lotStock->date_entree
                                )->format('d/m/Y')
                                : '—' }}

                        </strong>

                    </div>


                    {{-- EXPIRATION --}}
                    <div class="lot-information">

                        <span class="information-label">
                            Date d'expiration
                        </span>

                        <strong>

                            {{ $mouvement->lotStock->date_expiration
                                ? \Carbon\Carbon::parse(
                                    $mouvement->lotStock->date_expiration
                                )->format('d/m/Y')
                                : 'Non applicable' }}

                        </strong>

                    </div>


                    {{-- QUANTITÉ INITIALE --}}
                    <div class="lot-information">

                        <span class="information-label">
                            Quantité initiale
                        </span>

                        <strong>

                            {{ number_format(
                                (float) (
                                    $mouvement->lotStock->quantite_initiale ?? 0
                                ),
                                2,
                                ',',
                                ' '
                            ) }}

                            {{ $mouvement->stock?->unite ?? '' }}

                        </strong>

                    </div>


                    {{-- QUANTITÉ ACTUELLE --}}
                    <div class="lot-information">

                        <span class="information-label">
                            Quantité actuelle
                        </span>

                        <strong>

                            {{ number_format(
                                (float) (
                                    $mouvement->lotStock->quantite_actuelle ?? 0
                                ),
                                2,
                                ',',
                                ' '
                            ) }}

                            {{ $mouvement->stock?->unite ?? '' }}

                        </strong>

                    </div>


                    {{-- COÛT UNITAIRE --}}
                    <div class="lot-information">

                        <span class="information-label">
                            Coût unitaire
                        </span>

                        <strong>

                            @if($mouvement->lotStock->cout_unitaire !== null)

                                {{ number_format(
                                    (float) $mouvement->lotStock->cout_unitaire,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                $

                            @else

                                —

                            @endif

                        </strong>

                    </div>


                    {{-- FOURNISSEUR --}}
                    <div class="lot-information">

                        <span class="information-label">
                            Fournisseur
                        </span>

                        <strong>

                            {{ $mouvement->lotStock->fournisseur ?? '—' }}

                        </strong>

                    </div>

                </div>


                @if($mouvement->lotStock->reference_document)

                    <div class="lot-document">

                        <span class="information-label">
                            Document associé
                        </span>

                        <strong>

                            {{ $mouvement->lotStock->reference_document }}

                        </strong>

                    </div>

                @endif

            </div>

        </section>

    @endif


    {{-- =========================================================
         EMPLACEMENT
    ========================================================== --}}
    @if($mouvement->emplacement)

        <section class="content-section">

            <div class="section-header">

                <div>

                    <h2 class="section-title">
                        Emplacement
                    </h2>

                    <p class="section-description">
                        Lieu physique associé à la réception.
                    </p>

                </div>

            </div>


            <div class="location-card">

                <div class="location-icon">
                    <i class="fas fa-warehouse"></i>
                </div>

                <div class="location-content">

                    <span class="information-label">
                        Emplacement
                    </span>

                    <strong>

                        {{ $mouvement->emplacement->nom ?? '—' }}

                    </strong>

                    @if($mouvement->emplacement->code)

                        <span class="location-code">

                            {{ $mouvement->emplacement->code }}

                        </span>

                    @endif

                </div>

            </div>

        </section>

    @endif


    {{-- =========================================================
         PROJET
    ========================================================== --}}
    @if($mouvement->projet)

        <section class="content-section">

            <div class="section-header">

                <div>

                    <h2 class="section-title">
                        Projet associé
                    </h2>

                    <p class="section-description">
                        Projet bénéficiaire ou concerné
                        par cette réception.
                    </p>

                </div>

            </div>


            <div class="project-card">

                <div class="project-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>

                <div class="project-content">

                    <span class="information-label">
                        Projet
                    </span>

                    <strong>

                        {{ $mouvement->projet->nom
                            ?? $mouvement->projet->titre
                            ?? 'Projet' }}

                    </strong>

                </div>

            </div>

        </section>

    @endif


    {{-- =========================================================
         MOTIF / NOTES
    ========================================================== --}}
    @if($mouvement->motif || $mouvement->notes)

        <section class="content-section">

            <div class="section-header">

                <div>

                    <h2 class="section-title">
                        Informations complémentaires
                    </h2>

                </div>

            </div>


            @if($mouvement->motif)

                <div class="text-information">

                    <span class="information-label">
                        Motif
                    </span>

                    <p>
                        {{ $mouvement->motif }}
                    </p>

                </div>

            @endif


            @if($mouvement->notes)

                <div class="text-information">

                    <span class="information-label">
                        Notes
                    </span>

                    <p>
                        {!! nl2br(e($mouvement->notes)) !!}
                    </p>

                </div>

            @endif

        </section>

    @endif


    {{-- =========================================================
         TRAÇABILITÉ
    ========================================================== --}}
    <section class="content-section">

        <div class="section-header">

            <div>

                <h2 class="section-title">
                    Traçabilité
                </h2>

                <p class="section-description">
                    Informations permettant d’identifier
                    l’origine de l’opération.
                </p>

            </div>

        </div>


        <div class="audit-grid">

            {{-- CRÉATION --}}
            <div class="audit-item">

                <span class="information-label">
                    Créée le
                </span>

                <strong>

                    {{ $mouvement->created_at
                        ? $mouvement->created_at->format('d/m/Y à H:i')
                        : '—' }}

                </strong>

            </div>


            {{-- MODIFICATION --}}
            <div class="audit-item">

                <span class="information-label">
                    Dernière modification
                </span>

                <strong>

                    {{ $mouvement->updated_at
                        ? $mouvement->updated_at->format('d/m/Y à H:i')
                        : '—' }}

                </strong>

            </div>


            {{-- UTILISATEUR --}}
            @if($mouvement->utilisateur)

                <div class="audit-item">

                    <span class="information-label">
                        Enregistrée par
                    </span>

                    <strong>

                        {{ $mouvement->utilisateur->name
                            ?? $mouvement->utilisateur->nom
                            ?? 'Utilisateur' }}

                    </strong>

                </div>

            @endif

        </div>

    </section>


    {{-- =========================================================
         MOUVEMENT
    ========================================================== --}}
    <section class="content-section">

        <div class="section-header">

            <div>

                <h2 class="section-title">
                    Mouvement de stock
                </h2>

                <p class="section-description">
                    Événement enregistré dans l’historique du stock.
                </p>

            </div>

        </div>


        <div class="movement-card">

            <div class="movement-icon">

                <i class="fas fa-arrow-down"></i>

            </div>

            <div class="movement-content">

                <span class="information-label">
                    Identifiant du mouvement
                </span>

                <strong>

                    {{ $mouvement->id }}

                </strong>

                <span class="movement-type">

                    Entrée de stock

                </span>

            </div>

        </div>

    </section>


    {{-- =========================================================
         ACTIONS
    ========================================================== --}}
    <div class="page-actions">

        <a
            href="{{ route('admin.stocks.entrees.index') }}"
            class="btn btn-secondary"
        >

            <i class="fas fa-arrow-left"></i>

            Retour aux entrées

        </a>


        @if($mouvement->stock)

            <a
                href="{{ route('admin.stocks.show', $mouvement->stock) }}"
                class="btn btn-primary"
            >

                <i class="fas fa-box"></i>

                Voir le stock

            </a>

        @endif

    </div>

</div>

@endsection