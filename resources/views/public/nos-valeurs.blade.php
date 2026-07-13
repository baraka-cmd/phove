@extends('layouts.public')

@section('title', 'Nos Valeurs')

@section('meta_description', 'Découvrez les cinq valeurs fondamentales d\'AHD-RDC : Professionnalisme, Transparence, Redevabilité, Compassion et Sensibilité au genre.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/nos-valeurs.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-gem"></i>
            </div>
            <h1 class="page-header-title">Nos Valeurs</h1>
            <p class="page-header-subtitle">
                Les principes fondamentaux qui guident chacune de nos actions et incarnent l'esprit d'AHD-RDC.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ url('/qui-sommes-nous') }}">Qui sommes-nous ?</a>
                <i class="fas fa-chevron-right"></i>
                <span>Nos Valeurs</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 1 : INTRODUCTION — Article 10 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ce qui nous définit</span>
                <h2 class="section-title">Les cinq piliers de notre action</h2>
                <p class="section-description">
                    Conformément à <strong>l'article 10 de nos statuts</strong>, AHD-RDC fonde 
                    son action sur cinq valeurs essentielles qui constituent le socle de notre 
                    identité organisationnelle et guident notre conduite au quotidien.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : VALEUR 1 — PROFESSIONNALISME --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="professionnalisme">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <div class="value-number-badge">01</div>
                    <h2 class="section-title text-left">Professionnalisme</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <p class="content-block-text">
                        Nous nous engageons à accomplir notre mission avec <strong>rigueur, 
                        compétence et excellence</strong>. Le professionnalisme chez AHD-RDC 
                        se traduit par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Une <strong>gestion rigoureuse</strong> des ressources humaines, financières et matérielles</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>respect des normes</strong> et des procédures établies</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>formation continue</strong> de notre personnel et de nos bénévoles</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>évaluation régulière</strong> de nos actions pour en mesurer l'impact</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>respect des délais</strong> et des engagements pris envers nos partenaires</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Professionnalisme AHD-RDC" 
                         class="content-image">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : VALEUR 2 — TRANSPARENCE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="transparence">
        <div class="container">
            <div class="content-grid content-grid-reverse">
                <div class="content-block">
                    <div class="value-number-badge">02</div>
                    <h2 class="section-title text-left">Transparence</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-search"></i>
                    </div>
                    <p class="content-block-text">
                        Nous croyons que la <strong>clarté et l'ouverture</strong> sont 
                        essentielles pour établir une relation de confiance avec nos 
                        bénéficiaires, nos partenaires et nos donateurs. Notre transparence 
                        se manifeste par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>publication régulière</strong> de nos rapports financiers et d'activités</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Une <strong>communication ouverte</strong> sur nos projets, nos réussites et nos défis</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>accès aux documents</strong> officiels de l'organisation</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Une <strong>gestion claire</strong> des dons et des financements reçus</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>traçabilité</strong> complète de nos actions sur le terrain</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Transparence AHD-RDC" 
                         class="content-image">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4 : VALEUR 3 — REDEVABILITÉ --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="redevabilite">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <div class="value-number-badge">03</div>
                    <h2 class="section-title text-left">Redevabilité</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <p class="content-block-text">
                        Nous nous tenons <strong>responsables</strong> de nos actions devant 
                        nos bénéficiaires, nos partenaires, nos bailleurs et les autorités. 
                        La redevabilité chez AHD-RDC implique :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>reddition de comptes</strong> régulière à nos organes statutaires</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>obligation de résultats</strong> envers les communautés bénéficiaires</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>respect des engagements</strong> contractuels avec les bailleurs</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>mesure de l'impact</strong> réel de nos interventions</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>audit indépendant</strong> de nos comptes et de nos activités</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Redevabilité AHD-RDC" 
                         class="content-image">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 5 : VALEUR 4 — COMPASSION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="compassion">
        <div class="container">
            <div class="content-grid content-grid-reverse">
                <div class="content-block">
                    <div class="value-number-badge">04</div>
                    <h2 class="section-title text-left">Compassion</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-heart"></i>
                    </div>
                    <p class="content-block-text">
                        La <strong>compassion</strong> est au cœur de notre mission humanitaire. 
                        Elle guide notre approche centrée sur l'humain et se traduit par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Une <strong>écoute active</strong> des besoins exprimés par les communautés</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>respect de la dignité</strong> de chaque personne accompagnée</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>empathie</strong> dans toutes nos interactions avec les bénéficiaires</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>proximité</strong> avec les communautés les plus isolées</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>engagement personnel</strong> de chaque membre de notre équipe</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Compassion AHD-RDC" 
                         class="content-image">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 6 : VALEUR 5 — SENSIBILITÉ AU GENRE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="genre">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <div class="value-number-badge">05</div>
                    <h2 class="section-title text-left">Sensibilité au genre</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <p class="content-block-text">
                        Nous intégrons la <strong>dimension du genre</strong> dans toutes nos 
                        interventions pour garantir l'égalité et l'équité. Cette valeur se 
                        manifeste par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>promotion active</strong> des droits des femmes et des filles</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>égalité des chances</strong> dans le recrutement et la gouvernance</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>prise en compte</strong> des besoins spécifiques de chaque genre</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>lutte contre les violences</strong> basées sur le genre</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>autonomisation économique</strong> des femmes dans nos projets</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Sensibilité au genre AHD-RDC" 
                         class="content-image">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 7 : RÉCAPITULATIF — CARTES DES VALEURS --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">En résumé</span>
                <h2 class="section-title">Nos cinq valeurs fondamentales</h2>
            </div>

            <div class="cards-grid cards-grid-4 values-recap-grid">
                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Professionnalisme</h3>
                </div>

                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Transparence</h3>
                </div>

                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3>Redevabilité</h3>
                </div>

                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Compassion</h3>
                </div>

                <div class="value-recap-card value-recap-card-full">
                    <div class="value-recap-icon">
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    <h3>Sensibilité au genre</h3>
                </div>
            </div>
        </div>
    </section>

   {{-- CTA --}}
<section class="cta-section-page">
    <div class="container">
        <div class="cta-content-page">
            <h2 class="cta-title-page">
                Des <span class="highlight-page">valeurs</span> qui nous engagent
            </h2>
            <p class="cta-text-page">
                Vous partagez ces valeurs de solidarité, d'intégrité et de justice sociale ? 
                Rejoignez AHD-RDC et contribuez à une action humanitaire fondée sur des 
                principes éthiques forts.
            </p>
            <div class="cta-buttons-page">
                <a href="{{ url('/devenir-membre') }}" class="btn-page-don">
                    <i class="fas fa-user-plus"></i> Rejoindre AHD-RDC
                </a>
                <a href="{{ url('/gouvernance') }}" class="btn-page-outline">
                    <i class="fas fa-sitemap"></i> Notre gouvernance
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/nos-valeurs.js') }}"></script>
@endsection