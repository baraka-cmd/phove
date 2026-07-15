@extends('layouts.admin')

@section('title', 'Enregistrer un paiement')

@section('page_title', 'Enregistrer un paiement')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Enregistrez une nouvelle cotisation mensuelle pour un membre.')

@php
    $breadcrumb = [
        ['label' => 'Membres', 'url' => route('admin.membres.index')],
        ['label' => 'Cotisations', 'url' => route('admin.cotisations.index')],
        'Enregistrer un paiement'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-cotisations.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- FORMULAIRE D'ENREGISTREMENT --}}
    {{-- ============================================ --}}
    <div class="cotisation-form-card">
        
        {{-- En-tête --}}
        <div class="cotisation-form-header">
            <div class="cotisation-form-header-icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div>
                <h2>Nouveau paiement de cotisation</h2>
                <p style="font-size: 13px; color: #888; margin: 4px 0 0 0;">Remplissez les informations ci-dessous pour enregistrer un paiement.</p>
            </div>
        </div>

        {{-- Message d'erreur --}}
        @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <form action="{{ route('admin.cotisations.store') }}" method="POST" novalidate>
            @csrf

            <div class="cotisation-form-grid">
                {{-- Membre --}}
                <div class="cotisation-form-group">
                    <label for="membre_id" class="cotisation-form-label">
                        <i class="fas fa-user"></i> Membre <span class="required">*</span>
                    </label>
                    <select name="membre_id" id="membre_id" class="cotisation-form-select @error('membre_id') error @enderror" required>
                        <option value="">Sélectionnez un membre...</option>
                        @foreach($membres as $membre)
                            <option value="{{ $membre->id }}" {{ old('membre_id') == $membre->id ? 'selected' : '' }}>
                                {{ $membre->nomComplet() }} — {{ $membre->categorie === 'adherent' ? 'Adhérent' : ($membre->categorie === 'fondateur' ? 'Fondateur' : 'Initiateur') }}
                            </option>
                        @endforeach
                    </select>
                    @error('membre_id') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                {{-- Mois --}}
                <div class="cotisation-form-group">
                    <label for="mois" class="cotisation-form-label">
                        <i class="fas fa-calendar-alt"></i> Mois de cotisation <span class="required">*</span>
                    </label>
                    <select name="mois" id="mois" class="cotisation-form-select @error('mois') error @enderror" required>
                        <option value="">Sélectionnez un mois...</option>
                        @foreach($moisNoms as $key => $label)
                            <option value="{{ $key }}" {{ old('mois', $moisEnCours) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('mois') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                {{-- Montant --}}
                <div class="cotisation-form-group">
                    <label for="montant" class="cotisation-form-label">
                        <i class="fas fa-dollar-sign"></i> Montant ($) <span class="required">*</span>
                    </label>
                    <input type="number" 
                           id="montant" 
                           name="montant" 
                           class="cotisation-form-input @error('montant') error @enderror" 
                           value="{{ old('montant', '50.00') }}" 
                           step="0.01" 
                           min="1" 
                           max="10000" 
                           placeholder="Ex: 50.00" 
                           required>
                    @error('montant') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                {{-- Mode de paiement --}}
                <div class="cotisation-form-group">
                    <label for="mode_paiement" class="cotisation-form-label">
                        <i class="fas fa-credit-card"></i> Mode de paiement <span class="required">*</span>
                    </label>
                    <select name="mode_paiement" id="mode_paiement" class="cotisation-form-select @error('mode_paiement') error @enderror" required>
                        <option value="">Sélectionnez...</option>
                        <option value="especes" {{ old('mode_paiement') === 'especes' ? 'selected' : '' }}>💵 Espèces</option>
                        <option value="virement_bancaire" {{ old('mode_paiement') === 'virement_bancaire' ? 'selected' : '' }}>🏦 Virement bancaire</option>
                        <option value="mobile_money" {{ old('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
                        <option value="autre" {{ old('mode_paiement') === 'autre' ? 'selected' : '' }}>📋 Autre</option>
                    </select>
                    @error('mode_paiement') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                {{-- Date de paiement --}}
                <div class="cotisation-form-group">
                    <label for="date_paiement" class="cotisation-form-label">
                        <i class="fas fa-calendar-check"></i> Date de paiement <span class="required">*</span>
                    </label>
                    <input type="date" 
                           id="date_paiement" 
                           name="date_paiement" 
                           class="cotisation-form-input @error('date_paiement') error @enderror" 
                           value="{{ old('date_paiement', date('Y-m-d')) }}" 
                           required>
                    @error('date_paiement') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                {{-- Référence --}}
                <div class="cotisation-form-group">
                    <label for="reference" class="cotisation-form-label">
                        <i class="fas fa-hashtag"></i> Référence / N° transaction
                    </label>
                    <input type="text" 
                           id="reference" 
                           name="reference" 
                           class="cotisation-form-input" 
                           value="{{ old('reference') }}" 
                           placeholder="Ex: TXN-2024-001">
                </div>
            </div>

            {{-- Observation --}}
            <div class="cotisation-form-group cotisation-form-full" style="margin-top: 18px;">
                <label for="observation" class="cotisation-form-label">
                    <i class="fas fa-sticky-note"></i> Observation
                </label>
                <textarea name="observation" 
                          id="observation" 
                          class="cotisation-form-textarea" 
                          rows="3" 
                          placeholder="Note éventuelle sur ce paiement...">{{ old('observation') }}</textarea>
            </div>

            {{-- Info --}}
            <div style="background: var(--color-info-light); border-radius: 10px; padding: 14px 18px; margin-top: 20px; font-size: 13px; color: #0c5460; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle" style="font-size: 1.2rem;"></i>
                <span>La cotisation mensuelle standard est de <strong>50,00 $</strong> (Article 39 du ROI). Le paiement doit être effectué avant le <strong>30 du mois</strong> (Article 24 du ROI).</span>
            </div>

            {{-- Actions --}}
            <div class="cotisation-form-actions">
                <a href="{{ route('admin.cotisations.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer le paiement
                </button>
            </div>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-cotisations.js') }}"></script>
    <script>
        // Auto-remplir le montant selon le membre sélectionné
        const membreSelect = document.getElementById('membre_id');
        const montantInput = document.getElementById('montant');
        const membresData = @json($membres->mapWithKeys(fn($m) => [$m->id => $m->cotisation_mensuelle]));

        if (membreSelect && montantInput) {
            membreSelect.addEventListener('change', function () {
                const membreId = this.value;
                if (membresData[membreId]) {
                    montantInput.value = membresData[membreId];
                }
            });
        }
    </script>
@endsection