@extends('layouts.admin')

@section('title', 'Sortie de stock — ' . $stock->designation)

@section('page_title', 'Sortie de stock')
@section('page_icon', 'arrow-up')
@section('page_subtitle', 'Enregistrer une sortie pour : ' . $stock->designation)

@php
    $breadcrumb = [
        ['label' => 'Stocks', 'url' => route('admin.stocks.index')],
        ['label' => $stock->designation, 'url' => route('admin.stocks.show', $stock)],
        'Sortie'
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
                    class="fas fa-arrow-up"
                    style="color: #F59E0B;"
                ></i>

                Sortie de stock

            </h2>

        </div>


        {{-- =========================================================
             AVERTISSEMENT ARCHITECTURAL
        ========================================================== --}}
        <div
            style="
                background: #FEF3C7;
                border-left: 4px solid #F59E0B;
                border-radius: 10px;
                padding: 14px 18px;
                margin: 0 24px 20px;
            "
        >

            <div
                style="
                    display: flex;
                    align-items: flex-start;
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
                        color: #F59E0B;
                        flex-shrink: 0;
                    "
                >

                    <i class="fas fa-triangle-exclamation"></i>

                </div>

                <div>

                    <div
                        style="
                            font-size: 14px;
                            font-weight: 700;
                            color: #92400E;
                            margin-bottom: 4px;
                        "
                    >
                        Sortie hors bon de sortie
                    </div>

                    <div
                        style="
                            font-size: 12px;
                            color: #92400E;
                            line-height: 1.5;
                        "
                    >
                        Cette interface permet une sortie directe.
                        Pour une traçabilité complète, privilégiez
                        la création d’un <strong>bon de sortie</strong>
                        validé par le workflow normal.
                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             INFO ARTICLE
        ========================================================== --}}
        <div
            style="
                background: #FEF3C7;
                border-left: 4px solid #F59E0B;
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
                        color: #F59E0B;
                    "
                >

                    <i class="fas fa-box"></i>

                </div>

                <div>

                    <div
                        style="
                            font-size: 14px;
                            font-weight: 700;
                            color: #92400E;
                        "
                    >
                        {{ $stock->designation }}
                    </div>

                    <div
                        style="
                            font-size: 12px;
                            color: #92400E;
                        "
                    >

                        Stock disponible :
                        <strong>
                            {{ number_format(
                                (float) $stock->quantiteDisponible(),
                                2,
                                ',',
                                ' '
                            ) }}
                            {{ $stock->unite }}
                        </strong>

                        @if($stock->seuil_alerte !== null)
                            — Seuil :
                            {{ number_format((float) $stock->seuil_alerte, 2, ',', ' ') }}
                            {{ $stock->unite }}
                        @endif

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
            action="{{ route('admin.stocks.sorties.store') }}"
            method="POST"
            novalidate
        >

            @csrf


            {{-- =====================================================
                 SECTION — INFORMATIONS DE LA SORTIE
            ====================================================== --}}
            <div class="projet-form-section">

                <h4 class="projet-form-section-title">

                    <i class="fas fa-info-circle"></i>

                    Informations de la sortie

                </h4>


                <div class="projet-form-grid">

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
                            max="{{ (float) $stock->quantiteDisponible() }}"
                            required
                            placeholder="Ex: 10"
                        >

                        @error('quantite')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                        <span class="projet-form-hint">
                            Max disponible :
                            {{ number_format((float) $stock->quantiteDisponible(), 2, ',', ' ') }}
                            {{ $stock->unite }}
                        </span>

                    </div>


                    {{-- DATE DE SORTIE --}}
                    <div class="projet-form-group">

                        <label
                            for="date_sortie"
                            class="projet-form-label"
                        >
                            Date de sortie
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            name="date_sortie"
                            id="date_sortie"
                            class="projet-form-input @error('date_sortie') error @enderror"
                            value="{{ old('date_sortie', date('Y-m-d')) }}"
                            required
                        >

                        @error('date_sortie')

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
                            placeholder="Ex: BON-SOR-001"
                        >

                        @error('reference_document')

                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>

                        @enderror

                    </div>


                    {{-- NATURE --}}
                    <div class="projet-form-group">

                        <label
                            for="nature"
                            class="projet-form-label"
                        >
                            Nature de la sortie
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
                                value="distribution"
                                @selected(old('nature') === 'distribution')
                            >
                                Distribution
                            </option>

                            <option
                                value="perte"
                                @selected(old('nature') === 'perte')
                            >
                                Perte
                            </option>

                            <option
                                value="peremption"
                                @selected(old('nature') === 'peremption')
                            >
                                Péremption
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


                    {{-- DESTINATAIRE --}}
                    <div class="projet-form-group">

                        <label
                            for="destinataire"
                            class="projet-form-label"
                        >
                            Bénéficiaire / Destination
                        </label>

                        <input
                            type="text"
                            name="destinataire"
                            id="destinataire"
                            class="projet-form-input @error('destinataire') error @enderror"
                            value="{{ old('destinataire') }}"
                            maxlength="255"
                            placeholder="Ex: Orphelins de Kiziba"
                        >

                        @error('destinataire')

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
                            placeholder="Ex: Distribution mensuelle"
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
                            placeholder="Notes sur cette sortie..."
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
                    style="background: #F59E0B; border-color: #F59E0B;"
                >

                    <i class="fas fa-arrow-up"></i>

                    Enregistrer la sortie

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