@extends('layouts.public')

@section('title', 'Contact')

@section('meta_description', 'Contactez AHD-RDC pour toute question, partenariat, don ou adhésion. Siège social : Minova, Territoire de Kalehe, Sud-Kivu, RD Congo.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-envelope-open-text"></i>
            </div>
            <h1 class="page-header-title">Contactez-nous</h1>
            <p class="page-header-subtitle">
                Une question, une suggestion, une demande de partenariat ? Nous sommes à votre écoute.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Contact</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION COORDONNÉES --}}
    {{-- ============================================ --}}
    <section class="contact-coord-section">
        <div class="container">
            <div class="contact-coord-grid">
                <div class="contact-coord-card">
                    <div class="contact-coord-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-coord-content">
                        <h4>Adresse</h4>
                        <p>
                            Minova, Territoire de Kalehe<br>
                            Province du Sud-Kivu<br>
                            République Démocratique du Congo
                        </p>
                    </div>
                </div>

                <div class="contact-coord-card">
                    <div class="contact-coord-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="contact-coord-content">
                        <h4>Téléphone</h4>
                        <p>
                            <a href="tel:+243994656250">+243 994 656 250</a><br>
                            <a href="tel:+243973307807">+243 973 307 807</a>
                        </p>
                    </div>
                </div>

                <div class="contact-coord-card">
                    <div class="contact-coord-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-coord-content">
                        <h4>Email</h4>
                        <p>
                            <a href="mailto:contact@ahd-rdc.org">contact@ahd-rdc.org</a>
                        </p>
                    </div>
                </div>

                <div class="contact-coord-card">
                    <div class="contact-coord-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="contact-coord-content">
                        <h4>Heures d'ouverture</h4>
                        <p>
                            Lundi — Vendredi<br>
                            8h00 — 16h00<br>
                            <small>Heure de Kinshasa (GMT+1)</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION FORMULAIRE --}}
    {{-- ============================================ --}}
    <section class="contact-form-section" id="contact-form">
        <div class="container">
            <div class="contact-form-grid">
                {{-- Formulaire --}}
                <div class="contact-form-wrapper">
                    <h2 class="contact-form-title">
                        <i class="fas fa-paper-plane"></i> Envoyez-nous un message
                    </h2>
                    <p class="contact-form-subtitle">
                        Remplissez le formulaire ci-dessous. Nous vous répondrons dans les plus brefs délais.
                    </p>

                    {{-- Message de succès --}}
                    @if(session('success'))
                    <div class="contact-alert contact-alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    {{-- Erreurs --}}
                    @if($errors->any())
                    <div class="contact-alert contact-alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Veuillez corriger les erreurs ci-dessous.</span>
                    </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" id="contactForm" novalidate>
                        @csrf

                        <div class="contact-form-row">
                            <div class="contact-form-group">
                                <label for="nom" class="contact-label">
                                    Nom complet <span class="required">*</span>
                                </label>
                                <div class="contact-input-wrapper">
                                    <i class="fas fa-user contact-input-icon"></i>
                                    <input type="text" id="nom" name="nom" class="contact-input @error('nom') error @enderror" value="{{ old('nom') }}" placeholder="Votre nom complet" required>
                                </div>
                                @error('nom')
                                    <span class="contact-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="contact-form-group">
                                <label for="email" class="contact-label">
                                    Adresse email <span class="required">*</span>
                                </label>
                                <div class="contact-input-wrapper">
                                    <i class="fas fa-envelope contact-input-icon"></i>
                                    <input type="email" id="email" name="email" class="contact-input @error('email') error @enderror" value="{{ old('email') }}" placeholder="votre@email.com" required>
                                </div>
                                @error('email')
                                    <span class="contact-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="contact-form-row">
                            <div class="contact-form-group">
                                <label for="telephone" class="contact-label">Téléphone</label>
                                <div class="contact-input-wrapper">
                                    <i class="fas fa-phone-alt contact-input-icon"></i>
                                    <input type="tel" id="telephone" name="telephone" class="contact-input" value="{{ old('telephone') }}" placeholder="+243 000 000 000">
                                </div>
                            </div>

                            <div class="contact-form-group">
                                <label for="organisation" class="contact-label">Organisation</label>
                                <div class="contact-input-wrapper">
                                    <i class="fas fa-building contact-input-icon"></i>
                                    <input type="text" id="organisation" name="organisation" class="contact-input" value="{{ old('organisation') }}" placeholder="Votre organisation (optionnel)">
                                </div>
                            </div>
                        </div>

                        <div class="contact-form-group">
                            <label for="motif" class="contact-label">
                                Motif du contact <span class="required">*</span>
                            </label>
                            <select id="motif" name="motif" class="contact-select @error('motif') error @enderror" required>
                                <option value="">Sélectionnez un motif...</option>
                                @foreach($motifs as $value => $label)
                                    <option value="{{ $value }}" {{ old('motif') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('motif')
                                <span class="contact-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="contact-form-group">
                            <label for="sujet" class="contact-label">
                                Sujet <span class="required">*</span>
                            </label>
                            <input type="text" id="sujet" name="sujet" class="contact-input @error('sujet') error @enderror" value="{{ old('sujet') }}" placeholder="Sujet de votre message" required>
                            @error('sujet')
                                <span class="contact-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="contact-form-group">
                            <label for="message" class="contact-label">
                                Message <span class="required">*</span>
                            </label>
                            <textarea id="message" name="message" class="contact-textarea @error('message') error @enderror" rows="6" placeholder="Rédigez votre message ici..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <span class="contact-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="contact-submit-btn" id="contactSubmitBtn">
                            <span class="btn-text">
                                <i class="fas fa-paper-plane"></i> Envoyer le message
                            </span>
                            <span class="btn-loader"></span>
                        </button>
                    </form>
                </div>

                {{-- Infos complémentaires --}}
                <div class="contact-sidebar">
                    <div class="contact-sidebar-card">
                        <h3><i class="fas fa-info-circle"></i> Informations utiles</h3>
                        <ul class="contact-sidebar-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Nous répondons généralement sous <strong>24 à 48 heures</strong>.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Pour une demande urgente, appelez-nous au <strong>+243 994 656 250</strong>.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Pour devenir membre, utilisez le formulaire <a href="{{ url('/devenir-membre') }}">Devenir membre</a>.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Pour faire un don, utilisez notre page <a href="{{ url('/faire-un-don') }}">Faire un don</a>.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="contact-sidebar-card">
                        <h3><i class="fas fa-share-alt"></i> Suivez-nous</h3>
                        <div class="contact-socials">
                            <a href="https://facebook.com/ahdrdc" target="_blank" rel="noopener" class="contact-social" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/ahdrdc" target="_blank" rel="noopener" class="contact-social" title="Twitter/X">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                            <a href="https://instagram.com/ahdrdc" target="_blank" rel="noopener" class="contact-social" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://linkedin.com/company/ahdrdc" target="_blank" rel="noopener" class="contact-social" title="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="https://youtube.com/@ahdrdc" target="_blank" rel="noopener" class="contact-social" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Slogan --}}
                    <div class="contact-slogan">
                        <i class="fas fa-quote-left"></i>
                        <p>« Être ensemble pour le bien de la communauté »</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section-page">
        <div class="container">
            <div class="cta-content-page">
                <h2 class="cta-title-page">
                    Votre <span class="highlight-page">soutien</span> fait la différence
                </h2>
                <p class="cta-text-page">
                    Chaque message, chaque don, chaque adhésion nous rapproche d'un Congo plus solidaire et plus pacifique.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/faire-un-don') }}" class="btn-page-don">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                    <a href="{{ url('/devenir-membre') }}" class="btn-page-outline">
                        <i class="fas fa-user-plus"></i> Devenir membre
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/contact.js') }}"></script>
@endsection