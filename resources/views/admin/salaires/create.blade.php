@extends('layouts.admin')

@section('title', 'Nouveau salaire')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/salaires/create.css') }}">
@endsection

@section('content')

<div class="salary-create">

    {{-- =========================================================
         EN-TÊTE
         ========================================================= --}}
    <div class="page-header">
        <div class="page-header-content">

            <h1 class="page-title">
                <i class="fas fa-money-check-alt"></i>
                Nouveau salaire
            </h1>

            <p class="page-subtitle">
                Créez une nouvelle obligation salariale pour un membre du personnel.
            </p>

        </div>

        <div class="page-actions">

            <a href="{{ route('admin.salaires.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Retour aux salaires
            </a>

        </div>
    </div>


    {{-- =========================================================
         MESSAGE DE SESSION
         ========================================================= --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>

            <span>
                {{ session('success') }}
            </span>
        </div>
    @endif


    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>

            <span>
                {{ session('error') }}
            </span>
        </div>
    @endif


    {{-- =========================================================
         ERREURS DE VALIDATION
         ========================================================= --}}
    @if($errors->any())

        <div class="validation-errors">

            <div class="validation-errors-title">
                <i class="fas fa-exclamation-triangle"></i>

                <strong>
                    Veuillez corriger les erreurs suivantes.
                </strong>
            </div>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- =========================================================
         FORMULAIRE
         ========================================================= --}}
    <form
        id="salaryCreateForm"
        method="POST"
        action="{{ route('admin.salaires.store') }}"
        novalidate
    >

        @csrf


        <div class="form-layout">


            {{-- =================================================
                 COLONNE PRINCIPALE
                 ================================================= --}}
            <div class="form-main">


                {{-- =================================================
                     SECTION 1 : INFORMATIONS GÉNÉRALES
                     ================================================= --}}
                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-heading">

                            <div class="form-card-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>

                            <div>

                                <h2 class="form-card-title">
                                    Informations générales
                                </h2>

                                <p class="form-card-description">
                                    Identifiez la période et le bénéficiaire du salaire.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="form-card-body">

                        <div class="form-grid">


                            {{-- Membre --}}
                            <div class="form-group form-group-full">

                                <label
                                    for="membre_id"
                                    class="form-label"
                                >
                                    <i class="fas fa-user"></i>
                                    Membre du personnel

                                    <span class="required">*</span>
                                </label>


                                <select
                                    name="membre_id"
                                    id="membre_id"
                                    class="form-select @error('membre_id') is-invalid @enderror"
                                    required
                                >

                                    <option value="">
                                        Sélectionner un membre
                                    </option>

                                    @foreach($membres as $membre)

                                        <option
                                            value="{{ $membre->id }}"
                                            data-nom="{{ $membre->nom }}"
                                            data-post-nom="{{ $membre->post_nom }}"
                                            data-prenom="{{ $membre->prenom ?? '' }}"
                                            data-telephone="{{ $membre->telephone ?? '' }}"
                                            data-photo="{{ $membre->photo ?? '' }}"
                                            @selected(old('membre_id') == $membre->id)
                                        >

                                            {{ $membre->nom }}
                                            {{ $membre->post_nom }}
                                            {{ $membre->prenom ?? '' }}

                                        </option>

                                    @endforeach

                                </select>


                                @error('membre_id')
                                    <span class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror


                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i>
                                    Seuls les membres actifs sont proposés.
                                </span>

                            </div>


                            {{-- Organe --}}
                            <div class="form-group">

                                <label
                                    for="organe_id"
                                    class="form-label"
                                >
                                    <i class="fas fa-sitemap"></i>
                                    Organe

                                    <span class="required">*</span>
                                </label>


                                <select
                                    name="organe_id"
                                    id="organe_id"
                                    class="form-select @error('organe_id') is-invalid @enderror"
                                    required
                                >

                                    <option value="">
                                        Sélectionner un organe
                                    </option>

                                    @foreach($organes as $organe)

                                        <option
                                            value="{{ $organe->id }}"
                                            @selected(old('organe_id') == $organe->id)
                                        >
                                            {{ $organe->libelle
                                                ?? $organe->designation
                                                ?? $organe->nom_organe
                                                ?? 'Organe #' . $organe->id
                                            }}
                                        </option>

                                    @endforeach

                                </select>


                                @error('organe_id')
                                    <span class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>


                            {{-- Mois --}}
                            <div class="form-group">

                                <label
                                    for="mois"
                                    class="form-label"
                                >
                                    <i class="fas fa-calendar-alt"></i>
                                    Mois concerné

                                    <span class="required">*</span>
                                </label>


                                <div class="input-wrapper">

                                    <span class="input-icon">
                                        <i class="fas fa-calendar"></i>
                                    </span>

                                    <input
                                        type="month"
                                        name="mois"
                                        id="mois"
                                        class="form-control @error('mois') is-invalid @enderror"
                                        value="{{ old('mois', $moisCourant) }}"
                                        required
                                    >

                                </div>


                                @error('mois')
                                    <span class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror


                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i>
                                    Le salaire sera rattaché à cette période.
                                </span>

                            </div>


                            {{-- Référence --}}
                            <div class="form-group form-group-full">

                                <label
                                    for="reference"
                                    class="form-label"
                                >
                                    <i class="fas fa-hashtag"></i>
                                    Référence

                                    <span class="required">*</span>
                                </label>


                                <div class="input-wrapper">

                                    <span class="input-icon">
                                        <i class="fas fa-barcode"></i>
                                    </span>

                                    <input
                                        type="text"
                                        name="reference"
                                        id="reference"
                                        class="form-control @error('reference') is-invalid @enderror"
                                        value="{{ old('reference') }}"
                                        maxlength="50"
                                        readonly
                                        required
                                    >

                                </div>


                                @error('reference')
                                    <span class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror


                                <span class="form-help">
                                    <i class="fas fa-magic"></i>
                                    La référence est générée automatiquement.
                                </span>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- =================================================
                     SECTION 2 : COMPOSITION DU SALAIRE
                     ================================================= --}}
                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-heading">

                            <div class="form-card-icon">
                                <i class="fas fa-coins"></i>
                            </div>

                            <div>

                                <h2 class="form-card-title">
                                    Composition du salaire
                                </h2>

                                <p class="form-card-description">
                                    Définissez le salaire de base, les primes et les retenues.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="form-card-body">


                        <div class="salary-components">


                            {{-- Salaire de base --}}
                            <div class="salary-component salary-component--base">

                                <div class="salary-component-label">

                                    <i class="fas fa-money-bill-wave"></i>

                                    Salaire de base

                                    <span class="required">*</span>

                                </div>


                                <div class="money-input">

                                    <input
                                        type="number"
                                        name="salaire_base"
                                        id="salaire_base"
                                        class="form-control @error('salaire_base') is-invalid @enderror"
                                        value="{{ old('salaire_base', 0) }}"
                                        min="0"
                                        step="0.01"
                                        required
                                    >

                                    <span class="currency">
                                        USD
                                    </span>

                                </div>


                                @error('salaire_base')
                                    <span class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror


                                <p class="salary-component-description">
                                    Montant contractuel de base.
                                </p>

                            </div>


                            {{-- Prime --}}
                            <div class="salary-component salary-component--prime">

                                <div class="salary-component-label">

                                    <i class="fas fa-plus-circle"></i>

                                    Primes

                                </div>


                                <div class="money-input">

                                    <input
                                        type="number"
                                        name="primes"
                                        id="primes"
                                        class="form-control @error('primes') is-invalid @enderror"
                                        value="{{ old('primes', 0) }}"
                                        min="0"
                                        step="0.01"
                                    >

                                    <span class="currency">
                                        USD
                                    </span>

                                </div>


                                @error('primes')
                                    <span class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror


                                <p class="salary-component-description">
                                    Primes et avantages ajoutés au salaire.
                                </p>

                            </div>


                            {{-- Retenues --}}
                            <div class="salary-component salary-component--retenue">

                                <div class="salary-component-label">

                                    <i class="fas fa-minus-circle"></i>

                                    Retenues

                                </div>


                                <div class="money-input">

                                    <input
                                        type="number"
                                        name="retenues"
                                        id="retenues"
                                        class="form-control @error('retenues') is-invalid @enderror"
                                        value="{{ old('retenues', 0) }}"
                                        min="0"
                                        step="0.01"
                                    >

                                    <span class="currency">
                                        USD
                                    </span>

                                </div>


                                @error('retenues')
                                    <span class="field-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror


                                <p class="salary-component-description">
                                    Retenues appliquées au salaire.
                                </p>

                            </div>

                        </div>


                        {{-- Aperçu du calcul --}}
                        <div class="calculation-box">

                            <div class="calculation-row">

                                <span class="calculation-label">
                                    Salaire de base
                                </span>

                                <span
                                    class="calculation-value"
                                    id="previewBase"
                                >
                                    0.00 USD
                                </span>

                            </div>


                            <div class="calculation-row">

                                <span class="calculation-label">
                                    Primes
                                </span>

                                <span
                                    class="calculation-value"
                                    id="previewPrimes"
                                >
                                    0.00 USD
                                </span>

                            </div>


                            <div class="calculation-row">

                                <span class="calculation-label">
                                    Retenues
                                </span>

                                <span
                                    class="calculation-value"
                                    id="previewRetenues"
                                >
                                    0.00 USD
                                </span>

                            </div>


                            <div class="calculation-row calculation-row--net">

                                <span class="calculation-label">
                                    Salaire net
                                </span>

                                <span
                                    class="calculation-value"
                                    id="previewNet"
                                >
                                    0.00 USD
                                </span>

                            </div>

                        </div>


                        {{-- Aperçu principal --}}
                        <div class="net-preview">

                            <div class="net-preview-info">

                                <span class="net-preview-label">
                                    Montant net à payer
                                </span>

                                <h3 class="net-preview-title">
                                    Après déduction des retenues
                                </h3>

                            </div>


                            <div
                                class="net-preview-value"
                                id="netPreview"
                            >
                                0.00
                                <small>USD</small>
                            </div>

                        </div>

                    </div>

                </div>



                {{-- =================================================
                     SECTION 3 : NOTES
                     ================================================= --}}
                <div class="form-card">

                    <div class="form-card-header">

                        <div class="form-card-heading">

                            <div class="form-card-icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>

                            <div>

                                <h2 class="form-card-title">
                                    Informations complémentaires
                                </h2>

                                <p class="form-card-description">
                                    Ajoutez une remarque ou une information utile.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="form-card-body">

                        <div class="form-group">

                            <label
                                for="notes"
                                class="form-label"
                            >
                                <i class="fas fa-comment-alt"></i>
                                Notes
                            </label>


                            <textarea
                                name="notes"
                                id="notes"
                                class="form-control @error('notes') is-invalid @enderror"
                                maxlength="1000"
                                placeholder="Ajoutez éventuellement une remarque concernant ce salaire..."
                            >{{ old('notes') }}</textarea>


                            @error('notes')
                                <span class="field-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror


                            <div class="character-counter">

                                <span id="notesCounter">
                                    0 / 1000
                                </span>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- =================================================
                     ACTIONS
                     ================================================= --}}
                <div class="form-actions">

                    <div class="form-actions-left">

                        <a
                            href="{{ route('admin.salaires.index') }}"
                            class="btn btn-outline"
                        >
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>

                    </div>


                    <div class="form-actions-right">

                        <button
                            type="reset"
                            class="btn btn-secondary"
                            id="resetSalaryForm"
                        >
                            <i class="fas fa-undo"></i>
                            Réinitialiser
                        </button>


                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="submitSalaryForm"
                        >

                            <i class="fas fa-save"></i>

                            <span class="btn-text">
                                Enregistrer le salaire
                            </span>

                        </button>

                    </div>

                </div>

            </div>



            {{-- =================================================
                 COLONNE LATÉRALE
                 ================================================= --}}
            <aside class="form-sidebar">


                {{-- =================================================
                     RÉSUMÉ DU MEMBRE
                     ================================================= --}}
                <div class="summary-card">

                    <div class="summary-header">

                        <i class="fas fa-user"></i>

                        <h2 class="summary-title">
                            Membre sélectionné
                        </h2>

                    </div>


                    <div class="summary-body">

                        <div
                            class="member-summary"
                            id="memberSummary"
                        >

                            <div
                                class="member-summary-avatar"
                                id="memberAvatar"
                            >
                                <i class="fas fa-user"></i>
                            </div>


                            <div class="member-summary-info">

                                <span
                                    class="member-summary-name"
                                    id="memberName"
                                >
                                    Aucun membre sélectionné
                                </span>

                                <span
                                    class="member-summary-meta"
                                    id="memberMeta"
                                >
                                    Sélectionnez un membre pour afficher son résumé.
                                </span>

                            </div>

                        </div>


                        <ul class="summary-list">

                            <li class="summary-list-item">

                                <span class="summary-list-label">
                                    Mois
                                </span>

                                <span
                                    class="summary-list-value primary"
                                    id="summaryMonth"
                                >
                                    {{ $moisCourant }}
                                </span>

                            </li>


                            <li class="summary-list-item">

                                <span class="summary-list-label">
                                    Salaire de base
                                </span>

                                <span
                                    class="summary-list-value"
                                    id="summaryBase"
                                >
                                    0.00 USD
                                </span>

                            </li>


                            <li class="summary-list-item">

                                <span class="summary-list-label">
                                    Primes
                                </span>

                                <span
                                    class="summary-list-value success"
                                    id="summaryPrimes"
                                >
                                    0.00 USD
                                </span>

                            </li>


                            <li class="summary-list-item">

                                <span class="summary-list-label">
                                    Retenues
                                </span>

                                <span
                                    class="summary-list-value danger"
                                    id="summaryRetenues"
                                >
                                    0.00 USD
                                </span>

                            </li>


                            <li class="summary-list-item">

                                <span class="summary-list-label">
                                    Net
                                </span>

                                <span
                                    class="summary-list-value primary"
                                    id="summaryNet"
                                >
                                    0.00 USD
                                </span>

                            </li>

                        </ul>

                    </div>

                </div>



                {{-- =================================================
                     ÉTAT DU SALAIRE
                     ================================================= --}}
                <div class="summary-card">

                    <div class="summary-header">

                        <i class="fas fa-info-circle"></i>

                        <h2 class="summary-title">
                            État initial
                        </h2>

                    </div>


                    <div class="summary-body">

                        <div class="draft-indicator">

                            <div class="draft-indicator-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>


                            <div class="draft-indicator-content">

                                <span class="draft-indicator-title">
                                    Brouillon
                                </span>

                                <p class="draft-indicator-text">
                                    Le salaire sera créé à l'état brouillon.
                                    Il devra être validé avant tout paiement.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>



                {{-- =================================================
                     RÈGLES DE GESTION
                     ================================================= --}}
                <div class="summary-card">

                    <div class="summary-header">

                        <i class="fas fa-shield-alt"></i>

                        <h2 class="summary-title">
                            Règles de gestion
                        </h2>

                    </div>


                    <div class="summary-body">

                        <ul class="summary-list">

                            <li class="summary-list-item">

                                <span class="summary-list-label">
                                    Statut initial
                                </span>

                                <span class="info-badge">
                                    <i class="fas fa-file-alt"></i>
                                    Brouillon
                                </span>

                            </li>


                            <li class="summary-list-item">

                                <span class="summary-list-label">
                                    Paiement
                                </span>

                                <span class="summary-list-value">
                                    Après validation
                                </span>

                            </li>


                            <li class="summary-list-item">

                                <span class="summary-list-label">
                                    Historique
                                </span>

                                <span class="summary-list-value">
                                    Conservé
                                </span>

                            </li>

                        </ul>

                    </div>

                </div>

            </aside>

        </div>

    </form>

</div>

@endsection


@section('scripts')
    <script src="{{ asset('assets/js/admin/salaires/create.js') }}"></script>
@endsection