@extends('layouts.admin')

@section('title', 'Ajouter un partenaire | PHOVE')

@section('page_title', 'Ajouter un partenaire')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Ajoutez un nouveau partenaire PHOVE (FAO, WFP, OCHA, UNESCO, GPE, etc.) et associez-le à des projets.')

@php
    $breadcrumb = [
        ['label' => 'Partenaires', 'url' => route('admin.partenaires.index')],
        'Ajouter un partenaire'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-partenaires.css') }}">
@endsection

@section('content')

    <div class="partenaire-form-card">

        {{-- En-tête --}}
        <div class="partenaire-form-header">
            <div class="partenaire-form-header-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <div>
                <h2>Nouveau partenaire</h2>
                <p>Remplissez les informations ci-dessous pour ajouter un partenaire.</p>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.partenaires.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- SECTION 1 : Informations --}}
            <div class="partenaire-form-section">
                <h4 class="partenaire-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations du partenaire
                </h4>
                <div class="partenaire-form-grid">
                    <div class="partenaire-form-group partenaire-form-full">
                        <label for="nom" class="partenaire-form-label">Nom du partenaire <span class="required">*</span></label>
                        <input type="text" name="nom" id="nom" class="partenaire-form-input @error('nom') error @enderror" 
                               value="{{ old('nom') }}" placeholder="Ex: Food and Agriculture Organization" required>
                        @error('nom') <span class="partenaire-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="partenaire-form-group">
                        <label for="sigle" class="partenaire-form-label">Sigle <span class="optional">(optionnel)</span></label>
                        <input type="text" name="sigle" id="sigle" class="partenaire-form-input @error('sigle') error @enderror" 
                               value="{{ old('sigle') }}" placeholder="Ex: FAO, WFP, OCHA" maxlength="20">
                        @error('sigle') <span class="partenaire-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="partenaire-form-hint">Sigle officiel du partenaire</span>
                    </div>

                    <div class="partenaire-form-group">
                        <label for="type" class="partenaire-form-label">Type de partenaire <span class="required">*</span></label>
                        <select name="type" id="type" class="partenaire-form-select @error('type') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            <option value="bailleur_fonds" {{ old('type') === 'bailleur_fonds' ? 'selected' : '' }}>Bailleur de fonds</option>
                            <option value="technique" {{ old('type') === 'technique' ? 'selected' : '' }}>Partenaire technique</option>
                            <option value="local" {{ old('type') === 'local' ? 'selected' : '' }}>Partenaire local</option>
                            <option value="international" {{ old('type') === 'international' ? 'selected' : '' }}>Partenaire international</option>
                            <option value="institutionnel" {{ old('type') === 'institutionnel' ? 'selected' : '' }}>Partenaire institutionnel</option>
                        </select>
                        @error('type') <span class="partenaire-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="partenaire-form-group">
                        <label for="statut" class="partenaire-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="partenaire-form-select @error('statut') error @enderror" required>
                            <option value="actif" {{ old('statut', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Coordonnées --}}
            <div class="partenaire-form-section">
                <h4 class="partenaire-form-section-title">
                    <i class="fas fa-address-book"></i> Coordonnées
                </h4>
                <div class="partenaire-form-grid">
                    <div class="partenaire-form-group">
                        <label for="email" class="partenaire-form-label">Email</label>
                        <input type="email" name="email" id="email" class="partenaire-form-input" 
                               value="{{ old('email') }}" placeholder="Ex: fao@fao.org">
                    </div>
                    <div class="partenaire-form-group">
                        <label for="telephone" class="partenaire-form-label">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="partenaire-form-input" 
                               value="{{ old('telephone') }}" placeholder="Ex: +243 000 000 000">
                    </div>
                    <div class="partenaire-form-group">
                        <label for="adresse" class="partenaire-form-label">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="partenaire-form-input" 
                               value="{{ old('adresse') }}" placeholder="Ex: Goma, Nord-Kivu">
                    </div>
                    <div class="partenaire-form-group">
                        <label for="site_web" class="partenaire-form-label">Site web</label>
                        <input type="url" name="site_web" id="site_web" class="partenaire-form-input" 
                               value="{{ old('site_web') }}" placeholder="Ex: https://www.fao.org">
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Période et description --}}
            <div class="partenaire-form-section">
                <h4 class="partenaire-form-section-title">
                    <i class="fas fa-calendar-alt"></i> Période et description
                </h4>
                <div class="partenaire-form-grid">
                    <div class="partenaire-form-group">
                        <label for="date_debut" class="partenaire-form-label">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" class="partenaire-form-input" 
                               value="{{ old('date_debut', date('Y-m-d')) }}">
                    </div>
                    <div class="partenaire-form-group">
                        <label for="date_fin" class="partenaire-form-label">Date de fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="partenaire-form-input" 
                               value="{{ old('date_fin') }}">
                    </div>
                    <div class="partenaire-form-group partenaire-form-full">
                        <label for="description" class="partenaire-form-label">Description</label>
                        <textarea name="description" id="description" class="partenaire-form-textarea" rows="3" 
                                  placeholder="Décrivez le partenariat (ex: Partenaire du projet de résilience WFP)...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 4 : Logo --}}
            <div class="partenaire-form-section">
                <h4 class="partenaire-form-section-title">
                    <i class="fas fa-image"></i> Logo
                </h4>
                <div class="partenaire-logo-upload">
                    <div class="partenaire-logo-preview" id="logoPreview" onclick="document.getElementById('logo').click();">
                        <img id="logoPreviewImg" src="" alt="Aperçu" style="display: none;">
                        <i class="fas fa-cloud-upload-alt partenaire-logo-placeholder" id="logoPlaceholder"></i>
                    </div>
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: #1E293B; margin-bottom: 4px;">Logo du partenaire</p>
                        <p style="font-size: 11px; color: #64748B; margin-bottom: 8px;">Formats : JPG, PNG, WebP, SVG — Max 2 Mo</p>
                        <input type="file" name="logo" id="logo" class="partenaire-form-input" 
                               accept="image/jpeg,image/jpg,image/png,image/webp,image/svg+xml" style="display: none;">
                        @error('logo') <span class="partenaire-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 5 : Projets associés --}}
            <div class="partenaire-form-section">
                <h4 class="partenaire-form-section-title">
                    <i class="fas fa-project-diagram"></i> Projets associés
                </h4>
                @if($projets->count() > 0)
                <div class="partenaires-projets-checkboxes">
                    @foreach($projets as $projet)
                    <label class="partenaire-checkbox-item">
                        <input type="checkbox" name="projet_ids[]" value="{{ $projet->id }}" 
                               {{ in_array($projet->id, old('projet_ids', [])) ? 'checked' : '' }}>
                        <span>{{ Str::limit($projet->titre, 50) }}</span>
                        @if($projet->code_projet)
                            <small style="color: #64748B;">({{ $projet->code_projet }})</small>
                        @endif
                    </label>
                    @endforeach
                </div>
                @else
                <p style="font-size: 13px; color: #64748B; font-style: italic;">
                    Aucun projet disponible. Créez d'abord un projet.
                </p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="partenaire-form-actions">
                <a href="{{ route('admin.partenaires.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer le partenaire
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-partenaires.js') }}"></script>
@endsection