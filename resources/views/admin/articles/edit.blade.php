@extends('layouts.admin')

@section('title', 'Modifier l\'article | PHOVE')

@section('page_title', 'Modifier l\'article')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations de l\'article et enregistrez les changements.')

@php
    $breadcrumb = [
        'Articles' => route('admin.articles.index'),
        'Modifier' => null,
    ];

    $imageUrl = $article->image
        ? img_url($article->image)
        : null;

    $datePub = old('date_publication', $article->date_publication);
    $dateValue = $datePub
        ? ($datePub instanceof \Carbon\Carbon ? $datePub->format('Y-m-d\TH:i') : $datePub)
        : '';
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-galerie.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-articles.css') }}">
@endsection

@section('content')

    <div class="article-form-card">

        {{-- ============================================ --}}
        {{-- EN-TÊTE DU FORMULAIRE --}}
        {{-- ============================================ --}}
        <div class="article-form-header">
            <div class="article-form-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier l'article</h2>
                <p>
                    Article : <strong>{{ $article->titre }}</strong> —
                    <span class="article-statut-badge article-statut-{{ $article->statut }}">
                        <i class="fas fa-{{ $article->statut === 'publie' ? 'eye' : ($article->statut === 'brouillon' ? 'pen' : 'archive') }}"></i>
                        {{ $article->statut === 'publie' ? 'Publié' : ($article->statut === 'brouillon' ? 'Brouillon' : 'Archivé') }}
                    </span>
                    @if($article->epingle)
                        <span class="article-pin-badge">
                            <i class="fas fa-thumbtack"></i> Épinglé
                        </span>
                    @endif
                </p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- FORMULAIRE --}}
        {{-- ============================================ --}}
        <form action="{{ route('admin.articles.update', $article) }}"
              method="POST"
              enctype="multipart/form-data"
              id="articleForm">
            @csrf
            @method('PUT')

            {{-- ============================================ --}}
            {{-- SECTION 1 : INFORMATIONS PRINCIPALES --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations principales
                </div>

                {{-- Titre --}}
                <div class="article-form-group">
                    <label for="titre" class="article-form-label">
                        Titre de l'article <span class="required">*</span>
                    </label>
                    <input type="text"
                           name="titre"
                           id="titre"
                           class="article-form-input @error('titre') is-invalid @enderror"
                           value="{{ old('titre', $article->titre) }}"
                           placeholder="Ex: Lancement du projet Wash à Minova"
                           maxlength="255"
                           required
                           autofocus>
                    @error('titre')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                    <small class="article-form-hint">
                        <i class="fas fa-link"></i> Slug :
                        <strong id="slugPreview">{{ $article->slug }}</strong>
                    </small>
                </div>

                {{-- Catégorie --}}
                <div class="article-form-group">
                    <label for="categorie" class="article-form-label">
                        Catégorie <span class="required">*</span>
                    </label>
                    <select name="categorie"
                            id="categorie"
                            class="article-form-select @error('categorie') is-invalid @enderror"
                            required>
                        <option value="">-- Sélectionnez une catégorie --</option>
                        <option value="actualite"  {{ old('categorie', $article->categorie) === 'actualite'  ? 'selected' : '' }}>Actualité</option>
                        <option value="projet"     {{ old('categorie', $article->categorie) === 'projet'     ? 'selected' : '' }}>Projet</option>
                        <option value="evenement"  {{ old('categorie', $article->categorie) === 'evenement'  ? 'selected' : '' }}>Événement</option>
                        <option value="rapport"    {{ old('categorie', $article->categorie) === 'rapport'    ? 'selected' : '' }}>Rapport</option>
                        <option value="temoignage" {{ old('categorie', $article->categorie) === 'temoignage' ? 'selected' : '' }}>Témoignage</option>
                        <option value="communique" {{ old('categorie', $article->categorie) === 'communique' ? 'selected' : '' }}>Communiqué</option>
                    </select>
                    @error('categorie')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Extrait --}}
                <div class="article-form-group">
                    <label for="extrait" class="article-form-label">
                        Extrait
                        <small class="article-form-label-optional">
                            (optionnel — affiché dans les listes et les cartes)
                        </small>
                    </label>
                    <textarea name="extrait"
                              id="extrait"
                              class="article-form-textarea @error('extrait') is-invalid @enderror"
                              rows="2"
                              placeholder="Un court résumé de l'article (500 caractères max)"
                              maxlength="500">{{ old('extrait', $article->extrait) }}</textarea>
                    <small class="article-form-counter">
                        <span id="extraitCounter">0</span>/500 caractères
                    </small>
                    @error('extrait')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Contenu --}}
                <div class="article-form-group">
                    <label for="contenu" class="article-form-label">
                        Contenu <span class="required">*</span>
                    </label>
                    <textarea name="contenu"
                              id="contenu"
                              class="article-form-textarea @error('contenu') is-invalid @enderror"
                              rows="18"
                              placeholder="Rédigez le contenu de votre article ici..."
                              required>{{ old('contenu', $article->contenu) }}</textarea>
                    @error('contenu')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : IMAGE & PROJET LIÉ --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-image"></i> Image de couverture &amp; Projet lié
                </div>

                <div class="article-form-grid">

                    {{-- Image --}}
                    <div class="article-form-group">
                        <label class="article-form-label">
                            Image de couverture
                            <small class="article-form-label-optional">
                                (laissez vide pour conserver l'image actuelle)
                            </small>
                        </label>
                        <div class="article-image-upload">

                            <label for="image" class="article-image-preview" id="imagePreview">
                                @if($imageUrl)
                                    <img id="imagePreviewImg"
                                         src="{{ $imageUrl }}"
                                         alt="Image actuelle"
                                         style="display: block;"
                                         loading="lazy">
                                    <div class="article-image-placeholder" id="imagePlaceholder" style="display: none;">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                @else
                                    <img id="imagePreviewImg" src="" alt="Aperçu" style="display: none;">
                                    <div class="article-image-placeholder" id="imagePlaceholder">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                @endif
                            </label>

                            <div class="article-image-info">
                                <p>
                                    @if($imageUrl)
                                        <span class="article-image-status-ok">
                                            <i class="fas fa-check-circle"></i> Image actuelle
                                        </span>
                                        <br>
                                        <a href="{{ $imageUrl }}" target="_blank" class="article-image-link">
                                            <i class="fas fa-external-link-alt"></i> Voir l'image
                                        </a>
                                    @else
                                        Cliquez pour choisir une image
                                    @endif
                                </p>

                                <ul>
                                    <li><i class="fas fa-check-circle"></i> JPG, PNG ou WebP</li>
                                    <li><i class="fas fa-check-circle"></i> Taille max : 5 Mo</li>
                                    <li><i class="fas fa-check-circle"></i> Recommandé : 1200×630 px</li>

                                    @if($imageUrl)
                                        <li>
                                            <button type="button" id="removeImageBtn" class="article-image-remove-btn">
                                                <i class="fas fa-trash-alt"></i> Supprimer l'image actuelle
                                            </button>
                                        </li>
                                    @endif
                                </ul>

                                <input type="file"
                                       name="image"
                                       id="image"
                                       accept="image/jpeg,image/jpg,image/png,image/webp"
                                       hidden>

                                <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                            </div>
                        </div>
                        @error('image')
                            <span class="article-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Projet lié --}}
                    <div class="article-form-group">
                        <label for="projet_id" class="article-form-label">
                            Projet lié <small class="article-form-label-optional">(optionnel)</small>
                        </label>
                        <select name="projet_id" id="projet_id" class="article-form-select">
                            <option value="">-- Aucun projet --</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id', $article->projet_id) == $projet->id ? 'selected' : '' }}>
                                    {{ $projet->titre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3 : PARAMÈTRES DE PUBLICATION --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-cog"></i> Paramètres de publication
                </div>

                <div class="article-form-grid">

                    {{-- Statut --}}
                    <div class="article-form-group">
                        <label for="statut" class="article-form-label">
                            Statut <span class="required">*</span>
                        </label>
                        <select name="statut" id="statut" class="article-form-select" required>
                            <option value="brouillon" {{ old('statut', $article->statut) === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="publie"    {{ old('statut', $article->statut) === 'publie'    ? 'selected' : '' }}>Publié</option>
                        </select>
                    </div>

                    {{-- Date de publication --}}
                    <div class="article-form-group">
                        <label for="date_publication" class="article-form-label">
                            Date de publication
                            <small class="article-form-label-optional">(optionnel)</small>
                        </label>
                        <input type="datetime-local"
                               name="date_publication"
                               id="date_publication"
                               class="article-form-input @error('date_publication') is-invalid @enderror"
                               value="{{ $dateValue }}">
                        @error('date_publication')
                            <span class="article-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Épingler --}}
                    <div class="article-form-group article-form-full">
                        <label class="article-form-checkbox-row">
                            <input type="checkbox"
                                   name="epingle"
                                   value="1"
                                   {{ old('epingle', $article->epingle) ? 'checked' : '' }}>
                            <span>
                                <strong>
                                    <i class="fas fa-thumbtack"></i>
                                    Épingler cet article
                                </strong>
                                <small>L'article apparaîtra en haut de la page actualités</small>
                            </span>
                        </label>
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4 : GALERIE MÉDIA --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-images"></i> Galerie média
                </div>

                {{-- Médias existants --}}
                @if($article->medias->count() > 0)
                    <div class="projet-medias-existants">
                        <p class="projet-medias-title">
                            <i class="fas fa-photo-video"></i>
                            {{ $article->medias->count() }} média(s) existant(s) —
                            <span class="projet-medias-hint">cochez pour supprimer</span>
                        </p>

                        <div class="projet-medias-grid">
                            @foreach($article->medias as $media)
                                @php
                                    $thumbUrl = $media->miniature
                                        ? img_url($media->miniature)
                                        : ($media->fichier
                                            ? img_url($media->fichier)
                                            : null);
                                @endphp

                                <label class="projet-media-item" title="{{ $media->titre }}">
                                    <input type="checkbox"
                                           name="medias_supprimer[]"
                                           value="{{ $media->id }}"
                                           class="projet-media-checkbox">

                                    <div class="projet-media-thumb">
                                        @if($thumbUrl)
                                            <img src="{{ $thumbUrl }}" alt="{{ $media->titre }}" loading="lazy">
                                        @else
                                            <div class="projet-media-placeholder">
                                                <i class="fas fa-{{ $media->type === 'video' ? 'video' : 'image' }}"></i>
                                            </div>
                                        @endif

                                        @if($media->type === 'video')
                                            <span class="projet-media-type-badge">
                                                <i class="fas fa-video"></i>
                                            </span>
                                        @endif

                                        <span class="projet-media-delete-overlay">
                                            <i class="fas fa-trash-alt"></i> Supprimer
                                        </span>
                                    </div>

                                    <span class="projet-media-title">
                                        {{ Str::limit($media->titre ?: 'Sans titre', 25) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="projet-medias-empty">
                        <i class="fas fa-info-circle"></i>
                        Aucun média pour cet article. Ajoutez-en ci-dessous.
                    </p>
                @endif

                {{-- Zone d'ajout --}}
                <div class="article-form-group">
                    <label for="galerie" class="article-form-label">
                        Ajouter de nouveaux médias
                        <small class="article-form-label-optional">(optionnel — plusieurs fichiers)</small>
                    </label>

                    <div class="projet-galerie-upload">
                        <input type="file"
                               name="galerie[]"
                               id="galerie"
                               class="projet-galerie-input"
                               accept="image/jpeg,image/jpg,image/png,image/webp,video/mp4,video/webm"
                               multiple
                               hidden>

                        <label for="galerie" class="projet-galerie-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span class="projet-galerie-label-title">Cliquez pour choisir des fichiers</span>
                            <span class="projet-galerie-label-sub">ou glissez-déposez ici</span>
                            <span class="projet-galerie-label-info">
                                JPG, PNG, WebP, MP4, WebM — 10 Mo max par image, 50 Mo max par vidéo
                            </span>
                        </label>
                    </div>

                    <div id="galeriePreview" class="projet-galerie-preview"></div>

                    @error('galerie')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                    @error('galerie.*')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 5 : STATISTIQUES --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-chart-bar"></i> Statistiques
                </div>

                <div class="article-stats-grid">

                    <div class="article-stat-box">
                        <div class="article-stat-label">Vues</div>
                        <div class="article-stat-value">
                            <i class="fas fa-eye article-stat-icon-info"></i>
                            {{ number_format($article->vues, 0, ',', ' ') }}
                        </div>
                    </div>

                    <div class="article-stat-box">
                        <div class="article-stat-label">Créé le</div>
                        <div class="article-stat-value">
                            <i class="fas fa-calendar-plus article-stat-icon-primary"></i>
                            {{ $article->created_at->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="article-stat-box">
                        <div class="article-stat-label">Mis à jour le</div>
                        <div class="article-stat-value">
                            <i class="fas fa-clock article-stat-icon-warning"></i>
                            {{ $article->updated_at->format('d/m/Y') }}
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="article-form-actions">
                <a href="{{ route('admin.articles.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" name="statut" value="brouillon" class="btn btn-outline btn-draft">
                    <i class="fas fa-save"></i> Enregistrer comme brouillon
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Mettre à jour l'article
                </button>
            </div>

        </form>

        {{-- ============================================ --}}
        {{-- SUPPRESSION DE L'ARTICLE --}}
        {{-- ============================================ --}}
        <form action="{{ route('admin.articles.destroy', $article) }}"
              method="POST"
              class="article-form-delete">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="btn btn-accent btn-sm"
                    onclick="return confirm('Supprimer définitivement cet article ? Cette action est irréversible.');">
                <i class="fas fa-trash-alt"></i> Supprimer cet article
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script src="{{ asset('assets/js/admin-galerie.js') }}"></script>
    <script src="{{ asset('assets/js/admin-articles.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* ========================================================
               1. COMPTEUR DE CARACTÈRES POUR L'EXTRAIT
               ======================================================== */
            const extrait = document.getElementById('extrait');
            const counter = document.getElementById('extraitCounter');

            if (extrait && counter) {
                counter.textContent = extrait.value.length;
                extrait.addEventListener('input', function () {
                    counter.textContent = this.value.length;
                });
            }

            /* ========================================================
               2. APERÇU DE L'IMAGE DE COUVERTURE
               ======================================================== */
            const imageInput       = document.getElementById('image');
            const imagePreviewImg  = document.getElementById('imagePreviewImg');
            const imagePlaceholder = document.getElementById('imagePlaceholder');
            const removeImageBtn   = document.getElementById('removeImageBtn');
            const removeImageFlag  = document.getElementById('removeImageFlag');

            if (imageInput) {
                imageInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (!file) return;

                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

                    if (!allowedTypes.includes(file.type)) {
                        alert('Format non supporté. Utilisez JPG, PNG ou WebP.');
                        this.value = '';
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        alert('L\'image ne doit pas dépasser 5 Mo.');
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (imagePreviewImg) {
                            imagePreviewImg.src = e.target.result;
                            imagePreviewImg.style.display = 'block';
                        }
                        if (imagePlaceholder) {
                            imagePlaceholder.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);

                    // Annuler la suppression si on choisit une nouvelle image
                    if (removeImageFlag) {
                        removeImageFlag.value = '0';
                    }
                });
            }

            /* ========================================================
               3. SUPPRESSION DE L'IMAGE ACTUELLE
               ======================================================== */
            if (removeImageBtn && removeImageFlag) {
                removeImageBtn.addEventListener('click', function () {
                    if (confirm('Voulez-vous vraiment supprimer l\'image actuelle ?')) {
                        removeImageFlag.value = '1';

                        if (imagePreviewImg) {
                            imagePreviewImg.src = '';
                            imagePreviewImg.style.display = 'none';
                        }
                        if (imagePlaceholder) {
                            imagePlaceholder.style.display = 'flex';
                        }
                        if (imageInput) {
                            imageInput.value = '';
                        }

                        removeImageBtn.style.display = 'none';
                    }
                });
            }

            /* ========================================================
               4. BOUTON "ENREGISTRER COMME BROUILLON"
               ======================================================== */
            const brouillonBtn = document.querySelector('button[value="brouillon"]');
            const statutSelect = document.getElementById('statut');

            if (brouillonBtn && statutSelect) {
                brouillonBtn.addEventListener('click', function () {
                    statutSelect.value = 'brouillon';
                });
            }
        });
    </script>
@endsection