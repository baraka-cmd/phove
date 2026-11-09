@extends('layouts.public')

@section('title', 'Nos Valeurs')

@section('meta_description', 'Découvrez les valeurs fondamentales de PHOVE : Compassion, Solidarité, Foi en Dieu, Charité, Intégrité et Transparence. « Aime ton prochain comme toi-même » — Romain 13:8.')

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
                Les principes fondamentaux qui guident chacune de nos actions et incarnent l'esprit de PHOVE.
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
    {{-- SECTION 1 : INTRODUCTION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ce qui nous définit</span>
                <h2 class="section-title">Les six piliers de notre action</h2>
                <p class="section-description">
                    PHOVE fonde son action sur des valeurs essentielles inspirées des Saintes 
                    Écritures et de notre devise : <strong>« Compassion avec la population »</strong>. 
                    Ces valeurs constituent le socle de notre identité et guident notre conduite 
                    au quotidien.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : VALEUR 1 — COMPASSION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="compassion">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <div class="value-number-badge">01</div>
                    <h2 class="section-title text-left">Compassion</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-heart"></i>
                    </div>
                    <p class="content-block-text">
                        {{ data_get($siteSettings ?? [], 'page_value_compassion') ?: 'La compassion est au cœur de notre mission humanitaire.' }} 
                        C'est notre devise : <strong>« Compassion avec la population »</strong>. 
                        Elle se traduit par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Une <strong>écoute active</strong> des besoins des orphelins, veuves et personnes démunies</span>
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
                            <span>La <strong>proximité</strong> avec les communautés les plus vulnérables</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>engagement personnel</strong> de chaque membre de notre équipe</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ img_url(data_get($siteSettings ?? [], 'page_values_image') ?: 'assets/images/about/about-phove.jpg') }}" 
                         alt="Compassion PHOVE — Au service des plus démunis" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600'">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : VALEUR 2 — SOLIDARITÉ --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="solidarite">
        <div class="container">
            <div class="content-grid content-grid-reverse">
                <div class="content-block">
                    <div class="value-number-badge">02</div>
                    <h2 class="section-title text-left">Solidarité</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <p class="content-block-text">
                        {{ data_get($siteSettings ?? [], 'page_value_solidarite') ?: 'La solidarité est le fondement de notre action collective.' }} 
                        Nous croyons en l'union des forces : <strong>« L'union fait la force et la 
                        corde à 3 fils se rompt difficilement »</strong>. Elle se manifeste par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>partage des ressources</strong> avec les plus démunis</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>mobilisation communautaire</strong> pour l'entraide</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>soutien mutuel</strong> entre les membres de l'organisation</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>protection des biens communs</strong> par l'assistance aux démunis</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>accompagnement des activités socio-économiques</strong> des vulnérables</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/slide2.jpg') }}" 
                         alt="Solidarité PHOVE — Union des forces" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=600'">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4 : VALEUR 3 — FOI EN DIEU --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="foi">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <div class="value-number-badge">03</div>
                    <h2 class="section-title text-left">Foi en Dieu</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-dove"></i>
                    </div>
                    <p class="content-block-text">
                        Notre <strong>foi en Dieu</strong> est le fondement spirituel de notre 
                        organisation. Nous sommes des serviteurs de Dieu guidés par les Saintes 
                        Écritures. Cette valeur se traduit par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>mise en valeur des Saintes Écritures</strong> dans toutes nos actions</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>valorisation de notre vocation pastorale</strong></span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>encadrement spirituel</strong> des orphelins, veuves et personnes démunies</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>préparation spirituelle</strong> des fidèles à rencontrer le Seigneur</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>pratique de l'amour du prochain</strong> selon Romain 13:8</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/slide1.jpg') }}" 
                         alt="Foi en Dieu PHOVE — Guidés par les Saintes Écritures" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600'">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 5 : VALEUR 4 — CHARITÉ --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="charite">
        <div class="container">
            <div class="content-grid content-grid-reverse">
                <div class="content-block">
                    <div class="value-number-badge">04</div>
                    <h2 class="section-title text-left">Charité</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <p class="content-block-text">
                        La <strong>charité</strong> est l'expression concrète de notre amour 
                        du prochain. Conformément à l'article 45 des statuts, chaque membre 
                        doit être <strong>charitable et capable de vivre selon Romain 13:8</strong>. 
                        Elle se manifeste par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>assistance alimentaire</strong> aux orphelins, veuves et personnes démunies</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>octroi de logements</strong> aux personnes vulnérables</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>assurance des soins médicaux</strong> et de l'habillement</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>scolarisation des orphelins</strong></span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>soutien aux veuves</strong> et aux personnes âgées</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/slide3.jpg') }}" 
                         alt="Charité PHOVE — Amour du prochain" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1469571486292-0ba58a3f068b?w=600'">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 6 : VALEUR 5 — INTÉGRITÉ --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="integrite">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <div class="value-number-badge">05</div>
                    <h2 class="section-title text-left">Intégrité</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <p class="content-block-text">
                        L'<strong>intégrité</strong> guide notre conduite morale et éthique. 
                        Conformément à l'article 71 des statuts, <strong>la corruption à tout 
                        niveau est à bannir</strong> dans l'organisation. Elle se traduit par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>respect strict</strong> des statuts et du Règlement d'Ordre Intérieur</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>bonne moralité</strong> exigée de chaque membre (Article 45)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>lutte contre la corruption</strong> à tous les niveaux</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>honnêteté</strong> dans la gestion des ressources</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>transparence financière</strong> avec la Commission de Contrôle</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/about/about-phove.jpg') }}" 
                         alt="Intégrité PHOVE — Gestion transparente" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600'">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 7 : VALEUR 6 — TRANSPARENCE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="transparence">
        <div class="container">
            <div class="content-grid content-grid-reverse">
                <div class="content-block">
                    <div class="value-number-badge">06</div>
                    <h2 class="section-title text-left">Transparence</h2>
                    <div class="value-icon-large">
                        <i class="fas fa-search"></i>
                    </div>
                    <p class="content-block-text">
                        La <strong>transparence</strong> est essentielle pour établir une relation 
                        de confiance avec nos bénéficiaires, partenaires et donateurs. 
                        Elle se manifeste par :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>publication régulière</strong> des rapports financiers</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>Commission de Contrôle</strong> qui vérifie les opérations chaque trimestre (Article 35)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Le <strong>rapport trimestriel et annuel</strong> du Directeur Général au CA et à l'AG (Article 18)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>La <strong>traçabilité</strong> complète des dons et cotisations</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>L'<strong>accès aux documents</strong> officiels de l'organisation</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/about/about-phove.jpg') }}" 
                         alt="Transparence PHOVE — Reddition de comptes" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600'">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 8 : RÉCAPITULATIF — CARTES DES VALEURS --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">En résumé</span>
                <h2 class="section-title">Nos six valeurs fondamentales</h2>
            </div>

            <div class="cards-grid cards-grid-4 values-recap-grid">
                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Compassion</h3>
                </div>

                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Solidarité</h3>
                </div>

                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-dove"></i>
                    </div>
                    <h3>Foi en Dieu</h3>
                </div>

                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Charité</h3>
                </div>

                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Intégrité</h3>
                </div>

                <div class="value-recap-card">
                    <div class="value-recap-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Transparence</h3>
                </div>
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
                    Des <span class="highlight-page">valeurs</span> qui nous engagent
                </h2>
                <p class="cta-text-page">
                    Vous partagez ces valeurs de compassion, de solidarité et de foi ? 
                    Rejoignez PHOVE et contribuez à une action humanitaire fondée sur 
                    des principes éthiques forts.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/devenir-membre') }}" class="btn-page-don">
                        <i class="fas fa-user-plus"></i> Rejoindre PHOVE
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