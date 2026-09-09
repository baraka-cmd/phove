@extends('layouts.public')

@section('title', 'Accueil')

@section('meta_description', 'PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies. ONG humanitaire congolaise créée en 2012 à Goma. Compassion avec la population.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-home.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- HERO SECTION — SLIDER --}}
    {{-- ============================================ --}}
    <section class="hero" id="hero">
        {{-- Slides --}}
        <div class="hero-slider">
            <div class="hero-slide active" style="background-image: url('{{ asset('assets/images/slide1.jpg') }}');">
                <div class="hero-overlay"></div>
            </div>
            <div class="hero-slide" style="background-image: url('{{ asset('assets/images/slide2.jpg') }}');">
                <div class="hero-overlay"></div>
            </div>
            <div class="hero-slide" style="background-image: url('{{ asset('assets/images/slide3.jpg') }}');">
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
                    <i class="fas fa-dot-circle"></i> Depuis 2012 — Goma, Nord-Kivu, RD Congo
                </span>
                <h1 class="hero-title">
                    <span class="highlight">Compassion</span> avec la population
                </h1>
                <p class="hero-description">
                    PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies — 
                    vient en aide aux plus vulnérables : éducation, santé, agriculture, formation 
                    professionnelle, assistance sociale et spirituelle.
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
                        <span class="counter-value" data-target="4">0</span>
                    </div>
                    <div class="counter-label">Projets humanitaires</div>
                    <p class="counter-desc">En cours et réalisés</p>
                </div>

                {{-- Compteur 2 : Bénéficiaires --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="5000">0</span>
                        <span class="counter-plus">+</span>
                    </div>
                    <div class="counter-label">Bénéficiaires atteints</div>
                    <p class="counter-desc">Orphelins, veuves, personnes démunies</p>
                </div>

                {{-- Compteur 3 : Domaines --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="9">0</span>
                    </div>
                    <div class="counter-label">Domaines d'intervention</div>
                    <p class="counter-desc">Spirituel, social, éducatif, sanitaire</p>
                </div>

                {{-- Compteur 4 : Années --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="{{ date('Y') - 2012 }}">0</span>
                        <span class="counter-plus">+</span>
                    </div>
                    <div class="counter-label">Années d'expérience</div>
                    <p class="counter-desc">Au service des plus démunis</p>
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
                    <img src="{{ asset('assets/images/about/about-phove.jpg') }}" 
                         alt="PHOVE — Action humanitaire auprès des orphelins et veuves" 
                         class="about-image"
                         onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600'">
                    <div class="about-image-badge">
                        <span class="badge-number">{{ date('Y') - 2012 }}+</span>
                        <span class="badge-text">Années d'action<br>humanitaire</span>
                    </div>
                </div>

                {{-- Contenu --}}
                <div class="about-content">
                    <span class="section-subtitle">Qui sommes-nous ?</span>
                    <h2 class="section-title">
                        Une organisation au service des orphelins, veuves et personnes démunies
                    </h2>

                    <p class="about-text">
                        <strong>PHOVE (Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies)</strong> 
                        est une organisation non gouvernementale apolitique à caractère social et spirituel, 
                        créée le <strong>30 août 2012</strong> à Goma, dans la province du Nord-Kivu, 
                        en République Démocratique du Congo.
                    </p>

                    <p class="about-text">
                        Fondée par l'<strong>Apôtre Philippe KABOYI MUSAMUGUMA</strong> après 40 jours 
                        de jeûne et de prière sur la montagne KALAGANE à Bukavu, PHOVE est née d'une 
                        inspiration divine reçue le <strong>20 avril 2012</strong>. Sa mission : 
                        venir en aide aux personnes vulnérables — orphelins, veuves, personnes de 
                        3ème âge et personnes démunies.
                    </p>

                    <div class="about-values-list">
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
                                <strong>Solidarité</strong>
                                <span>Ensemble pour le bien-être de tous</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Foi en Dieu</strong>
                                <span>Guidés par les Saintes Écritures</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Intégrité</strong>
                                <span>Transparence et redevabilité</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Charité</strong>
                                <span>Amour du prochain selon Romain 13:8</span>
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
                    Conformément aux articles 8 et 9 de nos statuts, PHOVE intervient dans 
                    les domaines suivants pour répondre aux besoins essentiels des personnes 
                    vulnérables.
                </p>
            </div>

            <div class="domains-grid">
                {{-- Domaine 1 --}}
                <a href="{{ url('/projets?domaine=sante_nutrition') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-heartbeat"></i></div>
                    <h3 class="domain-title">Santé & Nutrition</h3>
                    <p class="domain-description">
                        Soins médicaux, alimentation, lutte contre la malnutrition 
                        et accompagnement des malades traumatisés.
                    </p>
                </a>

                {{-- Domaine 2 --}}
                <a href="{{ url('/projets?domaine=education') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3 class="domain-title">Éducation & Formation</h3>
                    <p class="domain-description">
                        Scolarisation des orphelins, alphabétisation, formation 
                        professionnelle : coupe couture, informatique, menuiserie.
                    </p>
                </a>

                {{-- Domaine 3 --}}
                <a href="{{ url('/projets?domaine=protection') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="domain-title">Protection Sociale</h3>
                    <p class="domain-description">
                        Assistance aux veuves et orphelins, lutte contre les violences, 
                        assistance judiciaire et accompagnement psychosocial.
                    </p>
                </a>

                {{-- Domaine 4 --}}
                <a href="{{ url('/projets?domaine=securite_alimentaire') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-seedling"></i></div>
                    <h3 class="domain-title">Agriculture & Élevage</h3>
                    <p class="domain-description">
                        Soutien à l'agriculture, l'élevage et la pêche. Ouverture 
                        et réhabilitation des routes de desserte agricole.
                    </p>
                </a>

                {{-- Domaine 5 --}}
                <a href="{{ url('/projets?domaine=wash') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-tint"></i></div>
                    <h3 class="domain-title">Eau & Assainissement</h3>
                    <p class="domain-description">
                        Accès à l'eau potable, hygiène communautaire et 
                        assainissement des milieux.
                    </p>
                </a>

                {{-- Domaine 6 --}}
                <a href="{{ url('/projets?domaine=spirituel') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-dove"></i></div>
                    <h3 class="domain-title">Accompagnement Spirituel</h3>
                    <p class="domain-description">
                        Encadrement spirituel des fidèles, formation biblique, 
                        soutien moral et préparation spirituelle.
                    </p>
                </a>

                {{-- Domaine 7 --}}
                <a href="{{ url('/projets?domaine=infrastructure') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-building"></i></div>
                    <h3 class="domain-title">Infrastructures</h3>
                    <p class="domain-description">
                        Construction et réhabilitation des infrastructures 
                        hospitalières, scolaires et dépôts agricoles.
                    </p>
                </a>

                {{-- Domaine 8 --}}
                <a href="{{ url('/projets?domaine=economique') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <h3 class="domain-title">Activités Économiques</h3>
                    <p class="domain-description">
                        Accompagnement des activités socio-économiques, 
                        lutte contre l'oisiveté juvénile et le vagabondage.
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
                    les conditions de vie des orphelins, veuves et personnes démunies.
                </p>
            </div>

            <div class="projects-grid">
                {{-- Projet 1 : En cours --}}
                <article class="project-card">
                    <div class="project-card-image">
                        <img src="{{ asset('assets/images/slide2.jpg') }}" 
                             alt="Assistance alimentaire à Muja" 
                             loading="lazy">
                        <span class="project-card-badge en-cours">
                            <i class="fas fa-spinner fa-pulse"></i> En cours
                        </span>
                    </div>
                    <div class="project-card-body">
                        <span class="project-card-domain">Sécurité Alimentaire</span>
                        <h3 class="project-card-title">
                            <a href="{{ url('/projets/assistance-alimentaire-muja-fao') }}">
                                Assistance alimentaire aux orphelins, veuves et personnes démunies du village Muja
                            </a>
                        </h3>
                        <div class="project-card-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Muja, Nyiragongo</span>
                            <span><i class="fas fa-calendar-alt"></i> 2025</span>
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
                            <a href="{{ url('/projets/assistance-alimentaire-muja-fao') }}" class="project-card-link">
                                Lire la suite <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>

                {{-- Projet 2 : En cours --}}
                <article class="project-card">
                    <div class="project-card-image">
                        <img src="{{ asset('assets/images/slide1.jpg') }}" 
                             alt="Projet de résilience WFP à Nyiragongo" 
                             loading="lazy">
                        <span class="project-card-badge en-cours">
                            <i class="fas fa-spinner fa-pulse"></i> En cours
                        </span>
                    </div>
                    <div class="project-card-body">
                        <span class="project-card-domain">Résilience & Développement Rural</span>
                        <h3 class="project-card-title">
                            <a href="{{ url('/projets/projet-resilience-unjp-rdc-082-wfp') }}">
                                Projet de résilience UNJP/RDC/082/WFP — Distribution de semences vivrières et maraîchères
                            </a>
                        </h3>
                        <div class="project-card-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Nyiragongo, Nord-Kivu</span>
                            <span><i class="fas fa-calendar-alt"></i> 2025</span>
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
                            <a href="{{ url('/projets/projet-resilience-unjp-rdc-082-wfp') }}" class="project-card-link">
                                Lire la suite <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>

                {{-- Projet 3 : En cours --}}
                <article class="project-card">
                    <div class="project-card-image">
                        <img src="{{ asset('assets/images/slide3.jpg') }}" 
                             alt="Appui nutritionnel aux orphelins de Kiziba" 
                             loading="lazy">
                        <span class="project-card-badge en-cours">
                            <i class="fas fa-spinner fa-pulse"></i> En cours
                        </span>
                    </div>
                    <div class="project-card-body">
                        <span class="project-card-domain">Santé & Nutrition</span>
                        <h3 class="project-card-title">
                            <a href="{{ url('/projets/appui-nutritionnel-orphelins-kiziba') }}">
                                Appui nutritionnel aux orphelins du village Kiziba I
                            </a>
                        </h3>
                        <div class="project-card-meta">
                            <span><i class="fas fa-map-marker-alt"></i> Kiziba I, Nyiragongo</span>
                            <span><i class="fas fa-calendar-alt"></i> 2025</span>
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
                            <a href="{{ url('/projets/appui-nutritionnel-orphelins-kiziba') }}" class="project-card-link">
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
                    Les voix de ceux qui ont bénéficié de l'accompagnement de PHOVE 
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
                        Grâce à PHOVE, mes enfants mangent à leur faim et vont à l'école. 
                        Les vivres et le soutien que nous recevons nous redonnent espoir 
                        chaque jour. Que Dieu bénisse cette organisation.
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">JM</div>
                        <div class="testimonial-info">
                            <span class="testimonial-name">Jeanne M.</span>
                            <span class="testimonial-role">Veuve bénéficiaire, Muja</span>
                        </div>
                    </div>
                </blockquote>

                {{-- Témoignage 2 --}}
                <blockquote class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <p class="testimonial-text">
                        Les semences et outils aratoires distribués par PHOVE et le PAM 
                        m'ont permis de cultiver mon champ. Aujourd'hui, je récolte assez 
                        pour nourrir ma famille et vendre le surplus au marché.
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">PK</div>
                        <div class="testimonial-info">
                            <span class="testimonial-name">Patrick K.</span>
                            <span class="testimonial-role">Agriculteur bénéficiaire, Nyiragongo</span>
                        </div>
                    </div>
                </blockquote>

                {{-- Témoignage 3 --}}
                <blockquote class="testimonial-card">
                    <div class="testimonial-quote">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <p class="testimonial-text">
                        L'atelier de formation des agents humanitaires m'a donné des 
                        compétences précieuses. Maintenant, je peux mieux servir les 
                        orphelins et les personnes vulnérables de ma communauté.
                    </p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">AL</div>
                        <div class="testimonial-info">
                            <span class="testimonial-name">Aline L.</span>
                            <span class="testimonial-role">Agente humanitaire formée, Goma</span>
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
                <h2 class="section-title">Nos partenaires internationaux</h2>
                <p class="section-description">
                    Organisations internationales et institutions qui soutiennent notre mission humanitaire.
                </p>
            </div>

            <div class="partners-grid">
                <div class="partner-item">
                    <div class="partner-placeholder">
                        <i class="fas fa-seedling"></i>
                        <span>FAO</span>
                    </div>
                </div>
                <div class="partner-item">
                    <div class="partner-placeholder">
                        <i class="fas fa-wheat-awn"></i>
                        <span>WFP / PAM</span>
                    </div>
                </div>
                <div class="partner-item">
                    <div class="partner-placeholder">
                        <i class="fas fa-globe"></i>
                        <span>OCHA</span>
                    </div>
                </div>
                <div class="partner-item">
                    <div class="partner-placeholder">
                        <i class="fas fa-graduation-cap"></i>
                        <span>UNESCO</span>
                    </div>
                </div>
                <div class="partner-item">
                    <div class="partner-placeholder">
                        <i class="fas fa-users"></i>
                        <span>GPE</span>
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
                    mission d'assistance aux orphelins, veuves et personnes démunies. 
                    « Aime ton prochain comme toi-même » — Romain 13:8.
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