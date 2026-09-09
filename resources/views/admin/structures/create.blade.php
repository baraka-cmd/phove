@extends('layouts.admin')

@section('title', 'Ajouter une structure | PHOVE')

@section('page_title', 'Ajouter une structure territoriale')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Créez une nouvelle station, base ou antenne pour PHOVE.')

@php
    $breadcrumb = [
        ['label' => 'Structures', 'url' => route('admin.structures.index')],
        'Ajouter une structure'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-sitemap"></i> Nouvelle structure
            </h2>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.structures.store') }}" method="POST" novalidate>
            @csrf

            {{-- SECTION 1 : Informations générales --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations générales
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label for="nom" class="projet-form-label">Nom de la structure <span class="required">*</span></label>
                        <input type="text" name="nom" id="nom" class="projet-form-input @error('nom') error @enderror" 
                               value="{{ old('nom') }}" placeholder="Ex: PHOVE Goma, PHOVE Rwanda, PHOVE Nyiragongo" required>
                        @error('nom') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="type" class="projet-form-label">Type de structure <span class="required">*</span></label>
                        <select name="type" id="type" class="projet-form-select @error('type') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="projet-form-select @error('statut') error @enderror" required>
                            <option value="actif" {{ old('statut', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Localisation --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Localisation
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="pays" class="projet-form-label">Pays</label>
                        <input type="text" name="pays" id="pays" class="projet-form-input" 
                               value="{{ old('pays') }}" placeholder="Ex: RD Congo">
                    </div>

                    <div class="projet-form-group">
                        <label for="province" class="projet-form-label">Province</label>
                        <input type="text" name="province" id="province" class="projet-form-input" 
                               value="{{ old('province') }}" placeholder="Ex: Nord-Kivu">
                    </div>

                    <div class="projet-form-group">
                        <label for="territoire" class="projet-form-label">Territoire / Commune</label>
                        <input type="text" name="territoire" id="territoire" class="projet-form-input" 
                               value="{{ old('territoire') }}" placeholder="Ex: Nyiragongo, Karisimbi">
                    </div>

                    <div class="projet-form-group">
                        <label for="adresse" class="projet-form-label">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="projet-form-input" 
                               value="{{ old('adresse') }}" placeholder="Ex: Rue Georges BUSHU N°092, Katoyi">
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Hiérarchie --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-level-up-alt"></i> Hiérarchie
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label for="parent_id" class="projet-form-label">Structure parente</label>
                        <select name="parent_id" id="parent_id" class="projet-form-select">
                            <option value="">Aucune (structure racine)</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->typeLibelle() }} — {{ $parent->nom }}
                                </option>
                            @endforeach
                        </select>
                        <span class="projet-form-hint">Ex: Une antenne peut avoir pour parent une base, une base peut avoir pour parent une station.</span>
                    </div>
                </div>
            </div>

            {{-- SECTION 4 : Description --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-file-alt"></i> Description
                </h4>

                <div class="projet-form-group projet-form-full">
                    <label for="description" class="projet-form-label">Description</label>
                    <textarea name="description" id="description" class="projet-form-textarea" rows="4" 
                              placeholder="Décrivez la structure...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.structures.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Créer la structure
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection