@extends('layouts.admin')

@section('title', 'Modifier le don')

@section('page_title', 'Modifier le don')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations du don de ' . $don->donateur_nom . ' — ' . number_format($don->montant, 0) . ' ' . $don->devise)

@php
    $breadcrumb = [
        ['label' => 'Dons', 'url' => route('admin.dons.index')],
        'Modifier le don'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dons.css') }}">
@endsection

@section('content')

    <div class="don-form-card">

        {{-- En-tête --}}
        <div class="don-form-header">
            <div class="don-form-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier le don</h2>
                <p>
                    Enregistré le {{ $don->created_at->format('d/m/Y à H:i') }} — 
                    Reçu : <strong>{{ $don->numero_recu ?? 'Non généré' }}</strong>
                </p>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.dons.update', $don) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION 1 : Donateur --}}
            <div class="don-form-section">
                <h4 class="don-form-section-title">
                    <i class="fas fa-user"></i> Informations du donateur
                </h4>
                <div class="don-form-grid">
                    <div class="don-form-group don-form-full">
                        <label for="donateur_nom" class="don-form-label">Nom complet <span class="required">*</span></label>
                        <input type="text" name="donateur_nom" id="donateur_nom" class="don-form-input @error('donateur_nom') error @enderror" 
                               value="{{ old('donateur_nom', $don->donateur_nom) }}" placeholder="Ex: Jean M." required>
                        @error('donateur_nom') <span class="don-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                    <div class="don-form-group">
                        <label for="donateur_email" class="don-form-label">Email</label>
                        <input type="email" name="donateur_email" id="donateur_email" class="don-form-input" 
                               value="{{ old('donateur_email', $don->donateur_email) }}" placeholder="Ex: jean@email.com">
                    </div>
                    <div class="don-form-group">
                        <label for="donateur_telephone" class="don-form-label">Téléphone</label>
                        <input type="text" name="donateur_telephone" id="donateur_telephone" class="don-form-input" 
                               value="{{ old('donateur_telephone', $don->donateur_telephone) }}" placeholder="Ex: +243 000 000 000">
                    </div>
                    <div class="don-form-group">
                        <label for="donateur_adresse" class="don-form-label">Adresse</label>
                        <input type="text" name="donateur_adresse" id="donateur_adresse" class="don-form-input" 
                               value="{{ old('donateur_adresse', $don->donateur_adresse) }}" placeholder="Ex: Minova, Sud-Kivu">
                    </div>
                    <div class="don-form-group">
                        <label for="type_donateur" class="don-form-label">Type de donateur <span class="required">*</span></label>
                        <select name="type_donateur" id="type_donateur" class="don-form-select" required>
                            <option value="particulier" {{ old('type_donateur', $don->type_donateur) === 'particulier' ? 'selected' : '' }}>Particulier</option>
                            <option value="organisation" {{ old('type_donateur', $don->type_donateur) === 'organisation' ? 'selected' : '' }}>Organisation</option>
                            <option value="etat" {{ old('type_donateur', $don->type_donateur) === 'etat' ? 'selected' : '' }}>État</option>
                            <option value="anonyme" {{ old('type_donateur', $don->type_donateur) === 'anonyme' ? 'selected' : '' }}>Anonyme</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Don --}}
            <div class="don-form-section">
                <h4 class="don-form-section-title">
                    <i class="fas fa-coins"></i> Détails du don
                </h4>
                <div class="don-form-grid">
                    <div class="don-form-group">
                        <label for="montant" class="don-form-label">Montant <span class="required">*</span></label>
                        <input type="number" name="montant" id="montant" class="don-form-input @error('montant') error @enderror" 
                               value="{{ old('montant', $don->montant) }}" placeholder="Ex: 50" step="0.01" min="1" required>
                        @error('montant') <span class="don-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                    <div class="don-form-group">
                        <label for="devise" class="don-form-label">Devise <span class="required">*</span></label>
                        <select name="devise" id="devise" class="don-form-select" required>
                            <option value="USD" {{ old('devise', $don->devise) === 'USD' ? 'selected' : '' }}>USD — Dollar américain</option>
                            <option value="CDF" {{ old('devise', $don->devise) === 'CDF' ? 'selected' : '' }}>CDF — Franc congolais</option>
                        </select>
                    </div>
                    <div class="don-form-group">
                        <label for="type_don" class="don-form-label">Type de don <span class="required">*</span></label>
                        <select name="type_don" id="type_don" class="don-form-select" required>
                            <option value="ponctuel" {{ old('type_don', $don->type_don) === 'ponctuel' ? 'selected' : '' }}>Don ponctuel</option>
                            <option value="recurrent" {{ old('type_don', $don->type_don) === 'recurrent' ? 'selected' : '' }}>Don récurrent (mensuel)</option>
                        </select>
                    </div>
                    <div class="don-form-group">
                        <label for="projet_id" class="don-form-label">Projet soutenu</label>
                        <select name="projet_id" id="projet_id" class="don-form-select">
                            <option value="">Don général (là où les besoins sont urgents)</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id', $don->projet_id) == $projet->id ? 'selected' : '' }}>
                                    {{ Str::limit($projet->titre, 50) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Paiement --}}
            <div class="don-form-section">
                <h4 class="don-form-section-title">
                    <i class="fas fa-credit-card"></i> Mode de paiement
                </h4>
                <div class="don-form-grid">
                    <div class="don-form-group">
                        <label for="mode_paiement" class="don-form-label">Mode de paiement <span class="required">*</span></label>
                        <select name="mode_paiement" id="mode_paiement" class="don-form-select" required>
                            <option value="">Sélectionnez...</option>
                            <option value="especes" {{ old('mode_paiement', $don->mode_paiement) === 'especes' ? 'selected' : '' }}>Espèces</option>
                            <option value="virement_bancaire" {{ old('mode_paiement', $don->mode_paiement) === 'virement_bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="mobile_money" {{ old('mode_paiement', $don->mode_paiement) === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="carte_bancaire" {{ old('mode_paiement', $don->mode_paiement) === 'carte_bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                            <option value="paypal" {{ old('mode_paiement', $don->mode_paiement) === 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="autre" {{ old('mode_paiement', $don->mode_paiement) === 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div class="don-form-group">
                        <label for="reference_transaction" class="don-form-label">Référence / N° transaction</label>
                        <input type="text" name="reference_transaction" id="reference_transaction" class="don-form-input" 
                               value="{{ old('reference_transaction', $don->reference_transaction) }}" placeholder="Ex: TXN-2026-001">
                    </div>
                    <div class="don-form-group">
                        <label for="date_don" class="don-form-label">Date du don <span class="required">*</span></label>
                        <input type="date" name="date_don" id="date_don" class="don-form-input @error('date_don') error @enderror" 
                               value="{{ old('date_don', $don->date_don ? $don->date_don->format('Y-m-d') : date('Y-m-d')) }}" required>
                        @error('date_don') <span class="don-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                    <div class="don-form-group">
                        <label for="statut" class="don-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="don-form-select" required>
                            <option value="confirme" {{ old('statut', $don->statut) === 'confirme' ? 'selected' : '' }}>Confirmé</option>
                            <option value="en_attente" {{ old('statut', $don->statut) === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="annule" {{ old('statut', $don->statut) === 'annule' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SECTION 4 : Message --}}
            <div class="don-form-section">
                <h4 class="don-form-section-title">
                    <i class="fas fa-sticky-note"></i> Message du donateur
                </h4>
                <div class="don-form-group don-form-full">
                    <label for="message_donateur" class="don-form-label">Message (optionnel)</label>
                    <textarea name="message_donateur" id="message_donateur" class="don-form-textarea" rows="3" 
                              placeholder="Message laissé par le donateur...">{{ old('message_donateur', $don->message_donateur) }}</textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="don-form-actions">
                <a href="{{ route('admin.dons.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>

        {{-- Suppression --}}
        <form action="{{ route('admin.dons.destroy', $don) }}" method="POST" style="margin-top: 16px; text-align: right;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce don ? Cette action est irréversible.');">
                <i class="fas fa-trash-alt"></i> Supprimer ce don
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-dons.js') }}"></script>
@endsection