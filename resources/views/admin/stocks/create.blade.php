@extends('layouts.admin')

@section('title', 'Ajouter un article | PHOVE')

@section('page_title', 'Ajouter un article')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Créez un nouvel article en stock.')

@php
    $breadcrumb = [
        ['label' => 'Stocks', 'url' => route('admin.stocks.index')],
        'Ajouter un article'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-box"></i> Nouvel article
            </h2>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.stocks.store') }}" method="POST" novalidate>
            @csrf

            {{-- SECTION 1 : Informations générales --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations de l'article
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="reference" class="projet-form-label">Référence <span class="required">*</span></label>
                        <input type="text" name="reference" id="reference" class="projet-form-input @error('reference') error @enderror" 
                               value="{{ old('reference', $referenceAuto) }}" placeholder="Ex: STK-202609-0001" required maxlength="50">
                        @error('reference') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="projet-form-hint">Référence unique — générée automatiquement, modifiable.</span>
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="designation" class="projet-form-label">Désignation <span class="required">*</span></label>
                        <input type="text" name="designation" id="designation" class="projet-form-input @error('designation') error @enderror" 
                               value="{{ old('designation') }}" placeholder="Ex: Riz, Maïs, Médicaments antipaludéens..." required>
                        @error('designation') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="categorie" class="projet-form-label">Catégorie <span class="required">*</span></label>
                        <select name="categorie" id="categorie" class="projet-form-select @error('categorie') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ old('categorie') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('categorie') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="unite" class="projet-form-label">Unité <span class="required">*</span></label>
                        <select name="unite" id="unite" class="projet-form-select @error('unite') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($unites as $value => $label)
                                <option value="{{ $value }}" {{ old('unite') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('unite') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Quantités --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-balance-scale"></i> Quantités
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="quantite_initiale" class="projet-form-label">Quantité initiale <span class="required">*</span></label>
                        <input type="number" name="quantite_initiale" id="quantite_initiale" class="projet-form-input @error('quantite_initiale') error @enderror" 
                               value="{{ old('quantite_initiale', 0) }}" step="0.01" min="0" required>
                        @error('quantite_initiale') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="seuil_alerte" class="projet-form-label">Seuil d'alerte <span class="optional">(optionnel)</span></label>
                        <input type="number" name="seuil_alerte" id="seuil_alerte" class="projet-form-input" 
                               value="{{ old('seuil_alerte') }}" step="0.01" min="0" placeholder="Ex: 10">
                        <span class="projet-form-hint">Alerte automatique quand le stock descend sous ce seuil.</span>
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Localisation et description --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Localisation et description
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="emplacement" class="projet-form-label">Emplacement</label>
                        <input type="text" name="emplacement" id="emplacement" class="projet-form-input" 
                               value="{{ old('emplacement') }}" placeholder="Ex: Magasin principal, Dépôt Nyiragongo">
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="description" class="projet-form-label">Description</label>
                        <textarea name="description" id="description" class="projet-form-textarea" rows="3" 
                                  placeholder="Description de l'article...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Créer l'article
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection