@extends('layouts.public')

@section('title', $article->titre)

@section('meta_description', $article->extraitOuDefaut(160))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/actualite-show.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE L'ARTICLE --}}
    {{-- ============================================ --}}
    <section class="article-header">
        <div class="article-header-overlay"></div>
        <div class="container">
            <div class="article-header-content">
                {{-- Badges --}}
                <div class="article-header-badges">
                    <span class="article-header-badge article-badge-{{ $article->categorie }}">
                        <i class="fas fa-{{ 
                            $article->categorie === 'actualite' ? 'bolt' : 
                            ($article->categorie === 'projet' ? 'project-diagram' : 
                            ($article->categorie === 'evenement' ? 'calendar-star' : 
                            ($article->categorie === 'rapport' ? 'file-alt' : 
                            ($article->categorie === 'temoignage' ? 'quote-right' : 'bullhorn'))))
                        }}"></i>
                        {{ $article->categorieLibelle() }}
                    </span>
                    @if($article->epingle)
                    <span class="article-header-badge article-header-pin">
                        <i class="fas fa-thumbtack"></i> Article épinglé
                    </span>
                    @endif
                </div>

                {{-- Titre --}}
                <h1 class="article-header-title">{{ $article->titre }}</h1>

                {{-- Métadonnées --}}
                <div class="article-header-meta">
                    @if($article->auteur)
                    <span class="article-header-meta-item">
                        <i class="fas fa-user-edit"></i>
                        {{ $article->auteur->nom }}
                    </span>
                    @endif
                    <span class="article-header-meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $article->date_publication ? $article->date_publication->format('d/m/Y à H:i') : $article->created_at->format('d/m/Y') }}
                    </span>
                    <span class="article-header-meta-item">
                        <i class="fas fa-eye"></i>
                        {{ $article->vues }} vues
                    </span>
                    <span class="article-header-meta-item">
                        <i class="fas fa-clock"></i>
                        {{ max(1, ceil(str_word_count(strip_tags($article->contenu)) / 200)) }} min de lecture
                    </span>
                </div>

                {{-- Fil d'Ariane --}}
                <nav class="page-breadcrumb">
                    <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="{{ route('actualites.index') }}">Actualités</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>{{ Str::limit($article->titre, 50) }}</span>
                </nav>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CONTENU DE L'ARTICLE --}}
    {{-- ============================================ --}}
    <section class="article-content-section">
        <div class="container">
            <div class="article-content-grid">
                {{-- Contenu principal --}}
                <div class="article-main">
                    {{-- Image --}}
                    @if($article->image)
                    <div class="article-featured-image">
                        <img src="{{ route('media.show', ['path' => $article->image]) }}" 
                             alt="{{ $article->titre }}" 
                             class="article-featured-img"
                             loading="lazy"
                             onerror="this.style.display='none';">
                    </div>
                    @endif

                    {{-- Contenu --}}
                    <div class="article-body">
                        {!! nl2br(e($article->contenu)) !!}
                    </div>

                    {{-- Projet lié --}}
                    @if($article->projet)
                    <div class="article-projet-lie">
                        <h3 class="article-projet-title">
                            <i class="fas fa-project-diagram"></i> Projet lié à cet article
                        </h3>
                        <a href="{{ route('projets.show', $article->projet->slug) }}" class="article-projet-card">
                            <div class="article-projet-info">
                                <span class="article-projet-domaine">{{ $article->projet->domaineLibelle() }}</span>
                                <span class="article-projet-name">{{ $article->projet->titre }}</span>
                                <span class="article-projet-zone">
                                    <i class="fas fa-map-marker-alt"></i> {{ $article->projet->zone_geographique }}
                                </span>
                            </div>
                            <div class="article-projet-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="article-actions">
                        <button class="article-action-btn btn-share" 
                                data-title="{{ $article->titre }}" 
                                data-text="{{ $article->extraitOuDefaut(150) }}"
                                title="Partager cet article">
                            <i class="fas fa-share-alt"></i> Partager
                        </button>
                        <button class="article-action-btn btn-print" title="Imprimer">
                            <i class="fas fa-print"></i> Imprimer
                        </button>
                        <a href="{{ route('actualites.index') }}" class="article-action-btn" title="Retour aux actualités">
                            <i class="fas fa-arrow-left"></i> Retour aux actualités
                        </a>
                    </div>
                </div>

                {{-- Sidebar --}}
                <aside class="article-sidebar">
                    {{-- Auteur --}}
                    @if($article->auteur)
                    <div class="article-sidebar-card">
                        <h3 class="article-sidebar-title">
                            <i class="fas fa-user-circle"></i> Auteur
                        </h3>
                        <div class="article-author">
                            <div class="article-author-avatar">
                                <span>{{ Str::upper(Str::substr($article->auteur->nom, 0, 2)) }}</span>
                            </div>
                            <div class="article-author-info">
                                <span class="article-author-name">{{ $article->auteur->nom }}</span>
                                <span class="article-author-role">{{ $article->auteur->roleLibelle() ?? 'Administrateur' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Détails de l'article --}}
                    <div class="article-sidebar-card">
                        <h3 class="article-sidebar-title">
                            <i class="fas fa-info-circle"></i> Détails
                        </h3>
                        <div class="article-details-list">
                            <div class="article-detail-item">
                                <span class="detail-label"><i class="fas fa-tag"></i> Catégorie</span>
                                <span class="detail-value">{{ $article->categorieLibelle() }}</span>
                            </div>
                            <div class="article-detail-item">
                                <span class="detail-label"><i class="fas fa-calendar-alt"></i> Publié le</span>
                                <span class="detail-value">{{ $article->date_publication ? $article->date_publication->format('d/m/Y') : $article->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="article-detail-item">
                                <span class="detail-label"><i class="fas fa-eye"></i> Vues</span>
                                <span class="detail-value">{{ $article->vues }}</span>
                            </div>
                            <div class="article-detail-item">
                                <span class="detail-label"><i class="fas fa-clock"></i> Lecture</span>
                                <span class="detail-value">{{ max(1, ceil(str_word_count(strip_tags($article->contenu)) / 200)) }} min</span>
                            </div>
                        </div>
                    </div>

                    {{-- Articles connexes --}}
                    @if(isset($articlesConnexes) && $articlesConnexes->count() > 0)
                    <div class="article-sidebar-card">
                        <h3 class="article-sidebar-title">
                            <i class="fas fa-link"></i> Articles connexes
                        </h3>
                        <div class="article-connexes-list">
                            @foreach($articlesConnexes as $connexe)
                            <a href="{{ route('actualites.show', $connexe->slug) }}" class="article-connexe-item">
                                <span class="article-connexe-title">{{ $connexe->titre }}</span>
                                <span class="article-connexe-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ $connexe->date_publication ? $connexe->date_publication->format('d/m/Y') : $connexe->created_at->format('d/m/Y') }}
                                </span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section-page">
        <div class="container">
            <div class="cta-content-page">
                <h2 class="cta-title-page">
                    Restez <span class="highlight-page">informés</span>
                </h2>
                <p class="cta-text-page">
                    Ne manquez aucune actualité de PHOVE. Suivez nos actions, nos projets 
                    et nos événements en vous abonnant à notre newsletter.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ route('actualites.index') }}" class="btn-page-don">
                        <i class="fas fa-newspaper"></i> Toutes les actualités
                    </a>
                    <a href="{{ url('/contact') }}" class="btn-page-outline">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/actualite-show.js') }}"></script>
@endsection