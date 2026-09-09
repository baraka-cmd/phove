@extends('layouts.admin')

@section('title', 'Modifier le partenaire — ' . $partenaire->nom . ' | PHOVE')

@section('page_title', 'Modifier le partenaire')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations du partenaire : ' . $partenaire->nom . ($partenaire->sigle ? ' (' . $partenaire->sigle . ')' : ''))

@php
    $breadcrumb = [
        ['label' => 'Partenaires', 'url' => route('admin.partenaires.index')],
        'Modifier : ' . $partenaire->nom
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
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier le partenaire</h2>
                <p>
                    {{ $partenaire->typeLibelle() }}
                    @if($partenaire->sigle)
                        — <strong>{{ $partenaire->sigle }}</strong>
                    @endif
                    — Créé le {{ $partenaire->created_at->format('d/m/Y') }} — 
                    {{ $partenaire->projets->count() }} projet(s) associé(s)
                </p>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.partenaires.update', $partenaire) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION 1 : Informations --}}
            <div class="partenaire-form-section">
                <h4 class="partenaire-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations du partenaire
                </h4>
                <div class="partenaire-form-grid">
                    <div class="partenaire-form-group partenaire-form-full">
                        <label for="nom" class="partenaire-form-label">Nom du partenaire <span class="required">*</span></label>
                        <input type="text" name="nom" id="nom" class="partenaire-form-input @error('nom') error @enderror" 
                               value="{{ old('nom', $partenaire->nom) }}" placeholder="Ex: Food and Agriculture Organization" required>
                        @error('nom') <span class="partenaire-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="partenaire-form-group">
                        <label for="sigle" class="partenaire-form-label">Sigle <span class="optional">(optionnel)</span></label>
                        <input type="text" name="sigle" id="sigle" class="partenaire-form-input @error('sigle') error @enderror" 
                               value="{{ old('sigle', $partenaire->sigle) }}" placeholder="Ex: FAO, WFP, OCHA" maxlength="20">
                        @error('sigle') <span class="partenaire-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="partenaire-form-hint">Sigle officiel du partenaire</span>
                    </div>

                    <div class="partenaire-form-group">
                        <label for="type" class="partenaire-form-label">Type de partenaire <span class="required">*</span></label>
                        <select name="type" id="type" class="partenaire-form-select @error('type') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            <option value="bailleur_fonds" {{ old('type', $partenaire->type) === 'bailleur_fonds' ? 'selected' : '' }}>Bailleur de fonds</option>
                            <option value="technique" {{ old('type', $partenaire->type) === 'technique' ? 'selected' : '' }}>Partenaire technique</option>
                            <option value="local" {{ old('type', $partenaire->type) === 'local' ? 'selected' : '' }}>Partenaire local</option>
                            <option value="international" {{ old('type', $partenaire->type) === 'international' ? 'selected' : '' }}>Partenaire international</option>
                            <option value="institutionnel" {{ old('type', $partenaire->type) === 'institutionnel' ? 'selected' : '' }}>Partenaire institutionnel</option>
                        </select>
                        @error('type') <span class="partenaire-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="partenaire-form-group">
                        <label for="statut" class="partenaire-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="partenaire-form-select @error('statut') error @enderror" required>
                            <option value="actif" {{ old('statut', $partenaire->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut', $partenaire->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
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
                               value="{{ old('email', $partenaire->email) }}" placeholder="Ex: fao@fao.org">
                    </div>
                    <div class="partenaire-form-group">
                        <label for="telephone" class="partenaire-form-label">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="partenaire-form-input" 
                               value="{{ old('telephone', $partenaire->telephone) }}" placeholder="Ex: +243 000 000 000">
                    </div>
                    <div class="partenaire-form-group">
                        <label for="adresse" class="partenaire-form-label">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="partenaire-form-input" 
                               value="{{ old('adresse', $partenaire->adresse) }}" placeholder="Ex: Goma, Nord-Kivu">
                    </div>
                    <div class="partenaire-form-group">
                        <label for="site_web" class="partenaire-form-label">Site web</label>
                        <input type="url" name="site_web" id="site_web" class="partenaire-form-input" 
                               value="{{ old('site_web', $partenaire->site_web) }}" placeholder="Ex: https://www.fao.org">
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
                               value="{{ old('date_debut', $partenaire->date_debut ? $partenaire->date_debut->format('Y-m-d') : '') }}">
                    </div>
                    <div class="partenaire-form-group">
                        <label for="date_fin" class="partenaire-form-label">Date de fin</label>
                        <input type="date" name="date_fin" id="date_fin" class="partenaire-form-input" 
                               value="{{ old('date_fin', $partenaire->date_fin ? $partenaire->date_fin->format('Y-m-d') : '') }}">
                    </div>
                    <div class="partenaire-form-group partenaire-form-full">
                        <label for="description" class="partenaire-form-label">Description</label>
                        <textarea name="description" id="description" class="partenaire-form-textarea" rows="3" 
                                  placeholder="Décrivez le partenariat...">{{ old('description', $partenaire->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 4 : Logo --}}
            <div class="partenaire-form-section">
                <h4 class="partenaire-form-section-title">
                    <i class="fas fa-image"></i> Logo
                </h4>
                @if($partenaire->logo)
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px; padding: 14px 18px; background: #FEF3C7; border-radius: 10px; border-left: 4px solid #F59E0B;">
                    <img src="{{ asset('storage/' . $partenaire->logo) }}" alt="Logo actuel" 
                         style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px; background: #FFFFFF; padding: 6px;" loading="lazy">
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: #1E293B; margin-bottom: 2px;">Logo actuel</p>
                        <p style="font-size: 11px; color: #64748B;">Laissez vide pour conserver ce logo.</p>
                    </div>
                </div>
                @endif
                <div class="partenaire-logo-upload">
                    <div class="partenaire-logo-preview" id="logoPreview" onclick="document.getElementById('logo').click();">
                        <img id="logoPreviewImg" src="" alt="Aperçu" style="display: none;">
                        <i class="fas fa-cloud-upload-alt partenaire-logo-placeholder" id="logoPlaceholder"></i>
                    </div>
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: #1E293B; margin-bottom: 4px;">
                            @if($partenaire->logo) Remplacer le logo @else Ajouter un logo @endif
                        </p>
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
                               {{ in_array($projet->id, old('projet_ids', $partenaire->projets->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>

        {{-- Suppression --}}
        <form action="{{ route('admin.partenaires.destroy', $partenaire) }}" method="POST" style="margin-top: 16px; text-align: right;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm btn-delete-partenaire" data-nom="{{ $partenaire->nom }}"
                    onclick="return confirm('Supprimer définitivement ce partenaire ?');">
                <i class="fas fa-trash-alt"></i> Supprimer ce partenaire
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-partenaires.js') }}"></script>
@endsection