{{-- ============================================ --}}
{{-- HEADER PUBLIC — PHOVE --}}
{{-- Logo + Menu navigation + Recherche + Bouton Don --}}
{{-- ============================================ --}}

@php
    /**
     * Détermination du menu actif (pour les états "active").
     */
    $routeActuelle = request()->route()?->getName();
    $urlActuelle   = request()->path();

    $estAccueil      = $urlActuelle === '/';
    $estProjets      = request()->is('projets*') || request()->is('domaines-intervention');
    $estActualites   = request()->is('actualites*');
    $estGalerie      = request()->is('galerie*');
    $estTransparence = request()->is('transparence*');
    $estContact      = request()->routeIs('contact');

    // Menu « Qui sommes-nous ? »
    $pagesInstitutionnelles = [
        'qui-sommes-nous',
        'mission-vision',
        'nos-valeurs',
        'gouvernance',
        'organigramme',
        'partenaires',
    ];
    $estInstitutionnel = request()->is(...array_map(fn($p) => $p . '*', $pagesInstitutionnelles));

    // Menu « Impliquez-vous »
    $pagesEngagement = [
        'faire-un-don',
        'devenir-membre',
        'devenir-benevole',
        'devenir-partenaire',
    ];
    $estEngagement = request()->is(...array_map(fn($p) => $p . '*', $pagesEngagement));
@endphp

