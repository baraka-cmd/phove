@extends('layouts.admin')

@section('title', 'Modifier le projet | PHOVE')

@section('page_title', 'Modifier le projet')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations du projet "' . Str::limit($projet->titre, 50) . '".')

@php
    $breadcrumb = [
        ['label' => 'Projets', 'url' => route('admin.projets.index')],
        'Modifier : ' . Str::limit($projet->titre, 40),
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-projets-form.css') }}">
@endsection

@section('content')

    <div class="projet-form-card">

        {{-- ============================================ --}}
        {{-- EN-TÊTE --}}
        {{-- ============================================ --}}
        <div class="projet-form-header">
            <div class="projet-form-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier le projet</h2>
                <p>
                    Créé le {{ $projet->created_at->format('d/m/Y') }} —
                    Statut : <strong>{{ $projet->statutLibelle() }}</strong>
                    @if($projet->code_projet)
                        — Code : <strong>{{ $projet->code_projet }}</strong>
                    @endif
                </p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- ALERTE D'ERREURS GLOBALES --}}
        {{-- ============================================ --}}
        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Veuillez corriger les erreurs ci-dessous.</span>
            </div>
        @endif

        <form action="{{ route('admin.projets.update', $projet) }}"
              method="POST"
              enctype="multipart/form-data"
              novalidate>
            @csrf
            @method('PUT')

            {{-- ============================================ --}}
            {{-- SECTION 1 : INFORMATIONS GÉNÉRALES --}}
            {{-- ============================================ --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations générales
                </h4>

                <div class="projet-form-grid">

                    {{-- Titre --}}
                    <div class="projet-form-group projet-form-full">
                        <label for="titre" class="projet-form-label">
                            Titre du projet <span class="required">*</span>
                        </label>
                        <input type="text"
                               name="titre"
                               id="titre"
                               class="projet-form-input @error('titre') error @enderror"
                               value="{{ old('titre', $projet->titre) }}"
                               placeholder="Ex: Appui nutritionnel aux orphelins du village Kiziba I"
                               maxlength="255"
                               required>
                        @error('titre')
                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                        <span class="projet-form-hint">
                            Slug actuel : <strong>{{ $projet->slug }}</strong>
                        </span>
                    </div>

                    {{-- Code projet --}}
                    <div class="projet-form-group">
                        <label for="code_projet" class="projet-form-label">
                            Code projet <span class="optional">(optionnel)</span>
                        </label>
                        <input type="text"
                               name="code_projet"
                               id="code_projet"
                               class="projet-form-input @error('code_projet') error @enderror"
                               value="{{ old('code_projet', $projet->code_projet) }}"
                               placeholder="Ex: UNJP/RDC/082/WFP"
                               maxlength="50">
                        @error('code_projet')
                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Domaine --}}
                    <div class="projet-form-group">
                        <label for="domaine" class="projet-form-label">
                            Domaine d'intervention <span class="required">*</span>
                        </label>
                        <select name="domaine"
                                id="domaine"
                                class="projet-form-select @error('domaine') error @enderror"
                                required>
                            <option value="">Sélectionnez un domaine...</option>
                            @foreach($domaines as $value => $label)
                                <option value="{{ $value }}" {{ old('domaine', $projet->domaine) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('domaine')
                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Statut --}}
                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">
                            Statut <span class="required">*</span>
                        </label>
                        <select name="statut"
                                id="statut"
                                class="projet-form-select @error('statut') error @enderror"
                                required>
                            <option value="">Sélectionnez un statut...</option>
                            @foreach($statuts as $value => $label)
                                <option value="{{ $value }}" {{ old('statut', $projet->statut) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('statut')
                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : LOCALISATION ET PÉRIODE --}}
            {{-- ============================================ --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Localisation et période
                </h4>

                <div class="projet-form-grid">

                    <div class="projet-form-group">
                        <label for="zone_geographique" class="projet-form-label">
                            Zone géographique <span class="required">*</span>
                        </label>
                        <input type="text"
                               name="zone_geographique"
                               id="zone_geographique"
                               class="projet-form-input @error('zone_geographique') error @enderror"
                               value="{{ old('zone_geographique', $projet->zone_geographique) }}"
                               placeholder="Ex: Village Kiziba I, Territoire de Nyiragongo"
                               maxlength="255"
                               required>
                        @error('zone_geographique')
                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="province" class="projet-form-label">
                            Province <span class="optional">(optionnel)</span>
                        </label>
                        <input type="text"
                               name="province"
                               id="province"
                               class="projet-form-input"
                               value="{{ old('province', $projet->province) }}"
                               placeholder="Ex: Nord-Kivu"
                               maxlength="100">
                    </div>

                    <div class="projet-form-group">
                        <label for="date_debut" class="projet-form-label">
                            Date de début <span class="required">*</span>
                        </label>
                        <input type="date"
                               name="date_debut"
                               id="date_debut"
                               class="projet-form-input @error('date_debut') error @enderror"
                               value="{{ old('date_debut', $projet->date_debut->format('Y-m-d')) }}"
                               required>
                        @error('date_debut')
                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="date_fin" class="projet-form-label">
                            Date de fin <span class="optional">(optionnel)</span>
                        </label>
                        <input type="date"
                               name="date_fin"
                               id="date_fin"
                               class="projet-form-input @error('date_fin') error @enderror"
                               value="{{ old('date_fin', $projet->date_fin ? $projet->date_fin->format('Y-m-d') : '') }}">
                        @error('date_fin')
                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3 : BUDGET ET FINANCEMENT --}}
            {{-- ============================================ --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-coins"></i> Budget et financement
                </h4>

                <div class="projet-form-grid">

                    <div class="projet-form-group">
                        <label for="budget_total" class="projet-form-label">
                            Budget total ($)
                        </label>
                        <input type="number"
                               name="budget_total"
                               id="budget_total"
                               class="projet-form-input"
                               value="{{ old('budget_total', $projet->budget_total) }}"
                               step="0.01"
                               min="0"
                               placeholder="Ex: 15000">
                    </div>

                    <div class="projet-form-group">
                        <label for="budget_utilise" class="projet-form-label">
                            Budget utilisé ($)
                        </label>
                        <input type="number"
                               name="budget_utilise"
                               id="budget_utilise"
                               class="projet-form-input"
                               value="{{ old('budget_utilise', $projet->budget_utilise) }}"
                               step="0.01"
                               min="0"
                               placeholder="Ex: 7500">
                    </div>

                    <div class="projet-form-group">
                        <label for="bailleur" class="projet-form-label">
                            Bailleur de fonds
                        </label>
                        <input type="text"
                               name="bailleur"
                               id="bailleur"
                               class="projet-form-input"
                               value="{{ old('bailleur', $projet->bailleur) }}"
                               placeholder="Ex: FAO, WFP, PHOVE"
                               maxlength="255">
                    </div>

                    <div class="projet-form-group">
                        <label for="source_financement" class="projet-form-label">
                            Source de financement
                        </label>
                        <input type="text"
                               name="source_financement"
                               id="source_financement"
                               class="projet-form-input"
                               value="{{ old('source_financement', $projet->source_financement) }}"
                               placeholder="Ex: Partenariat international, Autofinancement"
                               maxlength="255">
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4 : CONTENU DU PROJET --}}
            {{-- ============================================ --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-file-alt"></i> Contenu du projet
                </h4>

                <div class="projet-form-group projet-form-full">
                    <label for="description" class="projet-form-label">
                        Description <span class="required">*</span>
                    </label>
                    <textarea name="description"
                              id="description"
                              class="projet-form-textarea @error('description') error @enderror"
                              rows="5"
                              placeholder="Décrivez le projet en détail..."
                              required>{{ old('description', $projet->description) }}</textarea>
                    @error('description')
                        <span class="projet-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="projet-form-group projet-form-full">
                    <label for="objectifs" class="projet-form-label">
                        Objectifs <span class="optional">(optionnel)</span>
                    </label>
                    <textarea name="objectifs"
                              id="objectifs"
                              class="projet-form-textarea"
                              rows="3"
                              placeholder="Listez les objectifs du projet...">{{ old('objectifs', $projet->objectifs) }}</textarea>
                </div>

                <div class="projet-form-group projet-form-full">
                    <label for="resultats_attendus" class="projet-form-label">
                        Résultats attendus <span class="optional">(optionnel)</span>
                    </label>
                    <textarea name="resultats_attendus"
                              id="resultats_attendus"
                              class="projet-form-textarea"
                              rows="3"
                              placeholder="Décrivez les résultats attendus...">{{ old('resultats_attendus', $projet->resultats_attendus) }}</textarea>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 5 : IMAGE PRINCIPALE --}}
            {{-- ============================================ --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-image"></i> Image principale
                </h4>

                {{-- Image actuelle --}}
                @if($projet->image_principale)
                    <div class="projet-current-image">
                        <img src="{{ route('media.show', ['path' => ltrim($projet->image_principale, '/')]) }}"
                             alt="Image actuelle"
                             loading="lazy">
                        <div>
                            <p class="projet-current-image-title">Image actuelle</p>
                            <p class="projet-current-image-desc">Laissez vide pour conserver cette image.</p>
                        </div>
                    </div>
                @endif

                <div class="projet-image-upload">

                    {{-- Aperçu --}}
                    <div class="projet-image-preview"
                         id="imagePreview"
                         onclick="document.getElementById('image_principale').click();">
                        <img id="imagePreviewImg"
                             src=""
                             alt="Aperçu"
                             style="display: none;">
                        <i class="fas fa-cloud-upload-alt projet-image-placeholder" id="imagePlaceholder"></i>
                    </div>

                    {{-- Informations --}}
                    <div class="projet-image-info">
                        <p>
                            @if($projet->image_principale)
                                Remplacer l'image
                            @else
                                Ajouter une image
                            @endif
                        </p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Formats acceptés : JPG, PNG, WebP</li>
                            <li><i class="fas fa-check-circle"></i> Taille maximale : 5 Mo</li>
                            <li><i class="fas fa-check-circle"></i> Taille recommandée : 1200 x 800 px</li>
                        </ul>

                        <input type="file"
                               name="image_principale"
                               id="image_principale"
                               class="projet-image-input"
                               accept="image/jpeg,image/jpg,image/png,image/webp"
                               style="display: none;">

                        <span id="imageInfo" class="projet-image-file-info">
                            Aucun nouveau fichier sélectionné
                        </span>

                        @error('image_principale')
                            <span class="projet-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 6 : GALERIE MÉDIA --}}
            {{-- ============================================ --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-images"></i> Galerie du projet
                </h4>

                {{-- Médias existants --}}
                @if($projet->medias->count() > 0)
                    <div class="projet-medias-existants">
                        <p class="projet-medias-title">
                            <i class="fas fa-photo-video"></i>
                            {{ $projet->medias->count() }} média(s) existant(s) —
                            <span class="projet-medias-hint">cochez pour supprimer</span>
                        </p>

                        <div class="projet-medias-grid">
                            @foreach($projet->medias as $media)
                                @php
                                    $thumbUrl = $media->miniature
                                        ? route('media.show', ['path' => ltrim($media->miniature, '/')])
                                        : ($media->fichier
                                            ? route('media.show', ['path' => ltrim($media->fichier, '/')])
                                            : null);
                                @endphp

                                <label class="projet-media-item" title="{{ $media->titre }}">
                                    <input type="checkbox"
                                           name="medias_supprimer[]"
                                           value="{{ $media->id }}"
                                           class="projet-media-checkbox">

                                    <div class="projet-media-thumb">
                                        @if($thumbUrl)
                                            <img src="{{ $thumbUrl }}"
                                                 alt="{{ $media->titre }}"
                                                 loading="lazy">
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
                                        {{ Str::limit($media->titre, 25) }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="projet-medias-empty">
                        <i class="fas fa-info-circle"></i>
                        Aucun média pour ce projet. Ajoutez-en ci-dessous.
                    </p>
                @endif

                {{-- Zone d'ajout de nouveaux médias --}}
                <div class="projet-form-group projet-form-full">

                    <label for="galerie" class="projet-form-label">
                        Ajouter de nouveaux médias
                        <span class="optional">(optionnel — plusieurs fichiers)</span>
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
                        <span class="projet-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                    @error('galerie.*')
                        <span class="projet-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 7 : PUBLICATION --}}
            {{-- ============================================ --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-eye"></i> Publication
                </h4>

                <label class="projet-form-switch">
                    <input type="checkbox"
                           name="publier"
                           value="1"
                           {{ old('publier', $projet->publier) ? 'checked' : '' }}>
                    <div>
                        <div class="projet-form-switch-label">Publier le projet</div>
                        <div class="projet-form-switch-desc">Le projet sera visible sur le site public</div>
                    </div>
                </label>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.projets.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>

        </form>

        {{-- ============================================ --}}
        {{-- SUPPRESSION DU PROJET --}}
        {{-- ============================================ --}}
        <form action="{{ route('admin.projets.destroy', $projet) }}"
              method="POST"
              class="projet-form-delete">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="btn btn-accent btn-sm"
                    onclick="return confirm('Supprimer définitivement ce projet ? Cette action est irréversible.');">
                <i class="fas fa-trash-alt"></i> Supprimer ce projet
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script src="{{ asset('assets/js/admin-projets-form.js') }}"></script>
@endsection