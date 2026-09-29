@extends('layouts.admin')

@section('title', 'Modifier l\'article')

@section('page_title', 'Modifier l\'article')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations de l\'article et enregistrez les changements.')

@php
    $breadcrumb = ['Articles' => route('admin.articles.index'), 'Modifier' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-articles.css') }}">
@endsection

@section('content')

    <div class="article-form-card">

        {{-- En-tête du formulaire --}}
        <div class="article-form-header">
            <div class="article-form-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier l'article</h2>
                <p>
                    Article : <strong>{{ $article->titre }}</strong> — 
                    <span class="article-statut-badge article-statut-{{ $article->statut }}" style="margin-left: 6px;">
                        <i class="fas fa-{{ $article->statut === 'publie' ? 'eye' : ($article->statut === 'brouillon' ? 'pen' : 'archive') }}"></i>
                        {{ $article->statut === 'publie' ? 'Publié' : ($article->statut === 'brouillon' ? 'Brouillon' : 'Archivé') }}
                    </span>
                    @if($article->epingle)
                        <span class="article-pin-badge" style="margin-left: 6px;">
                            <i class="fas fa-thumbtack"></i> Épinglé
                        </span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" id="articleForm">
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
                    <input type="text" name="titre" id="titre" 
                           class="article-form-input @error('titre') is-invalid @enderror" 
                           value="{{ old('titre', $article->titre) }}" 
                           placeholder="Ex: Lancement du projet Wash à Minova" 
                           maxlength="255" required autofocus>
                    @error('titre')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                    <small style="font-size: 11px; color: var(--color-text-muted); margin-top: 4px;">
                        <i class="fas fa-link"></i> Slug : <strong id="slugPreview">{{ old('titre') ? \Illuminate\Support\Str::slug(old('titre')) : $article->slug }}</strong>
                    </small>
                </div>

                {{-- Catégorie --}}
                <div class="article-form-group">
                    <label for="categorie" class="article-form-label">
                        Catégorie <span class="required">*</span>
                    </label>
                    <select name="categorie" id="categorie" 
                            class="article-form-select @error('categorie') is-invalid @enderror" required>
                        <option value="">-- Sélectionnez une catégorie --</option>
                        <option value="actualite" {{ old('categorie', $article->categorie) === 'actualite' ? 'selected' : '' }}>Actualité</option>
                        <option value="projet" {{ old('categorie', $article->categorie) === 'projet' ? 'selected' : '' }}>Projet</option>
                        <option value="evenement" {{ old('categorie', $article->categorie) === 'evenement' ? 'selected' : '' }}>Événement</option>
                        <option value="rapport" {{ old('categorie', $article->categorie) === 'rapport' ? 'selected' : '' }}>Rapport</option>
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
                        Extrait <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel — affiché dans les listes et les cartes)</small>
                    </label>
                    <textarea name="extrait" id="extrait" 
                              class="article-form-textarea @error('extrait') is-invalid @enderror" 
                              rows="2" 
                              placeholder="Un court résumé de l'article (500 caractères max)"
                              maxlength="500">{{ old('extrait', $article->extrait) }}</textarea>
                    <small style="font-size: 11px; color: var(--color-text-muted); margin-top: 4px; text-align: right; display: block;">
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
                    <textarea name="contenu" id="contenu" 
                              class="article-form-textarea @error('contenu') is-invalid @enderror" 
                              rows="18" 
                              placeholder="Rédigez le contenu de votre article ici..." required>{{ old('contenu', $article->contenu) }}</textarea>
                    @error('contenu')
                        <span class="article-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : IMAGE & LIENS --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-image"></i> Image & Projet lié
                </div>

                <div class="article-form-grid">
                    {{-- Image --}}
                    <div class="article-form-group">
                        <label class="article-form-label">
                            Image de couverture <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel — laissez vide pour conserver l'image actuelle)</small>
                        </label>
                        <div class="article-image-upload">
                            @php
                                $imageUrl = $article->image
                                    ? route('media.show', ['path' => ltrim($article->image, '/')])
                                    : null;
                            @endphp

                            <label for="image" class="article-image-preview" id="imagePreview">
                                @if($imageUrl)
                                    <img id="imagePreviewImg" src="{{ $imageUrl }}" alt="Image actuelle" style="display: block;" loading="lazy">
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
                                        <span style="color: var(--color-success);">
                                            <i class="fas fa-check-circle"></i> Image actuelle
                                        </span>
                                        <br>
                                        <a href="{{ $imageUrl }}" target="_blank" style="font-size: 12px; color: var(--color-primary); text-decoration: underline;">
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
                                            <button type="button" id="removeImageBtn" style="background: none; border: none; color: var(--color-accent); font-size: 12px; cursor: pointer; padding: 0; text-decoration: underline;">
                                                <i class="fas fa-trash-alt"></i> Supprimer l'image actuelle
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                                <input type="file" name="image" id="image" 
                                    accept="image/jpeg,image/jpg,image/png,image/webp" 
                                    style="display: none;">
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
                            Projet lié <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel)</small>
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
            {{-- SECTION 3 : PUBLICATION --}}
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
                            <option value="publie" {{ old('statut', $article->statut) === 'publie' ? 'selected' : '' }}>Publié</option>
                        </select>
                    </div>

                    {{-- Date de publication --}}
                    <div class="article-form-group">
                        <label for="date_publication" class="article-form-label">
                            Date de publication <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel)</small>
                        </label>
                        @php
                            $datePub = old('date_publication', $article->date_publication);
                            $dateValue = $datePub ? $datePub->format('Y-m-d\TH:i') : '';
                        @endphp
                        <input type="datetime-local" name="date_publication" id="date_publication" 
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
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; font-weight: 600; color: #1a1a1a;">
                            <input type="checkbox" name="epingle" value="1" 
                                   {{ old('epingle', $article->epingle) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; accent-color: var(--color-accent); cursor: pointer;">
                            <span>
                                <i class="fas fa-thumbtack" style="color: var(--color-accent);"></i> 
                                Épingler cet article 
                                <small style="font-weight: 400; color: var(--color-text-muted); display: block; margin-top: 2px;">
                                    L'article apparaîtra en haut de la page actualités
                                </small>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- MÉTADONNÉES --}}
            {{-- ============================================ --}}
            <div class="article-form-section">
                <div class="article-form-section-title">
                    <i class="fas fa-chart-bar"></i> Statistiques
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; font-size: 13px; color: var(--color-text-secondary);">
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px;">
                        <div style="font-size: 11px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Vues</div>
                        <div style="font-weight: 700; font-size: 1.2rem; color: #1a1a1a;">
                            <i class="fas fa-eye" style="color: var(--color-info); margin-right: 6px;"></i> 
                            {{ number_format($article->vues, 0, ',', ' ') }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px;">
                        <div style="font-size: 11px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Créé le</div>
                        <div style="font-weight: 700; font-size: 1.2rem; color: #1a1a1a;">
                            <i class="fas fa-calendar-plus" style="color: var(--color-primary); margin-right: 6px;"></i> 
                            {{ $article->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px;">
                        <div style="font-size: 11px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Mis à jour le</div>
                        <div style="font-weight: 700; font-size: 1.2rem; color: #1a1a1a;">
                            <i class="fas fa-clock" style="color: var(--color-warning); margin-right: 6px;"></i> 
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
                <button type="submit" name="statut" value="brouillon" class="btn btn-outline" style="border-color: #aaa; color: #666;">
                    <i class="fas fa-save"></i> Enregistrer comme brouillon
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Mettre à jour l'article
                </button>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Compteur de caractères de l'extrait
            const extrait = document.getElementById('extrait');
            const counter = document.getElementById('extraitCounter');
            if (extrait && counter) {
                counter.textContent = extrait.value.length;
                extrait.addEventListener('input', function () {
                    counter.textContent = this.value.length;
                });
            }

            // Bouton "Enregistrer comme brouillon"
            const brouillonBtn = document.querySelector('button[value="brouillon"]');
            if (brouillonBtn) {
                brouillonBtn.addEventListener('click', function () {
                    document.getElementById('statut').value = 'brouillon';
                });
            }

            // Suppression de l'image actuelle
            const removeImageBtn = document.getElementById('removeImageBtn');
            const removeImageFlag = document.getElementById('removeImageFlag');
            const imagePreviewImg = document.getElementById('imagePreviewImg');
            const imagePlaceholder = document.getElementById('imagePlaceholder');
            const imageInput = document.getElementById('image');

            if (removeImageBtn && removeImageFlag && imagePreviewImg && imagePlaceholder) {
                removeImageBtn.addEventListener('click', function () {
                    if (confirm('Voulez-vous vraiment supprimer l\'image actuelle ?')) {
                        removeImageFlag.value = '1';
                        imagePreviewImg.style.display = 'none';
                        imagePreviewImg.src = '';
                        imagePlaceholder.style.display = 'flex';
                        imageInput.value = '';
                        removeImageBtn.style.display = 'none';
                    }
                });
            }

            // Réinitialiser le flag si une nouvelle image est choisie
            if (imageInput && removeImageFlag) {
                imageInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        removeImageFlag.value = '0';
                        if (removeImageBtn) {
                            removeImageBtn.style.display = 'inline';
                        }
                    }
                });
            }
        });
    </script>
    <script src="{{ asset('assets/js/admin-articles.js') }}"></script>
@endsection