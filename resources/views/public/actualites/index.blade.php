@extends('layouts.public')

@section('title', 'Actualités')

@section('meta_description', 'Restez informés des dernières actualités, projets, événements et communiqués d\'AHD-RDC en République Démocratique du Congo.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/actualites.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-newspaper"></i>
            </div>
            <h1 class="page-header-title">Actualités</h1>
            <p class="page-header-subtitle">
                Restez informés de nos dernières actions, projets, événements et communiqués officiels.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Actualités</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION STATISTIQUES RAPIDES --}}
    {{-- ============================================ --}}
    <section class="actualites-stats-section">
        <div class="container">
            <div class="actualites-stats-grid">
                <div class="actualite-stat-card">
                    <div class="actualite-stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="actualite-stat-info">
                        <span class="actualite-stat-number">{{ $totalArticles }}</span>
                        <span class="actualite-stat-label">Articles publiés</span>
                    </div>
                </div>
                <div class="actualite-stat-card">
                    <div class="actualite-stat-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="actualite-stat-info">
                        <span class="actualite-stat-number">{{ count($categories) }}</span>
                        <span class="actualite-stat-label">Catégories</span>
                    </div>
                </div>
                <div class="actualite-stat-card">
                    <div class="actualite-stat-icon">
                        <i class="fas fa-thumbtack"></i>
                    </div>
                    <div class="actualite-stat-info">
                        <span class="actualite-stat-number">{{ $articlesEpingles->count() }}</span>
                        <span class="actualite-stat-label">Articles épinglés</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- ARTICLES ÉPINGLÉS --}}
    {{-- ============================================ --}}
    @if($articlesEpingles->count() > 0 && !$recherche && !$categorie)
    <section class="actualites-pinned-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">À la une</span>
                <h2 class="section-title">Articles épinglés</h2>
            </div>

            <div class="actualites-grid">
                @foreach($articlesEpingles as $article)
                    @include('public.partials.article-card', ['article' => $article, 'class' => 'article-pinned'])
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================ --}}
    {{-- SECTION FILTRES + LISTE --}}
    {{-- ============================================ --}}
    <section class="actualites-liste-section" id="actualites-liste">
        <div class="container">
            
            {{-- En-tête avec compteur et recherche --}}
            <div class="actualites-liste-header">
                <div class="actualites-liste-count">
                    <i class="fas fa-list-ul"></i>
                    <span><strong>{{ $articles->total() }}</strong> article(s) trouvé(s)</span>
                    @if($recherche || $categorie)
                        <a href="{{ route('actualites.index') }}" class="actualites-reset-filters">
                            <i class="fas fa-times-circle"></i> Réinitialiser
                        </a>
                    @endif
                </div>

                {{-- Recherche --}}
                <form action="{{ route('actualites.index') }}" method="GET" class="actualites-search-form">
                    <div class="actualites-search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                               name="recherche" 
                               value="{{ $recherche }}" 
                               placeholder="Rechercher un article..." 
                               class="actualites-search-input"
                               id="articleSearch">
                    </div>
                </form>
            </div>

            {{-- Filtres par catégories --}}
            <div class="actualites-filtres">
                <a href="{{ route('actualites.index') }}" 
                   class="filtre-btn {{ !$categorie ? 'active' : '' }}">
                    <i class="fas fa-border-all"></i> Tous
                </a>
                @foreach($categories as $value => $label)
                    <a href="{{ route('actualites.index', ['categorie' => $value]) }}" 
                       class="filtre-btn {{ $categorie === $value ? 'active' : '' }}">
                        <i class="fas fa-{{ 
                            $value === 'actualite' ? 'bolt' : 
                            ($value === 'projet' ? 'project-diagram' : 
                            ($value === 'evenement' ? 'calendar-star' : 
                            ($value === 'rapport' ? 'file-alt' : 
                            ($value === 'temoignage' ? 'quote-right' : 'bullhorn'))))
                        }}"></i>
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Grille des articles --}}
            <div class="actualites-grid">
                @forelse($articles as $article)
                    @include('public.partials.article-card', ['article' => $article])
                @empty
                    <div class="actualites-empty">
                        <div class="actualites-empty-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <h3>Aucun article trouvé</h3>
                        <p>
                            Aucun article ne correspond à votre recherche. 
                            Essayez de modifier vos critères ou consultez d'autres catégories.
                        </p>
                        <a href="{{ route('actualites.index') }}" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Voir tous les articles
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($articles->hasPages())
            <div class="actualites-pagination">
                {{ $articles->links() }}
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
                    Suivez <span class="highlight-page">notre action</span>
                </h2>
                <p class="cta-text-page">
                    Abonnez-vous à notre newsletter pour recevoir nos actualités, 
                    rapports et témoignages directement dans votre boîte email.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/contact') }}" class="btn-page-don">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                    <a href="{{ url('/temoignages') }}" class="btn-page-outline">
                        <i class="fas fa-quote-right"></i> Voir les témoignages
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/actualites.js') }}"></script>
@endsection