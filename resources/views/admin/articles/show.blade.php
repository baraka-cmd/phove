@extends('layouts.admin')

@section('title', 'Détail de l\'article')

@section('page_title', 'Détail de l\'article')
@section('page_icon', 'eye')
@section('page_subtitle', 'Prévisualisation complète de l\'article avant publication sur le site public.')

@php
    $breadcrumb = ['Articles' => route('admin.articles.index'), 'Détail' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-articles.css') }}">
@endsection

@section('content')

    <div class="article-show-container">

        {{-- Bouton retour --}}
        <a href="{{ route('admin.articles.index') }}" class="article-show-back">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>

        {{-- Carte principale --}}
        <div class="article-show-card">

            {{-- ============================================ --}}
            {{-- BANNIÈRE --}}
            {{-- ============================================ --}}
            <div class="article-show-banner">
                @if($article->image)
                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->titre }}">
                @else
                    <div class="article-show-banner-placeholder">
                        <i class="fas fa-newspaper"></i>
                    </div>
                @endif
                <div class="article-show-banner-overlay">
                    <span class="article-show-banner-categorie article-show-banner-categorie-{{ $article->categorie }}">
                        <i class="fas fa-{{ 
                            $article->categorie === 'actualite' ? 'bolt' : 
                            ($article->categorie === 'projet' ? 'project-diagram' : 
                            ($article->categorie === 'evenement' ? 'calendar-star' : 
                            ($article->categorie === 'rapport' ? 'file-alt' : 
                            ($article->categorie === 'temoignage' ? 'quote-right' : 'bullhorn'))))
                        }}"></i>
                        {{ $article->categorieLibelle() }}
                    </span>
                    <h1 class="article-show-banner-titre">{{ $article->titre }}</h1>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- CORPS --}}
            {{-- ============================================ --}}
            <div class="article-show-body">

                {{-- Métadonnées --}}
                <div class="article-show-metadata">
                    <div class="article-show-metadata-item">
                        <i class="fas fa-user"></i>
                        <span><strong>Auteur :</strong> {{ $article->auteur ? $article->auteur->nom : '—' }}</span>
                    </div>
                    <div class="article-show-metadata-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>
                            <strong>Publié le :</strong> 
                            @if($article->date_publication)
                                {{ $article->date_publication->format('d/m/Y à H:i') }}
                            @elseif($article->statut === 'publie')
                                {{ $article->created_at->format('d/m/Y à H:i') }}
                            @else
                                Non publié
                            @endif
                        </span>
                    </div>
                    <div class="article-show-metadata-item">
                        <i class="fas fa-clock"></i>
                        <span><strong>Mis à jour :</strong> {{ $article->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="article-show-metadata-item">
                        <i class="fas fa-eye"></i>
                        <span><strong>{{ $article->vues }}</strong> vue(s)</span>
                    </div>
                    <div class="article-show-metadata-item">
                        <i class="fas fa-link"></i>
                        <span>
                            <strong>Slug :</strong> 
                            <code class="article-show-slug-code">{{ $article->slug }}</code>
                        </span>
                    </div>
                </div>

                {{-- Badges --}}
                <div class="article-show-badges">
                    {{-- Statut --}}
                    <span class="article-show-badge article-show-badge-statut-{{ $article->statut }}">
                        <i class="fas fa-{{ $article->statut === 'publie' ? 'eye' : 'pen' }}"></i>
                        {{ $article->statut === 'publie' ? 'Article publié' : 'Brouillon' }}
                    </span>

                    {{-- Épinglé --}}
                    @if($article->epingle)
                        <span class="article-show-badge article-show-badge-epingle">
                            <i class="fas fa-thumbtack"></i> Article épinglé
                        </span>
                    @endif

                    {{-- Projet lié --}}
                    @if($article->projet)
                        <span class="article-show-badge article-show-badge-projet-lie">
                            <i class="fas fa-project-diagram"></i> 
                            Projet : 
                            <a href="{{ route('admin.projets.show', $article->projet) }}">
                                {{ Str::limit($article->projet->titre, 60) }}
                            </a>
                        </span>
                    @endif
                </div>

                {{-- Extrait --}}
                @if($article->extrait)
                    <div class="article-show-extrait">
                        {{ $article->extrait }}
                    </div>
                @endif

                {{-- Contenu --}}
                <div class="article-show-contenu">
                    {!! nl2br(e($article->contenu)) !!}
                </div>

            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="article-show-actions">
                <div class="article-show-actions-left">
                    {{-- Publier/Dépublier --}}
                    <form action="{{ route('admin.articles.togglePublier', $article) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-{{ $article->statut === 'publie' ? 'outline' : 'primary' }} btn-sm">
                            <i class="fas fa-{{ $article->statut === 'publie' ? 'eye-slash' : 'eye' }}"></i>
                            {{ $article->statut === 'publie' ? 'Dépublier' : 'Publier' }}
                        </button>
                    </form>

                    {{-- Épingler/Désépingler --}}
                    <form action="{{ route('admin.articles.toggleEpingler', $article) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-outline btn-sm">
                            <i class="fas fa-thumbtack"></i>
                            {{ $article->epingle ? 'Désépingler' : 'Épingler' }}
                        </button>
                    </form>
                </div>

                <div class="article-show-actions-right">
                    {{-- Modifier --}}
                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>

                    {{-- Supprimer --}}
                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cet article ?\n\nCette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="fas fa-trash-alt"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-articles.js') }}"></script>
@endsection