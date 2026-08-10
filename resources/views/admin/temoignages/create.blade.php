@extends('layouts.admin')

@section('title', 'Nouveau témoignage')

@section('page_title', 'Nouveau témoignage')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Enregistrez un témoignage de bénéficiaire. Les champs marqués d\'un astérisque (*) sont obligatoires.')

@php
    $breadcrumb = ['Témoignages' => route('admin.temoignages.index'), 'Nouveau' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-temoignages.css') }}">
@endsection

@section('content')

    <div class="temoignage-form-card">

        {{-- En-tête du formulaire --}}
        <div class="temoignage-form-header">
            <div class="temoignage-form-header-icon">
                <i class="fas fa-quote-right"></i>
            </div>
            <div>
                <h2>Nouveau témoignage</h2>
                <p>Remplissez les informations du bénéficiaire et le contenu de son témoignage.</p>
            </div>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('admin.temoignages.store') }}" method="POST" enctype="multipart/form-data" id="temoignageForm">
            @csrf

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
                               value="{{ old('nom') }}" 
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
                               value="{{ old('post_nom') }}" 
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
                               value="{{ old('prenom') }}" 
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
                            <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Homme</option>
                            <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Femme</option>
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
                               value="{{ old('age') }}" 
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
                            <option value="femme" {{ old('categorie') === 'femme' ? 'selected' : '' }}>Femme</option>
                            <option value="enfant" {{ old('categorie') === 'enfant' ? 'selected' : '' }}>Enfant</option>
                            <option value="handicape" {{ old('categorie') === 'handicape' ? 'selected' : '' }}>Personne handicapée</option>
                            <option value="troisieme_age" {{ old('categorie') === 'troisieme_age' ? 'selected' : '' }}>3ème âge</option>
                            <option value="orphelin" {{ old('categorie') === 'orphelin' ? 'selected' : '' }}>Orphelin</option>
                            <option value="deplace_interne" {{ old('categorie') === 'deplace_interne' ? 'selected' : '' }}>Déplacé interne</option>
                            <option value="refugie" {{ old('categorie') === 'refugie' ? 'selected' : '' }}>Réfugié</option>
                            <option value="rapatrie" {{ old('categorie') === 'rapatrie' ? 'selected' : '' }}>Rapatrié</option>
                            <option value="autre" {{ old('categorie') === 'autre' ? 'selected' : '' }}>Autre</option>
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
                            <option value="Nord-Kivu" {{ old('province') === 'Nord-Kivu' ? 'selected' : '' }}>Nord-Kivu</option>
                            <option value="Sud-Kivu" {{ old('province') === 'Sud-Kivu' ? 'selected' : '' }}>Sud-Kivu</option>
                            <option value="Ituri" {{ old('province') === 'Ituri' ? 'selected' : '' }}>Ituri</option>
                            <option value="Kinshasa" {{ old('province') === 'Kinshasa' ? 'selected' : '' }}>Kinshasa</option>
                            <option value="Haut-Katanga" {{ old('province') === 'Haut-Katanga' ? 'selected' : '' }}>Haut-Katanga</option>
                            <option value="Tanganyika" {{ old('province') === 'Tanganyika' ? 'selected' : '' }}>Tanganyika</option>
                            <option value="Maniema" {{ old('province') === 'Maniema' ? 'selected' : '' }}>Maniema</option>
                            <option value="autre" {{ old('province') === 'autre' ? 'selected' : '' }}>Autre</option>
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
                               value="{{ old('localisation') }}" 
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
                        Photo <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel — photo portrait recommandée)</small>
                    </label>
                    <div class="temoignage-photo-upload">
                        <label for="photo" class="temoignage-photo-preview" id="photoPreview">
                            <img id="photoPreviewImg" src="" alt="Aperçu" style="display: none;">
                            <div class="temoignage-photo-placeholder" id="photoPlaceholder">
                                <i class="fas fa-user"></i>
                            </div>
                        </label>
                        <div class="temoignage-photo-info">
                            <p>Cliquez pour choisir une photo</p>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> JPG, PNG ou WebP</li>
                                <li><i class="fas fa-check-circle"></i> Taille max : 5 Mo</li>
                                <li><i class="fas fa-check-circle"></i> Format portrait recommandé</li>
                            </ul>
                            <input type="file" name="photo" id="photo" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp" 
                                   style="display: none;">
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
                              placeholder="Rédigez le témoignage du bénéficiaire ici. Exprimez son vécu, l'impact de l'aide reçue, son ressenti..."
                              maxlength="5000" required>{{ old('contenu') }}</textarea>
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
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
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
                            <option value="en_attente" {{ old('statut', 'en_attente') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="approuve" {{ old('statut') === 'approuve' ? 'selected' : '' }}>Approuvé</option>
                            <option value="rejete" {{ old('statut') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                            <option value="archive" {{ old('statut') === 'archive' ? 'selected' : '' }}>Archivé</option>
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
                               value="{{ old('ordre', 0) }}" 
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
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="temoignage-form-actions">
                <a href="{{ route('admin.temoignages.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer le témoignage
                </button>
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
        });
    </script>
    <script src="{{ asset('assets/js/admin-temoignages.js') }}"></script>
@endsection