{{-- ============================================ --}}
{{-- PARTIAL — CARTE MÉDIA RÉUTILISABLE --}}
{{-- -------------------------------------------- --}}
{{-- Utilisé dans : --}}
{{--   - public/galerie/index.blade.php --}}
{{--   - public/projets/show.blade.php (médias du projet) --}}
{{--   - public/accueil (médias récents) --}}
{{-- --}}
{{-- Variable attendue : $media (instance App\Models\Media) --}}
{{-- Variable optionnelle : $class (classes CSS supplémentaires) --}}
{{-- ============================================ --}}

@php
    /**
     * Prépare les données d'affichage du média.
     * Toute la logique reste hors du HTML pour rester lisible.
     */

    // Type normalisé
    $type = $media->type === 'video' ? 'video' : 'image';

    // URL de la miniature (ou du fichier si pas de miniature)
    // $media->urlMiniature() retombe automatiquement sur url() si pas de miniature
    $thumbUrl = $media->urlMiniature();

    // URL pour la lightbox (fichier principal, pleine résolution)
    $fullUrl = $media->url();

    // Titre de repli
    $titre = $media->titre ?: 'Média PHOVE';

    // Description de repli (pour la lightbox)
    $description = $media->description
        ? Str::limit(strip_tags($media->description), 200)
        : $titre;

    // Classe CSS additionnelle
    $extraClass = $class ?? '';

    // Clé d'album (si présent)
    $album = $media->album ?? null;

    // Lien vers le projet associé (si présent)
    $projet = $media->relationLoaded('projet') ? $media->projet : null;
@endphp

<article class="media-card {{ $extraClass }}"
         data-media-id="{{ $media->id }}"
         data-media-type="{{ $type }}"
         data-media-url="{{ $fullUrl }}"
         data-media-titre="{{ $titre }}"
         data-media-description="{{ $description }}"
         role="button"
         tabindex="0"
         aria-label="Voir {{ $titre }}">

    {{-- ---------------------------------------- --}}
    {{-- MINIATURE --}}
    {{-- ---------------------------------------- --}}
    <div class="media-card-thumb">

        @if($thumbUrl)
            <img src="{{ $thumbUrl }}"
                 alt="{{ $titre }}"
                 loading="lazy"
                 decoding="async"
                 onerror="this.onerror=null; this.src='{{ asset('assets/images/logo-phove.png') }}';">
        @else
            <div class="media-card-thumb-placeholder">
                <i class="fas fa-{{ $type === 'video' ? 'video' : 'image' }}"></i>
            </div>
        @endif

        {{-- Badge type (Photo / Vidéo) --}}
        <span class="media-card-type-badge media-type-{{ $type }}">
            <i class="fas fa-{{ $type === 'video' ? 'play-circle' : 'camera' }}"></i>
            {{ $type === 'video' ? 'Vidéo' : 'Photo' }}
        </span>

        {{-- Badge album (si présent) --}}
        @if($album)
            <span class="media-card-album-badge" title="Album : {{ $album }}">
                <i class="fas fa-folder"></i>
                {{ Str::limit($album, 20) }}
            </span>
        @endif

        {{-- Overlay au survol avec bouton d'agrandissement --}}
        <div class="media-card-overlay">
            <span class="media-card-view-btn" aria-hidden="true">
                <i class="fas fa-{{ $type === 'video' ? 'play' : 'expand' }}"></i>
            </span>
        </div>
    </div>

    {{-- ---------------------------------------- --}}
    {{-- INFORMATIONS --}}
    {{-- ---------------------------------------- --}}
    <div class="media-card-info">

        {{-- Titre --}}
        <h3 class="media-card-title" title="{{ $titre }}">
            {{ $titre }}
        </h3>

        {{-- Métadonnées (lieu / date / projet) --}}
        @php
            $hasMeta = $media->lieu || $media->date_prise || $projet;
        @endphp

        @if($hasMeta)
            <div class="media-card-meta">

                @if($media->lieu)
                    <span title="Lieu">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ Str::limit($media->lieu, 25) }}
                    </span>
                @endif

                @if($media->date_prise)
                    <span title="Date de prise">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $media->date_prise->format('d/m/Y') }}
                    </span>
                @endif

                @if($projet)
                    <span class="media-card-projet" title="Projet : {{ $projet->titre }}">
                        <i class="fas fa-project-diagram"></i>
                        {{ Str::limit($projet->titre, 22) }}
                    </span>
                @endif

            </div>
        @endif

    </div>
</article>