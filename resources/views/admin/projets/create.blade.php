@extends('layouts.admin')

@section('title', 'Ajouter un projet | PHOVE')

@section('page_title', 'Ajouter un projet')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Créez un nouveau projet humanitaire PHOVE en remplissant le formulaire ci-dessous.')

@php
    $breadcrumb = [
        ['label' => 'Projets', 'url' => route('admin.projets.index')],
        'Ajouter un projet'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-projets-form.css') }}">
@endsection

@section('content')

    <div class="projet-form-card">

        {{-- En-tête --}}
        <div class="projet-form-header">
            <div class="projet-form-header-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div>
                <h2>Nouveau projet</h2>
                <p>Remplissez les informations ci-dessous pour créer un nouveau projet humanitaire PHOVE.</p>
            </div>
        </div>

        {{-- Messages d'erreur --}}
        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.projets.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- SECTION 1 : Informations générales --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations générales
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label for="titre" class="projet-form-label">Titre du projet <span class="required">*</span></label>
                        <input type="text" name="titre" id="titre" class="projet-form-input @error('titre') error @enderror" 
                               value="{{ old('titre') }}" placeholder="Ex: Appui nutritionnel aux orphelins du village Kiziba I" required>
                        @error('titre') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="projet-form-hint">Slug : <strong id="slugPreview">{{ old('titre') ? Str::slug(old('titre')) : '...' }}</strong></span>
                    </div>

                    <div class="projet-form-group">
                        <label for="code_projet" class="projet-form-label">Code projet <span class="optional">(optionnel)</span></label>
                        <input type="text" name="code_projet" id="code_projet" class="projet-form-input @error('code_projet') error @enderror" 
                               value="{{ old('code_projet') }}" placeholder="Ex: UNJP/RDC/082/WFP" maxlength="50">
                        @error('code_projet') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="projet-form-hint">Code officiel du projet (bailleurs, ONU, etc.)</span>
                    </div>

                    <div class="projet-form-group">
                        <label for="domaine" class="projet-form-label">Domaine d'intervention <span class="required">*</span></label>
                        <select name="domaine" id="domaine" class="projet-form-select @error('domaine') error @enderror" required>
                            <option value="">Sélectionnez un domaine...</option>
                            @foreach($domaines as $value => $label)
                                <option value="{{ $value }}" {{ old('domaine') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('domaine') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="projet-form-select @error('statut') error @enderror" required>
                            <option value="">Sélectionnez un statut...</option>
                            @foreach($statuts as $value => $label)
                                <option value="{{ $value }}" {{ old('statut', 'en_preparation') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('statut') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Localisation et Période --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Localisation et période
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="zone_geographique" class="projet-form-label">Zone géographique <span class="required">*</span></label>
                        <input type="text" name="zone_geographique" id="zone_geographique" class="projet-form-input @error('zone_geographique') error @enderror" 
                               value="{{ old('zone_geographique') }}" placeholder="Ex: Village Kiziba I, Territoire de Nyiragongo" required>
                        @error('zone_geographique') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="province" class="projet-form-label">Province <span class="optional">(optionnel)</span></label>
                        <input type="text" name="province" id="province" class="projet-form-input" 
                               value="{{ old('province') }}" placeholder="Ex: Nord-Kivu">
                    </div>

                    <div class="projet-form-group">
                        <label for="date_debut" class="projet-form-label">Date de début <span class="required">*</span></label>
                        <input type="date" name="date_debut" id="date_debut" class="projet-form-input @error('date_debut') error @enderror" 
                               value="{{ old('date_debut', date('Y-m-d')) }}" required>
                        @error('date_debut') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="date_fin" class="projet-form-label">Date de fin <span class="optional">(optionnel)</span></label>
                        <input type="date" name="date_fin" id="date_fin" class="projet-form-input @error('date_fin') error @enderror" 
                               value="{{ old('date_fin') }}">
                        @error('date_fin') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Budget et Financement --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-coins"></i> Budget et financement
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="budget_total" class="projet-form-label">Budget total ($) <span class="optional">(optionnel)</span></label>
                        <input type="number" name="budget_total" id="budget_total" class="projet-form-input" 
                               value="{{ old('budget_total') }}" step="0.01" min="0" placeholder="Ex: 15000">
                    </div>

                    <div class="projet-form-group">
                        <label for="budget_utilise" class="projet-form-label">Budget utilisé ($) <span class="optional">(optionnel)</span></label>
                        <input type="number" name="budget_utilise" id="budget_utilise" class="projet-form-input" 
                               value="{{ old('budget_utilise') }}" step="0.01" min="0" placeholder="Ex: 7500">
                    </div>

                    <div class="projet-form-group">
                        <label for="bailleur" class="projet-form-label">Bailleur de fonds <span class="optional">(optionnel)</span></label>
                        <input type="text" name="bailleur" id="bailleur" class="projet-form-input" 
                               value="{{ old('bailleur') }}" placeholder="Ex: FAO, WFP, PHOVE">
                    </div>

                    <div class="projet-form-group">
                        <label for="source_financement" class="projet-form-label">Source de financement <span class="optional">(optionnel)</span></label>
                        <input type="text" name="source_financement" id="source_financement" class="projet-form-input" 
                               value="{{ old('source_financement') }}" placeholder="Ex: Partenariat international, Autofinancement">
                    </div>
                </div>
            </div>

            {{-- SECTION 4 : Contenu --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-file-alt"></i> Contenu du projet
                </h4>

                <div class="projet-form-group projet-form-full">
                    <label for="description" class="projet-form-label">Description <span class="required">*</span></label>
                    <textarea name="description" id="description" class="projet-form-textarea @error('description') error @enderror" rows="5" 
                              placeholder="Décrivez le projet en détail..." required>{{ old('description') }}</textarea>
                    @error('description') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="projet-form-group projet-form-full">
                    <label for="objectifs" class="projet-form-label">Objectifs <span class="optional">(optionnel)</span></label>
                    <textarea name="objectifs" id="objectifs" class="projet-form-textarea" rows="3" 
                              placeholder="Listez les objectifs du projet...">{{ old('objectifs') }}</textarea>
                </div>

                <div class="projet-form-group projet-form-full">
                    <label for="resultats_attendus" class="projet-form-label">Résultats attendus <span class="optional">(optionnel)</span></label>
                    <textarea name="resultats_attendus" id="resultats_attendus" class="projet-form-textarea" rows="3" 
                              placeholder="Décrivez les résultats attendus...">{{ old('resultats_attendus') }}</textarea>
                </div>
            </div>

            {{-- SECTION 5 : Image --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-image"></i> Image principale
                </h4>

                <div class="projet-image-upload">
                    <div class="projet-image-preview" id="imagePreview" onclick="document.getElementById('image_principale').click();">
                        <img id="imagePreviewImg" src="" alt="Aperçu" style="display: none;">
                        <i class="fas fa-cloud-upload-alt projet-image-placeholder" id="imagePlaceholder"></i>
                    </div>
                    <div class="projet-image-info">
                        <p>Image de couverture du projet</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Formats acceptés : JPG, PNG, WebP</li>
                            <li><i class="fas fa-check-circle"></i> Taille maximale : 5 Mo</li>
                            <li><i class="fas fa-check-circle"></i> Taille recommandée : 1200 x 800 px</li>
                        </ul>
                        <input type="file" name="image_principale" id="image_principale" class="projet-image-input" 
                               accept="image/jpeg,image/jpg,image/png,image/webp" style="display: none;">
                        <span id="imageInfo" style="font-size: 12px; color: #64748B;">Aucun fichier sélectionné</span>
                        @error('image_principale') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 6 : Publication --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-eye"></i> Publication
                </h4>

                <label class="projet-form-switch">
                    <input type="checkbox" name="publier" value="1" {{ old('publier', true) ? 'checked' : '' }}>
                    <div>
                        <div class="projet-form-switch-label">Publier le projet</div>
                        <div class="projet-form-switch-desc">Le projet sera visible sur le site public</div>
                    </div>
                </label>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.projets.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Créer le projet
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-projets-form.js') }}"></script>
@endsection