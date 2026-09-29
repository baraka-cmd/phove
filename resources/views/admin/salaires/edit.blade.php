@extends('layouts.admin')

@section('title', 'Modifier le salaire')

{{-- ============================================================
CSS SPÉCIFIQUE À LA PAGE
============================================================ --}}
@push('styles') <link
     rel="stylesheet"
     href="{{ asset('assets/css/admin/salaires/edit.css') }}"
 >
@endpush

@section('content')

@php
/*
|--------------------------------------------------------------------------
| État du salaire
|--------------------------------------------------------------------------
*/


$statut = (string) ($salaire->statut ?? '');

$estBrouillon = $statut === 'brouillon';

/*
|--------------------------------------------------------------------------
| Membre
|--------------------------------------------------------------------------
*/

$membreActuel = $salaire->membre;

$nomMembre = $membreActuel?->nom_complet
    ?? trim(
        ($membreActuel?->nom ?? '') . ' ' .
        ($membreActuel?->post_nom ?? '') . ' ' .
        ($membreActuel?->prenom ?? '')
    );

$nomMembre = $nomMembre !== ''
    ? $nomMembre
    : 'Membre non renseigné';

/*
|--------------------------------------------------------------------------
| Montants
|--------------------------------------------------------------------------
*/

$salaireBase = (float) ($salaire->salaire_base ?? 0);
$primes = (float) ($salaire->primes ?? 0);
$retenues = (float) ($salaire->retenues ?? 0);

$salaireNet = max(
    $salaireBase + $primes - $retenues,
    0
);

/*
|--------------------------------------------------------------------------
| Paiements
|--------------------------------------------------------------------------
*/

$totalPaye = (float) (
    $salaire->paiements?->sum('montant') ?? 0
);

$resteActuel = max(
    $salaireNet - $totalPaye,
    0
);

$progressionPaiement = $salaireNet > 0
    ? min(
        100,
        ($totalPaye / $salaireNet) * 100
    )
    : 0;

/*
|--------------------------------------------------------------------------
| Statut lisible
|--------------------------------------------------------------------------
*/

$statutLabels = [
    'brouillon' => 'Brouillon',
    'valide' => 'Validé',
    'partiellement_paye' => 'Partiellement payé',
    'paye' => 'Payé',
    'annule' => 'Annulé',
];

$statutIcons = [
    'brouillon' => 'fa-pencil-alt',
    'valide' => 'fa-check',
    'partiellement_paye' => 'fa-hourglass-half',
    'paye' => 'fa-check-double',
    'annule' => 'fa-ban',
];

$statutLabel = $statutLabels[$statut]
    ?? ucfirst(str_replace('_', ' ', $statut));

$statutIcon = $statutIcons[$statut]
    ?? 'fa-info-circle';

/*
|--------------------------------------------------------------------------
| Période
|--------------------------------------------------------------------------
*/

try {
    $periodeSalaire = \Carbon\Carbon::createFromFormat(
        'Y-m',
        $salaire->mois
    )->translatedFormat('F Y');
} catch (\Throwable $e) {
    $periodeSalaire = $salaire->mois ?? '—';
}

/*
|--------------------------------------------------------------------------
| Ancienne saisie / valeurs actuelles
|--------------------------------------------------------------------------
*/

$valeurBase = old(
    'salaire_base',
    $salaireBase
);

$valeurPrimes = old(
    'primes',
    $primes
);

$valeurRetenues = old(
    'retenues',
    $retenues
);

$valeurMois = old(
    'mois',
    $salaire->mois
);

$valeurMembre = old(
    'membre_id',
    $salaire->membre_id
);

$valeurOrgane = old(
    'organe_id',
    $salaire->organe_id
);

$valeurNotes = old(
    'notes',
    $salaire->notes
);


@endphp

<div class="admin-content">


<div
    class="salary-edit"
    data-status="{{ $statut }}"
    data-locked="{{ $estBrouillon ? 'false' : 'true' }}"
