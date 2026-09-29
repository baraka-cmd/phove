@extends('layouts.admin')

@section('title', 'Inventaire ' . $inventaire->numero)

@section('styles')
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin/stocks/inventaires-show.css') }}"
    >
@endsection

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Statut
    |--------------------------------------------------------------------------
    */

    $statutValue = (string) $inventaire->statut;

    $statutLabel = match ($statutValue) {
        'brouillon' => 'Brouillon',
        'en_cours'  => 'En cours',
        'termine'   => 'Terminé',
        'valide'    => 'Validé',
        'annule'    => 'Annulé',
        default     => ucfirst(str_replace('_', ' ', $statutValue)),
    };

    $statutIcon = match ($statutValue) {
        'brouillon' => 'fa-file-alt',
        'en_cours'  => 'fa-sync-alt',
        'termine'   => 'fa-clipboard-check',
        'valide'    => 'fa-check-double',
        'annule'    => 'fa-ban',
        default     => 'fa-file-alt',
    };

    /*
    |--------------------------------------------------------------------------
    | Autorisations d'action
    |--------------------------------------------------------------------------
    */

    $peutEtreCompte = in_array($statutValue, ['brouillon', 'en_cours'], true);
    $peutEtreValide = !in_array($statutValue, ['valide', 'annule'], true);

    /*
    |--------------------------------------------------------------------------
    | Lignes
    |--------------------------------------------------------------------------
    */

    $lignes = $inventaire->lignes;

    /*
     * Toutes les lignes sont-elles comptées ?
     * (utilisé pour l'état initial du bouton Valider)
     */
    $toutesComptees = $lignes->isNotEmpty()
        && $lignes->every(fn ($l) => $l->quantite_comptee !== null);
@endphp


