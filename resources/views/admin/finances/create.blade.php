@extends('layouts.admin')

@section('title', 'Nouveau rapport financier')

@section('page_title', 'Nouveau rapport financier')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Créez un rapport financier détaillé : entrées, sorties, solde.')

@php
    $breadcrumb = [
        ['label' => 'Finances', 'url' => route('admin.finances.index')],
        'Nouveau rapport'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-finances.css') }}">
@endsection

@section('content')

    <div class="finance-form-card">

        {{-- En-tête --}}
        <div class="finance-form-header">
            <div class="finance-form-header-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <h2>Nouveau rapport financier</h2>
                <p>Remplissez les informations ci-dessous pour générer un rapport financier.</p>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.finances.store') }}" method="POST" novalidate>
            @csrf

            {{-- SECTION 1 : Informations générales --}}
            <div class="finance-form-section">
                <h4 class="finance-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations générales
                </h4>
                <div class="finance-form-grid">
                    <div class="finance-form-group finance-form-full">
                        <label for="titre" class="finance-form-label">Titre du rapport <span class="required">*</span></label>
                        <input type="text" name="titre" id="titre" class="finance-form-input @error('titre') error @enderror" 
                               value="{{ old('titre') }}" placeholder="Ex: Rapport financier mensuel — Juillet 2026" required>
                        @error('titre') <span class="finance-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                    <div class="finance-form-group">
                        <label for="type" class="finance-form-label">Type de rapport <span class="required">*</span></label>
                        <select name="type" id="type" class="finance-form-select @error('type') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            <option value="mensuel" {{ old('type') === 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                            <option value="trimestriel" {{ old('type') === 'trimestriel' ? 'selected' : '' }}>Trimestriel</option>
                            <option value="annuel" {{ old('type') === 'annuel' ? 'selected' : '' }}>Annuel</option>
                            <option value="projet" {{ old('type') === 'projet' ? 'selected' : '' }}>Rapport de projet</option>
                            <option value="audit" {{ old('type') === 'audit' ? 'selected' : '' }}>Rapport d'audit</option>
                        </select>
                        @error('type') <span class="finance-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                    <div class="finance-form-group">
                        <label for="periode" class="finance-form-label">Période <span class="required">*</span></label>
                        <input type="month" name="periode" id="periode" class="finance-form-input @error('periode') error @enderror" 
                               value="{{ old('periode', date('Y-m')) }}" required>
                        @error('periode') <span class="finance-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Entrées --}}
            <div class="finance-form-section">
                <h4 class="finance-form-section-title">
                    <i class="fas fa-sign-in-alt"></i> Entrées (ressources)
                </h4>
                <div class="finance-form-grid">
                    <div class="finance-form-group">
                        <label for="dons_recus" class="finance-form-label">Dons reçus ($)</label>
                        <input type="number" name="dons_recus" id="dons_recus" class="finance-form-input" 
                               value="{{ old('dons_recus', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="finance-form-group">
                        <label for="cotisations_recues" class="finance-form-label">Cotisations reçues ($)</label>
                        <input type="number" name="cotisations_recues" id="cotisations_recues" class="finance-form-input" 
                               value="{{ old('cotisations_recues', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="finance-form-group">
                        <label for="subventions_recues" class="finance-form-label">Subventions reçues ($)</label>
                        <input type="number" name="subventions_recues" id="subventions_recues" class="finance-form-input" 
                               value="{{ old('subventions_recues', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="finance-form-group">
                        <label for="autres_entrees" class="finance-form-label">Autres entrées ($)</label>
                        <input type="number" name="autres_entrees" id="autres_entrees" class="finance-form-input" 
                               value="{{ old('autres_entrees', 0) }}" step="0.01" min="0">
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Sorties --}}
            <div class="finance-form-section">
                <h4 class="finance-form-section-title">
                    <i class="fas fa-sign-out-alt"></i> Sorties (dépenses)
                </h4>
                <div class="finance-form-grid">
                    <div class="finance-form-group">
                        <label for="depenses_projets" class="finance-form-label">Dépenses projets ($)</label>
                        <input type="number" name="depenses_projets" id="depenses_projets" class="finance-form-input" 
                               value="{{ old('depenses_projets', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="finance-form-group">
                        <label for="salaires" class="finance-form-label">Salaires ($)</label>
                        <input type="number" name="salaires" id="salaires" class="finance-form-input" 
                               value="{{ old('salaires', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="finance-form-group">
                        <label for="logistique" class="finance-form-label">Logistique ($)</label>
                        <input type="number" name="logistique" id="logistique" class="finance-form-input" 
                               value="{{ old('logistique', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="finance-form-group">
                        <label for="administratif" class="finance-form-label">Frais administratifs ($)</label>
                        <input type="number" name="administratif" id="administratif" class="finance-form-input" 
                               value="{{ old('administratif', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="finance-form-group finance-form-full">
                        <label for="autres_sorties" class="finance-form-label">Autres sorties ($)</label>
                        <input type="number" name="autres_sorties" id="autres_sorties" class="finance-form-input" 
                               value="{{ old('autres_sorties', 0) }}" step="0.01" min="0">
                    </div>
                </div>
            </div>

            {{-- SECTION 4 : Commentaire --}}
            <div class="finance-form-section">
                <h4 class="finance-form-section-title">
                    <i class="fas fa-sticky-note"></i> Commentaire
                </h4>
                <div class="finance-form-group finance-form-full">
                    <label for="commentaire" class="finance-form-label">Commentaire (optionnel)</label>
                    <textarea name="commentaire" id="commentaire" class="finance-form-textarea" rows="4" 
                              placeholder="Note explicative sur ce rapport...">{{ old('commentaire') }}</textarea>
                </div>
            </div>

            {{-- SECTION 5 : Publication --}}
            <div class="finance-form-section">
                <h4 class="finance-form-section-title">
                    <i class="fas fa-eye"></i> Publication
                </h4>
                <label style="display: flex; align-items: center; gap: 12px; padding: 14px 18px; background: var(--color-bg-light); border-radius: 10px; cursor: pointer;">
                    <input type="checkbox" name="public" value="1" {{ old('public') ? 'checked' : '' }} 
                           style="width: 40px; height: 22px; appearance: none; -webkit-appearance: none; background: #ccc; border-radius: 11px; position: relative; cursor: pointer; transition: all 0.3s ease;">
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: #1a1a1a;">Rendre ce rapport public</div>
                        <div style="font-size: 11px; color: #888;">Le rapport sera visible sur la page Transparence du site</div>
                    </div>
                </label>
            </div>

            {{-- Actions --}}
            <div class="finance-form-actions">
                <a href="{{ route('admin.finances.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Créer le rapport
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-finances.js') }}"></script>
    <script>
        // Switch visuel pour la checkbox
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.addEventListener('change', function () {
                this.style.background = this.checked ? 'var(--color-success)' : '#ccc';
            });
            // Initialiser
            cb.style.background = cb.checked ? 'var(--color-success)' : '#ccc';
        });
    </script>
@endsection