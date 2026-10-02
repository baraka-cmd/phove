@extends('layouts.public')

@section('title', $projet->titre)

@section('meta_description', Str::limit(strip_tags($projet->description), 160))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/projets-show.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DU PROJET --}}
    {{-- ============================================ --}}
    <section class="projet-header">
        <div class="projet-header-overlay"></div>
        <div class="container">
            <div class="projet-header-content">
                {{-- Badges --}}
                <div class="projet-header-badges">
                    <span class="projet-header-badge projet-badge-{{ $projet->statut }}">
                        @if($projet->statut === 'en_cours')
                            <i class="fas fa-spinner fa-pulse"></i> Projet en cours
                        @elseif($projet->statut === 'termine')
                            <i class="fas fa-check-circle"></i> Projet terminé
                        @elseif($projet->statut === 'en_preparation')
                            <i class="fas fa-clock"></i> En préparation
                        @elseif($projet->statut === 'suspendu')
                            <i class="fas fa-pause-circle"></i> Suspendu
                        @endif
                    </span>
                    <span class="projet-header-badge projet-header-badge-domain">
                        <i class="fas fa-tag"></i> {{ $projet->domaineLibelle() }}
                    </span>
                    @if($projet->code_projet)
                    <span class="projet-header-badge projet-header-badge-code">
                        <i class="fas fa-barcode"></i> {{ $projet->code_projet }}
                    </span>
                    @endif
                </div>

                {{-- Titre --}}
                <h1 class="projet-header-title">{{ $projet->titre }}</h1>

                {{-- Fil d'Ariane --}}
                <nav class="page-breadcrumb">
                    <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('projets.index') }}">Nos Projets</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>{{ Str::limit($projet->titre, 50) }}</span>
                </nav>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION INFORMATIONS GÉNÉRALES --}}
    {{-- ============================================ --}}
    <section class="projet-infos-section">
        <div class="container">
            <div class="projet-infos-grid">
                {{-- Carte info 1 : Zone --}}
                <div class="projet-info-card">
                    <div class="projet-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="projet-info-content">
                        <span class="projet-info-label">Zone d'intervention</span>
                        <span class="projet-info-value">{{ $projet->zone_geographique }}</span>
                        @if($projet->province)
                            <span class="projet-info-sub">{{ $projet->province }}</span>
                        @endif
                    </div>
                </div>

                {{-- Carte info 2 : Période --}}
                <div class="projet-info-card">
                    <div class="projet-info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="projet-info-content">
                        <span class="projet-info-label">Période d'exécution</span>
                        <span class="projet-info-value">
                            {{ $projet->date_debut->format('d/m/Y') }}
                            @if($projet->date_fin)
                                - {{ $projet->date_fin->format('d/m/Y') }}
                            @else
                                <span class="projet-info-en-cours">(En cours)</span>
                            @endif
                        </span>
                        @if($projet->date_fin)
                            <span class="projet-info-sub">Durée : {{ $projet->dureeEnMois() }} mois</span>
                        @endif
                    </div>
                </div>

                {{-- Carte info 3 : Budget --}}
                <div class="projet-info-card">
                    <div class="projet-info-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="projet-info-content">
                        <span class="projet-info-label">Budget</span>
                        <span class="projet-info-value">
                            {{ number_format($projet->budget_total, 0, ',', ' ') }} $
                        </span>
                        <span class="projet-info-sub">
                            Utilisé : {{ number_format($projet->budget_utilise, 0, ',', ' ') }} $
                        </span>
                    </div>
                </div>

                {{-- Carte info 4 : Bailleur --}}
                <div class="projet-info-card">
                    <div class="projet-info-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="projet-info-content">
                        <span class="projet-info-label">Source de financement</span>
                        <span class="projet-info-value">{{ $projet->bailleur ?? 'Non spécifié' }}</span>
                        @if($projet->source_financement)
                            <span class="projet-info-sub">{{ $projet->source_financement }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION CONTENU PRINCIPAL --}}
    {{-- ============================================ --}}
    <section class="projet-content-section">
        <div class="container">
            <div class="projet-content-grid">
                {{-- Colonne principale --}}
                <div class="projet-main">

                    {{-- Image principale --}}
                    <div class="projet-main-image">
                        @if($projet->image_principale)
                            <img src="{{ img_url($projet->image_principale) }}"
                                alt="{{ $projet->titre }}"
                                class="projet-featured-image"
                                loading="lazy"
                                onerror="this.onerror=null; this.src='{{ asset('assets/images/logo-phove.png') }}';">
                        @else
                            <img src="{{ asset('assets/images/logo-phove.png') }}"
                                alt="{{ $projet->titre }}"
                                class="projet-featured-image projet-featured-image-placeholder">
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="projet-block">
                        <h2 class="projet-block-title">
                            <i class="fas fa-align-left"></i> Description du projet
                        </h2>
                        <div class="projet-block-text">
                            {!! nl2br(e($projet->description)) !!}
                        </div>
                    </div>

                    {{-- Objectifs --}}
                    @if($projet->objectifs)
                    <div class="projet-block">
                        <h2 class="projet-block-title">
                            <i class="fas fa-bullseye"></i> Objectifs
                        </h2>
                        <div class="projet-block-text">
                            {!! nl2br(e($projet->objectifs)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Résultats attendus --}}
                    @if($projet->resultats_attendus)
                    <div class="projet-block">
                        <h2 class="projet-block-title">
                            <i class="fas fa-chart-bar"></i> Résultats attendus
                        </h2>
                        <div class="projet-block-text">
                            {!! nl2br(e($projet->resultats_attendus)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Galerie photos --}}
                    @if($projet->medias->count() > 0)
                    <div class="projet-block">
                        <h2 class="projet-block-title">
                            <i class="fas fa-images"></i> Galerie du projet
                        </h2>

                        <div class="projet-gallery" id="projetGallery">
                            @foreach($projet->medias as $media)
                                @php
                                    // Fichier principal (pleine résolution)
                                    $fichierUrl = $media->fichier
                                        ? img_url($media->fichier)
                                        : null;

                                    // Miniature (fallback sur le fichier principal)
                                    $thumbUrl = $media->miniature
                                        ? img_url($media->miniature)
                                        : $fichierUrl;
                                @endphp

                                <div class="gallery-item"
                                    data-src="{{ $fichierUrl }}"
                                    data-title="{{ $media->titre }}"
                                    data-type="{{ $media->type }}">

                                    @if($thumbUrl)
                                        <img src="{{ $thumbUrl }}"
                                            alt="{{ $media->titre }}"
                                            loading="lazy"
                                            class="gallery-thumb"
                                            onerror="this.style.display='none'; this.parentElement.style.background='linear-gradient(135deg, #E8F0F8, #F1F5F9)';">
                                    @endif

                                    @if($media->type === 'video')
                                        <div class="gallery-play-icon">
                                            <i class="fas fa-play"></i>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="projet-sidebar">
                    
                    {{-- Progression du budget --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-chart-pie"></i> Progression du budget
                        </h3>
                        <div class="sidebar-budget-progress">
                            <div class="budget-progress-circle" data-percent="{{ $projet->pourcentageBudget() }}">
                                <svg viewBox="0 0 36 36" class="budget-circle-svg">
                                    <path class="budget-circle-bg"
                                          d="M18 2.0845
                                             a 15.9155 15.9155 0 0 1 0 31.831
                                             a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#E2E8F0" stroke-width="3" />
                                    <path class="budget-circle-fill"
                                          d="M18 2.0845
                                             a 15.9155 15.9155 0 0 1 0 31.831
                                             a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" 
                                          stroke="{{ $projet->pourcentageBudget() >= 100 ? '#10B981' : '#1A5B9C' }}" 
                                          stroke-width="3"
                                          stroke-dasharray="{{ min($projet->pourcentageBudget(), 100) }}, 100" />
                                </svg>
                                <div class="budget-circle-text">
                                    <span class="budget-circle-percent">{{ $projet->pourcentageBudget() }}%</span>
                                    <span class="budget-circle-label">utilisé</span>
                                </div>
                            </div>
                            <div class="budget-details">
                                <div class="budget-detail-item">
                                    <span class="budget-detail-label">
                                        <i class="fas fa-wallet icon-total"></i> Budget total
                                    </span>
                                    <span class="budget-detail-value">{{ number_format($projet->budget_total, 0, ',', ' ') }} $</span>
                                </div>
                                <div class="budget-detail-item">
                                    <span class="budget-detail-label">
                                        <i class="fas fa-arrow-up icon-used"></i> Déjà utilisé
                                    </span>
                                    <span class="budget-detail-value budget-used">{{ number_format($projet->budget_utilise, 0, ',', ' ') }} $</span>
                                </div>
                                <div class="budget-detail-item">
                                    <span class="budget-detail-label">
                                        <i class="fas fa-piggy-bank icon-remaining"></i> Reste à utiliser
                                    </span>
                                    <span class="budget-detail-value budget-remaining">{{ number_format($projet->budget_total - $projet->budget_utilise, 0, ',', ' ') }} $</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Indicateurs de performance (KPI) --}}
                    @if($projet->indicateurs->count() > 0)
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-tachometer-alt"></i> Indicateurs de performance
                        </h3>
                        <div class="sidebar-kpis">
                            @foreach($projet->indicateurs as $indicateur)
                            <div class="kpi-item">
                                <div class="kpi-header">
                                    <span class="kpi-name">{{ $indicateur->nom_indicateur }}</span>
                                    <span class="kpi-percent">{{ $indicateur->pourcentage() }}%</span>
                                </div>
                                <div class="kpi-bar">
                                    <div class="kpi-fill {{ $indicateur->objectifAtteint() ? 'kpi-achieved' : '' }}" 
                                         style="width: {{ min($indicateur->pourcentage(), 100) }}%"
                                         data-width="{{ $indicateur->pourcentage() }}%">
                                    </div>
                                </div>
                                <div class="kpi-values">
                                    <span>{{ $indicateur->valeur_actuelle }} / {{ $indicateur->valeur_cible }} {{ $indicateur->unite }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Partenaires --}}
                    @if($projet->partenaires->count() > 0)
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-handshake"></i> Partenaires du projet
                        </h3>
                        <div class="sidebar-partners">
                            @foreach($projet->partenaires as $partenaire)
                            <div class="partner-item">
                                <div class="partner-item-avatar">
                                    <span>{{ $partenaire->sigleOuInitiales() }}</span>
                                </div>
                                <div class="partner-item-info">
                                    <span class="partner-item-name">
                                        {{ $partenaire->nom }}
                                        @if($partenaire->sigle)
                                            <small style="color: #64748B;">({{ $partenaire->sigle }})</small>
                                        @endif
                                    </span>
                                    <span class="partner-item-role">
                                        {{ $partenaire->pivot->role ? str_replace('_', ' ', ucfirst($partenaire->pivot->role)) : 'Partenaire' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Bouton de don --}}
                    <a href="{{ url('/faire-un-don') }}" class="btn btn-accent btn-lg sidebar-donate-btn">
                        <i class="fas fa-heart"></i> Soutenir ce projet
                    </a>

                    {{-- Actions --}}
                    <div class="sidebar-actions">
                        <button class="sidebar-action-btn btn-share" 
                                data-title="{{ $projet->titre }}" 
                                data-text="Découvrez le projet {{ $projet->titre }} de PHOVE"
                                title="Partager">
                            <i class="fas fa-share-alt"></i> Partager
                        </button>
                        <button class="sidebar-action-btn btn-print" title="Imprimer">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    

    {{-- ============================================ --}}
    {{-- SECTION TÉMOIGNAGES --}}
    {{-- ============================================ --}}
    @if($projet->beneficiaires->count() > 0)
    <section class="projet-temoignages-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ils témoignent</span>
                <h2 class="section-title">Témoignages des bénéficiaires</h2>
            </div>
            <div class="temoignages-grid">
                @foreach($projet->beneficiaires as $beneficiaire)
                <blockquote class="temoignage-card">
                    <div class="temoignage-quote">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <p class="temoignage-text">{{ $beneficiaire->temoignage }}</p>
                    <div class="temoignage-author">
                        <div class="temoignage-avatar">
                            <span>{{ Str::upper(Str::substr($beneficiaire->nom, 0, 1) . Str::substr($beneficiaire->prenom ?? '', 0, 1)) }}</span>
                        </div>
                        <div class="temoignage-info">
                            <span class="temoignage-name">{{ $beneficiaire->nomComplet() }}</span>
                            <span class="temoignage-location">{{ $beneficiaire->localisation }}</span>
                        </div>
                    </div>
                </blockquote>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================ --}}
    {{-- SECTION PROJETS CONNEXES --}}
    {{-- ============================================ --}}
    @if($projetsConnexes->count() > 0)
    <section class="projet-connexes-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">À découvrir aussi</span>
                <h2 class="section-title">Projets similaires</h2>
            </div>
            <div class="projets-grid">
                @foreach($projetsConnexes as $projetConnexe)
                    @include('public.partials.projet-card', ['projet' => $projetConnexe])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section-projet">
        <div class="cta-decoration cta-decoration-left"></div>
        <div class="cta-decoration cta-decoration-right"></div>

        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">
                    Chaque <span class="highlight">contribution</span> compte
                </h2>
                <p class="cta-text">
                    Votre don nous permet de continuer à mener des projets comme celui-ci 
                    et d'étendre notre impact auprès des orphelins, veuves et personnes démunies.
                </p>
                <div class="cta-buttons">
                    <a href="{{ url('/faire-un-don') }}" class="btn-cta-don">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                    <a href="{{ route('projets.index') }}" class="btn-cta-outline">
                        <i class="fas fa-list"></i> Voir tous les projets
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/projets-show.js') }}"></script>
@endsection