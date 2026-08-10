@extends('layouts.admin')

@section('title', 'Galerie Médias')

@section('page_title', 'Galerie Médias')
@section('page_icon', 'images')
@section('page_subtitle', 'Gérez la photothèque et vidéothèque : upload, organisation par albums, liaison aux projets.')

@php
    $breadcrumb = ['Galerie Médias' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-medias.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="medias-stats-grid">
        <div class="medias-stat-card">
            <div class="medias-stat-icon">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="medias-stat-content">
                <div class="medias-stat-value">{{ number_format($totalMedias, 0, ',', ' ') }}</div>
                <div class="medias-stat-label">Total médias</div>
                <div class="medias-stat-sub">{{ $totalImages }} images · {{ $totalVideos }} vidéos</div>
            </div>
        </div>
        <div class="medias-stat-card">
            <div class="medias-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="medias-stat-content">
                <div class="medias-stat-value">{{ number_format($totalPublies, 0, ',', ' ') }}</div>
                <div class="medias-stat-label">Publiés</div>
                <div class="medias-stat-sub">{{ $totalPrives }} privés</div>
            </div>
        </div>
        <div class="medias-stat-card">
            <div class="medias-stat-icon">
                <i class="fas fa-hdd"></i>
            </div>
            <div class="medias-stat-content">
                <div class="medias-stat-value">{{ $espaceDisqueFormate }}</div>
                <div class="medias-stat-label">Espace disque</div>
                <div class="medias-stat-sub">Stockage utilisé</div>
            </div>
        </div>
        <div class="medias-stat-card">
            <div class="medias-stat-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="medias-stat-content">
                <div class="medias-stat-value">{{ $albums->count() }}</div>
                <div class="medias-stat-label">Albums</div>
                <div class="medias-stat-sub">Dossiers virtuels</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.medias.index') }}" method="GET" class="medias-toolbar">
        <div class="medias-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="mediaSearch" value="{{ request('recherche') }}" 
                   class="medias-search-input" placeholder="Rechercher un média...">
        </div>

        <div class="medias-filtres">
            <select name="type" class="medias-filtre-select">
                <option value="">Tous les types</option>
                <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Vidéos</option>
            </select>

            <select name="album" class="medias-filtre-select">
                <option value="">Tous les albums</option>
                @foreach($albums as $album)
                    <option value="{{ $album }}" {{ request('album') === $album ? 'selected' : '' }}>{{ $album }}</option>
                @endforeach
            </select>

            <select name="projet_id" class="medias-filtre-select">
                <option value="">Tous les projets</option>
                @foreach($projets as $projet)
                    <option value="{{ $projet->id }}" {{ request('projet_id') == $projet->id ? 'selected' : '' }}>
                        {{ Str::limit($projet->titre, 40) }}
                    </option>
                @endforeach
            </select>

            <select name="publier" class="medias-filtre-select">
                <option value="">Tous les statuts</option>
                <option value="oui" {{ request('publier') === 'oui' ? 'selected' : '' }}>Publiés</option>
                <option value="non" {{ request('publier') === 'non' ? 'selected' : '' }}>Privés</option>
            </select>

            <select name="ordre" class="medias-filtre-select">
                @foreach($ordres as $key => $label)
                    <option value="{{ $key }}" {{ request('ordre', 'recent') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @if(request('recherche') || request('type') || request('album') || request('projet_id') || request('publier') || request('ordre') !== 'recent')
            <a href="{{ route('admin.medias.index') }}" class="medias-reset-btn" title="Réinitialiser les filtres">
                <i class="fas fa-times"></i>
            </a>
        @endif

        {{-- Switch vue Grille / Liste --}}
        <div class="medias-view-switch">
            <button type="button" class="medias-view-btn active" data-view="grid" title="Vue grille">
                <i class="fas fa-th"></i>
            </button>
            <button type="button" class="medias-view-btn" data-view="list" title="Vue liste">
                <i class="fas fa-list"></i>
            </button>
        </div>

        <a href="{{ route('admin.medias.create') }}" class="medias-add-btn">
            <i class="fas fa-cloud-upload-alt"></i> Ajouter des médias
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- CHECKBOX "TOUT SÉLECTIONNER" --}}
    {{-- ============================================ --}}
    @if($medias->count() > 0)
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding: 10px 16px; background: #fff; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #1a1a1a;">
            <input type="checkbox" id="selectAllMedias" style="width: 18px; height: 18px; accent-color: var(--color-primary); cursor: pointer;">
            Tout sélectionner
        </label>
        <span style="font-size: 12px; color: var(--color-text-muted);">
            {{ $medias->total() }} média(s) trouvé(s)
        </span>
    </div>
    @endif

    {{-- ============================================ --}}
    {{-- GRILLE DE MÉDIAS --}}
    {{-- ============================================ --}}
    <div id="mediasGrid" class="medias-grid">
        @forelse($medias as $index => $media)
            <div class="media-card" 
                 data-media-id="{{ $media->id }}"
                 data-media-type="{{ $media->type }}"
                 data-media-url="{{ $media->url() }}"
                 data-media-titre="{{ $media->titre }}"
                 data-media-description="{{ $media->description }}"
                 style="--index: {{ $index }};">
                
                {{-- Checkbox de sélection --}}
                <div class="media-card-checkbox" title="Sélectionner ce média">
                    <input type="checkbox" class="media-card-checkbox-input" data-media-id="{{ $media->id }}" style="display: none;">
                    <i class="fas fa-check" style="font-size: 11px; display: none;"></i>
                </div>

                {{-- Badge publication --}}
                <span class="media-card-publish-badge {{ $media->publier ? 'publie' : 'prive' }}">
                    <i class="fas fa-{{ $media->publier ? 'globe' : 'lock' }}"></i>
                    {{ $media->publier ? 'Public' : 'Privé' }}
                </span>

                {{-- Miniature --}}
                <div class="media-card-thumb">
                    @if($media->type === 'image')
                        @if($media->miniature)
                            <img src="{{ $media->urlMiniature() }}" alt="{{ $media->titre }}" loading="lazy">
                        @else
                            <img src="{{ $media->url() }}" alt="{{ $media->titre }}" loading="lazy">
                        @endif
                    @elseif($media->type === 'video')
                        @if($media->miniature)
                            <img src="{{ $media->urlMiniature() }}" alt="{{ $media->titre }}" loading="lazy">
                        @else
                            <div class="media-card-thumb-placeholder">
                                <i class="fas fa-video"></i>
                            </div>
                        @endif
                    @else
                        <div class="media-card-thumb-placeholder">
                            <i class="fas fa-file"></i>
                        </div>
                    @endif

                    {{-- Badge type --}}
                    <span class="media-card-type-badge {{ $media->type }}">
                        <i class="fas fa-{{ $media->type === 'image' ? 'image' : 'video' }}"></i>
                        {{ $media->type === 'image' ? 'Photo' : 'Vidéo' }}
                    </span>

                    {{-- Overlay au survol --}}
                    <div class="media-card-overlay">
                        <div class="media-card-overlay-actions">
                            <button type="button" class="media-card-overlay-btn view" title="Voir en grand">
                                <i class="fas fa-expand"></i>
                            </button>
                            <a href="{{ route('admin.medias.show', $media) }}" class="media-card-overlay-btn" title="Détails">
                                <i class="fas fa-info-circle"></i>
                            </a>
                            <a href="{{ route('admin.medias.edit', $media) }}" class="media-card-overlay-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.medias.destroy', $media) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="media-card-overlay-btn danger btn-delete-media" 
                                        data-titre="{{ $media->titre }}" title="Supprimer"
                                        onclick="event.stopPropagation();">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Infos --}}
                <div class="media-card-info">
                    <div class="media-card-title" title="{{ $media->titre }}">{{ $media->titre ?: 'Sans titre' }}</div>
                    <div class="media-card-meta">
                        <span><i class="fas fa-calendar-alt"></i> {{ $media->created_at->format('d/m/Y') }}</span>
                        @if($media->taille)
                            <span><i class="fas fa-weight-hanging"></i> {{ $media->taille > 1048576 ? round($media->taille / 1048576, 1) . ' Mo' : round($media->taille / 1024, 1) . ' Ko' }}</span>
                        @endif
                        @if($media->auteur)
                            <span><i class="fas fa-user"></i> {{ $media->auteur }}</span>
                        @endif
                    </div>
                    @if($media->album)
                        <div class="media-card-album" title="{{ $media->album }}">
                            <i class="fas fa-folder"></i> {{ $media->album }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="medias-empty" style="grid-column: 1 / -1;">
                <div class="medias-empty-icon">
                    <i class="fas fa-images"></i>
                </div>
                <h4>Aucun média trouvé</h4>
                <p>
                    @if(request('recherche') || request('type') || request('album') || request('projet_id') || request('publier'))
                        Aucun média ne correspond à vos critères de recherche.
                    @else
                        La galerie est vide. Commencez par ajouter des photos ou vidéos.
                    @endif
                </p>
                @if(request('recherche') || request('type') || request('album') || request('projet_id') || request('publier'))
                    <a href="{{ route('admin.medias.index') }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
                    </a>
                @else
                    <a href="{{ route('admin.medias.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-cloud-upload-alt"></i> Ajouter des médias
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- ============================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================ --}}
    @if($medias->hasPages())
    <div class="medias-pagination">
        {{ $medias->links('pagination.custom') }}
    </div>
    @endif

    {{-- ============================================ --}}
    {{-- BARRE D'ACTIONS PAR LOT --}}
    {{-- ============================================ --}}
    <div id="mediasBatchBar" class="medias-batch-bar">
        <div class="medias-batch-count">
            <span id="mediasBatchCountSpan">0</span> média(s) sélectionné(s)
        </div>
        <div class="medias-batch-actions">
            <button type="button" id="batchPublierBtn" class="medias-batch-btn" title="Publier les médias sélectionnés">
                <i class="fas fa-globe"></i> Publier
            </button>
            <button type="button" id="batchDepublierBtn" class="medias-batch-btn" title="Dépublier les médias sélectionnés">
                <i class="fas fa-lock"></i> Dépublier
            </button>
            <button type="button" id="batchAlbumBtn" class="medias-batch-btn" title="Déplacer vers un album">
                <i class="fas fa-folder"></i> Changer d'album
            </button>
            <button type="button" id="batchDeleteBtn" class="medias-batch-btn danger" 
                    data-url="{{ route('admin.medias.batchDelete') }}" title="Supprimer définitivement">
                <i class="fas fa-trash-alt"></i> Supprimer
            </button>
        </div>
        <button type="button" id="batchCancelBtn" class="medias-batch-btn" title="Annuler la sélection" style="background: rgba(255,255,255,0.03); border: none;">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- ============================================ --}}
    {{-- FORMULAIRES CACHÉS POUR ACTIONS BATCH --}}
    {{-- ============================================ --}}
    <form id="batchPublierForm" action="{{ route('admin.medias.batchTogglePublier') }}" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="publier" id="batchPublierInput" value="1">
    </form>

    <form id="batchAlbumForm" action="{{ route('admin.medias.batchChangeAlbum') }}" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="album" id="batchAlbumInput" value="">
    </form>

    {{-- ============================================ --}}
    {{-- MODALE CHANGEMENT D'ALBUM --}}
    {{-- ============================================ --}}
    <div id="albumModal" class="media-modal-overlay">
        <div class="media-modal">
            <h3><i class="fas fa-folder" style="color: var(--color-primary); margin-right: 8px;"></i> Changer l'album</h3>
            <input type="text" id="albumModalInput" class="media-modal-input" 
                   placeholder="Nom de l'album (ex: Projet Wash Minova, Cérémonie 2024...)" maxlength="255">
            <div class="media-modal-actions">
                <button type="button" id="albumModalCancel" class="btn btn-outline btn-sm">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="button" id="albumModalSubmit" class="btn btn-primary btn-sm">
                    <i class="fas fa-check"></i> Appliquer
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGHTBOX --}}
    {{-- ============================================ --}}
    <div id="mediaLightbox" class="media-lightbox">
        <button id="lightboxClose" class="media-lightbox-close" title="Fermer">
            <i class="fas fa-times"></i>
        </button>
        <button id="lightboxPrev" class="media-lightbox-nav prev" title="Précédent">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button id="lightboxNext" class="media-lightbox-nav next" title="Suivant">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="media-lightbox-content">
            <img id="lightboxImage" src="" alt="" style="display: none;">
            <video id="lightboxVideo" controls style="display: none;">
                <source src="" type="video/mp4">
                Votre navigateur ne supporte pas la lecture de vidéos.
            </video>
            <div class="media-lightbox-info">
                <h4 id="lightboxTitle"></h4>
                <p id="lightboxDescription"></p>
            </div>
        </div>
        <div id="lightboxCounter" class="media-lightbox-counter"></div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-medias.js') }}"></script>
@endsection