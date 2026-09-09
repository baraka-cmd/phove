@extends('layouts.admin')

@section('title', 'Sortie de Stock | PHOVE')

@section('page_title', 'Sortie de Stock')
@section('page_icon', 'arrow-up')
@section('page_subtitle', 'Enregistrer une sortie pour : ' . $stock->designation)

@php
    $breadcrumb = [
        ['label' => 'Stocks', 'url' => route('admin.stocks.index')],
        ['label' => $stock->designation, 'url' => route('admin.stocks.show', $stock)],
        'Sortie'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-arrow-up" style="color: #F59E0B;"></i> Sortie de stock
            </h2>
        </div>

        {{-- Info article --}}
        <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 10px; padding: 14px 18px; margin: 0 24px 24px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #FFFFFF; display: flex; align-items: center; justify-content: center; color: #F59E0B;">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <div style="font-size: 14px; font-weight: 700; color: #92400E;">{{ $stock->designation }}</div>
                    <div style="font-size: 12px; color: #92400E;">
                        Stock disponible : <strong>{{ number_format($stock->quantite_actuelle, 2, ',', ' ') }} {{ $stock->unite }}</strong>
                        @if($stock->seuil_alerte !== null)
                            — Seuil : {{ number_format($stock->seuil_alerte, 2, ',', ' ') }} {{ $stock->unite }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.stocks.sortie', $stock) }}" method="POST" novalidate>
            @csrf

            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations de la sortie
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="quantite" class="projet-form-label">Quantité <span class="required">*</span></label>
                        <input type="number" name="quantite" id="quantite" class="projet-form-input @error('quantite') error @enderror" 
                               value="{{ old('quantite') }}" step="0.01" min="0.01" max="{{ $stock->quantite_actuelle }}" required placeholder="Ex: 10">
                        @error('quantite') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="projet-form-hint">Max disponible : {{ number_format($stock->quantite_actuelle, 2, ',', ' ') }} {{ $stock->unite }}</span>
                    </div>

                    <div class="projet-form-group">
                        <label for="date_mouvement" class="projet-form-label">Date <span class="required">*</span></label>
                        <input type="date" name="date_mouvement" id="date_mouvement" class="projet-form-input @error('date_mouvement') error @enderror" 
                               value="{{ old('date_mouvement', date('Y-m-d')) }}" required>
                        @error('date_mouvement') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="reference_document" class="projet-form-label">Référence bon de sortie</label>
                        <input type="text" name="reference_document" id="reference_document" class="projet-form-input" 
                               value="{{ old('reference_document') }}" placeholder="Ex: BON-SOR-001" maxlength="50">
                    </div>

                    <div class="projet-form-group">
                        <label for="motif" class="projet-form-label">Motif</label>
                        <select name="motif" id="motif" class="projet-form-select">
                            <option value="">Sélectionnez...</option>
                            <option value="Distribution" {{ old('motif') === 'Distribution' ? 'selected' : '' }}>Distribution</option>
                            <option value="Don" {{ old('motif') === 'Don' ? 'selected' : '' }}>Don</option>
                            <option value="Utilisation interne" {{ old('motif') === 'Utilisation interne' ? 'selected' : '' }}>Utilisation interne</option>
                            <option value="Transfert" {{ old('motif') === 'Transfert' ? 'selected' : '' }}>Transfert</option>
                            <option value="Péremption" {{ old('motif') === 'Péremption' ? 'selected' : '' }}>Péremption</option>
                            <option value="Perte" {{ old('motif') === 'Perte' ? 'selected' : '' }}>Perte</option>
                            <option value="Autre" {{ old('motif') === 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>

                    <div class="projet-form-group">
                        <label for="destinataire" class="projet-form-label">Bénéficiaire / Destination</label>
                        <input type="text" name="destinataire" id="destinataire" class="projet-form-input" 
                               value="{{ old('destinataire') }}" placeholder="Ex: Orphelins de Kiziba, Ménages de Muja">
                    </div>

                    <div class="projet-form-group">
                        <label for="projet_id" class="projet-form-label">Projet lié</label>
                        <select name="projet_id" id="projet_id" class="projet-form-select">
                            <option value="">Aucun projet</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                    {{ Str::limit($projet->titre, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="notes" class="projet-form-label">Notes</label>
                        <textarea name="notes" id="notes" class="projet-form-textarea" rows="3" 
                                  placeholder="Notes sur cette sortie...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.stocks.show', $stock) }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary" style="background: #F59E0B; border-color: #F59E0B;">
                    <i class="fas fa-arrow-up"></i> Enregistrer la sortie
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection