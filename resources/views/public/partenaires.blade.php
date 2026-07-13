@extends('layouts.public')

@section('title', 'Nos Partenaires')

@section('meta_description', 'Découvrez les organisations et institutions qui soutiennent AHD-RDC dans sa mission humanitaire en République Démocratique du Congo.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/partenaires.css') }}">
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
            <h1 class="page-header-title">Nos Partenaires</h1>
            <p class="page-header-subtitle">
                Ils nous font confiance et soutiennent notre mission humanitaire pour le développement des communautés vulnérables en RD Congo.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Nos Partenaires</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION INTRODUCTION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre réseau</span>
                <h2 class="section-title">Ils nous accompagnent</h2>
                <p class="section-description">
                    AHD-RDC collabore avec des organisations locales et internationales qui partagent 
                    notre vision d'une société pacifiée et solidaire. Leur soutien financier, technique 
                    et logistique nous permet de mener à bien notre mission.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION PARTENAIRES ACTUELS --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            
            {{-- Partenaire 1 : Conseil d'Administration AHD-RDC --}}
            <div class="partenaire-card">
                <div class="partenaire-card-grid">
                    {{-- Logo --}}
                    <div class="partenaire-logo-wrapper">
                        <div class="partenaire-logo">
                            <span>AHD</span>
                        </div>
                        <div class="partenaire-type-badge">
                            <i class="fas fa-building"></i> Partenaire local
                        </div>
                    </div>

                    {{-- Infos --}}
                    <div class="partenaire-info">
                        <h2 class="partenaire-name">Conseil d'Administration AHD-RDC</h2>
                        <p class="partenaire-description">
                            Le Conseil d'Administration d'AHD-RDC assure l'autofinancement de l'organisation 
                            depuis sa création en 2017. Composé de membres engagés, il garantit la stabilité 
                            financière et la continuité des projets humanitaires dans les zones de Minova, 
                            Kirotshe, Kalehe et Goma.
                        </p>
                        <div class="partenaire-meta">
                            <div class="partenaire-meta-item">
                                <i class="fas fa-calendar-check"></i>
                                <span>Partenaire depuis <strong>2017</strong></span>
                            </div>
                            <div class="partenaire-meta-item">
                                <i class="fas fa-hand-holding-heart"></i>
                                <span>Type de soutien : <strong>Financement & Gouvernance</strong></span>
                            </div>
                        </div>
                        <div class="partenaire-projets">
                            <span class="partenaire-projets-label">
                                <i class="fas fa-project-diagram"></i> Projets soutenus :
                            </span>
                            <div class="partenaire-projets-tags">
                                <span class="partenaire-projet-tag">Sécurité Alimentaire</span>
                                <span class="partenaire-projet-tag">Santé-Nutrition</span>
                                <span class="partenaire-projet-tag">WASH</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Partenaire 2 : DMP-RDC --}}
            <div class="partenaire-card">
                <div class="partenaire-card-grid">
                    {{-- Logo --}}
                    <div class="partenaire-logo-wrapper">
                        <div class="partenaire-logo partenaire-logo-dmp">
                            <span>DMP</span>
                        </div>
                        <div class="partenaire-type-badge">
                            <i class="fas fa-handshake"></i> Partenaire technique
                        </div>
                    </div>

                    {{-- Infos --}}
                    <div class="partenaire-info">
                        <h2 class="partenaire-name">DMP-RDC</h2>
                        <p class="partenaire-description">
                            DMP-RDC est un partenaire technique clé dans la mise en œuvre des projets 
                            de sensibilisation sanitaire. Leur expertise a été déterminante lors des 
                            campagnes de sensibilisation contre le choléra, la maladie à virus Ebola 
                            et la pandémie de COVID-19 dans les zones de Minova et Kirotshe.
                        </p>
                        <div class="partenaire-meta">
                            <div class="partenaire-meta-item">
                                <i class="fas fa-calendar-check"></i>
                                <span>Partenaire depuis <strong>2020</strong></span>
                            </div>
                            <div class="partenaire-meta-item">
                                <i class="fas fa-hand-holding-heart"></i>
                                <span>Type de soutien : <strong>Technique & Mise en œuvre</strong></span>
                            </div>
                        </div>
                        <div class="partenaire-projets">
                            <span class="partenaire-projets-label">
                                <i class="fas fa-project-diagram"></i> Projets soutenus :
                            </span>
                            <div class="partenaire-projets-tags">
                                <span class="partenaire-projet-tag">Sensibilisation choléra</span>
                                <span class="partenaire-projet-tag">Sensibilisation Ebola</span>
                                <span class="partenaire-projet-tag">COVID-19</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION TYPES DE PARTENARIAT --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Comment collaborer</span>
                <h2 class="section-title">Types de partenariat</h2>
                <p class="section-description">
                    Nous proposons plusieurs formes de collaboration adaptées aux objectifs de chaque partenaire.
                </p>
            </div>

            <div class="partenariat-types-grid">
                {{-- Type 1 --}}
                <div class="partenariat-type-card">
                    <div class="partenariat-type-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3 class="partenariat-type-title">Bailleur de fonds</h3>
                    <p class="partenariat-type-text">
                        Financez un ou plusieurs de nos projets humanitaires. Vous recevrez des rapports 
                        détaillés sur l'utilisation des fonds et l'impact de votre contribution.
                    </p>
                </div>

                {{-- Type 2 --}}
                <div class="partenariat-type-card">
                    <div class="partenariat-type-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="partenariat-type-title">Partenaire technique</h3>
                    <p class="partenariat-type-text">
                        Apportez votre expertise technique, vos équipements ou votre savoir-faire 
                        pour renforcer la qualité de nos interventions sur le terrain.
                    </p>
                </div>

                {{-- Type 3 --}}
                <div class="partenariat-type-card">
                    <div class="partenariat-type-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="partenariat-type-title">Partenaire logistique</h3>
                    <p class="partenariat-type-text">
                        Soutenez nos opérations en fournissant du matériel, des véhicules, des 
                        fournitures ou des locaux pour nos activités.
                    </p>
                </div>

                {{-- Type 4 --}}
                <div class="partenariat-type-card">
                    <div class="partenariat-type-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3 class="partenariat-type-title">Partenaire média</h3>
                    <p class="partenariat-type-text">
                        Aidez-nous à amplifier notre message en relayant nos actions, nos campagnes 
                        de sensibilisation et nos appels aux dons.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION DEVENIR PARTENAIRE --}}
    {{-- ============================================ --}}
    <section class="devenir-partenaire-section">
        <div class="container">
            <div class="devenir-partenaire-card">
                <div class="devenir-partenaire-content">
                    <span class="section-subtitle">Rejoignez-nous</span>
                    <h2 class="section-title text-left">Devenir partenaire</h2>
                    <p class="content-block-text">
                        Vous représentez une organisation, une institution ou une entreprise ? 
                        Vous partagez nos valeurs de solidarité et de développement ? 
                        Rejoignez notre réseau de partenaires et contribuez à changer des vies 
                        en République Démocratique du Congo.
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Visibilité sur notre plateforme et nos supports de communication</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Rapports détaillés sur l'impact de votre contribution</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Participation aux visites de terrain et aux événements</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Reçus fiscaux et attestations de partenariat</span>
                        </li>
                    </ul>
                    <div class="devenir-partenaire-actions">
                        <a href="{{ url('/contact') }}" class="btn btn-accent btn-lg">
                            <i class="fas fa-paper-plane"></i> Nous contacter
                        </a>
                        <a href="{{ url('/faire-un-don') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-heart"></i> Faire un don
                        </a>
                    </div>
                </div>
                <div class="devenir-partenaire-visual">
                    <div class="devenir-partenaire-circle">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <p class="devenir-partenaire-slogan">
                        « Être ensemble pour le bien de la communauté »
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section-partenaires">
        <div class="cta-decoration cta-decoration-left"></div>
        <div class="cta-decoration cta-decoration-right"></div>

        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title-partenaires">
                    Construisons <span class="highlight-partenaires">ensemble</span> l'avenir
                </h2>
                <p class="cta-text-partenaires">
                    Chaque partenariat est une pierre apportée à l'édifice du développement 
                    communautaire en République Démocratique du Congo.
                </p>
                <div class="cta-buttons-partenaires">
                    <a href="{{ url('/contact') }}" class="btn-partenaires-don">
                        <i class="fas fa-handshake"></i> Devenir partenaire
                    </a>
                    <a href="{{ route('projets.index') }}" class="btn-partenaires-outline">
                        <i class="fas fa-list"></i> Voir nos projets
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/partenaires.js') }}"></script>
@endsection