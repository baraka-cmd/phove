@extends('layouts.admin')

@section('title', 'Réservation ' . $reservation->numero)

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/reservations-show.css') }}"
    >
@endsection

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Normalisation du statut
    |--------------------------------------------------------------------------
    */

    $statutValue = $reservation->statut instanceof \App\Enums\StatutReservation
        ? $reservation->statut->value
        : (string) $reservation->statut;

    $statutLabel = $reservation->statut instanceof \App\Enums\StatutReservation
        ? $reservation->statut->libelle()
        : ucfirst(str_replace('_', ' ', $statutValue));

    $statutClass = match ($statutValue) {
        'brouillon'  => 'is-draft',
        'confirmee'  => 'is-confirmed',
        'partielle'  => 'is-partial',
        'consommee'  => 'is-consumed',
        'expiree'    => 'is-expired',
        'annulee'    => 'is-cancelled',
        default      => 'is-default',
    };

    /*
    |--------------------------------------------------------------------------
    | Résumé chiffré (fourni par le service)
    |--------------------------------------------------------------------------
    */

    $totalDemande    = (float) ($resume['quantite_demandee'] ?? 0);
    $totalReserve    = (float) ($resume['quantite_reservee'] ?? 0);
    $totalConsomme   = (float) ($resume['quantite_consommee'] ?? 0);
    $resteReservation = (float) ($resume['reste_reserve'] ?? 0);

    $tauxReservation = $totalDemande > 0
        ? min(100, ($totalReserve / $totalDemande) * 100)
        : 0;

    $tauxConsommation = $totalReserve > 0
        ? min(100, ($totalConsomme / $totalReserve) * 100)
        : 0;

    /*
    |--------------------------------------------------------------------------
    | Expiration
    |--------------------------------------------------------------------------
    */

    $dateExpiration = $reservation->date_expiration
        ? \Carbon\Carbon::parse($reservation->date_expiration)
        : null;

    $estExpiree =
        $dateExpiration
        && $dateExpiration->isPast()
        && !in_array($statutValue, ['consommee', 'annulee', 'expiree'], true);

    /*
    |--------------------------------------------------------------------------
    | Lignes
    |--------------------------------------------------------------------------
    */

    $lignes = $reservation->lignes;
@endphp


<div class="stock-reservation-show">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <header class="stock-reservation-show__header">

        <div class="stock-reservation-show__header-main">

            <nav
                class="stock-reservation-show__breadcrumb"
                aria-label="Fil d’Ariane"
            >

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span aria-hidden="true">/</span>

                <a href="{{ route('admin.stocks.reservations.index') }}">
                    Réservations
                </a>

                <span aria-hidden="true">/</span>

                <span>{{ $reservation->numero }}</span>

            </nav>


            <div class="stock-reservation-show__title-row">

                <div>

                    <h1 class="stock-reservation-show__title">
                        Réservation {{ $reservation->numero }}
                    </h1>

                    <p class="stock-reservation-show__subtitle">
                        Détail de la réservation et suivi des quantités
                        réservées.
                    </p>

                </div>


                <span class="stock-reservation-show__status {{ $statutClass }}">

                    <span
                        class="stock-reservation-show__status-dot"
                        aria-hidden="true"
                    ></span>

                    {{ $statutLabel }}

                </span>

            </div>

        </div>


        <div class="stock-reservation-show__header-actions">

            <a
                href="{{ route('admin.stocks.reservations.index') }}"
                class="stock-reservation-show__button stock-reservation-show__button--secondary"
            >

                <i class="fas fa-arrow-left" aria-hidden="true"></i>

                Retour

            </a>

        </div>

    </header>


    {{-- =========================================================
         ALERTES
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-reservation-show__alert stock-reservation-show__alert--success" role="alert">

            <div class="stock-reservation-show__alert-icon">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
            </div>

            <div>

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-reservation-show__alert stock-reservation-show__alert--danger" role="alert">

            <div class="stock-reservation-show__alert-icon">
                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            </div>

            <div>

                <strong>Opération impossible</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    @if($estExpiree)

        <div class="stock-reservation-show__alert stock-reservation-show__alert--warning" role="alert">

            <div class="stock-reservation-show__alert-icon">
                <i class="fas fa-clock" aria-hidden="true"></i>
            </div>

            <div>

                <strong>Réservation arrivée à échéance</strong>

                <p>
                    La date d’expiration de cette réservation
                    est dépassée.
                </p>

            </div>

        </div>

    @endif


    {{-- =========================================================
         RÉSUMÉ
    ========================================================== --}}
    <section
        class="stock-reservation-show__summary"
        aria-label="Résumé de la réservation"
    >

        {{-- ARTICLES --}}
        <article class="stock-reservation-show__summary-card">

            <div class="stock-reservation-show__summary-icon">
                <i class="fas fa-list-ol" aria-hidden="true"></i>
            </div>

            <div class="stock-reservation-show__summary-content">

                <span class="stock-reservation-show__summary-label">
                    Articles
                </span>

                <strong class="stock-reservation-show__summary-value">
                    {{ $resume['nombre_lignes'] ?? $lignes->count() }}
                </strong>

            </div>

        </article>


        {{-- DEMANDÉE --}}
        <article class="stock-reservation-show__summary-card">

            <div class="stock-reservation-show__summary-icon">
                <i class="fas fa-boxes" aria-hidden="true"></i>
            </div>

            <div class="stock-reservation-show__summary-content">

                <span class="stock-reservation-show__summary-label">
                    Quantité demandée
                </span>

                <strong class="stock-reservation-show__summary-value">
                    {{ number_format($totalDemande, 2, ',', ' ') }}
                </strong>

            </div>

        </article>


        {{-- RÉSERVÉE --}}
        <article class="stock-reservation-show__summary-card">

            <div class="stock-reservation-show__summary-icon">
                <i class="fas fa-lock" aria-hidden="true"></i>
            </div>

            <div class="stock-reservation-show__summary-content">

                <span class="stock-reservation-show__summary-label">
                    Quantité réservée
                </span>

                <strong class="stock-reservation-show__summary-value">
                    {{ number_format($totalReserve, 2, ',', ' ') }}
                </strong>

            </div>

        </article>


        {{-- CONSOMMÉE --}}
        <article class="stock-reservation-show__summary-card">

            <div class="stock-reservation-show__summary-icon">
                <i class="fas fa-check-double" aria-hidden="true"></i>
            </div>

            <div class="stock-reservation-show__summary-content">

                <span class="stock-reservation-show__summary-label">
                    Quantité consommée
                </span>

                <strong class="stock-reservation-show__summary-value">
                    {{ number_format($totalConsomme, 2, ',', ' ') }}
                </strong>

            </div>

        </article>


        {{-- RESTE --}}
        <article class="stock-reservation-show__summary-card">

            <div class="stock-reservation-show__summary-icon">
                <i class="fas fa-hourglass-half" aria-hidden="true"></i>
            </div>

            <div class="stock-reservation-show__summary-content">

                <span class="stock-reservation-show__summary-label">
                    Reste réservé
                </span>

                <strong class="stock-reservation-show__summary-value">
                    {{ number_format($resteReservation, 2, ',', ' ') }}
                </strong>

            </div>

        </article>

    </section>


    {{-- =========================================================
         INFORMATIONS GÉNÉRALES
    ========================================================== --}}
    <section class="stock-reservation-show__card">

        <div class="stock-reservation-show__card-header">

            <div>

                <h2 class="stock-reservation-show__card-title">
                    Informations générales
                </h2>

                <p class="stock-reservation-show__card-description">
                    Informations administratives relatives
                    à la réservation.
                </p>

            </div>

            <span class="stock-reservation-show__section-icon">
                <i class="fas fa-info-circle" aria-hidden="true"></i>
            </span>

        </div>


        <div class="stock-reservation-show__card-body">

            <div class="stock-reservation-show__info-grid">

                <div class="stock-reservation-show__info-item">

                    <span class="stock-reservation-show__info-label">
                        Numéro
                    </span>

                    <strong class="stock-reservation-show__info-value">
                        {{ $reservation->numero }}
                    </strong>

                </div>


                <div class="stock-reservation-show__info-item">

                    <span class="stock-reservation-show__info-label">
                        Date de réservation
                    </span>

                    <strong class="stock-reservation-show__info-value">

                        {{ $reservation->date_reservation
                            ? \Carbon\Carbon::parse($reservation->date_reservation)->format('d/m/Y')
                            : '—'
                        }}

                    </strong>

                </div>


                <div class="stock-reservation-show__info-item">

                    <span class="stock-reservation-show__info-label">
                        Date d'expiration
                    </span>

                    <strong class="stock-reservation-show__info-value
                        {{ $estExpiree ? 'stock-reservation-show__info-value--danger' : '' }}">

                        {{ $dateExpiration
                            ? $dateExpiration->format('d/m/Y')
                            : 'Aucune'
                        }}

                    </strong>

                </div>


                <div class="stock-reservation-show__info-item">

                    <span class="stock-reservation-show__info-label">
                        Demandeur
                    </span>

                    <strong class="stock-reservation-show__info-value">
                        {{ $reservation->demandeur?->nom ?? '—' }}
                    </strong>

                </div>


                <div class="stock-reservation-show__info-item">

                    <span class="stock-reservation-show__info-label">
                        Projet
                    </span>

                    <strong class="stock-reservation-show__info-value">

                        @if($reservation->projet)

                            {{ $reservation->projet->titre
                                ?? ('Projet #' . $reservation->projet->id) }}

                        @else

                            <span class="stock-reservation-show__muted">
                                Aucun projet associé
                            </span>

                        @endif

                    </strong>

                </div>


                <div class="stock-reservation-show__info-item">

                    <span class="stock-reservation-show__info-label">
                        Destinataire
                    </span>

                    <strong class="stock-reservation-show__info-value">
                        {{ $reservation->destinataire ?: '—' }}
                    </strong>

                </div>


                <div class="stock-reservation-show__info-item stock-reservation-show__info-item--full">

                    <span class="stock-reservation-show__info-label">
                        Motif
                    </span>

                    <p class="stock-reservation-show__info-text">
                        {{ $reservation->motif ?: 'Aucun motif renseigné.' }}
                    </p>

                </div>


                <div class="stock-reservation-show__info-item stock-reservation-show__info-item--full">

                    <span class="stock-reservation-show__info-label">
                        Notes
                    </span>

                    <p class="stock-reservation-show__info-text">
                        {{ $reservation->notes ?: 'Aucune note.' }}
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         PROGRESSION
    ========================================================== --}}
    <section class="stock-reservation-show__card">

        <div class="stock-reservation-show__card-header">

            <div>

                <h2 class="stock-reservation-show__card-title">
                    Suivi de la réservation
                </h2>

                <p class="stock-reservation-show__card-description">
                    État des quantités demandées, réservées
                    et consommées.
                </p>

            </div>

        </div>


        <div class="stock-reservation-show__card-body">

            <div class="stock-reservation-show__progress-group">

                <div class="stock-reservation-show__progress-header">

                    <span>Réservation effectuée</span>

                    <strong>
                        {{ number_format($tauxReservation, 0) }} %
                    </strong>

                </div>

                <div
                    class="stock-reservation-show__progress"
                    role="progressbar"
                    aria-valuenow="{{ round($tauxReservation) }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-label="Progression de la réservation"
                >

                    <span
                        class="stock-reservation-show__progress-bar stock-reservation-show__progress-bar--reservation"
                        style="width: {{ $tauxReservation }}%;"
                    ></span>

                </div>

                <p class="stock-reservation-show__progress-help">

                    {{ number_format($totalReserve, 2, ',', ' ') }}
                    réservés sur
                    {{ number_format($totalDemande, 2, ',', ' ') }}
                    demandés.

                </p>

            </div>


            <div class="stock-reservation-show__progress-group">

                <div class="stock-reservation-show__progress-header">

                    <span>Consommation</span>

                    <strong>
                        {{ number_format($tauxConsommation, 0) }} %
                    </strong>

                </div>

                <div
                    class="stock-reservation-show__progress"
                    role="progressbar"
                    aria-valuenow="{{ round($tauxConsommation) }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-label="Progression de la consommation"
                >

                    <span
                        class="stock-reservation-show__progress-bar stock-reservation-show__progress-bar--consumption"
                        style="width: {{ $tauxConsommation }}%;"
                    ></span>

                </div>

                <p class="stock-reservation-show__progress-help">

                    {{ number_format($totalConsomme, 2, ',', ' ') }}
                    consommés sur
                    {{ number_format($totalReserve, 2, ',', ' ') }}
                    réservés.

                </p>

            </div>

        </div>

    </section>


    {{-- =========================================================
         LIGNES DE LA RÉSERVATION
    ========================================================== --}}
    <section class="stock-reservation-show__card">

        <div class="stock-reservation-show__card-header">

            <div>

                <h2 class="stock-reservation-show__card-title">
                    Articles réservés
                </h2>

                <p class="stock-reservation-show__card-description">
                    Détail des quantités demandées, réservées
                    et consommées pour chaque article.
                </p>

            </div>

            <span class="stock-reservation-show__count">

                {{ $lignes->count() }}
                {{ $lignes->count() > 1 ? 'articles' : 'article' }}

            </span>

        </div>


        <div class="stock-reservation-show__table-wrapper">

            @if($lignes->isNotEmpty())

                <table class="stock-reservation-show__table">

                    <thead>

                        <tr>

                            <th scope="col">Article</th>

                            <th scope="col" class="stock-reservation-show__table-number">
                                Demandée
                            </th>

                            <th scope="col" class="stock-reservation-show__table-number">
                                Réservée
                            </th>

                            <th scope="col" class="stock-reservation-show__table-number">
                                Consommée
                            </th>

                            <th scope="col" class="stock-reservation-show__table-number">
                                Reste
                            </th>

                            <th scope="col">Unité</th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($lignes as $ligne)

                            @php
                                $demande    = (float) $ligne->quantite_demandee;
                                $reserve    = (float) $ligne->quantite_reservee;
                                $consomme   = (float) $ligne->quantite_consommee;

                                $resteLigne = max(0, $reserve - $consomme);

                                $stock = $ligne->stock;
                            @endphp

                            <tr>

                                <td>

                                    <div class="stock-reservation-show__article">

                                        <div class="stock-reservation-show__article-icon">
                                            <i class="fas fa-box" aria-hidden="true"></i>
                                        </div>

                                        <div class="stock-reservation-show__article-content">

                                            <strong>
                                                {{ $stock?->designation ?? 'Article supprimé' }}
                                            </strong>

                                            @if($stock?->reference)

                                                <span>
                                                    Réf. {{ $stock->reference }}
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                <td class="stock-reservation-show__table-number">
                                    {{ number_format($demande, 2, ',', ' ') }}
                                </td>


                                <td class="stock-reservation-show__table-number">
                                    <strong class="stock-reservation-show__quantity--reserved">
                                        {{ number_format($reserve, 2, ',', ' ') }}
                                    </strong>
                                </td>


                                <td class="stock-reservation-show__table-number">
                                    {{ number_format($consomme, 2, ',', ' ') }}
                                </td>


                                <td class="stock-reservation-show__table-number">
                                    <strong>
                                        {{ number_format($resteLigne, 2, ',', ' ') }}
                                    </strong>
                                </td>


                                <td>
                                    <span class="stock-reservation-show__unit">
                                        {{ $ligne->unite ?: '—' }}
                                    </span>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>


                    <tfoot>

                        <tr>

                            <th scope="row">Total</th>

                            <td class="stock-reservation-show__table-number">
                                {{ number_format($totalDemande, 2, ',', ' ') }}
                            </td>

                            <td class="stock-reservation-show__table-number">
                                {{ number_format($totalReserve, 2, ',', ' ') }}
                            </td>

                            <td class="stock-reservation-show__table-number">
                                {{ number_format($totalConsomme, 2, ',', ' ') }}
                            </td>

                            <td class="stock-reservation-show__table-number">
                                {{ number_format($resteReservation, 2, ',', ' ') }}
                            </td>

                            <td>—</td>

                        </tr>

                    </tfoot>

                </table>

            @else

                <div class="stock-reservation-show__empty">

                    <div class="stock-reservation-show__empty-icon">
                        <i class="fas fa-box-open" aria-hidden="true"></i>
                    </div>

                    <h3>Aucune ligne</h3>

                    <p>
                        Cette réservation ne contient actuellement
                        aucun article.
                    </p>

                </div>

            @endif

        </div>

    </section>


    {{-- =========================================================
         ACTIONS DE WORKFLOW
    ========================================================== --}}
    <section class="stock-reservation-show__actions-card">

        <div class="stock-reservation-show__card-header">

            <div>

                <h2 class="stock-reservation-show__card-title">
                    Actions
                </h2>

                <p class="stock-reservation-show__card-description">
                    Les opérations disponibles dépendent de l’état
                    actuel de la réservation.
                </p>

            </div>

        </div>


        <div class="stock-reservation-show__workflow">

            {{-- BROUILLON → CONFIRMATION --}}
            @if($statutValue === 'brouillon')

                @can('confirm', $reservation)

                    <form
                        method="POST"
                        action="{{ route('admin.stocks.reservations.confirmer', $reservation) }}"
                        class="stock-reservation-show__action-form"
                    >
                        @csrf

                        {{-- =====================================================
                            Lignes de la réservation — envoyées automatiquement
                            avec quantite_reservee = quantite_demandee.

                            Le ConfirmerReservationStockRequest exige que
                            TOUTES les lignes soient présentes.
                        ====================================================== --}}
                        @foreach($reservation->lignes as $index => $ligne)

                            <input
                                type="hidden"
                                name="lignes[{{ $index }}][ligne_id]"
                                value="{{ $ligne->id }}"
                            >

                            <input
                                type="hidden"
                                name="lignes[{{ $index }}][quantite_reservee]"
                                value="{{ (float) $ligne->quantite_demandee }}"
                            >

                        @endforeach

                        <button
                            type="submit"
                            class="stock-reservation-show__workflow-button stock-reservation-show__workflow-button--primary"
                            data-confirm-action="true"
                            data-confirm-message="Voulez-vous confirmer cette réservation ? Les quantités seront bloquées."
                        >
                            <i class="fas fa-check" aria-hidden="true"></i>
                            Confirmer la réservation
                        </button>
                    </form>

                @endcan

            @endif


            {{-- CONFIRMÉE / PARTIELLE → ANNULATION --}}
            @if(in_array($statutValue, ['confirmee', 'partielle'], true))

                @can('cancel', $reservation)

                    <form
                        method="POST"
                        action="{{ route('admin.stocks.reservations.annuler', $reservation) }}"
                        class="stock-reservation-show__action-form"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="stock-reservation-show__workflow-button stock-reservation-show__workflow-button--danger"
                            data-confirm-action="true"
                            data-confirm-message="Voulez-vous vraiment annuler cette réservation ? Les quantités encore réservées seront libérées."
                        >

                            <i class="fas fa-ban" aria-hidden="true"></i>

                            Annuler

                        </button>

                    </form>

                @endcan

            @endif


            {{-- CONSOMMÉE --}}
            @if($statutValue === 'consommee')

                <div class="stock-reservation-show__workflow-message">

                    <i class="fas fa-check-double" aria-hidden="true"></i>

                    <span>
                        Cette réservation a été entièrement consommée.
                    </span>

                </div>

            @endif


            {{-- ANNULÉE --}}
            @if($statutValue === 'annulee')

                <div class="stock-reservation-show__workflow-message workflow-message--danger">

                    <i class="fas fa-ban" aria-hidden="true"></i>

                    <span>Cette réservation a été annulée.</span>

                </div>

            @endif


            {{-- EXPIRÉE --}}
            @if($statutValue === 'expiree')

                <div class="stock-reservation-show__workflow-message workflow-message--warning">

                    <i class="fas fa-clock" aria-hidden="true"></i>

                    <span>Cette réservation est arrivée à expiration.</span>

                </div>

            @endif

        </div>

    </section>


    {{-- =========================================================
         TRAÇABILITÉ
    ========================================================== --}}
    <section class="stock-reservation-show__card">

        <div class="stock-reservation-show__card-header">

            <div>

                <h2 class="stock-reservation-show__card-title">
                    Traçabilité
                </h2>

                <p class="stock-reservation-show__card-description">
                    Informations techniques relatives à la création
                    et à la modification de la réservation.
                </p>

            </div>

            <span class="stock-reservation-show__section-icon">
                <i class="fas fa-history" aria-hidden="true"></i>
            </span>

        </div>


        <div class="stock-reservation-show__card-body">

            <div class="stock-reservation-show__audit-grid">

                <div class="stock-reservation-show__audit-item">

                    <span>Créée le</span>

                    <strong>
                        {{ $reservation->created_at
                            ? $reservation->created_at->format('d/m/Y à H:i')
                            : '—'
                        }}
                    </strong>

                </div>


                <div class="stock-reservation-show__audit-item">

                    <span>Dernière modification</span>

                    <strong>
                        {{ $reservation->updated_at
                            ? $reservation->updated_at->format('d/m/Y à H:i')
                            : '—'
                        }}
                    </strong>

                </div>


                <div class="stock-reservation-show__audit-item">

                    <span>Identifiant technique</span>

                    <strong>#{{ $reservation->id }}</strong>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         RETOUR
    ========================================================== --}}
    <div class="stock-reservation-show__footer">

        <a
            href="{{ route('admin.stocks.reservations.index') }}"
            class="stock-reservation-show__button stock-reservation-show__button--secondary"
        >

            <i class="fas fa-arrow-left" aria-hidden="true"></i>

            Retour aux réservations

        </a>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/reservations-show.js') }}"
        defer
    ></script>

@endsection