@extends('layouts.public')

@section('title', 'Recherche : ' . $q)

@section('meta_description', 'Résultats de recherche pour "' . $q . '" sur le site AHD-RDC.')

@section('meta_robots', 'noindex, follow')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/recherche.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-search"></i>
            </div>
            <h1 class="page-header-title">Recherche</h1>
            <p class="page-header-subtitle">
                @if($q)
                    Résultats pour : <strong>"{{ $q }}"</strong>
                @else
                    Entrez un terme pour lancer la recherche.
                @endif
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Recherche</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- BARRE DE RECHERCHE --}}
    {{-- ============================================ --}}
    <section class="recherche-bar-section">
        <div class="container">
            <form action="{{ route('recherche') }}" method="GET" class="recherche-bar-form">
                <div class="recherche-bar-wrapper">
                    <i class="fas fa-search recherche-bar-icon"></i>
                    <input type="text" 
                           name="q" 
                           value="{{ $q }}" 
                           class="recherche-bar-input" 
                           placeholder="Rechercher un projet, un article, un document..." 
                           autocomplete="off"
                           autofocus>
                    <button type="submit" class="recherche-bar-btn">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- RÉSULTATS --}}
    {{-- ============================================ --}}
    <section class="recherche-resultats-section">
        <div class="container">

            @if($q)
                {{-- Compteur global --}}
                <div class="recherche-compteur">
                    <i class="fas fa-list-ul"></i>
                    <span>
                        <strong>{{ $totalResultats }}</strong> résultat(s) trouvé(s) pour <strong>"{{ $q }}"</strong>
                    </span>
                </div>

                {{-- ============================================ --}}
                {{-- PROJETS --}}
                {{-- ============================================ --}}
                @if($projets->count() > 0)
                <div class="recherche-section">
                    <h2 class="recherche-section-title">
                        <i class="fas fa-project-diagram"></i> Projets
                        <span class="recherche-section-count">{{ $projets->total() }}</span>
                    </h2>
                    <div class="recherche-grid">
                        @foreach($projets as $projet)
                        <a href="{{ route('projets.show', $projet->slug) }}" class="recherche-card">
                            <div class="recherche-card-image">
                                <img src="{{ $projet->image_principale ? asset('storage/' . $projet->image_principale) : asset('assets/images/logo-ahd.png') }}" 
                                     alt="{{ $projet->titre }}" 
                                     loading="lazy">
                                <span class="recherche-card-badge projet-badge-{{ $projet->statut }}">
                                    @if($projet->statut === 'en_cours')
                                        <i class="fas fa-spinner fa-pulse"></i> En cours
                                    @else
                                        <i class="fas fa-check-circle"></i> {{ $projet->statutLibelle() }}
                                    @endif
                                </span>
                            </div>
                            <div class="recherche-card-body">
                                <span class="recherche-card-type"><i class="fas fa-tag"></i> Projet</span>
                                <h3 class="recherche-card-title">{!! $projet->titre !!}</h3>
                                <p class="recherche-card-text">{{ Str::limit(strip_tags($projet->description), 120) }}</p>
                                <div class="recherche-card-meta">
                                    <span><i class="fas fa-map-marker-alt"></i> {{ $projet->zone_geographique }}</span>
                                    <span><i class="fas fa-calendar-alt"></i> {{ $projet->date_debut->format('Y') }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @if($projets->hasPages())
                    <div class="recherche-pagination">
                        {{ $projets->appends(['q' => $q])->fragment('projets')->links() }}
                    </div>
                    @endif
                </div>
                @endif

                {{-- ============================================ --}}
                {{-- ARTICLES --}}
                {{-- ============================================ --}}
                @if($articles->count() > 0)
                <div class="recherche-section">
                    <h2 class="recherche-section-title">
                        <i class="fas fa-newspaper"></i> Articles
                        <span class="recherche-section-count">{{ $articles->total() }}</span>
                    </h2>
                    <div class="recherche-list">
                        @foreach($articles as $article)
                        <a href="{{ route('actualites.show', $article->slug) }}" class="recherche-list-card">
                            <div class="recherche-list-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="recherche-list-body">
                                <span class="recherche-card-type"><i class="fas fa-tag"></i> {{ $article->categorieLibelle() }}</span>
                                <h3 class="recherche-list-title">{!! $article->titre !!}</h3>
                                <p class="recherche-list-text">{{ $article->extraitOuDefaut(150) }}</p>
                                <div class="recherche-card-meta">
                                    @if($article->auteur)
                                    <span><i class="fas fa-user-edit"></i> {{ $article->auteur->nom }}</span>
                                    @endif
                                    <span><i class="fas fa-calendar-alt"></i> {{ $article->date_publication ? $article->date_publication->format('d/m/Y') : $article->created_at->format('d/m/Y') }}</span>
                                    <span><i class="fas fa-eye"></i> {{ $article->vues }} vues</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @if($articles->hasPages())
                    <div class="recherche-pagination">
                        {{ $articles->appends(['q' => $q])->fragment('articles')->links() }}
                    </div>
                    @endif
                </div>
                @endif

                {{-- ============================================ --}}
                {{-- DOCUMENTS --}}
                {{-- ============================================ --}}
                @if($documents->count() > 0)
                <div class="recherche-section">
                    <h2 class="recherche-section-title">
                        <i class="fas fa-folder-open"></i> Documents
                        <span class="recherche-section-count">{{ $documents->total() }}</span>
                    </h2>
                    <div class="recherche-list">
                        @foreach($documents as $document)
                        <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank" class="recherche-list-card">
                            <div class="recherche-list-icon recherche-doc-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="recherche-list-body">
                                <span class="recherche-card-type"><i class="fas fa-tag"></i> {{ $document->typeLibelle() }}</span>
                                <h3 class="recherche-list-title">{!! $document->titre !!}</h3>
                                @if($document->description)
                                <p class="recherche-list-text">{{ Str::limit($document->description, 150) }}</p>
                                @endif
                                <div class="recherche-card-meta">
                                    @if($document->date_document)
                                    <span><i class="fas fa-calendar-alt"></i> {{ $document->date_document->format('d/m/Y') }}</span>
                                    @endif
                                    @if($document->taille)
                                    <span><i class="fas fa-weight"></i> {{ $document->tailleLisible() }}</span>
                                    @endif
                                    <span><i class="fas fa-download"></i> {{ $document->telechargements }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @if($documents->hasPages())
                    <div class="recherche-pagination">
                        {{ $documents->appends(['q' => $q])->fragment('documents')->links() }}
                    </div>
                    @endif
                </div>
                @endif

                {{-- ============================================ --}}
                {{-- TÉMOIGNAGES --}}
                {{-- ============================================ --}}
                @if($temoignages->count() > 0)
                <div class="recherche-section">
                    <h2 class="recherche-section-title">
                        <i class="fas fa-quote-right"></i> Témoignages
                        <span class="recherche-section-count">{{ $temoignages->total() }}</span>
                    </h2>
                    <div class="recherche-grid recherche-grid-2">
                        @foreach($temoignages as $temoignage)
                        <div class="recherche-temoignage-card">
                            <div class="recherche-temoignage-quote">
                                <i class="fas fa-quote-right"></i>
                            </div>
                            <p class="recherche-temoignage-text">
                                « {{ Str::limit($temoignage->temoignage, 200) }} »
                            </p>
                            <div class="recherche-temoignage-author">
                                <div class="recherche-temoignage-avatar">
                                    <span>{{ Str::upper(Str::substr($temoignage->nom, 0, 1) . Str::substr($temoignage->prenom ?? '', 0, 1)) }}</span>
                                </div>
                                <div class="recherche-temoignage-info">
                                    <span class="recherche-temoignage-name">{{ $temoignage->nomComplet() }}</span>
                                    <span class="recherche-temoignage-location">
                                        <i class="fas fa-map-marker-alt"></i> {{ $temoignage->localisation }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($temoignages->hasPages())
                    <div class="recherche-pagination">
                        {{ $temoignages->appends(['q' => $q])->fragment('temoignages')->links() }}
                    </div>
                    @endif
                </div>
                @endif

                {{-- Aucun résultat --}}
                @if($totalResultats === 0)
                <div class="recherche-empty">
                    <div class="recherche-empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Aucun résultat trouvé</h3>
                    <p>
                        Aucun résultat ne correspond à votre recherche <strong>"{{ $q }}"</strong>. 
                        Essayez avec d'autres mots-clés ou vérifiez l'orthographe.
                    </p>
                    <div class="recherche-suggestions">
                        <h4><i class="fas fa-lightbulb"></i> Suggestions :</h4>
                        <div class="recherche-suggestions-list">
                            <a href="{{ route('recherche', ['q' => 'santé']) }}">santé</a>
                            <a href="{{ route('recherche', ['q' => 'paix']) }}">paix</a>
                            <a href="{{ route('recherche', ['q' => 'eau']) }}">eau</a>
                            <a href="{{ route('recherche', ['q' => 'éducation']) }}">éducation</a>
                            <a href="{{ route('recherche', ['q' => 'sécurité alimentaire']) }}">sécurité alimentaire</a>
                            <a href="{{ route('recherche', ['q' => 'Minova']) }}">Minova</a>
                        </div>
                    </div>
                </div>
                @endif

            @else
                {{-- Pas de recherche --}}
                <div class="recherche-empty">
                    <div class="recherche-empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Recherchez sur le site</h3>
                    <p>
                        Utilisez la barre de recherche ci-dessus pour trouver des projets, 
                        articles, documents et témoignages.
                    </p>
                </div>
            @endif

        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/recherche.js') }}"></script>
@endsection