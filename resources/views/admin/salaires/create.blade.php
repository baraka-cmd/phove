@extends('layouts.admin')

@section('title', 'Créer un salaire | PHOVE')

@section('page_title', 'Créer un salaire')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Créez un nouveau bulletin de salaire pour le personnel.')

@php
    $breadcrumb = [
        ['label' => 'Salaires', 'url' => route('admin.salaires.index')],
        'Créer un salaire'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> Nouveau bulletin de salaire
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

        <form action="{{ route('admin.salaires.store') }}" method="POST" novalidate>
            @csrf

            {{-- SECTION 1 : Personnel --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-user"></i> Personnel concerné
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label for="membre_id" class="projet-form-label">Membre <span class="required">*</span></label>
                        <select name="membre_id" id="membre_id" class="projet-form-select @error('membre_id') error @enderror" required>
                            <option value="">Sélectionnez un membre...</option>
                            @foreach($membres as $membre)
                                <option value="{{ $membre->id }}" {{ old('membre_id') == $membre->id ? 'selected' : '' }}>
                                    {{ $membre->nomComplet() }} — {{ $membre->categorieLibelle() }}
                                </option>
                            @endforeach
                        </select>
                        @error('membre_id') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="organe_id" class="projet-form-label">Poste occupé</label>
                        <select name="organe_id" id="organe_id" class="projet-form-select">
                            <option value="">Aucun poste spécifique</option>
                            @foreach($organes as $organe)
                                <option value="{{ $organe->id }}" {{ old('organe_id') == $organe->id ? 'selected' : '' }}>
                                    {{ $organe->organeLibelle() }} — {{ $organe->poste }}
                                    @if($organe->membre)
                                        ({{ $organe->membre->nomComplet() }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <span class="projet-form-hint">Permet de lier le salaire au poste dans l'organigramme.</span>
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Période et référence --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-calendar-alt"></i> Période et référence
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="mois" class="projet-form-label">Mois <span class="required">*</span></label>
                        <input type="month" name="mois" id="mois" class="projet-form-input @error('mois') error @enderror" 
                               value="{{ old('mois', $moisCourant) }}" required>
                        @error('mois') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="reference" class="projet-form-label">Référence <span class="required">*</span></label>
                        <input type="text" name="reference" id="reference" class="projet-form-input @error('reference') error @enderror" 
                               value="{{ old('reference', $referenceAuto) }}" required maxlength="50" readonly>
                        @error('reference') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="projet-form-hint">Référence générée automatiquement.</span>
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Montants --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-dollar-sign"></i> Montants
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="salaire_base" class="projet-form-label">Salaire de base ($) <span class="required">*</span></label>
                        <input type="number" name="salaire_base" id="salaire_base" class="projet-form-input @error('salaire_base') error @enderror" 
                               value="{{ old('salaire_base') }}" step="0.01" min="0" required placeholder="Ex: 500">
                        @error('salaire_base') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="primes" class="projet-form-label">Primes ($)</label>
                        <input type="number" name="primes" id="primes" class="projet-form-input" 
                               value="{{ old('primes', 0) }}" step="0.01" min="0">
                    </div>

                    <div class="projet-form-group">
                        <label for="retenues" class="projet-form-label">Retenues ($)</label>
                        <input type="number" name="retenues" id="retenues" class="projet-form-input" 
                               value="{{ old('retenues', 0) }}" step="0.01" min="0">
                    </div>

                    <div class="projet-form-group">
                        <label class="projet-form-label">Salaire net estimé</label>
                        <div style="padding: 10px 14px; background: #F1F5F9; border-radius: 8px; font-size: 16px; font-weight: 800; color: #1A5B9C;" id="salaireNetPreview">
                            0,00 $
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 4 : Paiement --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-credit-card"></i> Paiement
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="mode_paiement" class="projet-form-label">Mode de paiement <span class="required">*</span></label>
                        <select name="mode_paiement" id="mode_paiement" class="projet-form-select @error('mode_paiement') error @enderror" required>
                            <option value="especes" {{ old('mode_paiement', 'especes') === 'especes' ? 'selected' : '' }}>Espèces</option>
                            <option value="virement" {{ old('mode_paiement') === 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="mobile_money" {{ old('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="cheque" {{ old('mode_paiement') === 'cheque' ? 'selected' : '' }}>Chèque</option>
                        </select>
                    </div>

                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="projet-form-select @error('statut') error @enderror" required>
                            <option value="brouillon" {{ old('statut', 'brouillon') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="valide" {{ old('statut') === 'valide' ? 'selected' : '' }}>Validé (à payer)</option>
                            <option value="paye" {{ old('statut') === 'paye' ? 'selected' : '' }}>Payé</option>
                        </select>
                    </div>

                    <div class="projet-form-group" id="datePaiementGroup" style="display: {{ old('statut') === 'paye' ? 'block' : 'none' }};">
                        <label for="date_paiement" class="projet-form-label">Date de paiement</label>
                        <input type="date" name="date_paiement" id="date_paiement" class="projet-form-input" 
                               value="{{ old('date_paiement', date('Y-m-d')) }}">
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="notes" class="projet-form-label">Notes</label>
                        <textarea name="notes" id="notes" class="projet-form-textarea" rows="3" 
                                  placeholder="Notes sur ce salaire...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.salaires.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Créer le salaire
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script>
        // Calcul automatique du salaire net
        const salaireBase = document.getElementById('salaire_base');
        const primes = document.getElementById('primes');
        const retenues = document.getElementById('retenues');
        const netPreview = document.getElementById('salaireNetPreview');
        const statutSelect = document.getElementById('statut');
        const datePaiementGroup = document.getElementById('datePaiementGroup');

        function calculerNet() {
            const base = parseFloat(salaireBase.value) || 0;
            const prime = parseFloat(primes.value) || 0;
            const retenue = parseFloat(retenues.value) || 0;
            const net = base + prime - retenue;
            netPreview.textContent = net.toFixed(2).replace('.', ',') + ' $';
        }

        salaireBase.addEventListener('input', calculerNet);
        primes.addEventListener('input', calculerNet);
        retenues.addEventListener('input', calculerNet);

        // Afficher/masquer la date de paiement
        statutSelect.addEventListener('change', function() {
            if (this.value === 'paye') {
                datePaiementGroup.style.display = 'block';
            } else {
                datePaiementGroup.style.display = 'none';
            }
        });

        calculerNet();
    </script>
@endsection