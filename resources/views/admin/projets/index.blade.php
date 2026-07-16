@extends('layouts.admin')

@section('title', 'Gestion des projets')

@section('page_title', 'Projets')
@section('page_icon', 'project-diagram')
@section('page_subtitle', 'Gérez l\'ensemble des projets humanitaires : création, modification, publication.')

@php
    $breadcrumb = [ 'Projets' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-projets-index.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI --}}
    {{-- ============================================ --}}
    <div class="projets-stats-grid">
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-project-diagram"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $totalProjets }}</div>
                <div class="projet-stat-label">Total projets</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-spinner"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $projetsEnCours }}</div>
                <div class="projet-stat-label">En cours</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $projetsTermines }}</div>
                <div class="projet-stat-label">Terminés</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-clock"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $projetsEnPreparation }}</div>
                <div class="projet-stat-label">En préparation</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.projets.index') }}" method="GET" class="projets-toolbar">
        <div class="projets-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="projetSearch" value="{{ request('recherche') }}" 
                   class="projets-search-input" placeholder="Rechercher un projet...">
        </div>

        <div class="projets-filtres">
            <select name="domaine" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les domaines</option>
                <option value="sante_nutrition" {{ request('domaine') === 'sante_nutrition' ? 'selected' : '' }}>Santé-Nutrition</option>
                <option value="education" {{ request('domaine') === 'education' ? 'selected' : '' }}>Éducation</option>
                <option value="protection" {{ request('domaine') === 'protection' ? 'selected' : '' }}>Protection</option>
                <option value="wash" {{ request('domaine') === 'wash' ? 'selected' : '' }}>Eau & Assainissement</option>
                <option value="securite_alimentaire" {{ request('domaine') === 'securite_alimentaire' ? 'selected' : '' }}>Sécurité Alimentaire</option>
                <option value="communication" {{ request('domaine') === 'communication' ? 'selected' : '' }}>Communication</option>
                <option value="environnement" {{ request('domaine') === 'environnement' ? 'selected' : '' }}>Environnement</option>
                <option value="paix" {{ request('domaine') === 'paix' ? 'selected' : '' }}>Paix & Cohésion</option>
                <option value="genre" {{ request('domaine') === 'genre' ? 'selected' : '' }}>Genre</option>
            </select>

            <select name="statut" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="termine" {{ request('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="en_preparation" {{ request('statut') === 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                <option value="suspendu" {{ request('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
            </select>

            <select name="zone" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Toutes les zones</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone }}" {{ request('zone') === $zone ? 'selected' : '' }}>{{ $zone }}</option>
                @endforeach
            </select>
        </div>

        @if(request('recherche') || request('domaine') || request('statut') || request('zone'))
            <a href="{{ route('admin.projets.index') }}" class="projets-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.projets.create') }}" class="projets-add-btn">
            <i class="fas fa-plus-circle"></i> Nouveau projet
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES PROJETS --}}
    {{-- ============================================ --}}
    <div class="projets-table-card">
        @if($projets->count() > 0)
        <div class="table-responsive">
            <table class="table-projets">
                <thead>
                    <tr>
                        <th>Projet</th>
                        <th>Domaine</th>
                        <th>Zone</th>
                        <th>Période</th>
                        <th>Budget</th>
                        <th>Statut</th>
                        <th>Publié</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projets as $projet)
                    <tr>
                        {{-- Projet --}}
                        <td>
                            <div class="projet-cell-titre">
                                <div class="projet-cell-image">
                                    @if($projet->image_principale)
                                        <img src="{{ asset('storage/' . $projet->image_principale) }}" alt="{{ $projet->titre }}">
                                    @else
                                        <div class="projet-cell-image-placeholder">
                                            <i class="fas fa-project-diagram"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="projet-cell-info">
                                    <div class="projet-cell-nom">{{ Str::limit($projet->titre, 60) }}</div>
                                    <div class="projet-cell-zone">
                                        <i class="fas fa-map-marker-alt"></i> {{ $projet->zone_geographique }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Domaine --}}
                        <td>
                            @php
                                $domaineClass = match($projet->domaine) {
                                    'sante_nutrition' => 'sante', 'education' => 'education',
                                    'protection' => 'protection', 'wash' => 'wash',
                                    'securite_alimentaire' => 'alimentaire', 'communication' => 'communication',
                                    'environnement' => 'environnement', 'paix' => 'paix', 'genre' => 'genre',
                                    default => 'sante'
                                };
                            @endphp
                            <span class="projet-badge projet-badge-{{ $domaineClass }}">{{ $projet->domaineLibelle() }}</span>
                        </td>

                        {{-- Zone --}}
                        <td>
                            <span style="font-size: 13px;">{{ $projet->zone_geographique }}</span>
                            @if($projet->province)
                                <br><small style="color: #888;">{{ $projet->province }}</small>
                            @endif
                        </td>

                        {{-- Période --}}
                        <td>
                            <span style="font-size: 13px; white-space: nowrap;">
                                <i class="fas fa-calendar-alt" style="color: var(--color-text-muted); font-size: 11px;"></i> 
                                {{ $projet->date_debut->format('m/Y') }}
                                @if($projet->date_fin) — {{ $projet->date_fin->format('m/Y') }} @endif
                            </span>
                        </td>

                        {{-- Budget --}}
                        <td>
                            <span class="projet-budget">
                                {{ number_format($projet->budget_utilise, 0, ',', ' ') }} $
                            </span>
                            <small>sur {{ number_format($projet->budget_total, 0, ',', ' ') }} $</small>
                        </td>

                        {{-- Statut --}}
                        <td>
                            <span class="projet-statut-badge projet-statut-{{ $projet->statut }}">
                                <i class="fas fa-{{ $projet->statut === 'en_cours' ? 'spinner fa-pulse' : ($projet->statut === 'termine' ? 'check-circle' : 'clock') }}"></i>
                                {{ $projet->statutLibelle() }}
                            </span>
                        </td>

                        {{-- Publié --}}
                        <td>
                            <span class="projet-publication-badge {{ $projet->publier ? 'projet-publie' : 'projet-non-publie' }}">
                                <i class="fas fa-{{ $projet->publier ? 'eye' : 'eye-slash' }}"></i>
                                {{ $projet->publier ? 'Publié' : 'Non publié' }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="projet-actions">
                                <a href="{{ route('admin.projets.show', $projet) }}" class="projet-action-btn view" title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.projets.edit', $projet) }}" class="projet-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.projets.togglePublier', $projet) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="projet-action-btn publish" title="{{ $projet->publier ? 'Dépublier' : 'Publier' }}">
                                        <i class="fas fa-{{ $projet->publier ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.projets.destroy', $projet) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="projet-action-btn delete btn-delete-projet" 
                                            data-titre="{{ $projet->titre }}" title="Supprimer">
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

        @if($projets->hasPages())
        <div class="projets-pagination">
            <span class="projets-pagination-info">
                Affichage de <strong>{{ $projets->firstItem() }}-{{ $projets->lastItem() }}</strong> sur <strong>{{ $projets->total() }}</strong> projets
            </span>
            {{ $projets->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="projets-empty">
            <div class="projets-empty-icon"><i class="fas fa-project-diagram"></i></div>
            <h4>Aucun projet trouvé</h4>
            <p>Aucun projet ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.projets.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-projets-index.js') }}"></script>
@endsection