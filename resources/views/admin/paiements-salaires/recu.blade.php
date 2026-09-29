```blade
@extends('layouts.admin')

@section('title', 'Reçu de paiement ' . $paiement->reference)

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/paiements-salaires/recu.css') }}"
    >
@endpush

@php
    /*
    |--------------------------------------------------------------------------
    | Données principales
    |--------------------------------------------------------------------------
    */

    $salaire = $paiement->salaire;
    $membre = $salaire?->membre;
    $payePar = $paiement->payePar;

    $montantPaiement = (float) ($paiement->montant ?? 0);
    $salaireNet = (float) ($salaire?->salaire_net ?? 0);
    $totalPaye = (float) ($salaire?->total_paye ?? 0);

    /*
    |--------------------------------------------------------------------------
    | Situation après ce paiement
    |--------------------------------------------------------------------------
    */

    $totalPaye = max(0, $totalPaye);

    $resteAPayer = max(
        0,
        $salaireNet - $totalPaye
    );

    /*
    |--------------------------------------------------------------------------
    | Situation avant ce paiement
    |--------------------------------------------------------------------------
    */

    $totalPayeAvant = max(
        0,
        $totalPaye - $montantPaiement
    );

    $resteAvant = max(
        0,
        $salaireNet - $totalPayeAvant
    );

    /*
    |--------------------------------------------------------------------------
    | Pourcentage de règlement
    |--------------------------------------------------------------------------
    */

    $pourcentageAvant = $salaireNet > 0
        ? min(100, max(0, ($totalPayeAvant / $salaireNet) * 100))
        : 0;

    $pourcentageApres = $salaireNet > 0
        ? min(100, max(0, ($totalPaye / $salaireNet) * 100))
        : 0;

    /*
    |--------------------------------------------------------------------------
    | Mode de paiement
    |--------------------------------------------------------------------------
    */

    $modePaiement = strtolower(
        trim((string) ($paiement->mode_paiement ?? ''))
    );

    $modeLabels = [
        'especes' => 'Espèces',
        'espece' => 'Espèces',
        'virement' => 'Virement bancaire',
        'mobile_money' => 'Mobile Money',
        'mobile-money' => 'Mobile Money',
        'cheque' => 'Chèque',
    ];

    $modeIcons = [
        'especes' => 'fa-money-bill-wave',
        'espece' => 'fa-money-bill-wave',
        'virement' => 'fa-building-columns',
        'mobile_money' => 'fa-mobile-screen-button',
        'mobile-money' => 'fa-mobile-screen-button',
        'cheque' => 'fa-money-check',
    ];

    $modeClasses = [
        'especes' => 'especes',
        'espece' => 'especes',
        'virement' => 'virement',
        'mobile_money' => 'mobile-money',
        'mobile-money' => 'mobile-money',
        'cheque' => 'cheque',
    ];

    $modeLabel = $modeLabels[$modePaiement]
        ?? ($modePaiement !== ''
            ? ucfirst(str_replace('_', ' ', $modePaiement))
            : 'Non renseigné');

    $modeIcon = $modeIcons[$modePaiement] ?? 'fa-wallet';
    $modeClass = $modeClasses[$modePaiement] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Identité du membre
    |--------------------------------------------------------------------------
    */

    $nomMembre = $membre?->nomComplet
        ?? $membre?->nom
        ?? trim(
            ($membre?->prenom ?? '') . ' ' .
            ($membre?->nom ?? '')
        );

    $nomMembre = trim($nomMembre) !== ''
        ? trim($nomMembre)
        : 'Membre';

    $matricule = $membre?->matricule ?? '—';

    $organeNom = $salaire?->organe?->nom ?? null;

    /*
    |--------------------------------------------------------------------------
    | Dates
    |--------------------------------------------------------------------------
    */

    try {
        $datePaiement = \Carbon\Carbon::parse(
            $paiement->date_paiement
        );

        $datePaiementLongue = $datePaiement->translatedFormat(
            'd F Y'
        );

        $datePaiementCourte = $datePaiement->format(
            'd/m/Y'
        );
    } catch (\Throwable $e) {
        $datePaiementLongue =
            $paiement->date_paiement ?? '—';

        $datePaiementCourte =
            $paiement->date_paiement ?? '—';
    }

    /*
    |--------------------------------------------------------------------------
    | Période salariale
    |--------------------------------------------------------------------------
    */

    try {
        $moisSalaire = $salaire?->mois
            ? \Carbon\Carbon::createFromFormat(
                'Y-m',
                $salaire->mois
            )->translatedFormat('F Y')
            : '—';
    } catch (\Throwable $e) {
        $moisSalaire = $salaire?->mois ?? '—';
    }

    /*
    |--------------------------------------------------------------------------
    | Conversion du montant en lettres
    |--------------------------------------------------------------------------
    */

    $montantEntier = (int) floor($montantPaiement);

    $centimes = (int) round(
        ($montantPaiement - $montantEntier) * 100
    );

    /*
    | Correction éventuelle des arrondis
    */

    if ($centimes >= 100) {
        $montantEntier++;
        $centimes = 0;
    }

    $montantEnLettres = null;

    if (
        class_exists(
            \NumberToWords\NumberToWords::class
        )
    ) {
        try {
            $numberToWords =
                new \NumberToWords\NumberToWords();

            $numberTransformer =
                $numberToWords->getNumberTransformer('fr');

            $montantEnLettres = ucfirst(
                $numberTransformer->toWords(
                    $montantEntier
                )
            );

            if ($centimes > 0) {
                $montantEnLettres .= ' et ' .
                    $numberTransformer->toWords(
                        $centimes
                    ) .
                    ' centime' .
                    ($centimes > 1 ? 's' : '');
            }

            $montantEnLettres .=
                ' dollars américains';
        } catch (\Throwable $e) {
            $montantEnLettres = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Utilisateur ayant effectué le paiement
    |--------------------------------------------------------------------------
    */

    $payeParNom = $payePar?->name
        ?? $payePar?->nom
        ?? $payePar?->nomComplet
        ?? 'Utilisateur';

    /*
    |--------------------------------------------------------------------------
    | Organisation
    |--------------------------------------------------------------------------
    */

    $organisationNom = 'PHOVE';
    $organisationAdresse = null;
    $organisationTelephone = null;
    $organisationEmail = null;
    $organisationLogo = null;

    if (isset($settings)) {
        $organisationNom =
            $settings->nom
            ?? $settings->name
            ?? $organisationNom;

        $organisationAdresse =
            $settings->adresse
            ?? $settings->address
            ?? null;

        $organisationTelephone =
            $settings->telephone
            ?? $settings->phone
            ?? null;

        $organisationEmail =
            $settings->email
            ?? null;

        $organisationLogo =
            $settings->logo
            ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Référence du document
    |--------------------------------------------------------------------------
    */

    $documentNumero =
        $paiement->reference ?? '—';

    /*
    |--------------------------------------------------------------------------
    | Statut du salaire
    |--------------------------------------------------------------------------
    */

    $statutBrut = strtolower(
        trim(
            (string) (
                $salaire?->statut
                ?? $salaire?->status
                ?? ''
            )
        )
    );

    $statutNormalise = strtr(
        $statutBrut,
        [
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'à' => 'a',
            'â' => 'a',
            'î' => 'i',
            'ï' => 'i',
            'ô' => 'o',
            'ù' => 'u',
            'û' => 'u',
            'ç' => 'c',
        ]
    );

    $statutNormalise = str_replace(
        ['-', ' '],
        '_',
        $statutNormalise
    );

    $statutLabels = [
        'brouillon' => 'Brouillon',
        'valide' => 'À payer',
        'partiellement_paye' => 'Partiellement payé',
        'partiellement_paye' => 'Partiellement payé',
        'paye' => 'Payé',
        'annule' => 'Annulé',
        'annulee' => 'Annulé',
        'cancelled' => 'Annulé',
        'canceled' => 'Annulé',
    ];

    $statutLabel =
        $statutLabels[$statutNormalise]
        ?? 'Non renseigné';

    $estAnnule = in_array(
        $statutNormalise,
        [
            'annule',
            'annulee',
            'cancelled',
            'canceled',
        ],
        true
    );

    $estBrouillon =
        $statutNormalise === 'brouillon';

    /*
    |--------------------------------------------------------------------------
    | Génération
    |--------------------------------------------------------------------------
    */

    try {
        $dateGeneration =
            now()->translatedFormat(
                'd F Y à H:i'
            );
    } catch (\Throwable $e) {
        $dateGeneration =
            now()->format('d/m/Y H:i');
    }

    /*
    |--------------------------------------------------------------------------
    | Initiales de l'organisation
    |--------------------------------------------------------------------------
    */

    $organisationInitiales = 'PH';

    $motsOrganisation = preg_split(
        '/\s+/',
        trim($organisationNom)
    );

    if (
        is_array($motsOrganisation) &&
        count($motsOrganisation) >= 2
    ) {
        $organisationInitiales =
            mb_strtoupper(
                mb_substr($motsOrganisation[0], 0, 1) .
                mb_substr(
                    $motsOrganisation[1],
                    0,
                    1
                )
            );
    } elseif (
        trim($organisationNom) !== ''
    ) {
        $organisationInitiales =
            mb_strtoupper(
                mb_substr(
                    trim($organisationNom),
                    0,
                    2
                )
            );
    }
@endphp

@section('content')

<div class="salary-payment-receipt">

    {{-- =========================================================
         BARRE D'ACTIONS
         ========================================================= --}}
    <div class="receipt-toolbar" data-no-print>

        <div class="toolbar-left">

            <a
                href="{{ route('admin.paiements-salaires.show', $paiement) }}"
                class="btn btn-secondary"
                data-back="{{ route('admin.paiements-salaires.show', $paiement) }}"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                <span>Retour</span>
            </a>

        </div>

        <div class="toolbar-right">

            <button
                type="button"
                class="btn btn-primary"
                data-print
            >
                <i class="fas fa-print" aria-hidden="true"></i>
                <span>Imprimer le reçu</span>
            </button>

        </div>

    </div>


    {{-- =========================================================
         CONTENEUR
         ========================================================= --}}
    <div class="receipt-container">

        @if($paiement)

            {{-- =================================================
                 DOCUMENT
                 ================================================= --}}
            <article
                class="receipt-document"
                data-receipt-document
                role="document"
                aria-label="Reçu de paiement de salaire"
            >

                {{-- =============================================
                     BANDE SUPÉRIEURE
                     ============================================= --}}
                <div
                    class="receipt-top-line"
                    aria-hidden="true"
                ></div>


                {{-- =============================================
                     EN-TÊTE ORGANISATION
                     ============================================= --}}
                <header class="organization-header">

                    <div class="organization-identity">

                        <div
                            class="organization-logo"
                            aria-label="Logo {{ $organisationNom }}"
                        >

                            @if($organisationLogo)

                                <img
                                    src="{{ asset('storage/' . ltrim($organisationLogo, '/')) }}"
                                    alt="{{ $organisationNom }}"
                                    data-logo
                                >

                            @else

                                <span aria-hidden="true">
                                    {{ $organisationInitiales }}
                                </span>

                            @endif

                        </div>


                        <div class="organization-info">

                            <div class="organization-name">
                                {{ $organisationNom }}
                            </div>

                            <div class="organization-address">
                                Gestion administrative et financière
                            </div>

                            @if($organisationAdresse)

                                <div class="organization-address">

                                    <i
                                        class="fas fa-location-dot"
                                        aria-hidden="true"
                                    ></i>

                                    {{ $organisationAdresse }}

                                </div>

                            @endif

                            @if($organisationTelephone || $organisationEmail)

                                <div class="organization-contact">

                                    @if($organisationTelephone)

                                        <span>
                                            <i
                                                class="fas fa-phone"
                                                aria-hidden="true"
                                            ></i>
                                            {{ $organisationTelephone }}
                                        </span>

                                    @endif

                                    @if(
                                        $organisationTelephone &&
                                        $organisationEmail
                                    )
                                        <span aria-hidden="true">
                                            &nbsp;•&nbsp;
                                        </span>
                                    @endif

                                    @if($organisationEmail)

                                        <span>
                                            <i
                                                class="fas fa-envelope"
                                                aria-hidden="true"
                                            ></i>
                                            {{ $organisationEmail }}
                                        </span>

                                    @endif

                                </div>

                            @endif

                        </div>

                    </div>


                    <div class="document-identification">

                        <div class="document-label">
                            Justificatif
                        </div>

                        <h1 class="document-title">
                            Reçu de paiement
                        </h1>

                        <div class="document-number">
                            N° {{ $documentNumero }}
                        </div>

                        <div class="document-date">
                            Émis le {{ $datePaiementCourte }}
                        </div>

                    </div>

                </header>


                {{-- =============================================
                     TITRE DU REÇU
                     ============================================= --}}
                <section class="receipt-heading">

                    <h1>
                        Reçu de paiement de salaire
                    </h1>

                    <p>
                        Document officiel attestant l'enregistrement
                        d'un paiement salarial au profit du bénéficiaire
                        identifié ci-dessous.
                    </p>

                </section>


                {{-- =============================================
                     RÉFÉRENCE
                     ============================================= --}}
                <div class="reference-banner">

                    <div class="reference-item">

                        <span class="reference-label">
                            Référence du paiement
                        </span>

                        <span
                            class="reference-value"
                            data-payment-reference
                            aria-label="Référence du paiement"
                        >
                            {{ $paiement->reference }}
                        </span>

                    </div>


                    <div class="reference-item">

                        <span class="reference-label">
                            Date du paiement
                        </span>

                        <span class="reference-value tabular">
                            {{ $datePaiementLongue }}
                        </span>

                    </div>

                </div>


                {{-- =============================================
                     BÉNÉFICIAIRE
                     ============================================= --}}
                <section class="section">

                    <header class="section-header">

                        <div
                            class="section-header-icon"
                            aria-hidden="true"
                        >
                            <i class="fas fa-user"></i>
                        </div>

                        <h2 class="section-title">
                            Bénéficiaire
                        </h2>

                    </header>


                    <div class="beneficiary-box">

                        <div class="beneficiary-item">

                            <span class="beneficiary-label">
                                Nom complet
                            </span>

                            <strong class="beneficiary-value">
                                {{ $nomMembre }}
                            </strong>

                        </div>


                        <div class="beneficiary-item">

                            <span class="beneficiary-label">
                                Matricule
                            </span>

                            <strong class="beneficiary-value font-mono">
                                {{ $matricule }}
                            </strong>

                        </div>


                        <div class="beneficiary-item">

                            <span class="beneficiary-label">
                                Organe
                            </span>

                            <strong class="beneficiary-value">
                                {{ $organeNom ?: '—' }}
                            </strong>

                        </div>

                    </div>

                </section>


                {{-- =============================================
                     MONTANT PRINCIPAL
                     ============================================= --}}
                <section class="amount-section">

                    <span class="amount-label">
                        Montant effectivement payé
                    </span>

                    <div class="amount-value">

                        {{ number_format(
                            $montantPaiement,
                            2,
                            ',',
                            ' '
                        ) }}

                        <span class="amount-currency">
                            USD
                        </span>

                    </div>

                    <div class="amount-words">

                        <span>
                            En lettres :
                        </span>

                        <strong>
                            @if($montantEnLettres)
                                {{ $montantEnLettres }}
                            @else
                                {{ number_format(
                                    $montantPaiement,
                                    2,
                                    ',',
                                    ' '
                                ) }}
                                dollars américains
                            @endif
                        </strong>

                    </div>

                </section>


                {{-- =============================================
                     DÉTAILS DU PAIEMENT
                     ============================================= --}}
                <section class="section">

                    <header class="section-header">

                        <div
                            class="section-header-icon"
                            aria-hidden="true"
                        >
                            <i class="fas fa-money-bill-transfer"></i>
                        </div>

                        <h2 class="section-title">
                            Détails du paiement
                        </h2>

                    </header>


                    <div class="payment-details">

                        <div class="payment-detail">

                            <span class="payment-detail-label">
                                Référence du paiement
                            </span>

                            <strong
                                class="payment-detail-value reference"
                            >
                                {{ $paiement->reference }}
                            </strong>

                        </div>


                        <div class="payment-detail">

                            <span class="payment-detail-label">
                                Date du paiement
                            </span>

                            <strong
                                class="payment-detail-value tabular"
                            >
                                {{ $datePaiementLongue }}
                            </strong>

                        </div>


                        <div class="payment-detail">

                            <span class="payment-detail-label">
                                Mode de paiement
                            </span>

                            <strong class="payment-detail-value">

                                <span
                                    class="payment-mode {{ $modeClass }}"
                                >

                                    <i
                                        class="fas {{ $modeIcon }}"
                                        aria-hidden="true"
                                    ></i>

                                    {{ $modeLabel }}

                                </span>

                            </strong>

                        </div>


                        <div class="payment-detail">

                            <span class="payment-detail-label">
                                Période salariale
                            </span>

                            <strong class="payment-detail-value">
                                {{ $moisSalaire }}
                            </strong>

                        </div>

                    </div>

                </section>


                {{-- =============================================
                     SALAIRE ASSOCIÉ
                     ============================================= --}}
                <section class="section">

                    <header class="section-header">

                        <div
                            class="section-header-icon"
                            aria-hidden="true"
                        >
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>

                        <h2 class="section-title">
                            Situation du salaire
                        </h2>

                    </header>


                    @if($salaire)

                        <div class="salary-info-box">

                            <div class="salary-info-item">

                                <span class="salary-info-label">
                                    Référence du salaire
                                </span>

                                <strong
                                    class="salary-info-value reference"
                                >
                                    {{ $salaire->reference ?? '—' }}
                                </strong>

                            </div>


                            <div class="salary-info-item">

                                <span class="salary-info-label">
                                    Période
                                </span>

                                <strong class="salary-info-value">
                                    {{ $moisSalaire }}
                                </strong>

                            </div>


                            <div class="salary-info-item">

                                <span class="salary-info-label">
                                    État actuel
                                </span>

                                <strong class="salary-info-value">

                                    {{ $statutLabel }}

                                </strong>

                            </div>

                        </div>


                        @if($estAnnule)

                            <div
                                class="receipt-cancelled"
                                role="alert"
                            >

                                <i
                                    class="fas fa-circle-xmark"
                                    aria-hidden="true"
                                ></i>

                                <strong>
                                    Paiement / salaire annulé.
                                </strong>

                            </div>

                        @elseif($estBrouillon)

                            <div
                                class="receipt-warning"
                                role="alert"
                            >

                                <i
                                    class="fas fa-triangle-exclamation"
                                    aria-hidden="true"
                                ></i>

                                <span>
                                    Ce salaire est actuellement à l'état
                                    de brouillon. Les informations présentées
                                    doivent être vérifiées avant toute
                                    validation définitive.
                                </span>

                            </div>

                        @endif


                        {{-- =========================================
                             AVANT / OPÉRATION / APRÈS
                             ========================================= --}}
                        <div class="settlement-grid">

                            <div class="settlement-card before">

                                <span class="settlement-label">
                                    Avant ce paiement
                                </span>

                                <span class="settlement-value">
                                    {{ number_format(
                                        $totalPayeAvant,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                    USD
                                </span>

                                <span class="settlement-label">
                                    Solde restant
                                </span>

                                <span class="settlement-value">
                                    {{ number_format(
                                        $resteAvant,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                    USD
                                </span>

                            </div>


                            <div
                                class="settlement-arrow"
                                aria-hidden="true"
                            >
                                <i class="fas fa-arrow-right"></i>
                            </div>


                            <div class="settlement-card">

                                <span class="settlement-label">
                                    Ce paiement
                                </span>

                                <span class="settlement-value text-primary">
                                    +
                                    {{ number_format(
                                        $montantPaiement,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                    USD
                                </span>

                                <span class="settlement-label">
                                    Opération
                                </span>

                                <span class="settlement-value">
                                    Enregistrée
                                </span>

                            </div>


                            <div
                                class="settlement-arrow"
                                aria-hidden="true"
                            >
                                <i class="fas fa-arrow-right"></i>
                            </div>


                            <div class="settlement-card after">

                                <span class="settlement-label">
                                    Après ce paiement
                                </span>

                                <span class="settlement-value">
                                    {{ number_format(
                                        $totalPaye,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                    USD
                                </span>

                                <span class="settlement-label">
                                    Solde restant
                                </span>

                                <span class="settlement-value">
                                    {{ number_format(
                                        $resteAPayer,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                    USD
                                </span>

                            </div>

                        </div>


                        {{-- =========================================
                             PROGRESSION
                             ========================================= --}}
                        <div class="progress-section">

                            <div class="progress-header">

                                <span class="progress-label">
                                    Progression du règlement
                                </span>

                                <strong class="progress-percentage">
                                    {{ number_format(
                                        $pourcentageApres,
                                        1,
                                        ',',
                                        ' '
                                    ) }} %
                                </strong>

                            </div>


                            <div
                                class="progress-track"
                                role="presentation"
                            >

                                <div
                                    class="progress-bar"
                                    data-progress="{{ $pourcentageApres }}"
                                    role="progressbar"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    aria-valuenow="{{ number_format($pourcentageApres, 1, '.', '') }}"
                                    aria-label="Progression du règlement"
                                ></div>

                            </div>

                        </div>

                    @else

                        <div
                            class="receipt-warning"
                            role="alert"
                        >

                            <i
                                class="fas fa-triangle-exclamation"
                                aria-hidden="true"
                            ></i>

                            <div>

                                <strong>
                                    Salaire associé indisponible
                                </strong>

                                <div>
                                    Les informations détaillées du salaire
                                    ne sont actuellement pas accessibles.
                                </div>

                            </div>

                        </div>

                    @endif

                </section>


                {{-- =============================================
                     NOTES
                     ============================================= --}}
                @if(
                    !empty($paiement->notes)
                )

                    <section class="section">

                        <header class="section-header">

                            <div
                                class="section-header-icon"
                                aria-hidden="true"
                            >
                                <i class="fas fa-note-sticky"></i>
                            </div>

                            <h2 class="section-title">
                                Observations
                            </h2>

                        </header>


                        <div class="notes-box">

                            <div class="notes-title">

                                <i
                                    class="fas fa-note-sticky"
                                    aria-hidden="true"
                                ></i>

                                Notes relatives au paiement

                            </div>

                            <div class="notes-content">
                                {{ $paiement->notes }}
                            </div>

                        </div>

                    </section>

                @endif


                {{-- =============================================
                     CERTIFICATION
                     ============================================= --}}
                <section class="certification">

                    <div class="certification-title">

                        <i
                            class="fas fa-circle-check"
                            aria-hidden="true"
                        ></i>

                        Certification de l'opération

                    </div>


                    <div class="certification-text">

                        Le présent document atteste qu'une opération de
                        paiement d'un montant de
                        <strong>
                            {{ number_format(
                                $montantPaiement,
                                2,
                                ',',
                                ' '
                            ) }} USD
                        </strong>
                        a été enregistrée sous la référence
                        <strong>
                            {{ $paiement->reference }}
                        </strong>
                        le
                        <strong>
                            {{ $datePaiementLongue }}
                        </strong>.

                        Ce reçu est généré à partir des données enregistrées
                        dans le système de gestion financière
                        {{ $organisationNom }}.

                    </div>

                </section>


                {{-- =============================================
                     SIGNATURES
                     ============================================= --}}
                <section class="signatures">

                    <div class="signature-box">

                        <div class="signature-title">
                            Bénéficiaire
                        </div>

                        <div class="signature-line"></div>

                        <div class="signature-name">
                            {{ $nomMembre }}
                        </div>

                        <div class="signature-role">
                            Signature
                        </div>

                    </div>


                    <div class="signature-box">

                        <div class="signature-title">
                            Payeur / Responsable financier
                        </div>

                        <div class="signature-line"></div>

                        <div class="signature-name">
                            {{ $payeParNom }}
                        </div>

                        <div class="signature-role">
                            Signature et cachet
                        </div>

                    </div>

                </section>


                {{-- =============================================
                     PIED DE PAGE
                     ============================================= --}}
                <footer class="receipt-footer">

                    <div class="footer-text">

                        {{ $organisationNom }} —
                        Reçu généré électroniquement.

                    </div>


                    <div class="footer-text">

                        Référence :
                        <span class="font-mono">
                            {{ $paiement->reference }}
                        </span>

                    </div>


                    <div class="footer-security">

                        Généré le {{ $dateGeneration }}

                    </div>

                </footer>


                {{-- =============================================
                     BADGE DE SÉCURITÉ
                     ============================================= --}}
                <div class="footer-security">

                    <span class="security-badge">

                        <i
                            class="fas fa-shield-halved"
                            aria-hidden="true"
                        ></i>

                        Document généré depuis le système
                        de gestion {{ $organisationNom }}

                    </span>

                </div>

            </article>

        @else

            {{-- =================================================
                 ÉTAT VIDE
                 ================================================= --}}
            <div class="empty-receipt">

                <div
                    class="empty-receipt-icon"
                    aria-hidden="true"
                >
                    <i class="fas fa-receipt"></i>
                </div>

                <h2 class="empty-receipt-title">
                    Reçu indisponible
                </h2>

                <p class="empty-receipt-text">
                    Aucun paiement valide n'a été fourni pour générer
                    ce reçu.
                </p>

            </div>

        @endif

    </div>

</div>

@endsection

@push('scripts')
    <script
        src="{{ asset('assets/js/admin/paiements-salaires/recu.js') }}"
    ></script>
@endpush
