@extends('layouts.admin')

@section('title', 'Modifier l’article de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/edit.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-edit-page">

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="page-header stock-edit-header">

        <div class="page-header-content">

            <div class="page-header-main">

                <div class="page-header-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>

                <div>

                    <h1 class="page-title">
                        Modifier l’article
                    </h1>

                    <p class="page-subtitle">
                        Modifiez les informations administratives de cet article.
                    </p>

                </div>

            </div>


            <div class="page-header-actions">

                <a
                    href="{{ route('admin.stocks.show', $stock) }}"
                    class="stock-edit-btn stock-edit-btn--secondary"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    <span>
                        Retour à la fiche
                    </span>

                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ERREURS DE VALIDATION
    ========================================================== --}}
    @if($errors->any())

        <div
            class="stock-edit-validation-summary"
            role="alert"
            aria-labelledby="stock-edit-validation-title"
        >

            <div class="stock-edit-validation-summary__header">

                <div class="stock-edit-validation-summary__icon">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>

                <div>

                    <h2 id="stock-edit-validation-title">
                        Vérifiez les informations
                    </h2>

                    <p>
                        Certaines données nécessitent une correction.
                    </p>

                </div>

            </div>


            <ul class="stock-edit-validation-summary__list">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         CONTENU PRINCIPAL
    ========================================================== --}}
    <div class="stock-edit-layout">

        {{-- =====================================================
             FORMULAIRE
        ====================================================== --}}
        <section class="stock-edit-card">

            <div class="stock-edit-card__header">

                <div>

                    <h2>
                        Informations de l’article
                    </h2>

                    <p>
                        Les informations modifiables sont indiquées
                        dans ce formulaire.
                    </p>

                </div>

                <div class="stock-edit-card__badge">

                    <i class="fa-solid fa-box"></i>

                    <span>
                        Article
                    </span>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.stocks.update', $stock) }}"
                class="stock-edit-form"
                novalidate
            >

                @csrf

                @method('PUT')


                {{-- =================================================
                     RÉFÉRENCE
                ================================================== --}}
                <div class="stock-edit-field">

                    <label
                        for="reference"
                        class="stock-edit-label"
                    >

                        Référence

                        <span class="stock-edit-required">
                            *
                        </span>

                    </label>

                    <div class="stock-edit-input-wrapper">

                        <span class="stock-edit-input-icon">
                            <i class="fa-solid fa-barcode"></i>
                        </span>

                        <input
                            type="text"
                            id="reference"
                            name="reference"
                            class="stock-edit-input @error('reference') is-invalid @enderror"
                            value="{{ old('reference', $stock->reference) }}"
                            maxlength="50"
                            required
                            autocomplete="off"
                        >

                    </div>

                    @error('reference')

                        <div class="stock-edit-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>

                    @enderror

                    <small class="stock-edit-help">
                        Référence interne unique de l’article.
                    </small>

                </div>


                {{-- =================================================
                     DÉSIGNATION
                ================================================== --}}
                <div class="stock-edit-field">

                    <label
                        for="designation"
                        class="stock-edit-label"
                    >

                        Désignation

                        <span class="stock-edit-required">
                            *
                        </span>

                    </label>

                    <div class="stock-edit-input-wrapper">

                        <span class="stock-edit-input-icon">
                            <i class="fa-solid fa-tag"></i>
                        </span>

                        <input
                            type="text"
                            id="designation"
                            name="designation"
                            class="stock-edit-input @error('designation') is-invalid @enderror"
                            value="{{ old('designation', $stock->designation) }}"
                            maxlength="255"
                            required
                            autocomplete="off"
                        >

                    </div>

                    @error('designation')

                        <div class="stock-edit-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>

                    @enderror

                </div>


                {{-- =================================================
                     CATÉGORIE + UNITÉ
                ================================================== --}}
                <div class="stock-edit-grid stock-edit-grid--two">

                    {{-- CATÉGORIE --}}
                    <div class="stock-edit-field">

                        <label
                            for="categorie"
                            class="stock-edit-label"
                        >

                            Catégorie

                            <span class="stock-edit-required">
                                *
                            </span>

                        </label>

                        <div class="stock-edit-input-wrapper">

                            <span class="stock-edit-input-icon">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>

                            <select
                                id="categorie"
                                name="categorie"
                                class="stock-edit-input stock-edit-select @error('categorie') is-invalid @enderror"
                                required
                            >

                                <option value="">
                                    Sélectionner une catégorie
                                </option>

                                @php
                                    $categories = [
                                        'vivres'      => 'Vivres',
                                        'medicaments' => 'Médicaments',
                                        'habillement' => 'Habillement',
                                        'abris'       => 'Abris',
                                        'materiel'    => 'Matériel',
                                        'semences'    => 'Semences',
                                        'outils'      => 'Outils',
                                        'autre'       => 'Autre',
                                    ];
                                @endphp

                                @foreach($categories as $value => $label)

                                    <option
                                        value="{{ $value }}"
                                        @selected(old('categorie', $stock->categorie) === $value)
                                    >
                                        {{ $label }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        @error('categorie')

                            <div class="stock-edit-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </div>

                        @enderror

                    </div>


                    {{-- UNITÉ --}}
                    <div class="stock-edit-field">

                        <label
                            for="unite"
                            class="stock-edit-label"
                        >

                            Unité de mesure

                            <span class="stock-edit-required">
                                *
                            </span>

                        </label>

                        <div class="stock-edit-input-wrapper">

                            <span class="stock-edit-input-icon">
                                <i class="fa-solid fa-ruler-combined"></i>
                            </span>

                            <input
                                type="text"
                                id="unite"
                                name="unite"
                                class="stock-edit-input @error('unite') is-invalid @enderror"
                                value="{{ old('unite', $stock->unite) }}"
                                maxlength="20"
                                required
                                autocomplete="off"
                                placeholder="kg, litre, pièce, boîte..."
                            >

                        </div>

                        @error('unite')

                            <div class="stock-edit-error">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </div>

                        @enderror

                        <small class="stock-edit-help">
                            Exemple : kg, litre, pièce, sac, boîte.
                        </small>

                    </div>

                </div>


                {{-- =================================================
                     SEUIL D'ALERTE
                ================================================== --}}
                <div class="stock-edit-field">

                    <label
                        for="seuil_alerte"
                        class="stock-edit-label"
                    >

                        Seuil d’alerte

                    </label>

                    <div class="stock-edit-input-wrapper">

                        <span class="stock-edit-input-icon">
                            <i class="fa-solid fa-bell"></i>
                        </span>

                        <input
                            type="number"
                            id="seuil_alerte"
                            name="seuil_alerte"
                            class="stock-edit-input stock-edit-input--numeric @error('seuil_alerte') is-invalid @enderror"
                            value="{{ old('seuil_alerte', $stock->seuil_alerte) }}"
                            min="0"
                            step="0.01"
                            inputmode="decimal"
                            autocomplete="off"
                        >

                    </div>

                    @error('seuil_alerte')

                        <div class="stock-edit-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>

                    @enderror

                    <small class="stock-edit-help">
                        Une alerte pourra être déclenchée lorsque
                        le stock disponible atteindra ce seuil.
                        Laissez vide pour désactiver l’alerte.
                    </small>

                </div>


                {{-- =================================================
                     EMPLACEMENT PRINCIPAL
                ================================================== --}}
                <div class="stock-edit-field">

                    <label
                        for="emplacement_principal_id"
                        class="stock-edit-label"
                    >

                        Emplacement principal

                    </label>

                    <div class="stock-edit-input-wrapper">

                        <span class="stock-edit-input-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>

                        <select
                            id="emplacement_principal_id"
                            name="emplacement_principal_id"
                            class="stock-edit-input stock-edit-select @error('emplacement_principal_id') is-invalid @enderror"
                        >

                            <option value="">
                                Aucun emplacement principal
                            </option>

                            @foreach($emplacements as $emplacement)

                                <option
                                    value="{{ $emplacement->id }}"
                                    @selected(
                                        (string) old(
                                            'emplacement_principal_id',
                                            $stock->emplacement_principal_id
                                        ) === (string) $emplacement->id
                                    )
                                >
                                    {{ $emplacement->code }}
                                    —
                                    {{ $emplacement->nom }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    @error('emplacement_principal_id')

                        <div class="stock-edit-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>

                    @enderror

                    <small class="stock-edit-help">
                        Cet emplacement correspond au lieu principal
                        associé à l’article.
                    </small>

                </div>


                {{-- =================================================
                     DESCRIPTION
                ================================================== --}}
                <div class="stock-edit-field">

                    <label
                        for="description"
                        class="stock-edit-label"
                    >

                        Description

                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="stock-edit-textarea @error('description') is-invalid @enderror"
                        rows="6"
                        maxlength="5000"
                        data-maxlength="5000"
                        placeholder="Décrivez éventuellement l’article..."
                    >{{ old('description', $stock->description) }}</textarea>

                    @error('description')

                        <div class="stock-edit-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>

                    @enderror

                    <div class="stock-edit-textarea-footer">

                        <small class="stock-edit-help">
                            Informations complémentaires sur l’article.
                        </small>

                        <small class="stock-edit-character-counter">
                            {{ strlen(old('description', $stock->description ?? '')) }}
                            / 5000
                        </small>

                    </div>

                </div>


                {{-- =================================================
                     INFORMATIONS PROTÉGÉES (LECTURE SEULE)
                ================================================== --}}
                <div class="stock-edit-readonly-section">

                    <div class="stock-edit-readonly-section__header">

                        <div class="stock-edit-readonly-section__icon">

                            <i class="fa-solid fa-lock"></i>

                        </div>

                        <div>

                            <h3>
                                Informations protégées
                            </h3>

                            <p>
                                Ces informations ne peuvent pas être
                                modifiées depuis cette page.
                            </p>

                        </div>

                    </div>


                    <div class="stock-edit-readonly-grid">

                        {{-- STOCK PHYSIQUE --}}
                        <div class="stock-edit-readonly-item">

                            <span class="stock-edit-readonly-label">
                                Stock physique
                            </span>

                            <strong>
                                {{ number_format(
                                    (float) $stock->quantitePhysique(),
                                    2,
                                    ',',
                                    ' '
                                ) }}
                                {{ $stock->unite }}
                            </strong>

                        </div>


                        {{-- STOCK RÉSERVÉ --}}
                        <div class="stock-edit-readonly-item">

                            <span class="stock-edit-readonly-label">
                                Stock réservé
                            </span>

                            <strong>
                                {{ number_format(
                                    (float) $stock->quantiteReservee(),
                                    2,
                                    ',',
                                    ' '
                                ) }}
                                {{ $stock->unite }}
                            </strong>

                        </div>


                        {{-- STOCK DISPONIBLE --}}
                        <div class="stock-edit-readonly-item">

                            <span class="stock-edit-readonly-label">
                                Stock disponible
                            </span>

                            <strong>
                                {{ number_format(
                                    (float) $stock->quantiteDisponible(),
                                    2,
                                    ',',
                                    ' '
                                ) }}
                                {{ $stock->unite }}
                            </strong>

                        </div>


                        {{-- STATUT --}}
                        <div class="stock-edit-readonly-item">

                            <span class="stock-edit-readonly-label">
                                Statut
                            </span>

                            <strong class="stock-edit-status">

                                {{ $stock->statut->libelle() }}

                            </strong>

                        </div>

                    </div>

                    <div class="stock-edit-readonly-notice">

                        <i class="fa-solid fa-circle-info"></i>

                        <span>
                            Les quantités sont modifiées uniquement
                            par les opérations de stock : entrées,
                            sorties, transferts et ajustements.
                        </span>

                    </div>

                </div>


                {{-- =================================================
                     ACTIONS
                ================================================== --}}
                <div class="stock-edit-form-actions">

                    <a
                        href="{{ route('admin.stocks.show', $stock) }}"
                        class="stock-edit-btn stock-edit-btn--secondary"
                    >

                        <i class="fa-solid fa-xmark"></i>

                        <span>
                            Annuler
                        </span>

                    </a>


                    <button
                        type="submit"
                        class="stock-edit-btn stock-edit-btn--primary"
                    >

                        <i class="fa-solid fa-floppy-disk"></i>

                        <span class="stock-edit-btn__text">
                            Enregistrer les modifications
                        </span>

                    </button>

                </div>

            </form>

        </section>


        {{-- =====================================================
             PANNEAU LATÉRAL
        ====================================================== --}}
        <aside class="stock-edit-sidebar">

            {{-- RAPPEL SÉCURITÉ --}}
            <div class="stock-edit-side-card">

                <div class="stock-edit-side-card__header">

                    <div class="stock-edit-side-card__icon">

                        <i class="fa-solid fa-shield-halved"></i>

                    </div>

                    <h2>
                        Sécurité du stock
                    </h2>

                </div>


                <div class="stock-edit-side-card__body">

                    <p>
                        La modification de l’article ne modifie
                        jamais directement les quantités de stock.
                    </p>

                    <ul>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Les mouvements restent traçables.
                        </li>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Les lots restent indépendants.
                        </li>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Les réservations restent protégées.
                        </li>

                        <li>
                            <i class="fa-solid fa-check"></i>
                            Les sorties passent par leur workflow.
                        </li>

                    </ul>

                </div>

            </div>


            {{-- IDENTIFICATION --}}
            <div class="stock-edit-side-card">

                <div class="stock-edit-side-card__header">

                    <div class="stock-edit-side-card__icon">

                        <i class="fa-solid fa-circle-info"></i>

                    </div>

                    <h2>
                        Identification
                    </h2>

                </div>


                <div class="stock-edit-side-card__body">

                    <div class="stock-edit-meta">

                        <span>
                            ID
                        </span>

                        <strong>
                            #{{ $stock->id }}
                        </strong>

                    </div>


                    <div class="stock-edit-meta">

                        <span>
                            Créé le
                        </span>

                        <strong>
                            {{ optional($stock->created_at)->format('d/m/Y H:i') ?? '—' }}
                        </strong>

                    </div>


                    <div class="stock-edit-meta">

                        <span>
                            Dernière modification
                        </span>

                        <strong>
                            {{ optional($stock->updated_at)->format('d/m/Y H:i') ?? '—' }}
                        </strong>

                    </div>

                </div>

            </div>


            {{-- AVERTISSEMENT --}}
            <div class="stock-edit-side-warning">

                <div class="stock-edit-side-warning__icon">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>

                <div>

                    <strong>
                        Attention
                    </strong>

                    <p>
                        La modification de la référence doit rester
                        exceptionnelle lorsqu’elle est déjà utilisée
                        dans des documents ou historiques.
                    </p>

                </div>

            </div>

        </aside>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/edit.js') }}"
        defer>
    </script>

@endsection