@extends('layouts.admin')

@section('title', 'Modifier le projet')

@section('page_title', 'Modifier le projet')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations du projet "' . Str::limit($projet->titre, 50) . '".')

@php
    $breadcrumb = [
        ['label' => 'Projets', 'url' => route('admin.projets.index')],
        'Modifier : ' . Str::limit($projet->titre, 40)
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
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier le projet</h2>
                <p>
                    Créé le {{ $projet->created_at->format('d/m/Y') }} — 
                    Statut : <strong>{{ $projet->statutLibelle() }}</strong>
                </p>
            </div>
        </div>

        {{-- Messages d'erreur --}}
        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.projets.update', $projet) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION 1 : Informations générales --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations générales
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label for="titre" class="projet-form-label">Titre du projet <span class="required">*</span></label>
                        <input type="text" name="titre" id="titre" class="projet-form-input @error('titre') error @enderror" 
                               value="{{ old('titre', $projet->titre) }}" placeholder="Ex: Soutien à la production maraîchère pour la sécurité alimentaire" required>
                        @error('titre') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="projet-form-hint">Slug actuel : <strong>{{ $projet->slug }}</strong></span>
                    </div>

                    <div class="projet-form-group">
                        <label for="domaine" class="projet-form-label">Domaine d'intervention <span class="required">*</span></label>
                        <select name="domaine" id="domaine" class="projet-form-select @error('domaine') error @enderror" required>
                            <option value="">Sélectionnez un domaine...</option>
                            @foreach($domaines as $value => $label)
                                <option value="{{ $value }}" {{ old('domaine', $projet->domaine) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('domaine') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="projet-form-select @error('statut') error @enderror" required>
                            <option value="">Sélectionnez un statut...</option>
                            @foreach($statuts as $value => $label)
                                <option value="{{ $value }}" {{ old('statut', $projet->statut) === $value ? 'selected' : '' }}>{{ $label }}</option>
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
                               value="{{ old('zone_geographique', $projet->zone_geographique) }}" placeholder="Ex: Minova et Kirotshe" required>
                        @error('zone_geographique') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="province" class="projet-form-label">Province <span class="optional">(optionnel)</span></label>
                        <input type="text" name="province" id="province" class="projet-form-input" 
                               value="{{ old('province', $projet->province) }}" placeholder="Ex: Nord-Kivu et Sud-Kivu">
                    </div>

                    <div class="projet-form-group">
                        <label for="date_debut" class="projet-form-label">Date de début <span class="required">*</span></label>
                        <input type="date" name="date_debut" id="date_debut" class="projet-form-input @error('date_debut') error @enderror" 
                               value="{{ old('date_debut', $projet->date_debut->format('Y-m-d')) }}" required>
                        @error('date_debut') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="date_fin" class="projet-form-label">Date de fin <span class="optional">(optionnel)</span></label>
                        <input type="date" name="date_fin" id="date_fin" class="projet-form-input @error('date_fin') error @enderror" 
                               value="{{ old('date_fin', $projet->date_fin ? $projet->date_fin->format('Y-m-d') : '') }}">
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
                        <label for="budget_total" class="projet-form-label">Budget total ($)</label>
                        <input type="number" name="budget_total" id="budget_total" class="projet-form-input" 
                               value="{{ old('budget_total', $projet->budget_total) }}" step="0.01" min="0" placeholder="Ex: 15000">
                    </div>

                    <div class="projet-form-group">
                        <label for="budget_utilise" class="projet-form-label">Budget utilisé ($)</label>
                        <input type="number" name="budget_utilise" id="budget_utilise" class="projet-form-input" 
                               value="{{ old('budget_utilise', $projet->budget_utilise) }}" step="0.01" min="0" placeholder="Ex: 7500">
                    </div>

                    <div class="projet-form-group">
                        <label for="bailleur" class="projet-form-label">Bailleur de fonds</label>
                        <input type="text" name="bailleur" id="bailleur" class="projet-form-input" 
                               value="{{ old('bailleur', $projet->bailleur) }}" placeholder="Ex: Autofinancement du Conseil d'Administration">
                    </div>

                    <div class="projet-form-group">
                        <label for="source_financement" class="projet-form-label">Source de financement</label>
                        <input type="text" name="source_financement" id="source_financement" class="projet-form-input" 
                               value="{{ old('source_financement', $projet->source_financement) }}" placeholder="Ex: Autofinancement, Partenariat, Subvention">
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
                              placeholder="Décrivez le projet en détail..." required>{{ old('description', $projet->description) }}</textarea>
                    @error('description') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="projet-form-group projet-form-full">
                    <label for="objectifs" class="projet-form-label">Objectifs</label>
                    <textarea name="objectifs" id="objectifs" class="projet-form-textarea" rows="3" 
                              placeholder="Listez les objectifs du projet...">{{ old('objectifs', $projet->objectifs) }}</textarea>
                </div>

                <div class="projet-form-group projet-form-full">
                    <label for="resultats_attendus" class="projet-form-label">Résultats attendus</label>
                    <textarea name="resultats_attendus" id="resultats_attendus" class="projet-form-textarea" rows="3" 
                              placeholder="Décrivez les résultats attendus...">{{ old('resultats_attendus', $projet->resultats_attendus) }}</textarea>
                </div>
            </div>

            {{-- SECTION 5 : Image --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-image"></i> Image principale
                </h4>

                {{-- Image actuelle --}}
                @if($projet->image_principale)
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px; padding: 14px 18px; background: #fef5e7; border-radius: 10px; border-left: 4px solid #e67e22;">
                    <img src="{{ asset('storage/' . $projet->image_principale) }}" alt="Image actuelle" 
                         style="width: 100px; height: 70px; object-fit: cover; border-radius: 8px;">
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: #1a1a1a; margin-bottom: 2px;">Image actuelle</p>
                        <p style="font-size: 11px; color: #888;">Laissez vide pour conserver cette image.</p>
                    </div>
                </div>
                @endif

                <div class="projet-image-upload">
                    <div class="projet-image-preview" id="imagePreview" onclick="document.getElementById('image_principale').click();">
                        <img id="imagePreviewImg" src="" alt="Aperçu" style="display: none;">
                        <i class="fas fa-cloud-upload-alt projet-image-placeholder" id="imagePlaceholder"></i>
                    </div>
                    <div class="projet-image-info">
                        <p>@if($projet->image_principale) Remplacer l'image @else Ajouter une image @endif</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Formats acceptés : JPG, PNG, WebP</li>
                            <li><i class="fas fa-check-circle"></i> Taille maximale : 5 Mo</li>
                            <li><i class="fas fa-check-circle"></i> Taille recommandée : 1200 x 800 px</li>
                        </ul>
                        <input type="file" name="image_principale" id="image_principale" class="projet-image-input" 
                               accept="image/jpeg,image/jpg,image/png,image/webp" style="display: none;">
                        <span id="imageInfo" style="font-size: 12px; color: var(--color-text-muted);">Aucun nouveau fichier sélectionné</span>
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
                    <input type="checkbox" name="publier" value="1" {{ old('publier', $projet->publier) ? 'checked' : '' }}>
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
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>

        {{-- Suppression --}}
        <form action="{{ route('admin.projets.destroy', $projet) }}" method="POST" style="margin-top: 16px; text-align: right;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce projet ? Cette action est irréversible.');">
                <i class="fas fa-trash-alt"></i> Supprimer ce projet
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-projets-form.js') }}"></script>
@endsection