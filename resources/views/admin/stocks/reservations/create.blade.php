@extends('layouts.admin')

@section('title', 'Nouvelle réservation de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/reservations-create.css') }}"
    >
@endsection

@section('content')

<div class="stock-reservation-create">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="stock-reservation-create__header">

        <div class="stock-reservation-create__header-content">

            <div class="stock-reservation-create__breadcrumb">

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span aria-hidden="true">/</span>

                <a href="{{ route('admin.stocks.reservations.index') }}">
                    Réservations
                </a>

                <span aria-hidden="true">/</span>

                <span>Nouvelle réservation</span>

            </div>

            <h1 class="stock-reservation-create__title">
                Nouvelle réservation de stock
            </h1>

            <p class="stock-reservation-create__subtitle">
                Créez une réservation afin de bloquer une quantité de stock
                disponible avant sa sortie physique.
            </p>

        </div>


        <div class="stock-reservation-create__header-actions">

            <a
                href="{{ route('admin.stocks.reservations.index') }}"
                class="stock-reservation-create__btn stock-reservation-create__btn--secondary"
            >

                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>

                <span>Retour</span>

            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES DE SESSION
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-reservation-create__alert stock-reservation-create__alert--success" role="alert">

            <div class="stock-reservation-create__alert-icon">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            </div>

            <div class="stock-reservation-create__alert-content">

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-reservation-create__alert stock-reservation-create__alert--danger" role="alert">

            <div class="stock-reservation-create__alert-icon">
                <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
            </div>

            <div class="stock-reservation-create__alert-content">

                <strong>Impossible de poursuivre</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    {{-- =========================================================
         ERREURS DE VALIDATION
    ========================================================== --}}
    @if($errors->any())

        <div class="stock-reservation-create__alert stock-reservation-create__alert--danger" role="alert">

            <div class="stock-reservation-create__alert-icon">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            </div>

            <div class="stock-reservation-create__alert-content">

                <strong>Vérifiez les informations saisies</strong>

                <ul class="stock-reservation-create__error-list">

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
        method="POST"
        action="{{ route('admin.stocks.reservations.store') }}"
        class="stock-reservation-create__form"
        id="reservation-stock-form"
        novalidate
    >

        @csrf


        {{-- =====================================================
             SECTION 1 — INFORMATIONS GÉNÉRALES
        ====================================================== --}}
        <section class="stock-reservation-create__card">

            <div class="stock-reservation-create__card-header">

                <div>

                    <h2 class="stock-reservation-create__card-title">
                        Informations générales
                    </h2>

                    <p class="stock-reservation-create__card-description">
                        Définissez le demandeur, le projet concerné et
                        les informations générales de la réservation.
                    </p>

                </div>

                <span class="stock-reservation-create__section-number">
                    01
                </span>

            </div>


            <div class="stock-reservation-create__card-body">

                <div class="stock-reservation-create__grid">

                    {{-- DEMANDEUR --}}
                    <div class="stock-reservation-create__field">

                        <label for="demandeur_id" class="stock-reservation-create__label">
                            Demandeur
                            <span class="stock-reservation-create__required">*</span>
                        </label>

                        <div class="stock-reservation-create__input-wrapper">

                            <i class="fa-solid fa-user" aria-hidden="true"></i>

                            <select
                                id="demandeur_id"
                                name="demandeur_id"
                                class="stock-reservation-create__input @error('demandeur_id') is-invalid @enderror"
                                required
                            >

                                <option value="">
                                    Sélectionner le demandeur
                                </option>

                                @foreach($demandeurs as $demandeur)

                                    <option
                                        value="{{ $demandeur->id }}"
                                        @selected(
                                            (string) old('demandeur_id', auth()->id())
                                            === (string) $demandeur->id
                                        )
                                    >
                                        {{ $demandeur->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        @error('demandeur_id')

                            <p class="stock-reservation-create__field-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- PROJET --}}
                    <div class="stock-reservation-create__field">

                        <label for="projet_id" class="stock-reservation-create__label">
                            Projet
                            <span class="stock-reservation-create__optional">
                                Facultatif
                            </span>
                        </label>

                        <div class="stock-reservation-create__input-wrapper">

                            <i class="fa-solid fa-diagram-project" aria-hidden="true"></i>

                            <select
                                id="projet_id"
                                name="projet_id"
                                class="stock-reservation-create__input @error('projet_id') is-invalid @enderror"
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

                            <p class="stock-reservation-create__field-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- DATE DE RÉSERVATION --}}
                    <div class="stock-reservation-create__field">

                        <label for="date_reservation" class="stock-reservation-create__label">
                            Date de réservation
                            <span class="stock-reservation-create__required">*</span>
                        </label>

                        <div class="stock-reservation-create__input-wrapper">

                            <i class="fa-solid fa-calendar-day" aria-hidden="true"></i>

                            <input
                                type="date"
                                id="date_reservation"
                                name="date_reservation"
                                class="stock-reservation-create__input @error('date_reservation') is-invalid @enderror"
                                value="{{ old('date_reservation', now()->format('Y-m-d')) }}"
                                required
                            >

                        </div>

                        @error('date_reservation')

                            <p class="stock-reservation-create__field-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- DATE D'EXPIRATION --}}
                    <div class="stock-reservation-create__field">

                        <label for="date_expiration" class="stock-reservation-create__label">
                            Date d’expiration
                            <span class="stock-reservation-create__optional">
                                Facultative
                            </span>
                        </label>

                        <div class="stock-reservation-create__input-wrapper">

                            <i class="fa-solid fa-calendar-xmark" aria-hidden="true"></i>

                            <input
                                type="date"
                                id="date_expiration"
                                name="date_expiration"
                                class="stock-reservation-create__input @error('date_expiration') is-invalid @enderror"
                                value="{{ old('date_expiration') }}"
                            >

                        </div>

                        <p class="stock-reservation-create__field-help">
                            Après cette date, une réservation active peut
                            être considérée comme expirée par le système.
                        </p>

                        @error('date_expiration')

                            <p class="stock-reservation-create__field-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- DESTINATAIRE --}}
                    <div class="stock-reservation-create__field">

                        <label for="destinataire" class="stock-reservation-create__label">
                            Destinataire
                            <span class="stock-reservation-create__optional">
                                Facultatif
                            </span>
                        </label>

                        <div class="stock-reservation-create__input-wrapper">

                            <i class="fa-solid fa-user-tag" aria-hidden="true"></i>

                            <input
                                type="text"
                                id="destinataire"
                                name="destinataire"
                                class="stock-reservation-create__input @error('destinataire') is-invalid @enderror"
                                value="{{ old('destinataire') }}"
                                maxlength="255"
                                placeholder="Nom du bénéficiaire ou service"
                            >

                        </div>

                        @error('destinataire')

                            <p class="stock-reservation-create__field-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- MOTIF --}}
                    <div class="stock-reservation-create__field">

                        <label for="motif" class="stock-reservation-create__label">
                            Motif
                            <span class="stock-reservation-create__optional">
                                Facultatif
                            </span>
                        </label>

                        <div class="stock-reservation-create__input-wrapper">

                            <i class="fa-solid fa-clipboard-question" aria-hidden="true"></i>

                            <input
                                type="text"
                                id="motif"
                                name="motif"
                                class="stock-reservation-create__input @error('motif') is-invalid @enderror"
                                value="{{ old('motif') }}"
                                maxlength="500"
                                placeholder="Motif de la réservation"
                            >

                        </div>

                        @error('motif')

                            <p class="stock-reservation-create__field-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    {{-- NOTES --}}
                    <div class="stock-reservation-create__field stock-reservation-create__field--full">

                        <label for="notes" class="stock-reservation-create__label">
                            Notes
                            <span class="stock-reservation-create__optional">
                                Facultatif
                            </span>
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                            class="stock-reservation-create__textarea @error('notes') is-invalid @enderror"
                            rows="4"
                            maxlength="5000"
                            placeholder="Informations complémentaires..."
                        >{{ old('notes') }}</textarea>

                        <div class="stock-reservation-create__field-footer">

                            <p class="stock-reservation-create__field-help">
                                Ajoutez uniquement les informations utiles
                                au traitement de la réservation.
                            </p>

                            <span class="stock-reservation-create__counter">
                                0 / 5000
                            </span>

                        </div>

                        @error('notes')

                            <p class="stock-reservation-create__field-error">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             SECTION 2 — ARTICLES À RÉSERVER
        ====================================================== --}}
        <section class="stock-reservation-create__card">

            <div class="stock-reservation-create__card-header">

                <div>

                    <h2 class="stock-reservation-create__card-title">
                        Articles à réserver
                    </h2>

                    <p class="stock-reservation-create__card-description">
                        Ajoutez les articles et les quantités souhaitées.
                        La disponibilité réelle sera vérifiée côté serveur
                        lors de la confirmation.
                    </p>

                </div>

                <span class="stock-reservation-create__section-number">
                    02
                </span>

            </div>


            <div class="stock-reservation-create__card-body">

                <div
                    class="stock-reservation-create__lines"
                    id="reservation-lines"
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
                            class="stock-reservation-create__line"
                            data-reservation-line
                        >

                            <div class="stock-reservation-create__line-header">

                                <div class="stock-reservation-create__line-title">

                                    <span class="stock-reservation-create__line-number">
                                        {{ $index + 1 }}
                                    </span>

                                    <span>Article à réserver</span>

                                </div>

                                <button
                                    type="button"
                                    class="stock-reservation-create__remove-line"
                                    data-remove-line
                                    @disabled(count($oldLines) === 1)
                                    title="Supprimer cette ligne"
                                >
                                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                                </button>

                            </div>


                            <div class="stock-reservation-create__line-grid">

                                {{-- ARTICLE --}}
                                <div class="stock-reservation-create__field">

                                    <label
                                        class="stock-reservation-create__label"
                                        for="lignes_{{ $index }}_stock_id"
                                    >
                                        Article
                                        <span class="stock-reservation-create__required">*</span>
                                    </label>

                                    <div class="stock-reservation-create__input-wrapper">

                                        <i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i>

                                        <select
                                            id="lignes_{{ $index }}_stock_id"
                                            name="lignes[{{ $index }}][stock_id]"
                                            class="stock-reservation-create__input reservation-stock-select @error('lignes.'.$index.'.stock_id') is-invalid @enderror"
                                            data-stock-select
                                            required
                                        >

                                            <option value="">
                                                Sélectionner un article
                                            </option>

                                            @foreach($stocks as $stock)

                                                <option
                                                    value="{{ $stock->id }}"
                                                    data-unite="{{ $stock->unite }}"
                                                    data-quantite-disponible="{{ (float) $stock->quantiteDisponible() }}"
                                                    @selected(($ligne['stock_id'] ?? '') == $stock->id)
                                                >
                                                    {{ $stock->designation }}
                                                    —
                                                    {{ $stock->reference }}
                                                </option>

                                            @endforeach

                                        </select>

                                    </div>

                                    @error('lignes.'.$index.'.stock_id')

                                        <p class="stock-reservation-create__field-error">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- QUANTITÉ DEMANDÉE --}}
                                <div class="stock-reservation-create__field">

                                    <label
                                        class="stock-reservation-create__label"
                                        for="lignes_{{ $index }}_quantite_demandee"
                                    >
                                        Quantité demandée
                                        <span class="stock-reservation-create__required">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        id="lignes_{{ $index }}_quantite_demandee"
                                        name="lignes[{{ $index }}][quantite_demandee]"
                                        class="stock-reservation-create__input @error('lignes.'.$index.'.quantite_demandee') is-invalid @enderror"
                                        value="{{ $ligne['quantite_demandee'] ?? '' }}"
                                        min="0.01"
                                        step="0.01"
                                        placeholder="0,00"
                                        data-quantity-requested
                                        required
                                    >

                                    @error('lignes.'.$index.'.quantite_demandee')

                                        <p class="stock-reservation-create__field-error">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- UNITÉ --}}
                                <div class="stock-reservation-create__field">

                                    <label
                                        class="stock-reservation-create__label"
                                        for="lignes_{{ $index }}_unite"
                                    >
                                        Unité
                                        <span class="stock-reservation-create__optional">
                                            Facultatif
                                        </span>
                                    </label>

                                    <input
                                        type="text"
                                        id="lignes_{{ $index }}_unite"
                                        name="lignes[{{ $index }}][unite]"
                                        class="stock-reservation-create__input reservation-unit-input @error('lignes.'.$index.'.unite') is-invalid @enderror"
                                        value="{{ $ligne['unite'] ?? '' }}"
                                        maxlength="20"
                                        placeholder="Ex. pièce"
                                        data-unit-input
                                    >

                                    @error('lignes.'.$index.'.unite')

                                        <p class="stock-reservation-create__field-error">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- DISPONIBILITÉ --}}
                                <div
                                    class="stock-reservation-create__availability"
                                    data-availability
                                >

                                    <div class="stock-reservation-create__availability-icon">

                                        <i class="fa-solid fa-box-open" aria-hidden="true"></i>

                                    </div>

                                    <div class="stock-reservation-create__availability-content">

                                        <span class="stock-reservation-create__availability-label">
                                            Stock disponible
                                        </span>

                                        <strong
                                            class="stock-reservation-create__availability-value"
                                            data-available-quantity
                                        >
                                            —
                                        </strong>

                                    </div>

                                </div>


                                {{-- NOTES DE LIGNE --}}
                                <div class="stock-reservation-create__field stock-reservation-create__field--full">

                                    <label
                                        class="stock-reservation-create__label"
                                        for="lignes_{{ $index }}_notes"
                                    >
                                        Notes de ligne
                                        <span class="stock-reservation-create__optional">
                                            Facultatif
                                        </span>
                                    </label>

                                    <textarea
                                        id="lignes_{{ $index }}_notes"
                                        name="lignes[{{ $index }}][notes]"
                                        class="stock-reservation-create__textarea @error('lignes.'.$index.'.notes') is-invalid @enderror"
                                        rows="2"
                                        maxlength="5000"
                                        placeholder="Information particulière concernant cet article..."
                                    >{{ $ligne['notes'] ?? '' }}</textarea>

                                    @error('lignes.'.$index.'.notes')

                                        <p class="stock-reservation-create__field-error">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- AJOUT D'UNE LIGNE --}}
                <div class="stock-reservation-create__add-line-wrapper">

                    <button
                        type="button"
                        class="stock-reservation-create__btn stock-reservation-create__btn--outline"
                        id="add-reservation-line"
                    >

                        <i class="fa-solid fa-plus" aria-hidden="true"></i>

                        <span>Ajouter un article</span>

                    </button>

                </div>


                {{-- RÉSUMÉ --}}
                <div class="stock-reservation-create__summary">

                    <div class="stock-reservation-create__summary-item">

                        <span>Nombre d'articles</span>

                        <strong data-lines-count>
                            {{ count($oldLines) }}
                        </strong>

                    </div>

                    <div class="stock-reservation-create__summary-item">

                        <span>Quantité totale demandée</span>

                        <strong data-total-requested>
                            0,00
                        </strong>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             INFORMATION MÉTIER
        ====================================================== --}}
        <div class="stock-reservation-create__information">

            <div class="stock-reservation-create__information-icon">

                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>

            </div>

            <div class="stock-reservation-create__information-content">

                <strong>À propos de la réservation</strong>

                <p>
                    La création d'une réservation ne constitue pas une
                    sortie physique. Elle bloque uniquement la quantité
                    disponible afin d'éviter qu'elle soit attribuée
                    simultanément à une autre opération.
                </p>

                <p>
                    La disponibilité définitive et les règles de stock
                    sont toujours vérifiées côté serveur et dans le
                    service métier.
                </p>

            </div>

        </div>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}
        <div class="stock-reservation-create__form-actions">

            <a
                href="{{ route('admin.stocks.reservations.index') }}"
                class="stock-reservation-create__btn stock-reservation-create__btn--secondary"
            >

                <i class="fa-solid fa-xmark" aria-hidden="true"></i>

                <span>Annuler</span>

            </a>


            <button
                type="submit"
                class="stock-reservation-create__btn stock-reservation-create__btn--primary"
                id="submit-reservation"
            >

                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>

                <span>Créer la réservation</span>

            </button>

        </div>

    </form>

