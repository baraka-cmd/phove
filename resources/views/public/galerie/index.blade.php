@extends('layouts.public')

@section('title', 'Galerie Média | PHOVE')

@section('meta_description', 'Découvrez en images et en vidéos les actions humanitaires de PHOVE au Nord-Kivu : projets, distributions, formations, événements et bien plus encore.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/public-galerie.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-images"></i>
            </div>
            <h1 class="page-header-title">Galerie Média</h1>
            <p class="page-header-subtitle">
                Découvrez en images et en vidéos les actions humanitaires menées par PHOVE
                auprès des orphelins, veuves et personnes démunies du Nord-Kivu.
            </p>
            <nav class="page-breadcrumb" aria-label="Fil d'Ariane">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Galerie</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- STATISTIQUES RAPIDES --}}
    {{-- ============================================ --}}
    <section class="galerie-stats-section">
        <div class="container">
            <div class="galerie-stats-grid">

                {{-- Total médias --}}
                <div class="galerie-stat-card">
                    <div class="galerie-stat-icon">
                        <i class="fas fa-photo-video"></i>
                    </div>
                    <div class="galerie-stat-info">
                        <span class="galerie-stat-number">{{ number_format($totalMedias, 0, ',', ' ') }}</span>
                        <span class="galerie-stat-label">Médias au total</span>
                    </div>
                </div>

                {{-- Photos --}}
                <div class="galerie-stat-card">
                    <div class="galerie-stat-icon stat-icon-images">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="galerie-stat-info">
                        <span class="galerie-stat-number">{{ number_format($totalImages, 0, ',', ' ') }}</span>
                        <span class="galerie-stat-label">Photos</span>
                    </div>
                </div>

                {{-- Vidéos --}}
                <div class="galerie-stat-card">
                    <div class="galerie-stat-icon stat-icon-videos">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="galerie-stat-info">
                        <span class="galerie-stat-number">{{ number_format($totalVideos, 0, ',', ' ') }}</span>
                        <span class="galerie-stat-label">Vidéos</span>
                    </div>
                </div>

                {{-- Albums --}}
                <div class="galerie-stat-card">
                    <div class="galerie-stat-icon stat-icon-albums">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="galerie-stat-info">
                        <span class="galerie-stat-number">{{ number_format($albums->count(), 0, ',', ' ') }}</span>
                        <span class="galerie-stat-label">Albums</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- GALERIE + FILTRES --}}
    {{-- ============================================ --}}
    <section class="galerie-section" id="galerie">
        <div class="container">

            {{-- ---------------------------------------- --}}
            {{-- BARRE DE FILTRES --}}
            {{-- ---------------------------------------- --}}
            <form action="{{ route('galerie.index') }}" method="GET" class="galerie-filtres" id="galerieFiltres">

                <div class="galerie-filtres-group">

                    {{-- Recherche textuelle --}}
                    <div class="filtre-group filtre-search">
                        <i class="fas fa-search"></i>
                        <input type="text"
                               name="recherche"
                               value="{{ request('recherche') }}"
                               placeholder="Rechercher un média, un album, un lieu..."
                               class="filtre-input"
                               autocomplete="off">
                    </div>

                    {{-- Filtre type --}}
                    <select name="type" class="filtre-select" onchange="this.form.submit()">
                        <option value="">Tous les types</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Filtre album --}}
                    <select name="album" class="filtre-select" onchange="this.form.submit()">
                        <option value="">Tous les albums</option>
                        @foreach($albums as $album)
                            <option value="{{ $album }}" {{ request('album') === $album ? 'selected' : '' }}>
                                {{ Str::limit($album, 40) }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Filtre projet --}}
                    @if($projets->count() > 0)
                        <select name="projet_id" class="filtre-select" onchange="this.form.submit()">
                            <option value="">Tous les projets</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ (int) request('projet_id') === $projet->id ? 'selected' : '' }}>
                                    {{ Str::limit($projet->titre, 40) }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    {{-- Filtre ordre --}}
                    <select name="ordre" class="filtre-select" onchange="this.form.submit()">
                        @foreach($ordres as $key => $label)
                            <option value="{{ $key }}" {{ request('ordre', 'recent') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Bouton réinitialiser --}}
                    @if(request()->hasAny(['recherche', 'type', 'album', 'projet_id']) || request('ordre') !== 'recent')
                        <a href="{{ route('galerie.index') }}" class="galerie-reset-btn" title="Réinitialiser les filtres">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif

                    {{-- Bouton filtrer (visible sur mobile) --}}
                    <button type="submit" class="galerie-submit-btn">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>

                </div>
            </form>

            {{-- ---------------------------------------- --}}
            {{-- COMPTEUR DE RÉSULTATS --}}
            {{-- ---------------------------------------- --}}
            <div class="galerie-count">
                <i class="fas fa-th"></i>
                <span>
                    <strong>{{ number_format($medias->total(), 0, ',', ' ') }}</strong>
                    média(s) trouvé(s)
                    @if($medias->hasPages())
                        — page {{ $medias->currentPage() }} sur {{ $medias->lastPage() }}
                    @endif
                </span>
            </div>

            {{-- ---------------------------------------- --}}
            {{-- GRILLE DES MÉDIAS --}}
            {{-- ---------------------------------------- --}}
            @if($medias->count() > 0)

                <div class="galerie-grid" id="galerieGrid">
                    @foreach($medias as $media)
                        @include('public.partials.media-card', ['media' => $media])
                    @endforeach
                </div>

                {{-- ---------------------------------------- --}}
                {{-- PAGINATION --}}
                {{-- ---------------------------------------- --}}
                @if($medias->hasPages())
                    <div class="galerie-pagination">
                        {{ $medias->links('pagination.custom') }}
                    </div>
                @endif

            @else

                {{-- ---------------------------------------- --}}
                {{-- ÉTAT VIDE --}}
                {{-- ---------------------------------------- --}}
                <div class="galerie-empty">
                    <i class="fas fa-images"></i>
                    <h3>Aucun média trouvé</h3>

                    @if(request()->hasAny(['recherche', 'type', 'album', 'projet_id']))
                        <p>Aucun média ne correspond à vos critères de recherche.</p>
                        <a href="{{ route('galerie.index') }}" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
                        </a>
                    @else
                        <p>La galerie est vide pour le moment. Revenez bientôt.</p>
                    @endif
                </div>

            @endif

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- LIGHTBOX (modale plein écran) --}}
    {{-- ============================================ --}}
    <div class="media-lightbox" id="mediaLightbox" aria-hidden="true" role="dialog">

        {{-- Bouton fermer --}}
        <button type="button" class="media-lightbox-close" id="lightboxClose" aria-label="Fermer">
            <i class="fas fa-times"></i>
        </button>

        {{-- Navigation --}}
        <button type="button" class="media-lightbox-nav prev" id="lightboxPrev" aria-label="Précédent">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" class="media-lightbox-nav next" id="lightboxNext" aria-label="Suivant">
            <i class="fas fa-chevron-right"></i>
        </button>

        {{-- Contenu --}}
        <div class="media-lightbox-content">

            <img id="lightboxImage" src="" alt="" style="display: none;">
            <video id="lightboxVideo" controls style="display: none;" preload="metadata">
                <source src="" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
            </video>

            <div class="media-lightbox-info">
                <h4 id="lightboxTitle"></h4>
                <p id="lightboxDescription"></p>
                <span id="lightboxCounter" class="media-lightbox-counter"></span>
            </div>

        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SECTION CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">
                    Soutenez <span class="highlight">nos actions</span>
                </h2>
                <p class="cta-text">
                    Chaque contribution nous permet de continuer notre mission humanitaire
                    auprès des orphelins, veuves et personnes démunies de la RD Congo.
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
    <script src="{{ asset('assets/js/public-galerie.js') }}"></script>
@endsection