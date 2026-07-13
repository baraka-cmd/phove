@extends('layouts.public')

@section('title', 'Devenir partenaire')

@section('meta_description', 'Devenez partenaire d\'AHD-RDC et contribuez au développement communautaire en République Démocratique du Congo. Ensemble, changeons des vies.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/devenir-partenaire.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h1 class="page-header-title">Devenir partenaire</h1>
            <p class="page-header-subtitle">
                Unissez vos forces aux nôtres pour amplifier l'impact de nos actions humanitaires auprès des communautés vulnérables.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Devenir partenaire</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION POURQUOI DEVENIR PARTENAIRE --}}
    {{-- ============================================ --}}
    <section class="partenaire-pourquoi-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Pourquoi nous rejoindre</span>
                <h2 class="section-title">Les avantages du partenariat</h2>
                <p class="section-description">
                    En devenant partenaire d'AHD-RDC, vous rejoignez un réseau d'organisations 
                    engagées pour le développement durable et la paix en RD Congo.
                </p>
            </div>

            <div class="partenaire-avantages-grid">
                <div class="partenaire-avantage-card">
                    <div class="partenaire-avantage-icon"><i class="fas fa-eye"></i></div>
                    <h4>Visibilité</h4>
                    <p>Votre logo et votre nom sur notre plateforme, nos rapports et nos supports de communication.</p>
                </div>
                <div class="partenaire-avantage-card">
                    <div class="partenaire-avantage-icon"><i class="fas fa-chart-line"></i></div>
                    <h4>Impact mesurable</h4>
                    <p>Des rapports détaillés sur l'utilisation des fonds et l'impact de votre contribution.</p>
                </div>
                <div class="partenaire-avantage-card">
                    <div class="partenaire-avantage-icon"><i class="fas fa-globe-africa"></i></div>
                    <h4>Présence terrain</h4>
                    <p>Participation aux visites de terrain et aux événements organisés par AHD-RDC.</p>
                </div>
                <div class="partenaire-avantage-card">
                    <div class="partenaire-avantage-icon"><i class="fas fa-file-invoice"></i></div>
                    <h4>Reçus fiscaux</h4>
                    <p>Des attestations de partenariat et des reçus fiscaux pour vos déclarations.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION TYPES DE PARTENARIAT --}}
    {{-- ============================================ --}}
    <section class="partenaire-types-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Nos besoins</span>
                <h2 class="section-title">Types de partenariat</h2>
                <p class="section-description">
                    Nous proposons plusieurs formes de collaboration adaptées aux objectifs de chaque partenaire.
                </p>
            </div>

            <div class="partenaire-types-grid">
                <div class="partenaire-type-card">
                    <div class="partenaire-type-icon"><i class="fas fa-coins"></i></div>
                    <h4>Bailleur de fonds</h4>
                    <p>Financez un ou plusieurs de nos projets humanitaires et recevez des rapports détaillés sur l'impact de votre contribution.</p>
                </div>
                <div class="partenaire-type-card">
                    <div class="partenaire-type-icon"><i class="fas fa-tools"></i></div>
                    <h4>Partenaire technique</h4>
                    <p>Apportez votre expertise, vos équipements ou votre savoir-faire pour renforcer la qualité de nos interventions.</p>
                </div>
                <div class="partenaire-type-card">
                    <div class="partenaire-type-icon"><i class="fas fa-truck"></i></div>
                    <h4>Partenaire logistique</h4>
                    <p>Soutenez nos opérations en fournissant du matériel, des véhicules ou des locaux pour nos activités.</p>
                </div>
                <div class="partenaire-type-card">
                    <div class="partenaire-type-icon"><i class="fas fa-bullhorn"></i></div>
                    <h4>Partenaire média</h4>
                    <p>Aidez-nous à amplifier notre message en relayant nos actions et nos campagnes de sensibilisation.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FORMULAIRE DE PARTENARIAT --}}
    {{-- ============================================ --}}
    <section class="partenaire-form-section" id="partenaire-form">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Formulaire</span>
                <h2 class="section-title">Proposition de partenariat</h2>
                <p class="section-description">
                    Remplissez ce formulaire pour proposer un partenariat. Les champs marqués d'un <span class="required">*</span> sont obligatoires.
                </p>
            </div>

            <div class="partenaire-form-wrapper">
                <form action="{{ route('contact.store') }}" method="POST" id="partenaireForm" novalidate>
                    @csrf
                    <input type="hidden" name="motif" value="partenariat">

                    @if(session('success'))
                    <div class="partenaire-alert partenaire-alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    {{-- ÉTAPE 1 : ORGANISATION --}}
                    <div class="partenaire-step">
                        <div class="partenaire-step-header">
                            <span class="partenaire-step-number">1</span>
                            <h2 class="partenaire-step-title">Votre organisation</h2>
                        </div>
                        <div class="partenaire-form-grid">
                            <div class="partenaire-form-group">
                                <label for="organisation" class="partenaire-label">Nom de l'organisation <span class="required">*</span></label>
                                <input type="text" id="organisation" name="organisation" class="partenaire-input" value="{{ old('organisation') }}" placeholder="Nom de votre organisation" required>
                            </div>
                            <div class="partenaire-form-group">
                                <label for="nom" class="partenaire-label">Nom du contact <span class="required">*</span></label>
                                <input type="text" id="nom" name="nom" class="partenaire-input" value="{{ old('nom') }}" placeholder="Votre nom complet" required>
                            </div>
                            <div class="partenaire-form-group">
                                <label for="email" class="partenaire-label">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" class="partenaire-input" value="{{ old('email') }}" placeholder="contact@organisation.org" required>
                            </div>
                            <div class="partenaire-form-group">
                                <label for="telephone" class="partenaire-label">Téléphone <span class="required">*</span></label>
                                <input type="tel" id="telephone" name="telephone" class="partenaire-input" value="{{ old('telephone') }}" placeholder="+243 000 000 000" required>
                            </div>
                            <div class="partenaire-form-group partenaire-form-full">
                                <label for="adresse" class="partenaire-label">Site web</label>
                                <input type="url" id="adresse" name="adresse" class="partenaire-input" value="{{ old('adresse') }}" placeholder="https://www.votre-organisation.org">
                            </div>
                        </div>
                    </div>

                    {{-- ÉTAPE 2 : PARTENARIAT --}}
                    <div class="partenaire-step">
                        <div class="partenaire-step-header">
                            <span class="partenaire-step-number">2</span>
                            <h2 class="partenaire-step-title">Votre proposition</h2>
                        </div>
                        <div class="partenaire-form-group">
                            <label class="partenaire-label">Type de partenariat envisagé <span class="required">*</span></label>
                            <div class="partenaire-radio-grid">
                                <label class="partenaire-radio-item">
                                    <input type="radio" name="type_partenariat" value="bailleur" checked> Bailleur de fonds
                                </label>
                                <label class="partenaire-radio-item">
                                    <input type="radio" name="type_partenariat" value="technique"> Partenaire technique
                                </label>
                                <label class="partenaire-radio-item">
                                    <input type="radio" name="type_partenariat" value="logistique"> Partenaire logistique
                                </label>
                                <label class="partenaire-radio-item">
                                    <input type="radio" name="type_partenariat" value="media"> Partenaire média
                                </label>
                            </div>
                        </div>
                        <div class="partenaire-form-group">
                            <label for="sujet" class="partenaire-label">Titre de la proposition <span class="required">*</span></label>
                            <input type="text" id="sujet" name="sujet" class="partenaire-input" value="{{ old('sujet') }}" placeholder="Ex: Partenariat pour la sécurité alimentaire à Minova" required>
                        </div>
                        <div class="partenaire-form-group">
                            <label for="message" class="partenaire-label">Description du partenariat <span class="required">*</span></label>
                            <textarea id="message" name="message" class="partenaire-textarea" rows="6" placeholder="Décrivez votre proposition de partenariat, les objectifs, les ressources que vous pouvez apporter, le budget éventuel..." required>{{ old('message') }}</textarea>
                        </div>
                    </div>

                    {{-- SOUMISSION --}}
                    <div class="partenaire-submit-section">
                        <button type="submit" class="partenaire-submit-btn" id="partenaireSubmitBtn">
                            <span class="btn-text"><i class="fas fa-paper-plane"></i> Envoyer ma proposition</span>
                            <span class="btn-loader"></span>
                        </button>
                        <p class="partenaire-info-text">
                            <i class="fas fa-info-circle"></i> 
                            Votre proposition sera examinée par notre équipe. Nous vous contacterons dans les plus brefs délais pour en discuter.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section-page">
        <div class="container">
            <div class="cta-content-page">
                <h2 class="cta-title-page">Construisons <span class="highlight-page">ensemble</span> l'avenir</h2>
                <p class="cta-text-page">Chaque partenariat est une pierre apportée à l'édifice du développement communautaire en RD Congo.</p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/partenaires') }}" class="btn-page-don"><i class="fas fa-users"></i> Nos partenaires</a>
                    <a href="{{ url('/contact') }}" class="btn-page-outline"><i class="fas fa-envelope"></i> Nous contacter</a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/devenir-partenaire.js') }}"></script>
@endsection