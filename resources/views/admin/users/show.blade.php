@extends('layouts.admin')

@section('title', 'Détail de l\'utilisateur | PHOVE')

@section('page_title', 'Détail utilisateur')
@section('page_icon', 'eye')
@section('page_subtitle', 'Consultez les informations complètes et l\'activité de ' . $user->nom . '.')

@php
    $breadcrumb = ['Utilisateurs' => route('admin.users.index'), $user->nom => null];
    $avatarColors = ['blue', 'green', 'orange', 'red', 'purple'];
    $avatarColor = $avatarColors[crc32($user->nom) % count($avatarColors)];
    
    $roleIcon = match($user->role) {
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

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-users.css') }}">
@endsection

@section('content')

    <div class="user-show-container">

        {{-- Bouton retour --}}
        <a href="{{ route('admin.users.index') }}" class="user-show-back">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>

        {{-- Carte principale --}}
        <div class="user-show-card">

            {{-- ============================================ --}}
            {{-- HEADER --}}
            {{-- ============================================ --}}
            <div class="user-show-header">
                <div class="user-show-avatar">
                    @if($user->photo_profil)
                        <img src="{{ asset('storage/' . $user->photo_profil) }}" alt="{{ $user->nom }}" loading="lazy">
                    @else
                        {{ $user->initiales() }}
                    @endif
                </div>
                <div class="user-show-header-info">
                    <h2>
                        {{ $user->nom }}
                        @if($user->id === Auth::id())
                            <span style="font-size: 11px; background: rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 50px; margin-left: 8px; font-weight: 500;">Vous</span>
                        @endif
                    </h2>
                    <div class="user-show-header-meta">
                        <span class="user-role-badge user-role-{{ $user->role }}" style="background: rgba(255,255,255,0.15); color: #FFFFFF; border-color: rgba(255,255,255,0.2);">
                            <i class="fas fa-{{ $roleIcon }}"></i>
                            {{ $user->roleLibelle() }}
                        </span>
                        <span class="user-statut-badge user-statut-{{ $user->statut }}" style="background: rgba(255,255,255,0.15); color: #FFFFFF;">
                            <i class="fas fa-{{ $user->statut === 'actif' ? 'check-circle' : ($user->statut === 'inactif' ? 'pause-circle' : 'ban') }}"></i>
                            {{ $user->statutLibelle() }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- CORPS --}}
            {{-- ============================================ --}}
            <div class="user-show-body">

                {{-- Mini statistiques d'activité --}}
                <div class="user-show-section">
                    <div class="user-show-section-title">
                        <i class="fas fa-chart-bar"></i> Activité
                    </div>
                    <div class="user-stats-mini-grid">
                        <div class="user-stat-mini">
                            <div class="user-stat-mini-value">{{ $user->articles_count ?? 0 }}</div>
                            <div class="user-stat-mini-label">
                                <i class="fas fa-file-alt" style="font-size: 9px; margin-right: 3px;"></i> Articles
                            </div>
                        </div>
                        <div class="user-stat-mini">
                            <div class="user-stat-mini-value">{{ $user->dons_enregistres_count ?? 0 }}</div>
                            <div class="user-stat-mini-label">
                                <i class="fas fa-hand-holding-heart" style="font-size: 9px; margin-right: 3px;"></i> Dons
                            </div>
                        </div>
                        <div class="user-stat-mini">
                            <div class="user-stat-mini-value">{{ $user->medias_count ?? 0 }}</div>
                            <div class="user-stat-mini-label">
                                <i class="fas fa-images" style="font-size: 9px; margin-right: 3px;"></i> Médias
                            </div>
                        </div>
                        <div class="user-stat-mini">
                            <div class="user-stat-mini-value">{{ $user->cotisations_enregistrees_count ?? 0 }}</div>
                            <div class="user-stat-mini-label">
                                <i class="fas fa-coins" style="font-size: 9px; margin-right: 3px;"></i> Cotisations
                            </div>
                        </div>
                        <div class="user-stat-mini">
                            <div class="user-stat-mini-value">{{ $user->rapports_financiers_count ?? 0 }}</div>
                            <div class="user-stat-mini-label">
                                <i class="fas fa-chart-pie" style="font-size: 9px; margin-right: 3px;"></i> Rapports
                            </div>
                        </div>
                        <div class="user-stat-mini">
                            <div class="user-stat-mini-value">{{ $user->contacts_traites_count ?? 0 }}</div>
                            <div class="user-stat-mini-label">
                                <i class="fas fa-envelope" style="font-size: 9px; margin-right: 3px;"></i> Messages
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Grille d'informations --}}
                <div class="user-show-grid">
                    {{-- Colonne gauche : Informations personnelles --}}
                    <div>
                        <div class="user-show-section">
                            <div class="user-show-section-title">
                                <i class="fas fa-user-circle"></i> Informations personnelles
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #E8F0F8; color: #1A5B9C;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Nom complet</div>
                                    <div class="user-show-field-value">{{ $user->nom }}</div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #DBEAFE; color: #3B82F6;">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Adresse email</div>
                                    <div class="user-show-field-value">
                                        <a href="mailto:{{ $user->email }}" style="color: #1A5B9C; text-decoration: underline;">
                                            {{ $user->email }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #D1FAE5; color: #10B981;">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Téléphone</div>
                                    <div class="user-show-field-value {{ $user->telephone ? '' : 'empty' }}">
                                        @if($user->telephone)
                                            <a href="tel:{{ $user->telephone }}" style="color: #1A5B9C; text-decoration: underline;">
                                                {{ $user->telephone }}
                                            </a>
                                        @else
                                            Non renseigné
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Colonne droite : Rôle & Statut --}}
                    <div>
                        <div class="user-show-section">
                            <div class="user-show-section-title">
                                <i class="fas fa-shield-alt"></i> Rôle & Statut
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #E8F0F8; color: #1A5B9C;">
                                    <i class="fas fa-{{ $roleIcon }}"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Rôle</div>
                                    <div class="user-show-field-value">
                                        <span class="user-role-badge user-role-{{ $user->role }}">
                                            <i class="fas fa-{{ $roleIcon }}"></i>
                                            {{ $user->roleLibelle() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: {{ $user->statut === 'actif' ? '#D1FAE5' : ($user->statut === 'inactif' ? '#FEF3C7' : '#FEE2E2') }}; 
                                    color: {{ $user->statut === 'actif' ? '#10B981' : ($user->statut === 'inactif' ? '#F59E0B' : '#EF4444') }};">
                                    <i class="fas fa-{{ $user->statut === 'actif' ? 'check-circle' : ($user->statut === 'inactif' ? 'pause-circle' : 'ban') }}"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Statut</div>
                                    <div class="user-show-field-value">
                                        <span class="user-statut-badge user-statut-{{ $user->statut }}">
                                            <i class="fas fa-{{ $user->statut === 'actif' ? 'check-circle' : ($user->statut === 'inactif' ? 'pause-circle' : 'ban') }}"></i>
                                            {{ $user->statutLibelle() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #DBEAFE; color: #3B82F6;">
                                    <i class="fas fa-fingerprint"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">ID utilisateur</div>
                                    <div class="user-show-field-value">
                                        <code style="background: #F1F5F9; padding: 2px 8px; border-radius: 4px; font-size: 12px;">#{{ $user->id }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deuxième grille : Historique --}}
                <div class="user-show-grid" style="margin-top: 0;">
                    <div>
                        <div class="user-show-section">
                            <div class="user-show-section-title">
                                <i class="fas fa-clock"></i> Historique du compte
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #D1FAE5; color: #10B981;">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Date de création</div>
                                    <div class="user-show-field-value">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #FEF3C7; color: #F59E0B;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Dernière modification</div>
                                    <div class="user-show-field-value">{{ $user->updated_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #E8F0F8; color: #1A5B9C;">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Dernière connexion</div>
                                    <div class="user-show-field-value {{ $user->derniere_connexion ? '' : 'empty' }}">
                                        @if($user->derniere_connexion)
                                            <span style="{{ $user->derniere_connexion->diffInHours(now()) < 24 ? 'color: #10B981; font-weight: 700;' : '' }}">
                                                {{ $user->derniere_connexion->diffInHours(now()) < 24 
                                                    ? $user->derniere_connexion->diffForHumans() 
                                                    : $user->derniere_connexion->format('d/m/Y à H:i') }}
                                            </span>
                                        @else
                                            Jamais connecté
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="user-show-section">
                            <div class="user-show-section-title">
                                <i class="fas fa-user-check"></i> Permissions
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #D1FAE5; color: #10B981;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Projets</div>
                                    <div class="user-show-field-value">
                                        {{ $user->peutGererProjets() ? '✅ Gestion complète' : '❌ Lecture seule' }}
                                    </div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #D1FAE5; color: #10B981;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Membres</div>
                                    <div class="user-show-field-value">
                                        {{ $user->peutGererMembres() ? '✅ Gestion complète' : '❌ Aucun accès' }}
                                    </div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #D1FAE5; color: #10B981;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Finances</div>
                                    <div class="user-show-field-value">
                                        {{ $user->peutGererFinances() ? '✅ Gestion complète' : '❌ Aucun accès' }}
                                    </div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #D1FAE5; color: #10B981;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Contenu</div>
                                    <div class="user-show-field-value">
                                        {{ $user->peutRedigerContenu() ? '✅ Articles, Médias, Témoignages' : '❌ Aucun accès' }}
                                    </div>
                                </div>
                            </div>

                            <div class="user-show-field">
                                <div class="user-show-field-icon" style="background: #D1FAE5; color: #10B981;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <div class="user-show-field-label">Paramètres</div>
                                    <div class="user-show-field-value">
                                        {{ $user->peutGererParametres() ? '✅ Accès complet' : '❌ Aucun accès' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="user-show-actions">
                <div class="user-show-actions-left">
                    @if($user->id !== Auth::id())
                        @if($user->statut !== 'actif')
                            <form action="{{ route('admin.users.activer', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check-circle"></i> Activer le compte
                                </button>
                            </form>
                        @endif

                        @if($user->statut === 'actif')
                            <form action="{{ route('admin.users.desactiver', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-outline btn-sm">
                                    <i class="fas fa-pause-circle"></i> Désactiver
                                </button>
                            </form>
                            <form action="{{ route('admin.users.suspendre', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-outline btn-sm" style="border-color: #F59E0B; color: #F59E0B;">
                                    <i class="fas fa-ban"></i> Suspendre
                                </button>
                            </form>
                        @endif
                    @else
                        <span style="font-size: 12px; color: #64748B; padding: 8px;">
                            <i class="fas fa-info-circle"></i> Vous ne pouvez pas modifier votre propre statut.
                        </span>
                    @endif
                </div>

                <div class="user-show-actions-right">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('admin.users.password', $user) }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-key"></i> Changer le mot de passe
                    </a>
                    @if($user->id !== Auth::id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement le compte de {{ $user->nom }} ?\n\nCette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-accent btn-sm">
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>

@endsection