<div class="admin-content">

    <div class="stock-inventaire-show-page">

        {{-- =========================================================
             EN-TÊTE
        ========================================================== --}}
        <header class="stock-inventaire-show-header">

            <div class="stock-inventaire-show-header__main">

                <nav
                    class="stock-inventaire-show-breadcrumb"
                    aria-label="Fil d’Ariane"
                >

                    <a href="{{ route('admin.stocks.index') }}">
                        Stocks
                    </a>

                    <span aria-hidden="true">/</span>

                    <a href="{{ route('admin.stocks.inventaires.index') }}">
                        Inventaires
                    </a>

                    <span aria-hidden="true">/</span>

                    <span>{{ $inventaire->numero }}</span>

                </nav>


                <div class="stock-inventaire-show-title-wrapper">

                    <div class="stock-inventaire-show-title-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>

                    <div>

                        <div class="stock-inventaire-show-reference-line">

                            <h1 class="stock-inventaire-show-title">
                                Inventaire
                            </h1>

                            <span class="stock-inventaire-show-reference">
                                {{ $inventaire->numero }}
                            </span>

                        </div>

                        <p class="stock-inventaire-show-subtitle">
                            Suivi du comptage physique, des écarts et de la validation
                            de l’opération d’inventaire.
                        </p>

                    </div>

                </div>

            </div>


            <div class="stock-inventaire-show-header__actions">

                <a
                    href="{{ route('admin.stocks.inventaires.index') }}"
                    class="stock-inventaire-show-button stock-inventaire-show-button--secondary"
                >

                    <i class="fas fa-arrow-left"></i>

                    <span>Retour</span>

                </a>

            </div>

        </header>


        {{-- =========================================================
             MESSAGES
        ========================================================== --}}
        @if(session('success'))

            <div class="stock-inventaire-show-alert stock-inventaire-show-alert--success" role="alert">

                <div class="stock-inventaire-show-alert__icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="stock-inventaire-show-alert__content">

                    <strong>Opération réussie</strong>

                    <p>{{ session('success') }}</p>

                </div>

            </div>

        @endif


        @if(session('error'))

            <div class="stock-inventaire-show-alert stock-inventaire-show-alert--danger" role="alert">

                <div class="stock-inventaire-show-alert__icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>

                <div class="stock-inventaire-show-alert__content">

                    <strong>Opération impossible</strong>

                    <p>{{ session('error') }}</p>

                </div>

            </div>

        @endif


        @if($errors->any())

            <div class="stock-inventaire-show-alert stock-inventaire-show-alert--danger" role="alert">

                <div class="stock-inventaire-show-alert__icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div class="stock-inventaire-show-alert__content">

                    <strong>Vérifiez les informations</strong>

                    <ul style="margin-top: 6px; padding-left: 20px;">

                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            </div>

        @endif


        {{-- =========================================================
             STATUT
        ========================================================== --}}
        <section class="stock-inventaire-show-status-card">

            <div class="stock-inventaire-show-status-card__main">

                <div class="stock-inventaire-show-status-icon">
                    <i class="fas {{ $statutIcon }}"></i>
                </div>


                <div class="stock-inventaire-show-status-card__content">

                    <span class="stock-inventaire-show-status-card__label">
                        État de l’inventaire
                    </span>

                    <strong>{{ $statutLabel }}</strong>

                    <p>

                        @switch($statutValue)

                            @case('brouillon')
                                L’inventaire est en préparation.
                                @break

                            @case('en_cours')
                                Le comptage physique est en cours.
                                @break

                            @case('termine')
                                Le comptage est terminé et les écarts peuvent être analysés.
                                @break

                            @case('valide')
                                L’inventaire a été validé et ses résultats sont définitifs.
                                @break

                            @case('annule')
                                Cette opération d’inventaire a été annulée.
                                @break

                            @default
                                État actuel de l’opération d’inventaire.

                        @endswitch

                    </p>

                </div>

            </div>


            {{-- =================================================
                 ACTIONS DE STATUT (HAUT DE PAGE)
                 ------------------------------------------------
                 Le bouton est TOUJOURS ACTIF dans le HTML.
                 Le JS le désactive dynamiquement si toutes
                 les lignes ne sont pas comptées.
            ================================================== --}}
            <div class="stock-inventaire-show-status-card__actions">

                @can('validate', $inventaire)

                    @if($peutEtreValide)

                        <form
                            action="{{ route('admin.stocks.inventaires.valider', $inventaire) }}"
                            method="POST"
                            class="stock-inventaire-show-inline-form"
                        >

                            @csrf

                            <button
                                type="submit"
                                class="stock-inventaire-show-button stock-inventaire-show-button--primary"
                                data-confirm-action="true"
                                data-confirm-message="Voulez-vous valider cet inventaire ? Les ajustements seront enregistrés de manière définitive."
                            >

                                <i class="fas fa-check"></i>

                                <span>Valider l’inventaire</span>

                            </button>

                        </form>

                    @endif

                @endcan

            </div>

        </section>


        {{-- =========================================================
             INDICATEURS
        ========================================================== --}}
        @php
            $ecartGlobal = (float) ($resume['ecart_global'] ?? 0);
        @endphp

        <section class="stock-inventaire-show-statistics">

            <article class="stock-inventaire-show-stat-card">

                <div class="stock-inventaire-show-stat-card__icon">
                    <i class="fas fa-boxes"></i>
                </div>

                <div class="stock-inventaire-show-stat-card__content">

                    <span>Articles contrôlés</span>

                    <strong>
                        {{ number_format((int) ($resume['nombre_lignes'] ?? 0), 0, ',', ' ') }}
                    </strong>

                </div>

            </article>


            <article class="stock-inventaire-show-stat-card">

                <div class="stock-inventaire-show-stat-card__icon">
                    <i class="fas fa-database"></i>
                </div>

                <div class="stock-inventaire-show-stat-card__content">

                    <span>Quantité système</span>

                    <strong>
                        {{ number_format(
                            (float) ($resume['quantite_systeme'] ?? 0),
                            2,
                            ',',
                            ' '
                        ) }}
                    </strong>

                </div>

            </article>


            <article class="stock-inventaire-show-stat-card">

                <div class="stock-inventaire-show-stat-card__icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>

                <div class="stock-inventaire-show-stat-card__content">

                    <span>Quantité comptée</span>

                    <strong>
                        {{ number_format(
                            (float) ($resume['quantite_comptee'] ?? 0),
                            2,
                            ',',
                            ' '
                        ) }}
                    </strong>

                </div>

            </article>


            <article
                class="
                    stock-inventaire-show-stat-card
                    {{ $ecartGlobal < 0 ? 'stock-inventaire-show-stat-card--danger' : '' }}
                    {{ $ecartGlobal > 0 ? 'stock-inventaire-show-stat-card--warning' : '' }}
                    {{ $ecartGlobal == 0 ? 'stock-inventaire-show-stat-card--success' : '' }}
                "
            >

                <div class="stock-inventaire-show-stat-card__icon">
                    <i class="fas fa-balance-scale"></i>
                </div>

                <div class="stock-inventaire-show-stat-card__content">

                    <span>Écart global</span>

                    <strong>
                        {{ $ecartGlobal > 0 ? '+' : '' }}{{ number_format($ecartGlobal, 2, ',', ' ') }}
                    </strong>

                    <small>
                        {{ (int) ($resume['nombre_ecarts'] ?? 0) }} article(s) avec écart
                    </small>

                </div>

            </article>

        </section>


        {{-- =========================================================
             FORMULAIRE DE COMPTAGE
             ------------------------------------------------
             Affiché uniquement tant que l'inventaire n'est pas validé.
        ========================================================== --}}
        @if($peutEtreCompte && $lignes->count())

            <form
                method="POST"
                action="{{ route('admin.stocks.inventaires.compter', $inventaire) }}"
                id="inventaire-comptage-form"
            >

                @csrf

                <section class="stock-inventaire-show-card">

                    <div class="stock-inventaire-show-card__header">

                        <div>

                            <h2>Comptage physique</h2>

                            <p>
                                Saisissez la quantité réellement constatée
                                pour chaque article. L’écart est calculé
                                en temps réel.
                            </p>

                        </div>

                        <div class="stock-inventaire-show-card__icon">
                            <i class="fas fa-keyboard"></i>
                        </div>

                    </div>


                    <div class="stock-inventaire-show-card__body stock-inventaire-show-card__body--table">

                        <div class="stock-inventaire-show-table-wrapper">

                            <table class="stock-inventaire-show-table">

                                <thead>

                                    <tr>

                                        <th>Article</th>
                                        <th>Référence</th>
                                        <th class="text-right">Système</th>
                                        <th class="text-right">Compté</th>
                                        <th class="text-right">Écart</th>
                                        <th>État</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach($lignes as $index => $ligne)

                                        @php
                                            $article = $ligne->stock;

                                            $quantiteTheorique = (float) $ligne->quantite_theorique;

                                            $quantiteComptee = old(
                                                "comptages.{$index}.quantite_comptee",
                                                $ligne->quantite_comptee
                                            );

                                            $ecart = ($quantiteComptee !== null && $quantiteComptee !== '')
                                                ? (float) $quantiteComptee - $quantiteTheorique
                                                : null;
                                        @endphp

                                        <tr>

                                            <td>

                                                <input
                                                    type="hidden"
                                                    name="comptages[{{ $index }}][ligne_id]"
                                                    value="{{ $ligne->id }}"
                                                >

                                                <div class="stock-inventaire-show-article">

                                                    <div class="stock-inventaire-show-article__icon">
                                                        <i class="fas fa-box"></i>
                                                    </div>

                                                    <div class="stock-inventaire-show-article__content">

                                                        <strong>
                                                            {{ $article?->designation ?? 'Article supprimé' }}
                                                        </strong>

                                                        @if($ligne->unite)
                                                            <small>Unité : {{ $ligne->unite }}</small>
                                                        @endif

                                                    </div>

                                                </div>

                                            </td>


                                            <td>
                                                <span class="stock-inventaire-show-reference-cell">
                                                    {{ $article?->reference ?? '—' }}
                                                </span>
                                            </td>


                                            <td class="text-right">
                                                <span class="stock-inventaire-show-quantity">
                                                    {{ number_format($quantiteTheorique, 2, ',', ' ') }}
                                                </span>
                                            </td>


                                            <td class="text-right">

                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    name="comptages[{{ $index }}][quantite_comptee]"
                                                    value="{{ $quantiteComptee }}"
                                                    class="stock-inventaire-show-input-quantity"
                                                    data-comptage-input
                                                    data-theorique="{{ $quantiteTheorique }}"
                                                    placeholder="—"
                                                >

                                            </td>


                                            <td class="text-right">

                                                <span
                                                    class="stock-inventaire-show-difference
                                                        @if($ecart === null)
                                                        @elseif($ecart > 0)
                                                            stock-inventaire-show-difference--positive
                                                        @elseif($ecart < 0)
                                                            stock-inventaire-show-difference--negative
                                                        @else
                                                            stock-inventaire-show-difference--zero
                                                        @endif"
                                                    data-comptage-ecart
                                                >

                                                    @if($ecart !== null)
                                                        {{ $ecart > 0 ? '+' : '' }}{{ number_format($ecart, 2, ',', ' ') }}
                                                    @else
                                                        <span class="stock-inventaire-show-muted">—</span>
                                                    @endif

                                                </span>

                                            </td>


                                            <td>

                                                <span
                                                    class="stock-inventaire-show-badge
                                                        @if($ecart === null)
                                                            stock-inventaire-show-badge--neutral
                                                        @elseif($ecart > 0)
                                                            stock-inventaire-show-badge--positive
                                                        @elseif($ecart < 0)
                                                            stock-inventaire-show-badge--negative
                                                        @else
                                                            stock-inventaire-show-badge--neutral
                                                        @endif"
                                                    data-comptage-badge
                                                >

                                                    @if($ecart === null)
                                                        <i class="fas fa-clock"></i>
                                                        Non compté
                                                    @elseif($ecart > 0)
                                                        <i class="fas fa-arrow-up"></i>
                                                        Surplus
                                                    @elseif($ecart < 0)
                                                        <i class="fas fa-arrow-down"></i>
                                                        Manquant
                                                    @else
                                                        <i class="fas fa-check"></i>
                                                        Conforme
                                                    @endif

                                                </span>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>


                    <footer class="stock-inventaire-show-card__footer">

                        <div class="stock-inventaire-show-comptage-actions">

                            <button
                                type="submit"
                                class="stock-inventaire-show-button stock-inventaire-show-button--primary"
                            >

                                <i class="fas fa-save"></i>

                                <span>Enregistrer les comptages</span>

                            </button>

                        </div>

                    </footer>

                </section>

            </form>

        @endif


        {{-- =========================================================
             RÉSULTATS EN LECTURE SEULE (inventaire validé ou comptage figé)
        ========================================================== --}}
        @if(!$peutEtreCompte && $lignes->count())

            <section class="stock-inventaire-show-card">

                <div class="stock-inventaire-show-card__header">

                    <div>

                        <h2>Résultats du comptage</h2>

                        <p>Comparaison entre les quantités système et constatées.</p>

                    </div>

                    <div class="stock-inventaire-show-card__icon">
                        <i class="fas fa-list-check"></i>
                    </div>

                </div>


                <div class="stock-inventaire-show-card__body stock-inventaire-show-card__body--table">

                    <div class="stock-inventaire-show-table-wrapper">

                        <table class="stock-inventaire-show-table">

                            <thead>

                                <tr>
                                    <th>Article</th>
                                    <th>Référence</th>
                                    <th class="text-right">Système</th>
                                    <th class="text-right">Compté</th>
                                    <th class="text-right">Écart</th>
                                    <th>État</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($lignes as $ligne)

                                    @php
                                        $article = $ligne->stock;
                                        $quantiteTheorique = (float) $ligne->quantite_theorique;
                                        $quantitePhysique = $ligne->quantite_comptee !== null
                                            ? (float) $ligne->quantite_comptee
                                            : null;
                                        $ecart = $ligne->ecart !== null
                                            ? (float) $ligne->ecart
                                            : null;
                                    @endphp

                                    <tr>

                                        <td>
                                            <div class="stock-inventaire-show-article">
                                                <div class="stock-inventaire-show-article__icon">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                                <div class="stock-inventaire-show-article__content">
                                                    <strong>{{ $article?->designation ?? 'Article supprimé' }}</strong>
                                                    @if($ligne->unite)
                                                        <small>Unité : {{ $ligne->unite }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="stock-inventaire-show-reference-cell">
                                                {{ $article?->reference ?? '—' }}
                                            </span>
                                        </td>

                                        <td class="text-right">
                                            <span class="stock-inventaire-show-quantity">
                                                {{ number_format($quantiteTheorique, 2, ',', ' ') }}
                                            </span>
                                        </td>

                                        <td class="text-right">
                                            <span class="stock-inventaire-show-quantity stock-inventaire-show-quantity--physical">
                                                {{ $quantitePhysique !== null
                                                    ? number_format($quantitePhysique, 2, ',', ' ')
                                                    : '—'
                                                }}
                                            </span>
                                        </td>

                                        <td class="text-right">
                                            @if($ecart === null)
                                                <span class="stock-inventaire-show-muted">—</span>
                                            @else
                                                <span class="stock-inventaire-show-difference
                                                    {{ $ecart > 0 ? 'stock-inventaire-show-difference--positive' : '' }}
                                                    {{ $ecart < 0 ? 'stock-inventaire-show-difference--negative' : '' }}
                                                    {{ $ecart == 0 ? 'stock-inventaire-show-difference--zero' : '' }}">
                                                    {{ $ecart > 0 ? '+' : '' }}{{ number_format($ecart, 2, ',', ' ') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($ecart === null)
                                                <span class="stock-inventaire-show-badge stock-inventaire-show-badge--neutral">
                                                    <i class="fas fa-clock"></i> Non compté
                                                </span>
                                            @elseif($ecart > 0)
                                                <span class="stock-inventaire-show-badge stock-inventaire-show-badge--positive">
                                                    <i class="fas fa-arrow-up"></i> Surplus
                                                </span>
                                            @elseif($ecart < 0)
                                                <span class="stock-inventaire-show-badge stock-inventaire-show-badge--negative">
                                                    <i class="fas fa-arrow-down"></i> Manquant
                                                </span>
                                            @else
                                                <span class="stock-inventaire-show-badge stock-inventaire-show-badge--neutral">
                                                    <i class="fas fa-check"></i> Conforme
                                                </span>
                                            @endif
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>


                            <tfoot>

                                <tr>

                                    <td colspan="2">
                                        <strong>Total</strong>
                                    </td>

                                    <td class="text-right">
                                        <strong>
                                            {{ number_format(
                                                (float) ($resume['quantite_systeme'] ?? 0),
                                                2,
                                                ',',
                                                ' '
                                            ) }}
                                        </strong>
                                    </td>

                                    <td class="text-right">
                                        <strong>
                                            {{ number_format(
                                                (float) ($resume['quantite_comptee'] ?? 0),
                                                2,
                                                ',',
                                                ' '
                                            ) }}
                                        </strong>
                                    </td>

                                    <td class="text-right">
                                        <strong class="
                                            {{ $ecartGlobal > 0 ? 'stock-inventaire-show-difference--positive' : '' }}
                                            {{ $ecartGlobal < 0 ? 'stock-inventaire-show-difference--negative' : '' }}
                                            {{ $ecartGlobal == 0 ? 'stock-inventaire-show-difference--zero' : '' }}">
                                            {{ $ecartGlobal > 0 ? '+' : '' }}{{ number_format($ecartGlobal, 2, ',', ' ') }}
                                        </strong>
                                    </td>

                                    <td></td>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                </div>

            </section>

        @endif


        {{-- =========================================================
             ANALYSE DES ÉCARTS
        ========================================================== --}}
        @if((int) ($resume['nombre_ecarts'] ?? 0) > 0)

            <section class="stock-inventaire-show-card stock-inventaire-show-card--warning">

                <div class="stock-inventaire-show-card__header">

                    <div>

                        <h2>Analyse des écarts</h2>

                        <p>Articles nécessitant une vérification ou un ajustement.</p>

                    </div>

                    <div class="stock-inventaire-show-card__icon">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                </div>


                <div class="stock-inventaire-show-card__body">

                    <div class="stock-inventaire-show-warning">

                        <div class="stock-inventaire-show-warning__icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>

                        <div class="stock-inventaire-show-warning__content">

                            <strong>
                                {{ (int) ($resume['nombre_ecarts'] ?? 0) }} article(s) présentent un écart.
                            </strong>

                            <p>
                                Un écart constaté lors du comptage ne modifie pas
                                automatiquement le stock. Les ajustements doivent
                                être validés avant la création des mouvements
                                correspondants.
                            </p>

                        </div>

                    </div>

                </div>

            </section>

        @endif


        {{-- =========================================================
             LAYOUT SECONDAIRE (INFOS + SIDEBAR)
        ========================================================== --}}
        <div class="stock-inventaire-show-layout">

            <main class="stock-inventaire-show-main">

                {{-- INFORMATIONS GÉNÉRALES --}}
                <section class="stock-inventaire-show-card">

                    <div class="stock-inventaire-show-card__header">

                        <div>

                            <h2>Informations générales</h2>

                            <p>Données principales de l’opération.</p>

                        </div>

                        <div class="stock-inventaire-show-card__icon">
                            <i class="fas fa-info-circle"></i>
                        </div>

                    </div>


                    <div class="stock-inventaire-show-card__body">

                        <div class="stock-inventaire-show-information-grid">

                            <div class="stock-inventaire-show-information-item">
                                <span class="stock-inventaire-show-information-item__label">
                                    Numéro
                                </span>
                                <strong>{{ $inventaire->numero }}</strong>
                            </div>


                            <div class="stock-inventaire-show-information-item">
                                <span class="stock-inventaire-show-information-item__label">
                                    Date de début
                                </span>
                                <strong>
                                    {{ $inventaire->date_debut
                                        ? \Carbon\Carbon::parse($inventaire->date_debut)->format('d/m/Y')
                                        : '—'
                                    }}
                                </strong>
                            </div>


                            <div class="stock-inventaire-show-information-item">
                                <span class="stock-inventaire-show-information-item__label">
                                    Date de clôture
                                </span>
                                <strong>
                                    {{ $inventaire->date_cloture
                                        ? \Carbon\Carbon::parse($inventaire->date_cloture)->format('d/m/Y')
                                        : '—'
                                    }}
                                </strong>
                            </div>


                            <div class="stock-inventaire-show-information-item">
                                <span class="stock-inventaire-show-information-item__label">
                                    Emplacement
                                </span>
                                <strong>
                                    {{ $inventaire->emplacement?->nom ?? 'Tous les emplacements' }}
                                </strong>
                            </div>


                            <div class="stock-inventaire-show-information-item">
                                <span class="stock-inventaire-show-information-item__label">
                                    Créé le
                                </span>
                                <strong>
                                    {{ $inventaire->created_at
                                        ? $inventaire->created_at->format('d/m/Y à H:i')
                                        : '—'
                                    }}
                                </strong>
                            </div>


                            <div class="stock-inventaire-show-information-item">
                                <span class="stock-inventaire-show-information-item__label">
                                    Dernière modification
                                </span>
                                <strong>
                                    {{ $inventaire->updated_at
                                        ? $inventaire->updated_at->format('d/m/Y à H:i')
                                        : '—'
                                    }}
                                </strong>
                            </div>

                        </div>

                    </div>

                </section>


                {{-- NOTES --}}
                @if($inventaire->notes)

                    <section class="stock-inventaire-show-card">

                        <div class="stock-inventaire-show-card__header">

                            <div>

                                <h2>Notes</h2>

                                <p>Informations complémentaires liées à l’opération.</p>

                            </div>

                            <div class="stock-inventaire-show-card__icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>

                        </div>


                        <div class="stock-inventaire-show-card__body">

                            <div class="stock-inventaire-show-notes">
                                {{ $inventaire->notes }}
                            </div>

                        </div>

                    </section>

                @endif

            </main>


            {{-- =========================================================
                 COLONNE LATÉRALE
            ========================================================== --}}
            <aside class="stock-inventaire-show-sidebar">

                {{-- RESPONSABLE --}}
                <section class="stock-inventaire-show-side-card">

                    <div class="stock-inventaire-show-side-card__header">
                        <h2>Responsable</h2>
                    </div>


                    <div class="stock-inventaire-show-user">

                        <div class="stock-inventaire-show-user__avatar">
                            <i class="fas fa-user"></i>
                        </div>

                        <div class="stock-inventaire-show-user__content">

                            <strong>
                                {{ $inventaire->responsable?->nom ?? 'Non renseigné' }}
                            </strong>

                            @if($inventaire->responsable?->email)
                                <span>{{ $inventaire->responsable->email }}</span>
                            @endif

                        </div>

                    </div>

                </section>


                {{-- VALIDATION --}}
                <section class="stock-inventaire-show-side-card">

                    <div class="stock-inventaire-show-side-card__header">
                        <h2>Validation</h2>
                    </div>


                    @if($inventaire->validePar)

                        <div class="stock-inventaire-show-validation">

                            <div class="stock-inventaire-show-validation__icon">
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="stock-inventaire-show-validation__content">

                                <strong>Validé</strong>

                                <span>{{ $inventaire->validePar->nom }}</span>

                                @if($inventaire->valide_at)
                                    <small>{{ $inventaire->valide_at->format('d/m/Y à H:i') }}</small>
                                @endif

                            </div>

                        </div>

                    @else

                        <div class="stock-inventaire-show-validation stock-inventaire-show-validation--pending">

                            <div class="stock-inventaire-show-validation__icon">
                                <i class="fas fa-clock"></i>
                            </div>

                            <div class="stock-inventaire-show-validation__content">

                                <strong>Non validé</strong>

                                <span>L’opération attend sa validation.</span>

                            </div>

                        </div>

                    @endif

                </section>


                {{-- RÉSUMÉ --}}
                <section class="stock-inventaire-show-side-card">

                    <div class="stock-inventaire-show-side-card__header">
                        <h2>Résumé</h2>
                    </div>


                    <div class="stock-inventaire-show-summary">

                        <div class="stock-inventaire-show-summary__row">
                            <span>Articles contrôlés</span>
                            <strong>
                                {{ number_format((int) ($resume['nombre_lignes'] ?? 0), 0, ',', ' ') }}
                            </strong>
                        </div>


                        <div class="stock-inventaire-show-summary__row">
                            <span>Quantité système</span>
                            <strong>
                                {{ number_format(
                                    (float) ($resume['quantite_systeme'] ?? 0),
                                    2,
                                    ',',
                                    ' '
                                ) }}
                            </strong>
                        </div>


                        <div class="stock-inventaire-show-summary__row">
                            <span>Quantité comptée</span>
                            <strong>
                                {{ number_format(
                                    (float) ($resume['quantite_comptee'] ?? 0),
                                    2,
                                    ',',
                                    ' '
                                ) }}
                            </strong>
                        </div>


                        <div class="stock-inventaire-show-summary__row stock-inventaire-show-summary__row--total">

                            <span>Écart global</span>

                            <strong class="
                                {{ $ecartGlobal > 0 ? 'stock-inventaire-show-difference--positive' : '' }}
                                {{ $ecartGlobal < 0 ? 'stock-inventaire-show-difference--negative' : '' }}
                                {{ $ecartGlobal == 0 ? 'stock-inventaire-show-difference--zero' : '' }}">

                                {{ $ecartGlobal > 0 ? '+' : '' }}{{ number_format($ecartGlobal, 2, ',', ' ') }}

                            </strong>

                        </div>

                    </div>

                </section>


                {{-- ACTIONS --}}
                <section class="stock-inventaire-show-side-card">

                    <div class="stock-inventaire-show-side-card__header">
                        <h2>Actions</h2>
                    </div>


                    <div class="stock-inventaire-show-actions">

                        @can('validate', $inventaire)

                            @if($peutEtreValide)

                                <form
                                    action="{{ route('admin.stocks.inventaires.valider', $inventaire) }}"
                                    method="POST"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="stock-inventaire-show-action-button stock-inventaire-show-action-button--primary"
                                        data-confirm-action="true"
                                        data-confirm-message="Voulez-vous valider cet inventaire ? Les ajustements seront enregistrés."
                                    >

                                        <i class="fas fa-check"></i>

                                        <span>Valider l’inventaire</span>

                                    </button>

                                </form>

                            @endif

                        @endcan


                        @can('cancel', $inventaire)

                            @if($peutEtreValide)

                                <form
                                    action="{{ route('admin.stocks.inventaires.annuler', $inventaire) }}"
                                    method="POST"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="stock-inventaire-show-action-button stock-inventaire-show-action-button--danger"
                                        data-confirm-action="true"
                                        data-confirm-message="Voulez-vous annuler cet inventaire ? Les comptages seront perdus."
                                    >

                                        <i class="fas fa-ban"></i>

                                        <span>Annuler l’inventaire</span>

                                    </button>

                                </form>

                            @endif

                        @endcan


                        <a
                            href="{{ route('admin.stocks.inventaires.index') }}"
                            class="stock-inventaire-show-action-button stock-inventaire-show-action-button--secondary"
                        >

                            <i class="fas fa-list"></i>

                            <span>Tous les inventaires</span>

                        </a>

                    </div>

                </section>

            </aside>

        </div>


        {{-- =========================================================
             INFORMATION DE SÉCURITÉ
        ========================================================== --}}
        <div class="stock-inventaire-show-security-notice">

            <div class="stock-inventaire-show-security-notice__icon">
                <i class="fas fa-shield-alt"></i>
            </div>

            <div class="stock-inventaire-show-security-notice__content">

                <strong>Traçabilité des ajustements</strong>

                <p>
                    Les écarts d’inventaire sont des constatations physiques.
                    Ils ne doivent pas modifier directement les quantités du stock.
                    Toute correction doit passer par le processus métier prévu
                    afin de conserver un historique fiable des mouvements.
                </p>

            </div>

        </div>

    </div>

</div>

@endsection


@section('scripts')

    <script
        src="{{ asset('assets/js/admin/stocks/inventaires-show.js') }}"
        defer
    ></script>

@endsection