@extends('layouts.admin')

@section('title', 'Enregistrer un paiement de salaire')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/admin/paiements-salaires/create.css') }}">
@endpush

@section('content')

@php
$statut = $salaire->statut;


$paiementAutorise = in_array($statut, [
    \App\Models\Salaire::STATUT_VALIDE,
    \App\Models\Salaire::STATUT_PARTIELLEMENT_PAYE,
], true);

$salaireNet = (float) $salaire->salaire_net;
$totalPaye = (float) $salaire->total_paye;

$resteAPayer = max(0, $salaireNet - $totalPaye);

$pourcentagePaiement = $salaireNet > 0
    ? min(100, ($totalPaye / $salaireNet) * 100)
    : 0;

$montantInitial = old('montant', '');

$datePaiement = old(
    'date_paiement',
    now()->format('Y-m-d')
);

$nomBeneficiaire = $salaire->membre->nom_complet
    ?? $salaire->membre->nom
    ?? 'Membre';

$initiale = strtoupper(
    substr(
        $salaire->membre->nom
            ?? $salaire->membre->nom_complet
            ?? 'M',
        0,
        1
    )
);


@endphp

<div
    class="salary-payment-create"
    data-salaire-id="{{ $salaire->id }}"
    data-statut="{{ $statut }}"
>


{{-- =========================================================
     EN-TÊTE
     ========================================================= --}}
<header class="page-header">

    <div class="page-header-content">

        <div class="page-title-icon">
            <i class="fas fa-money-check-dollar"></i>
        </div>

        <div>
            <h1 class="page-title">
                Enregistrer un paiement
            </h1>

            <p class="page-subtitle">
                Enregistrez une opération de paiement liée à cette obligation salariale.
            </p>
        </div>

    </div>

    <div class="page-actions">

        <a
            href="{{ route('admin.salaires.show', $salaire) }}"
            class="btn btn-secondary"
        >
            <i class="fas fa-arrow-left"></i>
            <span>Retour au salaire</span>
        </a>

    </div>

</header>


{{-- =========================================================
     MESSAGES DE SESSION
     ========================================================= --}}
@if(session('success'))
    <div class="alert alert-success">

        <i class="fas fa-circle-check"></i>

        <div>
            <strong>Opération réussie</strong>
            <p>{{ session('success') }}</p>
        </div>

        <button
            type="button"
            aria-label="Fermer"
            onclick="this.closest('.alert').remove()"
        >
            <i class="fas fa-xmark"></i>
        </button>

    </div>
@endif


@if(session('error'))
    <div class="alert alert-danger">

        <i class="fas fa-circle-exclamation"></i>

        <div>
            <strong>Opération impossible</strong>
            <p>{{ session('error') }}</p>
        </div>

        <button
            type="button"
            aria-label="Fermer"
            onclick="this.closest('.alert').remove()"
        >
            <i class="fas fa-xmark"></i>
        </button>

    </div>
@endif


{{-- =========================================================
     ERREURS DE VALIDATION
     ========================================================= --}}
@if($errors->any())
    <div class="validation-errors">

        <div class="validation-errors-title">
            <i class="fas fa-triangle-exclamation"></i>

            <strong>
                Vérifiez les informations saisies
            </strong>
        </div>

        <ul>
            @foreach($errors->all() as $error)
                <li>
                    <i class="fas fa-circle-xmark"></i>
                    <span>{{ $error }}</span>
                </li>
            @endforeach
        </ul>

    </div>
@endif


{{-- =========================================================
     SALAIRE NON PAYABLE
     ========================================================= --}}
