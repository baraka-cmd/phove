@extends('layouts.admin')

@section('title', 'Modifier un membre')

@section('page_title', 'Modifier un membre')
@section('page_icon', 'user-edit')
@section('page_subtitle', 'Modifiez les informations du membre ' . $membre->nomComplet() . '.')

@php
    $breadcrumb = [
        ['label' => 'Membres', 'url' => route('admin.membres.index')],
        'Modifier ' . $membre->nomComplet()
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-membre-form.css') }}">

    
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- FORMULAIRE DE MODIFICATION --}}
    {{-- ============================================ --}}
    <div class="membre-form-card">
        <form action="{{ route('admin.membres.update', $membre) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- ============================================ --}}
            {{-- SECTION 1 : IDENTITÉ --}}
            {{-- ============================================ --}}
            <h4 style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter);">
                <i class="fas fa-user-circle" style="color: var(--color-primary);"></i> Identité du membre
            </h4>

            <div class="membre-form-grid">
                <div class="membre-form-group">
                    <label for="nom" class="membre-form-label">Nom <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" class="membre-form-input @error('nom') error @enderror" value="{{ old('nom', $membre->nom) }}" placeholder="Ex: AKSANTI" required>
                    @error('nom') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="membre-form-group">
                    <label for="post_nom" class="membre-form-label">Post-nom <span class="required">*</span></label>
                    <input type="text" id="post_nom" name="post_nom" class="membre-form-input @error('post_nom') error @enderror" value="{{ old('post_nom', $membre->post_nom) }}" placeholder="Ex: DUNIA" required>
                    @error('post_nom') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="membre-form-group">
                    <label for="prenom" class="membre-form-label">Prénom <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" class="membre-form-input @error('prenom') error @enderror" value="{{ old('prenom', $membre->prenom) }}" placeholder="Ex: Steeng" required>
                    @error('prenom') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="membre-form-group">
                    <label for="sexe" class="membre-form-label">Sexe <span class="required">*</span></label>
                    <select id="sexe" name="sexe" class="membre-form-select @error('sexe') error @enderror" required>
                        <option value="">Sélectionnez...</option>
                        <option value="M" {{ old('sexe', $membre->sexe) === 'M' ? 'selected' : '' }}>Masculin</option>
                        <option value="F" {{ old('sexe', $membre->sexe) === 'F' ? 'selected' : '' }}>Féminin</option>
                    </select>
                    @error('sexe') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : COORDONNÉES --}}
            {{-- ============================================ --}}
            <h4 style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 24px 0 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter);">
                <i class="fas fa-address-book" style="color: var(--color-primary);"></i> Coordonnées
            </h4>

            <div class="membre-form-grid">
                <div class="membre-form-group">
                    <label for="adresse" class="membre-form-label">Adresse <span class="required">*</span></label>
                    <input type="text" id="adresse" name="adresse" class="membre-form-input @error('adresse') error @enderror" value="{{ old('adresse', $membre->adresse) }}" placeholder="Ex: Minova, Kalehe, Sud-Kivu" required>
                    @error('adresse') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="membre-form-group">
                    <label for="telephone" class="membre-form-label">Téléphone <span class="required">*</span></label>
                    <input type="text" id="telephone" name="telephone" class="membre-form-input @error('telephone') error @enderror" value="{{ old('telephone', $membre->telephone) }}" placeholder="Ex: +243 994 656 250" required>
                    @error('telephone') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="membre-form-group">
                    <label for="email" class="membre-form-label">Email</label>
                    <input type="email" id="email" name="email" class="membre-form-input @error('email') error @enderror" value="{{ old('email', $membre->email) }}" placeholder="Ex: contact@email.com">
                    @error('email') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="membre-form-group">
                    <label for="photo" class="membre-form-label">Photo passeport</label>
                    <div class="membre-photo-upload">
                        <div class="membre-photo-preview" id="photoPreview" onclick="document.getElementById('photo').click();">
                            @if($membre->photo)
                                <img id="photoPreviewImg" src="{{ asset('storage/' . $membre->photo) }}" alt="Photo actuelle">
                            @else
                                <img id="photoPreviewImg" src="" alt="Aperçu" style="display: none;">
                                <i class="fas fa-camera" id="photoPlaceholder"></i>
                            @endif
                        </div>
                        <input type="file" id="photo" name="photo" class="membre-photo-input" accept="image/jpeg,image/jpg,image/png" style="display: none;">
                        <small style="color: var(--color-text-muted);">JPG, PNG — Max 2 Mo. Laissez vide pour conserver la photo actuelle.</small>
                    </div>
                    @error('photo') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3 : CATÉGORIE & STATUT --}}
            {{-- ============================================ --}}
            <h4 style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 24px 0 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter);">
                <i class="fas fa-tags" style="color: var(--color-primary);"></i> Classification
            </h4>

            <div class="membre-form-grid">
                <div class="membre-form-group">
                    <label for="categorie" class="membre-form-label">Catégorie <span class="required">*</span></label>
                    <select id="categorie" name="categorie" class="membre-form-select @error('categorie') error @enderror" required>
                        <option value="">Sélectionnez...</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('categorie', $membre->categorie) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('categorie') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="membre-form-group">
                    <label for="statut" class="membre-form-label">Statut <span class="required">*</span></label>
                    <select id="statut" name="statut" class="membre-form-select @error('statut') error @enderror" required>
                        <option value="">Sélectionnez...</option>
                        @foreach($statuts as $value => $label)
                            <option value="{{ $value }}" {{ old('statut', $membre->statut) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('statut') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4 : FINANCES --}}
            {{-- ============================================ --}}
            <h4 style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 24px 0 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter);">
                <i class="fas fa-coins" style="color: var(--color-primary);"></i> Informations financières
            </h4>

            <div class="membre-form-grid">
                <div class="membre-form-group">
                    <label for="date_adhesion" class="membre-form-label">Date d'adhésion <span class="required">*</span></label>
                    <input type="date" id="date_adhesion" name="date_adhesion" class="membre-form-input @error('date_adhesion') error @enderror" value="{{ old('date_adhesion', $membre->date_adhesion ? $membre->date_adhesion->format('Y-m-d') : date('Y-m-d')) }}" required>
                    @error('date_adhesion') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="membre-form-group">
                    <label for="frais_adhesion" class="membre-form-label">Frais d'adhésion ($)</label>
                    <input type="number" id="frais_adhesion" name="frais_adhesion" class="membre-form-input" value="{{ old('frais_adhesion', $membre->frais_adhesion) }}" step="0.01" min="0">
                </div>

                <div class="membre-form-group">
                    <label for="cotisation_mensuelle" class="membre-form-label">Cotisation mensuelle ($)</label>
                    <input type="number" id="cotisation_mensuelle" name="cotisation_mensuelle" class="membre-form-input" value="{{ old('cotisation_mensuelle', $membre->cotisation_mensuelle) }}" step="0.01" min="0">
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="membre-form-actions">
                <a href="{{ route('admin.membres.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <a href="{{ route('admin.membres.show', $membre) }}" class="btn btn-outline">
                    <i class="fas fa-eye"></i> Voir la fiche
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-membres.js') }}"></script>
@endsection