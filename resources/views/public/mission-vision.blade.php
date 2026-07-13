@extends('layouts.public')

@section('title', 'Mission & Vision')

@section('meta_description', 'Découvrez la mission, la vision et les objectifs d\'AHD-RDC pour le développement communautaire, la paix et la protection des droits humains en République Démocratique du Congo.')

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
                Ce qui nous guide et nous inspire dans notre action humanitaire quotidienne au service des communautés vulnérables.
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
    {{-- SECTION 1 : NOTRE VISION — Article 7 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="content-grid">
                {{-- Texte --}}
                <div class="content-block">
                    <span class="section-subtitle">Ce qui nous inspire</span>
                    <h2 class="section-title text-left">Notre Vision</h2>

                    <div class="quote-block vision-quote">
                        <p>
                            Vivre une société pacifiée sans conflits communautaires violents, 
                            où règne l'équilibre social et où les droits des femmes sont respectés, 
                            mais aussi dans une communauté autonome et en bonne santé.
                        </p>
                    </div>

                    <p class="content-block-text">
                        Cette vision, définie à <strong>l'article 7 de nos statuts</strong>, 
                        reflète notre aspiration profonde : bâtir un Congo où chaque communauté 
                        vit en paix, dans la dignité et l'autonomie. Nous croyons en une société 
                        où les conflits se résolvent par le dialogue, où les femmes sont pleinement 
                        actrices du développement, et où chaque personne a accès aux services 
                        essentiels pour une vie saine et épanouie.
                    </p>
                </div>

                {{-- Image --}}
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Vision AHD-RDC — Paix et développement" 
                         class="content-image">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : NOTRE MISSION — Article 8 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre raison d'être</span>
                <h2 class="section-title">Notre Mission</h2>
                <p class="section-description">
                    Définie à l'article 8 de nos statuts, notre mission s'articule autour de trois axes fondamentaux.
                </p>
            </div>

            <div class="cards-grid">
                {{-- Mission 1 --}}
                <div class="mission-card">
                    <div class="mission-card-number">01</div>
                    <div class="mission-card-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="mission-card-title">
                        Renforcer les mécanismes de prévention et de transformation positive des conflits
                    </h3>
                    <p class="mission-card-text">
                        Nous accompagnons les communautés dans la gestion pacifique des différends 
                        à travers des mécanismes communautaires de médiation, de dialogue et de 
                        réconciliation. Nous promouvons la cohabitation pacifique et la bonne 
                        gouvernance comme piliers d'une paix durable.
                    </p>
                </div>

                {{-- Mission 2 --}}
                <div class="mission-card">
                    <div class="mission-card-number">02</div>
                    <div class="mission-card-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3 class="mission-card-title">
                        Assurer un plaidoyer et une sensibilisation régulière
                    </h3>
                    <p class="mission-card-text">
                        Nous menons un plaidoyer constant auprès des autorités politico-administratives 
                        pour la bonne gouvernance, la sécurisation foncière, la promotion des droits 
                        des femmes et la participation citoyenne. Nous informons et sensibilisons les 
                        populations sur leurs droits fondamentaux.
                    </p>
                </div>

                {{-- Mission 3 --}}
                <div class="mission-card">
                    <div class="mission-card-number">03</div>
                    <div class="mission-card-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3 class="mission-card-title">
                        Informer, former et appuyer dans les techniques de production et d'auto prise en charge
                    </h3>
                    <p class="mission-card-text">
                        Nous renforçons les capacités des communautés par la formation professionnelle, 
                        l'apprentissage des métiers et l'accompagnement aux initiatives de microfinance. 
                        Notre objectif est l'autonomisation économique des personnes vulnérables.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : OBJECTIF GLOBAL — Article 5 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="content-block text-center">
                <span class="section-subtitle">Notre but ultime</span>
                <h2 class="section-title">Objectif Global</h2>

                <div class="objective-global-card">
                    <div class="objective-global-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <p class="objective-global-text">
                        Contribuer à la <strong>résolution des conflits fonciers</strong> et aux 
                        <strong>renforcements des mécanismes de la consolidation de la paix</strong> 
                        entre les communautés, au <strong>relèvement communautaire</strong> et à la 
                        <strong>promotion des droits de la femme</strong> en République Démocratique 
                        du Congo en particulier et dans le monde en général.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4 : OBJECTIFS SPÉCIFIQUES — Article 6 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Nos engagements</span>
                <h2 class="section-title">Objectifs Spécifiques</h2>
                <p class="section-description">
                    Conformément à l'article 6 de nos statuts, nous poursuivons dix objectifs 
                    spécifiques pour atteindre notre mission.
                </p>
            </div>

            <div class="objectives-grid">
                {{-- Objectif 1 --}}
                <div class="objective-item">
                    <div class="objective-number">1</div>
                    <div class="objective-content">
                        <h4>Gestion et transformation positive des conflits</h4>
                        <p>
                            Accompagner les communautés dans la gestion et la transformation 
                            positives des conflits à travers des mécanismes communautaires, la 
                            promotion de la paix, la cohabitation pacifique et la bonne gouvernance.
                        </p>
                    </div>
                </div>

                {{-- Objectif 2 --}}
                <div class="objective-item">
                    <div class="objective-number">2</div>
                    <div class="objective-content">
                        <h4>Gouvernance foncière</h4>
                        <p>
                            Informer et former les communautés sur la gouvernance foncière, 
                            la sécurisation de la terre, l'accès à la terre et à la propriété.
                        </p>
                    </div>
                </div>

                {{-- Objectif 3 --}}
                <div class="objective-item">
                    <div class="objective-number">3</div>
                    <div class="objective-content">
                        <h4>Amélioration de l'habitat</h4>
                        <p>
                            Améliorer les conditions et l'accès à l'habitat pour les 
                            communautés vulnérables.
                        </p>
                    </div>
                </div>

                {{-- Objectif 4 --}}
                <div class="objective-item">
                    <div class="objective-number">4</div>
                    <div class="objective-content">
                        <h4>Promotion des droits humains et des enfants</h4>
                        <p>
                            Sensibiliser, informer et former les leaders et la population, 
                            et mener un plaidoyer auprès des gouvernants pour la promotion 
                            des droits humains et des enfants.
                        </p>
                    </div>
                </div>

                {{-- Objectif 5 --}}
                <div class="objective-item">
                    <div class="objective-number">5</div>
                    <div class="objective-content">
                        <h4>Droits des réfugiés et déplacés</h4>
                        <p>
                            Mener un plaidoyer en faveur des droits des réfugiés, rapatriés, 
                            demandeurs d'asile, retournés et déplacés internes.
                        </p>
                    </div>
                </div>

                {{-- Objectif 6 --}}
                <div class="objective-item">
                    <div class="objective-number">6</div>
                    <div class="objective-content">
                        <h4>Sécurité des moyens de subsistance</h4>
                        <p>
                            Améliorer la sécurité des moyens de subsistance des communautés rurales.
                        </p>
                    </div>
                </div>

                {{-- Objectif 7 --}}
                <div class="objective-item">
                    <div class="objective-number">7</div>
                    <div class="objective-content">
                        <h4>Santé familiale et lutte contre les MST/VIH-SIDA</h4>
                        <p>
                            Éduquer la population sur la santé familiale et les méthodes de 
                            lutte contre les maladies sexuellement transmissibles et le VIH/SIDA.
                        </p>
                    </div>
                </div>

                {{-- Objectif 8 --}}
                <div class="objective-item">
                    <div class="objective-number">8</div>
                    <div class="objective-content">
                        <h4>Eau potable, assainissement et hygiène</h4>
                        <p>
                            Informer et former les populations sur les techniques et méthodes 
                            de traitement et conservation de l'eau potable, d'assainissement 
                            et d'hygiène dans le milieu.
                        </p>
                    </div>
                </div>

                {{-- Objectif 9 --}}
                <div class="objective-item">
                    <div class="objective-number">9</div>
                    <div class="objective-content">
                        <h4>Formation professionnelle et réinsertion</h4>
                        <p>
                            Promouvoir la formation professionnelle, l'apprentissage des métiers 
                            et la réinsertion socioéconomique des personnes vulnérables.
                        </p>
                    </div>
                </div>

                {{-- Objectif 10 --}}
                <div class="objective-item">
                    <div class="objective-number">10</div>
                    <div class="objective-content">
                        <h4>Environnement et développement durable</h4>
                        <p>
                            Contribuer à la lutte contre la déforestation et le réchauffement 
                            climatique par le reboisement et l'éducation environnementale.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 5 : SLOGAN --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-primary">
        <div class="container text-center">
            <div class="slogan-section">
                <i class="fas fa-quote-left slogan-quote"></i>
                <h2 class="slogan-text">
                    Être ensemble pour le bien de la communauté
                </h2>
                <i class="fas fa-quote-right slogan-quote"></i>
                <p class="slogan-subtext">
                    Ce slogan incarne notre philosophie d'action : la solidarité, le partage 
                    et l'engagement collectif comme moteurs du développement communautaire.
                </p>
            </div>
        </div>
    </section>

    {{-- CTA --}}
<section class="cta-section-page">
    <div class="container">
        <div class="cta-content-page">
            <h2 class="cta-title-page">
                Partagez <span class="highlight-page">notre vision</span>
            </h2>
            <p class="cta-text-page">
                Vous croyez en un Congo pacifié, où chaque communauté vit dans la dignité 
                et l'autonomie ? Rejoignez-nous et contribuez à faire de cette vision une réalité.
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