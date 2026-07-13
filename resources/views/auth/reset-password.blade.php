@extends('layouts.auth')

@section('title', 'Réinitialiser le mot de passe')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/reset-password.css') }}">
@endsection

@section('content')
    {{-- Titre --}}
    <h2 class="auth-form-title">Nouveau mot de passe</h2>
    <p class="auth-form-subtitle">Choisissez un mot de passe sécurisé pour votre compte</p>

    {{-- Erreurs --}}
    @if ($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Formulaire --}}
    <form method="POST" action="{{ route('password.update') }}" class="auth-form" id="resetPasswordForm" novalidate>
        @csrf

        {{-- Token caché --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Champ Email (caché mais requis par Laravel) --}}
        <input type="hidden" name="email" value="{{ request('email') }}">

        {{-- Nouveau mot de passe --}}
        <div class="form-group">
            <label for="mot_de_passe" class="form-label">
                Nouveau mot de passe <span class="required">*</span>
            </label>
            <div class="input-icon-wrapper">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="mot_de_passe" 
                    name="mot_de_passe" 
                    class="form-input @error('mot_de_passe') error @enderror" 
                    placeholder="Minimum 8 caractères" 
                    required 
                    autofocus
                >
                <i class="fas fa-eye input-icon-right toggle-password" data-target="mot_de_passe" title="Afficher"></i>
            </div>
            @error('mot_de_passe')
                <span class="form-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Confirmation du mot de passe --}}
        <div class="form-group">
            <label for="mot_de_passe_confirmation" class="form-label">
                Confirmer le mot de passe <span class="required">*</span>
            </label>
            <div class="input-icon-wrapper">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="mot_de_passe_confirmation" 
                    name="mot_de_passe_confirmation" 
                    class="form-input" 
                    placeholder="Répétez le mot de passe" 
                    required
                >
                <i class="fas fa-eye input-icon-right toggle-password" data-target="mot_de_passe_confirmation" title="Afficher"></i>
            </div>
        </div>

        {{-- Indicateur de force du mot de passe --}}
        <div class="password-strength" id="passwordStrength">
            <div class="strength-bar">
                <div class="strength-fill" id="strengthFill"></div>
            </div>
            <span class="strength-text" id="strengthText"></span>
        </div>

        {{-- Exigences du mot de passe --}}
        <div class="password-requirements" id="passwordRequirements">
            <p class="requirement-title"><i class="fas fa-shield-alt"></i> Le mot de passe doit contenir :</p>
            <ul>
                <li id="req-length"><i class="fas fa-times-circle"></i> Au moins 8 caractères</li>
                <li id="req-uppercase"><i class="fas fa-times-circle"></i> Une majuscule</li>
                <li id="req-lowercase"><i class="fas fa-times-circle"></i> Une minuscule</li>
                <li id="req-number"><i class="fas fa-times-circle"></i> Un chiffre</li>
                <li id="req-special"><i class="fas fa-times-circle"></i> Un caractère spécial (@, #, $, etc.)</li>
            </ul>
        </div>

        {{-- Bouton --}}
        <button type="submit" class="btn btn-primary" id="resetPasswordBtn">
            <span class="btn-text">
                <i class="fas fa-save"></i> Enregistrer le mot de passe
            </span>
            <span class="btn-loader"></span>
        </button>
    </form>

    {{-- Lien retour connexion --}}
    <div class="auth-form-footer">
        <a href="{{ route('login') }}">
            <i class="fas fa-arrow-left"></i> Retour à la connexion
        </a>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/reset-password.js') }}"></script>
@endsection