@extends('layouts.admin')

@section('title', 'Bulletin de salaire')

@section('styles') <link rel="stylesheet" href="{{ asset('assets/css/admin/salaires/bulletin.css') }}">
@endsection

@section('content')

@php
/*
|--------------------------------------------------------------------------
| Données financières
|--------------------------------------------------------------------------
*/

$salaireBase = (float) ($salaire->salaire_base ?? 0);
$primes = (float) ($salaire->primes ?? 0);
$retenues = (float) ($salaire->retenues ?? 0);

$salaireNet = (float) (
    $salaire->salaire_net
    ?? ($salaireBase + $primes - $retenues)
);

/*
|--------------------------------------------------------------------------
| Paiements
|--------------------------------------------------------------------------
*/

$paiements = $salaire->paiements ?? collect();

$totalPaye = (float) $paiements->sum('montant');

$resteAPayer = max(
    $salaireNet - $totalPaye,
    0
);

$pourcentagePaiement = $salaireNet > 0
    ? min(
        max(
            ($totalPaye / $salaireNet) * 100,
            0
        ),
        100
    )
    : 0;

/*
|--------------------------------------------------------------------------
| Membre
|--------------------------------------------------------------------------
*/

$membre = $salaire->membre;

$nomMembre = $membre?->nom_complet;

if (empty($nomMembre)) {
    $nomMembre = trim(
        ($membre->nom ?? '') . ' ' .
        ($membre->post_nom ?? '') . ' ' .
        ($membre->prenom ?? '')
    );
}

$nomMembre = $nomMembre ?: '—';

/*
|--------------------------------------------------------------------------
| Période
|--------------------------------------------------------------------------
*/

try {
    $periode = \Carbon\Carbon::createFromFormat(
        'Y-m',
        $salaire->mois
    )->translatedFormat('F Y');
} catch (\Throwable $e) {
    $periode = $salaire->mois ?? '—';
}

/*
|--------------------------------------------------------------------------
| Statut
|--------------------------------------------------------------------------
*/

$statutLabels = [
    'brouillon' => 'Brouillon',
    'valide' => 'Validé',
    'partiellement_paye' => 'Partiellement payé',
    'paye' => 'Payé',
    'annule' => 'Annulé',
];

$statut = $salaire->statut ?? 'brouillon';

$statutLabel = $statutLabels[$statut]
    ?? ucfirst(
        str_replace('_', ' ', $statut)
    );

/*
|--------------------------------------------------------------------------
| Organe
|--------------------------------------------------------------------------
|
| Le modèle Organe ne possède pas nécessairement une colonne "nom".
| On utilise les champs connus avec fallback.
|
*/

$organe = $salaire->organe;

$nomOrgane = $organe?->libelle
    ?? $organe?->designation
    ?? $organe?->nom_organe
    ?? '—';

/*
|--------------------------------------------------------------------------
| Bulletin
|--------------------------------------------------------------------------
*/

$numeroBulletin = 'BUL-' . ($salaire->reference ?? $salaire->id);

$dateEdition = now()->format('d/m/Y');

/*
|--------------------------------------------------------------------------
| Mode de paiement
|--------------------------------------------------------------------------
*/

$modeLabels = [
    'especes' => 'Espèces',
    'virement' => 'Virement bancaire',
    'mobile_money' => 'Mobile Money',
    'cheque' => 'Chèque',
];


@endphp

<div class="salary-bulletin">

