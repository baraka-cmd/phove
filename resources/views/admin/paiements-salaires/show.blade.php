@extends('layouts.admin')

@section('title', 'Détail du paiement ' . $paiement->reference)

@push('styles') <link
     rel="stylesheet"
     href="{{ asset('assets/css/admin/paiements-salaires/show.css') }}"
 >
@endpush

@section('content')

@php
/*
|--------------------------------------------------------------------------
| DONNÉES PRINCIPALES
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
| CALCULS FINANCIERS
|--------------------------------------------------------------------------
*/

$resteAPayer = max(
    0,
    $salaireNet - $totalPaye
);

/*
 * Total payé avant le paiement actuellement consulté.
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
 * Pourcentage après paiement.
 */
$pourcentagePaiement = $salaireNet > 0
    ? min(
        100,
        ($totalPaye / $salaireNet) * 100
    )
    : 0;

/*
 * Pourcentage avant paiement.
 */
$pourcentageAvant = $salaireNet > 0
    ? min(
        100,
        ($totalPayeAvant / $salaireNet) * 100
    )
    : 0;

/*
|--------------------------------------------------------------------------
| MODE DE PAIEMENT
|--------------------------------------------------------------------------
*/

$modePaiement = strtolower(
    trim(
        (string) ($paiement->mode_paiement ?? '')
    )
);

$modeLabels = [
    'especes' => 'Espèces',
    'virement' => 'Virement bancaire',
    'mobile_money' => 'Mobile Money',
    'mobile-money' => 'Mobile Money',
    'cheque' => 'Chèque',
];

$modeIcons = [
    'especes' => 'fa-money-bill-wave',
    'virement' => 'fa-building-columns',
    'mobile_money' => 'fa-mobile-screen-button',
    'mobile-money' => 'fa-mobile-screen-button',
    'cheque' => 'fa-money-check',
];

$modeClassMap = [
    'especes' => 'especes',
    'virement' => 'virement',
    'mobile_money' => 'mobile-money',
    'mobile-money' => 'mobile-money',
    'cheque' => 'cheque',
];

$modeLabel = $modeLabels[$modePaiement]
    ?? ucfirst(
        str_replace(
            '_',
            ' ',
            $modePaiement
        )
    );

$modeIcon = $modeIcons[$modePaiement]
    ?? 'fa-wallet';

$modeClass = $modeClassMap[$modePaiement]
    ?? '';

/*
|--------------------------------------------------------------------------
| STATUT DU SALAIRE
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

/*
 * Normalisation des accents et séparateurs.
 */
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
        'ü' => 'u',
    ]
);

$statutNormalise = str_replace(
    ['-', ' '],
    '_',
    $statutNormalise
);

$statusClassMap = [
    'valide' => 'valid',
    'partiellement_paye' => 'partial',
    'paye' => 'paid',
    'annule' => 'cancelled',
    'brouillon' => 'draft',
];

$statusLabelMap = [
    'valide' => 'À payer',
    'partiellement_paye' => 'Partiellement payé',
    'paye' => 'Payé',
    'annule' => 'Annulé',
    'brouillon' => 'Brouillon',
];

$statusClass = $statusClassMap[$statutNormalise]
    ?? 'draft';

$statusLabel = $statusLabelMap[$statutNormalise]
    ?? (
        $statutNormalise !== ''
            ? ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $statutNormalise
                )
            )
            : 'Inconnu'
    );

/*
|--------------------------------------------------------------------------
| BÉNÉFICIAIRE
|--------------------------------------------------------------------------
*/

$nomMembre = $membre?->nomComplet
    ?? $membre?->nom
    ?? 'Membre';

$matricule = $membre?->matricule
    ?? '—';

$initiale = strtoupper(
    mb_substr(
        trim($nomMembre),
        0,
        1
    )
);

/*
|--------------------------------------------------------------------------
| DATES
|--------------------------------------------------------------------------
*/

