@extends('layouts.public')

@section('title', 'Qui sommes-nous ?')

@section('meta_description', 'AHD-RDC est une organisation non gouvernementale de droit congolais créée le 7 janvier 2017 à Minova, Sud-Kivu. Découvrez notre histoire, notre engagement et notre équipe.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/qui-sommes-nous.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-users"></i>
            </div>
            <h1 class="page-header-title">Qui sommes-nous ?</h1>
            <p class="page-header-subtitle">
                Découvrez l'histoire, la mission et l'engagement d'AHD-RDC au service des communautés vulnérables de l'Est de la République Démocratique du Congo.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Qui sommes-nous ?</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 1 : NOTRE HISTOIRE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="content-grid">
                {{-- Texte --}}
                <div class="content-block">
                    <span class="section-subtitle">Notre histoire</span>
                    <h2 class="section-title text-left">Une organisation née de la volonté d'agir</h2>
                    
                    <p class="content-block-text">
                        <strong>Action Humanitaire pour le Développement en République Démocratique du Congo (AHD-RDC)</strong> 
                        est une organisation non gouvernementale de droit congolais créée le 
                        <strong>7 janvier 2017</strong> à Minova, dans le territoire de Kalehe, 
                        province du Sud-Kivu, en République Démocratique du Congo.
                    </p>

                    <p class="content-block-text">
                        Nous sommes nés dans une région marquée par des conflits intercommunautaires, 
                        des guerres à répétition et des crises humanitaires qui ont causé des milliers 
                        de morts, des déplacements massifs de populations et une paupérisation 
                        généralisée des communautés.
                    </p>

                    <p class="content-block-text">
                        Face à cette situation, un groupe de <strong>7 membres fondateurs</strong>, 
                        mus par le souci et la volonté de contribuer à l'éradication ou à l'atténuation 
                        des misères des personnes vulnérables, a décidé de créer cette association 
                        sans but lucratif.
                    </p>

                    <div class="quote-block">
                        <p>
                            « Mus par le souci et la volonté de contribuer à l'éradication ou à l'atténuation 
                            des misères des personnes vulnérables victimes d'atrocités des guerres qui ont 
                            secoué toute la partie de l'Est de la République Démocratique du Congo. »
                        </p>
                    </div>
                </div>

                {{-- Image --}}
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="AHD-RDC — Action humanitaire en RD Congo" 
                         class="content-image">
                    <div class="content-image-badge">
                        <strong>{{ date('Y') - 2017 }}+</strong><br>
                        <small>années d'action</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : NOS FONDATEURS --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="fondateurs">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Les pionniers</span>
                <h2 class="section-title">Nos membres fondateurs</h2>
                <p class="section-description">
                    Le 7 janvier 2017, sept personnes engagées se sont réunies à Minova pour donner 
                    naissance à AHD-RDC.
                </p>
            </div>

            <div class="founders-grid">
                {{-- Fondateur 1 --}}
                <div class="founder-card">
                    <div class="founder-avatar">
                        <span>AD</span>
                    </div>
                    <div class="founder-info">
                        <h4 class="founder-name">Dr AKSANTI DUNIA Steeng</h4>
                        <span class="founder-role">Initiateur & Coordonnateur National</span>
                        <span class="founder-location">
                            <i class="fas fa-map-marker-alt"></i> Minova
                        </span>
                    </div>
                </div>

                {{-- Fondateur 2 --}}
                <div class="founder-card">
                    <div class="founder-avatar">
                        <span>BB</span>
                    </div>
                    <div class="founder-info">
                        <h4 class="founder-name">BISIMWA BASHONGA Riphin</h4>
                        <span class="founder-role">Membre Fondateur</span>
                        <span class="founder-location">
                            <i class="fas fa-map-marker-alt"></i> Minova
                        </span>
                    </div>
                </div>

                {{-- Fondateur 3 --}}
                <div class="founder-card">
                    <div class="founder-avatar">
                        <span>CM</span>
                    </div>
                    <div class="founder-info">
                        <h4 class="founder-name">CHRISTIAN MIRINDI</h4>
                        <span class="founder-role">Membre Fondateur</span>
                        <span class="founder-location">
                            <i class="fas fa-map-marker-alt"></i> Goma
                        </span>
                    </div>
                </div>

                {{-- Fondateur 4 --}}
                <div class="founder-card">
                    <div class="founder-avatar">
                        <span>MD</span>
                    </div>
                    <div class="founder-info">
                        <h4 class="founder-name">MAJINA DUNIA Judith</h4>
                        <span class="founder-role">Membre Fondateur</span>
                        <span class="founder-location">
                            <i class="fas fa-map-marker-alt"></i> Goma
                        </span>
                    </div>
                </div>

                {{-- Fondateur 5 --}}
                <div class="founder-card">
                    <div class="founder-avatar">
                        <span>MB</span>
                    </div>
                    <div class="founder-info">
                        <h4 class="founder-name">MUGARUKA BASHANGWA Léon</h4>
                        <span class="founder-role">Membre Fondateur</span>
                        <span class="founder-location">
                            <i class="fas fa-map-marker-alt"></i> Minova
                        </span>
                    </div>
                </div>

                {{-- Fondateur 6 --}}
                <div class="founder-card">
                    <div class="founder-avatar">
                        <span>OM</span>
                    </div>
                    <div class="founder-info">
                        <h4 class="founder-name">OLEMBE MUSANGI Grace</h4>
                        <span class="founder-role">Membre Fondateur</span>
                        <span class="founder-location">
                            <i class="fas fa-map-marker-alt"></i> Goma
                        </span>
                    </div>
                </div>

                {{-- Fondateur 7 --}}
                <div class="founder-card">
                    <div class="founder-avatar">
                        <span>NB</span>
                    </div>
                    <div class="founder-info">
                        <h4 class="founder-name">NATHALIE BISIMWA Julienne</h4>
                        <span class="founder-role">Membre Fondateur</span>
                        <span class="founder-location">
                            <i class="fas fa-map-marker-alt"></i> Minova
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : NOTRE IDENTITÉ --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre identité</span>
                <h2 class="section-title">Ce qui nous définit</h2>
            </div>

            <div class="cards-grid">
                {{-- Carte 1 --}}
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3 class="info-card-title">Statut juridique</h3>
                    <p class="info-card-text">
                        AHD-RDC est une <strong>Association Sans But Lucratif (ASBL)</strong> 
                        de droit congolais, régie par la loi n°004-2001 du 20 juillet 2001 
                        portant dispositions générales applicables aux associations sans but 
                        lucratif et aux établissements d'utilité publique.
                    </p>
                </div>

                {{-- Carte 2 --}}
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h3 class="info-card-title">Caractère</h3>
                    <p class="info-card-text">
                        Conformément à l'article 3 de nos statuts, AHD-RDC est une organisation 
                        <strong>apolitique et non confessionnelle</strong>. Nous intervenons 
                        sans discrimination de race, de religion, d'ethnie ou d'appartenance 
                        politique.
                    </p>
                </div>

                {{-- Carte 3 --}}
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="info-card-title">Durée</h3>
                    <p class="info-card-text">
                        Conformément à l'article 4 de nos statuts, AHD-RDC est créée pour 
                        une <strong>durée indéterminée</strong>. Notre engagement est permanent 
                        et s'inscrit dans le long terme auprès des communautés.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4 : ZONES D'INTERVENTION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="content-grid">
                {{-- Carte ou image --}}
                <div>
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Zones d'intervention AHD-RDC" 
                         class="content-image">
                </div>

                {{-- Texte --}}
                <div class="content-block">
                    <span class="section-subtitle">Notre présence</span>
                    <h2 class="section-title text-left">Zones d'intervention</h2>
                    
                    <p class="content-block-text">
                        Conformément à l'article 11 de nos statuts, AHD-RDC a comme rayon 
                        d'action les provinces du <strong>Sud-Kivu</strong> et du 
                        <strong>Nord-Kivu</strong>, avec une capacité d'extension sur toute 
                        l'étendue de la République Démocratique du Congo.
                    </p>

                    <p class="content-block-text">
                        Notre siège social est établi à <strong>Minova</strong>, dans le 
                        territoire de Kalehe, province du Sud-Kivu. Nous intervenons 
                        également dans les zones de <strong>Kirotshe</strong> et 
                        <strong>Goma</strong>.
                    </p>

                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Minova</strong> — Territoire de Kalehe, Sud-Kivu (Siège social)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Kirotshe</strong> — Territoire de Masisi, Nord-Kivu</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Goma</strong> — Chef-lieu du Nord-Kivu</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Extension prévue</strong> — Sur toute l'étendue de la RDC</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 5 : PUBLIC CIBLE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre raison d'être</span>
                <h2 class="section-title">Notre public cible</h2>
                <p class="section-description">
                    AHD-RDC se focalise sur la protection et l'accompagnement des personnes 
                    les plus vulnérables de nos communautés.
                </p>
            </div>

            <div class="cards-grid cards-grid-4">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-female"></i>
                    </div>
                    <h3 class="info-card-title">Femmes</h3>
                    <p class="info-card-text">
                        Protection, autonomisation et promotion des droits des femmes.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3 class="info-card-title">Enfants</h3>
                    <p class="info-card-text">
                        Protection de l'enfance et promotion des droits des enfants.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-wheelchair"></i>
                    </div>
                    <h3 class="info-card-title">Handicapés</h3>
                    <p class="info-card-text">
                        Inclusion et accompagnement des personnes vivant avec un handicap.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3 class="info-card-title">Orphelins & 3ème âge</h3>
                    <p class="info-card-text">
                        Protection des orphelins et des personnes du troisième âge.
                    </p>
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
                Rejoignez notre <span class="highlight-page">mission humanitaire</span>
            </h2>
            <p class="cta-text-page">
                Vous partagez nos valeurs de solidarité et de justice sociale ? 
                Impliquez-vous à nos côtés pour faire la différence dans la vie 
                des communautés vulnérables de la RD Congo.
            </p>
            <div class="cta-buttons-page">
                <a href="{{ url('/devenir-membre') }}" class="btn-page-don">
                    <i class="fas fa-user-plus"></i> Devenir membre
                </a>
                <a href="{{ url('/contact') }}" class="btn-page-outline">
                    <i class="fas fa-envelope"></i> Nous contacter
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/qui-sommes-nous.js') }}"></script>
@endsection