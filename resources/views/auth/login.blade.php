@extends('layouts.auth')

@section('title', 'Connexion')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
@endsection

@section('content')
    {{-- Titre --}}
    <h2 class="auth-form-title">Connexion</h2>
    <p class="auth-form-subtitle">Connectez-vous à votre espace d'administration</p>

    {{-- Messages d'erreur --}}
    @if ($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Message de succès (après réinitialisation mot de passe) --}}
    @if (session('status'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Message de déconnexion --}}
    @if (session('logout'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <span>{{ session('logout') }}</span>
        </div>
    @endif

    {{-- Formulaire de connexion --}}
    <form method="POST" action="{{ route('login') }}" class="auth-form" id="loginForm" novalidate>
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

        {{-- Champ Mot de passe --}}
        <div class="form-group">
            <label for="mot_de_passe" class="form-label">
                Mot de passe <span class="required">*</span>
            </label>
            <div class="input-icon-wrapper">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="mot_de_passe" 
                    name="mot_de_passe" 
                    class="form-input @error('mot_de_passe') error @enderror" 
                    placeholder="Votre mot de passe" 
                    required
                >
                <i class="fas fa-eye input-icon-right toggle-password" data-target="mot_de_passe" title="Afficher le mot de passe"></i>
            </div>
            @error('mot_de_passe')
                <span class="form-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Lien mot de passe oublié --}}
        <a href="{{ route('password.request') }}" class="auth-link-forgot">
            <i class="fas fa-question-circle"></i> Mot de passe oublié ?
        </a>

        {{-- Bouton de connexion --}}
        <button type="submit" class="btn btn-primary" id="loginBtn">
            <span class="btn-text">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </span>
            <span class="btn-loader"></span>
        </button>
    </form>

    {{-- Séparateur --}}
    <div class="auth-separator">
        <span>ou</span>
    </div>

    {{-- Bouton Google --}}
    <a href="{{ route('google.login') }}" class="btn btn-google">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C12.955 4 4 12.955 4 24s8.955 20 20 20s20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
            <path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
        </svg>
        <span>Se connecter avec Google</span>
    </a>

    {{-- Lien retour accueil --}}
    <div class="auth-form-footer">
        <a href="{{ url('/') }}">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/login.js') }}"></script>
@endsection