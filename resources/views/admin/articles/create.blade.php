@extends('layouts.admin')

@section('title', 'Nouvel article | PHOVE')

@section('page_title', 'Nouvel article')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Rédigez et publiez un nouvel article pour le site PHOVE.')

@php
    $breadcrumb = [
        'Articles' => route('admin.articles.index'),
        'Nouvel article' => null,
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-galerie.css') }}">  {{-- ← AJOUT --}}
    <link rel="stylesheet" href="{{ asset('assets/css/admin-articles.css') }}">
@endsection



@section('content')

    <div class="article-form-card">

        {{-- ============================================ --}}
        {{-- EN-TÊTE DU FORMULAIRE --}}
        {{-- ============================================ --}}
        <div class="article-form-header">
            <div class="article-form-header-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div>
                <h2>Nouvel article</h2>
                <p>Remplissez les informations ci-dessous pour créer un article. Les champs marqués d'un astérisque (*) sont obligatoires.</p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- FORMULAIRE --}}
        {{-- ============================================ --}}
        <form action="{{ route('admin.articles.store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="articleForm">
            @csrf

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
                           value="{{ old('titre') }}"
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
                        <strong id="slugPreview">
                            {{ old('titre') ? Str::slug(old('titre')) : '...' }}
                        </strong>
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
                        <option value="actualite"   {{ old('categorie') === 'actualite'   ? 'selected' : '' }}>Actualité</option>
                        <option value="projet"      {{ old('categorie') === 'projet'      ? 'selected' : '' }}>Projet</option>
                        <option value="evenement"   {{ old('categorie') === 'evenement'   ? 'selected' : '' }}>Événement</option>
                        <option value="rapport"     {{ old('categorie') === 'rapport'     ? 'selected' : '' }}>Rapport</option>
                        <option value="temoignage"  {{ old('categorie') === 'temoignage'  ? 'selected' : '' }}>Témoignage</option>
                        <option value="communique"  {{ old('categorie') === 'communique'  ? 'selected' : '' }}>Communiqué</option>
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
                              maxlength="500">{{ old('extrait') }}</textarea>
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
                              required>{{ old('contenu') }}</textarea>
                    @error('contenu')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : IMAGE DE COUVERTURE & PROJET LIÉ --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-image"></i> Image de couverture &amp; Projet lié
                </div>

                <div class="article-form-grid">

                    {{-- Image de couverture --}}
                    <div class="article-form-group">
                        <label class="article-form-label">
                            Image de couverture
                            <small class="article-form-label-optional">(optionnel)</small>
                        </label>
                        <div class="article-image-upload">
                            <label for="image" class="article-image-preview" id="imagePreview">
                                <img id="imagePreviewImg"
                                     src=""
                                     alt="Aperçu"
                                     style="display: none;">
                                <div class="article-image-placeholder" id="imagePlaceholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                            </label>
                            <div class="article-image-info">
                                <p>Cliquez pour choisir une image</p>
                                <ul>
                                    <li><i class="fas fa-check-circle"></i> JPG, PNG ou WebP</li>
                                    <li><i class="fas fa-check-circle"></i> Taille max : 5 Mo</li>
                                    <li><i class="fas fa-check-circle"></i> Recommandé : 1200×630 px</li>
                                </ul>
                                <input type="file"
                                       name="image"
                                       id="image"
                                       accept="image/jpeg,image/jpg,image/png,image/webp"
                                       style="display: none;">
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
                            Projet lié
                            <small class="article-form-label-optional">(optionnel)</small>
                        </label>
                        <select name="projet_id" id="projet_id" class="article-form-select">
                            <option value="">-- Aucun projet --</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                    {{ $projet->titre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3 : GALERIE MÉDIA --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-images"></i> Galerie média
                </div>

                <div class="article-form-group">

                    <label for="galerie" class="article-form-label">
                        Photos et vidéos de l'article
                        <small class="article-form-label-optional">(optionnel — plusieurs fichiers)</small>
                    </label>

                    {{-- Zone d'upload --}}
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

                    {{-- Aperçu --}}
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
            {{-- SECTION 4 : PARAMÈTRES DE PUBLICATION --}}
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
                            <option value="brouillon" {{ old('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="publie"    {{ old('statut', 'publie') === 'publie' ? 'selected' : '' }}>Publié</option>
                        </select>
                    </div>

                    {{-- Date de publication --}}
                    <div class="article-form-group">
                        <label for="date_publication" class="article-form-label">
                            Date de publication
                            <small class="article-form-label-optional">
                                (optionnel — laissez vide pour "maintenant")
                            </small>
                        </label>
                        <input type="datetime-local"
                               name="date_publication"
                               id="date_publication"
                               class="article-form-input @error('date_publication') is-invalid @enderror"
                               value="{{ old('date_publication') }}">
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
                                   {{ old('epingle') ? 'checked' : '' }}>
                            <span>
                                <strong>
                                    <i class="fas fa-thumbtack"></i>
                                    Épingler cet article
                                </strong>
                                <small>
                                    L'article apparaîtra en haut de la page actualités
                                </small>
                            </span>
                        </label>
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
                <button type="submit"
                        name="statut"
                        value="brouillon"
                        class="btn btn-outline btn-draft">
                    <i class="fas fa-save"></i> Enregistrer comme brouillon
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Publier l'article
                </button>
            </div>

        </form>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script src="{{ asset('assets/js/admin-galerie.js') }}"></script>  {{-- ← AJOUT --}}
    <script src="{{ asset('assets/js/admin-articles.js') }}"></script>
@endsection