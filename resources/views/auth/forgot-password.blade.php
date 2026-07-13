@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/forgot-password.css') }}">
@endsection

@section('content')
    {{-- Titre --}}
    <h2 class="auth-form-title">Mot de passe oublié ?</h2>
    <p class="auth-form-subtitle">Saisissez votre adresse email pour recevoir un lien de réinitialisation</p>

    {{-- Message de statut --}}
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Erreurs de validation --}}
    @if ($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Formulaire --}}
    <form method="POST" action="{{ route('password.email') }}" class="auth-form" id="forgotPasswordForm" novalidate>
        @csrf

        {{-- Champ Email --}}
        <div class="form-group">
            <label for="email" class="form-label">
                Adresse email <span class="required">*</span>
            </label>
            <div class="input-icon-wrapper">
                <i class="fas fa-envelope input-icon"></i>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input @error('email') error @enderror" 
                    placeholder="votre.email@exemple.com" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                >
            </div>
            @error('email')
                <span class="form-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Bouton --}}
        <button type="submit" class="btn btn-primary" id="forgotPasswordBtn">
            <span class="btn-text">
                <i class="fas fa-paper-plane"></i> Envoyer le lien
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
    <script src="{{ asset('assets/js/forgot-password.js') }}"></script>
@endsection