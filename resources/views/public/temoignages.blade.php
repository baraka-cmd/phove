@extends('layouts.public')

@section('title', 'Témoignages')

@section('meta_description', 'Découvrez les témoignages des bénéficiaires des projets humanitaires d\'AHD-RDC au Sud-Kivu et Nord-Kivu.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/temoignages.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-quote-right"></i>
            </div>
            <h1 class="page-header-title">Témoignages</h1>
            <p class="page-header-subtitle">
                Les voix de ceux qui ont bénéficié de l'accompagnement d'AHD-RDC dans leur communauté.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Témoignages</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION INTRODUCTION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ils témoignent</span>
                <h2 class="section-title">L'impact de nos actions</h2>
                <p class="section-description">
                    Chaque témoignage est une histoire de vie transformée par l'action humanitaire. 
                    Découvrez les récits de celles et ceux qui ont bénéficié de nos projets 
                    dans les provinces du Sud-Kivu et du Nord-Kivu.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION FILTRE PAR ZONE --}}
    {{-- ============================================ --}}
    <section class="temoignages-filtre-section">
        <div class="container">
            <div class="temoignages-filtre-bar">
                <span class="filtre-label">
                    <i class="fas fa-filter"></i> Filtrer par zone :
                </span>
                <div class="filtre-buttons">
                    <a href="{{ route('temoignages') }}" 
                       class="filtre-zone-btn {{ !$zone ? 'active' : '' }}">
                        <i class="fas fa-globe"></i> Toutes les zones
                    </a>
                    @foreach($zones as $zoneName)
                        <a href="{{ route('temoignages', ['zone' => $zoneName]) }}" 
                           class="filtre-zone-btn {{ $zone === $zoneName ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i> {{ $zoneName }}
                        </a>
                    @endforeach
                </div>
                @if($zone)
                    <a href="{{ route('temoignages') }}" class="filtre-reset">
                        <i class="fas fa-times-circle"></i> Réinitialiser
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- GRILLE DES TÉMOIGNAGES --}}
    {{-- ============================================ --}}
    <section class="temoignages-grid-section">
        <div class="container">
            
            {{-- Compteur --}}
            <div class="temoignages-count">
                <i class="fas fa-quote-left"></i>
                <span><strong>{{ $temoignages->total() }}</strong> témoignage(s) trouvé(s)</span>
            </div>

            {{-- Grille --}}
            <div class="temoignages-grid">
                @forelse($temoignages as $temoignage)
                <blockquote class="temoignage-card">
                    {{-- Icône citation --}}
                    <div class="temoignage-card-quote">
                        <i class="fas fa-quote-right"></i>
                    </div>

                    {{-- Texte --}}
                    <p class="temoignage-card-text">
                        « {{ $temoignage->temoignage }} »
                    </p>

                    {{-- Auteur --}}
                    <div class="temoignage-card-author">
                        <div class="temoignage-card-avatar">
                            <span>{{ Str::upper(Str::substr($temoignage->nom, 0, 1) . Str::substr($temoignage->prenom ?? $temoignage->post_nom ?? '', 0, 1)) }}</span>
                        </div>
                        <div class="temoignage-card-info">
                            <span class="temoignage-card-name">{{ $temoignage->nomComplet() }}</span>
                            <span class="temoignage-card-location">
                                <i class="fas fa-map-marker-alt"></i> {{ $temoignage->localisation }}
                            </span>
                            @if($temoignage->projet)
                            <span class="temoignage-card-projet">
                                <i class="fas fa-project-diagram"></i> 
                                <a href="{{ route('projets.show', $temoignage->projet->slug) }}">
                                    {{ Str::limit($temoignage->projet->titre, 60) }}
                                </a>
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Catégorie --}}
                    <span class="temoignage-card-categorie">
                        {{ $temoignage->categorieLibelle() }}
                    </span>
                </blockquote>
                @empty
                <div class="temoignages-empty">
                    <div class="temoignages-empty-icon">
                        <i class="fas fa-comment-slash"></i>
                    </div>
                    <h3>Aucun témoignage trouvé</h3>
                    <p>
                        Aucun témoignage ne correspond à votre filtre. 
                        Essayez une autre zone ou revenez plus tard.
                    </p>
                    <a href="{{ route('temoignages') }}" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Voir tous les témoignages
                    </a>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($temoignages->hasPages())
            <div class="temoignages-pagination">
                {{ $temoignages->links() }}
            </div>
            @endif

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section-page">
        <div class="container">
            <div class="cta-content-page">
                <h2 class="cta-title-page">
                    Votre <span class="highlight-page">soutien</span> change des vies
                </h2>
                <p class="cta-text-page">
                    Chaque témoignage est le fruit d'une action collective rendue possible 
                    grâce à la générosité de nos donateurs et partenaires.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/faire-un-don') }}" class="btn-page-don">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                    <a href="{{ route('projets.index') }}" class="btn-page-outline">
                        <i class="fas fa-list"></i> Voir nos projets
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/temoignages.js') }}"></script>
@endsection