@extends('layouts.admin')

@section('title', 'Nouveau bon de sortie')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/sorties-create.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-sortie-create">

    {{-- =========================================================
         EN-TÊTE DE PAGE
    ========================================================== --}}
    <div class="page-header">

        <div class="page-header-content">

            <div class="page-header-icon">
                <i class="fas fa-file-export"></i>
            </div>

            <div>

                <h1 class="page-title">
                    Nouveau bon de sortie
                </h1>

                <p class="page-subtitle">
                    Créer une demande de sortie de stock
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


    {{-- =========================================================
         MESSAGES DE SESSION
    ========================================================== --}}
    @if(session('success'))

        <div class="alert alert-success">

            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
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
                <i class="fas fa-times"></i>
            </button>

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

            <button
                type="button"
                class="alert-close"
                data-dismiss-alert
                aria-label="Fermer"
            >
                <i class="fas fa-times"></i>
            </button>

        </div>

    @endif


    {{-- =========================================================
         ERREURS DE VALIDATION
    ========================================================== --}}
    @if($errors->any())

        <div class="validation-summary">

            <div class="validation-summary-header">

                <div class="validation-summary-title">

                    <i class="fas fa-exclamation-triangle"></i>

                    <span>
                        Veuillez corriger les erreurs suivantes
                    </span>

                </div>

            </div>

            <ul class="validation-summary-list">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         FORMULAIRE
    ========================================================== --}}
    <form
        action="{{ route('admin.stocks.sorties.store') }}"
        method="POST"
        id="bon-sortie-form"
        class="stock-form"
        novalidate
    >

        @csrf


        {{-- =====================================================
             SECTION 1 — INFORMATIONS GÉNÉRALES
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-title">

                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>

                    <div>

                        <h2>
                            Informations générales
                        </h2>

                        <p>
                            Renseignez les informations principales
                            du bon de sortie.
                        </p>

                    </div>

                </div>

                <span class="required-hint">
                    * Champs obligatoires
                </span>

            </div>


            <div class="form-card-body">

                <div class="form-grid form-grid-2">

                    {{-- DATE DE DEMANDE --}}
                    <div class="form-group">

                        <label for="date_demande">
                            Date de demande
                            <span class="required">*</span>
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-calendar-alt input-icon"></i>

                            <input
                                type="date"
                                id="date_demande"
                                name="date_demande"
                                class="form-control @error('date_demande') is-invalid @enderror"
                                value="{{ old('date_demande', now()->format('Y-m-d')) }}"
                                required
                            >

                        </div>

                        @error('date_demande')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- DEMANDEUR --}}
                    <div class="form-group">

                        <label for="demandeur_id">
                            Demandeur
                            <span class="required">*</span>
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-user input-icon"></i>

                            <select
                                id="demandeur_id"
                                name="demandeur_id"
                                class="form-control @error('demandeur_id') is-invalid @enderror"
                                required
                            >

                                <option value="">
                                    Sélectionner le demandeur
                                </option>

                                @foreach($utilisateurs as $utilisateur)

                                    <option
                                        value="{{ $utilisateur->id }}"
                                        @selected(
                                            (string) old('demandeur_id', auth()->id())
                                            === (string) $utilisateur->id
                                        )
                                    >
                                        {{ $utilisateur->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        @error('demandeur_id')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- PROJET --}}
                    <div class="form-group">

                        <label for="projet_id">
                            Projet
                            <span class="optional-label">Facultatif</span>
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-project-diagram input-icon"></i>

                            <select
                                id="projet_id"
                                name="projet_id"
                                class="form-control @error('projet_id') is-invalid @enderror"
                            >

                                <option value="">
                                    Aucun projet
                                </option>

                                @foreach($projets as $projet)

                                    <option
                                        value="{{ $projet->id }}"
                                        @selected(old('projet_id') == $projet->id)
                                    >
                                        {{ $projet->nom
                                            ?? $projet->titre
                                            ?? ('Projet #' . $projet->id) }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        @error('projet_id')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- RÉFÉRENCE --}}
                    <div class="form-group">

                        <label for="reference">
                            Référence documentaire
                            <span class="optional-label">Facultatif</span>
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-hashtag input-icon"></i>

                            <input
                                type="text"
                                id="reference"
                                name="reference"
                                class="form-control @error('reference') is-invalid @enderror"
                                value="{{ old('reference') }}"
                                maxlength="100"
                                placeholder="Référence externe (facultative)"
                            >

                        </div>

                        @error('reference')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- DESTINATAIRE --}}
                    <div class="form-group">

                        <label for="destinataire">
                            Destinataire
                            <span class="required">*</span>
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-user-tag input-icon"></i>

                            <input
                                type="text"
                                id="destinataire"
                                name="destinataire"
                                class="form-control @error('destinataire') is-invalid @enderror"
                                value="{{ old('destinataire') }}"
                                maxlength="255"
                                placeholder="Nom du bénéficiaire ou service destinataire"
                                required
                            >

                        </div>

                        @error('destinataire')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>


                {{-- MOTIF --}}
                <div class="form-group">

                    <label for="motif">
                        Motif de la sortie
                        <span class="required">*</span>
                    </label>

                    <textarea
                        id="motif"
                        name="motif"
                        class="form-control textarea-control @error('motif') is-invalid @enderror"
                        rows="4"
                        maxlength="500"
                        placeholder="Expliquez la raison de cette demande de sortie..."
                        required
                    >{{ old('motif') }}</textarea>

                    <div class="field-meta">

                        <span>
                            Précisez clairement l’utilisation prévue
                            des articles.
                        </span>

                        <span id="motif-counter">
                            0 / 500
                        </span>

                    </div>

                    @error('motif')

                        <div class="field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 2 — ARTICLES À SORTIR
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-title">

                    <div class="section-icon">
                        <i class="fas fa-boxes"></i>
                    </div>

                    <div>

                        <h2>
                            Articles à sortir
                        </h2>

                        <p>
                            Ajoutez les articles et les quantités
                            demandées.
                        </p>

                    </div>

                </div>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="add-line-btn"
                >
                    <i class="fas fa-plus"></i>
                    Ajouter un article
                </button>

            </div>


            <div class="form-card-body">

                <div
                    id="stock-lines"
                    class="stock-lines"
                    data-next-index="{{ count(old('lignes', [])) }}"
                >

                    @php
                        $oldLines = old('lignes', []);

                        if (empty($oldLines)) {
                            $oldLines = [
                                [
                                    'stock_id'          => '',
                                    'quantite_demandee' => '',
                                    'notes'             => '',
                                ],
                            ];
                        }
                    @endphp


                    @foreach($oldLines as $index => $ligne)

                        <div
                            class="stock-line"
                            data-line-index="{{ $index }}"
                        >

                            <div class="stock-line-header">

                                <div class="stock-line-number">
                                    Article
                                    <span class="line-number">
                                        {{ $index + 1 }}
                                    </span>
                                </div>

                                <button
                                    type="button"
                                    class="line-remove-btn"
                                    data-remove-line
                                    title="Supprimer cet article"
                                    @disabled(count($oldLines) <= 1)
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                            </div>


                            <div class="stock-line-body">

                                {{-- ARTICLE --}}
                                <div class="form-group line-stock-field">

                                    <label for="lignes_{{ $index }}_stock_id">
                                        Article
                                        <span class="required">*</span>
                                    </label>

                                    <select
                                        id="lignes_{{ $index }}_stock_id"
                                        name="lignes[{{ $index }}][stock_id]"
                                        class="form-control stock-select @error('lignes.'.$index.'.stock_id') is-invalid @enderror"
                                        data-stock-select
                                        required
                                    >

                                        <option value="">
                                            Sélectionner un article
                                        </option>

                                        @foreach($stocks as $stock)

                                            @php
                                                /*
                                                 * Quantité disponible :
                                                 * méthode du modèle, pas un attribut.
                                                 */
                                                $stockDisponible = (float) $stock->quantiteDisponible();
                                            @endphp

                                            <option
                                                value="{{ $stock->id }}"
                                                data-unite="{{ $stock->unite }}"
                                                data-disponible="{{ $stockDisponible }}"
                                                @selected(($ligne['stock_id'] ?? '') == $stock->id)
                                            >
                                                {{ $stock->reference }}
                                                —
                                                {{ $stock->designation }}
                                            </option>

                                        @endforeach

                                    </select>

                                    @error('lignes.'.$index.'.stock_id')

                                        <div class="field-error">
                                            {{ $message }}
                                        </div>

                                    @enderror

                                </div>


                                {{-- QUANTITÉ --}}
                                <div class="form-group line-quantity-field">

                                    <label for="lignes_{{ $index }}_quantite_demandee">
                                        Quantité demandée
                                        <span class="required">*</span>
                                    </label>

                                    <div class="quantity-input-wrapper">

                                        <input
                                            type="number"
                                            id="lignes_{{ $index }}_quantite_demandee"
                                            name="lignes[{{ $index }}][quantite_demandee]"
                                            class="form-control quantity-input @error('lignes.'.$index.'.quantite_demandee') is-invalid @enderror"
                                            value="{{ $ligne['quantite_demandee'] ?? '' }}"
                                            min="0.01"
                                            step="0.01"
                                            data-quantity-input
                                            required
                                        >

                                        <span
                                            class="quantity-unit"
                                            data-unit-display
                                        >
                                            unité
                                        </span>

                                    </div>

                                    <div
                                        class="available-stock"
                                        data-available-display
                                    >
                                        Disponibilité : —
                                    </div>

                                    @error('lignes.'.$index.'.quantite_demandee')

                                        <div class="field-error">
                                            {{ $message }}
                                        </div>

                                    @enderror

                                </div>


                                {{-- NOTES --}}
                                <div class="form-group line-notes-field">

                                    <label for="lignes_{{ $index }}_notes">
                                        Note
                                        <span class="optional-label">
                                            Facultatif
                                        </span>
                                    </label>

                                    <input
                                        type="text"
                                        id="lignes_{{ $index }}_notes"
                                        name="lignes[{{ $index }}][notes]"
                                        class="form-control"
                                        value="{{ $ligne['notes'] ?? '' }}"
                                        maxlength="500"
                                        placeholder="Précision éventuelle"
                                    >

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- MESSAGE AUCUNE LIGNE --}}
                <div
                    id="no-stock-lines"
                    class="empty-lines-message"
                    hidden
                >

                    <div class="empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>

                    <h3>
                        Aucun article ajouté
                    </h3>

                    <p>
                        Ajoutez au moins un article pour créer
                        le bon de sortie.
                    </p>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="empty-add-line-btn"
                    >
                        <i class="fas fa-plus"></i>
                        Ajouter un article
                    </button>

                </div>


                {{-- RÉSUMÉ --}}
                <div class="stock-lines-summary">

                    <div class="summary-item">

                        <span class="summary-label">
                            Articles
                        </span>

                        <strong
                            id="total-lines"
                            class="summary-value"
                        >
                            {{ count($oldLines) }}
                        </strong>

                    </div>

                    <div class="summary-item">

                        <span class="summary-label">
                            Quantités demandées
                        </span>

                        <strong
                            id="total-quantity"
                            class="summary-value"
                        >
                            0
                        </strong>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 3 — NOTES
        ====================================================== --}}
        <section class="form-card">

            <div class="form-card-header">

                <div class="form-card-title">

                    <div class="section-icon">
                        <i class="fas fa-sticky-note"></i>
                    </div>

                    <div>

                        <h2>
                            Notes complémentaires
                        </h2>

                        <p>
                            Informations supplémentaires concernant
                            la demande.
                        </p>

                    </div>

                </div>

            </div>


            <div class="form-card-body">

                <div class="form-group">

                    <label for="notes">
                        Notes
                        <span class="optional-label">
                            Facultatif
                        </span>
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        class="form-control textarea-control @error('notes') is-invalid @enderror"
                        rows="5"
                        maxlength="5000"
                        placeholder="Ajoutez toute information utile concernant cette demande..."
                    >{{ old('notes') }}</textarea>

                    <div class="field-meta">

                        <span>
                            Ces informations seront conservées
                            avec le bon de sortie.
                        </span>

                        <span id="notes-counter">
                            0 / 5000
                        </span>

                    </div>

                    @error('notes')

                        <div class="field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </section>


        {{-- =====================================================
             RAPPEL WORKFLOW
        ====================================================== --}}
        <div class="workflow-notice">

            <div class="workflow-notice-icon">
                <i class="fas fa-info-circle"></i>
            </div>

            <div class="workflow-notice-content">

                <strong>
                    Fonctionnement du bon de sortie
                </strong>

                <p>
                    La création de ce formulaire ne provoque
                    aucune sortie physique du stock.
                    Le bon sera créé en brouillon et devra suivre
                    le workflow de validation avant qu’une sortie
                    réelle soit effectuée.
                </p>

            </div>

        </div>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}
        <div class="form-actions">

            <a
                href="{{ route('admin.stocks.sorties.index') }}"
                class="btn btn-secondary"
            >

                <i class="fas fa-times"></i>

                Annuler

            </a>

            <button
                type="submit"
                class="btn btn-primary"
                id="submit-bon-sortie"
            >

                <i class="fas fa-save"></i>

                Créer le bon de sortie

            </button>

        </div>

    </form>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/sorties-create.js') }}"
        defer>
    </script>

@endsection