{{-- ============================================ --}}
{{-- SIDEBAR ADMINISTRATEUR — PHOVE --}}
{{-- ============================================ --}}
<aside class="admin-sidebar" id="adminSidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <img src="{{ asset('assets/images/logo-phove.png') }}" alt="PHOVE" class="sidebar-logo-img">
        <div class="sidebar-logo-text">
            <span class="sidebar-logo-name">PHOVE</span>
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
                <a href="{{ route('admin.projets.create') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.projets.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i> Ajouter un projet
                </a>
                <a href="{{ route('admin.projets.budgets') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.projets.budgets') ? 'active' : '' }}">
                    <i class="fas fa-coins"></i> Budgets
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
                <a href="{{ route('admin.membres.create') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.membres.create') ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i> Ajouter un membre
                </a>
                <a href="{{ route('admin.membres.demandes') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.membres.demandes') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> Demandes d'adhésion
                    @php $demandesCount = \App\Models\Membre::where('statut', 'en_attente')->count(); @endphp
                    @if($demandesCount > 0)
                    <span class="sidebar-badge sidebar-badge-danger">{{ $demandesCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.cotisations.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.cotisations.*') ? 'active' : '' }}">
                    <i class="fas fa-coins"></i> Cotisations
                </a>
            </div>
        </div>

        {{-- Dossiers du personnel --}}
        @if(Auth::user()->peutGererDossiers())
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.dossiers.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.dossiers.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-folder-open"></i></span>
                <span class="sidebar-menu-text">Dossiers personnel</span>
            </a>
        </div>
        @endif

        {{-- ============================================ --}}
        {{-- CATÉGORIE : FINANCES --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Finances</div>

        {{-- Dons --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.dons.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.dons.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-hand-holding-heart"></i></span>
                <span class="sidebar-menu-text">Dons reçus</span>
            </a>
        </div>

        {{-- Rapports financiers --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.finances.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.finances.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-chart-pie"></i></span>
                <span class="sidebar-menu-text">Rapports financiers</span>
            </a>
        </div>

        {{-- Salaires --}}
        @if(Auth::user()->peutGererFinances())
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.salaires.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.salaires.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-money-bill-wave"></i></span>
                <span class="sidebar-menu-text">Salaires</span>
            </a>
        </div>
        @endif

        {{-- Stocks --}}
        @if(Auth::user()->peutGererStocks())
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.stocks.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.stocks.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-boxes"></i></span>
                <span class="sidebar-menu-text">Stocks</span>
            </a>
        </div>
        @endif

        {{-- Commission de Contrôle --}}
        @if(Auth::user()->peutGererFinances())
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.controles.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.controles.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-search-dollar"></i></span>
                <span class="sidebar-menu-text">Commission de Contrôle</span>
            </a>
        </div>
        @endif

        {{-- ============================================ --}}
        {{-- CATÉGORIE : CONTENU --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Contenu</div>

        {{-- Publications --}}
        <div class="sidebar-menu-item has-submenu {{ request()->routeIs('admin.articles.*') || request()->routeIs('admin.medias.*') || request()->routeIs('admin.temoignages.*') ? 'open' : '' }}">
            <a href="#" class="sidebar-menu-link">
                <span class="sidebar-menu-icon"><i class="fas fa-newspaper"></i></span>
                <span class="sidebar-menu-text">Publications</span>
                <span class="sidebar-menu-arrow"><i class="fas fa-chevron-right"></i></span>
            </a>
            <div class="sidebar-submenu">
                <a href="{{ route('admin.articles.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.articles.index') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Articles
                </a>
                <a href="{{ route('admin.articles.create') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.articles.create') ? 'active' : '' }}">
                    <i class="fas fa-pen"></i> Rédiger un article
                </a>
                <a href="{{ route('admin.medias.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.medias.index') ? 'active' : '' }}">
                    <i class="fas fa-images"></i> Médiathèque
                </a>
                <a href="{{ route('admin.temoignages.index') }}" class="sidebar-submenu-link {{ request()->routeIs('admin.temoignages.*') ? 'active' : '' }}">
                    <i class="fas fa-quote-right"></i> Témoignages
                </a>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- CATÉGORIE : COMMUNICATION --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Communication</div>

        {{-- Messages --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.contacts.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-envelope-open-text"></i></span>
                <span class="sidebar-menu-text">Contact</span>
                @php $messagesCount = \App\Models\Contact::where('lu', false)->count(); @endphp
                @if($messagesCount > 0)
                <span class="sidebar-badge sidebar-badge-primary">{{ $messagesCount }}</span>
                @endif
            </a>
        </div>

        {{-- Messagerie interne --}}
        @if(Auth::user()->aAccesMessagerie())
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.messages.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-comments"></i></span>
                <span class="sidebar-menu-text">Messagerie</span>
                @php
                    $unreadMessages = \App\Models\Message::where('destinataire_id', Auth::id())
                        ->where('lu', false)
                        ->where('archived_dest', false)
                        ->count();
                @endphp
                @if($unreadMessages > 0)
                    <span class="sidebar-badge sidebar-badge-danger">{{ $unreadMessages }}</span>
                @endif
            </a>
        </div>
        @endif

        {{-- Partenaires --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.partenaires.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.partenaires.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-handshake"></i></span>
                <span class="sidebar-menu-text">Partenaires</span>
            </a>
        </div>

        {{-- ============================================ --}}
        {{-- CATÉGORIE : ORGANISATION --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Organisation</div>

        {{-- Gouvernance --}}
        @if(Auth::user()->peutGererGouvernance())
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.gouvernance.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.gouvernance.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-sitemap"></i></span>
                <span class="sidebar-menu-text">Gouvernance</span>
            </a>
        </div>
        @endif

        {{-- Structures territoriales --}}
        @if(Auth::user()->peutGererStructures())
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.structures.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.structures.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-globe-africa"></i></span>
                <span class="sidebar-menu-text">Structures territoriales</span>
            </a>
        </div>
        @endif

        {{-- Documents officiels --}}
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.documents.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.documents.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-folder"></i></span>
                <span class="sidebar-menu-text">Documents officiels</span>
            </a>
        </div>

        {{-- ============================================ --}}
        {{-- CATÉGORIE : PARAMÈTRES --}}
        {{-- ============================================ --}}
        <div class="sidebar-category">Paramètres</div>

        {{-- Utilisateurs --}}
        @if(Auth::user()->peutGererUtilisateurs())
        <div class="sidebar-menu-item">
            <a href="{{ route('admin.users.index') }}" class="sidebar-menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="sidebar-menu-icon"><i class="fas fa-user-shield"></i></span>
                <span class="sidebar-menu-text">Utilisateurs</span>
            </a>
        </div>
        @endif

        {{-- Paramètres du site --}}
        @if(Auth::user()->peutGererParametres())
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
        <div class="sidebar-version">v1.0.0 — PHOVE</div>
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Réduire le menu">
            <i class="fas fa-chevron-left"></i>
            <span class="sidebar-footer-text">Réduire le menu</span>
        </button>
    </div>
</aside>

{{-- Overlay mobile --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>