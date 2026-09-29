@extends('layouts.admin')

@section('title', 'Modifier le contrôle | PHOVE')

@section('page_title', 'Modifier le contrôle')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez le rapport : ' . $controle->reference)

@php
    $breadcrumb = [
        ['label' => 'Contrôles', 'url' => route('admin.controles.index')],
        ['label' => $controle->reference, 'url' => route('admin.controles.show', $controle)],
        'Modifier'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-controlle-forms.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-edit"></i> Modifier le contrôle
            </h2>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        @if($controle->estValide())
        <div style="background: #D1FAE5; border-left: 4px solid #10B981; border-radius: 10px; padding: 16px 20px; margin: 0 24px 24px;">
            <p style="font-size: 14px; font-weight: 700; color: #065F46; margin-bottom: 4px;">
                <i class="fas fa-lock"></i> Contrôle validé — Modification impossible
            </p>
            <p style="font-size: 13px; color: #065F46;">
                Ce contrôle a déjà été validé et ne peut plus être modifié.
            </p>
        </div>
        @else
        <form action="{{ route('admin.controles.update', $controle) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION 1 : Période (lecture seule) --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-calendar-alt"></i> Période du contrôle
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label class="projet-form-label">Référence</label>
                        <input type="text" class="projet-form-input" value="{{ $controle->reference }}" disabled readonly>
                    </div>

                    <div class="projet-form-group">
                        <label class="projet-form-label">Trimestre</label>
                        <input type="text" class="projet-form-input" value="{{ $controle->trimestreLibelle() }} {{ $controle->annee }}" disabled readonly>
                    </div>

                    <div class="projet-form-group">
                        <label class="projet-form-label">Période contrôlée</label>
                        <input type="text" class="projet-form-input" value="{{ $controle->periodeFormatee() }}" disabled readonly>
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Type, statut et contrôleur --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-tag"></i> Type, statut et contrôleur
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="type_controle" class="projet-form-label">Type de contrôle <span class="required">*</span></label>
                        <select name="type_controle" id="type_controle" class="projet-form-select @error('type_controle') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ old('type_controle', $controle->type_controle) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type_controle') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="projet-form-select @error('statut') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            @foreach($statuts as $value => $label)
                                <option value="{{ $value }}" {{ old('statut', $controle->statut) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('statut') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="controleur" class="projet-form-label">Contrôleur (membre CC)</label>
                        <input type="text" name="controleur" id="controleur" class="projet-form-input" 
                               value="{{ old('controleur', $controle->controleur) }}" maxlength="255">
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
                               value="{{ old('total_entrees', $controle->total_entrees) }}" step="0.01" min="0">
                    </div>

                    <div class="projet-form-group">
                        <label for="total_sorties" class="projet-form-label">Total sorties ($)</label>
                        <input type="number" name="total_sorties" id="total_sorties" class="projet-form-input" 
                               value="{{ old('total_sorties', $controle->total_sorties) }}" step="0.01" min="0">
                    </div>

                    <div class="projet-form-group">
                        <label class="projet-form-label">Écart estimé</label>
                        <div style="padding: 10px 14px; background: #F1F5F9; border-radius: 8px; font-size: 16px; font-weight: 800; color: #1A5B9C;" id="ecartPreview">
                            {{ number_format($controle->ecart, 2, ',', ' ') }} $
                        </div>
                    </div>

                    <div class="projet-form-group">
                        <label for="conformite" class="projet-form-label">Conformité</label>
                        <label class="projet-form-switch" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="conformite" value="1" {{ old('conformite', $controle->conformite) ? 'checked' : '' }}>
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
                                  placeholder="Liste des documents vérifiés...">{{ old('documents_verifies', $controle->documents_verifies) }}</textarea>
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="observations" class="projet-form-label">Observations</label>
                        <textarea name="observations" id="observations" class="projet-form-textarea" rows="3" 
                                  placeholder="Observations du contrôleur...">{{ old('observations', $controle->observations) }}</textarea>
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="recommandations" class="projet-form-label">Recommandations</label>
                        <textarea name="recommandations" id="recommandations" class="projet-form-textarea" rows="3" 
                                  placeholder="Recommandations...">{{ old('recommandations', $controle->recommandations) }}</textarea>
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="notes" class="projet-form-label">Notes internes</label>
                        <textarea name="notes" id="notes" class="projet-form-textarea" rows="2" 
                                  placeholder="Notes internes...">{{ old('notes', $controle->notes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.controles.show', $controle) }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
        @endif

        {{-- Suppression --}}
        @if(!$controle->estValide())
        <form action="{{ route('admin.controles.destroy', $controle) }}" method="POST" style="margin-top: 16px; text-align: right;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce contrôle ?');">
                <i class="fas fa-trash-alt"></i> Supprimer
            </button>
        </form>
        @endif
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