@extends('layouts.admin')

@section('title', 'Modifier le témoignage')

@section('page_title', 'Modifier le témoignage')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations du bénéficiaire et le contenu de son témoignage.')

@php
    $breadcrumb = ['Témoignages' => route('admin.temoignages.index'), 'Modifier' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-temoignages.css') }}">
@endsection

@section('content')

    <div class="temoignage-form-card">

        {{-- En-tête du formulaire --}}
        <div class="temoignage-form-header">
            <div class="temoignage-form-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier le témoignage</h2>
                <p>
                    Bénéficiaire : <strong>{{ $temoignage->nomComplet() }}</strong> —
                    <span class="temoignage-statut-badge temoignage-statut-{{ $temoignage->statut }}" style="margin-left: 6px;">
                        <i class="fas fa-{{ 
                            $temoignage->statut === 'en_attente' ? 'clock' : 
                            ($temoignage->statut === 'approuve' ? 'check-circle' : 
                            ($temoignage->statut === 'rejete' ? 'times-circle' : 'archive'))
                        }}"></i>
                        {{ $temoignage->statutLibelle() }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('admin.temoignages.update', $temoignage) }}" method="POST" enctype="multipart/form-data" id="temoignageForm">
            @csrf
            @method('PUT')

            {{-- ============================================ --}}
            {{-- SECTION 1 : INFORMATIONS DU BÉNÉFICIAIRE --}}
            {{-- ============================================ --}}
            <div class="temoignage-form-section">
                <div class="temoignage-form-section-title">
                    <i class="fas fa-user-circle"></i> Informations du bénéficiaire
                </div>

                <div class="temoignage-form-grid">
                    {{-- Nom --}}
                    <div class="temoignage-form-group">
                        <label for="nom" class="temoignage-form-label">
                            Nom <span class="required">*</span>
                        </label>
                        <input type="text" name="nom" id="nom" 
                               class="temoignage-form-input @error('nom') is-invalid @enderror" 
                               value="{{ old('nom', $temoignage->nom) }}" 
                               placeholder="Ex: BISIMWA" 
                               maxlength="100" required autofocus>
                        @error('nom')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Post-nom --}}
                    <div class="temoignage-form-group">
                        <label for="post_nom" class="temoignage-form-label">
                            Post-nom
                        </label>
                        <input type="text" name="post_nom" id="post_nom" 
                               class="temoignage-form-input @error('post_nom') is-invalid @enderror" 
                               value="{{ old('post_nom', $temoignage->post_nom) }}" 
                               placeholder="Ex: BASHONGA" 
                               maxlength="100">
                        @error('post_nom')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Prénom --}}
                    <div class="temoignage-form-group">
                        <label for="prenom" class="temoignage-form-label">
                            Prénom
                        </label>
                        <input type="text" name="prenom" id="prenom" 
                               class="temoignage-form-input @error('prenom') is-invalid @enderror" 
                               value="{{ old('prenom', $temoignage->prenom) }}" 
                               placeholder="Ex: Riphin" 
                               maxlength="100">
                        @error('prenom')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Sexe --}}
                    <div class="temoignage-form-group">
                        <label for="sexe" class="temoignage-form-label">
                            Sexe <span class="required">*</span>
                        </label>
                        <select name="sexe" id="sexe" class="temoignage-form-select @error('sexe') is-invalid @enderror" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="M" {{ old('sexe', $temoignage->sexe) === 'M' ? 'selected' : '' }}>Homme</option>
                            <option value="F" {{ old('sexe', $temoignage->sexe) === 'F' ? 'selected' : '' }}>Femme</option>
                        </select>
                        @error('sexe')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Âge --}}
                    <div class="temoignage-form-group">
                        <label for="age" class="temoignage-form-label">
                            Âge
                        </label>
                        <input type="number" name="age" id="age" 
                               class="temoignage-form-input @error('age') is-invalid @enderror" 
                               value="{{ old('age', $temoignage->age) }}" 
                               placeholder="Ex: 42" 
                               min="1" max="120">
                        @error('age')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Catégorie --}}
                    <div class="temoignage-form-group">
                        <label for="categorie" class="temoignage-form-label">
                            Catégorie <span class="required">*</span>
                        </label>
                        <select name="categorie" id="categorie" class="temoignage-form-select @error('categorie') is-invalid @enderror" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="femme" {{ old('categorie', $temoignage->categorie) === 'femme' ? 'selected' : '' }}>Femme</option>
                            <option value="enfant" {{ old('categorie', $temoignage->categorie) === 'enfant' ? 'selected' : '' }}>Enfant</option>
                            <option value="handicape" {{ old('categorie', $temoignage->categorie) === 'handicape' ? 'selected' : '' }}>Personne handicapée</option>
                            <option value="troisieme_age" {{ old('categorie', $temoignage->categorie) === 'troisieme_age' ? 'selected' : '' }}>3ème âge</option>
                            <option value="orphelin" {{ old('categorie', $temoignage->categorie) === 'orphelin' ? 'selected' : '' }}>Orphelin</option>
                            <option value="deplace_interne" {{ old('categorie', $temoignage->categorie) === 'deplace_interne' ? 'selected' : '' }}>Déplacé interne</option>
                            <option value="refugie" {{ old('categorie', $temoignage->categorie) === 'refugie' ? 'selected' : '' }}>Réfugié</option>
                            <option value="rapatrie" {{ old('categorie', $temoignage->categorie) === 'rapatrie' ? 'selected' : '' }}>Rapatrié</option>
                            <option value="autre" {{ old('categorie', $temoignage->categorie) === 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('categorie')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : LOCALISATION --}}
            {{-- ============================================ --}}
            <div class="temoignage-form-section">
                <div class="temoignage-form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Localisation
                </div>

                <div class="temoignage-form-grid">
                    {{-- Province --}}
                    <div class="temoignage-form-group">
                        <label for="province" class="temoignage-form-label">
                            Province
                        </label>
                        <select name="province" id="province" class="temoignage-form-select @error('province') is-invalid @enderror">
                            <option value="">-- Sélectionnez --</option>
                            <option value="Nord-Kivu" {{ old('province', $temoignage->province) === 'Nord-Kivu' ? 'selected' : '' }}>Nord-Kivu</option>
                            <option value="Sud-Kivu" {{ old('province', $temoignage->province) === 'Sud-Kivu' ? 'selected' : '' }}>Sud-Kivu</option>
                            <option value="Ituri" {{ old('province', $temoignage->province) === 'Ituri' ? 'selected' : '' }}>Ituri</option>
                            <option value="Kinshasa" {{ old('province', $temoignage->province) === 'Kinshasa' ? 'selected' : '' }}>Kinshasa</option>
                            <option value="Haut-Katanga" {{ old('province', $temoignage->province) === 'Haut-Katanga' ? 'selected' : '' }}>Haut-Katanga</option>
                            <option value="Tanganyika" {{ old('province', $temoignage->province) === 'Tanganyika' ? 'selected' : '' }}>Tanganyika</option>
                            <option value="Maniema" {{ old('province', $temoignage->province) === 'Maniema' ? 'selected' : '' }}>Maniema</option>
                            <option value="autre" {{ old('province', $temoignage->province) === 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('province')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Localisation précise --}}
                    <div class="temoignage-form-group">
                        <label for="localisation" class="temoignage-form-label">
                            Localisation précise
                        </label>
                        <input type="text" name="localisation" id="localisation" 
                               class="temoignage-form-input @error('localisation') is-invalid @enderror" 
                               value="{{ old('localisation', $temoignage->localisation) }}" 
                               placeholder="Ex: Minova, Territoire de Kalehe" 
                               maxlength="255">
                        @error('localisation')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3 : PHOTO --}}
            {{-- ============================================ --}}
            <div class="temoignage-form-section">
                <div class="temoignage-form-section-title">
                    <i class="fas fa-camera"></i> Photo du bénéficiaire
                </div>

                <div class="temoignage-form-group">
                    <label class="temoignage-form-label">
                        Photo <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel — laissez vide pour conserver la photo actuelle)</small>
                    </label>
                    <div class="temoignage-photo-upload">
                        <label for="photo" class="temoignage-photo-preview" id="photoPreview">
                            @if($temoignage->photo)
                                <img id="photoPreviewImg" src="{{ $temoignage->urlPhoto() }}" alt="Photo actuelle" style="display: block;">
                                <div class="temoignage-photo-placeholder" id="photoPlaceholder" style="display: none;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @else
                                <img id="photoPreviewImg" src="" alt="Aperçu" style="display: none;">
                                <div class="temoignage-photo-placeholder" id="photoPlaceholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </label>
                        <div class="temoignage-photo-info">
                            <p>
                                @if($temoignage->photo)
                                    <span style="color: var(--color-success);">
                                        <i class="fas fa-check-circle"></i> Photo actuelle
                                    </span>
                                    <br>
                                    <a href="{{ $temoignage->urlPhoto() }}" target="_blank" style="font-size: 12px; color: var(--color-primary); text-decoration: underline;">
                                        <i class="fas fa-external-link-alt"></i> Voir la photo
                                    </a>
                                @else
                                    Cliquez pour choisir une photo
                                @endif
                            </p>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> JPG, PNG ou WebP</li>
                                <li><i class="fas fa-check-circle"></i> Taille max : 5 Mo</li>
                                <li><i class="fas fa-check-circle"></i> Format portrait recommandé</li>
                                @if($temoignage->photo)
                                    <li>
                                        <button type="button" id="removePhotoBtn" style="background: none; border: none; color: var(--color-accent); font-size: 12px; cursor: pointer; padding: 0; text-decoration: underline;">
                                            <i class="fas fa-trash-alt"></i> Supprimer la photo
                                        </button>
                                    </li>
                                @endif
                            </ul>
                            <input type="file" name="photo" id="photo" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp" 
                                   style="display: none;">
                            <input type="hidden" name="remove_photo" id="removePhotoFlag" value="0">
                        </div>
                    </div>
                    @error('photo')
                        <span class="temoignage-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4 : TÉMOIGNAGE --}}
            {{-- ============================================ --}}
            <div class="temoignage-form-section">
                <div class="temoignage-form-section-title">
                    <i class="fas fa-quote-right"></i> Contenu du témoignage
                </div>

                <div class="temoignage-form-group">
                    <label for="contenu" class="temoignage-form-label">
                        Témoignage <span class="required">*</span>
                    </label>
                    <textarea name="contenu" id="contenu" 
                              class="temoignage-form-textarea @error('contenu') is-invalid @enderror" 
                              rows="8" 
                              placeholder="Rédigez le témoignage du bénéficiaire ici..."
                              maxlength="5000" required>{{ old('contenu', $temoignage->contenu) }}</textarea>
                    <small style="font-size: 11px; color: var(--color-text-muted); margin-top: 4px; text-align: right; display: block;">
                        <span id="contenuCounter">0</span>/5000 caractères
                    </small>
                    @error('contenu')
                        <span class="temoignage-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 5 : PROJET & STATUT --}}
            {{-- ============================================ --}}
            <div class="temoignage-form-section">
                <div class="temoignage-form-section-title">
                    <i class="fas fa-cog"></i> Paramètres
                </div>

                <div class="temoignage-form-grid">
                    {{-- Projet lié --}}
                    <div class="temoignage-form-group">
                        <label for="projet_id" class="temoignage-form-label">
                            Projet lié <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel)</small>
                        </label>
                        <select name="projet_id" id="projet_id" class="temoignage-form-select @error('projet_id') is-invalid @enderror">
                            <option value="">-- Aucun projet --</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id', $temoignage->projet_id) == $projet->id ? 'selected' : '' }}>
                                    {{ Str::limit($projet->titre, 55) }}
                                </option>
                            @endforeach
                        </select>
                        @error('projet_id')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Statut --}}
                    <div class="temoignage-form-group">
                        <label for="statut" class="temoignage-form-label">
                            Statut <span class="required">*</span>
                        </label>
                        <select name="statut" id="statut" class="temoignage-form-select @error('statut') is-invalid @enderror" required>
                            <option value="en_attente" {{ old('statut', $temoignage->statut) === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="approuve" {{ old('statut', $temoignage->statut) === 'approuve' ? 'selected' : '' }}>Approuvé</option>
                            <option value="rejete" {{ old('statut', $temoignage->statut) === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                            <option value="archive" {{ old('statut', $temoignage->statut) === 'archive' ? 'selected' : '' }}>Archivé</option>
                        </select>
                        @error('statut')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Ordre --}}
                    <div class="temoignage-form-group">
                        <label for="ordre" class="temoignage-form-label">
                            Ordre d'affichage
                        </label>
                        <input type="number" name="ordre" id="ordre" 
                               class="temoignage-form-input @error('ordre') is-invalid @enderror" 
                               value="{{ old('ordre', $temoignage->ordre) }}" 
                               min="0" max="9999">
                        @error('ordre')
                            <span class="temoignage-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 6 : INFORMATIONS DE TRAITEMENT --}}
            {{-- ============================================ --}}
            <div class="temoignage-form-section">
                <div class="temoignage-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations de traitement
                </div>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;">
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Soumis le</div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1a1a1a;">
                            <i class="fas fa-calendar-plus" style="color: var(--color-primary); margin-right: 4px;"></i>
                            {{ $temoignage->date_soumission ? $temoignage->date_soumission->format('d/m/Y') : $temoignage->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Traité par</div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1a1a1a;">
                            <i class="fas fa-user-check" style="color: var(--color-info); margin-right: 4px;"></i>
                            {{ $temoignage->traitePar ? $temoignage->traitePar->nom : '—' }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Validé le</div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1a1a1a;">
                            <i class="fas fa-calendar-check" style="color: var(--color-success); margin-right: 4px;"></i>
                            {{ $temoignage->date_validation ? $temoignage->date_validation->format('d/m/Y') : '—' }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Modifié le</div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1a1a1a;">
                            <i class="fas fa-clock" style="color: #e67e22; margin-right: 4px;"></i>
                            {{ $temoignage->updated_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="temoignage-form-actions">
                <a href="{{ route('admin.temoignages.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.temoignages.show', $temoignage) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> Voir les détails
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Compteur de caractères pour le contenu
            const contenu = document.getElementById('contenu');
            const counter = document.getElementById('contenuCounter');
            if (contenu && counter) {
                counter.textContent = contenu.value.length;
                contenu.addEventListener('input', function () {
                    counter.textContent = this.value.length;
                });
            }

            // Suppression de la photo actuelle
            const removePhotoBtn = document.getElementById('removePhotoBtn');
            const removePhotoFlag = document.getElementById('removePhotoFlag');
            const photoPreviewImg = document.getElementById('photoPreviewImg');
            const photoPlaceholder = document.getElementById('photoPlaceholder');
            const photoInput = document.getElementById('photo');

            if (removePhotoBtn && removePhotoFlag && photoPreviewImg && photoPlaceholder) {
                removePhotoBtn.addEventListener('click', function () {
                    if (confirm('Voulez-vous vraiment supprimer la photo actuelle ?')) {
                        removePhotoFlag.value = '1';
                        photoPreviewImg.style.display = 'none';
                        photoPreviewImg.src = '';
                        photoPlaceholder.style.display = 'flex';
                        photoInput.value = '';
                        removePhotoBtn.style.display = 'none';
                    }
                });
            }

            // Réinitialiser le flag si une nouvelle photo est choisie
            if (photoInput && removePhotoFlag) {
                photoInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        removePhotoFlag.value = '0';
                        if (removePhotoBtn) {
                            removePhotoBtn.style.display = 'inline';
                        }
                    }
                });
            }
        });
    </script>
    <script src="{{ asset('assets/js/admin-temoignages.js') }}"></script>
@endsection