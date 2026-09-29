@extends('layouts.admin')

@section('title', 'Entrée de stock — ' . $stock->designation)

@section('page_title', 'Entrée de stock')
@section('page_icon', 'arrow-down')
@section('page_subtitle', 'Enregistrer une entrée pour : ' . $stock->designation)

@php
    $breadcrumb = [
        ['label' => 'Stocks', 'url' => route('admin.stocks.index')],
        ['label' => $stock->designation, 'url' => route('admin.stocks.show', $stock)],
        'Entrée'
    ];
@endphp

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin-common.css') }}"
    >
@endsection

@section('content')

    <div class="card">

        {{-- =========================================================
             EN-TÊTE DE CARTE
        ========================================================== --}}
        <div class="card-header">

            <h2 class="card-title">

                <i
                    class="fas fa-arrow-down"
                    style="color: #10B981;"
                ></i>

                Entrée de stock

            </h2>

        </div>


        {{-- =========================================================
             INFO ARTICLE
        ========================================================== --}}
        <div
            style="
                background: #D1FAE5;
                border-left: 4px solid #10B981;
                border-radius: 10px;
                padding: 14px 18px;
                margin: 0 24px 24px;
            "
        >

            <div
                style="
                    display: flex;
                    align-items: center;
                    gap: 12px;
                "
            >

                <div
                    style="
                        width: 40px;
                        height: 40px;
                        border-radius: 10px;
                        background: #FFFFFF;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #10B981;
                    "
                >

                    <i class="fas fa-box"></i>

                </div>

                <div>

                    <div
                        style="
                            font-size: 14px;
                            font-weight: 700;
                            color: #065F46;
                        "
                    >
                        {{ $stock->designation }}
                    </div>

                    <div
                        style="
                            font-size: 12px;
                            color: #065F46;
                        "
                    >

                        Stock actuel :
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

                </div>

            </div>

        </div>


        {{-- =========================================================
             ERREURS DE VALIDATION
        ========================================================== --}}
        @if($errors->any())

            <div
                class="alert alert-error"
                style="margin-bottom: 20px;"
                role="alert"
            >

                <i class="fas fa-exclamation-triangle"></i>

                <span>
                    Veuillez corriger les erreurs ci-dessous.
                </span>

            </div>

        @endif


        {{-- =========================================================
             FORMULAIRE
        ========================================================== --}}
        <form
            action="{{ route('admin.stocks.entrees.store') }}"
            method="POST"
            novalidate
        >

            @csrf


            {{-- =====================================================
                 SECTION — INFORMATIONS DE L'ENTRÉE
            ====================================================== --}}
            <div class="projet-form-section">

                <h4 class="projet-form-section-title">

                    <i class="fas fa-info-circle"></i>

                    Informations de l'entrée

                </h4>


                <div class="projet-form-grid">

                    {{-- ARTICLE (pré-rempli et caché) --}}
                    <input
                        type="hidden"
                        name="stock_id"
                        value="{{ old('stock_id', $stock->id) }}"
                    >


                    {{-- QUANTITÉ --}}
                    <div class="projet-form-group">

                        <label
                            for="quantite"
                            class="projet-form-label"
                        >
                            Quantité
                            <span class="required">*</span>
                        </label>

                        <input
                            type="number"
                            name="quantite"
                            id="quantite"
                            class="projet-form-input @error('quantite') error @enderror"
                            value="{{ old('quantite') }}"
                            step="0.01"
                            min="0.01"
                            required
                            placeholder="Ex: 50"
                        >

                        @error('quantite')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                        <span class="projet-form-hint">
                            Unité : {{ $stock->unite }}
                        </span>

                    </div>


                    {{-- NUMÉRO DE LOT --}}
                    <div class="projet-form-group">

                        <label
                            for="numero_lot"
                            class="projet-form-label"
                        >
                            Numéro de lot
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="numero_lot"
                            id="numero_lot"
                            class="projet-form-input @error('numero_lot') error @enderror"
                            value="{{ old('numero_lot') }}"
                            maxlength="100"
                            required
                            placeholder="Ex: LOT-2026-001"
                        >

                        @error('numero_lot')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                        <span class="projet-form-hint">
                            Obligatoire pour la traçabilité.
                        </span>

                    </div>


                    {{-- NATURE --}}
                    <div class="projet-form-group">

                        <label
                            for="nature"
                            class="projet-form-label"
                        >
                            Nature de l'entrée
                            <span class="required">*</span>
                        </label>

                        <select
                            name="nature"
                            id="nature"
                            class="projet-form-select @error('nature') error @enderror"
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

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- DATE D'ENTRÉE --}}
                    <div class="projet-form-group">

                        <label
                            for="date_entree"
                            class="projet-form-label"
                        >
                            Date d'entrée
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            name="date_entree"
                            id="date_entree"
                            class="projet-form-input @error('date_entree') error @enderror"
                            value="{{ old('date_entree', date('Y-m-d')) }}"
                            required
                        >

                        @error('date_entree')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- EMPLACEMENT --}}
                    <div class="projet-form-group">

                        <label
                            for="emplacement_id"
                            class="projet-form-label"
                        >
                            Emplacement
                        </label>

                        <select
                            name="emplacement_id"
                            id="emplacement_id"
                            class="projet-form-select @error('emplacement_id') error @enderror"
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

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- RÉFÉRENCE DOCUMENT --}}
                    <div class="projet-form-group">

                        <label
                            for="reference_document"
                            class="projet-form-label"
                        >
                            Référence document
                        </label>

                        <input
                            type="text"
                            name="reference_document"
                            id="reference_document"
                            class="projet-form-input @error('reference_document') error @enderror"
                            value="{{ old('reference_document') }}"
                            maxlength="100"
                            placeholder="Ex: BL-2026-001"
                        >

                        @error('reference_document')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- FOURNISSEUR --}}
                    <div class="projet-form-group">

                        <label
                            for="fournisseur"
                            class="projet-form-label"
                        >
                            Fournisseur / Source
                        </label>

                        <input
                            type="text"
                            name="fournisseur"
                            id="fournisseur"
                            class="projet-form-input @error('fournisseur') error @enderror"
                            value="{{ old('fournisseur') }}"
                            maxlength="255"
                            placeholder="Ex: FAO, WFP, Fournisseur X"
                        >

                        @error('fournisseur')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- DATE DE FABRICATION --}}
                    <div class="projet-form-group">

                        <label
                            for="date_fabrication"
                            class="projet-form-label"
                        >
                            Date de fabrication
                        </label>

                        <input
                            type="date"
                            name="date_fabrication"
                            id="date_fabrication"
                            class="projet-form-input @error('date_fabrication') error @enderror"
                            value="{{ old('date_fabrication') }}"
                        >

                        @error('date_fabrication')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- DATE D'EXPIRATION --}}
                    <div class="projet-form-group">

                        <label
                            for="date_expiration"
                            class="projet-form-label"
                        >
                            Date d'expiration
                        </label>

                        <input
                            type="date"
                            name="date_expiration"
                            id="date_expiration"
                            class="projet-form-input @error('date_expiration') error @enderror"
                            value="{{ old('date_expiration') }}"
                        >

                        @error('date_expiration')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- COÛT UNITAIRE --}}
                    <div class="projet-form-group">

                        <label
                            for="cout_unitaire"
                            class="projet-form-label"
                        >
                            Coût unitaire (USD)
                        </label>

                        <input
                            type="number"
                            name="cout_unitaire"
                            id="cout_unitaire"
                            class="projet-form-input @error('cout_unitaire') error @enderror"
                            value="{{ old('cout_unitaire') }}"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                        >

                        @error('cout_unitaire')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- PROJET --}}
                    <div class="projet-form-group">

                        <label
                            for="projet_id"
                            class="projet-form-label"
                        >
                            Projet lié
                        </label>

                        <select
                            name="projet_id"
                            id="projet_id"
                            class="projet-form-select @error('projet_id') error @enderror"
                        >

                            <option value="">
                                Aucun projet
                            </option>

                            @foreach($projets as $projet)

                                <option
                                    value="{{ $projet->id }}"
                                    @selected(old('projet_id') == $projet->id)
                                >
                                    {{ Str::limit(
                                        $projet->nom
                                            ?? $projet->titre
                                            ?? ('Projet #' . $projet->id),
                                        50
                                    ) }}
                                </option>

                            @endforeach

                        </select>

                        @error('projet_id')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- MOTIF --}}
                    <div class="projet-form-group">

                        <label
                            for="motif"
                            class="projet-form-label"
                        >
                            Motif
                        </label>

                        <input
                            type="text"
                            name="motif"
                            id="motif"
                            class="projet-form-input @error('motif') error @enderror"
                            value="{{ old('motif') }}"
                            maxlength="500"
                            placeholder="Ex: Réapprovisionnement mensuel"
                        >

                        @error('motif')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- NOTES --}}
                    <div class="projet-form-group projet-form-full">

                        <label
                            for="notes"
                            class="projet-form-label"
                        >
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            id="notes"
                            class="projet-form-textarea @error('notes') error @enderror"
                            rows="3"
                            maxlength="5000"
                            placeholder="Notes sur cette entrée..."
                        >{{ old('notes') }}</textarea>

                        @error('notes')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 ACTIONS
            ====================================================== --}}
            <div class="projet-form-actions">

                <a
                    href="{{ route('admin.stocks.show', $stock) }}"
                    class="btn btn-outline"
                >

                    <i class="fas fa-arrow-left"></i>

                    Annuler

                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                    style="background: #10B981; border-color: #10B981;"
                >

                    <i class="fas fa-arrow-down"></i>

                    Enregistrer l'entrée

                </button>

            </div>

        </form>

    </div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin-common.js') }}"
        defer>
    </script>

@endsection