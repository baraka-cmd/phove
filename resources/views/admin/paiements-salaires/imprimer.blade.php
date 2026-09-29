@extends('layouts.admin')

@section('title', 'Impression du paiement')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/paiements-salaires/imprimer.css') }}"
    >
@endsection

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Données principales
    |--------------------------------------------------------------------------
    */

    $membre = $salaire?->membre;
    $organe = $salaire?->organe;
    $payePar = $paiement?->payePar;

    /*
    |--------------------------------------------------------------------------
    | Bénéficiaire
    |--------------------------------------------------------------------------
    |
    | Utilisation de l'accessor nom_complet si disponible.
    |
    */
    $nomBeneficiaire = $membre?->nom_complet
        ?? $membre?->nom
        ?? 'Bénéficiaire non renseigné';

    /*
    |--------------------------------------------------------------------------
    | Montants
    |--------------------------------------------------------------------------
    */

    $montant = (float) ($paiement?->montant ?? 0);

    $salaireNet = (float) ($salaire?->salaire_net ?? 0);

    /*
    |--------------------------------------------------------------------------
    | Total payé
    |--------------------------------------------------------------------------
    */

    $totalPaye = $salaire
        ? (float) $salaire->totalPaye
        : $montant;

    /*
    |--------------------------------------------------------------------------
    | Reste à payer
    |--------------------------------------------------------------------------
    */

    $resteAPayer = $salaire
        ? max(0, (float) $salaire->resteAPayer())
        : 0;

    /*
    |--------------------------------------------------------------------------
    | Pourcentage payé
    |--------------------------------------------------------------------------
    */

    $pourcentagePaye = $salaireNet > 0
        ? min(100, max(0, ($totalPaye / $salaireNet) * 100))
        : 0;

    /*
    |--------------------------------------------------------------------------
    | Date du paiement
    |--------------------------------------------------------------------------
    */

    $datePaiement = $paiement?->date_paiement
        ? \Carbon\Carbon::parse($paiement->date_paiement)
        : null;

    /*
    |--------------------------------------------------------------------------
    | Libellé du mode de paiement
    |--------------------------------------------------------------------------
    */

    $modePaiement = $paiement?->mode_paiement ?? 'Non renseigné';

    $modePaiementLabel = match ($modePaiement) {
        'especes' => 'Espèces',
        'virement' => 'Virement bancaire',
        'mobile_money' => 'Mobile Money',
        'cheque' => 'Chèque',
        default => ucfirst(str_replace('_', ' ', $modePaiement)),
    };

    /*
    |--------------------------------------------------------------------------
    | Payeur
    |--------------------------------------------------------------------------
    */

    $nomPayeur = $payePar?->name
        ?? $payePar?->nom
        ?? 'Utilisateur non renseigné';

    /*
    |--------------------------------------------------------------------------
    | Statut du salaire
    |--------------------------------------------------------------------------
    */

    $statut = $salaire?->statut;

    $statutLabel = match ($statut) {
        'brouillon' => 'Brouillon',
        'valide' => 'Validé',
        'partiellement_paye' => 'Partiellement payé',
        'paye' => 'Payé',
        'annule' => 'Annulé',
        default => $statut
            ? ucfirst(str_replace('_', ' ', $statut))
            : 'Non renseigné',
    };

    /*
    |--------------------------------------------------------------------------
    | Informations organisationnelles
    |--------------------------------------------------------------------------
    |
    | Ces valeurs restent volontairement tolérantes afin de ne pas
    | supposer des colonnes qui n'existent pas dans les modèles.
    |
    */

    $organisationNom = config('app.name', 'PHOVE');

    $organisationAdresse = config(
        'app.address',
        'Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies'
    );

    $organisationTelephone = config(
        'app.phone',
        null
    );

    $organisationEmail = config(
        'app.email',
        null
    );

    /*
    |--------------------------------------------------------------------------
    | Référence
    |--------------------------------------------------------------------------
    */

    $referencePaiement = $paiement?->reference ?? '—';

    $referenceSalaire = $salaire?->reference ?? '—';

    /*
    |--------------------------------------------------------------------------
    | Notes
    |--------------------------------------------------------------------------
    */

    $notes = $paiement?->notes;
@endphp

