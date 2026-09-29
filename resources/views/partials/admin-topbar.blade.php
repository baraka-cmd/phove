{{-- ============================================ --}}
{{-- TOPBAR ADMINISTRATEUR — PHOVE --}}
{{-- ============================================ --}}
@php
    $user = Auth::user();
    $userInitiales = $user
        ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->nom ?? 'AD', 0, 2))
        : 'AD';

    // URL de la photo de profil via la route custom media.show
    $userPhotoUrl = ($user && !empty($user->photo_profil))
        ? route('media.show', ['path' => ltrim($user->photo_profil, '/')])
        : null;

    // Libellé du rôle
    $userRoleLabel = 'Utilisateur';
    if ($user) {
        if (method_exists($user, 'roleLibelle')) {
            $userRoleLabel = $user->roleLibelle();
        } else {
            $userRoleLabel = match($user->role) {
                'super_admin'      => 'Super Admin',
                'admin'            => 'Administrateur',
                'dg'               => 'Directeur Général',
                'chef_mission'     => 'Chef de Mission',
                'charge_programme' => 'Chargé de Programme',
                'secretaire'       => 'Secrétaire',
                'comptable'        => 'Comptable',
                'caissier'         => 'Caissier',
                'editeur'          => 'Éditeur',
                default            => ucfirst($user->role ?? 'Utilisateur'),
            };
        }
    }
@endphp

<header class="admin-topbar" id="adminTopbar">

    {{-- Bouton toggle mobile --}}
    <button class="admin-topbar-btn" id="sidebarToggleMobile" title="Menu">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Bouton réduire sidebar (desktop) --}}
    <button class="admin-topbar-btn" id="sidebarToggle" title="Réduire la barre latérale">
        <i class="fas fa-indent"></i>
    </button>

    {{-- Fil d'Ariane --}}
    <nav class="topbar-breadcrumb" aria-label="Fil d'Ariane">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-home" title="Tableau de bord">
            <i class="fas fa-home"></i>
        </a>
        <i class="fas fa-chevron-right"></i>
        @if(isset($breadcrumb) && !empty($breadcrumb))
            @foreach($breadcrumb as $key => $item)
                @if(is_array($item) && isset($item['url'], $item['label']))
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @elseif(is_string($item))
                    <span>{{ $item }}</span>
                @elseif(is_array($item) && isset($item['label']))
                    <span>{{ $item['label'] }}</span>
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

            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-dropdown-header">
                    <span class="notif-dropdown-title">Notifications</span>
                    <a href="#" class="notif-dropdown-mark">Tout marquer comme lu</a>
                </div>
                <div class="notif-dropdown-list">
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

                    <div class="notif-item">
                        <div class="notif-item-icon notif-icon-message">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="notif-item-content">
                            <p class="notif-item-text"><strong>Nouveau message</strong> de DMP-RDC</p>
                            <span class="notif-item-time">Il y a 3 heures</span>
                        </div>
                    </div>

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
                    @if($userPhotoUrl)
                        <img src="{{ $userPhotoUrl }}"
                             alt="{{ $user->nom ?? 'Utilisateur' }}"
                             loading="lazy"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="user-avatar-fallback">{{ $userInitiales }}</span>
                    @else
                        <span>{{ $userInitiales }}</span>
                    @endif
                </div>
                <div class="user-info">
                    <span class="user-name">{{ \Illuminate\Support\Str::limit($user->nom ?? 'Admin', 15) }}</span>
                    <span class="user-role">{{ $userRoleLabel }}</span>
                </div>
            </button>

            {{-- Dropdown profil --}}
            <div class="user-dropdown" id="userDropdown">
                <div class="user-dropdown-header">
                    <div class="user-dropdown-avatar">
                        @if($userPhotoUrl)
                            <img src="{{ $userPhotoUrl }}"
                                 alt="{{ $user->nom ?? 'Utilisateur' }}"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="user-avatar-fallback">{{ $userInitiales }}</span>
                        @else
                            <span>{{ $userInitiales }}</span>
                        @endif
                    </div>
                    <div class="user-dropdown-info">
                        <span class="user-dropdown-name">{{ $user->nom ?? 'Administrateur' }}</span>
                        <span class="user-dropdown-email">{{ $user->email ?? 'admin@phove.org' }}</span>
                    </div>
                </div>

                {{-- ✅ Lien vers la fiche utilisateur (profil) --}}
                @if($user)
                    <a href="{{ route('admin.users.show', $user) }}" class="user-dropdown-link">
                        <i class="fas fa-user-circle"></i> Mon profil
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="user-dropdown-link">
                        <i class="fas fa-user-edit"></i> Modifier mes informations
                    </a>
                    <a href="{{ route('admin.users.password', $user) }}" class="user-dropdown-link">
                        <i class="fas fa-key"></i> Changer mon mot de passe
                    </a>
                @endif

                <div class="user-dropdown-divider"></div>

                <a href="{{ url('/') }}" class="user-dropdown-link" target="_blank" rel="noopener">
                    <i class="fas fa-external-link-alt"></i> Voir le site public
                </a>

                <div class="user-dropdown-divider"></div>

                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                    @csrf
                    <a href="#" class="user-dropdown-link logout" 
                       onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </form>
            </div>
        </div>
    </div>
</header>