@if(!$paiementAutorise)

    @if($statut === \App\Models\Salaire::STATUT_PAYE)

        <div class="locked-state already-paid">

            <div class="locked-icon">
                <i class="fas fa-circle-check"></i>
            </div>

            <h2 class="locked-title">
                Salaire déjà payé
            </h2>

            <p class="locked-text">
                Ce salaire est déjà entièrement payé.
                Aucun nouveau paiement ne peut être enregistré.
            </p>

            <div class="locked-actions">

                <a
                    href="{{ route('admin.salaires.show', $salaire) }}"
                    class="btn btn-primary"
                >
                    <i class="fas fa-file-invoice-dollar"></i>
                    Voir le salaire
                </a>

                <a
                    href="{{ route('admin.salaires.index') }}"
                    class="btn btn-secondary"
                >
                    Retour aux salaires
                </a>

            </div>

        </div>

    @elseif($statut === \App\Models\Salaire::STATUT_ANNULE)

        <div class="locked-state cancelled-payment">

            <div class="locked-icon">
                <i class="fas fa-ban"></i>
            </div>

            <h2 class="locked-title">
                Salaire annulé
            </h2>

            <p class="locked-text">
                Ce salaire a été annulé.
                Une obligation salariale annulée ne peut pas recevoir de paiement.
            </p>

            <div class="locked-actions">

                <a
                    href="{{ route('admin.salaires.show', $salaire) }}"
                    class="btn btn-primary"
                >
                    <i class="fas fa-file-invoice-dollar"></i>
                    Voir le salaire
                </a>

                <a
                    href="{{ route('admin.salaires.index') }}"
                    class="btn btn-secondary"
                >
                    Retour aux salaires
                </a>

            </div>

        </div>

    @else

        <div class="locked-state">

            <div class="locked-icon">
                <i class="fas fa-lock"></i>
            </div>

            <h2 class="locked-title">
                Paiement non disponible
            </h2>

            @if($statut === \App\Models\Salaire::STATUT_BROUILLON)
                <p class="locked-text">
                    Ce salaire est encore en brouillon.
                    Il doit d'abord être validé avant qu'un paiement puisse être enregistré.
                </p>
            @else
                <p class="locked-text">
                    Le statut actuel de ce salaire ne permet pas
                    l'enregistrement d'un paiement.
                </p>
            @endif

            <div class="locked-actions">

                <a
                    href="{{ route('admin.salaires.show', $salaire) }}"
                    class="btn btn-primary"
                >
                    <i class="fas fa-file-invoice-dollar"></i>
                    Voir le salaire
                </a>

                <a
                    href="{{ route('admin.salaires.index') }}"
                    class="btn btn-secondary"
                >
                    Retour aux salaires
                </a>

            </div>

        </div>

    @endif

