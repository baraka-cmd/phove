@extends('layouts.admin')

@section('title', 'Nouvelle entrée de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/entrees-create.css') }}"
    >
@endsection

@section('content')

<div class="stock-entry-page">

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="breadcrumb">

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span class="breadcrumb-separator">/</span>

                <a href="{{ route('admin.stocks.entrees.index') }}">
                    Entrées
                </a>

                <span class="breadcrumb-separator">/</span>

                <span>Nouvelle entrée</span>

            </div>

            <h1 class="page-title">
                Nouvelle entrée de stock
            </h1>

            <p class="page-description">
                Enregistrez une réception de marchandises ou de matériels
                en renseignant l'article, le lot et les informations
                nécessaires à la traçabilité.
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('admin.stocks.entrees.index') }}"
                class="btn btn-secondary"
            >

                <i class="fas fa-arrow-left" aria-hidden="true"></i>

                <span>Retour aux entrées</span>

            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES DE SESSION
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success" role="alert">

            <i class="fas fa-check-circle" aria-hidden="true"></i>

            <div>
                {{ session('success') }}
            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger" role="alert">

            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>

            <div>
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- =========================================================
         ERREURS DE VALIDATION (résumé global)
    ========================================================== --}}
    @if($errors->any())

        <div class="alert alert-danger validation-alert" role="alert">

            <div class="alert-icon">

                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>

            </div>

            <div class="alert-content">

                <strong>
                    Impossible d’enregistrer cette entrée.
                </strong>

                <ul class="error-list">

                    @foreach($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =========================================================
         FORMULAIRE
    ========================================================== --}}
    <form
        action="{{ route('admin.stocks.entrees.store') }}"
        method="POST"
        class="stock-entry-form"
        novalidate
    >

        @csrf


        {{-- =====================================================
             SECTION 1 — ARTICLE
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-icon">
                    <i class="fas fa-box" aria-hidden="true"></i>
                </div>

                <div>

                    <h2 class="form-card-title">
                        Article
                    </h2>

                    <p class="form-card-description">
                        Sélectionnez l’article qui entre dans le stock.
                    </p>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-grid">

                    {{-- ARTICLE --}}
                    <div class="form-group form-group-full">

                        <label for="stock_id" class="form-label">
                            Article
                            <span class="required">*</span>
                        </label>

                        <select
                            id="stock_id"
                            name="stock_id"
                            class="form-control @error('stock_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                Sélectionner un article
                            </option>

                            @foreach($stocks as $stockItem)

                                <option
                                    value="{{ $stockItem->id }}"
                                    @selected(old('stock_id') == $stockItem->id)
                                >
                                    {{ $stockItem->reference }}
                                    —
                                    {{ $stockItem->designation }}
                                </option>

                            @endforeach

                        </select>

                        @error('stock_id')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="field-help">
                            Seuls les articles actifs peuvent recevoir
                            une nouvelle entrée.
                        </div>

                    </div>


                    {{-- QUANTITÉ --}}
                    <div class="form-group">

                        <label for="quantite" class="form-label">
                            Quantité reçue
                            <span class="required">*</span>
                        </label>

                        <div class="input-with-unit">

                            <input
                                type="number"
                                id="quantite"
                                name="quantite"
                                value="{{ old('quantite') }}"
                                class="form-control @error('quantite') is-invalid @enderror"
                                min="0.01"
                                step="0.01"
                                inputmode="decimal"
                                placeholder="Ex. 500"
                                required
                            >

                            <span class="input-unit" id="stock-unit">
                                unité
                            </span>

                        </div>

                        @error('quantite')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="field-help">
                            La quantité doit être strictement supérieure à zéro.
                        </div>

                    </div>


                    {{-- EMPLACEMENT --}}
                    <div class="form-group">

                        <label for="emplacement_id" class="form-label">
                            Emplacement
                        </label>

                        <select
                            id="emplacement_id"
                            name="emplacement_id"
                            class="form-control @error('emplacement_id') is-invalid @enderror"
                        >

                            <option value="">
                                Sélectionner un emplacement
                            </option>

                            @foreach($emplacements as $emplacement)

                                <option
                                    value="{{ $emplacement->id }}"
                                    @selected(old('emplacement_id') == $emplacement->id)
                                >
                                    {{ $emplacement->code }}
                                    —
                                    {{ $emplacement->nom }}
                                </option>

                            @endforeach

                        </select>

                        @error('emplacement_id')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="field-help">
                            Lieu physique où les produits seront réceptionnés.
                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 2 — INFORMATIONS DU LOT
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-icon">
                    <i class="fas fa-layer-group" aria-hidden="true"></i>
                </div>

                <div>

                    <h2 class="form-card-title">
                        Informations du lot
                    </h2>

                    <p class="form-card-description">
                        Ces informations permettent d’assurer la traçabilité
                        et la gestion FEFO des produits.
                    </p>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-grid">

                    {{-- NUMÉRO DE LOT --}}
                    <div class="form-group">

                        <label for="numero_lot" class="form-label">
                            Numéro de lot
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="numero_lot"
                            name="numero_lot"
                            value="{{ old('numero_lot') }}"
                            class="form-control @error('numero_lot') is-invalid @enderror"
                            maxlength="100"
                            required
                            placeholder="Ex. MED-2026-001"
                        >

                        @error('numero_lot')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="field-help">
                            Obligatoire pour garantir la traçabilité.
                        </div>

                    </div>


                    {{-- DATE DE FABRICATION --}}
                    <div class="form-group">

                        <label for="date_fabrication" class="form-label">
                            Date de fabrication
                        </label>

                        <input
                            type="date"
                            id="date_fabrication"
                            name="date_fabrication"
                            value="{{ old('date_fabrication') }}"
                            class="form-control @error('date_fabrication') is-invalid @enderror"
                        >

                        @error('date_fabrication')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- DATE D'EXPIRATION --}}
                    <div class="form-group">

                        <label for="date_expiration" class="form-label">
                            Date d’expiration
                        </label>

                        <input
                            type="date"
                            id="date_expiration"
                            name="date_expiration"
                            value="{{ old('date_expiration') }}"
                            class="form-control @error('date_expiration') is-invalid @enderror"
                        >

                        @error('date_expiration')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="field-help">
                            Obligatoire pour les produits soumis à expiration.
                        </div>

                    </div>


                    {{-- COÛT UNITAIRE --}}
                    <div class="form-group">

                        <label for="cout_unitaire" class="form-label">
                            Coût unitaire
                        </label>

                        <div class="input-with-suffix">

                            <input
                                type="number"
                                id="cout_unitaire"
                                name="cout_unitaire"
                                value="{{ old('cout_unitaire') }}"
                                class="form-control @error('cout_unitaire') is-invalid @enderror"
                                min="0"
                                step="0.01"
                                inputmode="decimal"
                                placeholder="0.00"
                            >

                            <span class="input-suffix">
                                USD
                            </span>

                        </div>

                        @error('cout_unitaire')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 3 — INFORMATIONS DE RÉCEPTION
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-icon">
                    <i class="fas fa-truck-loading" aria-hidden="true"></i>
                </div>

                <div>

                    <h2 class="form-card-title">
                        Réception
                    </h2>

                    <p class="form-card-description">
                        Renseignez l’origine et les références documentaires
                        de cette entrée.
                    </p>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-grid">

                    {{-- FOURNISSEUR --}}
                    <div class="form-group">

                        <label for="fournisseur" class="form-label">
                            Fournisseur / Donateur
                        </label>

                        <input
                            type="text"
                            id="fournisseur"
                            name="fournisseur"
                            value="{{ old('fournisseur') }}"
                            class="form-control @error('fournisseur') is-invalid @enderror"
                            maxlength="255"
                            placeholder="Nom du fournisseur ou donateur"
                        >

                        @error('fournisseur')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- RÉFÉRENCE DOCUMENT --}}
                    <div class="form-group">

                        <label for="reference_document" class="form-label">
                            Référence du document
                        </label>

                        <input
                            type="text"
                            id="reference_document"
                            name="reference_document"
                            value="{{ old('reference_document') }}"
                            class="form-control @error('reference_document') is-invalid @enderror"
                            maxlength="100"
                            placeholder="Ex. BL-2026-0045"
                        >

                        @error('reference_document')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="field-help">
                            Bon de livraison, facture, bordereau de don,
                            procès-verbal, etc.
                        </div>

                    </div>


                    {{-- DATE D'ENTRÉE --}}
                    <div class="form-group">

                        <label for="date_entree" class="form-label">
                            Date de réception
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            id="date_entree"
                            name="date_entree"
                            value="{{ old('date_entree', now()->format('Y-m-d')) }}"
                            class="form-control @error('date_entree') is-invalid @enderror"
                            required
                        >

                        @error('date_entree')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- NATURE --}}
                    <div class="form-group">

                        <label for="nature" class="form-label">
                            Nature de l’entrée
                            <span class="required">*</span>
                        </label>

                        <select
                            id="nature"
                            name="nature"
                            class="form-control @error('nature') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                Sélectionner une nature
                            </option>

                            <option
                                value="achat"
                                @selected(old('nature') === 'achat')
                            >
                                Achat
                            </option>

                            <option
                                value="don"
                                @selected(old('nature') === 'don')
                            >
                                Don
                            </option>

                            <option
                                value="retour"
                                @selected(old('nature') === 'retour')
                            >
                                Retour
                            </option>

                        </select>

                        @error('nature')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 4 — PROJET
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-icon">
                    <i class="fas fa-project-diagram" aria-hidden="true"></i>
                </div>

                <div>

                    <h2 class="form-card-title">
                        Projet
                    </h2>

                    <p class="form-card-description">
                        Associez cette entrée à un projet lorsqu’elle
                        est financée ou destinée à celui-ci.
                    </p>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-grid">

                    <div class="form-group form-group-full">

                        <label for="projet_id" class="form-label">
                            Projet
                        </label>

                        <select
                            id="projet_id"
                            name="projet_id"
                            class="form-control @error('projet_id') is-invalid @enderror"
                        >

                            <option value="">
                                Aucun projet spécifique
                            </option>

                            @foreach($projets as $projet)

                                <option
                                    value="{{ $projet->id }}"
                                    @selected(old('projet_id') == $projet->id)
                                >
                                    {{ $projet->nom ?? $projet->titre ?? ('Projet #' . $projet->id) }}
                                </option>

                            @endforeach

                        </select>

                        @error('projet_id')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 5 — NOTES
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-icon">
                    <i class="fas fa-sticky-note" aria-hidden="true"></i>
                </div>

                <div>

                    <h2 class="form-card-title">
                        Notes
                    </h2>

                    <p class="form-card-description">
                        Informations complémentaires utiles à la traçabilité.
                    </p>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-group">

                    <label for="notes" class="form-label">
                        Notes complémentaires
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        class="form-control textarea-control @error('notes') is-invalid @enderror"
                        rows="5"
                        maxlength="5000"
                        placeholder="Ajoutez une observation concernant cette réception..."
                    >{{ old('notes') }}</textarea>

                    @error('notes')

                        <div class="field-error">
                            {{ $message }}
                        </div>

                    @enderror

                    <div class="field-help">
                        Maximum 5000 caractères.
                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             RAPPEL DE TRAÇABILITÉ
        ====================================================== --}}
        <div class="traceability-notice">

            <div class="traceability-icon">
                <i class="fas fa-shield-alt" aria-hidden="true"></i>
            </div>

            <div class="traceability-content">

                <strong>
                    Opération traçable
                </strong>

                <p>
                    Cette réception créera ou alimentera un lot et générera
                    un mouvement d’entrée. La quantité ne sera pas modifiée
                    directement depuis l’interface.
                </p>

            </div>

        </div>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}
        <div class="form-actions">

            <a
                href="{{ route('admin.stocks.entrees.index') }}"
                class="btn btn-secondary"
            >

                <i class="fas fa-times" aria-hidden="true"></i>

                <span>Annuler</span>

            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >

                <i class="fas fa-save" aria-hidden="true"></i>

                <span>Enregistrer l’entrée</span>

            </button>

        </div>

    </form>

</div>

@endsection