<header class="header" id="header">
    <div class="container">
        <div class="header-inner">

            {{-- ======================================== --}}
            {{-- LOGO --}}
            {{-- ======================================== --}}
            <a href="{{ url('/') }}" class="header-logo" title="{{ data_get($siteSettings ?? [], 'site_name', 'PHOVE') }} — Retour à l'accueil">
                <img src="{{ data_get($siteSettings ?? [], 'site_logo') ? media_url(data_get($siteSettings, 'site_logo')) : asset('assets/images/logo-phove.png') }}"
                     alt="Logo {{ data_get($siteSettings ?? [], 'site_name', 'PHOVE') }}"
                     class="header-logo-img">
                <div class="header-logo-text">
                    <span class="header-logo-name">{{ data_get($siteSettings ?? [], 'site_name', 'PHOVE') }}</span>
                    <span class="header-logo-subtitle">
                        {{ data_get($siteSettings ?? [], 'site_full_name', 'Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies') }}
                    </span>
                </div>
            </a>

            {{-- ======================================== --}}
            {{-- MENU PRINCIPAL --}}
            {{-- ======================================== --}}
            <nav class="header-nav" id="mainNav" aria-label="Navigation principale">
                <ul class="header-menu">

                    {{-- ---------------------------------- --}}
                    {{-- ACCUEIL --}}
                    {{-- ---------------------------------- --}}
                    <li class="header-menu-item">
                        <a href="{{ url('/') }}"
                           class="header-menu-link {{ $estAccueil ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Accueil</span>
                        </a>
                    </li>

                    {{-- ---------------------------------- --}}
                    {{-- QUI SOMMES-NOUS ? --}}
                    {{-- ---------------------------------- --}}
                    <li class="header-menu-item has-dropdown">
                        <a href="#" class="header-menu-link {{ $estInstitutionnel ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Qui sommes-nous ?</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="header-dropdown">
                            <li><a href="{{ url('/qui-sommes-nous') }}" class="dropdown-link">Présentation</a></li>
                            <li><a href="{{ url('/mission-vision') }}" class="dropdown-link">Mission &amp; Vision</a></li>
                            <li><a href="{{ url('/nos-valeurs') }}" class="dropdown-link">Nos Valeurs</a></li>
                            <li><a href="{{ url('/gouvernance') }}" class="dropdown-link">Gouvernance</a></li>
                            <li><a href="{{ url('/organigramme') }}" class="dropdown-link">Organigramme</a></li>
                            <li><a href="{{ url('/partenaires') }}" class="dropdown-link">Nos Partenaires</a></li>
                        </ul>
                    </li>

                    {{-- ---------------------------------- --}}
                    {{-- NOS ACTIONS --}}
                    {{-- ---------------------------------- --}}
                    <li class="header-menu-item has-dropdown">
                        <a href="#" class="header-menu-link {{ $estProjets ? 'active' : '' }}">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Nos Actions</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="header-dropdown header-dropdown-large">
                            <li>
                                <a href="{{ url('/projets') }}" class="dropdown-link">
                                    <i class="fas fa-list"></i> Tous les projets
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <a href="{{ url('/projets?domaine=sante_nutrition') }}" class="dropdown-link">
                                    <i class="fas fa-heartbeat"></i> Santé-Nutrition
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/projets?domaine=education') }}" class="dropdown-link">
                                    <i class="fas fa-graduation-cap"></i> Éducation
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/projets?domaine=protection') }}" class="dropdown-link">
                                    <i class="fas fa-shield-alt"></i> Protection
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/projets?domaine=wash') }}" class="dropdown-link">
                                    <i class="fas fa-tint"></i> Eau, Hygiène &amp; Assainissement
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/projets?domaine=securite_alimentaire') }}" class="dropdown-link">
                                    <i class="fas fa-seedling"></i> Sécurité Alimentaire
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/projets?domaine=environnement') }}" class="dropdown-link">
                                    <i class="fas fa-tree"></i> Environnement
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('/projets?domaine=paix') }}" class="dropdown-link">
                                    <i class="fas fa-dove"></i> Paix &amp; Cohésion sociale
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <a href="{{ url('/domaines-intervention') }}" class="dropdown-link">
                                    <i class="fas fa-shapes"></i> Domaines d'intervention
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- ---------------------------------- --}}
                    {{-- ACTUALITÉS --}}
                    {{-- ---------------------------------- --}}
                    <li class="header-menu-item">
                        <a href="{{ url('/actualites') }}"
                           class="header-menu-link {{ $estActualites ? 'active' : '' }}">
                            <i class="fas fa-newspaper"></i>
                            <span>Actualités</span>
                        </a>
                    </li>

                    {{-- ---------------------------------- --}}
                    {{-- GALERIE --}}
                    {{-- ---------------------------------- --}}
                    <li class="header-menu-item">
                        <a href="{{ route('galerie.index') }}"
                           class="header-menu-link {{ $estGalerie ? 'active' : '' }}">
                            <i class="fas fa-images"></i>
                            <span>Galerie</span>
                        </a>
                    </li>

                    {{-- ---------------------------------- --}}
                    {{-- TRANSPARENCE --}}
                    {{-- ---------------------------------- --}}
                    <li class="header-menu-item has-dropdown">
                        <a href="#" class="header-menu-link {{ $estTransparence ? 'active' : '' }}">
                            <i class="fas fa-search-dollar"></i>
                            <span>Transparence</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="header-dropdown">
                            <li>
                                <a href="{{ route('transparence', ['tab' => 'financiers']) }}" class="dropdown-link">
                                    Rapports financiers
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('transparence', ['tab' => 'annuels']) }}" class="dropdown-link">
                                    Rapports annuels
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('transparence', ['tab' => 'documents']) }}" class="dropdown-link">
                                    Documents officiels
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- ---------------------------------- --}}
                    {{-- IMPLIQUEZ-VOUS --}}
                    {{-- ---------------------------------- --}}
                    <li class="header-menu-item has-dropdown">
                        <a href="#" class="header-menu-link {{ $estEngagement ? 'active' : '' }}">
                            <i class="fas fa-hands-helping"></i>
                            <span>Impliquez-vous</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="header-dropdown">
                            <li>
                                <a href="{{ url('/faire-un-don') }}" class="dropdown-link">
                                    <i class="fas fa-heart text-accent"></i> Faire un don
                                </a>
                            </li>
                            <li><a href="{{ url('/devenir-membre') }}" class="dropdown-link">Devenir membre</a></li>
                            <li><a href="{{ url('/devenir-benevole') }}" class="dropdown-link">Devenir bénévole</a></li>
                            <li><a href="{{ url('/devenir-partenaire') }}" class="dropdown-link">Devenir partenaire</a></li>
                            <li><a href="{{ url('/partenaires') }}" class="dropdown-link">Nos Partenaires</a></li>
                        </ul>
                    </li>

                    {{-- ---------------------------------- --}}
                    {{-- CONTACT --}}
                    {{-- ---------------------------------- --}}
                    <li class="header-menu-item">
                        <a href="{{ route('contact') }}"
                           class="header-menu-link {{ $estContact ? 'active' : '' }}">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Contact</span>
                        </a>
                    </li>

                </ul>
            </nav>

            {{-- ======================================== --}}
            {{-- ACTIONS HEADER --}}
            {{-- ======================================== --}}
            <div class="header-actions">

                {{-- Recherche --}}
                <button type="button"
                        class="header-search-btn"
                        id="searchToggle"
                        aria-label="Ouvrir la recherche"
                        title="Rechercher">
                    <i class="fas fa-search"></i>
                </button>

                {{-- Bouton Don --}}
                <a href="{{ url('/faire-un-don') }}"
                   class="btn btn-accent btn-sm header-donate-btn">
                    <i class="fas fa-heart"></i>
                    <span>Faire un don</span>
                </a>

                {{-- Accès admin (si connecté) --}}
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="header-admin-btn"
                       title="Accéder au tableau de bord">
                        <i class="fas fa-tachometer-alt"></i>
                    </a>
                @endauth

                {{-- Burger menu mobile --}}
                <button type="button"
                        class="header-burger"
                        id="burgerMenu"
                        aria-label="Menu de navigation"
                        aria-expanded="false"
                        aria-controls="mainNav">
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                </button>

            </div>
        </div>
    </div>

    {{-- ======================================== --}}
    {{-- BARRE DE RECHERCHE --}}
    {{-- ======================================== --}}
    <div class="header-search-bar" id="searchBar" aria-hidden="true">
        <div class="container">
            <form action="{{ url('/recherche') }}" method="GET" class="search-form" role="search">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-input-icon"></i>
                    <input type="text"
                           name="q"
                           class="search-input"
                           placeholder="Rechercher un projet, un article, un document..."
                           autocomplete="off"
                           aria-label="Terme de recherche">
                    <button type="button"
                            class="search-close"
                            id="searchClose"
                            aria-label="Fermer la recherche"
                            title="Fermer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button type="submit" class="btn btn-primary btn-sm search-submit">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </form>
        </div>
    </div>
</header>

{{-- ======================================== --}}
{{-- OVERLAY MOBILE --}}
{{-- ======================================== --}}
<div class="overlay" id="menuOverlay" aria-hidden="true"></div>