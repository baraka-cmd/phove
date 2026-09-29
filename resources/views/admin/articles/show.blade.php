@extends('layouts.admin')

@section('title', 'Détail de l\'article | PHOVE')

@section('page_title', 'Détail de l\'article')
@section('page_icon', 'eye')
@section('page_subtitle', 'Prévisualisation complète de l\'article avant publication sur le site public.')

@php
    $breadcrumb = [
        'Articles' => route('admin.articles.index'),
        'Détail' => null,
    ];

    $imageUrl = $article->image
        ? route('media.show', ['path' => ltrim($article->image, '/')])
        : null;
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-galerie.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-articles.css') }}">
@endsection

@section('content')

    <div class="article-show-container">

        {{-- ============================================ --}}
        {{-- BOUTON RETOUR --}}
        {{-- ============================================ --}}
        <a href="{{ route('admin.articles.index') }}" class="article-show-back">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>

        {{-- ============================================ --}}
        {{-- CARTE PRINCIPALE --}}
        {{-- ============================================ --}}
        <div class="article-show-card">

            {{-- ======================================== --}}
            {{-- BANNIÈRE --}}
            {{-- ======================================== --}}
            <div class="article-show-banner">

                @if($imageUrl)
                    <img src="{{ $imageUrl }}"
                         alt="{{ $article->titre }}"
                         loading="lazy"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="article-show-banner-placeholder" style="display: none;">
                        <i class="fas fa-newspaper"></i>
                    </div>
                @else
                    <div class="article-show-banner-placeholder">
                        <i class="fas fa-newspaper"></i>
                    </div>
                @endif

                <div class="article-show-banner-overlay">

                    @php
                        $catIcone = match($article->categorie) {
                            'actualite'  => 'bolt',
                            'projet'     => 'project-diagram',
                            'evenement'  => 'calendar-star',
                            'rapport'    => 'file-alt',
                            'temoignage' => 'quote-right',
                            default      => 'bullhorn',
                        };
                    @endphp

                    <span class="article-show-banner-categorie article-show-banner-categorie-{{ $article->categorie }}">
                        <i class="fas fa-{{ $catIcone }}"></i>
                        {{ $article->categorieLibelle() }}
                    </span>

                    <h1 class="article-show-banner-titre">{{ $article->titre }}</h1>
                </div>
            </div>

            {{-- ======================================== --}}
            {{-- CORPS --}}
            {{-- ======================================== --}}
            <div class="article-show-body">

                {{-- Métadonnées --}}
                <div class="article-show-metadata">

                    <div class="article-show-metadata-item">
                        <i class="fas fa-user"></i>
                        <span>
                            <strong>Auteur :</strong>
                            {{ $article->auteur ? $article->auteur->nom : '—' }}
                        </span>
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
                        <span>
                            <strong>Mis à jour :</strong>
                            {{ $article->updated_at->format('d/m/Y à H:i') }}
                        </span>
                    </div>

                    <div class="article-show-metadata-item">
                        <i class="fas fa-eye"></i>
                        <span>
                            <strong>{{ number_format($article->vues, 0, ',', ' ') }}</strong> vue(s)
                        </span>
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

                    <span class="article-show-badge article-show-badge-statut-{{ $article->statut }}">
                        <i class="fas fa-{{ $article->statut === 'publie' ? 'eye' : 'pen' }}"></i>
                        {{ $article->statut === 'publie' ? 'Article publié' : 'Brouillon' }}
                    </span>

                    @if($article->epingle)
                        <span class="article-show-badge article-show-badge-epingle">
                            <i class="fas fa-thumbtack"></i> Article épinglé
                        </span>
                    @endif

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

            {{-- ======================================== --}}
            {{-- GALERIE MÉDIA --}}
            {{-- ======================================== --}}
            @if($article->medias && $article->medias->count() > 0)
                <div class="article-show-galerie">
                    <div class="article-show-galerie-header">
                        <h3>
                            <i class="fas fa-images"></i>
                            Galerie média
                            <span class="article-show-galerie-count">
                                ({{ $article->medias->count() }} média{{ $article->medias->count() > 1 ? 's' : '' }})
                            </span>
                        </h3>
                    </div>

                    <div class="article-show-galerie-grid">
                        @foreach($article->medias as $media)
                            @php
                                $thumbUrl = $media->miniature
                                    ? route('media.show', ['path' => ltrim($media->miniature, '/')])
                                    : ($media->fichier
                                        ? route('media.show', ['path' => ltrim($media->fichier, '/')])
                                        : null);

                                $fichierUrl = $media->fichier
                                    ? route('media.show', ['path' => ltrim($media->fichier, '/')])
                                    : null;
                            @endphp

                            <a href="{{ $fichierUrl }}"
                               target="_blank"
                               rel="noopener"
                               class="article-show-galerie-item"
                               title="{{ $media->titre }}">

                                <div class="article-show-galerie-thumb">
                                    @if($thumbUrl)
                                        <img src="{{ $thumbUrl }}"
                                             alt="{{ $media->titre }}"
                                             loading="lazy"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="article-show-galerie-placeholder" style="display: none;">
                                            <i class="fas fa-{{ $media->type === 'video' ? 'video' : 'image' }}"></i>
                                        </div>
                                    @else
                                        <div class="article-show-galerie-placeholder">
                                            <i class="fas fa-{{ $media->type === 'video' ? 'video' : 'image' }}"></i>
                                        </div>
                                    @endif

                                    <span class="article-show-galerie-type article-show-galerie-type-{{ $media->type }}">
                                        <i class="fas fa-{{ $media->type === 'video' ? 'play-circle' : 'camera' }}"></i>
                                        {{ $media->type === 'video' ? 'Vidéo' : 'Photo' }}
                                    </span>

                                    <span class="article-show-galerie-overlay">
                                        <i class="fas fa-expand"></i>
                                    </span>
                                </div>

                                <span class="article-show-galerie-title">
                                    {{ Str::limit($media->titre ?: 'Sans titre', 30) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ======================================== --}}
            {{-- ACTIONS --}}
            {{-- ======================================== --}}
            <div class="article-show-actions">

                {{-- Gauche : publier / épingler --}}
                <div class="article-show-actions-left">

                    <form action="{{ route('admin.articles.togglePublier', $article) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit"
                                class="btn btn-{{ $article->statut === 'publie' ? 'outline' : 'primary' }} btn-sm">
                            <i class="fas fa-{{ $article->statut === 'publie' ? 'eye-slash' : 'eye' }}"></i>
                            {{ $article->statut === 'publie' ? 'Dépublier' : 'Publier' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.articles.toggleEpingler', $article) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-outline btn-sm">
                            <i class="fas fa-thumbtack"></i>
                            {{ $article->epingle ? 'Désépingler' : 'Épingler' }}
                        </button>
                    </form>

                </div>

                {{-- Droite : modifier / supprimer --}}
                <div class="article-show-actions-right">

                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>

                    <form action="{{ route('admin.articles.destroy', $article) }}"
                          method="POST"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cet article ?\n\nCette action est irréversible.');">
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
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script src="{{ asset('assets/js/admin-articles.js') }}"></script>
@endsection