try {
    $datePaiement = \Carbon\Carbon::parse(
        $paiement->date_paiement
    );

    $datePaiementLongue =
        $datePaiement->translatedFormat(
            'd F Y'
        );

    $datePaiementCourte =
        $datePaiement->format(
            'd/m/Y'
        );
} catch (\Throwable $e) {
    $datePaiementLongue =
        $paiement->date_paiement
        ?? '—';

    $datePaiementCourte =
        $paiement->date_paiement
        ?? '—';
}

try {
    $moisSalaire =
        \Carbon\Carbon::createFromFormat(
            'Y-m',
            $salaire->mois
        )->translatedFormat('F Y');
} catch (\Throwable $e) {
    $moisSalaire =
        $salaire?->mois
        ?? '—';
}

try {
    $createdAt =
        $paiement->created_at
            ? $paiement->created_at->translatedFormat(
                'd F Y à H:i'
            )
            : '—';
} catch (\Throwable $e) {
    $createdAt = '—';
}

/*
|--------------------------------------------------------------------------
| UTILISATEUR AYANT ENREGISTRÉ LE PAIEMENT
|--------------------------------------------------------------------------
*/

$payeParNom = $payePar?->name
    ?? $payePar?->nom
    ?? 'Utilisateur';


@endphp

<div class="salary-payment-show">


{{-- =========================================================
     EN-TÊTE DE PAGE
     ========================================================= --}}
<header class="page-header">

    <div class="page-header-content">

        <nav class="breadcrumb" aria-label="Fil d'Ariane">

            <a
                href="{{ route('admin.paiements-salaires.index') }}"
            >
                Paiements salaires
            </a>

            <span
                class="breadcrumb-separator"
                aria-hidden="true"
            >
                <i class="fas fa-chevron-right"></i>
            </span>

            <span class="breadcrumb-current">
                {{ $paiement->reference }}
            </span>

        </nav>


        <h1 class="page-title">

            <span class="page-title-icon">
                <i class="fas fa-money-check-dollar"></i>
            </span>

            Détail du paiement

        </h1>


        <p class="page-subtitle">
            Consultation et traçabilité de l'opération
            <strong>{{ $paiement->reference }}</strong>.
        </p>

    </div>


    <div class="page-actions">

        <button
            type="button"
            class="btn btn-secondary"
            data-print
            data-print-url="{{ route('admin.paiements-salaires.imprimer', $paiement) }}"
        >
            <i class="fas fa-print"></i>
            <span>Imprimer</span>
        </button>


        <a
            href="{{ route('admin.paiements-salaires.recu', $paiement) }}"
            class="btn btn-primary"
        >
            <i class="fas fa-receipt"></i>
            <span>Voir le reçu</span>
        </a>

    </div>

</header>


{{-- =========================================================
     MESSAGES
     ========================================================= --}}

@if(session('success'))

    <div class="alert alert-success" role="alert">

        <i
            class="fas fa-circle-check"
            aria-hidden="true"
        ></i>

        <div>
            <strong>Opération réussie</strong>

            <div>
                {{ session('success') }}
            </div>
        </div>

    </div>

@endif


@if(session('error'))

    <div class="alert alert-danger" role="alert">

        <i
            class="fas fa-circle-exclamation"
            aria-hidden="true"
        ></i>

        <div>
            <strong>Opération impossible</strong>

            <div>
                {{ session('error') }}
            </div>
        </div>

    </div>

@endif


@if(session('warning'))

    <div class="alert alert-warning" role="alert">

        <i
            class="fas fa-triangle-exclamation"
            aria-hidden="true"
        ></i>

        <div>
            {{ session('warning') }}
        </div>

    </div>

@endif


@if(session('info'))

    <div class="alert alert-info" role="alert">

        <i
            class="fas fa-circle-info"
            aria-hidden="true"
        ></i>

        <div>
            {{ session('info') }}
        </div>

    </div>

@endif


{{-- =========================================================
     STRUCTURE PRINCIPALE
     ========================================================= --}}
