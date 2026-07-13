@extends('layouts.public')

@section('title', 'Organigramme')

@section('meta_description', 'Découvrez la structure organisationnelle complète d\'AHD-RDC : Assemblée Générale, Conseil d\'Administration, Direction Exécutive et Commission de Contrôle.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/organigramme.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <h1 class="page-header-title">Organigramme</h1>
            <p class="page-header-subtitle">
                La structure organisationnelle complète d'AHD-RDC, conformément au Règlement d'Ordre Intérieur.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ url('/qui-sommes-nous') }}">Qui sommes-nous ?</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ url('/gouvernance') }}">Gouvernance</a>
                <i class="fas fa-chevron-right"></i>
                <span>Organigramme</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 1 : ORGANIGRAMME VISUEL --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Structure organisationnelle</span>
                <h2 class="section-title">Notre organisation</h2>
                <p class="section-description">
                    Conformément au <strong>Titre V du Règlement d'Ordre Intérieur</strong>, 
                    voici la structure hiérarchique et fonctionnelle d'AHD-RDC.
                </p>
            </div>

            {{-- Organigramme --}}
            <div class="org-chart">
                {{-- Niveau 1 : Assemblée Générale --}}
                <div class="org-level">
                    <div class="org-box org-box-ag">
                        <div class="org-box-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Assemblée Générale</h3>
                            <p class="org-box-subtitle">Organe suprême d'orientation et de décision</p>
                            <span class="org-box-info">Tous les membres effectifs</span>
                        </div>
                    </div>
                </div>

                {{-- Lignes de connexion --}}
                <div class="org-connector">
                    <div class="org-line"></div>
                    <div class="org-split">
                        <div class="org-line-branch left"></div>
                        <div class="org-line-branch right"></div>
                    </div>
                </div>

                {{-- Niveau 2 : CA et CC --}}
                <div class="org-level org-level-2">
                    {{-- Conseil d'Administration --}}
                    <div class="org-box org-box-ca">
                        <div class="org-box-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Conseil d'Administration</h3>
                            <p class="org-box-subtitle">Organe de pilotage</p>
                            <span class="org-box-info">7 membres — Mandat 3 ans</span>
                        </div>
                    </div>

                    {{-- Commission de Contrôle --}}
                    <div class="org-box org-box-cc">
                        <div class="org-box-icon">
                            <i class="fas fa-search-dollar"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Commission de Contrôle</h3>
                            <p class="org-box-subtitle">Organe de vérification</p>
                            <span class="org-box-info">3 commissaires aux comptes</span>
                        </div>
                    </div>
                </div>

                {{-- Ligne vers DE --}}
                <div class="org-connector">
                    <div class="org-line org-line-single"></div>
                </div>

                {{-- Niveau 3 : Direction Exécutive --}}
                <div class="org-level">
                    <div class="org-box org-box-de">
                        <div class="org-box-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Direction Exécutive</h3>
                            <p class="org-box-subtitle">Coordonnateur National</p>
                            <span class="org-box-info">Dr AKSANTI DUNIA Steeng</span>
                        </div>
                    </div>
                </div>

                {{-- Lignes vers les départements --}}
                <div class="org-connector">
                    <div class="org-line"></div>
                    <div class="org-split">
                        <div class="org-line-branch left"></div>
                        <div class="org-line-branch right"></div>
                    </div>
                </div>

                {{-- Niveau 4 : Départements --}}
                <div class="org-level org-level-2">
                    {{-- Bureau des Programmes --}}
                    <div class="org-box org-box-prog">
                        <div class="org-box-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Bureau des Programmes</h3>
                            <p class="org-box-subtitle">Coordination des projets</p>
                        </div>
                    </div>

                    {{-- Administration et Finance --}}
                    <div class="org-box org-box-admin">
                        <div class="org-box-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Administration & Finance</h3>
                            <p class="org-box-subtitle">Gestion administrative et financière</p>
                        </div>
                    </div>
                </div>

                {{-- Lignes vers sous-départements --}}
                <div class="org-connector">
                    <div class="org-line"></div>
                    <div class="org-split org-split-4">
                        <div class="org-line-branch"></div>
                        <div class="org-line-branch"></div>
                        <div class="org-line-branch"></div>
                        <div class="org-line-branch"></div>
                    </div>
                </div>

                {{-- Niveau 5 : Sous-départements --}}
                <div class="org-level org-level-4">
                    <div class="org-box org-box-sub">
                        <div class="org-box-icon org-box-icon-sm">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Assistants Programmes</h3>
                        </div>
                    </div>
                    <div class="org-box org-box-sub">
                        <div class="org-box-icon org-box-icon-sm">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Animateurs</h3>
                        </div>
                    </div>
                    <div class="org-box org-box-sub">
                        <div class="org-box-icon org-box-icon-sm">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Comptabilité</h3>
                        </div>
                    </div>
                    <div class="org-box org-box-sub">
                        <div class="org-box-icon org-box-icon-sm">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Caisse</h3>
                        </div>
                    </div>
                </div>

                {{-- Ligne vers la base --}}
                <div class="org-connector">
                    <div class="org-line org-line-single"></div>
                </div>

                {{-- Niveau 6 : Structures de base --}}
                <div class="org-level">
                    <div class="org-box org-box-base">
                        <div class="org-box-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Structures de Base Communautaire</h3>
                            <p class="org-box-subtitle">Relais communautaires sur le terrain</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : LÉGENDE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Comprendre l'organigramme</span>
                <h2 class="section-title">Légende</h2>
            </div>

            <div class="legend-grid">
                <div class="legend-item">
                    <div class="legend-color legend-ag"></div>
                    <div class="legend-text">
                        <strong>Assemblée Générale</strong>
                        <span>Organe suprême — Tous les membres effectifs</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-ca"></div>
                    <div class="legend-text">
                        <strong>Conseil d'Administration</strong>
                        <span>Organe de pilotage — 7 membres</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-cc"></div>
                    <div class="legend-text">
                        <strong>Commission de Contrôle</strong>
                        <span>Organe de vérification — 3 commissaires</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-de"></div>
                    <div class="legend-text">
                        <strong>Direction Exécutive</strong>
                        <span>Organe d'exécution — Coordonnateur National</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-prog"></div>
                    <div class="legend-text">
                        <strong>Bureau des Programmes</strong>
                        <span>Coordination des projets et activités</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-admin"></div>
                    <div class="legend-text">
                        <strong>Administration & Finance</strong>
                        <span>Gestion administrative, financière et logistique</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-sub"></div>
                    <div class="legend-text">
                        <strong>Sous-départements</strong>
                        <span>Assistants, Animateurs, Comptabilité, Caisse</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-base"></div>
                    <div class="legend-text">
                        <strong>Structures de Base</strong>
                        <span>Relais communautaires sur le terrain</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : FONCTIONNEMENT --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <span class="section-subtitle">Principes</span>
                    <h2 class="section-title text-left">Comment fonctionne notre organisation</h2>

                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>L'Assemblée Générale</strong> se réunit chaque année en janvier et prend les grandes décisions</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Le Conseil d'Administration</strong> siège tous les 3 mois pour piloter l'association</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>La Direction Exécutive</strong> gère le quotidien et exécute les décisions</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>La Commission de Contrôle</strong> vérifie la conformité et fait rapport à l'AG</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Les <strong>Structures de Base</strong> assurent le relais direct avec les communautés</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/logo-ahd.png') }}" 
                         alt="Structure AHD-RDC" 
                         class="content-image">
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
<section class="cta-section-page">
    <div class="container">
        <div class="cta-content-page">
            <h2 class="cta-title-page">
                Une <span class="highlight-page">structure</span> au service des communautés
            </h2>
            <p class="cta-text-page">
                Notre organisation est conçue pour garantir l'efficacité, la transparence 
                et la redevabilité dans toutes nos actions humanitaires.
            </p>
            <div class="cta-buttons-page">
                <a href="{{ url('/gouvernance') }}" class="btn-page-don">
                    <i class="fas fa-sitemap"></i> Voir la gouvernance
                </a>
                <a href="{{ url('/transparence/documents-officiels') }}" class="btn-page-outline">
                    <i class="fas fa-file-alt"></i> Documents officiels
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/organigramme.js') }}"></script>
@endsection