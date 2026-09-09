{{-- ============================================ --}}
{{-- HEADER — PHOVE --}}
{{-- Logo + Menu navigation + Bouton Don --}}
{{-- ============================================ --}}
<header class="header" id="header">
    <div class="container">
        <div class="header-inner">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="header-logo" title="PHOVE — Retour à l'accueil">
                <img src="{{ asset('assets/images/logo-phove.png') }}" alt="Logo PHOVE" class="header-logo-img">
                <div class="header-logo-text">
                    <span class="header-logo-name">PHOVE</span>
                    <span class="header-logo-subtitle">Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies</span>
                </div>
            </a>

            {{-- Menu principal --}}
            <nav class="header-nav" id="mainNav">
                <ul class="header-menu">

                    {{-- Accueil --}}
                    <li class="header-menu-item">
                        <a href="{{ url('/') }}" class="header-menu-link {{ request()->is('/') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Accueil</span>
                        </a>
                    </li>

                    {{-- Qui sommes-nous ? --}}
                    <li class="header-menu-item has-dropdown">
                        <a href="#" class="header-menu-link">
                            <i class="fas fa-users"></i>
                            <span>Qui sommes-nous ?</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="header-dropdown">
                            <li><a href="{{ url('/qui-sommes-nous') }}" class="dropdown-link">Présentation</a></li>
                            <li><a href="{{ url('/mission-vision') }}" class="dropdown-link">Mission & Vision</a></li>
                            <li><a href="{{ url('/nos-valeurs') }}" class="dropdown-link">Nos Valeurs</a></li>
                            <li><a href="{{ url('/gouvernance') }}" class="dropdown-link">Gouvernance</a></li>
                            <li><a href="{{ url('/organigramme') }}" class="dropdown-link">Organigramme</a></li>
                            <li><a href="{{ url('/partenaires') }}" class="dropdown-link">Nos Partenaires</a></li>
                        </ul>
                    </li>

                    {{-- Nos Actions --}}
                    <li class="header-menu-item has-dropdown">
                        <a href="#" class="header-menu-link">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Nos Actions</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="header-dropdown header-dropdown-large">
                            <li><a href="{{ url('/projets') }}" class="dropdown-link"><i class="fas fa-list"></i> Tous les projets</a></li>
                            <li class="dropdown-divider"></li>
                            <li><a href="{{ url('/projets?domaine=sante_nutrition') }}" class="dropdown-link"><i class="fas fa-heartbeat"></i> Santé-Nutrition</a></li>
                            <li><a href="{{ url('/projets?domaine=education') }}" class="dropdown-link"><i class="fas fa-graduation-cap"></i> Éducation</a></li>
                            <li><a href="{{ url('/projets?domaine=protection') }}" class="dropdown-link"><i class="fas fa-shield-alt"></i> Protection</a></li>
                            <li><a href="{{ url('/projets?domaine=wash') }}" class="dropdown-link"><i class="fas fa-tint"></i> Eau, Hygiène & Assainissement</a></li>
                            <li><a href="{{ url('/projets?domaine=securite_alimentaire') }}" class="dropdown-link"><i class="fas fa-seedling"></i> Sécurité Alimentaire</a></li>
                            <li><a href="{{ url('/projets?domaine=environnement') }}" class="dropdown-link"><i class="fas fa-tree"></i> Environnement</a></li>
                            <li><a href="{{ url('/projets?domaine=paix') }}" class="dropdown-link"><i class="fas fa-dove"></i> Paix & Cohésion sociale</a></li>
                            <li><a href="{{ url('/domaines-intervention') }}" class="dropdown-link"><i class="fas fa-shapes"></i> Domaines d'intervention</a></li>
                        </ul>
                    </li>

                    {{-- Actualités --}}
                    <li class="header-menu-item">
                        <a href="{{ url('/actualites') }}" class="header-menu-link {{ request()->is('actualites*') ? 'active' : '' }}">
                            <i class="fas fa-newspaper"></i>
                            <span>Actualités</span>
                        </a>
                    </li>

                    {{-- Transparence --}}
                    <li class="header-menu-item has-dropdown">
                        <a href="#" class="header-menu-link">
                            <i class="fas fa-search-dollar"></i>
                            <span>Transparence</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="header-dropdown">
                            <li><a href="{{ route('transparence', ['tab' => 'financiers']) }}" class="dropdown-link">Rapports financiers</a></li>
                            <li><a href="{{ route('transparence', ['tab' => 'annuels']) }}" class="dropdown-link">Rapports annuels</a></li>
                            <li><a href="{{ route('transparence', ['tab' => 'documents']) }}" class="dropdown-link">Documents officiels</a></li>
                        </ul>
                    </li>

                    {{-- Impliquez-vous --}}
                    <li class="header-menu-item has-dropdown">
                        <a href="#" class="header-menu-link">
                            <i class="fas fa-hands-helping"></i>
                            <span>Impliquez-vous</span>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="header-dropdown">
                            <li><a href="{{ url('/faire-un-don') }}" class="dropdown-link"><i class="fas fa-heart text-accent"></i> Faire un don</a></li>
                            <li><a href="{{ url('/devenir-membre') }}" class="dropdown-link">Devenir membre</a></li>
                            <li><a href="{{ url('/devenir-benevole') }}" class="dropdown-link">Devenir bénévole</a></li>
                            <li><a href="{{ url('/devenir-partenaire') }}" class="dropdown-link">Devenir partenaire</a></li>
                            <li><a href="{{ url('/partenaires') }}" class="dropdown-link">Nos Partenaires</a></li>
                        </ul>
                    </li>

                    {{-- Contact --}}
                    <li class="header-menu-item">
                        <a href="{{ route('contact') }}" class="header-menu-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Contact</span>
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- Actions header --}}
            <div class="header-actions">

                {{-- Barre de recherche --}}
                <button class="header-search-btn" id="searchToggle" title="Rechercher">
                    <i class="fas fa-search"></i>
                </button>

                {{-- Bouton Don --}}
                <a href="{{ url('/faire-un-don') }}" class="btn btn-accent btn-sm header-donate-btn">
                    <i class="fas fa-heart"></i>
                    <span>Faire un don</span>
                </a>

                {{-- Espace admin (si connecté) --}}
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="header-admin-btn" title="Accéder au dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                    </a>
                @endauth

                {{-- Burger menu mobile --}}
                <button class="header-burger" id="burgerMenu" aria-label="Menu de navigation" title="Menu">
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                </button>
            </div>

        </div>
    </div>

    {{-- Barre de recherche (cachée par défaut) --}}
    <div class="header-search-bar" id="searchBar">
        <div class="container">
            <form action="{{ url('/recherche') }}" method="GET" class="search-form">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-input-icon"></i>
                    <input type="text" name="q" class="search-input" placeholder="Rechercher un projet, un article, un document..." autocomplete="off">
                    <button type="button" class="search-close" id="searchClose" title="Fermer la recherche">
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

{{-- Overlay pour menu mobile --}}
<div class="overlay" id="menuOverlay"></div>