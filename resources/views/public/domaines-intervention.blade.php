@extends('layouts.public')

@section('title', 'Domaines d\'intervention')

@section('meta_description', 'Découvrez les 9 domaines d\'intervention de PHOVE : Santé, Éducation, Protection Sociale, Agriculture, Infrastructures, Socio-économique, Spirituel, Psychosocial et Assistance Judiciaire.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/domaines-intervention.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-shapes"></i>
            </div>
            <h1 class="page-header-title">Nos Domaines d'Intervention</h1>
            <p class="page-header-subtitle">
                Conformément aux articles 8 et 9 des statuts, PHOVE intervient dans 
                l'encadrement des personnes vulnérables : orphelins, veuves et personnes démunies.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Domaines d'intervention</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION STATISTIQUES RAPIDES --}}
    {{-- ============================================ --}}
    <section class="domaines-stats-section">
        <div class="container">
            <div class="domaines-stats-grid">
                <div class="domaine-stat-card">
                    <div class="domaine-stat-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="domaine-stat-info">
                        <span class="domaine-stat-number">9</span>
                        <span class="domaine-stat-label">Domaines d'intervention</span>
                    </div>
                </div>
                <div class="domaine-stat-card">
                    <div class="domaine-stat-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="domaine-stat-info">
                        <span class="domaine-stat-number">{{ \App\Models\Projet::where('publier', true)->count() }}</span>
                        <span class="domaine-stat-label">Projets réalisés</span>
                    </div>
                </div>
                <div class="domaine-stat-card">
                    <div class="domaine-stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="domaine-stat-info">
                        <span class="domaine-stat-number">10</span>
                        <span class="domaine-stat-label">Membres fondateurs</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 1 : SANTÉ & NUTRITION --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section" id="sante-nutrition">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <span class="domaine-detail-number">01</span>
                </div>
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Santé & Nutrition</h2>
                    <p class="domaine-detail-text">
                        PHOVE assure l'<strong>alimentation</strong>, les <strong>soins médicaux</strong> 
                        et l'<strong>habillement</strong> des personnes vulnérables. Nous supervisons 
                        les activités sanitaires du programme, organisons des consultations et 
                        accompagnons les malades traumatisés pour lutter contre la stigmatisation 
                        et la discrimination.
                    </p>
                    <p class="domaine-detail-text">
                        Nos <strong>médecins</strong> supervisent les activités sanitaires, élaborent 
                        les rapports mensuels et veillent au patrimoine sanitaire de l'organisation. 
                        Nos <strong>psychologues</strong> assurent la stabilité mentale des victimes 
                        de guerre et de violences sexuelles.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'sante_nutrition']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Santé & Nutrition
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 2 : ÉDUCATION & FORMATION --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section domaine-alt" id="education">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Éducation & Formation</h2>
                    <p class="domaine-detail-text">
                        PHOVE assure la <strong>scolarisation des orphelins</strong>, l'<strong>alphabétisation</strong> 
                        et la <strong>formation professionnelle</strong>. Nos encadreurs et moniteurs forment 
                        les personnes dans différentes professions : coupe et couture, informatique, 
                        art culinaire, maçonnerie, menuiserie, art, esthétique, musique, agriculture, 
                        pêche et élevage.
                    </p>
                    <p class="domaine-detail-text">
                        Ils <strong>livrent les attestations de mérite</strong> dans chaque domaine, 
                        organisent des séminaires, des jeux, des loisirs, des visites et des promenades. 
                        L'éducation est la clé de l'autonomisation des personnes vulnérables.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'education']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Éducation
                        </a>
                    </div>
                </div>
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <span class="domaine-detail-number">02</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 3 : PROTECTION SOCIALE --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section" id="protection">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span class="domaine-detail-number">03</span>
                </div>
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Protection Sociale</h2>
                    <p class="domaine-detail-text">
                        PHOVE <strong>octroie des logements</strong> aux personnes vulnérables, 
                        assure leur <strong>sécurité alimentaire</strong> et veille à leur 
                        <strong>protection</strong>. Nous luttons contre le banditisme, le vol, 
                        le viol, les avortements forcés, les mariages précoces, le vagabondage 
                        et la pauvreté.
                    </p>
                    <p class="domaine-detail-text">
                        Nous accompagnons les <strong>délaissés juridiquement</strong> et assurons 
                        l'<strong>assistance judiciaire</strong> aux personnes vulnérables. Notre 
                        objectif est de protéger les plus faibles et de défendre leurs droits.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'protection']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Protection
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 4 : AGRICULTURE & ÉLEVAGE --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section domaine-alt" id="agriculture">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Agriculture, Élevage & Pêche</h2>
                    <p class="domaine-detail-text">
                        PHOVE soutient l'<strong>agriculture</strong>, l'<strong>élevage</strong> 
                        et la <strong>pêche</strong> pour améliorer la sécurité alimentaire des 
                        communautés. Nous traçons, réfectionnons et réhabilitons les 
                        <strong>routes de desserte agricole</strong> pour faciliter l'écoulement 
                        des productions.
                    </p>
                    <p class="domaine-detail-text">
                        Nous construisons des <strong>dépôts de productions agricoles</strong> en 
                        milieux ruraux et urbains pour la conservation des récoltes. Nos projets 
                        avec la <strong>FAO</strong> et le <strong>PAM</strong> renforcent la 
                        résilience des ménages vulnérables.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'securite_alimentaire']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Agriculture
                        </a>
                    </div>
                </div>
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-tractor"></i>
                    </div>
                    <span class="domaine-detail-number">04</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 5 : INFRASTRUCTURES --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section" id="infrastructures">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <span class="domaine-detail-number">05</span>
                </div>
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Infrastructures</h2>
                    <p class="domaine-detail-text">
                        PHOVE <strong>construit, réfectionne et réhabilite</strong> les 
                        <strong>infrastructures hospitalières et scolaires</strong> pour améliorer 
                        l'accès aux soins de santé et à l'éducation. Nous construisons également 
                        des <strong>dépôts de productions agricoles</strong> en milieux ruraux 
                        et urbains.
                    </p>
                    <p class="domaine-detail-text">
                        Notre objectif est de créer un environnement propice au développement 
                        des communautés vulnérables en leur offrant des infrastructures de 
                        qualité pour la santé, l'éducation et l'agriculture.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Infrastructures
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 6 : ACTIVITÉS SOCIO-ÉCONOMIQUES --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section domaine-alt" id="socio-economique">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Activités Socio-Économiques</h2>
                    <p class="domaine-detail-text">
                        PHOVE <strong>accompagne les activités socio-économiques</strong> des 
                        personnes vulnérables pour leur autonomisation. Nous soutenons leurs 
                        initiatives génératrices de revenus et luttons contre l'<strong>oisiveté 
                        juvénile</strong> qui pousse les jeunes vers la délinquance.
                    </p>
                    <p class="domaine-detail-text">
                        Nous encourageons l'entrepreneuriat local, la formation aux métiers et 
                        l'insertion professionnelle des orphelins, veuves et personnes démunies 
                        pour leur permettre de subvenir à leurs besoins.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Socio-économiques
                        </a>
                    </div>
                </div>
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <span class="domaine-detail-number">06</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 7 : ACCOMPAGNEMENT SPIRITUEL --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section" id="spirituel">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-dove"></i>
                    </div>
                    <span class="domaine-detail-number">07</span>
                </div>
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Accompagnement Spirituel</h2>
                    <p class="domaine-detail-text">
                        PHOVE est une organisation à <strong>caractère spirituel</strong>. Nos 
                        <strong>prédicateurs</strong> — pasteurs, évangélistes, intercesseurs — 
                        s'occupent de la vie spirituelle de l'organisation. Ils sont 
                        <strong>bénévoles et charitables</strong>.
                    </p>
                    <p class="domaine-detail-text">
                        Nous <strong>mettons en valeur les Saintes Écritures</strong>, donnons la 
                        valeur à notre vocation pastorale et préparons les brebis du Seigneur à 
                        le rencontrer un jour. Nous encadrons spirituellement et socialement 
                        les orphelins, veuves et personnes démunies.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ url('/mission-vision') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Découvrir notre mission
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 8 : ACCOMPAGNEMENT PSYCHOSOCIAL --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section domaine-alt" id="psychosocial">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Accompagnement Psychosocial</h2>
                    <p class="domaine-detail-text">
                        Nos <strong>psychologues</strong> s'occupent de l'état psychique des personnes 
                        vulnérables. Ils font des <strong>consultations</strong>, des <strong>enquêtes 
                        sur terrain</strong> pour se rendre compte de l'état psychique des victimes 
                        traumatisées par la guerre ou les violences sexuelles.
                    </p>
                    <p class="domaine-detail-text">
                        Ils <strong>étudient et élaborent les fiches d'enquête</strong>, assurent la 
                        <strong>stabilité mentale</strong> des victimes et prodiguent des conseils 
                        pouvant rendre la personne traumatisée stable. Nous luttons contre la 
                        stigmatisation et la discrimination.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ url('/contact') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Nous contacter
                        </a>
                    </div>
                </div>
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <span class="domaine-detail-number">08</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 9 : ASSISTANCE JUDICIAIRE --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section" id="assistance-judiciaire">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <span class="domaine-detail-number">09</span>
                </div>
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Assistance Judiciaire</h2>
                    <p class="domaine-detail-text">
                        PHOVE accompagne les <strong>délaissés juridiquement</strong>. Nous 
                        offrons une <strong>assistance judiciaire</strong> aux personnes vulnérables 
                        qui ne peuvent pas se défendre. Nos chargés des affaires juridiques 
                        veillent au respect des droits des orphelins, veuves et personnes démunies.
                    </p>
                    <p class="domaine-detail-text">
                        Nous luttons contre les <strong>violences sexuelles</strong>, les 
                        <strong>avortements forcés</strong> et les <strong>mariages précoces</strong>. 
                        Notre objectif est de garantir l'accès à la justice pour tous.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ url('/contact') }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Demander de l'aide
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section-domaines">
        <div class="cta-decoration cta-decoration-left"></div>
        <div class="cta-decoration cta-decoration-right"></div>

        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">
                    Soutenez nos <span class="highlight">actions</span>
                </h2>
                <p class="cta-text">
                    Votre soutien nous permet d'intervenir dans tous ces domaines pour le bien-être 
                    des orphelins, veuves et personnes démunies de la RD Congo.
                </p>
                <div class="cta-buttons">
                    <a href="{{ url('/faire-un-don') }}" class="btn-cta-don">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                    <a href="{{ route('projets.index') }}" class="btn-cta-outline">
                        <i class="fas fa-list"></i> Voir tous les projets
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/domaines-intervention.js') }}"></script>
@endsection