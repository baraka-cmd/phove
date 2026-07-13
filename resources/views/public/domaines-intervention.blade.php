@extends('layouts.public')

@section('title', 'Domaines d\'intervention')

@section('meta_description', 'Découvrez les 9 domaines d\'intervention d\'AHD-RDC : Santé-Nutrition, Éducation, Protection, Eau & Assainissement, Sécurité Alimentaire, Communication, Environnement, Paix et Genre.')

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
                Conformément à l'article 9 de nos statuts, AHD-RDC intervient dans neuf domaines clés pour le développement communautaire.
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
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="domaine-stat-info">
                        <span class="domaine-stat-number">4</span>
                        <span class="domaine-stat-label">Zones couvertes</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 1 : SANTÉ-NUTRITION --}}
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
                    <h2 class="domaine-detail-title">Santé-Nutrition</h2>
                    <p class="domaine-detail-text">
                        Nous éduquons la population sur la <strong>santé familiale</strong> et les méthodes 
                        de lutte contre les maladies sexuellement transmissibles et le <strong>VIH/SIDA</strong>. 
                        Nous combattons la malnutrition sous toutes ses formes en garantissant à la communauté 
                        l'accès à l'alimentation et aux soins de santé primaires.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'sante_nutrition']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Santé-Nutrition
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 2 : ÉDUCATION --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section domaine-alt" id="education">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Éducation</h2>
                    <p class="domaine-detail-text">
                        Nous promouvons la <strong>formation professionnelle</strong>, l'apprentissage des 
                        métiers et la <strong>réinsertion socioéconomique</strong> des personnes vulnérables. 
                        Nous croyons que l'éducation et la formation sont les clés de l'autonomisation 
                        et du développement durable des communautés.
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
    {{-- DOMAINE 3 : PROTECTION --}}
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
                    <h2 class="domaine-detail-title">Protection de l'Enfant et de la Femme</h2>
                    <p class="domaine-detail-text">
                        Nous sensibilisons, informons et formons les leaders et la population sur la 
                        <strong>promotion des droits humains et des enfants</strong>. Nous menons un 
                        plaidoyer en faveur des droits des <strong>réfugiés, rapatriés, demandeurs 
                        d'asile, retournés et déplacés internes</strong>. La protection des plus 
                        vulnérables est au cœur de notre mission.
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
    {{-- DOMAINE 4 : WASH --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section domaine-alt" id="wash">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Eau, Hygiène et Assainissement (WASH)</h2>
                    <p class="domaine-detail-text">
                        Nous informons et formons les populations sur les techniques et méthodes de 
                        <strong>traitement et conservation de l'eau potable</strong>, d'assainissement 
                        et d'hygiène dans le milieu. L'accès à l'eau potable et à l'assainissement 
                        est un droit fondamental que nous défendons.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'wash']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets WASH
                        </a>
                    </div>
                </div>
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <span class="domaine-detail-number">04</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 5 : SÉCURITÉ ALIMENTAIRE --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section" id="securite-alimentaire">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <span class="domaine-detail-number">05</span>
                </div>
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Sécurité Alimentaire</h2>
                    <p class="domaine-detail-text">
                        Nous améliorons la <strong>sécurité des moyens de subsistance</strong> des 
                        communautés rurales en soutenant la production agricole, l'élevage et les 
                        activités génératrices de revenus. Notre objectif est de garantir l'accès 
                        à une alimentation suffisante et nutritive pour tous.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'securite_alimentaire']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Sécurité Alimentaire
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 6 : COMMUNICATION --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section domaine-alt" id="communication">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Communication</h2>
                    <p class="domaine-detail-text">
                        La communication est un outil essentiel pour le <strong>changement social</strong>. 
                        Nous informons, sensibilisons et mobilisons les communautés autour des enjeux 
                        de développement, de santé publique et de droits humains à travers des 
                        campagnes de proximité.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'communication']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Communication
                        </a>
                    </div>
                </div>
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <span class="domaine-detail-number">06</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 7 : ENVIRONNEMENT --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section" id="environnement">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-tree"></i>
                    </div>
                    <span class="domaine-detail-number">07</span>
                </div>
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Protection de l'Environnement</h2>
                    <p class="domaine-detail-text">
                        Nous contribuons à la lutte contre la <strong>déforestation</strong> et le 
                        <strong>réchauffement climatique</strong> par le reboisement et l'éducation 
                        environnementale. La protection de notre planète est une responsabilité 
                        collective que nous portons avec détermination.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'environnement']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Environnement
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 8 : PAIX --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section domaine-alt" id="paix">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Promotion de la Paix</h2>
                    <p class="domaine-detail-text">
                        Nous accompagnons les communautés dans la <strong>gestion et la transformation 
                        positive des conflits</strong> à travers des mécanismes communautaires, la 
                        promotion de la cohabitation pacifique et la bonne gouvernance. La paix est 
                        la condition essentielle de tout développement durable.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'paix']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Paix
                        </a>
                    </div>
                </div>
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-dove"></i>
                    </div>
                    <span class="domaine-detail-number">08</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- DOMAINE 9 : GENRE --}}
    {{-- ============================================ --}}
    <section class="domaine-detail-section" id="genre">
        <div class="container">
            <div class="domaine-detail-grid">
                <div class="domaine-detail-visual">
                    <div class="domaine-detail-icon">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <span class="domaine-detail-number">09</span>
                </div>
                <div class="domaine-detail-content">
                    <h2 class="domaine-detail-title">Sensibilité au Genre</h2>
                    <p class="domaine-detail-text">
                        La <strong>sensibilité au genre</strong> est une valeur transversale dans 
                        toutes nos interventions. Nous promouvons l'égalité des chances, la 
                        participation des femmes à la prise de décision et luttons contre toutes 
                        les formes de discrimination basées sur le genre.
                    </p>
                    <div class="domaine-detail-actions">
                        <a href="{{ route('projets.index', ['domaine' => 'genre']) }}" class="btn btn-primary">
                            <i class="fas fa-search"></i> Voir les projets Genre
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
                    des communautés vulnérables de la RD Congo.
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