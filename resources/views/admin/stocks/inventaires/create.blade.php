@extends('layouts.admin')

@section('title', 'Nouvel inventaire')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/inventaires-create.css') }}"
    >
@endsection

@section('content')

<div class="admin-content">

    <div class="stock-inventaire-create-page">

        {{-- =========================================================
             EN-TÊTE
        ========================================================== --}}
        <header class="stock-inventaire-create-header">

            <div class="stock-inventaire-create-header__main">

                <nav
                    class="stock-inventaire-create-breadcrumb"
                    aria-label="Fil d’Ariane"
                >

                    <a href="{{ route('admin.stocks.index') }}">
                        Stocks
                    </a>

                    <span aria-hidden="true">/</span>

                    <a href="{{ route('admin.stocks.inventaires.index') }}">
                        Inventaires
                    </a>

                    <span aria-hidden="true">/</span>

                    <span>Nouvel inventaire</span>

                </nav>


                <div class="stock-inventaire-create-title-wrapper">

                    <div class="stock-inventaire-create-title-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>

                    <div>

                        <h1 class="stock-inventaire-create-title">
                            Nouvel inventaire
                        </h1>

                        <p class="stock-inventaire-create-subtitle">
                            Créez une opération d’inventaire avant de procéder
                            au comptage physique des articles.
                        </p>

                    </div>

                </div>

            </div>


            <div class="stock-inventaire-create-header__actions">

                <a
                    href="{{ route('admin.stocks.inventaires.index') }}"
                    class="stock-inventaire-create-button stock-inventaire-create-button--secondary"
                >

                    <i class="fas fa-arrow-left"></i>

                    <span>Retour aux inventaires</span>

                </a>

            </div>

        </header>


        {{-- =========================================================
             MESSAGES / ERREURS
        ========================================================== --}}
        @if(session('error'))

            <div
                class="stock-inventaire-create-alert stock-inventaire-create-alert--danger"
                role="alert"
            >

                <div class="stock-inventaire-create-alert__icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>

                <div class="stock-inventaire-create-alert__content">

                    <strong>Impossible de créer l’inventaire</strong>

                    <p>{{ session('error') }}</p>

                </div>

            </div>

        @endif


        @if($errors->any())

            <div
                class="stock-inventaire-create-alert stock-inventaire-create-alert--danger"
                role="alert"
            >

                <div class="stock-inventaire-create-alert__icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div class="stock-inventaire-create-alert__content">

                    <strong>Vérifiez les informations saisies</strong>

                    <ul class="stock-inventaire-create-error-list">

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
            action="{{ route('admin.stocks.inventaires.store') }}"
            method="POST"
            id="inventaire-create-form"
            class="stock-inventaire-create-form"
            novalidate
        >

            @csrf


            {{-- =====================================================
                 SECTION 1 — INFORMATIONS GÉNÉRALES
            ====================================================== --}}
            <section class="stock-inventaire-create-card">

                <div class="stock-inventaire-create-card__header">

                    <div>

                        <h2 class="stock-inventaire-create-card__title">
                            Informations générales
                        </h2>

                        <p class="stock-inventaire-create-card__description">
                            Définissez le cadre de l’opération d’inventaire.
                        </p>

                    </div>

                    <div class="stock-inventaire-create-card__icon">
                        <i class="fas fa-info-circle"></i>
                    </div>

                </div>


                <div class="stock-inventaire-create-card__body">

                    <div class="stock-inventaire-create-form-grid">

                        {{-- DATE DE DÉBUT --}}
                        <div class="stock-inventaire-create-field">

                            <label
                                for="date_debut"
                                class="stock-inventaire-create-label"
                            >
                                Date de début
                                <span class="required" aria-hidden="true">*</span>
                            </label>

                            <div class="stock-inventaire-create-input-wrapper">

                                <i class="fas fa-calendar-alt"></i>

                                <input
                                    type="date"
                                    id="date_debut"
                                    name="date_debut"
                                    value="{{ old('date_debut', now()->format('Y-m-d')) }}"
                                    class="stock-inventaire-create-input stock-inventaire-create-input--icon @error('date_debut') is-invalid @enderror"
                                    required
                                >

                            </div>

                            @error('date_debut')

                                <span class="stock-inventaire-create-field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                            <span class="stock-inventaire-create-field-help">
                                Date officielle à laquelle le comptage physique débute.
                            </span>

                        </div>


                        {{-- EMPLACEMENT (périmètre) --}}
                        <div class="stock-inventaire-create-field">

                            <label
                                for="emplacement_id"
                                class="stock-inventaire-create-label"
                            >
                                Emplacement
                                <span class="optional">Facultatif</span>
                            </label>

                            <select
                                id="emplacement_id"
                                name="emplacement_id"
                                class="stock-inventaire-create-input @error('emplacement_id') is-invalid @enderror"
                            >

                                <option value="">
                                    Tous les emplacements
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

                                <span class="stock-inventaire-create-field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                            <span class="stock-inventaire-create-field-help">
                                Délimite le périmètre physique à contrôler.
                            </span>

                        </div>


                        {{-- RESPONSABLE --}}
                        <div class="stock-inventaire-create-field">

                            <label
                                for="responsable_id"
                                class="stock-inventaire-create-label"
                            >
                                Responsable
                                <span class="optional">Facultatif</span>
                            </label>

                            <select
                                id="responsable_id"
                                name="responsable_id"
                                class="stock-inventaire-create-input @error('responsable_id') is-invalid @enderror"
                            >

                                <option value="">
                                    Utilisateur connecté (par défaut)
                                </option>

                                @foreach($utilisateurs as $utilisateur)

                                    <option
                                        value="{{ $utilisateur->id }}"
                                        @selected(
                                            (string) old('responsable_id', auth()->id())
                                            === (string) $utilisateur->id
                                        )
                                    >
                                        {{ $utilisateur->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('responsable_id')

                                <span class="stock-inventaire-create-field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                            <span class="stock-inventaire-create-field-help">
                                Utilisateur en charge de l’opération.
                            </span>

                        </div>


                        {{-- NOTES --}}
                        <div class="stock-inventaire-create-field stock-inventaire-create-field--full">

                            <label
                                for="notes"
                                class="stock-inventaire-create-label"
                            >
                                Notes
                                <span class="optional">Facultatif</span>
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                rows="5"
                                maxlength="5000"
                                class="stock-inventaire-create-textarea @error('notes') is-invalid @enderror"
                                placeholder="Ajoutez une observation ou une information utile concernant cet inventaire..."
                            >{{ old('notes') }}</textarea>

                            @error('notes')

                                <span class="stock-inventaire-create-field-error">
                                    {{ $message }}
                                </span>

                            @enderror

                            <div class="stock-inventaire-create-character-counter">

                                <span>Informations internes</span>

                                <span>
                                    <strong data-character-count="notes">0</strong>
                                    / 5000
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =====================================================
                 SECTION 2 — PÉRIMÈTRE
            ====================================================== --}}
            <section class="stock-inventaire-create-card">

                <div class="stock-inventaire-create-card__header">

                    <div>

                        <h2 class="stock-inventaire-create-card__title">
                            Périmètre du comptage
                        </h2>

                        <p class="stock-inventaire-create-card__description">
                            Choisissez comment les articles seront sélectionnés
                            pour l’inventaire.
                        </p>

                    </div>

                    <div class="stock-inventaire-create-card__icon">
                        <i class="fas fa-boxes"></i>
                    </div>

                </div>


                <div class="stock-inventaire-create-card__body">

                    <div class="stock-inventaire-create-scope-options">

                        {{-- TOUS LES ARTICLES --}}
                        <label
                            class="stock-inventaire-create-scope-option"
                            for="scope_all"
                        >

                            <input
                                type="radio"
                                id="scope_all"
                                name="scope"
                                value="all"
                                @checked(old('scope', 'all') === 'all')
                            >

                            <span class="stock-inventaire-create-scope-option__indicator"></span>

                            <span class="stock-inventaire-create-scope-option__content">

                                <strong>Tous les articles</strong>

                                <small>
                                    Inclure tous les articles actifs du périmètre
                                    de stockage sélectionné.
                                </small>

                            </span>

                        </label>


                        {{-- ARTICLES AVEC STOCK --}}
                        <label
                            class="stock-inventaire-create-scope-option"
                            for="scope_stock"
                        >

                            <input
                                type="radio"
                                id="scope_stock"
                                name="scope"
                                value="with_stock"
                                @checked(old('scope') === 'with_stock')
                            >

                            <span class="stock-inventaire-create-scope-option__indicator"></span>

                            <span class="stock-inventaire-create-scope-option__content">

                                <strong>Articles avec stock</strong>

                                <small>
                                    Inclure uniquement les articles possédant
                                    actuellement une quantité en stock.
                                </small>

                            </span>

                        </label>


                        {{-- SÉLECTION MANUELLE --}}
                        <label
                            class="stock-inventaire-create-scope-option"
                            for="scope_manual"
                        >

                            <input
                                type="radio"
                                id="scope_manual"
                                name="scope"
                                value="manual"
                                @checked(old('scope') === 'manual')
                            >

                            <span class="stock-inventaire-create-scope-option__indicator"></span>

                            <span class="stock-inventaire-create-scope-option__content">

                                <strong>Sélection manuelle</strong>

                                <small>
                                    Les articles seront sélectionnés individuellement.
                                </small>

                            </span>

                        </label>

                    </div>

                    @error('scope')

                        <span class="stock-inventaire-create-field-error">
                            {{ $message }}
                        </span>

                    @enderror

                </div>

            </section>


            {{-- =====================================================
                 SECTION 3 — SÉLECTION MANUELLE DES ARTICLES
            ====================================================== --}}
            <section
                class="stock-inventaire-create-card"
                id="manual-selection-section"
                hidden
            >

                <div class="stock-inventaire-create-card__header">

                    <div>

                        <h2 class="stock-inventaire-create-card__title">
                            Sélection manuelle des articles
                        </h2>

                        <p class="stock-inventaire-create-card__description">
                            Cochez les articles à inclure dans l’inventaire.
                        </p>

                    </div>

                    <div class="stock-inventaire-create-card__icon">
                        <i class="fas fa-list-check"></i>
                    </div>

                </div>


                <div class="stock-inventaire-create-card__body">

                    <div class="stock-inventaire-create-stock-search">

                        <i class="fas fa-search" aria-hidden="true"></i>

                        <input
                            type="search"
                            class="stock-inventaire-create-stock-search__input"
                            placeholder="Rechercher un article..."
                            data-stock-search
                        >

                    </div>

                    <div class="stock-inventaire-create-stock-list">

                        @foreach($stocks as $stock)

                            <label
                                class="stock-inventaire-create-stock-item"
                                data-stock-label
                            >

                                <input
                                    type="checkbox"
                                    name="stock_ids[]"
                                    value="{{ $stock->id }}"
                                    @checked(
                                        in_array(
                                            $stock->id,
                                            (array) old('stock_ids', [])
                                        )
                                    )
                                >

                                <span class="stock-inventaire-create-stock-item__content">

                                    <strong>
                                        {{ $stock->reference }}
                                    </strong>

                                    —

                                    <span>
                                        {{ $stock->designation }}
                                    </span>

                                    <small>
                                        Unité : {{ $stock->unite }}
                                    </small>

                                </span>

                            </label>

                        @endforeach

                    </div>

                    <p class="stock-inventaire-create-field-help">
                        Ces articles seront les seuls concernés par l’inventaire.
                    </p>

                </div>

            </section>


            {{-- =====================================================
                 SECTION 4 — RESPONSABLE (information)
            ====================================================== --}}
            <section class="stock-inventaire-create-card">

                <div class="stock-inventaire-create-card__header">

                    <div>

                        <h2 class="stock-inventaire-create-card__title">
                            Responsable du contrôle
                        </h2>

                        <p class="stock-inventaire-create-card__description">
                            L’utilisateur connecté est enregistré par défaut
                            comme responsable si aucun n’est sélectionné.
                        </p>

                    </div>

                    <div class="stock-inventaire-create-card__icon">
                        <i class="fas fa-user-check"></i>
                    </div>

                </div>


                <div class="stock-inventaire-create-card__body">

                    <div class="stock-inventaire-create-current-user">

                        <div class="stock-inventaire-create-current-user__avatar">

                            <i class="fas fa-user"></i>

                        </div>


                        <div class="stock-inventaire-create-current-user__content">

                            <strong>
                                {{ auth()->user()->name }}
                            </strong>

                            @if(auth()->user()->email)

                                <span>
                                    {{ auth()->user()->email }}
                                </span>

                            @endif

                            <small>
                                Utilisateur connecté
                            </small>

                        </div>

                        <div class="stock-inventaire-create-current-user__status">

                            <i class="fas fa-lock"></i>

                            Automatique

                        </div>

                    </div>

                </div>

            </section>


            {{-- =====================================================
                 PROCESSUS
            ====================================================== --}}
            <section class="stock-inventaire-create-workflow">

                <div class="stock-inventaire-create-workflow__header">

                    <div class="stock-inventaire-create-workflow__icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>

                    <div>

                        <h2>Déroulement de l’inventaire</h2>

                        <p>
                            La création de l’inventaire ne modifie aucune
                            quantité en stock.
                        </p>

                    </div>

                </div>


                <div class="stock-inventaire-create-workflow__steps">

                    <div class="stock-inventaire-create-workflow__step is-active">

                        <span class="stock-inventaire-create-workflow__number">1</span>

                        <div>
                            <strong>Création</strong>
                            <small>Préparer l’opération</small>
                        </div>

                    </div>


                    <div class="stock-inventaire-create-workflow__line"></div>


                    <div class="stock-inventaire-create-workflow__step">

                        <span class="stock-inventaire-create-workflow__number">2</span>

                        <div>
                            <strong>Comptage physique</strong>
                            <small>Relever les quantités réelles</small>
                        </div>

                    </div>


                    <div class="stock-inventaire-create-workflow__line"></div>


                    <div class="stock-inventaire-create-workflow__step">

                        <span class="stock-inventaire-create-workflow__number">3</span>

                        <div>
                            <strong>Comparaison</strong>
                            <small>Calculer les écarts</small>
                        </div>

                    </div>


                    <div class="stock-inventaire-create-workflow__line"></div>


                    <div class="stock-inventaire-create-workflow__step">

                        <span class="stock-inventaire-create-workflow__number">4</span>

                        <div>
                            <strong>Validation</strong>
                            <small>Autoriser les ajustements</small>
                        </div>

                    </div>

                </div>

            </section>


            {{-- =====================================================
                 AVERTISSEMENT MÉTIER
            ====================================================== --}}
            <div class="stock-inventaire-create-notice">

                <div class="stock-inventaire-create-notice__icon">
                    <i class="fas fa-shield-alt"></i>
                </div>

                <div class="stock-inventaire-create-notice__content">

                    <strong>Protection des quantités</strong>

                    <p>
                        La création de cet inventaire ne modifie pas les
                        quantités disponibles. Les écarts constatés seront
                        enregistrés séparément puis transformés en mouvements
                        d’ajustement uniquement après validation.
                    </p>

                </div>

            </div>


            {{-- =====================================================
                 ACTIONS
            ====================================================== --}}
            <footer class="stock-inventaire-create-form-actions">

                <a
                    href="{{ route('admin.stocks.inventaires.index') }}"
                    class="stock-inventaire-create-button stock-inventaire-create-button--secondary"
                >

                    <i class="fas fa-times"></i>

                    <span>Annuler</span>

                </a>


                <button
                    type="submit"
                    id="inventaire-create-submit"
                    class="stock-inventaire-create-button stock-inventaire-create-button--primary"
                >

                    <i class="fas fa-clipboard-check"></i>

                    <span>Créer l’inventaire</span>

                </button>

            </footer>

        </form>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/inventaires-create.js') }}"
        defer>
    </script>

@endsection