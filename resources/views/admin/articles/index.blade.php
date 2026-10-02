@extends('layouts.admin')

@section('title', 'Gestion des articles')

@section('page_title', 'Articles')
@section('page_icon', 'newspaper')
@section('page_subtitle', 'Gérez les articles et actualités : création, publication, épinglage.')

@php
    $breadcrumb = [ 'Articles' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-articles.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="articles-stats-grid">
        <div class="article-stat-card">
            <div class="article-stat-icon"><i class="fas fa-file-alt"></i></div>
            <div class="article-stat-content">
                <div class="article-stat-value">{{ $totalArticles }}</div>
                <div class="article-stat-label">Total articles</div>
            </div>
        </div>
        <div class="article-stat-card">
            <div class="article-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="article-stat-content">
                <div class="article-stat-value">{{ $articlesPublies }}</div>
                <div class="article-stat-label">Publiés</div>
            </div>
        </div>
        <div class="article-stat-card">
            <div class="article-stat-icon"><i class="fas fa-pen"></i></div>
            <div class="article-stat-content">
                <div class="article-stat-value">{{ $articlesBrouillons }}</div>
                <div class="article-stat-label">Brouillons</div>
            </div>
        </div>
        <div class="article-stat-card">
            <div class="article-stat-icon"><i class="fas fa-thumbtack"></i></div>
            <div class="article-stat-content">
                <div class="article-stat-value">{{ $articlesEpingles }}</div>
                <div class="article-stat-label">Épinglés</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.articles.index') }}" method="GET" class="articles-toolbar">
        <div class="articles-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="articleSearch" value="{{ request('recherche') }}" 
                   class="articles-search-input" placeholder="Rechercher un article...">
        </div>

        <div class="articles-filtres">
            <select name="categorie" class="articles-filtre-select" onchange="this.form.submit()">
                <option value="">Toutes les catégories</option>
                <option value="actualite" {{ request('categorie') === 'actualite' ? 'selected' : '' }}>Actualité</option>
                <option value="projet" {{ request('categorie') === 'projet' ? 'selected' : '' }}>Projet</option>
                <option value="evenement" {{ request('categorie') === 'evenement' ? 'selected' : '' }}>Événement</option>
                <option value="rapport" {{ request('categorie') === 'rapport' ? 'selected' : '' }}>Rapport</option>
                <option value="temoignage" {{ request('categorie') === 'temoignage' ? 'selected' : '' }}>Témoignage</option>
                <option value="communique" {{ request('categorie') === 'communique' ? 'selected' : '' }}>Communiqué</option>
            </select>

            <select name="statut" class="articles-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="publie" {{ request('statut') === 'publie' ? 'selected' : '' }}>Publié</option>
                <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                <option value="archive" {{ request('statut') === 'archive' ? 'selected' : '' }}>Archivé</option>
            </select>
        </div>

        @if(request('recherche') || request('categorie') || request('statut'))
            <a href="{{ route('admin.articles.index') }}" class="articles-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.articles.create') }}" class="articles-add-btn">
            <i class="fas fa-plus-circle"></i> Nouvel article
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES ARTICLES --}}
    {{-- ============================================ --}}
    <div class="articles-table-card">
        @if($articles->count() > 0)
        <div class="table-responsive">
            <table class="table-articles">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Date publication</th>
                        <th>Statut</th>
                        <th>Vues</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($articles as $article)
                    <tr>
                        {{-- Article --}}
                        <td>
                            <div class="article-cell-titre">
                                <div class="article-cell-image">
                                    @if($article->image)
                                        <img src="{{ img_url($article->image) }}"
                                            alt="{{ $article->titre }}"
                                            loading="lazy">
                                    @else
                                        <div class="article-cell-image-placeholder">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="article-cell-info">
                                    <div class="article-cell-nom">{{ Str::limit($article->titre, 60) }}</div>
                                    <div class="article-cell-meta">
                                        @if($article->epingle)
                                        <span class="article-pin-badge"><i class="fas fa-thumbtack"></i> Épinglé</span>
                                        @endif
                                        <span><i class="fas fa-calendar-alt"></i> {{ $article->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Catégorie --}}
                        <td>
                            <span class="article-cat-badge article-cat-{{ $article->categorie }}">
                                <i class="fas fa-{{ 
                                    $article->categorie === 'actualite' ? 'bolt' : 
                                    ($article->categorie === 'projet' ? 'project-diagram' : 
                                    ($article->categorie === 'evenement' ? 'calendar-star' : 
                                    ($article->categorie === 'rapport' ? 'file-alt' : 
                                    ($article->categorie === 'temoignage' ? 'quote-right' : 'bullhorn'))))
                                }}"></i>
                                {{ $article->categorieLibelle() }}
                            </span>
                        </td>

                        {{-- Auteur --}}
                        <td>
                            <span style="font-size: 13px;">
                                {{ $article->auteur ? $article->auteur->nom : '—' }}
                            </span>
                        </td>

                        {{-- Date publication --}}
                        <td>
                            <span style="font-size: 13px; white-space: nowrap;">
                                {{ $article->date_publication ? $article->date_publication->format('d/m/Y H:i') : ($article->statut === 'publie' ? $article->created_at->format('d/m/Y H:i') : '—') }}
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td>
                            <span class="article-statut-badge article-statut-{{ $article->statut }}">
                                <i class="fas fa-{{ $article->statut === 'publie' ? 'eye' : ($article->statut === 'brouillon' ? 'pen' : 'archive') }}"></i>
                                {{ $article->statut === 'publie' ? 'Publié' : ($article->statut === 'brouillon' ? 'Brouillon' : 'Archivé') }}
                            </span>
                        </td>

                        {{-- Vues --}}
                        <td>
                            <span class="article-vues">
                                <i class="fas fa-eye"></i> {{ $article->vues }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="article-actions">
                                <a href="{{ route('admin.articles.show', $article) }}" class="article-action-btn view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.articles.edit', $article) }}" class="article-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.articles.togglePublier', $article) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="article-action-btn publish" 
                                            title="{{ $article->statut === 'publie' ? 'Dépublier' : 'Publier' }}">
                                        <i class="fas fa-{{ $article->statut === 'publie' ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.articles.toggleEpingler', $article) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="article-action-btn pin" 
                                            title="{{ $article->epingle ? 'Désépingler' : 'Épingler' }}">
                                        <i class="fas fa-thumbtack" style="color: {{ $article->epingle ? 'var(--color-accent)' : '' }};"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="article-action-btn delete btn-delete-article" 
                                            data-titre="{{ $article->titre }}" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($articles->hasPages())
        <div class="articles-pagination">
            {{ $articles->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="articles-empty">
            <div class="articles-empty-icon"><i class="fas fa-newspaper"></i></div>
            <h4>Aucun article trouvé</h4>
            <p>Aucun article ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.articles.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-articles.js') }}"></script>
@endsection