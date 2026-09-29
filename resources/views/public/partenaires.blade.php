@extends('layouts.public')

@section('title', 'Nos Partenaires | PHOVE')

@section('meta_description', 'Découvrez les organisations internationales qui soutiennent PHOVE : FAO, WFP, OCHA, UNESCO, GPE et la République Démocratique du Congo.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/partenaires.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h1 class="page-header-title">Nos Partenaires</h1>
            <p class="page-header-subtitle">
                Ils nous font confiance et soutiennent notre mission humanitaire pour les orphelins, veuves et personnes démunies de la RD Congo.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Nos Partenaires</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- STATISTIQUES RAPIDES --}}
    {{-- ============================================ --}}
    <section class="partenaires-stats-section">
        <div class="container">
            <div class="partenaires-stats-grid">
                <div class="partenaire-stat-card">
                    <div class="partenaire-stat-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="partenaire-stat-info">
                        <span class="partenaire-stat-number">{{ $totalPartenaires }}</span>
                        <span class="partenaire-stat-label">Partenaires actifs</span>
                    </div>
                </div>
                <div class="partenaire-stat-card">
                    <div class="partenaire-stat-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <div class="partenaire-stat-info">
                        <span class="partenaire-stat-number">{{ $statsTypes['international'] ?? 0 }}</span>
                        <span class="partenaire-stat-label">Partenaires internationaux</span>
                    </div>
                </div>
                <div class="partenaire-stat-card">
                    <div class="partenaire-stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="partenaire-stat-info">
                        <span class="partenaire-stat-number">{{ $statsTypes['institutionnel'] ?? 0 }}</span>
                        <span class="partenaire-stat-label">Partenaires institutionnels</span>
                    </div>
                </div>
                <div class="partenaire-stat-card">
                    <div class="partenaire-stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="partenaire-stat-info">
                        <span class="partenaire-stat-number">{{ $totalProjetsAssocies }}</span>
                        <span class="partenaire-stat-label">Projets soutenus</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- INTRODUCTION + FILTRES --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre réseau</span>
                <h2 class="section-title">Ils nous accompagnent</h2>
                <p class="section-description">
                    PHOVE collabore avec des organisations internationales et des institutions
                    qui partagent notre mission d'assistance aux personnes vulnérables.
                    Leur soutien financier, technique et logistique nous permet de mener
                    à bien nos actions humanitaires.
                </p>
            </div>

            {{-- Filtres par type --}}
            <div class="partenaires-filter">
                <a href="{{ route('partenaires') }}"
                   class="partenaire-filter-btn {{ !$type ? 'active' : '' }}">
                    <i class="fas fa-th-list"></i> Tous
                </a>
                @foreach($types as $value => $label)
                    <a href="{{ route('partenaires', ['type' => $value]) }}"
                       class="partenaire-filter-btn {{ $type === $value ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- LISTE DES PARTENAIRES --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">

            @if($partenaires->count() > 0)
                @foreach($partenaires as $partenaire)
                @php
                    // ✅ URL du logo via la route custom media.show
                    $logoUrl = $partenaire->logo
                        ? route('media.show', ['path' => ltrim($partenaire->logo, '/')])
                        : null;

                    $sigle = $partenaire->sigleOuInitiales();

                    $typeIcone = match($partenaire->type) {
                        'international'  => 'globe-africa',
                        'institutionnel' => 'building',
                        'bailleur_fonds' => 'coins',
                        'technique'      => 'tools',
                        'local'          => 'map-marker-alt',
                        default          => 'handshake',
                    };
                @endphp
                <div class="partenaire-card">
                    <div class="partenaire-card-grid">

                        {{-- Logo / Initiales --}}
                        <div class="partenaire-logo-wrapper">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}"
                                     alt="Logo {{ $partenaire->nom }}"
                                     class="partenaire-logo-img"
                                     loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                {{-- Fallback si l'image échoue --}}
                                <div class="partenaire-logo partenaire-logo-fallback" style="display: none;">
                                    <span>{{ $sigle }}</span>
                                </div>
                            @else
                                <div class="partenaire-logo">
                                    <span>{{ $sigle }}</span>
                                </div>
                            @endif

                            <div class="partenaire-type-badge">
                                <i class="fas fa-{{ $typeIcone }}"></i>
                                {{ $partenaire->typeLibelle() }}
                            </div>
                        </div>

                        {{-- Infos --}}
                        <div class="partenaire-info">
                            <h2 class="partenaire-name">
                                {{ $partenaire->nom }}
                                @if($partenaire->sigle)
                                    <small class="partenaire-name-sigle">({{ $partenaire->sigle }})</small>
                                @endif
                            </h2>

                            @if($partenaire->description)
                                <p class="partenaire-description">{{ $partenaire->description }}</p>
                            @endif

                            <div class="partenaire-meta">
                                @if($partenaire->date_debut)
                                <div class="partenaire-meta-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Partenaire depuis <strong>{{ $partenaire->date_debut->format('Y') }}</strong></span>
                                </div>
                                @endif

                                @if($partenaire->site_web)
                                <div class="partenaire-meta-item">
                                    <i class="fas fa-globe"></i>
                                    <a href="{{ $partenaire->urlSiteWeb() }}"
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        {{ Str::limit($partenaire->site_web, 40) }}
                                    </a>
                                </div>
                                @endif

                                @if($partenaire->email)
                                <div class="partenaire-meta-item">
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:{{ $partenaire->email }}">
                                        {{ $partenaire->email }}
                                    </a>
                                </div>
                                @endif
                            </div>

                            {{-- Projets soutenus --}}
                            @if($partenaire->projets->count() > 0)
                            <div class="partenaire-projets">
                                <span class="partenaire-projets-label">
                                    <i class="fas fa-project-diagram"></i>
                                    Projets soutenus ({{ $partenaire->projets->count() }}) :
                                </span>
                                <div class="partenaire-projets-tags">
                                    @foreach($partenaire->projets->take(4) as $projet)
                                        <span class="partenaire-projet-tag">
                                            <i class="fas fa-tag"></i>
                                            {{ Str::limit($projet->titre, 40) }}
                                            @if($projet->code_projet)
                                                <small>({{ $projet->code_projet }})</small>
                                            @endif
                                        </span>
                                    @endforeach
                                    @if($partenaire->projets->count() > 4)
                                        <span class="partenaire-projet-tag partenaire-projet-more">
                                            +{{ $partenaire->projets->count() - 4 }} autre(s)
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Pagination --}}
                @if($partenaires->hasPages())
                    <div class="partenaires-pagination">
                        {{ $partenaires->links() }}
                    </div>
                @endif

            @else
                <div class="partenaires-empty">
                    <div class="partenaires-empty-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Aucun partenaire trouvé</h3>
                    <p>
                        Aucun partenaire ne correspond à ce filtre.
                        <a href="{{ route('partenaires') }}">Voir tous les partenaires</a>.
                    </p>
                </div>
            @endif

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- TYPES DE PARTENARIAT --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Comment collaborer</span>
                <h2 class="section-title">Types de partenariat</h2>
                <p class="section-description">
                    Nous proposons plusieurs formes de collaboration adaptées aux objectifs de chaque partenaire.
                </p>
            </div>

            <div class="partenariat-types-grid">
                <div class="partenariat-type-card">
                    <div class="partenariat-type-icon"><i class="fas fa-coins"></i></div>
                    <h3 class="partenariat-type-title">Bailleur de fonds</h3>
                    <p class="partenariat-type-text">
                        Financez un ou plusieurs de nos projets humanitaires. Vous recevrez des rapports
                        détaillés sur l'utilisation des fonds et l'impact de votre contribution.
                    </p>
                </div>

                <div class="partenariat-type-card">
                    <div class="partenariat-type-icon"><i class="fas fa-tools"></i></div>
                    <h3 class="partenariat-type-title">Partenaire technique</h3>
                    <p class="partenariat-type-text">
                        Apportez votre expertise technique, vos équipements ou votre savoir-faire
                        pour renforcer la qualité de nos interventions sur le terrain.
                    </p>
                </div>

                <div class="partenariat-type-card">
                    <div class="partenariat-type-icon"><i class="fas fa-truck"></i></div>
                    <h3 class="partenariat-type-title">Partenaire logistique</h3>
                    <p class="partenariat-type-text">
                        Soutenez nos opérations en fournissant du matériel, des véhicules, des
                        fournitures ou des locaux pour nos activités.
                    </p>
                </div>

                <div class="partenariat-type-card">
                    <div class="partenariat-type-icon"><i class="fas fa-bullhorn"></i></div>
                    <h3 class="partenariat-type-title">Partenaire média</h3>
                    <p class="partenariat-type-text">
                        Aidez-nous à amplifier notre message en relayant nos actions, nos campagnes
                        de sensibilisation et nos appels aux dons.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DEVENIR PARTENAIRE --}}
    {{-- ============================================ --}}
    <section class="devenir-partenaire-section">
        <div class="container">
            <div class="devenir-partenaire-card">
                <div class="devenir-partenaire-content">
                    <span class="section-subtitle">Rejoignez-nous</span>
                    <h2 class="section-title text-left">Devenir partenaire</h2>
                    <p class="content-block-text">
                        Vous représentez une organisation, une institution ou une entreprise ?
                        Vous partagez nos valeurs de compassion et de solidarité ?
                        Rejoignez notre réseau de partenaires et contribuez à changer des vies
                        en République Démocratique du Congo.
                    </p>
                    <ul class="check-list">
                        <li><i class="fas fa-check-circle"></i> <span>Visibilité sur notre plateforme et nos supports de communication</span></li>
                        <li><i class="fas fa-check-circle"></i> <span>Rapports détaillés sur l'impact de votre contribution</span></li>
                        <li><i class="fas fa-check-circle"></i> <span>Participation aux visites de terrain et aux événements</span></li>
                        <li><i class="fas fa-check-circle"></i> <span>Attestations de partenariat officielles</span></li>
                    </ul>
                    <div class="devenir-partenaire-actions">
                        <a href="{{ url('/contact') }}" class="btn btn-accent btn-lg">
                            <i class="fas fa-paper-plane"></i> Nous contacter
                        </a>
                        <a href="{{ url('/faire-un-don') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-heart"></i> Faire un don
                        </a>
                    </div>
                </div>
                <div class="devenir-partenaire-visual">
                    <div class="devenir-partenaire-circle">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <p class="devenir-partenaire-slogan">
                        « Compassion avec la population »
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA FINAL --}}
    {{-- ============================================ --}}
    <section class="cta-section-partenaires">
        <div class="cta-decoration cta-decoration-left"></div>
        <div class="cta-decoration cta-decoration-right"></div>

        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title-partenaires">
                    Construisons <span class="highlight-partenaires">ensemble</span> l'avenir
                </h2>
                <p class="cta-text-partenaires">
                    Chaque partenariat est une pierre apportée à l'édifice de la compassion
                    et de la solidarité envers les orphelins, veuves et personnes démunies.
                </p>
                <div class="cta-buttons-partenaires">
                    <a href="{{ url('/contact') }}" class="btn-partenaires-don">
                        <i class="fas fa-handshake"></i> Devenir partenaire
                    </a>
                    <a href="{{ route('projets.index') }}" class="btn-partenaires-outline">
                        <i class="fas fa-list"></i> Voir nos projets
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/partenaires.js') }}"></script>
@endsection