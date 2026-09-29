@extends('layouts.admin')

@section('title', 'Lot ' . ($lot->numero_lot ?? $lot->id))

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/lots-show.css') }}"
    >
@endsection

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Normalisation du statut
    |--------------------------------------------------------------------------
    */

    $statutLotValue = $lot->statut instanceof \App\Enums\StatutLot
        ? $lot->statut->value
        : (string) $lot->statut;

    $statutLotLabel = $lot->statut instanceof \App\Enums\StatutLot
        ? $lot->statut->libelle()
        : ucfirst(str_replace('_', ' ', $statutLotValue));

    $statutLotClass = match ($statutLotValue) {
        'actif'  => 'success',
        'bloque' => 'warning',
        'expire' => 'danger',
        'epuise' => 'default',
        default  => 'default',
    };

    /*
    |--------------------------------------------------------------------------
    | Calculs d'affichage
    |--------------------------------------------------------------------------
    */

    $quantiteInitiale  = (float) $lot->quantite_initiale;
    $quantiteActuelle  = (float) $lot->quantite_actuelle;
    $quantiteConsommee = max(0, $quantiteInitiale - $quantiteActuelle);

    $pourcentageConsomme = $quantiteInitiale > 0
        ? min(100, ($quantiteConsommee / $quantiteInitiale) * 100)
        : 0;

    $unite = $lot->stock?->unite ?? '';

    /*
    |--------------------------------------------------------------------------
    | Expiration
    |--------------------------------------------------------------------------
    */

    $dateExpiration = $lot->date_expiration
        ? \Carbon\Carbon::parse($lot->date_expiration)
        : null;

    $estExpire = $dateExpiration ? $dateExpiration->isPast() : false;

    $expireBientot = $dateExpiration
        && !$estExpire
        && now()->diffInDays($dateExpiration, false) <= 30;

    /*
    |--------------------------------------------------------------------------
    | Actions disponibles
    |--------------------------------------------------------------------------
    */

    $peutEtreBloque = $lot->statut instanceof \App\Enums\StatutLot
        ? $lot->statut->peutEtreBloque()
        : false;

    $peutEtreDebloque = $lot->statut instanceof \App\Enums\StatutLot
        ? $lot->statut->peutEtreDebloque()
        : false;
@endphp


