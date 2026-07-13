@extends('layouts.public')

@section('title', 'Devenir bénévole')

@section('meta_description', 'Rejoignez AHD-RDC en tant que bénévole et contribuez à nos projets humanitaires au Sud-Kivu et Nord-Kivu en RD Congo.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/devenir-benevole.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-hands-helping"></i>
            </div>
            <h1 class="page-header-title">Devenir bénévole</h1>
            <p class="page-header-subtitle">
                Donnez de votre temps et de vos compétences pour soutenir notre mission humanitaire auprès des communautés vulnérables.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Devenir bénévole</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION POURQUOI LE BÉNÉVOLAT --}}
    {{-- ============================================ --}}
    <section class="benevole-pourquoi-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Pourquoi s'engager</span>
                <h2 class="section-title">Donnez de votre temps, changez des vies</h2>
                <p class="section-description">
                    Le bénévolat chez AHD-RDC est une expérience enrichissante qui vous permet 
                    de contribuer directement à l'amélioration des conditions de vie des 
                    communautés vulnérables.
                </p>
            </div>

            <div class="benevole-avantages-grid">
                <div class="benevole-avantage-card">
                    <div class="benevole-avantage-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Impact direct</h4>
                    <p>Vos actions ont un impact concret sur la vie des bénéficiaires.</p>
                </div>
                <div class="benevole-avantage-card">
                    <div class="benevole-avantage-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Esprit d'équipe</h4>
                    <p>Rejoignez une communauté soudée et solidaire.</p>
                </div>
                <div class="benevole-avantage-card">
                    <div class="benevole-avantage-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>Apprentissage</h4>
                    <p>Développez de nouvelles compétences sur le terrain.</p>
                </div>
                <div class="benevole-avantage-card">
                    <div class="benevole-avantage-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Reconnaissance</h4>
                    <p>Recevez une attestation de bénévolat valorisable.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION NOS BESOINS --}}
    {{-- ============================================ --}}
    <section class="benevole-besoins-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Nos besoins</span>
                <h2 class="section-title">Domaines de bénévolat</h2>
                <p class="section-description">
                    Nous recherchons des bénévoles dans les domaines suivants.
                </p>
            </div>

            <div class="benevole-besoins-grid">
                <div class="benevole-besoin-card">
                    <div class="benevole-besoin-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h4>Santé & Nutrition</h4>
                    <p>Campagnes de sensibilisation, dépistage, éducation nutritionnelle.</p>
                </div>
                <div class="benevole-besoin-card">
                    <div class="benevole-besoin-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>Éducation & Formation</h4>
                    <p>Alphabétisation, formation professionnelle, soutien scolaire.</p>
                </div>
                <div class="benevole-besoin-card">
                    <div class="benevole-besoin-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h4>Eau & Assainissement</h4>
                    <p>Sensibilisation à l'hygiène, aménagement de points d'eau.</p>
                </div>
                <div class="benevole-besoin-card">
                    <div class="benevole-besoin-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h4>Sécurité Alimentaire</h4>
                    <p>Appui aux activités agricoles, formation en maraîchage.</p>
                </div>
                <div class="benevole-besoin-card">
                    <div class="benevole-besoin-icon">
                        <i class="fas fa-dove"></i>
                    </div>
                    <h4>Paix & Cohésion sociale</h4>
                    <p>Médiation communautaire, résolution pacifique des conflits.</p>
                </div>
                <div class="benevole-besoin-card">
                    <div class="benevole-besoin-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h4>Communication</h4>
                    <p>Appui à la communication, réseaux sociaux, reportages terrain.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FORMULAIRE DE CANDIDATURE --}}
    {{-- ============================================ --}}
    <section class="benevole-form-section" id="benevole-form">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Formulaire</span>
                <h2 class="section-title">Candidature bénévole</h2>
                <p class="section-description">
                    Remplissez ce formulaire pour proposer votre candidature comme bénévole. 
                    Les champs marqués d'un <span class="required">*</span> sont obligatoires.
                </p>
            </div>

            <div class="benevole-form-wrapper">
                <form action="{{ route('contact.store') }}" method="POST" id="benevoleForm" novalidate>
                    @csrf
                    <input type="hidden" name="motif" value="benevolat">

                    {{-- Message de succès --}}
                    @if(session('success'))
                    <div class="benevole-alert benevole-alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    {{-- ÉTAPE 1 : IDENTITÉ --}}
                    <div class="benevole-step">
                        <div class="benevole-step-header">
                            <span class="benevole-step-number">1</span>
                            <h2 class="benevole-step-title">Votre identité</h2>
                        </div>

                        <div class="benevole-form-grid">
                            <div class="benevole-form-group">
                                <label for="nom" class="benevole-label">
                                    Nom complet <span class="required">*</span>
                                </label>
                                <input type="text" id="nom" name="nom" class="benevole-input" value="{{ old('nom') }}" placeholder="Votre nom complet" required>
                            </div>

                            <div class="benevole-form-group">
                                <label for="email" class="benevole-label">
                                    Adresse email <span class="required">*</span>
                                </label>
                                <input type="email" id="email" name="email" class="benevole-input" value="{{ old('email') }}" placeholder="votre@email.com" required>
                            </div>

                            <div class="benevole-form-group">
                                <label for="telephone" class="benevole-label">
                                    Téléphone <span class="required">*</span>
                                </label>
                                <input type="tel" id="telephone" name="telephone" class="benevole-input" value="{{ old('telephone') }}" placeholder="+243 000 000 000" required>
                            </div>

                            <div class="benevole-form-group">
                                <label for="organisation" class="benevole-label">
                                    Profession / Occupation
                                </label>
                                <input type="text" id="organisation" name="organisation" class="benevole-input" value="{{ old('organisation') }}" placeholder="Votre profession actuelle">
                            </div>
                        </div>
                    </div>

                    {{-- ÉTAPE 2 : COMPÉTENCES & DISPONIBILITÉS --}}
                    <div class="benevole-step">
                        <div class="benevole-step-header">
                            <span class="benevole-step-number">2</span>
                            <h2 class="benevole-step-title">Vos compétences et disponibilités</h2>
                        </div>

                        <div class="benevole-form-group">
                            <label class="benevole-label">Domaines d'intérêt (plusieurs choix possibles)</label>
                            <div class="benevole-checkboxes-grid">
                                <label class="benevole-checkbox-item">
                                    <input type="checkbox" name="domaines[]" value="sante"> Santé & Nutrition
                                </label>
                                <label class="benevole-checkbox-item">
                                    <input type="checkbox" name="domaines[]" value="education"> Éducation & Formation
                                </label>
                                <label class="benevole-checkbox-item">
                                    <input type="checkbox" name="domaines[]" value="wash"> Eau & Assainissement
                                </label>
                                <label class="benevole-checkbox-item">
                                    <input type="checkbox" name="domaines[]" value="securite_alimentaire"> Sécurité Alimentaire
                                </label>
                                <label class="benevole-checkbox-item">
                                    <input type="checkbox" name="domaines[]" value="paix"> Paix & Cohésion sociale
                                </label>
                                <label class="benevole-checkbox-item">
                                    <input type="checkbox" name="domaines[]" value="communication"> Communication
                                </label>
                                <label class="benevole-checkbox-item">
                                    <input type="checkbox" name="domaines[]" value="protection"> Protection
                                </label>
                                <label class="benevole-checkbox-item">
                                    <input type="checkbox" name="domaines[]" value="environnement"> Environnement
                                </label>
                            </div>
                        </div>

                        <div class="benevole-form-group">
                            <label class="benevole-label">Disponibilité</label>
                            <div class="benevole-radio-grid">
                                <label class="benevole-radio-item">
                                    <input type="radio" name="disponibilite" value="temps_plein"> Temps plein
                                </label>
                                <label class="benevole-radio-item">
                                    <input type="radio" name="disponibilite" value="temps_partiel"> Temps partiel
                                </label>
                                <label class="benevole-radio-item">
                                    <input type="radio" name="disponibilite" value="weekend"> Week-ends
                                </label>
                                <label class="benevole-radio-item">
                                    <input type="radio" name="disponibilite" value="ponctuel"> Ponctuel
                                </label>
                            </div>
                        </div>

                        <div class="benevole-form-group">
                            <label for="sujet" class="benevole-label">
                                Compétences particulières
                            </label>
                            <input type="text" id="sujet" name="sujet" class="benevole-input" value="{{ old('sujet') }}" placeholder="Ex: informatique, médecine, enseignement, agriculture...">
                        </div>

                        <div class="benevole-form-group">
                            <label for="message" class="benevole-label">
                                Motivation <span class="required">*</span>
                            </label>
                            <textarea id="message" name="message" class="benevole-textarea" rows="5" placeholder="Expliquez pourquoi vous souhaitez devenir bénévole chez AHD-RDC et ce que vous pouvez apporter..." required>{{ old('message') }}</textarea>
                        </div>
                    </div>

                    {{-- SOUMISSION --}}
                    <div class="benevole-submit-section">
                        <button type="submit" class="benevole-submit-btn" id="benevoleSubmitBtn">
                            <span class="btn-text">
                                <i class="fas fa-paper-plane"></i> Envoyer ma candidature
                            </span>
                            <span class="btn-loader"></span>
                        </button>

                        <p class="benevole-info-text">
                            <i class="fas fa-info-circle"></i> 
                            Votre candidature sera examinée par notre équipe. Nous vous contacterons 
                            dans les plus brefs délais pour discuter de votre engagement.
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
                <h2 class="cta-title-page">
                    <span class="highlight-page">Merci</span> pour votre engagement
                </h2>
                <p class="cta-text-page">
                    Chaque bénévole est un maillon essentiel de notre chaîne de solidarité.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/faire-un-don') }}" class="btn-page-don">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                    <a href="{{ url('/contact') }}" class="btn-page-outline">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/devenir-benevole.js') }}"></script>
@endsection