@extends('layouts.admin')

@section('title', 'Nouvel article de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/create.css') }}"
    >
@endsection

@section('content')

<div
    class="admin-content stock-create-page"
    data-stock-create
>

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-text">

                <div class="page-breadcrumb">

                    <a
                        href="{{ route('admin.stocks.index') }}"
                        class="breadcrumb-link"
                    >
                        Stocks
                    </a>

                    <span class="breadcrumb-separator">/</span>

                    <span class="breadcrumb-current">
                        Nouvel article
                    </span>

                </div>


                <h1 class="page-title">
                    Nouvel article de stock
                </h1>

                <p class="page-description">
                    Créez la fiche de référence d'un article qui pourra
                    ensuite être approvisionné, stocké et distribué.
                </p>

            </div>


            <div class="page-header-actions">

                <a
                    href="{{ route('admin.stocks.index') }}"
                    class="btn btn-secondary"
                >
                    <i
                        class="fa-solid fa-arrow-left"
                        aria-hidden="true"
                    ></i>

                    <span>Retour aux stocks</span>
                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ERREURS DE VALIDATION (résumé global)
    ========================================================== --}}
    @if($errors->any())

        <div
            class="alert alert-danger validation-summary"
            role="alert"
            aria-labelledby="validation-summary-title"
        >

            <div class="alert-icon">
                <i
                    class="fa-solid fa-triangle-exclamation"
                    aria-hidden="true"
                ></i>
            </div>

            <div class="alert-content">

                <strong id="validation-summary-title">
                    Le formulaire contient des erreurs.
                </strong>

                <ul class="validation-error-list">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =========================================================
         INFORMATION ARCHITECTURALE
    ========================================================== --}}
    <div class="info-banner">

        <div class="info-banner-icon">

            <i
                class="fa-solid fa-circle-info"
                aria-hidden="true"
            ></i>

        </div>

        <div class="info-banner-content">

            <strong>
                Création d'un article
            </strong>

            <p>
                Cette étape crée uniquement la fiche de l'article.
                Les quantités physiques seront enregistrées plus tard
                lors des opérations d'entrée en stock.
            </p>

        </div>

    </div>


    {{-- =========================================================
         FORMULAIRE
    ========================================================== --}}
    <form
        action="{{ route('admin.stocks.store') }}"
        method="POST"
        class="stock-form"
        id="stock-create-form"
        data-stock-form
        novalidate
    >

        @csrf


        {{-- =====================================================
             SECTION 1 — INFORMATIONS PRINCIPALES
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-title-wrapper">

                    <div class="form-card-icon">

                        <i
                            class="fa-solid fa-box"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <h2 class="form-card-title">
                            Informations de l'article
                        </h2>

                        <p class="form-card-description">
                            Identifiez précisément le produit ou
                            matériel qui sera géré dans le stock.
                        </p>

                    </div>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-grid">


                    {{-- -------------------------------------------------
                         RÉFÉRENCE
                    -------------------------------------------------- --}}
                    <div class="form-group">

                        <label
                            for="reference"
                            class="form-label"
                        >
                            Référence

                            <span
                                class="required-mark"
                                aria-hidden="true"
                            >
                                *
                            </span>
                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">
                                <i
                                    class="fa-solid fa-barcode"
                                    aria-hidden="true"
                                ></i>
                            </span>

                            <input
                                type="text"
                                id="reference"
                                name="reference"
                                value="{{ old('reference') }}"
                                class="form-control @error('reference') is-invalid @enderror"
                                maxlength="50"
                                required
                                autocomplete="off"
                                placeholder="Ex. MED-001"
                                data-stock-reference
                            >

                        </div>


                        <div class="form-help">
                            Code interne unique permettant d'identifier
                            l'article.
                        </div>


                        @error('reference')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- -------------------------------------------------
                         DÉSIGNATION
                    -------------------------------------------------- --}}
                    <div class="form-group">

                        <label
                            for="designation"
                            class="form-label"
                        >
                            Désignation

                            <span
                                class="required-mark"
                                aria-hidden="true"
                            >
                                *
                            </span>
                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">
                                <i
                                    class="fa-solid fa-tag"
                                    aria-hidden="true"
                                ></i>
                            </span>

                            <input
                                type="text"
                                id="designation"
                                name="designation"
                                value="{{ old('designation') }}"
                                class="form-control @error('designation') is-invalid @enderror"
                                maxlength="255"
                                required
                                placeholder="Ex. Riz blanc"
                            >

                        </div>


                        <div class="form-help">
                            Nom clair et compréhensible de l'article.
                        </div>


                        @error('designation')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- -------------------------------------------------
                         CATÉGORIE
                    -------------------------------------------------- --}}
                    <div class="form-group">

                        <label
                            for="categorie"
                            class="form-label"
                        >
                            Catégorie

                            <span
                                class="required-mark"
                                aria-hidden="true"
                            >
                                *
                            </span>
                        </label>


                        <div class="select-wrapper">

                            <select
                                id="categorie"
                                name="categorie"
                                class="form-control form-select @error('categorie') is-invalid @enderror"
                                required
                                data-stock-category
                            >

                                <option value="">
                                    Sélectionner une catégorie
                                </option>

                                <option
                                    value="vivres"
                                    @selected(old('categorie') === 'vivres')
                                >
                                    Vivres
                                </option>

                                <option
                                    value="medicaments"
                                    @selected(old('categorie') === 'medicaments')
                                >
                                    Médicaments
                                </option>

                                <option
                                    value="habillement"
                                    @selected(old('categorie') === 'habillement')
                                >
                                    Habillement
                                </option>

                                <option
                                    value="abris"
                                    @selected(old('categorie') === 'abris')
                                >
                                    Abris
                                </option>

                                <option
                                    value="materiel"
                                    @selected(old('categorie') === 'materiel')
                                >
                                    Matériel
                                </option>

                                <option
                                    value="semences"
                                    @selected(old('categorie') === 'semences')
                                >
                                    Semences
                                </option>

                                <option
                                    value="outils"
                                    @selected(old('categorie') === 'outils')
                                >
                                    Outils
                                </option>

                                <option
                                    value="autre"
                                    @selected(old('categorie') === 'autre')
                                >
                                    Autre
                                </option>

                            </select>

                        </div>


                        <div class="form-help">
                            Permet de classer et filtrer les articles.
                        </div>


                        @error('categorie')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- -------------------------------------------------
                         UNITÉ
                    -------------------------------------------------- --}}
                    <div class="form-group">

                        <label
                            for="unite"
                            class="form-label"
                        >
                            Unité de mesure

                            <span
                                class="required-mark"
                                aria-hidden="true"
                            >
                                *
                            </span>
                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">
                                <i
                                    class="fa-solid fa-scale-balanced"
                                    aria-hidden="true"
                                ></i>
                            </span>

                            <input
                                type="text"
                                id="unite"
                                name="unite"
                                value="{{ old('unite') }}"
                                class="form-control @error('unite') is-invalid @enderror"
                                maxlength="20"
                                required
                                placeholder="Ex. kg"
                            >

                        </div>


                        <div class="form-help">
                            Exemples : kg, litre, sac, boîte, pièce.
                        </div>


                        @error('unite')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- -------------------------------------------------
                         SEUIL D'ALERTE
                    -------------------------------------------------- --}}
                    <div class="form-group">

                        <label
                            for="seuil_alerte"
                            class="form-label"
                        >
                            Seuil d'alerte
                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">
                                <i
                                    class="fa-solid fa-bell"
                                    aria-hidden="true"
                                ></i>
                            </span>

                            <input
                                type="number"
                                id="seuil_alerte"
                                name="seuil_alerte"
                                value="{{ old('seuil_alerte') }}"
                                class="form-control @error('seuil_alerte') is-invalid @enderror"
                                min="0"
                                step="0.01"
                                inputmode="decimal"
                                placeholder="Ex. 50"
                            >

                        </div>


                        <div class="form-help">
                            Quantité à partir de laquelle une alerte
                            de stock faible pourra être déclenchée.
                            Laissez vide pour désactiver l'alerte.
                        </div>


                        @error('seuil_alerte')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 2 — EMPLACEMENT PRINCIPAL
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-title-wrapper">

                    <div class="form-card-icon">

                        <i
                            class="fa-solid fa-warehouse"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <h2 class="form-card-title">
                            Emplacement principal
                        </h2>

                        <p class="form-card-description">
                            Définissez l'emplacement habituel de
                            stockage de cet article.
                        </p>

                    </div>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-grid">

                    <div class="form-group form-group-full">

                        <label
                            for="emplacement_principal_id"
                            class="form-label"
                        >
                            Emplacement principal
                        </label>


                        <div class="select-wrapper">

                            <select
                                id="emplacement_principal_id"
                                name="emplacement_principal_id"
                                class="form-control form-select @error('emplacement_principal_id') is-invalid @enderror"
                                data-stock-location
                            >

                                <option value="">
                                    Aucun emplacement principal
                                </option>

                                @foreach($emplacements as $emplacement)

                                    <option
                                        value="{{ $emplacement->id }}"
                                        @selected(
                                            (string) old('emplacement_principal_id')
                                            ===
                                            (string) $emplacement->id
                                        )
                                    >
                                        {{ $emplacement->code }}
                                        —
                                        {{ $emplacement->nom }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="form-help">
                            L'emplacement principal est une référence
                            organisationnelle. Les mouvements peuvent
                            ensuite être associés à d'autres emplacements.
                        </div>


                        @error('emplacement_principal_id')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 3 — DESCRIPTION
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-title-wrapper">

                    <div class="form-card-icon">

                        <i
                            class="fa-solid fa-align-left"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <h2 class="form-card-title">
                            Description
                        </h2>

                        <p class="form-card-description">
                            Ajoutez les informations complémentaires
                            utiles à l'identification de l'article.
                        </p>

                    </div>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-group form-group-full">

                    <label
                        for="description"
                        class="form-label"
                    >
                        Description
                    </label>


                    <textarea
                        id="description"
                        name="description"
                        class="form-control form-textarea @error('description') is-invalid @enderror"
                        rows="6"
                        maxlength="5000"
                        placeholder="Décrivez l'article, ses caractéristiques ou toute information utile..."
                        data-stock-description
                    >{{ old('description') }}</textarea>


                    <div class="form-footer">

                        <div class="form-help">
                            Informations complémentaires sur l'article.
                        </div>

                        <div
                            class="character-counter"
                            data-stock-description-counter
                        >
                            0 caractères
                        </div>

                    </div>


                    @error('description')

                        <div class="field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 4 — RÉCAPITULATIF (live)
        ====================================================== --}}
        <section class="form-card form-summary-card">

            <div class="form-card-header">

                <div class="form-card-title-wrapper">

                    <div class="form-card-icon">

                        <i
                            class="fa-solid fa-clipboard-check"
                            aria-hidden="true"
                        ></i>

                    </div>

                    <div>

                        <h2 class="form-card-title">
                            Vérification
                        </h2>

                        <p class="form-card-description">
                            Vérifiez les informations avant de créer
                            l'article.
                        </p>

                    </div>

                </div>

            </div>


            <div class="form-card-body">

                <div class="creation-summary">

                    <div class="summary-item">

                        <span class="summary-label">
                            Référence
                        </span>

                        <strong
                            class="summary-value"
                            data-summary-reference
                        >
                            —
                        </strong>

                    </div>


                    <div class="summary-item">

                        <span class="summary-label">
                            Désignation
                        </span>

                        <strong
                            class="summary-value"
                            data-summary-designation
                        >
                            —
                        </strong>

                    </div>


                    <div class="summary-item">

                        <span class="summary-label">
                            Catégorie
                        </span>

                        <strong
                            class="summary-value"
                            data-summary-category
                        >
                            —
                        </strong>

                    </div>


                    <div class="summary-item">

                        <span class="summary-label">
                            Unité
                        </span>

                        <strong
                            class="summary-value"
                            data-summary-unit
                        >
                            —
                        </strong>

                    </div>


                    <div class="summary-item">

                        <span class="summary-label">
                            Seuil d'alerte
                        </span>

                        <strong
                            class="summary-value"
                            data-summary-threshold
                        >
                            —
                        </strong>

                    </div>


                    <div class="summary-item">

                        <span class="summary-label">
                            Emplacement
                        </span>

                        <strong
                            class="summary-value"
                            data-summary-location
                        >
                            —
                        </strong>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}
        <div class="form-actions">

            <a
                href="{{ route('admin.stocks.index') }}"
                class="btn btn-secondary"
            >
                <i
                    class="fa-solid fa-xmark"
                    aria-hidden="true"
                ></i>

                <span>Annuler</span>
            </a>


            <button
                type="submit"
                class="btn btn-primary"
                data-stock-submit
            >

                <i
                    class="fa-solid fa-plus"
                    aria-hidden="true"
                    data-submit-icon
                ></i>

                <span data-submit-text>
                    Créer l'article
                </span>

                <span
                    class="submit-loader"
                    data-submit-loader
                    hidden
                >
                    <i
                        class="fa-solid fa-spinner fa-spin"
                        aria-hidden="true"
                    ></i>
                </span>

            </button>

        </div>

    </form>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/create.js') }}"
        defer>
    </script>

@endsection