@else

    {{-- =====================================================
         INFORMATIONS DU SALAIRE
         ===================================================== --}}
    <section class="card">

        <div class="card-header">

            <div>
                <div class="card-heading">
                    <i class="card-heading-icon fas fa-file-invoice-dollar"></i>

                    <div>
                        <h2 class="card-title">
                            {{ $salaire->reference }}
                        </h2>

                        <p class="card-subtitle">
                            Obligation salariale
                        </p>
                    </div>
                </div>
            </div>

            <span
                class="status-badge
                @if($statut === \App\Models\Salaire::STATUT_VALIDE)
                    valid
                @elseif($statut === \App\Models\Salaire::STATUT_PARTIELLEMENT_PAYE)
                    partial
                @else
                    draft
                @endif"
            >
                @if($statut === \App\Models\Salaire::STATUT_VALIDE)
                    À payer
                @elseif($statut === \App\Models\Salaire::STATUT_PARTIELLEMENT_PAYE)
                    Partiellement payé
                @else
                    {{ ucfirst(str_replace('_', ' ', $statut)) }}
                @endif
            </span>

        </div>

        <div class="card-body">

            {{-- Informations générales --}}
            <div class="salary-info">

                <div class="info-item">

                    <span class="info-label">
                        <i class="fas fa-hashtag"></i>
                        Référence
                    </span>

                    <strong
                        class="info-value reference"
                        data-copy
                        data-copy-value="{{ $salaire->reference }}"
                        title="Cliquer pour copier"
                    >
                        {{ $salaire->reference }}
                    </strong>

                </div>


                <div class="info-item">

                    <span class="info-label">
                        <i class="fas fa-calendar"></i>
                        Période
                    </span>

                    <strong class="info-value month">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $salaire->mois)->translatedFormat('F Y') }}
                    </strong>

                </div>

            </div>


            {{-- Bénéficiaire --}}
            <div class="beneficiary">

                <div class="beneficiary-avatar">
                    {{ $initiale }}
                </div>

                <div class="beneficiary-info">

                    <span class="beneficiary-name">
                        {{ $nomBeneficiaire }}
                    </span>

                    <div class="beneficiary-meta">

                        @if(isset($salaire->membre->matricule))
                            <span class="beneficiary-meta-item">
                                <i class="fas fa-id-card"></i>
                                {{ $salaire->membre->matricule }}
                            </span>
                        @endif

                        @if($salaire->organe)
                            <span class="beneficiary-meta-item">
                                <i class="fas fa-building"></i>
                                {{ $salaire->organe->nom }}
                            </span>
                        @endif

                    </div>

                </div>

            </div>


            {{-- =================================================
                 RÉSUMÉ FINANCIER
                 ================================================= --}}
            <div class="financial-summary">

                <div class="financial-item">

                    <span class="financial-item-label">
                        Salaire net
                    </span>

                    <strong
                        class="financial-item-value amount-primary"
                        data-net-salary
                        data-value="{{ number_format($salaireNet, 2, '.', '') }}"
                    >
                        {{ number_format($salaireNet, 2, ',', ' ') }}
                        <span class="currency">USD</span>
                    </strong>

                </div>


                <div class="financial-item paid">

                    <span class="financial-item-label">
                        Déjà payé
                    </span>

                    <strong
                        class="financial-item-value"
                        data-total-paid
                        data-value="{{ number_format($totalPaye, 2, '.', '') }}"
                    >
                        {{ number_format($totalPaye, 2, ',', ' ') }}
                        <span class="currency">USD</span>
                    </strong>

                </div>


                <div class="financial-item remaining">

                    <span class="financial-item-label">
                        Reste à payer
                    </span>

                    <strong
                        class="financial-item-value"
                        data-remaining
                        data-value="{{ number_format($resteAPayer, 2, '.', '') }}"
                    >
                        {{ number_format($resteAPayer, 2, ',', ' ') }}
                        <span class="currency">USD</span>
                    </strong>

                </div>

            </div>


            {{-- =================================================
                 PROGRESSION
                 ================================================= --}}
            <div class="payment-progress">

                <div class="progress-header">

                    <span class="progress-label">
                        Progression du paiement
                    </span>

                    <strong
                        class="progress-percentage"
                        data-payment-percentage
                    >
                        {{ number_format($pourcentagePaiement, 1, ',', ' ') }} %
                    </strong>

                </div>

                <div
                    class="progress-track"
                    role="progressbar"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-valuenow="{{ $pourcentagePaiement }}"
                >

                    <div
                        class="progress-bar"
                        data-progress="{{ $pourcentagePaiement }}"
                        style="width: {{ $pourcentagePaiement }}%;"
                    ></div>

                </div>

            </div>

        </div>

    </section>


    {{-- =====================================================
         LAYOUT PRINCIPAL
         ===================================================== --}}
    <div class="payment-layout">

        {{-- =================================================
             FORMULAIRE
             ================================================= --}}
        <main class="payment-main">

            <section class="card">

                <div class="card-header">

                    <div class="card-heading">

                        <i class="card-heading-icon fas fa-money-check-dollar"></i>

                        <div>
                            <h2 class="card-title">
                                Informations du paiement
                            </h2>

                            <p class="card-subtitle">
                                Saisissez les informations relatives à cette opération.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="card-body">

                    <form
                        method="POST"
                        action="{{ route('admin.salaires.paiements.store', $salaire) }}"
                        data-payment-form
                        data-confirm-message="Confirmez-vous l'enregistrement de ce paiement ?"
                    >

                        @csrf


                        {{-- =====================================
                             MONTANT
                             ===================================== --}}
                        <div
                            class="form-group @error('montant') has-error @enderror"
                            data-field="montant"
                        >

                            <label
                                for="montant"
                                class="form-label"
                            >
                                Montant du paiement
                                <span class="required">*</span>
                            </label>

                            <div class="money-field">

                                <input
                                    type="number"
                                    id="montant"
                                    name="montant"
                                    class="form-control"
                                    value="{{ $montantInitial }}"
                                    min="0.01"
                                    max="{{ number_format($resteAPayer, 2, '.', '') }}"
                                    step="0.01"
                                    inputmode="decimal"
                                    autocomplete="off"
                                    placeholder="0.00"
                                    required
                                    data-payment-amount
                                    data-max-amount="{{ number_format($resteAPayer, 2, '.', '') }}"
                                >

                                <span class="currency-suffix">
                                    USD
                                </span>

                            </div>

                            <div class="amount-limit">
                                <span>
                                    Montant maximal autorisé :
                                </span>

                                <strong>
                                    {{ number_format($resteAPayer, 2, ',', ' ') }} USD
                                </strong>
                            </div>

                            <div class="form-help">
                                Le montant doit être supérieur à 0 et ne peut pas dépasser
                                le reste à payer.
                            </div>

                            <div
                                class="field-error"
                                data-field-error
                                @if(!$errors->has('montant')) hidden @endif
                            >
                                <i class="fas fa-circle-exclamation"></i>

                                <span>
                                    {{ $errors->first('montant') }}
                                </span>
                            </div>

                        </div>


                        {{-- =====================================
                             DATE + MODE
                             ===================================== --}}
                        <div class="form-grid">

                            <div
                                class="form-group @error('date_paiement') has-error @enderror"
                                data-field="date_paiement"
                            >

                                <label
                                    for="date_paiement"
                                    class="form-label"
                                >
                                    Date du paiement
                                    <span class="required">*</span>
                                </label>

                                <input
                                    type="date"
                                    id="date_paiement"
                                    name="date_paiement"
                                    class="form-control"
                                    value="{{ $datePaiement }}"
                                    required
                                    data-payment-date
                                >

                                <div class="form-help">
                                    Date réelle à laquelle le paiement a été effectué.
                                </div>

                                <div
                                    class="field-error"
                                    data-field-error
                                    @if(!$errors->has('date_paiement')) hidden @endif
                                >
                                    <i class="fas fa-circle-exclamation"></i>

                                    <span>
                                        {{ $errors->first('date_paiement') }}
                                    </span>
                                </div>

                            </div>


                            <div
                                class="form-group @error('mode_paiement') has-error @enderror"
                                data-field="mode_paiement"
                            >

                                <label
                                    for="mode_paiement"
                                    class="form-label"
                                >
                                    Mode de paiement
                                    <span class="required">*</span>
                                </label>

                                <select
                                    id="mode_paiement"
                                    name="mode_paiement"
                                    class="form-control"
                                    required
                                    data-payment-mode
                                >

                                    <option value="">
                                        Sélectionner un mode
                                    </option>

                                    <option
                                        value="especes"
                                        @selected(old('mode_paiement') === 'especes')
                                    >
                                        Espèces
                                    </option>

                                    <option
                                        value="virement"
                                        @selected(old('mode_paiement') === 'virement')
                                    >
                                        Virement bancaire
                                    </option>

                                    <option
                                        value="mobile_money"
                                        @selected(old('mode_paiement') === 'mobile_money')
                                    >
                                        Mobile Money
                                    </option>

                                    <option
                                        value="cheque"
                                        @selected(old('mode_paiement') === 'cheque')
                                    >
                                        Chèque
                                    </option>

                                </select>

                                <div class="form-help">
                                    Sélectionnez le moyen réellement utilisé.
                                </div>

                                <div
                                    class="field-error"
                                    data-field-error
                                    @if(!$errors->has('mode_paiement')) hidden @endif
                                >
                                    <i class="fas fa-circle-exclamation"></i>

                                    <span>
                                        {{ $errors->first('mode_paiement') }}
                                    </span>
                                </div>

                            </div>

                        </div>


                        {{-- =====================================
                             NOTES
                             ===================================== --}}
                        <div
                            class="form-group @error('notes') has-error @enderror"
                            data-field="notes"
                        >

                            <label
                                for="notes"
                                class="form-label"
                            >
                                Notes
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                class="form-control"
                                rows="5"
                                maxlength="2000"
                                placeholder="Ajoutez une précision utile concernant cette opération..."
                            >{{ old('notes') }}</textarea>

                            <div class="textarea-footer">

                                <span class="textarea-help">
                                    Facultatif. Évitez d'inscrire des informations
                                    sensibles inutiles.
                                </span>

                                <span
                                    class="character-counter"
                                    data-notes-counter
                                >
                                    {{ mb_strlen(old('notes', '')) }} / 2000
                                </span>

                            </div>

                            <div
                                class="field-error"
                                data-field-error
                                @if(!$errors->has('notes')) hidden @endif
                            >
                                <i class="fas fa-circle-exclamation"></i>

                                <span>
                                    {{ $errors->first('notes') }}
                                </span>
                            </div>

                        </div>


                        {{-- =====================================
                             CONFIRMATION
                             ===================================== --}}
                        <div class="security-note">

                            <div class="security-note-icon">
                                <i class="fas fa-shield-halved"></i>
                            </div>

                            <div class="security-note-content">

                                <strong class="security-note-title">
                                    Vérification avant enregistrement
                                </strong>

                                <span class="security-note-text">
                                    Vérifiez le montant, la date et le mode de paiement.
                                    Le serveur recalculera le reste à payer avant
                                    l'enregistrement définitif.
                                </span>

                            </div>

                        </div>


                        {{-- =====================================
                             RESTE APRÈS PAIEMENT
                             ===================================== --}}
                        <div class="remaining-card">

                            <span class="remaining-label">
                                Reste après ce paiement
                            </span>

                            <strong
                                class="remaining-value"
                                data-remaining-after
                            >
                                {{ number_format($resteAPayer, 2, ',', ' ') }}
                                <span class="currency">USD</span>
                            </strong>

                            <span class="remaining-description">
                                Le montant sera recalculé et vérifié côté serveur.
                            </span>

                        </div>


                        {{-- =====================================
                             APERÇU
                             ===================================== --}}
                        <div class="payment-preview">

                            <h3 class="payment-preview-title">
                                <i class="fas fa-calculator"></i>
                                Aperçu du paiement
                            </h3>


                            <div class="preview-row">

                                <span class="preview-label">
                                    Salaire net
                                </span>

                                <strong class="preview-value">
                                    {{ number_format($salaireNet, 2, ',', ' ') }} USD
                                </strong>

                            </div>


                            <div class="preview-row">

                                <span class="preview-label">
                                    Déjà payé
                                </span>

                                <strong class="preview-value">
                                    {{ number_format($totalPaye, 2, ',', ' ') }} USD
                                </strong>

                            </div>


                            <div class="preview-row highlight">

                                <span class="preview-label">
                                    Nouveau paiement
                                </span>

                                <strong
                                    class="preview-value"
                                    data-preview-payment
                                >
                                    {{ $montantInitial !== ''
                                        ? number_format((float) $montantInitial, 2, ',', ' ')
                                        : '0,00'
                                    }}
                                    USD
                                </strong>

                            </div>


                            <div class="preview-row">

                                <span class="preview-label">
                                    Nouveau reste à payer
                                </span>

                                <strong
                                    class="preview-value"
                                    data-preview-remaining
                                >
                                    {{ number_format($resteAPayer, 2, ',', ' ') }} USD
                                </strong>

                            </div>

                        </div>


                        {{-- =====================================
                             ACTIONS
                             ===================================== --}}
                        <div class="form-actions">

                            <a
                                href="{{ route('admin.salaires.show', $salaire) }}"
                                class="btn btn-outline"
                                data-cancel-payment
                            >
                                <i class="fas fa-xmark"></i>
                                Annuler
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                data-submit-payment
                            >
                                <i class="fas fa-check"></i>
                                <span>Enregistrer le paiement</span>
                            </button>

                        </div>

                    </form>

                </div>

            </section>

        </main>


        {{-- =================================================
             SIDEBAR
             ================================================= --}}
        <aside class="payment-sidebar">


            {{-- =============================================
                 SITUATION FINANCIÈRE
                 ============================================= --}}
            <div class="sidebar-card">

                <h3 class="sidebar-card-title">
                    <i class="fas fa-chart-pie"></i>
                    Situation actuelle
                </h3>

                <div class="summary-list">

                    <div class="summary-row">

                        <span class="summary-label">
                            Montant dû
                        </span>

                        <strong class="summary-value">
                            {{ number_format($salaireNet, 2, ',', ' ') }}
                            <span class="currency">USD</span>
                        </strong>

                    </div>


                    <div class="summary-row">

                        <span class="summary-label">
                            Total payé
                        </span>

                        <strong class="summary-value">
                            {{ number_format($totalPaye, 2, ',', ' ') }}
                            <span class="currency">USD</span>
                        </strong>

                    </div>


                    <div class="summary-row">

                        <span class="summary-label">
                            Reste
                        </span>

                        <strong class="summary-value amount-success">
                            {{ number_format($resteAPayer, 2, ',', ' ') }}
                            <span class="currency">USD</span>
                        </strong>

                    </div>

                </div>

            </div>


            {{-- =============================================
                 RÈGLES DE SÉCURITÉ
                 ============================================= --}}
            <div class="sidebar-card">

                <h3 class="sidebar-card-title">
                    <i class="fas fa-shield-halved"></i>
                    Contrôles appliqués
                </h3>

                <div class="security-note">

                    <div class="security-note-icon">
                        <i class="fas fa-lock"></i>
                    </div>

                    <div class="security-note-content">

                        <strong class="security-note-title">
                            Paiement sécurisé
                        </strong>

                        <span class="security-note-text">
                            Le serveur vérifie à nouveau les règles
                            avant l'enregistrement.
                        </span>

                    </div>

                </div>

                <ul class="summary-list">

                    <li class="summary-row">
                        <span class="summary-label">
                            Salaire validé
                        </span>

                        <strong class="summary-value">
                            <i class="fas fa-check"></i>
                        </strong>
                    </li>

                    <li class="summary-row">
                        <span class="summary-label">
                            Montant &gt; 0
                        </span>

                        <strong class="summary-value">
                            <i class="fas fa-check"></i>
                        </strong>
                    </li>

                    <li class="summary-row">
                        <span class="summary-label">
                            Montant ≤ reste dû
                        </span>

                        <strong class="summary-value">
                            <i class="fas fa-check"></i>
                        </strong>
                    </li>

                    <li class="summary-row">
                        <span class="summary-label">
                            Référence automatique
                        </span>

                        <strong class="summary-value">
                            <i class="fas fa-check"></i>
                        </strong>
                    </li>

                </ul>

            </div>


            {{-- =============================================
                 INFORMATION
                 ============================================= --}}
            <div class="sidebar-card">

                <h3 class="sidebar-card-title">
                    <i class="fas fa-circle-info"></i>
                    À savoir
                </h3>

                <div class="security-note">

                    <div class="security-note-icon">
                        <i class="fas fa-info"></i>
                    </div>

                    <div class="security-note-content">

                        <strong class="security-note-title">
                            Important
                        </strong>

                        <span class="security-note-text">
                            Après enregistrement, le paiement sera conservé
                            dans l'historique financier du salaire.
                        </span>

                    </div>

                </div>

            </div>

        </aside>

    </div>

@endif


</div>

@endsection

@push('scripts') <script src="{{ asset('assets/js/admin/paiements-salaires/create.js') }}"></script>
@endpush
