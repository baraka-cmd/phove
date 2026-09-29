@extends('layouts.admin')

@section('title', 'Détail du salaire')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/salaires/show.css') }}">
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

    $progression = $salaireNet > 0
        ? min(
            100,
            max(
                0,
                ($totalPaye / $salaireNet) * 100
            )
        )
        : 0;

    /*
    |--------------------------------------------------------------------------
    | Membre
    |--------------------------------------------------------------------------
    */
    $membre = $salaire->membre;

    $nomMembre = $membre?->nom_complet
        ?? trim(
            ($membre?->nom ?? '')
            . ' '
            . ($membre?->post_nom ?? '')
            . ' '
            . ($membre?->prenom ?? '')
        );

    $nomMembre = $nomMembre !== ''
        ? $nomMembre
        : '—';

    /*
    |--------------------------------------------------------------------------
    | Organe
    |--------------------------------------------------------------------------
    */
    $organe = $salaire->organe;

    $nomOrgane = $organe?->libelle
        ?? $organe?->designation
        ?? $organe?->nom_organe
        ?? (
            $organe
                ? 'Organe #' . $organe->id
                : '—'
        );

    /*
    |--------------------------------------------------------------------------
    | Période
    |--------------------------------------------------------------------------
    */
    try {
        $periode = \Carbon\Carbon::createFromFormat(
            'Y-m',
            $salaire->mois
        )
            ->locale('fr')
            ->translatedFormat('F Y');
    } catch (\Throwable $e) {
        $periode = $salaire->mois ?? '—';
    }

    /*
    |--------------------------------------------------------------------------
    | Statut
    |--------------------------------------------------------------------------
    */
    $statut = $salaire->statut ?? 'brouillon';

    /*
     * Transformation importante :
     *
     * partiellement_paye
     * devient
     * partiellement-paye
     *
     * afin de correspondre aux classes CSS.
     */
    $statutClass = str_replace(
        '_',
        '-',
        $statut
    );

    $statutLabels = [
        'brouillon' => 'Brouillon',
        'valide' => 'Validé',
        'partiellement_paye' => 'Partiellement payé',
        'paye' => 'Payé',
        'annule' => 'Annulé',
    ];

    $statutLabel =
        $statutLabels[$statut]
        ?? ucfirst(
            str_replace(
                '_',
                ' ',
                $statut
            )
        );

    /*
    |--------------------------------------------------------------------------
    | Référence
    |--------------------------------------------------------------------------
    */
    $reference = $salaire->reference
        ?? '—';

    /*
    |--------------------------------------------------------------------------
    | Modes de paiement
    |--------------------------------------------------------------------------
    */
    $modeLabels = [
        'especes' => 'Espèces',
        'virement' => 'Virement bancaire',
        'mobile_money' => 'Mobile Money',
        'cheque' => 'Chèque',
    ];

    /*
    |--------------------------------------------------------------------------
    | Date de création
    |--------------------------------------------------------------------------
    */
    $dateCreation = $salaire->created_at
        ? $salaire->created_at->format('d/m/Y H:i')
        : '—';

    /*
    |--------------------------------------------------------------------------
    | Date de mise à jour
    |--------------------------------------------------------------------------
    */
    $dateModification = $salaire->updated_at
        ? $salaire->updated_at->format('d/m/Y H:i')
        : '—';
@endphp

