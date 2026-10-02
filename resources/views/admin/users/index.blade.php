@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs | PHOVE')

@section('page_title', 'Utilisateurs')
@section('page_icon', 'users-cog')
@section('page_subtitle', 'Gérez les comptes : création, attribution des rôles hiérarchiques PHOVE, activation et suspension.')

@php
    $breadcrumb = ['Utilisateurs' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-users.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="users-stats-grid">
        <div class="user-stat-card">
            <div class="user-stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="user-stat-content">
                <div class="user-stat-value">{{ $totalUsers }}</div>
                <div class="user-stat-label">Total utilisateurs</div>
            </div>
        </div>
        <div class="user-stat-card">
            <div class="user-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="user-stat-content">
                <div class="user-stat-value">{{ $usersActifs }}</div>
                <div class="user-stat-label">Comptes actifs</div>
            </div>
        </div>
        <div class="user-stat-card">
            <div class="user-stat-icon">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="user-stat-content">
                <div class="user-stat-value">{{ $usersInactifs }}</div>
                <div class="user-stat-label">Comptes inactifs</div>
            </div>
        </div>
        <div class="user-stat-card">
            <div class="user-stat-icon">
                <i class="fas fa-ban"></i>
            </div>
            <div class="user-stat-content">
                <div class="user-stat-value">{{ $usersSuspendus }}</div>
                <div class="user-stat-label">Comptes suspendus</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.users.index') }}" method="GET" class="users-toolbar">
        <div class="users-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="userSearch" value="{{ request('recherche') }}" 
                   class="users-search-input" placeholder="Rechercher par nom, email...">
        </div>

        <div class="users-filtres">
            <select name="role" class="users-filtre-select">
                <option value="">Tous les rôles</option>
                @foreach(\App\Enums\Roles::all() as $code => $libelle)
                    <option value="{{ $code }}" {{ request('role') === $code ? 'selected' : '' }}>{{ $libelle }}</option>
                @endforeach
            </select>

            <select name="statut" class="users-filtre-select">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                <option value="suspendu" {{ request('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
            </select>
        </div>

        @if(request('recherche') || request('role') || request('statut'))
            <a href="{{ route('admin.users.index') }}" class="users-reset-btn" title="Réinitialiser les filtres">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.users.create') }}" class="users-add-btn">
            <i class="fas fa-user-plus"></i> Nouvel utilisateur
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES UTILISATEURS --}}
    {{-- ============================================ --}}
    <div class="users-table-card">
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table-users">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Dernière connexion</th>
                        <th>Créé le</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $utilisateur)
                    @php
                        $avatarColors = ['blue', 'green', 'orange', 'red', 'purple'];
                        $avatarColor = $avatarColors[crc32($utilisateur->nom) % count($avatarColors)];
                        $isCurrentUser = $utilisateur->id === Auth::id();
                    @endphp
                    <tr>
                        {{-- Utilisateur --}}
                        <td>
                            <div class="user-cell">
                                <div class="user-cell-avatar {{ $avatarColor }}">
                                    @if($utilisateur->photo_profil)
                                        <img src="{{ img_url($utilisateur->photo_profil) }}"
                                            alt="{{ $utilisateur->nom }}"
                                            loading="lazy">
                                    @else
                                        {{ $utilisateur->initiales() }}
                                    @endif
                                </div>
                                <div class="user-cell-info">
                                    <div class="user-cell-nom">
                                        {{ $utilisateur->nom }}
                                        @if($isCurrentUser)
                                            <span style="font-size: 10px; background: #E8F0F8; color: #1A5B9C; padding: 1px 6px; border-radius: 50px; margin-left: 4px;">Vous</span>
                                        @endif
                                    </div>
                                    <div class="user-cell-email">
                                        <i class="fas fa-envelope" style="font-size: 9px; margin-right: 3px;"></i> {{ $utilisateur->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Rôle --}}
                        <td>
                            @php
                                $roleIcon = match($utilisateur->role) {
                                    'super_admin' => 'crown',
                                    'dg' => 'user-tie',
                                    'chef_mission' => 'globe-africa',
                                    'charge_programme' => 'clipboard-list',
                                    'secretaire' => 'file-alt',
                                    'comptable' => 'calculator',
                                    'caissier' => 'cash-register',
                                    'editeur' => 'user-edit',
                                    'utilisateur' => 'user',
                                    default => 'user',
                                };
                            @endphp
                            <span class="user-role-badge user-role-{{ $utilisateur->role }}">
                                <i class="fas fa-{{ $roleIcon }}"></i>
                                {{ $utilisateur->roleLibelle() }}
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td>
                            <span class="user-statut-badge user-statut-{{ $utilisateur->statut }}">
                                <i class="fas fa-{{ $utilisateur->statut === 'actif' ? 'check-circle' : ($utilisateur->statut === 'inactif' ? 'pause-circle' : 'ban') }}"></i>
                                {{ $utilisateur->statutLibelle() }}
                            </span>
                        </td>

                        {{-- Dernière connexion --}}
                        <td>
                            @if($utilisateur->derniere_connexion)
                                <span class="user-last-login {{ $utilisateur->derniere_connexion->diffInHours(now()) < 24 ? 'recent' : '' }}">
                                    <i class="fas fa-clock"></i> 
                                    {{ $utilisateur->derniere_connexion->diffInHours(now()) < 24 
                                        ? $utilisateur->derniere_connexion->diffForHumans() 
                                        : $utilisateur->derniere_connexion->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <span style="font-size: 12px; color: #94A3B8;">Jamais connecté</span>
                            @endif
                        </td>

                        {{-- Créé le --}}
                        <td>
                            <span style="font-size: 12px;">
                                {{ $utilisateur->created_at->format('d/m/Y') }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="user-actions">
                                <a href="{{ route('admin.users.show', $utilisateur) }}" class="user-action-btn view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $utilisateur) }}" class="user-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.users.password', $utilisateur) }}" class="user-action-btn password" title="Changer le mot de passe">
                                    <i class="fas fa-key"></i>
                                </a>

                                @if(!$isCurrentUser)
                                    @if($utilisateur->statut === 'inactif' || $utilisateur->statut === 'suspendu')
                                        <form action="{{ route('admin.users.activer', $utilisateur) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="user-action-btn activate" title="Activer le compte">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($utilisateur->statut === 'actif')
                                        <form action="{{ route('admin.users.desactiver', $utilisateur) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="user-action-btn deactivate" title="Désactiver le compte">
                                                <i class="fas fa-pause-circle"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.suspendre', $utilisateur) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="user-action-btn suspend btn-delete-user" 
                                                    data-nom="{{ $utilisateur->nom }}" title="Suspendre le compte">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.users.destroy', $utilisateur) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="user-action-btn delete btn-delete-user" 
                                                data-nom="{{ $utilisateur->nom }}" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size: 10px; color: #64748B; padding: 6px;">Vous</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="users-pagination">
            {{ $users->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="users-empty">
            <div class="users-empty-icon"><i class="fas fa-users"></i></div>
            <h4>Aucun utilisateur trouvé</h4>
            <p>
                @if(request('recherche') || request('role') || request('statut'))
                    Aucun utilisateur ne correspond à vos critères de recherche.
                @else
                    Aucun utilisateur n'a encore été créé.
                @endif
            </p>
            @if(request('recherche') || request('role') || request('statut'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
                </a>
            @else
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus"></i> Créer un utilisateur
                </a>
            @endif
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.users-filtre-select').forEach(select => {
                select.addEventListener('change', function () {
                    this.closest('form').submit();
                });
            });

            const searchInput = document.getElementById('userSearch');
            let searchTimeout;
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                });
            }

            document.querySelectorAll('.btn-delete-user').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const nom = this.getAttribute('data-nom') || 'cet utilisateur';
                    if (!confirm('Êtes-vous sûr de vouloir supprimer/suspendre le compte de "' + nom + '" ?\n\nCette action est irréversible.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endsection