</div>


{{-- =============================================================
     TEMPLATE JAVASCRIPT POUR LES LIGNES DYNAMIQUES
============================================================= --}}
<template id="reservation-line-template">

    <div
        class="stock-reservation-create__line"
        data-reservation-line
    >

        <div class="stock-reservation-create__line-header">

            <div class="stock-reservation-create__line-title">

                <span class="stock-reservation-create__line-number">
                    __NUMBER__
                </span>

                <span>Article à réserver</span>

            </div>

            <button
                type="button"
                class="stock-reservation-create__remove-line"
                data-remove-line
                title="Supprimer cette ligne"
            >
                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
            </button>

        </div>


        <div class="stock-reservation-create__line-grid">

            {{-- ARTICLE --}}
            <div class="stock-reservation-create__field">

                <label
                    class="stock-reservation-create__label"
                    for="lignes___INDEX___stock_id"
                >
                    Article
                    <span class="stock-reservation-create__required">*</span>
                </label>

                <div class="stock-reservation-create__input-wrapper">

                    <i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i>

                    <select
                        id="lignes___INDEX___stock_id"
                        name="lignes[__INDEX__][stock_id]"
                        class="stock-reservation-create__input reservation-stock-select"
                        data-stock-select
                        required
                    >

                        <option value="">
                            Sélectionner un article
                        </option>

                        @foreach($stocks as $stock)

                            <option
                                value="{{ $stock->id }}"
                                data-unite="{{ $stock->unite }}"
                                data-quantite-disponible="{{ (float) $stock->quantiteDisponible() }}"
                            >
                                {{ $stock->designation }}
                                —
                                {{ $stock->reference }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- QUANTITÉ DEMANDÉE --}}
            <div class="stock-reservation-create__field">

                <label
                    class="stock-reservation-create__label"
                    for="lignes___INDEX___quantite_demandee"
                >
                    Quantité demandée
                    <span class="stock-reservation-create__required">*</span>
                </label>

                <input
                    type="number"
                    id="lignes___INDEX___quantite_demandee"
                    name="lignes[__INDEX__][quantite_demandee]"
                    class="stock-reservation-create__input"
                    min="0.01"
                    step="0.01"
                    placeholder="0,00"
                    data-quantity-requested
                    required
                >

            </div>


            {{-- UNITÉ --}}
            <div class="stock-reservation-create__field">

                <label
                    class="stock-reservation-create__label"
                    for="lignes___INDEX___unite"
                >
                    Unité
                    <span class="stock-reservation-create__optional">
                        Facultatif
                    </span>
                </label>

                <input
                    type="text"
                    id="lignes___INDEX___unite"
                    name="lignes[__INDEX__][unite]"
                    class="stock-reservation-create__input reservation-unit-input"
                    maxlength="20"
                    placeholder="Ex. pièce"
                    data-unit-input
                >

            </div>


            {{-- DISPONIBILITÉ --}}
            <div
                class="stock-reservation-create__availability"
                data-availability
            >

                <div class="stock-reservation-create__availability-icon">

                    <i class="fa-solid fa-box-open" aria-hidden="true"></i>

                </div>

                <div class="stock-reservation-create__availability-content">

                    <span class="stock-reservation-create__availability-label">
                        Stock disponible
                    </span>

                    <strong
                        class="stock-reservation-create__availability-value"
                        data-available-quantity
                    >
                        —
                    </strong>

                </div>

            </div>


            {{-- NOTES DE LIGNE --}}
            <div class="stock-reservation-create__field stock-reservation-create__field--full">

                <label
                    class="stock-reservation-create__label"
                    for="lignes___INDEX___notes"
                >
                    Notes de ligne
                    <span class="stock-reservation-create__optional">
                        Facultatif
                    </span>
                </label>

                <textarea
                    id="lignes___INDEX___notes"
                    name="lignes[__INDEX__][notes]"
                    class="stock-reservation-create__textarea"
                    rows="2"
                    maxlength="5000"
                    placeholder="Information particulière concernant cet article..."
                ></textarea>

            </div>

        </div>

    </div>

</template>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/reservations-create.js') }}"
        defer
    ></script>

@endsection