<div class="stock-lot-show-page">

    {{-- =========================================================
         EN-TÊTE
    ========================================================== --}}
    <div class="stock-lot-show-page__header">

        <div class="stock-lot-show-page__header-main">

            <div class="stock-lot-show-page__breadcrumb">

                <a href="{{ route('admin.stocks.index') }}">
                    Stocks
                </a>

                <span class="stock-lot-show-page__breadcrumb-separator">/</span>

                <a href="{{ route('admin.stocks.lots.index') }}">
                    Lots
                </a>

                <span class="stock-lot-show-page__breadcrumb-separator">/</span>

                <span>Détail</span>

            </div>

            <div class="stock-lot-show-page__title-row">

                <div>

                    <h1 class="stock-lot-show-page__title">
                        Lot {{ $lot->numero_lot ?? ('#' . $lot->id) }}
                    </h1>

                    <p class="stock-lot-show-page__subtitle">
                        Consultation détaillée du lot et de son état
                        dans le stock.
                    </p>

                </div>

                <span class="stock-lot-show-page__status stock-lot-show-page__status--{{ $statutLotClass }}">

                    <span class="stock-lot-show-page__status-dot"></span>

                    {{ $statutLotLabel }}

                </span>

            </div>

        </div>


        <div class="stock-lot-show-page__header-actions">

            <a
                href="{{ route('admin.stocks.lots.index') }}"
                class="stock-lot-show-page__button stock-lot-show-page__button--secondary"
            >

                <i class="fas fa-arrow-left"></i>

                <span>Retour aux lots</span>

            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES FLASH
    ========================================================== --}}
    @if(session('success'))

        <div class="stock-lot-show-page__alert stock-lot-show-page__alert--success">

            <div class="stock-lot-show-page__alert-icon">
                <i class="fas fa-check"></i>
            </div>

            <div class="stock-lot-show-page__alert-content">

                <strong>Opération réussie</strong>

                <p>{{ session('success') }}</p>

            </div>

        </div>

    @endif


    @if(session('error'))

        <div class="stock-lot-show-page__alert stock-lot-show-page__alert--danger">

            <div class="stock-lot-show-page__alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

            <div class="stock-lot-show-page__alert-content">

                <strong>Opération impossible</strong>

                <p>{{ session('error') }}</p>

            </div>

        </div>

    @endif


    @if(session('warning'))

        <div class="stock-lot-show-page__alert stock-lot-show-page__alert--warning">

            <div class="stock-lot-show-page__alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>

            <div class="stock-lot-show-page__alert-content">

                <strong>Attention</strong>

                <p>{{ session('warning') }}</p>

            </div>

        </div>

    @endif


    {{-- =========================================================
         GRILLE PRINCIPALE
    ========================================================== --}}
    <div class="stock-lot-show-page__grid">

        {{-- =====================================================
             IDENTITÉ DU LOT
        ====================================================== --}}
        <section class="stock-lot-show-page__card stock-lot-show-page__card--main">

            <div class="stock-lot-show-page__card-header">

                <div>

                    <h2 class="stock-lot-show-page__card-title">
                        Informations du lot
                    </h2>

                    <p class="stock-lot-show-page__card-description">
                        Informations d’identification et de traçabilité.
                    </p>

                </div>

                <div class="stock-lot-show-page__card-icon">
                    <i class="fas fa-boxes"></i>
                </div>

            </div>


            <div class="stock-lot-show-page__card-body">

                <div class="stock-lot-show-page__information-grid">

                    {{-- NUMÉRO --}}
                    <div class="stock-lot-show-page__information-item">

                        <span class="stock-lot-show-page__information-label">
                            Numéro du lot
                        </span>

                        <strong class="stock-lot-show-page__information-value stock-lot-show-page__information-value--primary">
                            {{ $lot->numero_lot ?? ('LOT-' . $lot->id) }}
                        </strong>

                    </div>


                    {{-- ARTICLE --}}
                    <div class="stock-lot-show-page__information-item">

                        <span class="stock-lot-show-page__information-label">
                            Article
                        </span>

                        <div class="stock-lot-show-page__article">

                            <div class="stock-lot-show-page__article-icon">
                                <i class="fas fa-cube"></i>
                            </div>

                            <div class="stock-lot-show-page__article-content">

                                @if($lot->stock)

                                    <a
                                        href="{{ route('admin.stocks.show', $lot->stock) }}"
                                        class="stock-lot-show-page__article-name"
                                    >
                                        {{ $lot->stock->designation }}
                                    </a>

                                    @if($lot->stock->reference)

                                        <span class="stock-lot-show-page__article-reference">
                                            Réf. {{ $lot->stock->reference }}
                                        </span>

                                    @endif

                                @else

                                    <span class="stock-lot-show-page__article-name">
                                        Article non disponible
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- STATUT --}}
                    <div class="stock-lot-show-page__information-item">

                        <span class="stock-lot-show-page__information-label">
                            Statut
                        </span>

                        <span class="stock-lot-show-page__status stock-lot-show-page__status--{{ $statutLotClass }}">

                            <span class="stock-lot-show-page__status-dot"></span>

                            {{ $statutLotLabel }}

                        </span>

                    </div>


                    {{-- UNITÉ --}}
                    <div class="stock-lot-show-page__information-item">

                        <span class="stock-lot-show-page__information-label">
                            Unité
                        </span>

                        <strong class="stock-lot-show-page__information-value">
                            {{ $lot->stock?->unite ?? '—' }}
                        </strong>

                    </div>


                    {{-- DATE DE FABRICATION --}}
                    @if($lot->date_fabrication)

                        <div class="stock-lot-show-page__information-item">

                            <span class="stock-lot-show-page__information-label">
                                Date de fabrication
                            </span>

                            <strong class="stock-lot-show-page__information-value">
                                {{ \Carbon\Carbon::parse($lot->date_fabrication)->format('d/m/Y') }}
                            </strong>

                        </div>

                    @endif


                    {{-- DATE D'ENTRÉE --}}
                    @if($lot->date_entree)

                        <div class="stock-lot-show-page__information-item">

                            <span class="stock-lot-show-page__information-label">
                                Date d'entrée
                            </span>

                            <strong class="stock-lot-show-page__information-value">
                                {{ \Carbon\Carbon::parse($lot->date_entree)->format('d/m/Y') }}
                            </strong>

                        </div>

                    @endif


                    {{-- DATE D'EXPIRATION --}}
                    @if($dateExpiration)

                        <div class="stock-lot-show-page__information-item">

                            <span class="stock-lot-show-page__information-label">
                                Date d'expiration
                            </span>

                            <strong class="stock-lot-show-page__information-value
                                {{ $estExpire
                                    ? 'stock-lot-show-page__information-value--danger'
                                    : ($expireBientot
                                        ? 'stock-lot-show-page__information-value--warning'
                                        : 'stock-lot-show-page__information-value--success') }}">

                                <i class="fas fa-calendar-alt"></i>

                                {{ $dateExpiration->format('d/m/Y') }}

                            </strong>

                            @if($estExpire)

                                <span class="stock-lot-show-page__information-help stock-lot-show-page__information-help--danger">
                                    Lot expiré
                                </span>

                            @elseif($expireBientot)

                                <span class="stock-lot-show-page__information-help stock-lot-show-page__information-help--warning">
                                    Expiration prochaine
                                </span>

                            @endif

                        </div>

                    @endif


                    {{-- CRÉATION --}}
                    @if($lot->created_at)

                        <div class="stock-lot-show-page__information-item">

                            <span class="stock-lot-show-page__information-label">
                                Créé le
                            </span>

                            <strong class="stock-lot-show-page__information-value">
                                {{ $lot->created_at->format('d/m/Y H:i') }}
                            </strong>

                        </div>

                    @endif

                </div>

            </div>

        </section>


        {{-- =====================================================
             SITUATION DU STOCK
        ====================================================== --}}
        <section class="stock-lot-show-page__card">

            <div class="stock-lot-show-page__card-header">

                <div>

                    <h2 class="stock-lot-show-page__card-title">
                        Situation du stock
                    </h2>

                    <p class="stock-lot-show-page__card-description">
                        Quantités et évolution du lot.
                    </p>

                </div>

                <div class="stock-lot-show-page__card-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>

            </div>


            <div class="stock-lot-show-page__card-body">

                {{-- QUANTITÉ ACTUELLE (mise en avant) --}}
                <div class="stock-lot-show-page__quantity-highlight">

                    <div class="stock-lot-show-page__quantity-highlight-icon">
                        <i class="fas fa-cubes"></i>
                    </div>

                    <div class="stock-lot-show-page__quantity-highlight-content">

                        <span>
                            Quantité actuelle
                        </span>

                        <strong>
                            {{ number_format($quantiteActuelle, 2, ',', ' ') }}
                            {{ $unite }}
                        </strong>

                    </div>

                </div>


                <div class="stock-lot-show-page__quantity-list">

                    {{-- QUANTITÉ INITIALE --}}
                    <div class="stock-lot-show-page__quantity-row">

                        <div class="stock-lot-show-page__quantity-label">

                            <span class="stock-lot-show-page__quantity-dot stock-lot-show-page__quantity-dot--primary"></span>

                            Quantité initiale

                        </div>

                        <strong>
                            {{ number_format($quantiteInitiale, 2, ',', ' ') }}
                            {{ $unite }}
                        </strong>

                    </div>


                    {{-- QUANTITÉ ACTUELLE --}}
                    <div class="stock-lot-show-page__quantity-row">

                        <div class="stock-lot-show-page__quantity-label">

                            <span class="stock-lot-show-page__quantity-dot stock-lot-show-page__quantity-dot--success"></span>

                            Quantité actuelle

                        </div>

                        <strong>
                            {{ number_format($quantiteActuelle, 2, ',', ' ') }}
                            {{ $unite }}
                        </strong>

                    </div>


                    {{-- QUANTITÉ CONSOMMÉE --}}
                    <div class="stock-lot-show-page__quantity-row">

                        <div class="stock-lot-show-page__quantity-label">

                            <span class="stock-lot-show-page__quantity-dot stock-lot-show-page__quantity-dot--muted"></span>

                            Quantité consommée

                        </div>

                        <strong>
                            {{ number_format($quantiteConsommee, 2, ',', ' ') }}
                            {{ $unite }}
                        </strong>

                    </div>

                </div>


                @if($quantiteInitiale > 0)

                    <div class="stock-lot-show-page__progress">

                        <div class="stock-lot-show-page__progress-header">

                            <span>
                                Utilisation du lot
                            </span>

                            <strong>
                                {{ number_format($pourcentageConsomme, 1, ',', ' ') }} %
                            </strong>

                        </div>

                        <div class="stock-lot-show-page__progress-track">

                            <div
                                class="stock-lot-show-page__progress-bar"
                                style="width: {{ $pourcentageConsomme }}%;"
                            ></div>

                        </div>

                    </div>

                @endif

            </div>

        </section>

    </div>


    {{-- =========================================================
         INFORMATIONS COMPLÉMENTAIRES
    ========================================================== --}}
    @if($lot->fournisseur || $lot->cout_unitaire !== null || $lot->reference_document)

        <section class="stock-lot-show-page__card">

            <div class="stock-lot-show-page__card-header">

                <div>

                    <h2 class="stock-lot-show-page__card-title">
                        Informations complémentaires
                    </h2>

                    <p class="stock-lot-show-page__card-description">
                        Données supplémentaires liées à ce lot.
                    </p>

                </div>

                <div class="stock-lot-show-page__card-icon">
                    <i class="fas fa-info-circle"></i>
                </div>

            </div>


            <div class="stock-lot-show-page__card-body">

                <div class="stock-lot-show-page__information-grid">

                    {{-- FOURNISSEUR --}}
                    @if($lot->fournisseur)

                        <div class="stock-lot-show-page__information-item">

                            <span class="stock-lot-show-page__information-label">
                                Fournisseur
                            </span>

                            <strong class="stock-lot-show-page__information-value">
                                {{ $lot->fournisseur }}
                            </strong>

                        </div>

                    @endif


                    {{-- COÛT UNITAIRE --}}
                    @if($lot->cout_unitaire !== null)

                        <div class="stock-lot-show-page__information-item">

                            <span class="stock-lot-show-page__information-label">
                                Coût unitaire
                            </span>

                            <strong class="stock-lot-show-page__information-value">
                                {{ number_format((float) $lot->cout_unitaire, 2, ',', ' ') }}
                                $
                            </strong>

                        </div>

                    @endif


                    {{-- RÉFÉRENCE DOCUMENT --}}
                    @if($lot->reference_document)

                        <div class="stock-lot-show-page__information-item">

                            <span class="stock-lot-show-page__information-label">
                                Référence document
                            </span>

                            <strong class="stock-lot-show-page__information-value">
                                {{ $lot->reference_document }}
                            </strong>

                        </div>

                    @endif

                </div>

            </div>

        </section>

    @endif


    {{-- =========================================================
         ACTIONS SUR LE LOT
    ========================================================== --}}
    @if(
        ($peutEtreBloque && auth()->user()->can('block', $lot))
        || ($peutEtreDebloque && auth()->user()->can('unblock', $lot))
    )

        <section class="stock-lot-show-page__card stock-lot-show-page__card--actions">

            <div class="stock-lot-show-page__card-header">

                <div>

                    <h2 class="stock-lot-show-page__card-title">
                        Actions disponibles
                    </h2>

                    <p class="stock-lot-show-page__card-description">
                        Les actions ci-dessous sont soumises aux
                        autorisations métier.
                    </p>

                </div>

                <div class="stock-lot-show-page__card-icon">
                    <i class="fas fa-cogs"></i>
                </div>

            </div>


            <div class="stock-lot-show-page__card-body">

                <div class="stock-lot-show-page__action-list">

                    {{-- BLOQUER --}}
                    @if($peutEtreBloque)

                        @can('block', $lot)

                            <form
                                action="{{ route('admin.stocks.lots.bloquer', $lot) }}"
                                method="POST"
                                class="stock-lot-show-page__action-form"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="stock-lot-show-page__action-button stock-lot-show-page__action-button--warning"
                                    data-confirm="Voulez-vous vraiment bloquer ce lot ?"
                                >

                                    <i class="fas fa-lock"></i>

                                    <span>Bloquer le lot</span>

                                </button>

                            </form>

                        @endcan

                    @endif


                    {{-- DÉBLOQUER --}}
                    @if($peutEtreDebloque)

                        @can('unblock', $lot)

                            <form
                                action="{{ route('admin.stocks.lots.debloquer', $lot) }}"
                                method="POST"
                                class="stock-lot-show-page__action-form"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="stock-lot-show-page__action-button stock-lot-show-page__action-button--success"
                                    data-confirm="Voulez-vous vraiment débloquer ce lot ?"
                                >

                                    <i class="fas fa-unlock"></i>

                                    <span>Débloquer le lot</span>

                                </button>

                            </form>

                        @endcan

                    @endif

                </div>

            </div>

        </section>

    @endif


    {{-- =========================================================
         HISTORIQUE DES MOUVEMENTS
    ========================================================== --}}
    @if($lot->mouvements->isNotEmpty())

        <section class="stock-lot-show-page__card">

            <div class="stock-lot-show-page__card-header">

                <div>

                    <h2 class="stock-lot-show-page__card-title">
                        Historique des mouvements
                    </h2>

                    <p class="stock-lot-show-page__card-description">
                        Traçabilité des mouvements enregistrés sur ce lot.
                    </p>

                </div>

                <span class="stock-lot-show-page__count">

                    {{ $lot->mouvements->count() }}
                    mouvement{{ $lot->mouvements->count() > 1 ? 's' : '' }}

                </span>

            </div>


            <div class="stock-lot-show-page__table-wrapper">

                <table class="stock-lot-show-page__table">

                    <thead>

                        <tr>

                            <th>Date</th>
                            <th>Type</th>
                            <th>Nature</th>
                            <th class="text-right">Quantité</th>
                            <th>Référence</th>
                            <th>Utilisateur</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($lot->mouvements->sortByDesc('date_mouvement') as $mouvement)

                            @php
                                $typeMouvement = $mouvement->type instanceof \App\Enums\TypeMouvement
                                    ? $mouvement->type->value
                                    : (string) $mouvement->type;

                                $typeLabel = $mouvement->type instanceof \App\Enums\TypeMouvement
                                    ? $mouvement->type->libelle()
                                    : ucfirst(str_replace('_', ' ', $typeMouvement));

                                $typeClass = match ($typeMouvement) {
                                    'entree'     => 'success',
                                    'sortie'     => 'danger',
                                    'ajustement' => 'warning',
                                    default      => 'default',
                                };

                                $natureLabel = $mouvement->nature instanceof \App\Enums\NatureMouvement
                                    ? $mouvement->nature->libelle()
                                    : ($mouvement->nature
                                        ? ucfirst(str_replace('_', ' ', (string) $mouvement->nature))
                                        : '—');

                                $quantiteSignee = (float) $mouvement->quantite
                                    * (int) ($mouvement->sens ?? 1);
                            @endphp

                            <tr>

                                {{-- DATE --}}
                                <td>

                                    <span class="stock-lot-show-page__table-date">

                                        {{ $mouvement->date_mouvement
                                            ? \Carbon\Carbon::parse($mouvement->date_mouvement)->format('d/m/Y')
                                            : '—'
                                        }}

                                    </span>

                                </td>


                                {{-- TYPE --}}
                                <td>

                                    <span class="stock-lot-show-page__movement-type stock-lot-show-page__movement-type--{{ $typeClass }}">

                                        <span class="stock-lot-show-page__movement-type-dot"></span>

                                        {{ $typeLabel }}

                                    </span>

                                </td>


                                {{-- NATURE --}}
                                <td>

                                    <span class="stock-lot-show-page__table-nature">

                                        {{ $natureLabel }}

                                    </span>

                                </td>


                                {{-- QUANTITÉ --}}
                                <td class="text-right">

                                    <strong class="stock-lot-show-page__table-quantity">

                                        @if($quantiteSignee > 0)
                                            +
                                        @elseif($quantiteSignee < 0)
                                            -
                                        @endif

                                        {{ number_format(abs($quantiteSignee), 2, ',', ' ') }}

                                        {{ $unite }}

                                    </strong>

                                </td>


                                {{-- RÉFÉRENCE --}}
                                <td>

                                    <span class="stock-lot-show-page__table-reference">

                                        {{ $mouvement->reference_document ?? '—' }}

                                    </span>

                                </td>


                                {{-- UTILISATEUR --}}
                                <td>

                                    @if($mouvement->utilisateur)

                                        <span class="stock-lot-show-page__table-user">
                                            {{ $mouvement->utilisateur->name }}
                                        </span>

                                    @else

                                        <span class="stock-lot-show-page__muted">
                                            —
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </section>

    @else

        <section class="stock-lot-show-page__card">

            <div class="stock-lot-show-page__empty">

                <div class="stock-lot-show-page__empty-icon">
                    <i class="fas fa-history"></i>
                </div>

                <h3>
                    Aucun mouvement enregistré
                </h3>

                <p>
                    Aucun mouvement n'est actuellement associé
                    à ce lot.
                </p>

            </div>

        </section>

    @endif


    {{-- =========================================================
         AVERTISSEMENT MÉTIER
    ========================================================== --}}
    <div class="stock-lot-show-page__info-card">

        <div class="stock-lot-show-page__info-icon">
            <i class="fas fa-shield-alt"></i>
        </div>

        <div class="stock-lot-show-page__info-content">

            <h3>
                Traçabilité du stock
            </h3>

            <p>
                La quantité de ce lot ne doit pas être modifiée
                directement depuis l'interface. Toute variation doit
                provenir d'un <strong>mouvement de stock</strong>,
                d'une <strong>réservation</strong>, d'une
                <strong>libération</strong> ou d'un
                <strong>ajustement d'inventaire validé</strong>.
            </p>

        </div>

    </div>

</div>

@endsection