@extends('layouts.admin')

@section('title', 'Modifier l\'article | PHOVE')

@section('page_title', 'Modifier l\'article')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations de l\'article : ' . $stock->designation)

@php
    $breadcrumb = [
        ['label' => 'Stocks', 'url' => route('admin.stocks.index')],
        ['label' => $stock->designation, 'url' => route('admin.stocks.show', $stock)],
        'Modifier'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-edit"></i> Modifier l'article
            </h2>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.stocks.update', $stock) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION 1 : Informations générales --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations de l'article
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="reference" class="projet-form-label">Référence <span class="required">*</span></label>
                        <input type="text" name="reference" id="reference" class="projet-form-input @error('reference') error @enderror" 
                               value="{{ old('reference', $stock->reference) }}" required maxlength="50">
                        @error('reference') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="designation" class="projet-form-label">Désignation <span class="required">*</span></label>
                        <input type="text" name="designation" id="designation" class="projet-form-input @error('designation') error @enderror" 
                               value="{{ old('designation', $stock->designation) }}" required>
                        @error('designation') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="categorie" class="projet-form-label">Catégorie <span class="required">*</span></label>
                        <select name="categorie" id="categorie" class="projet-form-select @error('categorie') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($categories as $value => $label)
                                <option value="{{ $value }}" {{ old('categorie', $stock->categorie) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('categorie') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="unite" class="projet-form-label">Unité <span class="required">*</span></label>
                        <select name="unite" id="unite" class="projet-form-select @error('unite') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($unites as $value => $label)
                                <option value="{{ $value }}" {{ old('unite', $stock->unite) === $value ? 'selected' : '' }}>{{ $label }}</option>
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
                               value="{{ old('quantite_initiale', $stock->quantite_initiale) }}" step="0.01" min="0" required>
                        @error('quantite_initiale') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="seuil_alerte" class="projet-form-label">Seuil d'alerte <span class="optional">(optionnel)</span></label>
                        <input type="number" name="seuil_alerte" id="seuil_alerte" class="projet-form-input" 
                               value="{{ old('seuil_alerte', $stock->seuil_alerte) }}" step="0.01" min="0">
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
                               value="{{ old('emplacement', $stock->emplacement) }}" placeholder="Ex: Magasin principal">
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="description" class="projet-form-label">Description</label>
                        <textarea name="description" id="description" class="projet-form-textarea" rows="3" 
                                  placeholder="Description de l'article...">{{ old('description', $stock->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.stocks.show', $stock) }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>

        {{-- Suppression --}}
        <form action="{{ route('admin.stocks.destroy', $stock) }}" method="POST" style="margin-top: 16px; text-align: right;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer cet article et tous ses mouvements ?');">
                <i class="fas fa-trash-alt"></i> Supprimer l'article
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection