@extends('layouts.admin')

@section('title', 'Réservations de stock')

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/reservations-index.css') }}"
    >
@endsection

@section('content')

<div class="admin-content stock-reservations-index">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="stock-reservations-index__header">

        <div class="stock-reservations-index__header-content">

            <div class="stock-reservations-index__breadcrumb">

                <a href="{{ route('admin.dashboard') }}">
                    Tableau de bord
                </a>

                <span aria-hidden="true">/</span>

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span aria-hidden="true">/</span>

                <span>Réservations</span>

            </div>

            <h1 class="stock-reservations-index__title">
                Réservations de stock
            </h1>

            <p class="stock-reservations-index__subtitle">
                Consultez, recherchez et suivez les réservations de stock
                avant leur consommation physique.
            </p>

        </div>


        <div class="stock-reservations-index__header-actions">

            @can('create', App\Models\ReservationStock::class)

                <a
                    href="{{ route('admin.stocks.reservations.create') }}"
                    class="stock-reservations-index__btn stock-reservations-index__btn--primary"
                >

                    <i class="fas fa-plus" aria-hidden="true"></i>

                    <span>Nouvelle réservation</span>

                </a>

            @endcan

        </div>

    </div>


    {{-- =========================================================
         MESSAGES DE SESSION
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-reservations-index__alert stock-reservations-index__alert--success" role="alert">

            <div class="stock-reservations-index__alert-icon">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
            </div>

            <div class="stock-reservations-index__alert-content">

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-reservations-index__alert stock-reservations-index__alert--danger" role="alert">

            <div class="stock-reservations-index__alert-icon">
                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            </div>

            <div class="stock-reservations-index__alert-content">

                <strong>Opération impossible</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    {{-- =========================================================
         STATISTIQUES
         ------------------------------------------------
         $stats est fourni par le controller (si disponible).
         Fallback : calcul sur la page courante uniquement.
    ========================================================== --}}
    @php
        $statsTotal       = $stats['total']       ?? $reservations->total();
        $statsBrouillons  = $stats['brouillon']   ?? 0;
        $statsConfirmees  = $stats['confirmee']   ?? 0;
        $statsActives     = $stats['actives']     ?? 0;
    @endphp

    <div class="stock-reservations-index__stats">

        {{-- TOTAL --}}
        <div class="stock-reservations-index__stat-card">

            <div class="stock-reservations-index__stat-icon">
                <i class="fas fa-clipboard-list" aria-hidden="true"></i>
            </div>

            <div class="stock-reservations-index__stat-content">

                <span class="stock-reservations-index__stat-label">
                    Total
                </span>

                <strong class="stock-reservations-index__stat-value">
                    {{ number_format($statsTotal, 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- BROUILLONS --}}
        <div class="stock-reservations-index__stat-card">

            <div class="stock-reservations-index__stat-icon stock-reservations-index__stat-icon--draft">
                <i class="fas fa-file-alt" aria-hidden="true"></i>
            </div>

            <div class="stock-reservations-index__stat-content">

                <span class="stock-reservations-index__stat-label">
                    Brouillons
                </span>

                <strong class="stock-reservations-index__stat-value">
                    {{ number_format($statsBrouillons, 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- CONFIRMÉES --}}
        <div class="stock-reservations-index__stat-card">

            <div class="stock-reservations-index__stat-icon stock-reservations-index__stat-icon--confirmed">
                <i class="fas fa-check" aria-hidden="true"></i>
            </div>

            <div class="stock-reservations-index__stat-content">

                <span class="stock-reservations-index__stat-label">
                    Confirmées
                </span>

                <strong class="stock-reservations-index__stat-value">
                    {{ number_format($statsConfirmees, 0, ',', ' ') }}
                </strong>

            </div>

        </div>


        {{-- ACTIVES --}}
        <div class="stock-reservations-index__stat-card">

            <div class="stock-reservations-index__stat-icon stock-reservations-index__stat-icon--active">
                <i class="fas fa-lock" aria-hidden="true"></i>
            </div>

            <div class="stock-reservations-index__stat-content">

                <span class="stock-reservations-index__stat-label">
                    Actives
                </span>

                <strong class="stock-reservations-index__stat-value">
                    {{ number_format($statsActives, 0, ',', ' ') }}
                </strong>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTRES
    ========================================================== --}}
    <div class="stock-reservations-index__card">

        <div class="stock-reservations-index__card-header">

            <div>

                <h2 class="stock-reservations-index__card-title">
                    Rechercher une réservation
                </h2>

                <p class="stock-reservations-index__card-description">
                    Utilisez les filtres pour retrouver rapidement
                    une réservation.
                </p>

            </div>

        </div>


        <div class="stock-reservations-index__card-body">

            <form
                method="GET"
                action="{{ route('admin.stocks.reservations.index') }}"
                class="stock-reservations-index__filters"
            >

                {{-- RECHERCHE --}}
                <div class="stock-reservations-index__field">

                    <label for="recherche">
                        Recherche
                    </label>

                    <div class="stock-reservations-index__input-wrapper">

                        <i class="fas fa-search" aria-hidden="true"></i>

                        <input
                            type="search"
                            id="recherche"
                            name="recherche"
                            value="{{ request('recherche') }}"
                            placeholder="Numéro, destinataire, motif..."
                            autocomplete="off"
                        >

                    </div>

                </div>


                {{-- STATUT --}}
                <div class="stock-reservations-index__field">

                    <label for="statut">
                        Statut
                    </label>

                    <select id="statut" name="statut">

                        <option value="">
                            Tous les statuts
                        </option>

                        @foreach($statuts as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(request('statut') === $value)
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- DATE DÉBUT --}}
                <div class="stock-reservations-index__field">

                    <label for="date_debut">
                        Du
                    </label>

                    <input
                        type="date"
                        id="date_debut"
                        name="date_debut"
                        value="{{ request('date_debut') }}"
                    >

                </div>


                {{-- DATE FIN --}}
                <div class="stock-reservations-index__field">

                    <label for="date_fin">
                        Au
                    </label>

                    <input
                        type="date"
                        id="date_fin"
                        name="date_fin"
                        value="{{ request('date_fin') }}"
                    >

                </div>


                {{-- ACTIONS --}}
                <div class="stock-reservations-index__filter-actions">

                    <button
                        type="submit"
                        class="stock-reservations-index__btn stock-reservations-index__btn--primary"
                    >

                        <i class="fas fa-filter" aria-hidden="true"></i>

                        <span>Filtrer</span>

                    </button>

                    <a
                        href="{{ route('admin.stocks.reservations.index') }}"
                        class="stock-reservations-index__btn stock-reservations-index__btn--secondary"
                    >

                        <i class="fas fa-undo" aria-hidden="true"></i>

                        <span>Réinitialiser</span>

                    </a>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         LISTE DES RÉSERVATIONS
    ========================================================== --}}
    <div class="stock-reservations-index__card">

        <div class="stock-reservations-index__card-header">

            <div>

                <h2 class="stock-reservations-index__card-title">
                    Liste des réservations
                </h2>

                <p class="stock-reservations-index__card-description">

                    @if($reservations->total() > 0)

                        {{ number_format($reservations->total(), 0, ',', ' ') }}
                        réservation(s) trouvée(s).

                    @else

                        Aucune réservation trouvée.

                    @endif

                </p>

            </div>

        </div>


        @if($reservations->count() > 0)

            <div class="stock-reservations-index__table-wrapper">

                <table class="stock-reservations-index__table">

                    <thead>

                        <tr>

                            <th scope="col">Réservation</th>
                            <th scope="col">Demandeur</th>
                            <th scope="col">Projet</th>
                            <th scope="col">Date</th>
                            <th scope="col">Expiration</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Actions</th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($reservations as $reservation)

                            @php
                                /*
                                 * Normalisation du statut.
                                 */
                                $statutValue = $reservation->statut instanceof \App\Enums\StatutReservation
                                    ? $reservation->statut->value
                                    : (string) $reservation->statut;

                                $statutLabel = $reservation->statut instanceof \App\Enums\StatutReservation
                                    ? $reservation->statut->libelle()
                                    : ucfirst(str_replace('_', ' ', $statutValue));

                                $dateExpiration = $reservation->date_expiration
                                    ? \Carbon\Carbon::parse($reservation->date_expiration)
                                    : null;

                                $estExpiree =
                                    $dateExpiration
                                    && $dateExpiration->isPast()
                                    && in_array($statutValue, ['confirmee', 'partielle'], true);
                            @endphp

                            <tr>

                                {{-- NUMÉRO / DESTINATAIRE --}}
                                <td>

                                    <div class="stock-reservations-index__reservation">

                                        <div class="stock-reservations-index__reservation-icon">
                                            <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                                        </div>

                                        <div class="stock-reservations-index__reservation-info">

                                            <a
                                                href="{{ route('admin.stocks.reservations.show', $reservation) }}"
                                                class="stock-reservations-index__reservation-number"
                                            >
                                                {{ $reservation->numero }}
                                            </a>

                                            @if($reservation->destinataire)

                                                <span class="stock-reservations-index__reservation-recipient">
                                                    {{ $reservation->destinataire }}
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- DEMANDEUR --}}
                                <td>

                                    @if($reservation->demandeur)

                                        <span class="stock-reservations-index__person-name">
                                            {{ $reservation->demandeur->name }}
                                        </span>

                                    @else

                                        <span class="stock-reservations-index__muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- PROJET --}}
                                <td>

                                    @if($reservation->projet)

                                        <span class="stock-reservations-index__project">

                                            {{ $reservation->projet->nom
                                                ?? $reservation->projet->titre
                                                ?? 'Projet #' . $reservation->projet->id }}

                                        </span>

                                    @else

                                        <span class="stock-reservations-index__muted">
                                            Aucun projet
                                        </span>

                                    @endif

                                </td>


                                {{-- DATE RÉSERVATION --}}
                                <td>

                                    <div class="stock-reservations-index__date">

                                        <strong>
                                            {{ $reservation->date_reservation
                                                ? \Carbon\Carbon::parse($reservation->date_reservation)->format('d/m/Y')
                                                : '—'
                                            }}
                                        </strong>

                                    </div>

                                </td>


                                {{-- EXPIRATION --}}
                                <td>

                                    @if($dateExpiration)

                                        <div class="stock-reservations-index__expiration
                                            {{ $estExpiree ? 'stock-reservations-index__expiration--danger' : '' }}">

                                            <strong>
                                                {{ $dateExpiration->format('d/m/Y') }}
                                            </strong>

                                            @if($estExpiree)

                                                <span>Expirée</span>

                                            @elseif(
                                                in_array($statutValue, ['confirmee', 'partielle'], true)
                                                && $dateExpiration->diffInDays(now(), false) <= 3
                                            )

                                                <span>Bientôt</span>

                                            @endif

                                        </div>

                                    @else

                                        <span class="stock-reservations-index__muted">
                                            Sans expiration
                                        </span>

                                    @endif

                                </td>


                                {{-- STATUT --}}
                                <td>

                                    <span class="stock-reservations-index__status
                                        stock-reservations-index__status--{{ $statutValue }}">

                                        <span
                                            class="stock-reservations-index__status-dot"
                                            aria-hidden="true"
                                        ></span>

                                        {{ $statutLabel }}

                                    </span>

                                </td>


                                {{-- ACTIONS --}}
                                <td>

                                    <div class="stock-reservations-index__actions">

                                        {{-- VOIR --}}
                                        <a
                                            href="{{ route('admin.stocks.reservations.show', $reservation) }}"
                                            class="stock-reservations-index__action
                                                   stock-reservations-index__action--view"
                                            title="Voir la réservation"
                                            aria-label="Voir la réservation {{ $reservation->numero }}"
                                        >

                                            <i class="fas fa-eye" aria-hidden="true"></i>

                                        </a>


                                        {{-- CONFIRMER --}}
                                        @if($statutValue === 'brouillon')

                                            @can('confirm', $reservation)

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'admin.stocks.reservations.confirmer',
                                                        $reservation
                                                    ) }}"
                                                    class="stock-reservations-index__action-form"
                                                >

                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="stock-reservations-index__action
                                                               stock-reservations-index__action--confirm"
                                                        title="Confirmer"
                                                        aria-label="Confirmer {{ $reservation->numero }}"
                                                        data-confirm="true"
                                                        data-confirm-message="Voulez-vous confirmer cette réservation ?"
                                                    >

                                                        <i class="fas fa-check" aria-hidden="true"></i>

                                                    </button>

                                                </form>

                                            @endcan

                                        @endif


                                        {{-- ANNULER --}}
                                        @if(in_array($statutValue, ['brouillon', 'confirmee'], true))

                                            @can('cancel', $reservation)

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'admin.stocks.reservations.annuler',
                                                        $reservation
                                                    ) }}"
                                                    class="stock-reservations-index__action-form"
                                                >

                                                    @csrf

                                                    <button
                                                        type="submit"
                                                        class="stock-reservations-index__action
                                                               stock-reservations-index__action--cancel"
                                                        title="Annuler"
                                                        aria-label="Annuler {{ $reservation->numero }}"
                                                        data-confirm="true"
                                                        data-confirm-message="Voulez-vous annuler cette réservation ? Les quantités réservées seront libérées."
                                                    >

                                                        <i class="fas fa-ban" aria-hidden="true"></i>

                                                    </button>

                                                </form>

                                            @endcan

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if($reservations->hasPages())

                <div class="stock-reservations-index__pagination">

                    {{ $reservations->withQueryString()->links() }}

                </div>

            @endif


        @else

            {{-- ÉTAT VIDE --}}
            <div class="stock-reservations-index__empty">

                <div class="stock-reservations-index__empty-icon">
                    <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                </div>

                <h3>
                    Aucune réservation
                </h3>

                <p>
                    Aucune réservation ne correspond aux critères
                    de recherche actuels.
                </p>

                @can('create', App\Models\ReservationStock::class)

                    <a
                        href="{{ route('admin.stocks.reservations.create') }}"
                        class="stock-reservations-index__btn
                               stock-reservations-index__btn--primary"
                    >

                        <i class="fas fa-plus" aria-hidden="true"></i>

                        <span>Créer une réservation</span>

                    </a>

                @endcan

            </div>

        @endif

    </div>

</div>

@endsection