<div class="salary-payment-print">

    {{-- ==========================================================
         BARRE D'ACTIONS
    =========================================================== --}}
    <div class="print-toolbar" data-no-print>

        <div class="print-toolbar__left">

            <a
                href="{{ route('admin.paiements-salaires.show', $paiement) }}"
                class="print-toolbar__back"
            >
                <i class="fas fa-arrow-left"></i>
                <span>Retour au paiement</span>
            </a>

        </div>

        <div class="print-toolbar__right">

            <button
                type="button"
                class="print-toolbar__button print-toolbar__button--primary"
                data-print
            >
                <i class="fas fa-print"></i>
                <span>Imprimer</span>
            </button>

        </div>

    </div>


    {{-- ==========================================================
         DOCUMENT
    =========================================================== --}}
    <article class="payment-print-document">

        {{-- ======================================================
             EN-TÊTE
        ======================================================= --}}
        <header class="document-header">

            <div class="document-header__organization">

                <div class="organization-mark">
                    <img
                        src="{{ asset('assets/images/favicon-phove.png') }}"
                        alt="Logo PHOVE"
                    >
                </div>

                <div class="organization-details">

                    <div class="organization-name">
                        {{ $organisationNom }}
                    </div>

                    <div class="organization-description">
                        {{ $organisationAdresse }}
                    </div>

                    @if($organisationTelephone || $organisationEmail)
                        <div class="organization-contact">

                            @if($organisationTelephone)
                                <span>
                                    <i class="fas fa-phone"></i>
                                    {{ $organisationTelephone }}
                                </span>
                            @endif

                            @if($organisationEmail)
                                <span>
                                    <i class="fas fa-envelope"></i>
                                    {{ $organisationEmail }}
                                </span>
                            @endif

                        </div>
                    @endif

                </div>

            </div>


            <div class="document-header__identity">

                <div class="document-label">
                    DOCUMENT FINANCIER
                </div>

                <h1 class="document-title">
                    Reçu de paiement
                </h1>

                <div class="document-number">
                    {{ $referencePaiement }}
                </div>

                @if($datePaiement)
                    <div class="document-date">
                        Émis le
                        <strong>
                            {{ $datePaiement->format('d/m/Y') }}
                        </strong>
                    </div>
                @endif

            </div>

        </header>


        {{-- ======================================================
             LIGNE DE SÉPARATION
        ======================================================= --}}
        <div class="document-divider"></div>


        {{-- ======================================================
             RÉFÉRENCES
        ======================================================= --}}
        <section class="document-section">

            <div class="section-heading">
                <div class="section-heading__icon">
                    <i class="fas fa-fingerprint"></i>
                </div>

                <div>
                    <div class="section-heading__eyebrow">
                        IDENTIFICATION
                    </div>

                    <h2 class="section-heading__title">
                        Références de l'opération
                    </h2>
                </div>
            </div>


            <div class="reference-grid">

                <div class="reference-card">
                    <span class="reference-card__label">
                        Référence paiement
                    </span>

                    <strong class="reference-card__value reference-card__value--mono">
                        {{ $referencePaiement }}
                    </strong>
                </div>

                <div class="reference-card">
                    <span class="reference-card__label">
                        Référence salaire
                    </span>

                    <strong class="reference-card__value reference-card__value--mono">
                        {{ $referenceSalaire }}
                    </strong>
                </div>

                <div class="reference-card">
                    <span class="reference-card__label">
                        Période salariale
                    </span>

                    <strong class="reference-card__value">
                        {{ $salaire?->mois ?? '—' }}
                    </strong>
                </div>

            </div>

        </section>


        {{-- ======================================================
             BÉNÉFICIAIRE
        ======================================================= --}}
        <section class="document-section">

            <div class="section-heading">

                <div class="section-heading__icon">
                    <i class="fas fa-user"></i>
                </div>

                <div>
                    <div class="section-heading__eyebrow">
                        BÉNÉFICIAIRE
                    </div>

                    <h2 class="section-heading__title">
                        Informations du bénéficiaire
                    </h2>
                </div>

            </div>


            <div class="beneficiary-card">

                <div class="beneficiary-card__main">

                    <div class="beneficiary-avatar">
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="beneficiary-identity">

                        <span class="beneficiary-label">
                            Nom complet
                        </span>

                        <strong class="beneficiary-name">
                            {{ $nomBeneficiaire }}
                        </strong>

                    </div>

                </div>


                <div class="beneficiary-meta">

                    <div class="meta-item">

                        <span class="meta-item__label">
                            Organe
                        </span>

                        <strong class="meta-item__value">
                            {{ $organe?->nom ?? $organe?->libelle ?? '—' }}
                        </strong>

                    </div>

                    <div class="meta-item">

                        <span class="meta-item__label">
                            Période
                        </span>

                        <strong class="meta-item__value">
                            {{ $salaire?->mois ?? '—' }}
                        </strong>

                    </div>

                </div>

            </div>

        </section>


        {{-- ======================================================
             MONTANT PRINCIPAL
        ======================================================= --}}
        <section class="amount-panel">

            <div class="amount-panel__label">
                MONTANT DU PAIEMENT
            </div>

            <div class="amount-panel__value">

                <span class="amount-panel__number">
                    {{ number_format($montant, 2, ',', ' ') }}
                </span>

                <span class="amount-panel__currency">
                    USD
                </span>

            </div>

            <div class="amount-panel__status">
                <i class="fas fa-check-circle"></i>
                Paiement enregistré
            </div>

        </section>


        {{-- ======================================================
             DÉTAILS DU PAIEMENT
        ======================================================= --}}
        <section class="document-section">

            <div class="section-heading">

                <div class="section-heading__icon">
                    <i class="fas fa-money-check-alt"></i>
                </div>

                <div>
                    <div class="section-heading__eyebrow">
                        OPÉRATION
                    </div>

                    <h2 class="section-heading__title">
                        Détails du paiement
                    </h2>
                </div>

            </div>


            <div class="details-grid">

                <div class="detail-item">

                    <span class="detail-item__label">
                        Mode de paiement
                    </span>

                    <strong class="detail-item__value">
                        {{ $modePaiementLabel }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span class="detail-item__label">
                        Date du paiement
                    </span>

                    <strong class="detail-item__value">
                        {{ $datePaiement?->format('d/m/Y') ?? '—' }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span class="detail-item__label">
                        Responsable du paiement
                    </span>

                    <strong class="detail-item__value">
                        {{ $nomPayeur }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span class="detail-item__label">
                        Statut du salaire
                    </span>

                    <strong class="detail-item__value">
                        {{ $statutLabel }}
                    </strong>

                </div>

            </div>

        </section>


        {{-- ======================================================
             SITUATION SALARIALE
        ======================================================= --}}
        <section class="document-section">

            <div class="section-heading">

                <div class="section-heading__icon">
                    <i class="fas fa-chart-pie"></i>
                </div>

                <div>
                    <div class="section-heading__eyebrow">
                        SITUATION FINANCIÈRE
                    </div>

                    <h2 class="section-heading__title">
                        Situation du salaire
                    </h2>
                </div>

            </div>


            <div class="financial-grid">

                <div class="financial-card">

                    <span class="financial-card__label">
                        Salaire net
                    </span>

                    <strong class="financial-card__value">
                        {{ number_format($salaireNet, 2, ',', ' ') }}
                        <small>USD</small>
                    </strong>

                </div>


                <div class="financial-card">

                    <span class="financial-card__label">
                        Total payé
                    </span>

                    <strong class="financial-card__value financial-card__value--positive">
                        {{ number_format($totalPaye, 2, ',', ' ') }}
                        <small>USD</small>
                    </strong>

                </div>


                <div class="financial-card">

                    <span class="financial-card__label">
                        Reste à payer
                    </span>

                    <strong class="financial-card__value">
                        {{ number_format($resteAPayer, 2, ',', ' ') }}
                        <small>USD</small>
                    </strong>

                </div>

            </div>


            {{-- Progression --}}
            <div class="payment-progress">

                <div class="payment-progress__header">

                    <span>
                        Progression du paiement
                    </span>

                    <strong>
                        {{ number_format($pourcentagePaye, 0) }} %
                    </strong>

                </div>

                <div class="payment-progress__track">
                    <div
                        class="payment-progress__bar"
                        style="width: {{ $pourcentagePaye }}%;"
                    ></div>
                </div>

            </div>

        </section>


        {{-- ======================================================
             NOTES
        ======================================================= --}}
        @if($notes)
            <section class="document-section">

                <div class="section-heading">

                    <div class="section-heading__icon">
                        <i class="fas fa-sticky-note"></i>
                    </div>

                    <div>
                        <div class="section-heading__eyebrow">
                            OBSERVATION
                        </div>

                        <h2 class="section-heading__title">
                            Notes
                        </h2>
                    </div>

                </div>


                <div class="notes-card">
                    {{ $notes }}
                </div>

            </section>
        @endif


        {{-- ======================================================
             CERTIFICATION
        ======================================================= --}}
        <section class="certification-box">

            <div class="certification-box__icon">
                <i class="fas fa-shield-alt"></i>
            </div>

            <div class="certification-box__content">

                <strong>
                    Certification de l'opération
                </strong>

                <p>
                    Le présent document atteste de l'enregistrement
                    du paiement indiqué ci-dessus dans le système
                    de gestion des salaires.
                </p>

            </div>

        </section>


        {{-- ======================================================
             SIGNATURES
        ======================================================= --}}
        <section class="signatures-grid">

            <div class="signature-block">

                <div class="signature-block__title">
                    Responsable du paiement
                </div>

                <div class="signature-block__space"></div>

                <div class="signature-block__line"></div>

                <strong class="signature-block__name">
                    {{ $nomPayeur }}
                </strong>

            </div>


            <div class="signature-block">

                <div class="signature-block__title">
                    Bénéficiaire
                </div>

                <div class="signature-block__space"></div>

                <div class="signature-block__line"></div>

                <strong class="signature-block__name">
                    {{ $nomBeneficiaire }}
                </strong>

            </div>

        </section>


        {{-- ======================================================
             PIED DE DOCUMENT
        ======================================================= --}}
        <footer class="document-footer">

            <div class="document-footer__security">

                <i class="fas fa-lock"></i>

                <span>
                    Document généré par le système de gestion PHOVE
                </span>

            </div>

            <div class="document-footer__reference">
                Référence :
                <strong>{{ $referencePaiement }}</strong>
            </div>

        </footer>

    </article>

</div>

@endsection


@section('scripts')
    <script src="{{ asset('assets/js/admin/paiements-salaires/imprimer.js') }}"></script>
@endsection