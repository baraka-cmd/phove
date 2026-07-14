{{-- ============================================ --}}
{{-- SIDEBAR ADMINISTRATEUR — AHD-RDC --}}
{{-- ============================================ --}}
<aside class="admin-sidebar" id="adminSidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <img src="{{ asset('assets/images/logo-ahd.png') }}" alt="AHD-RDC" class="sidebar-logo-img">
        <div class="sidebar-logo-text">
            <span class="sidebar-logo-name">AHD<span>-</span>RDC</span>
            <span class="sidebar-logo-sub">Administration</span>
        </div>
    </div>

    {{-- Menu de navigation --}}
    <nav class="sidebar-menu" id="sidebarMenu">

        {{-- ============================================ --}}
        {{-- CATÉGORIE : GÉNÉRAL --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Général</div>

        {{-- Tableau de bord --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="sidebar-menu-text">Tableau de bord</span>
            </a>
        </div>

        {{-- ============================================ --}}
        {{-- CATÉGORIE : GESTION --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Gestion</div>

        {{-- Projets --}}
        <div class="sidebar-menu-item has-submenu {{ request()->routeIs('admin.projets.*') ? 'open' : '' }}">
            <a href="#" class="sidebar-menu-link">
                <span class="sidebar-menu-icon"><i class="fas fa-project-diagram"></i></span>
                <span class="sidebar-menu-text">Projets</span>
                <span class="sidebar-menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </a>
            <div class="sidebar-submenu">
                <a href="{{ route('admin.projets.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.projets.index') ? 'active' : '' }}">
                    <i class="fas fa-list-ul"></i> Tous les projets
                </a>
                <a href="#" class="sidebar-submenu-link">
                    <i class="fas fa-plus-circle"></i> Ajouter un projet
                </a>
                <a href="#" class="sidebar-submenu-link">
                    <i class="fas fa-tags"></i> Domaines
                </a>
            </div>
        </div>

        {{-- Membres --}}
        <div class="sidebar-menu-item has-submenu {{ request()->routeIs('admin.membres.*') ? 'open' : '' }}">
            <a href="#" class="sidebar-menu-link">
                <span class="sidebar-menu-icon"><i class="fas fa-users"></i></span>
                <span class="sidebar-menu-text">Membres</span>
                <span class="sidebar-menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </a>
            <div class="sidebar-submenu">
                <a href="{{ route('admin.membres.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.membres.index') ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i> Tous les membres
                </a>
                <a href="#" class="sidebar-submenu-link">
                    <i class="fas fa-user-plus"></i> Ajouter un membre
                </a>
                <a href="#" class="sidebar-submenu-link">
                    <i class="fas fa-clock"></i> Demandes d'adhésion
                    <span class="sidebar-badge sidebar-badge-danger">3</span>
                </a>
                <a href="#" class="sidebar-submenu-link">
                    <i class="fas fa-coins"></i> Cotisations
                </a>
            </div>
        </div>

        {{-- Bénéficiaires --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.beneficiaires.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.beneficiaires.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-hands-helping"></i></span>
                <span class="sidebar-menu-text">Bénéficiaires</span>
            </a>
        </div>

        {{-- ============================================ --}}
        {{-- CATÉGORIE : CONTENU --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Contenu</div>

        {{-- Publications --}}
        <div class="sidebar-menu-item has-submenu {{ request()->routeIs('admin.articles.*') || request()->routeIs('admin.medias.*') ? 'open' : '' }}">
            <a href="#" class="sidebar-menu-link">
                <span class="sidebar-menu-icon"><i class="fas fa-newspaper"></i></span>
                <span class="sidebar-menu-text">Publications</span>
                <span class="sidebar-menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </a>
            <div class="sidebar-submenu">
                <a href="{{ route('admin.articles.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.articles.index') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Articles
                </a>
                <a href="#" class="sidebar-submenu-link">
                    <i class="fas fa-pen"></i> Rédiger un article
                </a>
                <a href="{{ route('admin.medias.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.medias.index') ? 'active' : '' }}">
                    <i class="fas fa-images"></i> Médiathèque
                </a>
                <a href="#" class="sidebar-submenu-link">
                    <i class="fas fa-quote-right"></i> Témoignages
                </a>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- CATÉGORIE : FINANCES --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Finances</div>

        {{-- Dons --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.dons.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.dons.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-hand-holding-heart"></i></span>
                <span class="sidebar-menu-text">Dons reçus</span>
                <span class="sidebar-badge sidebar-badge-success">Nouveau</span>
            </a>
        </div>

        {{-- Rapports financiers --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.finances.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.finances.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-chart-pie"></i></span>
                <span class="sidebar-menu-text">Rapports financiers</span>
            </a>
        </div>

        {{-- ============================================ --}}
        {{-- CATÉGORIE : COMMUNICATION --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Communication</div>

        {{-- Messages --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.contacts.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-envelope-open-text"></i></span>
                <span class="sidebar-menu-text">Messages</span>
                <span class="sidebar-badge sidebar-badge-primary">5</span>
            </a>
        </div>

        {{-- Partenaires --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.partenaires.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.partenaires.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-handshake"></i></span>
                <span class="sidebar-menu-text">Partenaires</span>
            </a>
        </div>

        {{-- ============================================ --}}
        {{-- CATÉGORIE : PARAMÈTRES --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Paramètres</div>

        {{-- Utilisateurs --}}
        @if(Auth::user()->role === 'super_admin')
        <div class="sidebar-menu-item">
            <a href="#" class="sidebar-menu-link">
                <span class="sidebar-menu-icon"><i class="fas fa-user-shield"></i></span>
                <span class="sidebar-menu-text">Utilisateurs</span>
            </a>
        </div>
        @endif

        {{-- Gouvernance --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.gouvernance.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.gouvernance.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-sitemap"></i></span>
                <span class="sidebar-menu-text">Gouvernance</span>
            </a>
        </div>

        {{-- Paramètres du site --}}
        @if(Auth::user()->role === 'super_admin')
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.parametres.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.parametres.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-cog"></i></span>
                <span class="sidebar-menu-text">Paramètres</span>
            </a>
        </div>
        @endif

    </nav>

    {{-- Footer sidebar --}}
    <div class="sidebar-footer">
        <div class="sidebar-version">v1.0.0 — AHD-RDC</div>
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Réduire le menu">
            <i class="fas fa-chevron-left"></i>
            <span class="sidebar-footer-text">Réduire le menu</span>
        </button>
    </div>
</aside>

{{-- Overlay mobile --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>