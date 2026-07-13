@extends('layouts.public')

@section('title', 'Nos Projets')

@section('meta_description', 'Découvrez tous les projets humanitaires d\'AHD-RDC au Sud-Kivu et Nord-Kivu : santé, éducation, protection, eau, sécurité alimentaire, environnement et paix.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/projets-index.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <h1 class="page-header-title">Nos Projets</h1>
            <p class="page-header-subtitle">
                Découvrez l'ensemble de nos actions humanitaires menées sur le terrain pour le développement des communautés vulnérables.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Nos Projets</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION STATISTIQUES RAPIDES --}}
    {{-- ============================================ --}}
    <section class="projets-stats-section">
        <div class="container">
            <div class="projets-stats-grid">
                <div class="projet-stat-card">
                    <div class="projet-stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="projet-stat-info">
                        <span class="projet-stat-number">{{ $totalProjets }}</span>
                        <span class="projet-stat-label">Projets au total</span>
                    </div>
                </div>
                <div class="projet-stat-card">
                    <div class="projet-stat-icon stat-icon-progress">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <div class="projet-stat-info">
                        <span class="projet-stat-number">{{ $projetsEnCours }}</span>
                        <span class="projet-stat-label">Projets en cours</span>
                    </div>
                </div>
                <div class="projet-stat-card">
                    <div class="projet-stat-icon stat-icon-done">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="projet-stat-info">
                        <span class="projet-stat-number">{{ $projetsTermines }}</span>
                        <span class="projet-stat-label">Projets terminés</span>
                    </div>
                </div>
                <div class="projet-stat-card">
                    <div class="projet-stat-icon stat-icon-zones">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="projet-stat-info">
                        <span class="projet-stat-number">{{ $zones->count() }}</span>
                        <span class="projet-stat-label">Zones d'intervention</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION ZONES D'INTERVENTION --}}
    {{-- ============================================ --}}
    <section class="zones-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Où nous agissons</span>
                <h2 class="section-title">Nos zones d'intervention</h2>
                <p class="section-description">
                    Cliquez sur une zone pour filtrer les projets correspondants.
                </p>
            </div>

            <div class="zones-grid">
                @foreach($zones as $zoneName)
                    @php
                        $zoneIcon = 'minova';
                        $zoneProvince = 'Sud-Kivu';
                        
                        if (stripos($zoneName, 'kirotshe') !== false) {
                            $zoneIcon = 'kirotshe';
                            $zoneProvince = 'Nord-Kivu';
                        } elseif (stripos($zoneName, 'kalehe') !== false) {
                            $zoneIcon = 'kalehe';
                            $zoneProvince = 'Sud-Kivu';
                        } elseif (stripos($zoneName, 'goma') !== false) {
                            $zoneIcon = 'goma';
                            $zoneProvince = 'Nord-Kivu';
                        }
                        
                        $zoneProjetsCount = $zonesStats[$zoneName] ?? 0;
                        $zoneProjetsEnCours = \App\Models\Projet::where('publier', true)
                            ->where('zone_geographique', 'like', '%' . $zoneName . '%')
                            ->where('statut', 'en_cours')
                            ->count();
                    @endphp

                    @include('public.partials.zone-card', [
                        'zoneNom' => $zoneName,
                        'zoneIcon' => $zoneIcon,
                        'zoneProvince' => $zoneProvince,
                        'zoneProjetsCount' => $zoneProjetsCount,
                        'zoneProjetsEnCours' => $zoneProjetsEnCours,
                        'active' => $zone === $zoneName,
                    ])
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION FILTRES + LISTE PROJETS --}}
    {{-- ============================================ --}}
    <section class="projets-liste-section" id="projets-liste">
        <div class="container">
            
            {{-- En-tête avec compteur et contrôles --}}
            <div class="projets-liste-header">
                <div class="projets-liste-count">
                    <i class="fas fa-list-ul"></i>
                    <span><strong>{{ $projets->total() }}</strong> projet(s) trouvé(s)</span>
                    @if($recherche || $domaine || $zone || $statut || $annee)
                        <a href="{{ route('projets.index') }}" class="projets-reset-filters">
                            <i class="fas fa-times-circle"></i> Réinitialiser les filtres
                        </a>
                    @endif
                </div>
                <div class="projets-liste-controls">
                    <button class="projets-view-btn {{ request()->cookie('projets_view', 'grid') === 'grid' ? 'active' : '' }}" 
                            data-view="grid" title="Vue en grille">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="projets-view-btn {{ request()->cookie('projets_view', 'grid') === 'list' ? 'active' : '' }}" 
                            data-view="list" title="Vue en liste">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            {{-- Barre de filtres --}}
            <div class="projets-filtres">
                <form action="{{ route('projets.index') }}" method="GET" id="projetsFilters" class="projets-filtres-form">
                    {{-- Recherche textuelle --}}
                    <div class="filtre-group filtre-search">
                        <i class="fas fa-search filtre-search-icon"></i>
                        <input type="text" 
                               name="recherche" 
                               value="{{ $recherche }}" 
                               placeholder="Rechercher un projet..." 
                               class="filtre-input filtre-input-search"
                               id="projetSearch">
                    </div>

                    {{-- Filtre Domaine --}}
                    <div class="filtre-group">
                        <select name="domaine" class="filtre-select" id="filtreDomaine">
                            <option value="">Tous les domaines</option>
                            @foreach($domaines as $value => $label)
                                <option value="{{ $value }}" {{ $domaine === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtre Zone --}}
                    <div class="filtre-group">
                        <select name="zone" class="filtre-select" id="filtreZone">
                            <option value="">Toutes les zones</option>
                            @foreach($zones as $zoneName)
                                <option value="{{ $zoneName }}" {{ $zone === $zoneName ? 'selected' : '' }}>
                                    {{ $zoneName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtre Statut --}}
                    <div class="filtre-group">
                        <select name="statut" class="filtre-select" id="filtreStatut">
                            <option value="">Tous les statuts</option>
                            @foreach($statuts as $value => $label)
                                <option value="{{ $value }}" {{ $statut === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtre Année --}}
                    <div class="filtre-group">
                        <select name="annee" class="filtre-select" id="filtreAnnee">
                            <option value="">Toutes les années</option>
                            @foreach($annees as $anneeVal)
                                <option value="{{ $anneeVal }}" {{ $annee == $anneeVal ? 'selected' : '' }}>
                                    {{ $anneeVal }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bouton filtrer --}}
                    <button type="submit" class="btn btn-primary btn-sm filtre-btn-submit">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </form>

                {{-- Filtres actifs --}}
                @if($recherche || $domaine || $zone || $statut || $annee)
                <div class="projets-filtres-actifs">
                    <span class="filtres-actifs-label">Filtres actifs :</span>
                    @if($recherche)
                        <span class="filtre-actif-tag">
                            Recherche : "{{ $recherche }}"
                            <a href="{{ route('projets.index', array_merge(request()->except('recherche'), ['page' => null])) }}" title="Retirer">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if($domaine)
                        <span class="filtre-actif-tag">
                            {{ $domaines[$domaine] ?? $domaine }}
                            <a href="{{ route('projets.index', array_merge(request()->except('domaine'), ['page' => null])) }}" title="Retirer">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if($zone)
                        <span class="filtre-actif-tag">
                            Zone : {{ $zone }}
                            <a href="{{ route('projets.index', array_merge(request()->except('zone'), ['page' => null])) }}" title="Retirer">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if($statut)
                        <span class="filtre-actif-tag">
                            {{ $statuts[$statut] ?? $statut }}
                            <a href="{{ route('projets.index', array_merge(request()->except('statut'), ['page' => null])) }}" title="Retirer">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                    @if($annee)
                        <span class="filtre-actif-tag">
                            Année : {{ $annee }}
                            <a href="{{ route('projets.index', array_merge(request()->except('annee'), ['page' => null])) }}" title="Retirer">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif
                </div>
                @endif
            </div>

            {{-- ============================================ --}}
            {{-- GRILLE DES PROJETS --}}
            {{-- ============================================ --}}
            <div class="projets-grid" id="projetsGrid">
                @forelse($projets as $projet)
                    @include('public.partials.projet-card', ['projet' => $projet])
                @empty
                    <div class="projets-empty">
                        <div class="projets-empty-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Aucun projet trouvé</h3>
                        <p>
                            Aucun projet ne correspond à vos critères de recherche. 
                            Essayez de modifier vos filtres ou de réinitialiser la recherche.
                        </p>
                        <a href="{{ route('projets.index') }}" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($projets->hasPages())
            <div class="projets-pagination">
                {{ $projets->links() }}
            </div>
            @endif

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">
                    Soutenez <span class="highlight">nos projets</span>
                </h2>
                <p class="cta-text">
                    Chaque contribution nous permet de continuer notre action humanitaire 
                    auprès des communautés vulnérables de l'Est de la RD Congo.
                </p>
                <div class="cta-buttons">
                    <a href="{{ url('/faire-un-don') }}" class="btn btn-accent btn-lg">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                    <a href="{{ url('/devenir-partenaire') }}" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-handshake"></i> Devenir partenaire
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/projets-index.js') }}"></script>
@endsection