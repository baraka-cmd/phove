@extends('layouts.admin')

@section('title', 'Gestion des partenaires | PHOVE')

@section('page_title', 'Partenaires')
@section('page_icon', 'handshake')
@section('page_subtitle', 'Gérez les partenaires de PHOVE : FAO, WFP, OCHA, UNESCO, GPE, RDC et plus.')

@php
    $breadcrumb = [ 'Partenaires' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-partenaires.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="partenaires-stats-grid">
        <div class="partenaire-stat-card">
            <div class="partenaire-stat-icon"><i class="fas fa-handshake"></i></div>
            <div class="partenaire-stat-content">
                <div class="partenaire-stat-value">{{ $totalPartenaires }}</div>
                <div class="partenaire-stat-label">Partenaires</div>
            </div>
        </div>
        <div class="partenaire-stat-card">
            <div class="partenaire-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="partenaire-stat-content">
                <div class="partenaire-stat-value">{{ $partenairesActifs }}</div>
                <div class="partenaire-stat-label">Partenaires actifs</div>
            </div>
        </div>
        <div class="partenaire-stat-card">
            <div class="partenaire-stat-icon"><i class="fas fa-globe-africa"></i></div>
            <div class="partenaire-stat-content">
                <div class="partenaire-stat-value">{{ $partenairesInternationaux }}</div>
                <div class="partenaire-stat-label">Internationaux</div>
            </div>
        </div>
        <div class="partenaire-stat-card">
            <div class="partenaire-stat-icon"><i class="fas fa-project-diagram"></i></div>
            <div class="partenaire-stat-content">
                <div class="partenaire-stat-value">{{ $totalCollaborations }}</div>
                <div class="partenaire-stat-label">Collaborations</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.partenaires.index') }}" method="GET" class="partenaires-toolbar">
        <div class="partenaires-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="partenaireSearch" value="{{ request('recherche') }}" 
                   class="partenaires-search-input" placeholder="Rechercher un partenaire, un sigle...">
        </div>

        <div class="partenaires-filtres">
            <select name="type" class="partenaires-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="bailleur_fonds" {{ request('type') === 'bailleur_fonds' ? 'selected' : '' }}>Bailleur de fonds</option>
                <option value="technique" {{ request('type') === 'technique' ? 'selected' : '' }}>Partenaire technique</option>
                <option value="local" {{ request('type') === 'local' ? 'selected' : '' }}>Partenaire local</option>
                <option value="international" {{ request('type') === 'international' ? 'selected' : '' }}>Partenaire international</option>
                <option value="institutionnel" {{ request('type') === 'institutionnel' ? 'selected' : '' }}>Partenaire institutionnel</option>
            </select>

            <select name="statut" class="partenaires-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>

        @if(request('recherche') || request('type') || request('statut'))
            <a href="{{ route('admin.partenaires.index') }}" class="partenaires-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.partenaires.create') }}" class="partenaires-add-btn">
            <i class="fas fa-plus-circle"></i> Nouveau partenaire
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- GRILLE DES PARTENAIRES --}}
    {{-- ============================================ --}}
    @if($partenaires->count() > 0)
    <div class="partenaires-grid">
        @foreach($partenaires as $partenaire)
        <div class="partenaire-card">
            {{-- En-tête --}}
            <div class="partenaire-card-header">
                <div class="partenaire-logo">
                    @if($partenaire->logo)
                        <img src="{{ img_url($partenaire->logo) }}"
                             alt="{{ $partenaire->nom }}"
                             loading="lazy">
                    @else
                        <span>{{ $partenaire->sigleOuInitiales() }}</span>
                    @endif
                </div>
                <div class="partenaire-info">
                    <h3 class="partenaire-nom">
                        {{ $partenaire->nom }}
                        @if($partenaire->sigle)
                            <small class="partenaire-sigle">({{ $partenaire->sigle }})</small>
                        @endif
                    </h3>
                    <span class="partenaire-type partenaire-type-{{ $partenaire->type }}">
                        <i class="fas fa-tag"></i> {{ $partenaire->typeLibelle() }}
                    </span>
                    <span class="partenaire-statut-badge partenaire-statut-{{ $partenaire->statut }}">
                        <i class="fas fa-circle"></i> {{ $partenaire->statut === 'actif' ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>

            {{-- Corps --}}
            <div class="partenaire-card-body">
                @if($partenaire->description)
                <p class="partenaire-description">{{ Str::limit($partenaire->description, 100) }}</p>
                @endif
                <div class="partenaire-meta">
                    @if($partenaire->email)
                    <span><i class="fas fa-envelope"></i> {{ $partenaire->email }}</span>
                    @endif
                    @if($partenaire->telephone)
                    <span><i class="fas fa-phone-alt"></i> {{ $partenaire->telephone }}</span>
                    @endif
                    @if($partenaire->site_web)
                    <span><i class="fas fa-globe"></i> {{ Str::limit($partenaire->site_web, 30) }}</span>
                    @endif
                    @if($partenaire->date_debut)
                    <span><i class="fas fa-calendar-alt"></i> Depuis {{ $partenaire->date_debut->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>

            {{-- Pied --}}
            <div class="partenaire-card-footer">
                <span class="partenaire-projets-count">
                    <i class="fas fa-project-diagram"></i> 
                    {{ $partenaire->projets_count }} projet(s)
                </span>
                <div class="partenaire-actions">
                    <a href="{{ route('admin.partenaires.show', $partenaire) }}" class="partenaire-action-btn view" title="Voir le détail">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.partenaires.edit', $partenaire) }}" class="partenaire-action-btn edit" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.partenaires.destroy', $partenaire) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="partenaire-action-btn delete btn-delete-partenaire" 
                                data-nom="{{ $partenaire->nom }}" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($partenaires->hasPages())
    <div style="display: flex; justify-content: center; margin-top: var(--spacing-2xl);">
        {{ $partenaires->links('pagination.custom') }}
    </div>
    @endif

    @else
    <div class="partenaires-empty">
        <div class="partenaires-empty-icon"><i class="fas fa-handshake"></i></div>
        <h4>Aucun partenaire trouvé</h4>
        <p>Aucun partenaire ne correspond à vos critères de recherche.</p>
        <a href="{{ route('admin.partenaires.index') }}" class="btn btn-outline btn-sm mt-3">
            <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
        </a>
    </div>
    @endif

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-partenaires.js') }}"></script>
@endsection