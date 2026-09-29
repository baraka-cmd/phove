@extends('layouts.admin')

@section('title', 'Créer un contrôle | PHOVE')

@section('page_title', 'Nouveau contrôle trimestriel')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Créez un rapport de contrôle pour la Commission de Contrôle — Article 35.')

@php
    $breadcrumb = [
        ['label' => 'Contrôles', 'url' => route('admin.controles.index')],
        'Créer un contrôle'
    ];
@endphp

@section('styles')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-controlle-forms.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-search-dollar"></i> Nouveau contrôle trimestriel
            </h2>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <form action="{{ route('admin.controles.store') }}" method="POST" novalidate>
            @csrf

            {{-- SECTION 1 : Période --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-calendar-alt"></i> Période du contrôle
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="annee" class="projet-form-label">Année <span class="required">*</span></label>
                        <input type="number" name="annee" id="annee" class="projet-form-input @error('annee') error @enderror" 
                               value="{{ old('annee', $anneeCourante) }}" min="2000" max="2100" required>
                        @error('annee') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="trimestre" class="projet-form-label">Trimestre <span class="required">*</span></label>
                        <select name="trimestre" id="trimestre" class="projet-form-select @error('trimestre') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($trimestres as $value => $label)
                                <option value="{{ $value }}" {{ old('trimestre', $trimestreCourant) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('trimestre') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="reference" class="projet-form-label">Référence <span class="required">*</span></label>
                        <input type="text" name="reference" id="reference" class="projet-form-input @error('reference') error @enderror" 
                               value="{{ old('reference', $referenceAuto) }}" required maxlength="30" readonly>
                        @error('reference') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="projet-form-hint">Référence générée automatiquement.</span>
                    </div>

                    <div class="projet-form-group">
                        <label for="date_debut" class="projet-form-label">Date de début <span class="required">*</span></label>
                        <input type="date" name="date_debut" id="date_debut" class="projet-form-input @error('date_debut') error @enderror" 
                               value="{{ old('date_debut', $dates['debut']) }}" required>
                        @error('date_debut') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="date_fin" class="projet-form-label">Date de fin <span class="required">*</span></label>
                        <input type="date" name="date_fin" id="date_fin" class="projet-form-input @error('date_fin') error @enderror" 
                               value="{{ old('date_fin', $dates['fin']) }}" required>
                        @error('date_fin') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="date_controle" class="projet-form-label">Date du contrôle <span class="required">*</span></label>
                        <input type="date" name="date_controle" id="date_controle" class="projet-form-input @error('date_controle') error @enderror" 
                               value="{{ old('date_controle', date('Y-m-d')) }}" required>
                        @error('date_controle') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Type et statut --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-tag"></i> Type et statut
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="type_controle" class="projet-form-label">Type de contrôle <span class="required">*</span></label>
                        <select name="type_controle" id="type_controle" class="projet-form-select @error('type_controle') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('type_controle') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type_controle') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="projet-form-select @error('statut') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($statuts as $value => $label)
                                <option value="{{ $value }}" {{ old('statut', 'planifie') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('statut') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="controleur" class="projet-form-label">Contrôleur (membre CC)</label>
                        <input type="text" name="controleur" id="controleur" class="projet-form-input" 
                               value="{{ old('controleur') }}" placeholder="Ex: KAHAMBU MUTANGI Julienne" maxlength="255">
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Montants --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-dollar-sign"></i> Montants constatés
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="total_entrees" class="projet-form-label">Total entrées ($)</label>
                        <input type="number" name="total_entrees" id="total_entrees" class="projet-form-input" 
                               value="{{ old('total_entrees', 0) }}" step="0.01" min="0">
                    </div>

                    <div class="projet-form-group">
                        <label for="total_sorties" class="projet-form-label">Total sorties ($)</label>
                        <input type="number" name="total_sorties" id="total_sorties" class="projet-form-input" 
                               value="{{ old('total_sorties', 0) }}" step="0.01" min="0">
                    </div>

                    <div class="projet-form-group">
                        <label class="projet-form-label">Écart estimé</label>
                        <div style="padding: 10px 14px; background: #F1F5F9; border-radius: 8px; font-size: 16px; font-weight: 800; color: #1A5B9C;" id="ecartPreview">
                            0,00 $
                        </div>
                    </div>

                    <div class="projet-form-group">
                        <label for="conformite" class="projet-form-label">Conformité</label>
                        <label class="projet-form-switch" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="conformite" value="1" {{ old('conformite', true) ? 'checked' : '' }}>
                            <span style="font-size: 13px;">Le contrôle est conforme</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- SECTION 4 : Documents et observations --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-file-alt"></i> Documents et observations
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label for="documents_verifies" class="projet-form-label">Documents vérifiés</label>
                        <textarea name="documents_verifies" id="documents_verifies" class="projet-form-textarea" rows="3" 
                                  placeholder="Liste des documents vérifiés (séparés par des virgules)...">{{ old('documents_verifies') }}</textarea>
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="observations" class="projet-form-label">Observations</label>
                        <textarea name="observations" id="observations" class="projet-form-textarea" rows="3" 
                                  placeholder="Observations du contrôleur...">{{ old('observations') }}</textarea>
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="recommandations" class="projet-form-label">Recommandations</label>
                        <textarea name="recommandations" id="recommandations" class="projet-form-textarea" rows="3" 
                                  placeholder="Recommandations...">{{ old('recommandations') }}</textarea>
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="notes" class="projet-form-label">Notes internes</label>
                        <textarea name="notes" id="notes" class="projet-form-textarea" rows="2" 
                                  placeholder="Notes internes...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.controles.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Créer le contrôle
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script>
        // Calcul automatique de l'écart
        const entrees = document.getElementById('total_entrees');
        const sorties = document.getElementById('total_sorties');
        const ecartPreview = document.getElementById('ecartPreview');

        function calculerEcart() {
            const e = parseFloat(entrees.value) || 0;
            const s = parseFloat(sorties.value) || 0;
            const ecart = e - s;
            ecartPreview.textContent = ecart.toFixed(2).replace('.', ',') + ' $';
        }

        entrees.addEventListener('input', calculerEcart);
        sorties.addEventListener('input', calculerEcart);
        calculerEcart();
    </script>
@endsection