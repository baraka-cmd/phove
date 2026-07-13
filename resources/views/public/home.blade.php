@extends('layouts.public')

@section('title', 'Accueil')

@section('meta_description', 'AHD-RDC — Action Humanitaire pour le Développement en RD Congo. ONG engagée dans le développement communautaire, la protection des personnes vulnérables et la promotion de la paix à l\'Est de la RDC.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-home.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- HERO SECTION — SLIDER --}}
    {{-- ============================================ --}}
    {{-- ============================================ --}}
{{-- HERO SECTION — CORRIGÉ --}}
{{-- ============================================ --}}
<section class="hero" id="hero">
    {{-- Slides --}}
    <div class="hero-slider">
        <div class="hero-slide active" style="background-image: url('{{ asset('assets/images/logo-ahd.png') }}');">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-slide" style="background-image: url('{{ asset('assets/images/logo-ahd.png') }}');">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-slide" style="background-image: url('{{ asset('assets/images/logo-ahd.png') }}');">
            <div class="hero-overlay"></div>
        </div>
    </div>

    {{-- Indicateurs --}}
    <div class="hero-indicators">
        <span class="hero-indicator active" data-slide="0"></span>
        <span class="hero-indicator" data-slide="1"></span>
        <span class="hero-indicator" data-slide="2"></span>
    </div>

    {{-- Contenu centré --}}
    <div class="container">
        <div class="hero-content">
            <span class="hero-badge">
                <i class="fas fa-dot-circle"></i> Depuis 2017 — Minova, Sud-Kivu, RD Congo
            </span>
            <h1 class="hero-title">
                Ensemble, <span class="highlight">bâtissons l'espoir</span> pour les communautés vulnérables
            </h1>
            <p class="hero-description">
                Action Humanitaire pour le Développement en RD Congo œuvre pour la résilience 
                communautaire, la protection des femmes et des enfants, et la consolidation de la paix 
                dans les provinces du Nord-Kivu et Sud-Kivu.
            </p>
            <div class="hero-buttons">
                <a href="{{ url('/faire-un-don') }}" class="hero-btn-primary">
                    <i class="fas fa-heart"></i> Faire un don
                </a>
                <a href="{{ url('/qui-sommes-nous') }}" class="hero-btn-secondary">
                    <i class="fas fa-info-circle"></i> Découvrir notre mission
                </a>
            </div>
        </div>
    </div>

    {{-- Flèches de navigation --}}
    <button class="hero-arrow hero-arrow-left" id="heroPrev" aria-label="Slide précédent">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="hero-arrow hero-arrow-right" id="heroNext" aria-label="Slide suivant">
        <i class="fas fa-chevron-right"></i>
    </button>
</section>

    {{-- ============================================ --}}
    {{-- SECTION COMPTEURS — IMPACT EN CHIFFRES --}}
    {{-- ============================================ --}}
    <section class="counters-section">
        <div class="container">
            <div class="counters-grid">
                {{-- Compteur 1 : Projets --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="5">0</span>
                    </div>
                    <div class="counter-label">Projets humanitaires</div>
                    <p class="counter-desc">Réalisés depuis 2017</p>
                </div>

                {{-- Compteur 2 : Bénéficiaires --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="10000">0</span>
                        <span class="counter-plus">+</span>
                    </div>
                    <div class="counter-label">Bénéficiaires atteints</div>
                    <p class="counter-desc">Femmes, enfants, personnes vulnérables</p>
                </div>

                {{-- Compteur 3 : Zones --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="4">0</span>
                    </div>
                    <div class="counter-label">Zones d'intervention</div>
                    <p class="counter-desc">Minova, Kirotshe, Kalehe, Goma</p>
                </div>

                {{-- Compteur 4 : Années --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="{{ date('Y') - 2017 }}">0</span>
                    </div>
                    <div class="counter-label">Années d'expérience</div>
                    <p class="counter-desc">Au service des communautés</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION À PROPOS — QUI SOMMES-NOUS ? --}}
    {{-- ============================================ --}}
    <section class="about-section" id="about">
        <div class="container">
            <div class="about-grid">
                {{-- Image --}}
                <div class="about-image-wrapper">
                    <img src="{{ asset('assets/images/about/about-ahd.jpg') }}" 
                         alt="AHD-RDC — Action humanitaire sur le terrain" 
                         class="about-image"
                         onerror="this.src='https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=600'">
                    <div class="about-image-badge">
                        <span class="badge-number">{{ date('Y') - 2017 }}+</span>
                        <span class="badge-text">Années d'action<br>humanitaire</span>
                    </div>
                </div>

                {{-- Contenu --}}
                <div class="about-content">
                    <span class="section-subtitle">Qui sommes-nous ?</span>
                    <h2 class="section-title">
                        Une organisation au service des communautés vulnérables de l'Est de la RDC
                    </h2>

                    <p class="about-text">
                        <strong>Action Humanitaire pour le Développement en RD Congo (AHD-RDC)</strong> 
                        est une organisation non gouvernementale de droit congolais créée le 
                        <strong>7 janvier 2017</strong> à Minova, dans le territoire de Kalehe, 
                        province du Sud-Kivu.
                    </p>

                    <p class="about-text">
                        Née dans une région marquée par des conflits intercommunautaires, des guerres 
                        et des crises humanitaires, AHD-RDC s'est donné pour mission de 
                        <strong>renforcer la résilience communautaire</strong> par l'auto-prise en 
                        charge, la promotion de la paix et la protection des droits humains.
                    </p>

                    <div class="about-values-list">
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Transparence</strong>
                                <span>Gestion claire et ouverte</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Redevabilité</strong>
                                <span>Rendre compte de nos actions</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Professionnalisme</strong>
                                <span>Excellence dans l'action</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Compassion</strong>
                                <span>Au service des plus démunis</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Sensibilité au genre</strong>
                                <span>Égalité et inclusion</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('/qui-sommes-nous') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> En savoir plus sur notre histoire
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION DOMAINES D'INTERVENTION --}}
    {{-- ============================================ --}}
    <section class="domains-section" id="domains">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ce que nous faisons</span>
                <h2 class="section-title">Nos domaines d'intervention</h2>
                <p class="section-description">
                    Conformément à l'article 9 de nos statuts, AHD-RDC intervient dans les domaines 
                    suivants pour répondre aux besoins essentiels des communautés.
                </p>
            </div>

            <div class="domains-grid">
                {{-- Domaine 1 --}}
                <a href="{{ url('/projets?domaine=sante_nutrition') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-heartbeat"></i></div>
                    <h3 class="domain-title">Santé-Nutrition</h3>
                    <p class="domain-description">
                        Lutte contre la malnutrition, sensibilisation aux MST/VIH-SIDA, 
                        éducation à la santé familiale.
                    </p>
                </a>

                {{-- Domaine 2 --}}
                <a href="{{ url('/projets?domaine=education') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3 class="domain-title">Éducation</h3>
                    <p class="domain-description">
                        Formation professionnelle, apprentissage des métiers et 
                        réinsertion socioéconomique.
                    </p>
                </a>

                {{-- Domaine 3 --}}
                <a href="{{ url('/projets?domaine=protection') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="domain-title">Protection</h3>
                    <p class="domain-description">
                        Protection des enfants et des femmes, promotion des droits 
                        humains et plaidoyer.
                    </p>
                </a>

                {{-- Domaine 4 --}}
                <a href="{{ url('/projets?domaine=wash') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-tint"></i></div>
                    <h3 class="domain-title">Eau & Assainissement</h3>
                    <p class="domain-description">
                        Traitement de l'eau potable, assainissement et promotion 
                        de l'hygiène communautaire.
                    </p>
                </a>

                {{-- Domaine 5 --}}
                <a href="{{ url('/projets?domaine=securite_alimentaire') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-seedling"></i></div>
                    <h3 class="domain-title">Sécurité Alimentaire</h3>
                    <p class="domain-description">
                        Soutien à la production agricole, amélioration des moyens 
                        de subsistance des ménages.
                    </p>
                </a>

                {{-- Domaine 6 --}}
                <a href="{{ url('/projets?domaine=communication') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-bullhorn"></i></div>
                    <h3 class="domain-title">Communication</h3>
                    <p class="domain-description">
                        Information, sensibilisation et mobilisation communautaire 
                        pour le changement social.
                    </p>
                </a>

                {{-- Domaine 7 --}}
                <a href="{{ url('/projets?domaine=environnement') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-tree"></i></div>
                    <h3 class="domain-title">Environnement</h3>
                    <p class="domain-description">
                        Lutte contre la déforestation, reboisement et éducation 
                        environnementale.
                    </p>
                </a>

                {{-- Domaine 8 --}}
                <a href="{{ url('/projets?domaine=paix') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-dove"></i></div>
                    <h3 class="domain-title">Paix & Cohésion</h3>
                    <p class="domain-description">
                        Transformation positive des conflits, gouvernance foncière 
                        et consolidation de la paix.
                    </p>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
{{-- SECTION PROJETS RÉCENTS --}}
{{-- ============================================ --}}
<section class="projects-section" id="projects">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Nos actions</span>
            <h2 class="section-title">Projets en cours et réalisés</h2>
            <p class="section-description">
                Découvrez les projets que nous menons sur le terrain pour améliorer 
                les conditions de vie des communautés.
            </p>
        </div>

        <div class="projects-grid">
            {{-- Projet 1 : En cours --}}
            <article class="project-card">
                <div class="project-card-image">
                    <img src="{{ asset('assets/images/slide2.jpg') }}" 
                         alt="Production maraîchère à Minova" 
                         loading="lazy">
                    <span class="project-card-badge en-cours">
                        <i class="fas fa-spinner fa-pulse"></i> En cours
                    </span>
                </div>
                <div class="project-card-body">
                    <span class="project-card-domain">Sécurité Alimentaire</span>
                    <h3 class="project-card-title">
                        <a href="{{ url('/projets/soutien-production-maraichere') }}">
                            Soutien à la production maraîchère pour la sécurité alimentaire
                        </a>
                    </h3>
                    <div class="project-card-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Minova & Kirotshe</span>
                        <span><i class="fas fa-calendar-alt"></i> 2026</span>
                    </div>
                    <div class="project-card-progress">
                        <div class="progress-label">
                            <span>Progression</span>
                            <span>50%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="50%" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="project-card-footer">
                        <a href="{{ url('/projets/soutien-production-maraichere') }}" class="project-card-link">
                            Lire la suite <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>

            {{-- Projet 2 : En cours --}}
            <article class="project-card">
                <div class="project-card-image">
                    <img src="{{ asset('assets/images/slide1.jpg') }}" 
                         alt="Sensibilisation sanitaire" 
                         loading="lazy">
                    <span class="project-card-badge en-cours">
                        <i class="fas fa-spinner fa-pulse"></i> En cours
                    </span>
                </div>
                <div class="project-card-body">
                    <span class="project-card-domain">Santé-Nutrition</span>
                    <h3 class="project-card-title">
                        <a href="{{ url('/projets/sensibilisation-cholera-ebola') }}">
                            Sensibilisation contre le choléra et la maladie à virus Ebola
                        </a>
                    </h3>
                    <div class="project-card-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Minova & Kirotshe</span>
                        <span><i class="fas fa-calendar-alt"></i> 2026</span>
                    </div>
                    <div class="project-card-progress">
                        <div class="progress-label">
                            <span>Progression</span>
                            <span>40%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="40%" style="width: 40%"></div>
                        </div>
                    </div>
                    <div class="project-card-footer">
                        <a href="{{ url('/projets/sensibilisation-cholera-ebola') }}" class="project-card-link">
                            Lire la suite <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>

            {{-- Projet 3 : Terminé --}}
            <article class="project-card">
                <div class="project-card-image">
                    <img src="{{ asset('assets/images/slide3.jpg') }}" 
                         alt="Hygiène communautaire à Minova" 
                         loading="lazy">
                    <span class="project-card-badge termine">
                        <i class="fas fa-check-circle"></i> Terminé
                    </span>
                </div>
                <div class="project-card-body">
                    <span class="project-card-domain">Eau, Hygiène & Assainissement</span>
                    <h3 class="project-card-title">
                        <a href="{{ url('/projets/promotion-hygiene-communautaire') }}">
                            Promotion de l'hygiène communautaire dans la zone de Minova
                        </a>
                    </h3>
                    <div class="project-card-meta">
                        <span><i class="fas fa-map-marker-alt"></i> Minova</span>
                        <span><i class="fas fa-calendar-alt"></i> 2024</span>
                    </div>
                    <div class="project-card-progress">
                        <div class="progress-label">
                            <span>Réalisé</span>
                            <span>100%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill completed" data-width="100%" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="project-card-footer">
                        <a href="{{ url('/projets/promotion-hygiene-communautaire') }}" class="project-card-link">
                            Lire la suite <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </article>
        </div>

        <div class="section-footer">
            <a href="{{ url('/projets') }}" class="btn btn-outline">
                <i class="fas fa-list-ul"></i> Voir tous les projets
            </a>
        </div>
    </div>
</section>

    {{-- ============================================ --}}
    {{-- SECTION TÉMOIGNAGES --}}
    {{-- ============================================ --}}
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ils témoignent</span>
                <h2 class="section-title">L'impact de nos actions</h2>
                <p class="section-description">
                    Les voix de ceux qui ont bénéficié de l'accompagnement d'AHD-RDC 
                    dans leur communauté.
                </p>
            </div>

            <div class="testimonials-grid">
                {{-- Témoignage 1 --}}
                <blockquote class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <p class="testimonial-text">
                        Grâce aux formations d'AHD-RDC sur la résolution pacifique des conflits, 
                        notre communauté a retrouvé la paix. Aujourd'hui, les différends se règlent 
                        par le dialogue et non par la violence.
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">JM</div>
                        <div class="testimonial-info">
                            <span class="testimonial-name">Jean M.</span>
                            <span class="testimonial-role">Chef de village, Territoire de Kalehe</span>
                        </div>
                    </div>
                </blockquote>

                {{-- Témoignage 2 --}}
                <blockquote class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <p class="testimonial-text">
                        La formation en maraîchage a changé ma vie. Je cultive des légumes que je 
                        vends au marché. Mes enfants mangent à leur faim et je peux payer leurs 
                        frais de scolarité.
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">MA</div>
                        <div class="testimonial-info">
                            <span class="testimonial-name">Marie A.</span>
                            <span class="testimonial-role">Bénéficiaire, Minova</span>
                        </div>
                    </div>
                </blockquote>

                {{-- Témoignage 3 --}}
                <blockquote class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <p class="testimonial-text">
                        Quand le choléra a frappé notre zone, les équipes d'AHD-RDC étaient là. 
                        Les séances de sensibilisation nous ont appris les gestes qui sauvent. 
                        Nous n'avons pas perdu un seul enfant.
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">PK</div>
                        <div class="testimonial-info">
                            <span class="testimonial-name">Patrick K.</span>
                            <span class="testimonial-role">Relais communautaire, Kirotshe</span>
                        </div>
                    </div>
                </blockquote>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION PARTENAIRES --}}
    {{-- ============================================ --}}
    <section class="partners-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ils nous font confiance</span>
                <h2 class="section-title">Nos partenaires</h2>
                <p class="section-description">
                    Organisations et institutions qui soutiennent notre mission humanitaire.
                </p>
            </div>

            <div class="partners-grid">
                <div class="partner-item">
                    <div class="partner-placeholder">
                        <i class="fas fa-building"></i>
                        <span>DMP-RDC</span>
                    </div>
                </div>
                <div class="partner-item">
                    <div class="partner-placeholder">
                        <i class="fas fa-users"></i>
                        <span>CA AHD-RDC</span>
                    </div>
                </div>
                <div class="partner-item">
                    <div class="partner-placeholder partner-become">
                        <i class="fas fa-handshake"></i>
                        <span>Devenir partenaire</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION CTA — APPEL À L'ACTION --}}
    {{-- ============================================ --}}
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">
                    Vous aussi, <span class="highlight">faites la différence</span>
                </h2>
                <p class="cta-text">
                    Chaque don, chaque adhésion, chaque action compte. Rejoignez-nous dans notre 
                    mission de développement communautaire, de protection des personnes vulnérables 
                    et de consolidation de la paix en République Démocratique du Congo.
                </p>
                <div class="cta-buttons">
                    <a href="{{ url('/faire-un-don') }}" class="btn btn-accent btn-lg">
                        <i class="fas fa-heart"></i> Faire un don maintenant
                    </a>
                    <a href="{{ url('/devenir-membre') }}" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-user-plus"></i> Devenir membre
                    </a>
                    <a href="{{ url('/contact') }}" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-home.js') }}"></script>
@endsection