<div class="salary-show">

    {{-- ======================================================
         EN-TÊTE
    ======================================================= --}}
    <div class="page-header">

        <div class="page-header-content">

            <div>
                <div class="page-breadcrumb">
                    <a href="{{ route('admin.salaires.index') }}">
                        <i class="fas fa-money-check-dollar"></i>
                        Salaires
                    </a>

                    <i class="fas fa-chevron-right"></i>

                    <span>Détail</span>
                </div>

                <h1 class="page-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Détail du salaire
                </h1>

                <p class="page-subtitle">
                    Consultation complète du salaire
                    {{ $reference }}
                </p>
            </div>

            <div class="page-actions">

                <a
                    href="{{ route('admin.salaires.index') }}"
                    class="btn btn-outline"
                >
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>

                <a
                    href="{{ route('admin.salaires.bulletin', $salaire) }}"
                    class="btn btn-secondary"
                >
                    <i class="fas fa-file-invoice"></i>
                    <span>Bulletin</span>
                </a>

                @if($statut === 'brouillon')

                    <a
                        href="{{ route('admin.salaires.edit', $salaire) }}"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-pen"></i>
                        <span>Modifier</span>
                    </a>

                @endif

            </div>

        </div>

    </div>


    {{-- ======================================================
         ALERTES
    ======================================================= --}}

    @if($statut === 'brouillon')

        <div
            class="alert alert-warning"
            role="alert"
        >
            <i class="fas fa-triangle-exclamation"></i>

            <div>
                <strong>Salaire en brouillon</strong>

                <p>
                    Ce salaire n'est pas encore validé.
                    Il peut encore être modifié ou annulé.
                </p>
            </div>
        </div>

    @elseif($statut === 'valide')

        <div
            class="alert alert-info"
            role="alert"
        >
            <i class="fas fa-circle-info"></i>

            <div>
                <strong>Salaire validé</strong>

                <p>
                    Le salaire est validé et peut maintenant
                    faire l'objet d'un paiement.
                </p>
            </div>
        </div>

    @elseif($statut === 'partiellement_paye')

        <div
            class="alert alert-warning"
            role="alert"
        >
            <i class="fas fa-clock"></i>

            <div>
                <strong>Paiement partiel</strong>

                <p>
                    Une partie du salaire a déjà été payée.
                    Le solde reste à régler.
                </p>
            </div>
        </div>

    @elseif($statut === 'paye')

        <div
            class="alert alert-success"
            role="alert"
        >
            <i class="fas fa-circle-check"></i>

            <div>
                <strong>Salaire entièrement payé</strong>

                <p>
                    La totalité du salaire net a été réglée.
                </p>
            </div>
        </div>

    @elseif($statut === 'annule')

        <div
            class="alert alert-danger"
            role="alert"
        >
            <i class="fas fa-ban"></i>

            <div>
                <strong>Salaire annulé</strong>

                <p>
                    Ce salaire ne peut plus faire l'objet
                    d'une modification ou d'un paiement.
                </p>
            </div>
        </div>

    @endif


    {{-- ======================================================
         CONTENU PRINCIPAL
    ======================================================= --}}
    <div class="salary-show-grid">

        {{-- ==================================================
             COLONNE PRINCIPALE
        =================================================== --}}
        <div class="salary-show-main">

            {{-- ==============================================
                 IDENTIFICATION
            =============================================== --}}
            <section class="show-card">

                <div class="show-card-header">

                    <div>
                        <span class="show-card-kicker">
                            Identification
                        </span>

                        <h2 class="show-card-title">
                            Informations du salaire
                        </h2>
                    </div>

                    <span
                        class="status-badge status-{{ $statutClass }}"
                    >
                        <i class="fas fa-circle"></i>
                        {{ $statutLabel }}
                    </span>

                </div>

                <div class="show-card-body">

                    <div class="information-grid">

                        <div class="information-item">

                            <span class="information-label">
                                Référence
                            </span>

                            <div class="information-value reference-value">

                                <span
                                    data-copy
                                    data-copy-value="{{ $reference }}"
                                    class="copy-reference"
                                    tabindex="0"
                                    role="button"
                                    title="Copier la référence"
                                >
                                    {{ $reference }}
                                </span>

                                <i
                                    class="fas fa-copy"
                                    aria-hidden="true"
                                ></i>

                            </div>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Période
                            </span>

                            <span class="information-value">
                                {{ ucfirst($periode) }}
                            </span>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Bénéficiaire
                            </span>

                            <span class="information-value">
                                {{ $nomMembre }}
                            </span>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Matricule
                            </span>

                            <span class="information-value">
                                {{ $membre?->matricule ?? '—' }}
                            </span>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Organe / Service
                            </span>

                            <span class="information-value">
                                {{ $nomOrgane }}
                            </span>

                        </div>


                        <div class="information-item">

                            <span class="information-label">
                                Créé le
                            </span>

                            <span class="information-value">
                                {{ $dateCreation }}
                            </span>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ==============================================
                 COMPOSITION FINANCIÈRE
            =============================================== --}}
            <section class="show-card">

                <div class="show-card-header">

                    <div>
                        <span class="show-card-kicker">
                            Finance
                        </span>

                        <h2 class="show-card-title">
                            Composition du salaire
                        </h2>
                    </div>

                    <i class="fas fa-coins show-card-icon"></i>

                </div>

                <div class="show-card-body">

                    <div class="financial-summary">

                        <div class="financial-row">

                            <div>
                                <span class="financial-label">
                                    Salaire de base
                                </span>
                            </div>

                            <span class="financial-amount">
                                {{ number_format($salaireBase, 2, ',', ' ') }}
                                USD
                            </span>

                        </div>


                        <div class="financial-row financial-row-positive">

                            <div>
                                <span class="financial-label">
                                    Primes
                                </span>
                            </div>

                            <span class="financial-amount positive">
                                +
                                {{ number_format($primes, 2, ',', ' ') }}
                                USD
                            </span>

                        </div>


                        <div class="financial-row financial-row-negative">

                            <div>
                                <span class="financial-label">
                                    Retenues
                                </span>
                            </div>

                            <span class="financial-amount negative">
                                −
                                {{ number_format($retenues, 2, ',', ' ') }}
                                USD
                            </span>

                        </div>


                        <div class="financial-separator"></div>


                        <div class="financial-row financial-row-total">

                            <div>
                                <span class="financial-label">
                                    Salaire net
                                </span>
                            </div>

                            <span class="financial-amount">
                                {{ number_format($salaireNet, 2, ',', ' ') }}
                                USD
                            </span>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ==============================================
                 SUIVI DU PAIEMENT
            =============================================== --}}
            <section class="show-card">

                <div class="show-card-header">

                    <div>
                        <span class="show-card-kicker">
                            Règlement
                        </span>

                        <h2 class="show-card-title">
                            Suivi du paiement
                        </h2>
                    </div>

                    <span class="payment-status status-{{ $statutClass }}">
                        {{ $statutLabel }}
                    </span>

                </div>

                <div class="show-card-body">

                    <div class="payment-overview">

                        <div class="payment-overview-item">

                            <span class="payment-overview-label">
                                Net dû
                            </span>

                            <strong>
                                {{ number_format($salaireNet, 2, ',', ' ') }}
                                USD
                            </strong>

                        </div>


                        <div class="payment-overview-item">

                            <span class="payment-overview-label">
                                Total payé
                            </span>

                            <strong class="positive">
                                {{ number_format($totalPaye, 2, ',', ' ') }}
                                USD
                            </strong>

                        </div>


                        <div class="payment-overview-item">

                            <span class="payment-overview-label">
                                Reste à payer
                            </span>

                            <strong class="{{ $resteAPayer > 0 ? 'warning' : 'positive' }}">
                                {{ number_format($resteAPayer, 2, ',', ' ') }}
                                USD
                            </strong>

                        </div>

                    </div>


                    {{-- Barre de progression --}}
                    <div class="payment-progress">

                        <div class="payment-progress-header">

                            <span>
                                Progression du paiement
                            </span>

                            <strong>
                                {{ number_format($progression, 0) }} %
                            </strong>

                        </div>

                        <div class="payment-progress-track">

                            <div
                                class="payment-progress-bar {{ $progression >= 100 ? 'complete' : ($progression > 0 ? 'partial' : '') }}"
                                data-progress="{{ $progression }}"
                            ></div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ==============================================
                 HISTORIQUE DES PAIEMENTS
            =============================================== --}}
            <section class="show-card">

                <div class="show-card-header">

                    <div>
                        <span class="show-card-kicker">
                            Historique
                        </span>

                        <h2 class="show-card-title">
                            Paiements enregistrés
                        </h2>
                    </div>

                    <span class="payment-count">
                        {{ $paiements->count() }}
                    </span>

                </div>

                <div class="show-card-body">

                    @if($paiements->isNotEmpty())

                        <div
                            class="payment-table-wrapper"
                            data-payment-table
                        >

                            <table class="payment-table">

                                <thead>

                                    <tr>
                                        <th>#</th>
                                        <th>Référence</th>
                                        <th>Date</th>
                                        <th>Mode</th>
                                        <th>Payé par</th>
                                        <th class="text-right">
                                            Montant
                                        </th>
                                        <th class="text-right">
                                            Action
                                        </th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($paiements->sortBy('date_paiement') as $paiement)

                                        @php
                                            $modeLabel =
                                                $modeLabels[$paiement->mode_paiement]
                                                ?? ucfirst(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $paiement->mode_paiement ?? ''
                                                    )
                                                );

                                            $payeur =
                                                $paiement->payePar;

                                            $nomPayeur =
                                                $payeur?->name
                                                ?? $payeur?->nom
                                                ?? '—';
                                        @endphp

                                        <tr
                                            data-payment-row
                                            tabindex="0"
                                        >

                                            <td>
                                                <span class="payment-index">
                                                    {{ $loop->iteration }}
                                                </span>
                                            </td>

                                            <td>
                                                <span
                                                    class="payment-reference"
                                                    data-copy
                                                    data-copy-value="{{ $paiement->reference ?? '' }}"
                                                    tabindex="0"
                                                    role="button"
                                                    title="Copier la référence"
                                                >
                                                    {{ $paiement->reference ?? '—' }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ optional($paiement->date_paiement)->format('d/m/Y') ?? '—' }}
                                            </td>

                                            <td>
                                                <span class="payment-mode">
                                                    {{ $modeLabel }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ $nomPayeur }}
                                            </td>

                                            <td class="text-right">

                                                <strong class="payment-amount">
                                                    {{ number_format((float) $paiement->montant, 2, ',', ' ') }}
                                                    USD
                                                </strong>

                                            </td>

                                            <td class="text-right">

                                                <a
                                                    href="{{ route('admin.paiements-salaires.show', $paiement) }}"
                                                    class="btn btn-sm btn-outline"
                                                    aria-label="Voir le paiement {{ $paiement->reference ?? '' }}"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                                <tfoot>

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="text-right"
                                        >
                                            <strong>
                                                Total payé
                                            </strong>
                                        </td>

                                        <td
                                            class="text-right"
                                        >
                                            <strong class="payment-total">
                                                {{ number_format($totalPaye, 2, ',', ' ') }}
                                                USD
                                            </strong>
                                        </td>

                                        <td></td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                    @else

                        <div
                            class="empty-state"
                            data-empty-state
                        >

                            <div class="empty-state-icon">
                                <i class="fas fa-money-bill-transfer"></i>
                            </div>

                            <h3>
                                Aucun paiement enregistré
                            </h3>

                            <p>
                                Aucun paiement n'a encore été enregistré
                                pour ce salaire.
                            </p>

                        </div>

                    @endif

                </div>

            </section>


            {{-- ==============================================
                 NOTES
            =============================================== --}}
            @if(filled($salaire->notes))

                <section class="show-card">

                    <div class="show-card-header">

                        <div>
                            <span class="show-card-kicker">
                                Observations
                            </span>

                            <h2 class="show-card-title">
                                Notes
                            </h2>
                        </div>

                        <i class="fas fa-note-sticky show-card-icon"></i>

                    </div>

                    <div class="show-card-body">

                        <div class="notes-content">
                            {{ $salaire->notes }}
                        </div>

                    </div>

                </section>

            @endif

        </div>


        {{-- ==================================================
             SIDEBAR
        =================================================== --}}
        <aside class="salary-show-sidebar">


            {{-- ==============================================
                 STATUT
            =============================================== --}}
            <section class="show-card status-card">

                <div class="show-card-header">

                    <div>
                        <span class="show-card-kicker">
                            État
                        </span>

                        <h2 class="show-card-title">
                            Statut
                        </h2>
                    </div>

                </div>

                <div class="show-card-body">

                    <div class="status-display">

                        <span class="status-badge status-{{ $statutClass }}">
                            <i class="fas fa-circle"></i>
                            {{ $statutLabel }}
                        </span>

                    </div>


                    {{-- ======================================
                         ACTIONS
                    ======================================= --}}

                    @if($statut === 'brouillon')

                        <div class="action-list">

                            {{-- Validation --}}
                            <form
                                method="POST"
                                action="{{ route('admin.salaires.valider', $salaire) }}"
                                data-sensitive-action
                                data-confirm-message="Voulez-vous vraiment valider ce salaire ? Après validation, il ne pourra plus être modifié."
                            >
                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="btn btn-success btn-block"
                                >
                                    <i class="fas fa-check"></i>
                                    Valider le salaire
                                </button>

                            </form>


                            {{-- Annulation --}}
                            <button
                                type="button"
                                class="btn btn-danger btn-block"
                                data-modal-open="#cancelSalaryModal"
                            >
                                <i class="fas fa-ban"></i>
                                Annuler le salaire
                            </button>

                        </div>

                    @elseif(
                        $statut === 'valide'
                        || $statut === 'partiellement_paye'
                    )

                        <div class="action-list">

                            <a
                                href="{{ route('admin.salaires.paiements.create', $salaire) }}"
                                class="btn btn-primary btn-block"
                                data-payment-action
                                data-confirm-message="Voulez-vous enregistrer un paiement pour ce salaire ?"
                            >
                                <i class="fas fa-money-bill-wave"></i>
                                Enregistrer un paiement
                            </a>

                        </div>

                    @endif


                    {{-- Bulletin --}}
                    <div class="action-list">

                        <a
                            href="{{ route('admin.salaires.bulletin', $salaire) }}"
                            class="btn btn-outline btn-block"
                            data-print
                        >
                            <i class="fas fa-print"></i>
                            Imprimer le bulletin
                        </a>

                    </div>

                </div>

            </section>


            {{-- ==============================================
                 RÉSUMÉ FINANCIER
            =============================================== --}}
            <section class="show-card">

                <div class="show-card-header">

                    <div>
                        <span class="show-card-kicker">
                            Résumé
                        </span>

                        <h2 class="show-card-title">
                            Situation financière
                        </h2>
                    </div>

                </div>

                <div class="show-card-body">

                    <div class="sidebar-financial-list">

                        <div class="sidebar-financial-item">

                            <span>
                                Salaire brut
                            </span>

                            <strong>
                                {{ number_format($salaireBase + $primes, 2, ',', ' ') }}
                                USD
                            </strong>

                        </div>


                        <div class="sidebar-financial-item">

                            <span>
                                Retenues
                            </span>

                            <strong class="negative">
                                −
                                {{ number_format($retenues, 2, ',', ' ') }}
                                USD
                            </strong>

                        </div>


                        <div class="sidebar-financial-item">

                            <span>
                                Salaire net
                            </span>

                            <strong class="primary">
                                {{ number_format($salaireNet, 2, ',', ' ') }}
                                USD
                            </strong>

                        </div>


                        <div class="sidebar-financial-divider"></div>


                        <div class="sidebar-financial-item">

                            <span>
                                Total payé
                            </span>

                            <strong class="success">
                                {{ number_format($totalPaye, 2, ',', ' ') }}
                                USD
                            </strong>

                        </div>


                        <div class="sidebar-financial-item sidebar-financial-item-total">

                            <span>
                                Reste à payer
                            </span>

                            <strong class="{{ $resteAPayer > 0 ? 'warning' : 'success' }}">
                                {{ number_format($resteAPayer, 2, ',', ' ') }}
                                USD
                            </strong>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ==============================================
                 MÉTADONNÉES
            =============================================== --}}
            <section class="show-card">

                <div class="show-card-header">

                    <div>
                        <span class="show-card-kicker">
                            Système
                        </span>

                        <h2 class="show-card-title">
                            Métadonnées
                        </h2>
                    </div>

                    <i class="fas fa-database show-card-icon"></i>

                </div>

                <div class="show-card-body">

                    <div class="metadata-list">

                        <div class="metadata-item">

                            <span>
                                ID
                            </span>

                            <strong>
                                #{{ $salaire->id }}
                            </strong>

                        </div>


                        <div class="metadata-item">

                            <span>
                                Créé le
                            </span>

                            <strong>
                                {{ $dateCreation }}
                            </strong>

                        </div>


                        <div class="metadata-item">

                            <span>
                                Modifié le
                            </span>

                            <strong>
                                {{ $dateModification }}
                            </strong>

                        </div>

                    </div>

                </div>

            </section>

        </aside>

    </div>


    {{-- ======================================================
         MODALE D'ANNULATION
    ======================================================= --}}
    @if($statut === 'brouillon')

        <div
            id="cancelSalaryModal"
            class="salary-modal"
            data-modal
            hidden
            aria-hidden="true"
            role="dialog"
            aria-modal="true"
            aria-labelledby="cancelSalaryModalTitle"
        >

            <div class="salary-modal-dialog">

                <div class="salary-modal-header">

                    <div>

                        <span class="show-card-kicker">
                            Action sensible
                        </span>

                        <h2
                            id="cancelSalaryModalTitle"
                            class="salary-modal-title"
                        >
                            Annuler le salaire
                        </h2>

                    </div>

                    <button
                        type="button"
                        class="salary-modal-close"
                        data-modal-close
                        aria-label="Fermer"
                    >
                        <i class="fas fa-times"></i>
                    </button>

                </div>


                <div class="salary-modal-body">

                    <div class="modal-warning">

                        <i class="fas fa-triangle-exclamation"></i>

                        <p>
                            Vous êtes sur le point d'annuler
                            définitivement ce salaire.
                        </p>

                    </div>


                    <div class="modal-summary">

                        <div>
                            <span>Référence</span>
                            <strong>
                                {{ $reference }}
                            </strong>
                        </div>

                        <div>
                            <span>Bénéficiaire</span>
                            <strong>
                                {{ $nomMembre }}
                            </strong>
                        </div>

                        <div>
                            <span>Montant net</span>
                            <strong>
                                {{ number_format($salaireNet, 2, ',', ' ') }}
                                USD
                            </strong>
                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('admin.salaires.annuler', $salaire) }}"
                        data-confirm-action
                        data-confirm-message="Confirmez-vous définitivement l'annulation de ce salaire ?"
                    >

                        @csrf
                        @method('PUT')

                        <div class="form-group">

                            <label
                                for="motif_annulation"
                                class="form-label"
                            >
                                Motif de l'annulation
                            </label>

                            <textarea
                                id="motif_annulation"
                                name="motif_annulation"
                                class="form-control"
                                rows="4"
                                maxlength="1000"
                                placeholder="Indiquez le motif de l'annulation..."
                            >{{ old('motif_annulation') }}</textarea>

                        </div>


                        <div class="salary-modal-actions">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-modal-cancel
                            >
                                <i class="fas fa-times"></i>
                                Retour
                            </button>

                            <button
                                type="submit"
                                class="btn btn-danger"
                            >
                                <i class="fas fa-ban"></i>
                                Confirmer l'annulation
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endif

</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin/salaires/show.js') }}"></script>
@endsection