<div class="payment-layout">


    {{-- =====================================================
         COLONNE PRINCIPALE
         ===================================================== --}}
    <main class="payment-main">


        {{-- =================================================
             HERO DU PAIEMENT
             ================================================= --}}
        <section class="payment-hero">

            <div class="payment-hero-top">

                <div class="payment-reference-block">

                    <span class="payment-reference-label">
                        Référence du paiement
                    </span>


                    <strong
                        class="payment-reference"
                        data-payment-reference
                        data-copy-value="{{ $paiement->reference }}"
                        title="{{ $paiement->reference }}"
                    >
                        {{ $paiement->reference }}
                    </strong>


                    <div class="payment-date">

                        <i
                            class="fas fa-calendar-check"
                            aria-hidden="true"
                        ></i>

                        Effectué le
                        {{ $datePaiementLongue }}

                    </div>


                    <button
                        type="button"
                        class="btn btn-outline btn-sm"
                        data-copy
                        data-copy-value="{{ $paiement->reference }}"
                        aria-label="Copier la référence du paiement"
                    >
                        <i class="fas fa-copy"></i>
                        Copier la référence
                    </button>

                </div>


                <div class="payment-amount-block">

                    <span class="payment-amount-label">
                        Montant payé
                    </span>


                    <strong class="payment-amount">

                        {{ number_format(
                            $montantPaiement,
                            2,
                            ',',
                            ' '
                        ) }}

                        <span class="currency">
                            USD
                        </span>

                    </strong>


                    <span
                        class="payment-method-display {{ $modeClass }}"
                    >

                        <i
                            class="fas {{ $modeIcon }}"
                            aria-hidden="true"
                        ></i>

                        {{ $modeLabel }}

                    </span>

                </div>

            </div>


            {{-- Détails essentiels de l'opération --}}
            <div class="payment-details">

                <div class="detail-item">

                    <span class="detail-label">
                        Date d'opération
                    </span>

                    <strong class="detail-value">
                        {{ $datePaiementCourte }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Mode de paiement
                    </span>

                    <strong class="detail-value">

                        <span
                            class="payment-method-display {{ $modeClass }}"
                        >

                            <i
                                class="fas {{ $modeIcon }}"
                                aria-hidden="true"
                            ></i>

                            {{ $modeLabel }}

                        </span>

                    </strong>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Enregistré le
                    </span>

                    <strong class="detail-value">
                        {{ $createdAt }}
                    </strong>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Enregistré par
                    </span>

                    <strong class="detail-value">
                        {{ $payeParNom }}
                    </strong>

                </div>

            </div>

        </section>


        {{-- =================================================
             BÉNÉFICIAIRE
             ================================================= --}}
        <section class="card">

            <div class="card-header">

                <div class="card-heading">

                    <span class="card-heading-icon">
                        <i class="fas fa-user"></i>
                    </span>

                    <div>

                        <h2 class="card-title">
                            Bénéficiaire
                        </h2>

                        <p class="card-subtitle">
                            Informations du membre ayant reçu le paiement
                        </p>

                    </div>

                </div>

            </div>


            <div class="card-body">

                <div class="beneficiary">

                    <div class="beneficiary-avatar">

                        @if($membre?->photo ?? false)

                            <img
                                src="{{ asset($membre->photo) }}"
                                alt="Photo de {{ $nomMembre }}"
                                data-logo
                            >

                        @else

                            {{ $initiale }}

                        @endif

                    </div>


                    <div class="beneficiary-content">

                        <strong class="beneficiary-name">
                            {{ $nomMembre }}
                        </strong>


                        <div class="beneficiary-meta">

                            <span class="beneficiary-meta-item">

                                <i
                                    class="fas fa-id-card"
                                    aria-hidden="true"
                                ></i>

                                {{ $matricule }}

                            </span>


                            @if($membre?->organe)

                                <span class="beneficiary-meta-item">

                                    <i
                                        class="fas fa-building"
                                        aria-hidden="true"
                                    ></i>

                                    {{ $membre->organe }}

                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </section>


        {{-- =================================================
             SALAIRE ASSOCIÉ
             ================================================= --}}
        <section class="card">

            <div class="card-header">

                <div class="card-heading">

                    <span class="card-heading-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </span>

                    <div>

                        <h2 class="card-title">
                            Salaire associé
                        </h2>

                        <p class="card-subtitle">
                            Salaire concerné par cette opération
                        </p>

                    </div>

                </div>

            </div>


            <div class="card-body">

                @if($salaire)

                    <div class="salary-reference">

                        <div class="salary-reference-info">

                            <span class="salary-reference-label">
                                Référence du salaire
                            </span>


                            <strong class="salary-reference-code">
                                {{ $salaire->reference }}
                            </strong>


                            <span class="salary-reference-period">
                                Période : {{ $moisSalaire }}
                            </span>

                        </div>


                        <div class="salary-reference-action">

                            <a
                                href="{{ route('admin.salaires.show', $salaire) }}"
                                class="btn btn-outline btn-sm"
                            >
                                <i class="fas fa-eye"></i>
                                Voir le salaire
                            </a>

                        </div>

                    </div>


                    <div class="section-title">

                        <i
                            class="fas fa-calculator"
                            aria-hidden="true"
                        ></i>

                        Situation financière

                    </div>


                    <div class="financial-summary">

                        <div class="financial-item total">

                            <span class="financial-label">
                                Salaire net
                            </span>

                            <strong class="financial-value">

                                {{ number_format(
                                    $salaireNet,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                <span class="currency">
                                    USD
                                </span>

                            </strong>

                        </div>


                        <div class="financial-item paid">

                            <span class="financial-label">
                                Total payé
                            </span>

                            <strong class="financial-value">

                                {{ number_format(
                                    $totalPaye,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                <span class="currency">
                                    USD
                                </span>

                            </strong>

                        </div>


                        <div class="financial-item remaining">

                            <span class="financial-label">
                                Reste à payer
                            </span>

                            <strong class="financial-value">

                                {{ number_format(
                                    $resteAPayer,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                <span class="currency">
                                    USD
                                </span>

                            </strong>

                        </div>

                    </div>


                    <div class="section-title">

                        <i
                            class="fas fa-file-invoice"
                            aria-hidden="true"
                        ></i>

                        Composition du salaire

                    </div>


                    <div class="summary-list">

                        <div class="summary-row">

                            <span class="summary-label">
                                Salaire de base
                            </span>

                            <strong class="summary-value">

                                {{ number_format(
                                    (float) ($salaire->salaire_base ?? 0),
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                USD

                            </strong>

                        </div>


                        <div class="summary-row">

                            <span class="summary-label">
                                Primes
                            </span>

                            <strong class="summary-value text-success">

                                +
                                {{ number_format(
                                    (float) ($salaire->primes ?? 0),
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                USD

                            </strong>

                        </div>


                        <div class="summary-row">

                            <span class="summary-label">
                                Retenues
                            </span>

                            <strong class="summary-value text-danger">

                                −
                                {{ number_format(
                                    (float) ($salaire->retenues ?? 0),
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                USD

                            </strong>

                        </div>


                        <div class="summary-row">

                            <span class="summary-label">
                                Salaire net
                            </span>

                            <strong class="summary-value text-primary">

                                {{ number_format(
                                    $salaireNet,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                USD

                            </strong>

                        </div>

                    </div>


                @else

                    <div class="empty-state">

                        <div class="empty-icon">
                            <i class="fas fa-link-slash"></i>
                        </div>

                        <h3 class="empty-title">
                            Salaire indisponible
                        </h3>

                        <p class="empty-text">
                            Le salaire associé à ce paiement
                            n'est plus accessible.
                        </p>

                    </div>

                @endif

            </div>

        </section>


        {{-- =================================================
             PROGRESSION DU RÈGLEMENT
             ================================================= --}}
        @if($salaire)

            <section class="card">

                <div class="card-header">

                    <div class="card-heading">

                        <span class="card-heading-icon">
                            <i class="fas fa-chart-simple"></i>
                        </span>

                        <div>

                            <h2 class="card-title">
                                Progression du règlement
                            </h2>

                            <p class="card-subtitle">
                                Situation du salaire après ce paiement
                            </p>

                        </div>

                    </div>

                </div>


                <div class="card-body">

                    <div class="payment-progress">

                        <div class="progress-header">

                            <span class="progress-label">
                                Salaire couvert
                            </span>

                            <strong class="progress-percentage">
                                {{ number_format(
                                    $pourcentagePaiement,
                                    1,
                                    ',',
                                    ' '
                                ) }} %
                            </strong>

                        </div>


                        <div class="progress-track">

                            <div
                                class="progress-bar"
                                data-progress="{{ $pourcentagePaiement }}"
                            ></div>

                        </div>


                        <div class="summary-list">

                            <div class="summary-row">

                                <span class="summary-label">
                                    Total payé
                                </span>

                                <strong class="summary-value text-success">

                                    {{ number_format(
                                        $totalPaye,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    USD

                                </strong>

                            </div>


                            <div class="summary-row">

                                <span class="summary-label">
                                    Reste à payer
                                </span>

                                <strong class="summary-value text-warning">

                                    {{ number_format(
                                        $resteAPayer,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    USD

                                </strong>

                            </div>

                        </div>

                    </div>


                    <div class="section-title">

                        <i
                            class="fas fa-right-left"
                            aria-hidden="true"
                        ></i>

                        Impact de ce paiement

                    </div>


                    <div class="settlement-comparison">

                        <div class="settlement-box before">

                            <span class="settlement-label">
                                Avant ce paiement
                            </span>

                            <strong class="settlement-value">

                                {{ number_format(
                                    $resteAvant,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                USD

                            </strong>

                        </div>


                        <div
                            class="settlement-arrow"
                            aria-hidden="true"
                        >
                            <i class="fas fa-arrow-right"></i>
                        </div>


                        <div class="settlement-box after">

                            <span class="settlement-label">
                                Après ce paiement
                            </span>

                            <strong class="settlement-value">

                                {{ number_format(
                                    $resteAPayer,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                USD

                            </strong>

                        </div>

                    </div>

                </div>

            </section>

        @endif


        {{-- =================================================
             NOTES
             ================================================= --}}
        <section class="card">

            <div class="card-header">

                <div class="card-heading">

                    <span class="card-heading-icon">
                        <i class="fas fa-note-sticky"></i>
                    </span>

                    <div>

                        <h2 class="card-title">
                            Observations
                        </h2>

                        <p class="card-subtitle">
                            Informations complémentaires liées au paiement
                        </p>

                    </div>

                </div>

            </div>


            <div class="card-body">

                <div class="notes-box">

                    <div class="notes-label">

                        <i
                            class="fas fa-note-sticky"
                            aria-hidden="true"
                        ></i>

                        Observations

                    </div>


                    @if($paiement->notes)

                        <p class="notes-text">
                            {{ $paiement->notes }}
                        </p>

                    @else

                        <p class="notes-empty">
                            Aucune observation n'a été enregistrée
                            pour ce paiement.
                        </p>

                    @endif

                </div>

            </div>

        </section>


        {{-- =================================================
             INFORMATIONS DE L'OPÉRATION
             ================================================= --}}
        <section class="card">

            <div class="card-header">

                <div class="card-heading">

                    <span class="card-heading-icon">
                        <i class="fas fa-circle-info"></i>
                    </span>

                    <div>

                        <h2 class="card-title">
                            Informations de l'opération
                        </h2>

                        <p class="card-subtitle">
                            Données d'enregistrement du paiement
                        </p>

                    </div>

                </div>

            </div>


            <div class="card-body">

                <div class="payment-details">

                    <div class="detail-item">

                        <span class="detail-label">
                            Référence
                        </span>

                        <strong
                            class="detail-value reference"
                            data-payment-reference
                            data-copy-value="{{ $paiement->reference }}"
                        >
                            {{ $paiement->reference }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Montant
                        </span>

                        <strong class="detail-value">

                            {{ number_format(
                                $montantPaiement,
                                2,
                                ',',
                                ' '
                            ) }}

                            <span class="currency">
                                USD
                            </span>

                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Date du paiement
                        </span>

                        <strong class="detail-value">
                            {{ $datePaiementCourte }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Mode de paiement
                        </span>

                        <strong class="detail-value">

                            <span
                                class="payment-method-display {{ $modeClass }}"
                            >

                                <i
                                    class="fas {{ $modeIcon }}"
                                    aria-hidden="true"
                                ></i>

                                {{ $modeLabel }}

                            </span>

                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Enregistré le
                        </span>

                        <strong class="detail-value">
                            {{ $createdAt }}
                        </strong>

                    </div>


                    <div class="detail-item">

                        <span class="detail-label">
                            Enregistré par
                        </span>

                        <strong class="detail-value">
                            {{ $payeParNom }}
                        </strong>

                    </div>

                </div>

            </div>

        </section>

    </main>


    {{-- =====================================================
         SIDEBAR
         ===================================================== --}}
    <aside class="payment-sidebar">


        {{-- =================================================
             RÉSUMÉ
             ================================================= --}}
        <section class="sidebar-card">

            <h2 class="sidebar-card-title">

                <i class="fas fa-chart-pie"></i>

                Résumé du paiement

            </h2>


            <div class="summary-list">

                <div class="summary-row">

                    <span class="summary-label">
                        Montant
                    </span>

                    <strong class="summary-value text-success">

                        {{ number_format(
                            $montantPaiement,
                            2,
                            ',',
                            ' '
                        ) }}

                        USD

                    </strong>

                </div>


                <div class="summary-row">

                    <span class="summary-label">
                        Mode
                    </span>

                    <strong class="summary-value">
                        {{ $modeLabel }}
                    </strong>

                </div>


                <div class="summary-row">

                    <span class="summary-label">
                        Date
                    </span>

                    <strong class="summary-value">
                        {{ $datePaiementCourte }}
                    </strong>

                </div>


                @if($salaire)

                    <div class="summary-row">

                        <span class="summary-label">
                            Salaire net
                        </span>

                        <strong class="summary-value">

                            {{ number_format(
                                $salaireNet,
                                2,
                                ',',
                                ' '
                            ) }}

                            USD

                        </strong>

                    </div>


                    <div class="summary-row">

                        <span class="summary-label">
                            Reste
                        </span>

                        <strong class="summary-value text-warning">

                            {{ number_format(
                                $resteAPayer,
                                2,
                                ',',
                                ' '
                            ) }}

                            USD

                        </strong>

                    </div>

                @endif

            </div>

        </section>


        {{-- =================================================
             ÉTAT DU RÈGLEMENT
             ================================================= --}}
        @if($salaire)

            <section class="sidebar-card">

                <h2 class="sidebar-card-title">

                    <i class="fas fa-flag-checkered"></i>

                    État du règlement

                </h2>


                <div class="settlement-status {{ $statutNormalise === 'partiellement_paye' ? 'partial' : ($statutNormalise === 'paye' ? 'complete' : '') }}">

                    <div class="settlement-status-content">

                        <span class="settlement-status-label">
                            Situation actuelle
                        </span>


                        <span class="settlement-status-text">

                            @if($statutNormalise === 'paye')

                                Salaire entièrement payé

                            @elseif($statutNormalise === 'partiellement_paye')

                                Salaire partiellement payé

                            @elseif($statutNormalise === 'valide')

                                Salaire en attente de règlement

                            @elseif($statutNormalise === 'annule')

                                Salaire annulé

                            @else

                                Situation :
                                {{ $statusLabel }}

                            @endif

                        </span>

                    </div>


                    <span class="status-badge {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>

                </div>

            </section>

        @endif


        {{-- =================================================
             TRAÇABILITÉ
             ================================================= --}}
        <section class="sidebar-card">

            <h2 class="sidebar-card-title">

                <i class="fas fa-shield-halved"></i>

                Traçabilité

            </h2>


            <div class="trace-badge">

                <i
                    class="fas fa-fingerprint"
                    aria-hidden="true"
                ></i>

                Opération traçable

            </div>


            <div class="audit-list">

                <div class="audit-item">

                    <span class="audit-icon">
                        <i class="fas fa-hashtag"></i>
                    </span>

                    <div class="audit-content">

                        <strong class="audit-title">
                            Référence
                        </strong>

                        <span class="audit-description">
                            {{ $paiement->reference }}
                        </span>

                        <span class="audit-date">
                            Identifiant unique
                        </span>

                    </div>

                </div>


                <div class="audit-item">

                    <span class="audit-icon">
                        <i class="fas fa-calendar-check"></i>
                    </span>

                    <div class="audit-content">

                        <strong class="audit-title">
                            Paiement
                        </strong>

                        <span class="audit-description">
                            Opération effectuée
                        </span>

                        <span class="audit-date">
                            {{ $datePaiementCourte }}
                        </span>

                    </div>

                </div>


                <div class="audit-item">

                    <span class="audit-icon">
                        <i class="fas fa-user-check"></i>
                    </span>

                    <div class="audit-content">

                        <strong class="audit-title">
                            Enregistré par
                        </strong>

                        <span class="audit-description">
                            {{ $payeParNom }}
                        </span>

                        <span class="audit-date">
                            Utilisateur responsable
                        </span>

                    </div>

                </div>


                <div class="audit-item">

                    <span class="audit-icon">
                        <i class="fas fa-clock"></i>
                    </span>

                    <div class="audit-content">

                        <strong class="audit-title">
                            Création
                        </strong>

                        <span class="audit-description">
                            Enregistrement initial
                        </span>

                        <span class="audit-date">
                            {{ $createdAt }}
                        </span>

                    </div>

                </div>

            </div>

        </section>


        {{-- =================================================
             ACTIONS
             ================================================= --}}
        <section class="sidebar-card">

            <h2 class="sidebar-card-title">

                <i class="fas fa-bolt"></i>

                Actions

            </h2>


            <div class="action-list">

                <a
                    href="{{ route('admin.paiements-salaires.recu', $paiement) }}"
                    class="btn btn-primary"
                >
                    <i class="fas fa-receipt"></i>
                    Voir le reçu
                </a>


                <button
                    type="button"
                    class="btn btn-outline"
                    data-print
                    data-print-url="{{ route('admin.paiements-salaires.imprimer', $paiement) }}"
                >
                    <i class="fas fa-print"></i>
                    Imprimer le paiement
                </button>


                @if($salaire)

                    <a
                        href="{{ route('admin.salaires.show', $salaire) }}"
                        class="btn btn-outline"
                    >
                        <i class="fas fa-file-invoice-dollar"></i>
                        Voir le salaire
                    </a>

                @endif


                <div class="action-separator"></div>


                <a
                    href="{{ route('admin.paiements-salaires.index') }}"
                    class="btn btn-secondary"
                    data-back="{{ route('admin.paiements-salaires.index') }}"
                >
                    <i class="fas fa-arrow-left"></i>
                    Retour aux paiements
                </a>

            </div>

        </section>


        {{-- =================================================
             SÉCURITÉ
             ================================================= --}}
        <div class="security-note">

            <div class="security-icon">

                <i
                    class="fas fa-lock"
                    aria-hidden="true"
                ></i>

            </div>


            <div class="security-content">

                <strong class="security-title">
                    Historique protégé
                </strong>


                <p class="security-text">
                    Ce paiement constitue une trace financière.
                    Sa modification directe n'est pas autorisée
                    afin de préserver l'intégrité de l'historique.
                </p>

            </div>

        </div>

    </aside>

</div>


{{-- =========================================================
     PIED DE PAGE
     ========================================================= --}}
<footer class="receipt-footer">

    <div>

        <span class="text-muted">
            Opération :
        </span>

        <strong class="font-mono">
            {{ $paiement->reference }}
        </strong>

    </div>


    <div>

        <span class="trace-badge">

            <i class="fas fa-fingerprint"></i>

            Traçabilité active

        </span>

    </div>

</footer>


</div>

@endsection

@push('scripts') <script
     src="{{ asset('assets/js/admin/paiements-salaires/show.js') }}"
 ></script>
@endpush
