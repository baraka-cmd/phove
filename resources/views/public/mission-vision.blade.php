@extends('layouts.public')

@section('title', 'Mission & Vision')

@section('meta_description', 'Découvrez la mission, la vision et les objectifs de PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies. « Aime ton prochain comme toi-même » — Romain 13:8.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/mission-vision.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-bullseye"></i>
            </div>
            <h1 class="page-header-title">Mission & Vision</h1>
            <p class="page-header-subtitle">
                Ce qui nous guide et nous inspire dans notre action humanitaire quotidienne au service des orphelins, veuves et personnes démunies.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ url('/qui-sommes-nous') }}">Qui sommes-nous ?</a>
                <i class="fas fa-chevron-right"></i>
                <span>Mission & Vision</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 1 : NOTRE MISSION — Article 5 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="content-grid">
                {{-- Texte --}}
                <div class="content-block">
                    <span class="section-subtitle">Notre raison d'être</span>
                    <h2 class="section-title text-left">Notre Mission</h2>

                    <div class="quote-block vision-quote">
                        <p>
                            « Aime ton prochain comme toi-même » — Romain 13:8
                        </p>
                    </div>

                    <p class="content-block-text">
                        {{ (data_get($siteSettings ?? [], 'page_mission_content') ?: data_get($siteSettings ?? [], 'page_mission_vision') ?: 'Conformément à l’article 5 des statuts, PHOVE a pour but de venir en aide aux personnes vulnérables : orphelins, veuves, personnes âgées et personnes démunies. Notre action s’appuie sur l’amour du prochain et l’accompagnement spirituel et social.') }}
                    </p>
                </div>

                {{-- Image --}}
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/about/about-phove.jpg') }}" 
                         alt="Mission PHOVE — Compassion avec la population" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600'">
                    <div class="content-image-badge">
                        <strong>Romain 13:8</strong><br>
                        <small>Aimez-vous les uns les autres</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : NOTRE VISION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ce qui nous inspire</span>
                <h2 class="section-title">Notre Vision</h2>
                <p class="section-description">
                    {{ (data_get($siteSettings ?? [], 'page_vision_content') ?: 'PHOVE aspire à un monde où chaque personne vulnérable est accompagnée, protégée et soutenue dans sa dignité.') }}
                </p>
            </div>

            <div class="cards-grid">
                {{-- Vision 1 --}}
                <div class="mission-card">
                    <div class="mission-card-number">01</div>
                    <div class="mission-card-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3 class="mission-card-title">
                        Des orphelins encadrés et épanouis
                    </h3>
                    <p class="mission-card-text">
                        Nous œuvrons pour que chaque orphelin ait accès à l'éducation, 
                        à l'alimentation, aux soins de santé et à un encadrement spirituel 
                        et social qui lui permette de devenir un bon citoyen.
                    </p>
                </div>

                {{-- Vision 2 --}}
                <div class="mission-card">
                    <div class="mission-card-number">02</div>
                    <div class="mission-card-icon">
                        <i class="fas fa-female"></i>
                    </div>
                    <h3 class="mission-card-title">
                        Des veuves soutenues et autonomes
                    </h3>
                    <p class="mission-card-text">
                        Nous accompagnons les veuves dans leur réinsertion socio-économique 
                        par la formation professionnelle, les activités génératrices de revenus 
                        et le soutien spirituel.
                    </p>
                </div>

                {{-- Vision 3 --}}
                <div class="mission-card">
                    <div class="mission-card-number">03</div>
                    <div class="mission-card-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3 class="mission-card-title">
                        Des personnes démunies aidées et dignes
                    </h3>
                    <p class="mission-card-text">
                        Nous assistons les personnes démunies par l'alimentation, l'habillement, 
                        les soins médicaux, le logement et l'accompagnement dans leurs activités 
                        socio-économiques.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : OBJECTIFS SPÉCIFIQUES — Article 7 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Nos engagements</span>
                <h2 class="section-title">Objectifs Spécifiques</h2>
                <p class="section-description">
                    Conformément à <strong>l'article 7 des statuts</strong>, PHOVE poursuit 
                    les objectifs spécifiques suivants.
                </p>
            </div>

            <div class="objectives-grid">
                {{-- Objectif 1 --}}
                <div class="objective-item">
                    <div class="objective-number">1</div>
                    <div class="objective-content">
                        <h4>Mettre en valeur les Saintes Écritures</h4>
                        <p>
                            Promouvoir la parole de Dieu et donner la valeur à notre vocation 
                            pastorale dans toutes nos actions.
                        </p>
                    </div>
                </div>

                {{-- Objectif 2 --}}
                <div class="objective-item">
                    <div class="objective-number">2</div>
                    <div class="objective-content">
                        <h4>Lutter contre les fléaux sociaux</h4>
                        <p>
                            Lutter contre le banditisme, le vol, le viol, les avortements forcés, 
                            les mariages précoces, le vagabondage, la pauvreté et l'oisiveté juvénile.
                        </p>
                    </div>
                </div>

                {{-- Objectif 3 --}}
                <div class="objective-item">
                    <div class="objective-number">3</div>
                    <div class="objective-content">
                        <h4>Encadrer et accompagner les personnes vulnérables</h4>
                        <p>
                            Encadrer et accompagner les orphelins, veuves et personnes démunies 
                            dans les domaines spirituel et social : éducation, agriculture, pêche, 
                            élevage, menuiserie, maçonnerie, mécanique, coupe et couture, art, 
                            esthétique, art culinaire, activités socio-économiques et sanitaires.
                        </p>
                    </div>
                </div>

                {{-- Objectif 4 --}}
                <div class="objective-item">
                    <div class="objective-number">4</div>
                    <div class="objective-content">
                        <h4>Assurer les besoins essentiels</h4>
                        <p>
                            Assurer l'alimentation, les soins médicaux, l'habillement et le logement 
                            des personnes vulnérables.
                        </p>
                    </div>
                </div>

                {{-- Objectif 5 --}}
                <div class="objective-item">
                    <div class="objective-number">5</div>
                    <div class="objective-content">
                        <h4>Susciter l'amour du prochain</h4>
                        <p>
                            Susciter l'amour du prochain et la protection des biens communs 
                            par l'assistance aux plus démunis.
                        </p>
                    </div>
                </div>

                {{-- Objectif 6 --}}
                <div class="objective-item">
                    <div class="objective-number">6</div>
                    <div class="objective-content">
                        <h4>Accompagner les activités socio-économiques</h4>
                        <p>
                            Accompagner les activités socio-économiques des personnes vulnérables 
                            pour leur autonomisation.
                        </p>
                    </div>
                </div>

                {{-- Objectif 7 --}}
                <div class="objective-item">
                    <div class="objective-number">7</div>
                    <div class="objective-content">
                        <h4>Assurer l'assistance judiciaire</h4>
                        <p>
                            Accompagner les délaissés juridiquement et assurer leur défense 
                            et leur protection.
                        </p>
                    </div>
                </div>

                {{-- Objectif 8 --}}
                <div class="objective-item">
                    <div class="objective-number">8</div>
                    <div class="objective-content">
                        <h4>Assurer la scolarisation et la formation</h4>
                        <p>
                            Assurer la scolarisation des orphelins, l'alphabétisation et la 
                            formation professionnelle des personnes vulnérables.
                        </p>
                    </div>
                </div>

                {{-- Objectif 9 --}}
                <div class="objective-item">
                    <div class="objective-number">9</div>
                    <div class="objective-content">
                        <h4>Accompagner les malades traumatisés</h4>
                        <p>
                            Accompagner les malades traumatisés pour lutter contre la stigmatisation 
                            et la discrimination.
                        </p>
                    </div>
                </div>

                {{-- Objectif 10 --}}
                <div class="objective-item">
                    <div class="objective-number">10</div>
                    <div class="objective-content">
                        <h4>Soutenir l'agriculture et les infrastructures</h4>
                        <p>
                            Soutenir l'agriculture, l'élevage et la pêche. Ouvrir, tracer et 
                            réfectionner les routes de desserte agricole. Construire et réhabiliter 
                            les infrastructures hospitalières, scolaires et les dépôts de productions agricoles.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4 : DOMAINES D'INTERVENTION — Articles 8 et 9 --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre champ d'action</span>
                <h2 class="section-title">Domaines d'Intervention</h2>
                <p class="section-description">
                    Conformément aux articles 8 et 9 des statuts, PHOVE intervient dans 
                    l'encadrement des personnes vulnérables.
                </p>
            </div>

            <div class="cards-grid cards-grid-4">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3 class="info-card-title">Logements</h3>
                    <p class="info-card-text">
                        Octroi de logements aux personnes vulnérables.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="info-card-title">Scolarisation</h3>
                    <p class="info-card-text">
                        Scolarisation des orphelins et alphabétisation.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="info-card-title">Santé & Alimentation</h3>
                    <p class="info-card-text">
                        Alimentation, habillement et soins médicaux.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-tractor"></i>
                    </div>
                    <h3 class="info-card-title">Agriculture</h3>
                    <p class="info-card-text">
                        Agriculture, élevage, pêche et routes de desserte agricole.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <h3 class="info-card-title">Infrastructures</h3>
                    <p class="info-card-text">
                        Construction et réhabilitation des hôpitaux, écoles et dépôts.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3 class="info-card-title">Socio-économique</h3>
                    <p class="info-card-text">
                        Activités socio-économiques et assistance judiciaire.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-dove"></i>
                    </div>
                    <h3 class="info-card-title">Spirituel</h3>
                    <p class="info-card-text">
                        Vie spirituelle, formation biblique et préparation spirituelle.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="info-card-title">Psychosocial</h3>
                    <p class="info-card-text">
                        Accompagnement des malades traumatisés et lutte contre la stigmatisation.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 5 : NOTRE DEVISE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-primary">
        <div class="container text-center">
            <div class="slogan-section">
                <i class="fas fa-quote-left slogan-quote"></i>
                <h2 class="slogan-text">
                    Compassion avec la population
                </h2>
                <i class="fas fa-quote-right slogan-quote"></i>
                <p class="slogan-subtext">
                    Notre devise incarne notre philosophie d'action : la compassion, 
                    l'amour du prochain et l'engagement collectif comme moteurs de 
                    notre mission humanitaire.
                </p>
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
                    Partagez <span class="highlight-page">notre mission</span>
                </h2>
                <p class="cta-text-page">
                    Vous croyez en un monde où chaque orphelin, chaque veuve et chaque personne 
                    démunie vit dans la dignité ? Rejoignez-nous et contribuez à faire de 
                    cette vision une réalité.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/faire-un-don') }}" class="btn-page-don">
                        <i class="fas fa-heart"></i> Soutenir notre mission
                    </a>
                    <a href="{{ url('/nos-valeurs') }}" class="btn-page-outline">
                        <i class="fas fa-gem"></i> Découvrir nos valeurs
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/mission-vision.js') }}"></script>
@endsection