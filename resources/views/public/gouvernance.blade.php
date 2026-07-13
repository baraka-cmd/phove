@extends('layouts.public')

@section('title', 'Gouvernance')

@section('meta_description', 'Découvrez la structure de gouvernance d\'AHD-RDC : Assemblée Générale, Conseil d\'Administration, Direction Exécutive et Commission de Contrôle.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gouvernance.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-sitemap"></i>
            </div>
            <h1 class="page-header-title">Gouvernance</h1>
            <p class="page-header-subtitle">
                Une structure organisationnelle solide et transparente pour garantir l'efficacité de notre action humanitaire.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ url('/qui-sommes-nous') }}">Qui sommes-nous ?</a>
                <i class="fas fa-chevron-right"></i>
                <span>Gouvernance</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 1 : INTRODUCTION — Articles 22-23 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre organisation</span>
                <h2 class="section-title">Une structure à quatre organes</h2>
                <p class="section-description">
                    Conformément à <strong>l'article 22 de nos statuts</strong>, AHD-RDC est 
                    composée de quatre organes distincts qui assurent une gouvernance 
                    démocratique, transparente et efficace.
                </p>
            </div>

            <div class="organs-overview">
                {{-- Organe 1 : AG --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3>Assemblée Générale</h3>
                    <p>Organe suprême d'orientation et de décision</p>
                    <a href="#assemblee-generale" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>

                {{-- Organe 2 : CA --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Conseil d'Administration</h3>
                    <p>Organe de pilotage et de suivi</p>
                    <a href="#conseil-administration" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>

                {{-- Organe 3 : DE --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Direction Exécutive</h3>
                    <p>Organe d'exécution et de gestion quotidienne</p>
                    <a href="#direction-executive" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>

                {{-- Organe 4 : CC --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-search-dollar"></i>
                    </div>
                    <h3>Commission de Contrôle</h3>
                    <p>Organe de vérification et de conformité</p>
                    <a href="#commission-controle" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : ASSEMBLÉE GÉNÉRALE — Articles 23-25 --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="assemblee-generale">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <span class="section-subtitle">Organe suprême</span>
                    <h2 class="section-title text-left">Assemblée Générale</h2>

                    <div class="quote-block">
                        <p>
                            L'Assemblée Générale est l'organe suprême d'orientation et de décision 
                            de l'organisation. Elle est composée de tous les membres effectifs.
                        </p>
                    </div>

                    <p class="content-block-text">
                        Conformément à <strong>l'article 23 des statuts</strong>, l'Assemblée 
                        Générale (AG) est dirigée par des personnes physiques choisies lors d'une 
                        session ordinaire. Elle incarne la souveraineté de l'organisation.
                    </p>

                    <h4 class="content-block-title">
                        <i class="fas fa-list-ul"></i> Attributions (Article 24)
                    </h4>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Adopter et amender les statuts et le Règlement d'Ordre Intérieur</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Fixer le programme d'activités annuelles</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Approuver le budget et le rapport d'activités présentés par la Direction Exécutive</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Donner un mandat au Conseil d'Administration et recevoir de lui le rapport</span>
                        </li>
                    </ul>

                    <h4 class="content-block-title">
                        <i class="fas fa-calendar-alt"></i> Fonctionnement (Article 25)
                    </h4>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-calendar-check"></i>
                            <span>Session <strong>ordinaire</strong> : chaque année au mois de janvier</span>
                        </li>
                        <li>
                            <i class="fas fa-calendar-plus"></i>
                            <span>Session <strong>extraordinaire</strong> : chaque fois que l'urgence s'impose</span>
                        </li>
                        <li>
                            <i class="fas fa-users"></i>
                            <span><strong>Quorum</strong> : majorité simple des membres effectifs</span>
                        </li>
                        <li>
                            <i class="fas fa-vote-yea"></i>
                            <span><strong>Décisions</strong> : prises à la majorité des 2/3 des membres présents</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Assemblée Générale AHD-RDC" 
                         class="content-image">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : CONSEIL D'ADMINISTRATION — Articles 26-27 --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="conseil-administration">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Organe de pilotage</span>
                <h2 class="section-title">Conseil d'Administration</h2>
                <p class="section-description">
                    Le Conseil d'Administration est l'organe de pilotage de l'association. 
                    Il est composé de <strong>7 membres</strong> élus par l'Assemblée Générale 
                    pour un mandat de <strong>3 ans renouvelable une fois</strong> (Articles 26-27).
                </p>
            </div>

            {{-- Membres actuels du CA — Source : PV d'élection du 7 janvier 2017 --}}
            <div class="team-grid">
                {{-- Membre 1 --}}
                <div class="team-card">
                    <div class="team-avatar">
                        <span>MD</span>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">Judith MAJINA DUNIA</h4>
                        <span class="team-role">Présidente du Conseil d'Administration</span>
                        <span class="team-contact">
                            <i class="fas fa-phone-alt"></i> +243 972 059 604
                        </span>
                    </div>
                </div>

                {{-- Membre 2 --}}
                <div class="team-card">
                    <div class="team-avatar">
                        <span>CM</span>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">CHRISTIAN MIRINDI</h4>
                        <span class="team-role">Vice-Président du Conseil d'Administration</span>
                        <span class="team-contact">
                            <i class="fas fa-phone-alt"></i> +243 999 210 576
                        </span>
                    </div>
                </div>

                {{-- Membre 3 --}}
                <div class="team-card">
                    <div class="team-avatar">
                        <span>BB</span>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">BISIMWA BASHONGA Riphin</h4>
                        <span class="team-role">Membre du Conseil d'Administration</span>
                        <span class="team-contact">
                            <i class="fas fa-phone-alt"></i> +243 973 307 807
                        </span>
                    </div>
                </div>

                {{-- Membre 4 --}}
                <div class="team-card">
                    <div class="team-avatar">
                        <span>OM</span>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">OLEMBE MUSANGI Grace</h4>
                        <span class="team-role">Membre du Conseil d'Administration</span>
                        <span class="team-contact">
                            <i class="fas fa-phone-alt"></i> +243 971 346 492
                        </span>
                    </div>
                </div>
            </div>

            <div class="content-block mt-5">
                <h4 class="content-block-title">
                    <i class="fas fa-list-ul"></i> Attributions du Conseil d'Administration
                </h4>
                <ul class="check-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Veiller au bon déroulement de l'ensemble des activités de l'association</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Exploiter et analyser le rapport de la Direction Exécutive</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Donner des orientations à la Direction Exécutive</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Siéger une fois tous les 3 mois sur convocation du Président</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4 : DIRECTION EXÉCUTIVE — Articles 28-31 --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="direction-executive">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Organe d'exécution</span>
                <h2 class="section-title">Direction Exécutive</h2>
                <p class="section-description">
                    La Direction Exécutive est l'organe d'exécution de toutes les décisions 
                    prises par le Conseil d'Administration. Elle gère quotidiennement 
                    l'association et engage celle-ci dans tous les contacts avec les 
                    partenaires locaux et internationaux (Articles 28-31).
                </p>
            </div>

            {{-- Coordonnateur National — Source : PV de nomination du 7 janvier 2017 --}}
            <div class="team-grid team-grid-single">
                <div class="team-card team-card-featured">
                    <div class="team-avatar team-avatar-featured">
                        <span>AD</span>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">Dr AKSANTI DUNIA Steeng</h4>
                        <span class="team-role">Coordonnateur National</span>
                        <span class="team-contact">
                            <i class="fas fa-phone-alt"></i> +243 994 656 250
                        </span>
                        <p class="team-bio">
                            Initiateur de l'organisation, le Coordonnateur National est le 
                            gestionnaire et mandataire légal qui représente l'association 
                            vis-à-vis des tiers de droit public ou de droit privé.
                        </p>
                    </div>
                </div>
            </div>

            <div class="content-block mt-5">
                <h4 class="content-block-title">
                    <i class="fas fa-list-ul"></i> Composition et attributions
                </h4>
                <ul class="check-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>La Direction Exécutive est composée d'un <strong>Directeur Exécutif</strong>, d'un <strong>Coordonnateur des programmes</strong> et d'un <strong>Responsable de l'Administration et Finances</strong></span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Elle exécute les décisions du Conseil d'Administration conformément aux programmes d'activités</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Elle recrute, engage ou révoque la main d'œuvre nécessaire après débat et délibération avec le CA</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Le Directeur Exécutif peut déléguer une partie de son pouvoir de gestion à un membre de l'association</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 5 : COMMISSION DE CONTRÔLE — Article 32 --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="commission-controle">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Organe de vérification</span>
                <h2 class="section-title">Commission de Contrôle</h2>
                <p class="section-description">
                    La Commission de Contrôle est constituée de trois commissaires aux comptes 
                    élus en Assemblée Générale ordinaire pour un mandat de <strong>3 ans 
                    renouvelable une fois</strong>. Elle fait respecter les recommandations 
                    de l'AG (Article 32).
                </p>
            </div>

            {{-- Membres actuels de la CC — Source : PV d'élection du 7 janvier 2017 --}}
            <div class="team-grid team-grid-cc">
                {{-- Membre 1 --}}
                <div class="team-card">
                    <div class="team-avatar">
                        <span>NB</span>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">NATHALIE BISIMWA Julienne</h4>
                        <span class="team-role">Présidente de la Commission de Contrôle</span>
                        <span class="team-contact">
                            <i class="fas fa-phone-alt"></i> +243 970 138 592
                        </span>
                    </div>
                </div>

                {{-- Membre 2 --}}
                <div class="team-card">
                    <div class="team-avatar">
                        <span>MB</span>
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">MUGARUKA BASHANGWA Léon</h4>
                        <span class="team-role">Secrétaire de la Commission de Contrôle</span>
                        <span class="team-contact">
                            <i class="fas fa-phone-alt"></i> +243 975 105 996
                        </span>
                    </div>
                </div>
            </div>

            <div class="content-block mt-5">
                <h4 class="content-block-title">
                    <i class="fas fa-list-ul"></i> Attributions
                </h4>
                <ul class="check-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Faire respecter les recommandations de l'Assemblée Générale</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Faire rapport à l'Assemblée Générale avec copie au Conseil d'Administration</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Veiller à la conformité de la gestion financière et administrative</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

   {{-- CTA --}}
<section class="cta-section-page">
    <div class="container">
        <div class="cta-content-page">
            <h2 class="cta-title-page">
                Une <span class="highlight-page">gouvernance</span> transparente
            </h2>
            <p class="cta-text-page">
                La transparence et la redevabilité sont au cœur de notre fonctionnement. 
                Consultez nos documents officiels et nos rapports pour mieux comprendre notre action.
            </p>
            <div class="cta-buttons-page">
                <a href="{{ url('/transparence/documents-officiels') }}" class="btn-page-don">
                    <i class="fas fa-file-alt"></i> Documents officiels
                </a>
                <a href="{{ url('/organigramme') }}" class="btn-page-outline">
                    <i class="fas fa-project-diagram"></i> Voir l'organigramme
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/gouvernance.js') }}"></script>
@endsection