```
<div class="bulletin-page">

    <div class="bulletin-container">


        {{-- =====================================================
             BARRE D'ACTIONS
        ====================================================== --}}

        <div class="bulletin-toolbar no-print">

            <div class="toolbar-left">

                <a
                    href="{{ route('admin.salaires.show', $salaire) }}"
                    class="btn btn-outline"
                    data-back
                >
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour au salaire</span>
                </a>

                <h1 class="toolbar-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Bulletin de salaire
                </h1>

            </div>


            <div class="toolbar-right">

                <button
                    type="button"
                    class="btn btn-primary"
                    data-print
                >
                    <i class="fas fa-print"></i>
                    <span>Imprimer</span>
                </button>

            </div>

        </div>


        {{-- =====================================================
             AVERTISSEMENT BROUILLON
        ====================================================== --}}

        @if($statut === 'brouillon')

            <div class="bulletin-alert bulletin-alert-warning no-print">

                <i class="fas fa-exclamation-triangle"></i>

                <div>
                    <strong>Bulletin non validé</strong>

                    <div>
                        Ce bulletin correspond à un salaire encore
                        en brouillon. Il ne constitue pas encore
                        un document financier définitif.
                    </div>
                </div>

            </div>

        @endif


        {{-- =====================================================
             AVERTISSEMENT ANNULATION
        ====================================================== --}}

        @if($statut === 'annule')

            <div class="bulletin-alert bulletin-alert-danger no-print">

                <i class="fas fa-ban"></i>

                <div>
                    <strong>Salaire annulé</strong>

                    <div>
                        Ce bulletin correspond à une opération
                        qui a été annulée.
                    </div>
                </div>

            </div>

        @endif


        {{-- =====================================================
             FEUILLE DU BULLETIN
        ====================================================== --}}

        <article
            class="bulletin-paper"
            id="salary-bulletin"
            data-bulletin-document
        >


            {{-- =================================================
                 FILIGRANE ANNULÉ
            ================================================== --}}

            @if($statut === 'annule')

                <div
                    class="cancelled-watermark"
                    aria-hidden="true"
                >
                    ANNULÉ
                </div>

            @elseif($statut === 'brouillon')

                <div
                    class="draft-watermark"
                    aria-hidden="true"
                >
                    BROUILLON
                </div>

            @endif


            {{-- =================================================
                 EN-TÊTE ORGANISATION
            ================================================== --}}

            <header class="organization-header">

                <div class="organization-identity">

                    <div class="organization-logo">

                        @if(
                            isset($settings)
                            && !empty($settings->logo)
                        )

                            <img
                                src="{{ asset('storage/' . $settings->logo) }}"
                                alt="Logo PHOVE"
                            >

                        @else

                            <i
                                class="fas fa-building"
                                aria-hidden="true"
                            ></i>

                        @endif

                    </div>


                    <div class="organization-info">

                        <h2 class="organization-name">
                            PHOVE
                        </h2>

                        <p class="organization-description">
                            Organisation humanitaire
                            et gestion administrative
                        </p>

                        <div class="organization-contact">

                            <div class="organization-contact-item">
                                <i class="fas fa-building"></i>
                                <span>
                                    Gestion administrative et financière
                                </span>
                            </div>

                        </div>

                    </div>

                </div>


                <div class="document-meta">

                    <p class="document-type">
                        Bulletin de salaire
                    </p>

                    <p class="document-number">
                        {{ $numeroBulletin }}
                    </p>

                    <p class="document-date">
                        Édité le {{ $dateEdition }}
                    </p>

                </div>

            </header>


            {{-- =================================================
                 TITRE
            ================================================== --}}

            <div class="bulletin-heading">

                <h1 class="bulletin-title">
                    Bulletin de salaire
                </h1>

                <p class="bulletin-period">
                    Période :
                    <strong>
                        {{ ucfirst($periode) }}
                    </strong>
                </p>

            </div>


            {{-- =================================================
                 IDENTIFICATION DU SALARIÉ
            ================================================== --}}

            <section class="employee-section">

                <h2 class="section-title">

                    <i class="fas fa-user"></i>

                    Identification du bénéficiaire

                </h2>


                <div class="employee-grid">

                    <div class="employee-item">

                        <span class="employee-label">
                            Bénéficiaire
                        </span>

                        <strong class="employee-value">
                            {{ $nomMembre }}
                        </strong>

                    </div>


                    <div class="employee-item">

                        <span class="employee-label">
                            Matricule
                        </span>

                        <strong class="employee-value">
                            {{ $membre?->matricule ?? '—' }}
                        </strong>

                    </div>


                    <div class="employee-item">

                        <span class="employee-label">
                            Organe / Service
                        </span>

                        <strong class="employee-value">
                            {{ $nomOrgane }}
                        </strong>

                    </div>


                    <div class="employee-item">

                        <span class="employee-label">
                            Référence
                        </span>

                        <strong class="employee-value">
                            <span data-copy-value="{{ $salaire->reference ?? '' }}">
                                {{ $salaire->reference ?? '—' }}
                            </span>

                            @if(!empty($salaire->reference))

                                <button
                                    type="button"
                                    class="btn btn-outline btn-sm no-print"
                                    data-copy
                                    data-copy-value="{{ $salaire->reference }}"
                                    aria-label="Copier la référence du salaire"
                                >
                                    <i class="fas fa-copy"></i>
                                    Copier
                                </button>

                            @endif

                        </strong>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 COMPOSITION DU SALAIRE
            ================================================== --}}

            <section class="financial-section">

                <h2 class="section-title">

                    <i class="fas fa-money-check-alt"></i>

                    Composition du salaire

                </h2>


                <div class="financial-table-wrapper">

                    <table class="financial-table">

                        <thead>

                            <tr>

                                <th>
                                    Désignation
                                </th>

                                <th class="amount-column">
                                    Montant
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            {{-- Salaire de base --}}

                            <tr>

                                <td class="description">
                                    Salaire de base
                                </td>

                                <td class="amount">

                                    {{ number_format(
                                        $salaireBase,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    USD

                                </td>

                            </tr>


                            {{-- Primes --}}

                            <tr>

                                <td class="description">
                                    Primes et compléments
                                </td>

                                <td class="amount positive">

                                    +

                                    {{ number_format(
                                        $primes,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    USD

                                </td>

                            </tr>


                            {{-- Retenues --}}

                            <tr>

                                <td class="description">
                                    Retenues
                                </td>

                                <td class="amount negative">

                                    −

                                    {{ number_format(
                                        $retenues,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    USD

                                </td>

                            </tr>

                        </tbody>


                        <tfoot>

                            <tr class="financial-total-row">

                                <td class="financial-total-label">
                                    Salaire net
                                </td>

                                <td class="amount financial-total-value">

                                    {{ number_format(
                                        $salaireNet,
                                        2,
                                        ',',
                                        ' '
                                    ) }}

                                    USD

                                </td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </section>


            {{-- =================================================
                 MONTANT NET
            ================================================== --}}

            <div class="net-salary-box">

                <div class="net-salary-label">

                    <i class="fas fa-wallet"></i>

                    <span>
                        Salaire net à payer
                    </span>

                </div>

                <strong class="net-salary-value">

                    {{ number_format(
                        $salaireNet,
                        2,
                        ',',
                        ' '
                    ) }}

                    USD

                </strong>

            </div>


            {{-- =================================================
                 SITUATION DU PAIEMENT
            ================================================== --}}

            <section class="payment-status-section">

                <div class="payment-status-header">

                    <h2 class="payment-status-title">
                        Situation du paiement
                    </h2>


                    <span
                        class="status-badge status-{{ $statut }}"
                    >

                        <i class="fas fa-circle"></i>

                        {{ $statutLabel }}

                    </span>

                </div>


                <div class="payment-progress">

                    <div class="payment-progress-header">

                        <span class="payment-progress-label">
                            Progression du paiement
                        </span>

                        <strong class="payment-progress-percentage">
                            {{ number_format($pourcentagePaiement, 0) }}%
                        </strong>

                    </div>


                    <div class="payment-progress-track">

                        <div
                            class="payment-progress-bar
                            @if($pourcentagePaiement >= 100)
                                complete
                            @elseif($pourcentagePaiement > 0)
                                partial
                            @endif"
                            data-progress="{{ $pourcentagePaiement }}"
                        ></div>

                    </div>


                    <div class="settlement-grid">

                        <div class="settlement-item">

                            <span class="settlement-label">
                                Salaire net dû
                            </span>

                            <strong class="settlement-value">

                                {{ number_format(
                                    $salaireNet,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                USD

                            </strong>

                        </div>


                        <div class="settlement-item">

                            <span class="settlement-label">
                                Total payé
                            </span>

                            <strong class="settlement-value success">

                                {{ number_format(
                                    $totalPaye,
                                    2,
                                    ',',
                                    ' '
                                ) }}

                                USD

                            </strong>

                        </div>


                        <div class="settlement-item">

                            <span class="settlement-label">
                                Reste à payer
                            </span>

                            <strong
                                class="settlement-value
                                {{ $resteAPayer > 0
                                    ? 'warning'
                                    : 'success' }}"
                            >

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


            {{-- =================================================
                 HISTORIQUE DES PAIEMENTS
            ================================================== --}}

            @if($paiements->isNotEmpty())

                <section class="payment-history">

                    <h2 class="section-title">

                        <i class="fas fa-history"></i>

                        Historique des paiements

                    </h2>


                    <div class="history-table-wrapper">

                        <table class="history-table">

                            <thead>

                                <tr>

                                    <th>
                                        Référence
                                    </th>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Mode
                                    </th>

                                    <th>
                                        Payé par
                                    </th>

                                    <th>
                                        Montant
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach(
                                    $paiements->sortBy('date_paiement')
                                    as $paiement
                                )

                                    @php

                                        $modeLabel =
                                            $modeLabels[
                                                $paiement->mode_paiement
                                            ]
                                            ?? ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $paiement->mode_paiement
                                                )
                                            );

                                        $nomPayeur =
                                            $paiement->payePar?->name
                                            ?? $paiement->payePar?->nom
                                            ?? '—';

                                    @endphp


                                    <tr
                                        data-payment-row
                                        data-reference="{{ $paiement->reference }}"
                                    >

                                        <td>

                                            <strong class="history-reference">
                                                {{ $paiement->reference }}
                                            </strong>

                                        </td>


                                        <td>

                                            @if($paiement->date_paiement)

                                                {{ optional(
                                                    $paiement->date_paiement
                                                )->format('d/m/Y') }}

                                            @else

                                                —

                                            @endif

                                        </td>


                                        <td class="history-mode">
                                            {{ $modeLabel }}
                                        </td>


                                        <td>
                                            {{ $nomPayeur }}
                                        </td>


                                        <td class="history-amount">

                                            {{ number_format(
                                                (float) $paiement->montant,
                                                2,
                                                ',',
                                                ' '
                                            ) }}

                                            USD

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>


                            <tfoot>

                                <tr>

                                    <td
                                        colspan="4"
                                        class="financial-total-label"
                                    >
                                        Total payé
                                    </td>

                                    <td class="history-amount">

                                        <strong>

                                            {{ number_format(
                                                $totalPaye,
                                                2,
                                                ',',
                                                ' '
                                            ) }}

                                            USD

                                        </strong>

                                    </td>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                </section>

            @endif


            {{-- =================================================
                 OBSERVATIONS
            ================================================== --}}

            @if(!empty($salaire->notes))

                <section class="notes-section">

                    <h2 class="notes-title">

                        <i class="fas fa-sticky-note"></i>

                        Observations

                    </h2>

                    <p class="notes-content">
                        {{ $salaire->notes }}
                    </p>

                </section>

            @endif


            {{-- =================================================
                 ANNULATION
            ================================================== --}}

            @if(
                $statut === 'annule'
                && !empty($salaire->motif_annulation)
            )

                <section class="cancellation-section">

                    <h2 class="cancellation-title">

                        <i class="fas fa-ban"></i>

                        Annulation

                    </h2>

                    <p class="cancellation-content">
                        {{ $salaire->motif_annulation }}
                    </p>


                    @if($salaire->annule_at)

                        <p class="cancellation-content">

                            Annulé le

                            <strong>
                                {{ optional(
                                    $salaire->annule_at
                                )->format('d/m/Y H:i') }}
                            </strong>

                            @if($salaire->annulePar)

                                par

                                <strong>
                                    {{ $salaire->annulePar?->name
                                        ?? $salaire->annulePar?->nom
                                        ?? 'Utilisateur' }}
                                </strong>

                            @endif

                        </p>

                    @endif

                </section>

            @endif


            {{-- =================================================
                 SIGNATURES
            ================================================== --}}

            <section class="signature-section">

                <div class="signature-grid">


                    <div class="signature-box">

                        <p class="signature-role">
                            Responsable financier
                        </p>

                        <div class="signature-line"></div>

                        <p class="signature-name">
                            Nom, signature et cachet
                        </p>

                    </div>


                    <div class="signature-box">

                        <p class="signature-role">
                            Bénéficiaire
                        </p>

                        <div class="signature-line"></div>

                        <p class="signature-name">
                            Signature précédée de la mention
                            « Lu et approuvé »
                        </p>

                    </div>


                </div>

            </section>


            {{-- =================================================
                 PIED DU BULLETIN
            ================================================== --}}

            <footer class="bulletin-footer">

                <div class="footer-left">

                    <strong>
                        {{ $numeroBulletin }}
                    </strong>

                    <div>
                        Document généré par le système
                        de gestion PHOVE.
                    </div>

                    <div class="footer-security">
                        Document administratif et financier.
                    </div>

                </div>


                <div class="footer-right">

                    <div>
                        Édité le {{ $dateEdition }}
                    </div>

                    <div>
                        {{ $nombrePaiements = $paiements->count() }}
                        paiement(s) enregistré(s)
                    </div>

                </div>

            </footer>


        </article>


        {{-- =====================================================
             CONFIDENTIALITÉ
        ====================================================== --}}

        <div class="bulletin-alert bulletin-alert-info no-print">

            <i class="fas fa-lock"></i>

            <span>
                Document confidentiel — réservé à l'usage
                administratif et financier.
            </span>

        </div>


    </div>

</div>
```

</div>

@endsection

@section('scripts') <script src="{{ asset('assets/js/admin/salaires/bulletin.js') }}"></script>
@endsection
