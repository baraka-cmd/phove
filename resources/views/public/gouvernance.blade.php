@extends('layouts.public')

@section('title', 'Gouvernance')

@section('meta_description', 'Découvrez la structure de gouvernance de PHOVE : Assemblée Générale, Conseil d\'Administration, Direction Générale, Commission de Contrôle, Comité de Suivi et Comité de Gestion.')

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
    {{-- SECTION 1 : INTRODUCTION — Article 10 des Statuts --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre organisation</span>
                <h2 class="section-title">Une structure à six organes</h2>
                <p class="section-description">
                    Conformément à <strong>l'article 10 des statuts</strong>, PHOVE est 
                    constituée des organes suivants : l'Assemblée Générale (AG), le Conseil 
                    d'Administration (CA), la Commission de Contrôle, le Comité de Suivi, 
                    le Comité de Gestion, les Stations, les Bases et les Antennes.
                </p>
            </div>

            <div class="organs-overview">
                {{-- Organe 1 : AG --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3>Assemblée Générale</h3>
                    <p>Organe suprême qui prend les décisions</p>
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
                    <p>Organe qui exécute les décisions de l'AG</p>
                    <a href="#conseil-administration" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>

                {{-- Organe 3 : CC --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-search-dollar"></i>
                    </div>
                    <h3>Commission de Contrôle</h3>
                    <p>Organe qui supervise les activités sur terrain</p>
                    <a href="#commission-controle" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>

                {{-- Organe 4 : CS --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3>Comité de Suivi</h3>
                    <p>Travaille en collaboration avec la Commission de Contrôle</p>
                    <a href="#comite-suivi" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>

                {{-- Organe 5 : CG --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Comité de Gestion</h3>
                    <p>Travaille sous la responsabilité du Directeur Général</p>
                    <a href="#comite-gestion" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>

                {{-- Organe 6 : Structures territoriales --}}
                <div class="organ-preview-card">
                    <div class="organ-preview-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <h3>Stations, Bases & Antennes</h3>
                    <p>Exécutent les activités dans les pays, territoires et communes</p>
                    <a href="#structures-territoriales" class="organ-preview-link">
                        En savoir plus <i class="fas fa-arrow-down"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : ASSEMBLÉE GÉNÉRALE — Articles 11-13 --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="assemblee-generale">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <span class="section-subtitle">Organe suprême</span>
                    <h2 class="section-title text-left">Assemblée Générale (AG)</h2>

                    <div class="quote-block">
                        <p>
                            L'Assemblée Générale est l'organe suprême de l'ONG qui prend les décisions.
                        </p>
                    </div>

                    <p class="content-block-text">
                        Conformément à <strong>l'article 11 des statuts</strong>, l'AG est 
                        l'organe suprême de PHOVE. Elle est composée de tous les membres 
                        effectifs (fondateurs et adhérents) qui ont une voix délibérative. 
                        Les membres d'honneur et sympathisants ont une voix consultative.
                    </p>

                    <h4 class="content-block-title">
                        <i class="fas fa-list-ul"></i> Fonctionnement (Articles 12-13)
                    </h4>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-user-tie"></i>
                            <span><strong>Président de l'AG</strong> : voté par l'assemblée, mandat de 2 ans renouvelable deux fois</span>
                        </li>
                        <li>
                            <i class="fas fa-user"></i>
                            <span><strong>Secrétaire Général</strong> : assure le secrétariat de l'AG</span>
                        </li>
                        <li>
                            <i class="fas fa-coins"></i>
                            <span><strong>Trésorier</strong> : gère les finances de l'organisation</span>
                        </li>
                        <li>
                            <i class="fas fa-users"></i>
                            <span><strong>Trois Conseillers</strong> : assistent le Président</span>
                        </li>
                    </ul>

                    <h4 class="content-block-title">
                        <i class="fas fa-calendar-alt"></i> Sessions
                    </h4>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-calendar-check"></i>
                            <span>Le Conseil d'Administration prépare les assemblées générales <strong>une fois par an</strong> (Article 40)</span>
                        </li>
                        <li>
                            <i class="fas fa-calendar-plus"></i>
                            <span>Session <strong>extraordinaire</strong> : convoquée pour la révision des statuts (2/3 de l'AG)</span>
                        </li>
                        <li>
                            <i class="fas fa-vote-yea"></i>
                            <span><strong>Décisions</strong> : prises à la majorité des 2/3 (Articles 77, 79, 81)</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ img_url(data_get($siteSettings ?? [], 'page_assemblee_generale_image') ?: 'assets/images/about/about-phove.jpg') }}" 
                        alt="Assemblée Générale PHOVE" 
                        class="content-image"
                        onerror="this.src='https://images.unsplash.com/photo-1521791136064-7986c2920216?w=600'">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : CONSEIL D'ADMINISTRATION — DYNAMIQUE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="conseil-administration">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Organe d'exécution</span>
                <h2 class="section-title">Conseil d'Administration (CA)</h2>
                <p class="section-description">
                    Conformément à <strong>l'article 11b des statuts</strong>, le Conseil 
                    d'Administration est l'organe qui <strong>exécute les décisions prises 
                    par l'Assemblée Générale</strong>. Il assume l'administration de l'organisation.
                </p>
            </div>

            {{-- Membres dynamiques du CA --}}
            @if($membresCA->count() > 0)
            <div class="team-grid">
                @foreach($membresCA as $organe)
                <div class="team-card">
                    <div class="team-avatar">
                        @if($organe->membre && $organe->membre->photo)
                            <img src="{{ img_url($organe->membre->photo) }}"
                                alt="{{ $organe->membre->nomComplet() }}"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span style="display: none;">
                                {{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}
                            </span>
                        @else
                            <span>{{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">{{ $organe->membre ? $organe->membre->nomComplet() : '—' }}</h4>
                        <span class="team-role">{{ $organe->poste }}</span>
                        @if($organe->membre && $organe->membre->telephone)
                        <span class="team-contact">
                            <i class="fas fa-phone-alt"></i> {{ $organe->membre->telephone }}
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align: center; padding: 30px; color: #94A3B8;">
                <i class="fas fa-user-tie" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.4;"></i>
                <p style="font-size: 13px;">Aucun membre au Conseil d'Administration pour le moment.</p>
            </div>
            @endif

            <div class="content-block mt-5">
                <h4 class="content-block-title">
                    <i class="fas fa-list-ul"></i> Attributions du Directeur Général (Article 18)
                </h4>
                <ul class="check-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Représente PHOVE auprès des tiers et partout</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Veille sur la gestion de l'économie et étudie les voies et moyens de régénérer les ressources</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Veille sur le bon fonctionnement du programme partout dans le monde et fixe les salaires de base</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Dresse un rapport trimestriel et annuel à l'intention du CA et de l'AG</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Engage et révoque les agents en collaboration avec le CA et l'AG</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span><strong>Mandat</strong> : 6 ans renouvelable</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4 : COMMISSION DE CONTRÔLE — DYNAMIQUE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="commission-controle">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Organe de supervision</span>
                <h2 class="section-title">Commission de Contrôle</h2>
                <p class="section-description">
                    Conformément à <strong>l'article 11c des statuts</strong>, le Comité de 
                    Contrôle est l'organe qui <strong>supervise les activités sur terrain</strong> 
                    de l'ONG et fait rapport à l'Assemblée Générale.
                </p>
            </div>

            {{-- Membres dynamiques de la CC --}}
            @if($membresCC->count() > 0)
            <div class="team-grid">
                @foreach($membresCC as $organe)
                <div class="team-card">
                    <div class="team-avatar">
                        @if($organe->membre && $organe->membre->photo)
                            <img src="{{ img_url($organe->membre->photo) }}"
                                alt="{{ $organe->membre->nomComplet() }}"
                                loading="lazy"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span style="display: none;">
                                {{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}
                            </span>
                        @else
                            <span>{{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="team-info">
                        <h4 class="team-name">{{ $organe->membre ? $organe->membre->nomComplet() : '—' }}</h4>
                        <span class="team-role">{{ $organe->poste }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="content-block mt-5">
                <h4 class="content-block-title">
                    <i class="fas fa-list-ul"></i> Attributions (Article 35)
                </h4>
                <ul class="check-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Une Commission de Contrôle est constituée <strong>à la fin de chaque trimestre</strong></span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Elle <strong>vérifie les opérations passées</strong> et surtout celles qui traitent des finances</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Elle travaille en collaboration avec le Comité de Suivi (Article 38)</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Elle donne rapport au Directeur Général et Coordonnateur du programme</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 5 : COMITÉ DE SUIVI — Article 38 --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="comite-suivi">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Organe d'accompagnement</span>
                <h2 class="section-title">Comité de Suivi</h2>
                <p class="section-description">
                    Conformément à <strong>l'article 38 des statuts</strong>, les Comités de 
                    Suivi travaillent en collaboration avec la Commission de Contrôle et tous 
                    donnent rapport au Directeur Général et Coordonnateur du programme.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 6 : COMITÉ DE GESTION — Article 39 --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light" id="comite-gestion">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Organe de gestion</span>
                <h2 class="section-title">Comité de Gestion</h2>
                <p class="section-description">
                    Conformément à <strong>l'article 39 des statuts</strong>, le Conseil 
                    d'Administration travaille en collaboration avec le Conseil de Gestion 
                    sous la responsabilité du Directeur Général.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 7 : STRUCTURES TERRITORIALES — DYNAMIQUE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white" id="structures-territoriales">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre présence</span>
                <h2 class="section-title">Stations, Bases & Antennes</h2>
                <p class="section-description">
                    Conformément aux <strong>articles 2c et 11d-g des statuts</strong>, PHOVE 
                    déploie sa structure sur trois niveaux territoriaux.
                </p>
            </div>

            @if($structures->count() > 0)
            <div class="cards-grid">
                @foreach($structures->where('type', 'station') as $station)
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-flag"></i>
                    </div>
                    <h3 class="info-card-title">{{ $station->nom }}</h3>
                    <p class="info-card-text">
                        Station — {{ $station->pays ?? 'Pays non spécifié' }}
                    </p>
                </div>
                @endforeach

                @foreach($structures->where('type', 'base') as $base)
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="info-card-title">{{ $base->nom }}</h3>
                    <p class="info-card-text">
                        Base — {{ $base->province ?? 'Province non spécifiée' }}
                    </p>
                </div>
                @endforeach

                @foreach($structures->where('type', 'antenne') as $antenne)
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="info-card-title">{{ $antenne->nom }}</h3>
                    <p class="info-card-text">
                        Antenne — {{ $antenne->territoire ?? 'Territoire non spécifié' }}
                    </p>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align: center; padding: 30px; color: #94A3B8;">
                <i class="fas fa-sitemap" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.4;"></i>
                <p style="font-size: 13px;">Aucune structure territoriale pour le moment.</p>
            </div>
            @endif
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
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