>

    {{-- ========================================================
         EN-TÊTE
    ========================================================= --}}
    <header class="page-header">

        <div class="page-header-content">

            <div class="page-breadcrumb">

                <a href="{{ route('admin.salaires.index') }}">
                    Salaires
                </a>

                <i
                    class="fas fa-chevron-right"
                    aria-hidden="true"
                ></i>

                <a href="{{ route('admin.salaires.show', $salaire) }}">
                    {{ $salaire->reference }}
                </a>

                <i
                    class="fas fa-chevron-right"
                    aria-hidden="true"
                ></i>

                <span>Modification</span>

            </div>


            <h1 class="page-title">

                <span class="page-title-icon">
                    <i
                        class="fas fa-edit"
                        aria-hidden="true"
                    ></i>
                </span>

                Modifier le salaire

            </h1>


            <p class="page-subtitle">
                Modifiez les éléments du salaire avant sa validation.
            </p>

        </div>


        <div class="page-header-actions">

            <a
                href="{{ route('admin.salaires.show', $salaire) }}"
                class="btn btn-outline"
            >
                <i
                    class="fas fa-arrow-left"
                    aria-hidden="true"
                ></i>

                Retour au salaire
            </a>

        </div>

    </header>


    {{-- ========================================================
         MESSAGES DE SESSION
    ========================================================= --}}

    @if(session('success'))

        <div
            class="alert alert-success"
            role="status"
        >

            <i
                class="fas fa-check-circle"
                aria-hidden="true"
            ></i>

            <div>
                {{ session('success') }}
            </div>

        </div>

    @endif


    @if(session('error'))

        <div
            class="alert alert-danger"
            role="alert"
        >

            <i
                class="fas fa-exclamation-circle"
                aria-hidden="true"
            ></i>

            <div>
                {{ session('error') }}
            </div>

        </div>

    @endif


    {{-- ========================================================
         ERREURS DE VALIDATION
    ========================================================= --}}

    @if($errors->any())

        <div
            class="alert alert-danger salary-validation-errors"
            role="alert"
        >

            <i
                class="fas fa-exclamation-triangle"
                aria-hidden="true"
            ></i>

            <div>

                <strong>
                    Impossible d'enregistrer les modifications
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- ========================================================
         AVERTISSEMENT — SALAIRE VERROUILLÉ
    ========================================================= --}}

    @if(!$estBrouillon)

        <div
            class="salary-edit-warning"
            role="alert"
        >

            <div class="salary-edit-warning-icon">

                <i
                    class="fas fa-lock"
                    aria-hidden="true"
                ></i>

            </div>


            <div class="salary-edit-warning-content">

                <strong>
                    Modification verrouillée
                </strong>

                <p>
                    Ce salaire n'est plus à l'état de brouillon.
                    Il ne peut donc plus être modifié.
                </p>

            </div>


            <a
                href="{{ route('admin.salaires.show', $salaire) }}"
                class="btn btn-outline btn-sm"
            >
                Voir le détail
            </a>

        </div>

    @endif


    {{-- ========================================================
         LAYOUT PRINCIPAL
    ========================================================= --}}

    <div class="salary-edit-layout">

        {{-- ====================================================
             COLONNE PRINCIPALE
        ===================================================== --}}

        <main class="salary-edit-main">

            <form
                method="POST"
                action="{{ route('admin.salaires.update', $salaire) }}"
                id="salary-edit-form"
                class="salary-form"
                data-salary-edit-form
                novalidate
            >

                @csrf

                @method('PUT')


                {{-- =================================================
                     INFORMATIONS GÉNÉRALES
                ================================================== --}}

                <section class="card salary-form-card">

                    <div class="card-header">

                        <div>

                            <h2 class="card-title">

                                <i
                                    class="fas fa-info-circle"
                                    aria-hidden="true"
                                ></i>

                                Informations générales

                            </h2>

                            <p class="card-subtitle">
                                Identité et période du salaire.
                            </p>

                        </div>


                        <span
                            class="salary-status-badge status-{{ $statut }}"
                            data-salary-status
                        >

                            <i
                                class="fas {{ $statutIcon }}"
                                aria-hidden="true"
                            ></i>

                            {{ $statutLabel }}

                        </span>

                    </div>


                    <div class="card-body">

                        <div class="form-grid">

                            {{-- Référence --}}

                            <div class="form-group">

                                <label class="form-label">
                                    Référence
                                </label>

                                <div class="readonly-field">

                                    <i
                                        class="fas fa-hashtag"
                                        aria-hidden="true"
                                    ></i>

                                    <span>
                                        {{ $salaire->reference }}
                                    </span>

                                </div>

                                <small class="form-help">
                                    La référence est générée automatiquement
                                    et ne peut pas être modifiée.
                                </small>

                            </div>


                            {{-- Mois --}}

                            <div
                                class="form-group"
                                data-field
                            >

                                <label
                                    for="mois"
                                    class="form-label"
                                >
                                    Mois
                                    <span
                                        class="required"
                                        aria-hidden="true"
                                    >*</span>
                                </label>

                                <input
                                    type="month"
                                    id="mois"
                                    name="mois"
                                    class="form-input @error('mois') is-invalid @enderror"
                                    data-month-input
                                    value="{{ $valeurMois }}"
                                    required
                                    aria-required="true"
                                    aria-invalid="{{ $errors->has('mois') ? 'true' : 'false' }}"
                                    @disabled(!$estBrouillon)
                                >

                                @error('mois')

                                    <span
                                        class="form-error"
                                        data-field-error
                                    >
                                        {{ $message }}
                                    </span>

                                @else

                                    <span
                                        class="form-error"
                                        data-field-error
                                    ></span>

                                @enderror

                            </div>


                            {{-- Membre --}}

                            <div
                                class="form-group form-group-full"
                                data-field
                            >

                                <label
                                    for="membre_id"
                                    class="form-label"
                                >
                                    Membre
                                    <span
                                        class="required"
                                        aria-hidden="true"
                                    >*</span>
                                </label>

                                <select
                                    id="membre_id"
                                    name="membre_id"
                                    class="form-select @error('membre_id') is-invalid @enderror"
                                    data-member-select
                                    required
                                    aria-required="true"
                                    aria-invalid="{{ $errors->has('membre_id') ? 'true' : 'false' }}"
                                    @disabled(!$estBrouillon)
                                >

                                    <option value="">
                                        -- Sélectionner un membre --
                                    </option>

                                    @foreach($membres as $membre)

                                        @php
                                            $nomComplet = $membre->nom_complet
                                                ?? trim(
                                                    ($membre->nom ?? '') . ' ' .
                                                    ($membre->post_nom ?? '') . ' ' .
                                                    ($membre->prenom ?? '')
                                                );

                                            $nomComplet = $nomComplet !== ''
                                                ? $nomComplet
                                                : 'Membre sans nom';
                                        @endphp

                                        <option
                                            value="{{ $membre->id }}"
                                            @selected((string) $valeurMembre === (string) $membre->id)
                                        >
                                            {{ $nomComplet }}

                                            @if(!empty($membre->matricule))
                                                — {{ $membre->matricule }}
                                            @endif
                                        </option>

                                    @endforeach

                                </select>

                                @error('membre_id')

                                    <span
                                        class="form-error"
                                        data-field-error
                                    >
                                        {{ $message }}
                                    </span>

                                @else

                                    <span
                                        class="form-error"
                                        data-field-error
                                    ></span>

                                @enderror

                            </div>


                            {{-- Organe --}}

                            <div
                                class="form-group"
                                data-field
                            >

                                <label
                                    for="organe_id"
                                    class="form-label"
                                >
                                    Organe
                                </label>

                                <select
                                    id="organe_id"
                                    name="organe_id"
                                    class="form-select @error('organe_id') is-invalid @enderror"
                                    data-organ-select
                                    aria-invalid="{{ $errors->has('organe_id') ? 'true' : 'false' }}"
                                    @disabled(!$estBrouillon)
                                >

                                    <option value="">
                                        -- Aucun organe --
                                    </option>

                                    @foreach($organes as $organe)

                                        <option
                                            value="{{ $organe->id }}"
                                            @selected((string) $valeurOrgane === (string) $organe->id)
                                        >
                                            {{ $organe->nom }}
                                        </option>

                                    @endforeach

                                </select>

                                @error('organe_id')

                                    <span
                                        class="form-error"
                                        data-field-error
                                    >
                                        {{ $message }}
                                    </span>

                                @else

                                    <span
                                        class="form-error"
                                        data-field-error
                                    ></span>

                                @enderror

                            </div>

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     COMPOSITION DU SALAIRE
                ================================================== --}}

                <section class="card salary-form-card">

                    <div class="card-header">

                        <div>

                            <h2 class="card-title">

                                <i
                                    class="fas fa-calculator"
                                    aria-hidden="true"
                                ></i>

                                Composition du salaire

                            </h2>

                            <p class="card-subtitle">
                                Définissez le salaire de base, les primes
                                et les retenues.
                            </p>

                        </div>

                    </div>


                    <div class="card-body">

                        <div class="salary-amount-grid">

                            {{-- Salaire de base --}}

                            <div
                                class="form-group"
                                data-field
                            >

                                <label
                                    for="salaire_base"
                                    class="form-label"
                                >
                                    Salaire de base
                                    <span
                                        class="required"
                                        aria-hidden="true"
                                    >*</span>
                                </label>

                                <div class="amount-input-wrapper">

                                    <input
                                        type="number"
                                        id="salaire_base"
                                        name="salaire_base"
                                        class="form-input amount-input @error('salaire_base') is-invalid @enderror"
                                        data-salaire-base
                                        data-money-input
                                        value="{{ $valeurBase }}"
                                        min="0"
                                        max="999999999999.99"
                                        step="0.01"
                                        inputmode="decimal"
                                        required
                                        aria-required="true"
                                        aria-invalid="{{ $errors->has('salaire_base') ? 'true' : 'false' }}"
                                        @disabled(!$estBrouillon)
                                    >

                                    <span class="amount-currency">
                                        USD
                                    </span>

                                </div>

                                @error('salaire_base')

                                    <span
                                        class="form-error"
                                        data-field-error
                                    >
                                        {{ $message }}
                                    </span>

                                @else

                                    <span
                                        class="form-error"
                                        data-field-error
                                    ></span>

                                @enderror

                            </div>


                            {{-- Primes --}}

                            <div
                                class="form-group"
                                data-field
                            >

                                <label
                                    for="primes"
                                    class="form-label"
                                >
                                    Primes
                                </label>

                                <div class="amount-input-wrapper">

                                    <input
                                        type="number"
                                        id="primes"
                                        name="primes"
                                        class="form-input amount-input @error('primes') is-invalid @enderror"
                                        data-salaire-primes
                                        data-money-input
                                        value="{{ $valeurPrimes }}"
                                        min="0"
                                        max="999999999999.99"
                                        step="0.01"
                                        inputmode="decimal"
                                        aria-invalid="{{ $errors->has('primes') ? 'true' : 'false' }}"
                                        @disabled(!$estBrouillon)
                                    >

                                    <span class="amount-currency">
                                        USD
                                    </span>

                                </div>

                                @error('primes')

                                    <span
                                        class="form-error"
                                        data-field-error
                                    >
                                        {{ $message }}
                                    </span>

                                @else

                                    <span
                                        class="form-error"
                                        data-field-error
                                    ></span>

                                @enderror

                            </div>


                            {{-- Retenues --}}

                            <div
                                class="form-group"
                                data-field
                            >

                                <label
                                    for="retenues"
                                    class="form-label"
                                >
                                    Retenues
                                </label>

                                <div class="amount-input-wrapper">

                                    <input
                                        type="number"
                                        id="retenues"
                                        name="retenues"
                                        class="form-input amount-input @error('retenues') is-invalid @enderror"
                                        data-salaire-retenues
                                        data-money-input
                                        value="{{ $valeurRetenues }}"
                                        min="0"
                                        max="999999999999.99"
                                        step="0.01"
                                        inputmode="decimal"
                                        aria-invalid="{{ $errors->has('retenues') ? 'true' : 'false' }}"
                                        @disabled(!$estBrouillon)
                                    >

                                    <span class="amount-currency">
                                        USD
                                    </span>

                                </div>

                                @error('retenues')

                                    <span
                                        class="form-error"
                                        data-field-error
                                    >
                                        {{ $message }}
                                    </span>

                                @else

                                    <span
                                        class="form-error"
                                        data-field-error
                                    ></span>

                                @enderror

                            </div>

                        </div>


                        {{-- =================================================
                             CALCUL DU NET
                        ================================================== --}}

                        <div
                            class="salary-calculation"
                            data-calculation-box
                            aria-live="polite"
                            aria-invalid="false"
                        >

                            <div class="calculation-line">

                                <span>
                                    Salaire de base
                                </span>

                                <strong data-summary-base>
                                    {{ number_format(
                                        $salaireBase,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </strong>

                                <span class="calculation-currency">
                                    USD
                                </span>

                            </div>


                            <div class="calculation-line">

                                <span>
                                    + Primes
                                </span>

                                <strong data-summary-primes>
                                    {{ number_format(
                                        $primes,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </strong>

                                <span class="calculation-currency">
                                    USD
                                </span>

                            </div>


                            <div class="calculation-line calculation-deduction">

                                <span>
                                    − Retenues
                                </span>

                                <strong data-summary-retenues>
                                    {{ number_format(
                                        $retenues,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </strong>

                                <span class="calculation-currency">
                                    USD
                                </span>

                            </div>


                            <div class="calculation-separator"></div>


                            <div class="calculation-total">

                                <span>
                                    Salaire net
                                </span>

                                <strong
                                    id="display-net"
                                    data-salaire-net
                                >
                                    {{ number_format(
                                        $salaireNet,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                </strong>

                                <span class="calculation-currency">
                                    USD
                                </span>

                            </div>


                            <div
                                id="net-warning"
                                class="calculation-warning"
                                hidden
                            >

                                <i
                                    class="fas fa-exclamation-triangle"
                                    aria-hidden="true"
                                ></i>

                                <span>
                                    Le salaire net ne peut pas être négatif.
                                </span>

                            </div>

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     PROGRESSION DES PAIEMENTS
                ================================================== --}}

                <section class="card salary-form-card">

                    <div class="card-header">

                        <div>

                            <h2 class="card-title">

                                <i
                                    class="fas fa-chart-line"
                                    aria-hidden="true"
                                ></i>

                                Situation financière

                            </h2>

                            <p class="card-subtitle">
                                État actuel du règlement de ce salaire.
                            </p>

                        </div>

                    </div>


                    <div class="card-body">

                        <div class="payment-progress-summary">

                            <div class="progress-summary-row">

                                <span>
                                    Salaire net
                                </span>

                                <strong>
                                    {{ number_format(
                                        $salaireNet,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                    USD
                                </strong>

                            </div>


                            <div class="progress-summary-row">

                                <span>
                                    Déjà payé
                                </span>

                                <strong>
                                    {{ number_format(
                                        $totalPaye,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                    USD
                                </strong>

                            </div>


                            <div class="progress-summary-row">

                                <span>
                                    Reste à payer
                                </span>

                                <strong>
                                    {{ number_format(
                                        $resteActuel,
                                        2,
                                        ',',
                                        ' '
                                    ) }}
                                    USD
                                </strong>

                            </div>

                        </div>


                        <div class="payment-progress">

                            <div class="payment-progress-header">

                                <span>
                                    Progression du paiement
                                </span>

                                <strong>
                                    {{ number_format(
                                        $progressionPaiement,
                                        1,
                                        ',',
                                        ' '
                                    ) }} %
                                </strong>

                            </div>


                            <div
                                class="progress-track"
                                aria-hidden="true"
                            >

                                <div
                                    class="progress-bar"
                                    data-progress="{{ $progressionPaiement }}"
                                ></div>

                            </div>

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     NOTES
                ================================================== --}}

                <section class="card salary-form-card">

                    <div class="card-header">

                        <div>

                            <h2 class="card-title">

                                <i
                                    class="fas fa-sticky-note"
                                    aria-hidden="true"
                                ></i>

                                Notes

                            </h2>

                            <p class="card-subtitle">
                                Informations complémentaires liées à ce salaire.
                            </p>

                        </div>

                    </div>


                    <div class="card-body">

                        <div
                            class="form-group"
                            data-field
                        >

                            <label
                                for="notes"
                                class="form-label"
                            >
                                Notes / observations
                            </label>

                            <textarea
                                id="notes"
                                name="notes"
                                class="form-input form-textarea @error('notes') is-invalid @enderror"
                                data-notes
                                rows="5"
                                maxlength="2000"
                                placeholder="Ajoutez éventuellement une observation..."
                                aria-describedby="notes-help notes-counter"
                                aria-invalid="{{ $errors->has('notes') ? 'true' : 'false' }}"
                                @disabled(!$estBrouillon)
                            >{{ $valeurNotes }}</textarea>


                            <div class="form-help-row">

                                <small
                                    id="notes-help"
                                    class="form-help"
                                >
                                    Ces informations sont internes à la gestion du salaire.
                                </small>


                                <small
                                    id="notes-counter"
                                    class="character-counter"
                                    data-notes-counter
                                    aria-live="polite"
                                >
                                    0 / 2000
                                </small>

                            </div>


                            @error('notes')

                                <span
                                    class="form-error"
                                    data-field-error
                                >
                                    {{ $message }}
                                </span>

                            @else

                                <span
                                    class="form-error"
                                    data-field-error
                                ></span>

                            @enderror

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     ACTIONS
                ================================================== --}}

                @if($estBrouillon)

                    <div class="salary-form-actions">

                        <a
                            href="{{ route('admin.salaires.show', $salaire) }}"
                            class="btn btn-outline"
                            data-cancel-edit
                        >

                            <i
                                class="fas fa-times"
                                aria-hidden="true"
                            ></i>

                            Annuler

                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="submit-salary"
                            data-submit-salary
                            data-loading-text="Mise à jour..."
                        >

                            <i
                                class="fas fa-save"
                                aria-hidden="true"
                            ></i>

                            <span>
                                Enregistrer les modifications
                            </span>

                        </button>

                    </div>

                @else

                    <div class="salary-form-actions">

                        <a
                            href="{{ route('admin.salaires.show', $salaire) }}"
                            class="btn btn-outline"
                        >

                            <i
                                class="fas fa-arrow-left"
                                aria-hidden="true"
                            ></i>

                            Retour au salaire

                        </a>

                    </div>

                @endif

            </form>

        </main>


        {{-- ====================================================
             COLONNE LATÉRALE
        ===================================================== --}}

        <aside class="salary-edit-sidebar">


            {{-- =================================================
                 RÉSUMÉ
            ================================================== --}}

            <section class="card salary-summary-card">

                <div class="card-header">

                    <h2 class="card-title">

                        <i
                            class="fas fa-file-invoice-dollar"
                            aria-hidden="true"
                        ></i>

                        Résumé

                    </h2>

                </div>


                <div class="card-body">

                    <div class="salary-summary-member">

                        <div class="member-avatar">

                            <i
                                class="fas fa-user"
                                aria-hidden="true"
                            ></i>

                        </div>


                        <div>

                            <strong>
                                {{ $nomMembre }}
                            </strong>

                            @if(!empty($membreActuel?->matricule))

                                <span>
                                    {{ $membreActuel->matricule }}
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="summary-details">

                        <div class="summary-detail-row">

                            <span>
                                Référence
                            </span>

                            <strong>
                                {{ $salaire->reference }}
                            </strong>

                        </div>


                        <div class="summary-detail-row">

                            <span>
                                Période
                            </span>

                            <strong>
                                {{ $periodeSalaire }}
                            </strong>

                        </div>


                        <div class="summary-detail-row">

                            <span>
                                Statut
                            </span>

                            <strong>
                                {{ $statutLabel }}
                            </strong>

                        </div>


                        <div class="summary-detail-row">

                            <span>
                                Créé le
                            </span>

                            <strong>
                                {{ optional($salaire->created_at)->format('d/m/Y H:i') }}
                            </strong>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 RÈGLE DE GESTION
            ================================================== --}}

            <section class="card salary-rule-card">

                <div class="card-body">

                    <div class="rule-icon">

                        <i
                            class="fas fa-shield-alt"
                            aria-hidden="true"
                        ></i>

                    </div>


                    <div class="rule-content">

                        <h3>
                            Règle de gestion
                        </h3>

                        <p>
                            Un salaire peut uniquement être modifié
                            lorsqu'il est encore en
                            <strong>brouillon</strong>.
                        </p>

                        <p>
                            Après validation, les montants sont verrouillés
                            afin de préserver l'intégrité financière.
                        </p>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 PAIEMENTS EXISTANTS
            ================================================== --}}

            <section class="card salary-payment-summary-card">

                <div class="card-header">

                    <h2 class="card-title">

                        <i
                            class="fas fa-money-bill-wave"
                            aria-hidden="true"
                        ></i>

                        Paiements

                    </h2>

                </div>


                <div class="card-body">

                    <div class="payment-summary-item">

                        <span>
                            Salaire net
                        </span>

                        <strong>
                            {{ number_format(
                                $salaireNet,
                                2,
                                ',',
                                ' '
                            ) }}
                            USD
                        </strong>

                    </div>


                    <div class="payment-summary-item">

                        <span>
                            Déjà payé
                        </span>

                        <strong class="payment-paid">

                            {{ number_format(
                                $totalPaye,
                                2,
                                ',',
                                ' '
                            ) }}
                            USD

                        </strong>

                    </div>


                    <div class="payment-summary-item payment-remainder">

                        <span>
                            Reste à payer
                        </span>

                        <strong>

                            {{ number_format(
                                $resteActuel,
                                2,
                                ',',
                                ' '
                            ) }}
                            USD

                        </strong>

                    </div>


                    <div class="payment-sidebar-progress">

                        <div class="payment-sidebar-progress-header">

                            <span>
                                Progression
                            </span>

                            <strong>
                                {{ number_format(
                                    $progressionPaiement,
                                    1,
                                    ',',
                                    ' '
                                ) }} %
                            </strong>

                        </div>


                        <div class="progress-track">

                            <div
                                class="progress-bar"
                                data-progress="{{ $progressionPaiement }}"
                            ></div>

                        </div>

                    </div>

                </div>

            </section>

        </aside>

    </div>

</div>


</div>

@endsection

{{-- ============================================================
JAVASCRIPT SPÉCIFIQUE À LA PAGE
============================================================ --}}
@push('scripts') <script
     src="{{ asset('assets/js/admin/salaires/edit.js') }}"
 ></script>
@endpush
