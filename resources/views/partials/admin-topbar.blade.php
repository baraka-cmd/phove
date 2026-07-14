{{-- ============================================ --}}
{{-- TOPBAR ADMINISTRATEUR — AHD-RDC --}}
{{-- ============================================ --}}
<header class="admin-topbar" id="adminTopbar">

    {{-- Bouton toggle mobile --}}
    <button class="topbar-toggle-btn" id="sidebarToggleMobile" title="Menu">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Bouton réduire sidebar (desktop) --}}
    <button class="topbar-toggle-btn" id="sidebarToggle" title="Réduire la barre latérale">
        <i class="fas fa-indent"></i>
    </button>

    {{-- Fil d'Ariane --}}
    <nav class="topbar-breadcrumb" aria-label="Fil d'Ariane">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-home" title="Tableau de bord">
            <i class="fas fa-home"></i>
        </a>
        <i class="fas fa-chevron-right"></i>
        @if(isset($breadcrumb))
            @foreach($breadcrumb as $key => $item)
                @if(is_array($item))
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @else
                    <span>{{ $item }}</span>
                @endif
                @if(!$loop->last)
                    <i class="fas fa-chevron-right"></i>
                @endif
            @endforeach
        @else
            <span>Tableau de bord</span>
        @endif
    </nav>

    {{-- Section droite --}}
    <div class="topbar-right">

        {{-- Recherche rapide --}}
        <div class="topbar-search">
            <button class="topbar-search-btn" id="topbarSearchBtn" title="Rechercher (Ctrl+K)">
                <i class="fas fa-search"></i>
            </button>
            <div class="topbar-search-input-wrapper" id="topbarSearchWrapper">
                <input type="text" class="topbar-search-input" id="topbarSearchInput" placeholder="Rechercher un projet, un membre, un article...">
                <button class="topbar-search-close" id="topbarSearchClose" title="Fermer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Mode sombre --}}
        <button class="topbar-darkmode-btn" id="darkModeBtn" title="Mode sombre / Mode clair">
            <i class="fas fa-moon"></i>
        </button>

        {{-- Notifications --}}
        <div class="topbar-notifications">
            <button class="topbar-notif-btn" id="notifBtn" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-badge">8</span>
            </button>

            {{-- Dropdown notifications --}}
            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-dropdown-header">
                    <span class="notif-dropdown-title">Notifications</span>
                    <a href="#" class="notif-dropdown-mark">Tout marquer comme lu</a>
                </div>
                <div class="notif-dropdown-list">
                    {{-- Notification 1 --}}
                    <div class="notif-item unread">
                        <div class="notif-item-icon notif-icon-don">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="notif-item-content">
                            <p class="notif-item-text"><strong>Nouveau don</strong> de 50 $ reçu de Jean M.</p>
                            <span class="notif-item-time">Il y a 10 minutes</span>
                        </div>
                        <span class="notif-item-dot"></span>
                    </div>

                    {{-- Notification 2 --}}
                    <div class="notif-item unread">
                        <div class="notif-item-icon notif-icon-membre">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="notif-item-content">
                            <p class="notif-item-text"><strong>Nouvelle adhésion</strong> de Marie K.</p>
                            <span class="notif-item-time">Il y a 1 heure</span>
                        </div>
                        <span class="notif-item-dot"></span>
                    </div>

                    {{-- Notification 3 --}}
                    <div class="notif-item">
                        <div class="notif-item-icon notif-icon-message">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="notif-item-content">
                            <p class="notif-item-text"><strong>Nouveau message</strong> de DMP-RDC</p>
                            <span class="notif-item-time">Il y a 3 heures</span>
                        </div>
                    </div>

                    {{-- Notification 4 --}}
                    <div class="notif-item unread">
                        <div class="notif-item-icon notif-icon-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notif-item-content">
                            <p class="notif-item-text"><strong>Cotisation en retard</strong> pour 3 membres</p>
                            <span class="notif-item-time">Il y a 5 heures</span>
                        </div>
                        <span class="notif-item-dot"></span>
                    </div>

                    {{-- Notification 5 --}}
                    <div class="notif-item">
                        <div class="notif-item-icon notif-icon-don">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="notif-item-content">
                            <p class="notif-item-text"><strong>Nouveau don</strong> de 100 $ reçu anonyme</p>
                            <span class="notif-item-time">Hier</span>
                        </div>
                    </div>
                </div>
                <div class="notif-dropdown-footer">
                    <a href="#"><i class="fas fa-arrow-right"></i> Voir toutes les notifications</a>
                </div>
            </div>
        </div>

        {{-- Profil utilisateur --}}
        <div class="topbar-user">
            <button class="topbar-user-btn" id="userBtn" title="Mon profil">
                <div class="user-avatar">
                    <span>{{ Str::upper(Str::substr(Auth::user()->nom ?? 'AD', 0, 2)) }}</span>
                </div>
                <div class="user-info">
                    <span class="user-name">{{ Str::limit(Auth::user()->nom ?? 'Admin', 15) }}</span>
                    <span class="user-role">
                        @if(Auth::user()->role === 'super_admin')
                            Super Admin
                        @elseif(Auth::user()->role === 'admin')
                            Administrateur
                        @else
                            Éditeur
                        @endif
                    </span>
                </div>
            </button>

            {{-- Dropdown profil --}}
            <div class="user-dropdown" id="userDropdown">
                <div class="user-dropdown-header">
                    <div class="user-dropdown-avatar">
                        <span>{{ Str::upper(Str::substr(Auth::user()->nom ?? 'AD', 0, 2)) }}</span>
                    </div>
                    <div class="user-dropdown-info">
                        <span class="user-dropdown-name">{{ Auth::user()->nom ?? 'Administrateur' }}</span>
                        <span class="user-dropdown-email">{{ Auth::user()->email ?? 'admin@ahd-rdc.org' }}</span>
                    </div>
                </div>
                <a href="#" class="user-dropdown-link">
                    <i class="fas fa-user-circle"></i> Mon profil
                </a>
                <a href="#" class="user-dropdown-link">
                    <i class="fas fa-cog"></i> Paramètres du compte
                </a>
                <div class="user-dropdown-divider"></div>
                <a href="{{ url('/') }}" class="user-dropdown-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Voir le site
                </a>
                <div class="user-dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                    @csrf
                    <a href="#" class="user-dropdown-link logout" onclick="document.getElementById('logoutForm').submit(); return false;">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </form>
            </div>
        </div>
    </div>
</header>