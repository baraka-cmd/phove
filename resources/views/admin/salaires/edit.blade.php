@extends('layouts.admin')

@section('title', 'Modifier le salaire | PHOVE')

@section('page_title', 'Modifier le salaire')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez le bulletin : ' . $salaire->reference)

@php
    $breadcrumb = [
        ['label' => 'Salaires', 'url' => route('admin.salaires.index')],
        ['label' => $salaire->reference, 'url' => route('admin.salaires.show', $salaire)],
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
                <i class="fas fa-edit"></i> Modifier le salaire
            </h2>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        @if($salaire->estPaye())
        <div style="background: #FEE2E2; border-left: 4px solid #EF4444; border-radius: 10px; padding: 16px 20px; margin: 0 24px 24px;">
            <p style="font-size: 14px; font-weight: 700; color: #B91C1C; margin-bottom: 4px;">
                <i class="fas fa-lock"></i> Salaire payé — Modification impossible
            </p>
            <p style="font-size: 13px; color: #B91C1C;">
                Ce salaire a déjà été payé le {{ $salaire->date_paiement ? $salaire->date_paiement->format('d/m/Y') : 'N/A' }}. 
                Il ne peut plus être modifié.
            </p>
        </div>
        @else
        <form action="{{ route('admin.salaires.update', $salaire) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION 1 : Personnel (lecture seule) --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-user"></i> Personnel concerné
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label class="projet-form-label">Membre</label>
                        <input type="text" class="projet-form-input" value="{{ $salaire->nomMembre() }}" disabled readonly>
                    </div>

                    <div class="projet-form-group">
                        <label class="projet-form-label">Mois</label>
                        <input type="text" class="projet-form-input" value="{{ $salaire->mois }}" disabled readonly>
                    </div>

                    <div class="projet-form-group">
                        <label class="projet-form-label">Référence</label>
                        <input type="text" class="projet-form-input" value="{{ $salaire->reference }}" disabled readonly>
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Poste --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-sitemap"></i> Poste occupé
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label for="organe_id" class="projet-form-label">Poste</label>
                        <select name="organe_id" id="organe_id" class="projet-form-select">
                            <option value="">Aucun poste spécifique</option>
                            @foreach($organes as $organe)
                                <option value="{{ $organe->id }}" {{ old('organe_id', $salaire->organe_id) == $organe->id ? 'selected' : '' }}>
                                    {{ $organe->organeLibelle() }} — {{ $organe->poste }}
                                    @if($organe->membre)
                                        ({{ $organe->membre->nomComplet() }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
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
                               value="{{ old('salaire_base', $salaire->salaire_base) }}" step="0.01" min="0" required>
                        @error('salaire_base') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="primes" class="projet-form-label">Primes ($)</label>
                        <input type="number" name="primes" id="primes" class="projet-form-input" 
                               value="{{ old('primes', $salaire->primes) }}" step="0.01" min="0">
                    </div>

                    <div class="projet-form-group">
                        <label for="retenues" class="projet-form-label">Retenues ($)</label>
                        <input type="number" name="retenues" id="retenues" class="projet-form-input" 
                               value="{{ old('retenues', $salaire->retenues) }}" step="0.01" min="0">
                    </div>

                    <div class="projet-form-group">
                        <label class="projet-form-label">Salaire net estimé</label>
                        <div style="padding: 10px 14px; background: #F1F5F9; border-radius: 8px; font-size: 16px; font-weight: 800; color: #1A5B9C;" id="salaireNetPreview">
                            {{ number_format($salaire->salaire_net, 2, ',', ' ') }} $
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
                            <option value="especes" {{ old('mode_paiement', $salaire->mode_paiement) === 'especes' ? 'selected' : '' }}>Espèces</option>
                            <option value="virement" {{ old('mode_paiement', $salaire->mode_paiement) === 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="mobile_money" {{ old('mode_paiement', $salaire->mode_paiement) === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="cheque" {{ old('mode_paiement', $salaire->mode_paiement) === 'cheque' ? 'selected' : '' }}>Chèque</option>
                        </select>
                    </div>

                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="projet-form-select @error('statut') error @enderror" required>
                            <option value="brouillon" {{ old('statut', $salaire->statut) === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="valide" {{ old('statut', $salaire->statut) === 'valide' ? 'selected' : '' }}>Validé (à payer)</option>
                            <option value="paye" {{ old('statut', $salaire->statut) === 'paye' ? 'selected' : '' }}>Payé</option>
                        </select>
                    </div>

                    <div class="projet-form-group" id="datePaiementGroup" style="display: {{ $salaire->statut === 'paye' ? 'block' : 'none' }};">
                        <label for="date_paiement" class="projet-form-label">Date de paiement</label>
                        <input type="date" name="date_paiement" id="date_paiement" class="projet-form-input" 
                               value="{{ old('date_paiement', $salaire->date_paiement ? $salaire->date_paiement->format('Y-m-d') : date('Y-m-d')) }}">
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="notes" class="projet-form-label">Notes</label>
                        <textarea name="notes" id="notes" class="projet-form-textarea" rows="3" 
                                  placeholder="Notes sur ce salaire...">{{ old('notes', $salaire->notes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.salaires.show', $salaire) }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
        @endif

        {{-- Suppression --}}
        @if(!$salaire->estPaye())
        <form action="{{ route('admin.salaires.destroy', $salaire) }}" method="POST" style="margin-top: 16px; text-align: right;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce salaire ?');">
                <i class="fas fa-trash-alt"></i> Supprimer
            </button>
        </form>
        @endif
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