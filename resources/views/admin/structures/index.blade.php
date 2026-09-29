@extends('layouts.admin')

@section('title', 'Structures Territoriales | PHOVE')

@section('page_title', 'Structures Territoriales')
@section('page_icon', 'sitemap')
@section('page_subtitle', 'Gérez les stations (pays), bases (provinces) et antennes (territoires/communes) de PHOVE.')

@php
    $breadcrumb = [ 'Structures Territoriales' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-structures.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="projets-stats-grid">
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-sitemap"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $totalStructures }}</div>
                <div class="projet-stat-label">Total structures</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-flag"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $stations }}</div>
                <div class="projet-stat-label">Stations (Pays)</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-building"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $bases }}</div>
                <div class="projet-stat-label">Bases (Provinces)</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $antennes }}</div>
                <div class="projet-stat-label">Antennes (Territoires)</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.structures.index') }}" method="GET" class="projets-toolbar">
        <div class="projets-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="structureSearch" value="{{ request('recherche') }}" 
                   class="projets-search-input" placeholder="Rechercher une structure, un pays, une province...">
        </div>

        <div class="projets-filtres">
            <select name="type" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="station" {{ request('type') === 'station' ? 'selected' : '' }}>Station (Pays)</option>
                <option value="base" {{ request('type') === 'base' ? 'selected' : '' }}>Base (Province)</option>
                <option value="antenne" {{ request('type') === 'antenne' ? 'selected' : '' }}>Antenne (Territoire/Commune)</option>
            </select>

            <select name="statut" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
            </select>
        </div>

        @if(request('recherche') || request('type') || request('statut'))
            <a href="{{ route('admin.structures.index') }}" class="projets-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.structures.create') }}" class="projets-add-btn">
            <i class="fas fa-plus-circle"></i> Nouvelle structure
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES STRUCTURES --}}
    {{-- ============================================ --}}
    <div class="projets-table-card">
        @if($structures->count() > 0)
        <div class="table-responsive">
            <table class="table-projets">
                <thead>
                    <tr>
                        <th>Structure</th>
                        <th>Type</th>
                        <th>Localisation</th>
                        <th>Parent</th>
                        <th>Postes</th>
                        <th>Statut</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($structures as $structure)
                    <tr>
                        {{-- Nom --}}
                        <td>
                            <div class="cell-with-icon">
                                <div class="cell-icon">
                                    <i class="fas fa-{{ $structure->type === 'station' ? 'flag' : ($structure->type === 'base' ? 'building' : 'map-marker-alt') }}"></i>
                                </div>
                                <div class="cell-info">
                                    <div class="cell-title">{{ $structure->nom }}</div>
                                    @if($structure->adresse)
                                        <div class="cell-subtitle">{{ Str::limit($structure->adresse, 40) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Type --}}
                        <td>
                            <span class="projet-badge badge-type-{{ $structure->type }}">
                                <i class="fas fa-{{ $structure->type === 'station' ? 'flag' : ($structure->type === 'base' ? 'building' : 'map-marker-alt') }}"></i>
                                {{ $structure->typeLibelle() }}
                            </span>
                        </td>

                        {{-- Localisation --}}
                        <td>
                            <span class="cell-text">{{ $structure->localisationComplete() }}</span>
                        </td>

                        {{-- Parent --}}
                        <td>
                            @if($structure->parent)
                                <span class="cell-text">
                                    <i class="fas fa-level-up-alt cell-parent-icon"></i>
                                    {{ $structure->parent->nom }}
                                </span>
                            @else
                                <span class="cell-empty">—</span>
                            @endif
                        </td>

                        {{-- Postes --}}
                        <td>
                            <span class="cell-count">{{ $structure->organes_count }}</span>
                            <span class="cell-count-label">poste(s)</span>
                        </td>

                        {{-- Statut --}}
                        <td>
                            <span class="projet-publication-badge {{ $structure->statut === 'actif' ? 'projet-publie' : 'projet-non-publie' }}">
                                <i class="fas fa-circle badge-dot"></i>
                                {{ $structure->statut === 'actif' ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="projet-actions">
                                <a href="{{ route('admin.structures.show', $structure) }}" class="projet-action-btn view" title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.structures.edit', $structure) }}" class="projet-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.structures.destroy', $structure) }}" method="POST" class="form-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="projet-action-btn delete" 
                                            onclick="return confirm('Supprimer cette structure ?');" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($structures->hasPages())
        <div class="projets-pagination">
            <span class="projets-pagination-info">
                Affichage de <strong>{{ $structures->firstItem() }}-{{ $structures->lastItem() }}</strong> sur <strong>{{ $structures->total() }}</strong> structures
            </span>
            {{ $structures->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="projets-empty">
            <div class="projets-empty-icon"><i class="fas fa-sitemap"></i></div>
            <h4>Aucune structure trouvée</h4>
            <p>Aucune structure ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.structures.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection