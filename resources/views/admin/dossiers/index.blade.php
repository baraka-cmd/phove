@extends('layouts.admin')

@section('title', 'Dossiers du Personnel | PHOVE')

@section('page_title', 'Dossiers du Personnel')
@section('page_icon', 'folder-open')
@section('page_subtitle', 'Suivez la complétude des dossiers du personnel : 12 pièces exigées par l\'Article 65 des Statuts.')

@php
    $breadcrumb = [ 'Dossiers du Personnel' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dossiers.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="projets-stats-grid">
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-folder-open"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $totalDossiers }}</div>
                <div class="projet-stat-label">Total dossiers</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #D1FAE5; color: #065F46;"><i class="fas fa-check-circle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #065F46;">{{ $dossiersComplets }}</div>
                <div class="projet-stat-label">Dossiers complets</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #FEF3C7; color: #92400E;"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #92400E;">{{ $dossiersIncomplets }}</div>
                <div class="projet-stat-label">Dossiers incomplets</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #DBEAFE; color: #1E40AF;"><i class="fas fa-clock"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #1E40AF;">{{ $dossiersEnAttente }}</div>
                <div class="projet-stat-label">En attente</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.dossiers.index') }}" method="GET" class="projets-toolbar">
        <div class="projets-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="dossierSearch" value="{{ request('recherche') }}" 
                   class="projets-search-input" placeholder="Rechercher un membre, un dossier...">
        </div>

        <div class="projets-filtres">
            <select name="statut" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="complet" {{ request('statut') === 'complet' ? 'selected' : '' }}>Complets</option>
                <option value="incomplet" {{ request('statut') === 'incomplet' ? 'selected' : '' }}>Incomplets</option>
                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
            </select>
        </div>

        @if(request('recherche') || request('statut'))
            <a href="{{ route('admin.dossiers.index') }}" class="projets-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.dossiers.create') }}" class="projets-add-btn">
            <i class="fas fa-plus-circle"></i> Nouveau dossier
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES DOSSIERS --}}
    {{-- ============================================ --}}
    <div class="projets-table-card">
        @if($dossiers->count() > 0)
        <div class="table-responsive">
            <table class="table-projets">
                <thead>
                    <tr>
                        <th>Membre</th>
                        <th>Statut</th>
                        <th>Progression</th>
                        <th>Documents fournis</th>
                        <th>Date de création</th>
                        <th>Validation</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dossiers as $dossier)
                    @php
                        $pourcentage = $dossier->pourcentageCompletion();
                        $classeProgression = $pourcentage >= 100 ? 'completed' : ($pourcentage >= 50 ? 'warning' : 'danger');
                    @endphp
                    <tr>
                        {{-- Membre --}}
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 38px; height: 38px; border-radius: 50%; background: #E8F0F8; display: flex; align-items: center; justify-content: center; color: #1A5B9C; font-size: 13px; font-weight: 700; flex-shrink: 0;">
                                    {{ $dossier->membre ? $dossier->membre->initiales() : '?' }}
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #1E293B;">
                                        {{ $dossier->nomMembre() }}
                                    </div>
                                    @if($dossier->membre && $dossier->membre->categorie)
                                    <div style="font-size: 11px; color: #64748B;">
                                        {{ $dossier->membre->categorieLibelle() }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Statut --}}
                        <td>
                            @if($dossier->statut === 'complet')
                                <span class="projet-publication-badge projet-publie">
                                    <i class="fas fa-check-circle"></i> Complet
                                </span>
                            @elseif($dossier->statut === 'incomplet')
                                <span class="projet-publication-badge" style="background: #FEF3C7; color: #92400E;">
                                    <i class="fas fa-exclamation-triangle"></i> Incomplet
                                </span>
                            @else
                                <span class="projet-publication-badge" style="background: #DBEAFE; color: #1E40AF;">
                                    <i class="fas fa-clock"></i> En attente
                                </span>
                            @endif
                        </td>

                        {{-- Progression --}}
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="flex: 1; height: 8px; background: #E2E8F0; border-radius: 4px; overflow: hidden; max-width: 120px;">
                                    <div style="height: 100%; width: {{ $pourcentage }}%; background: {{ $pourcentage >= 100 ? '#10B981' : ($pourcentage >= 50 ? '#F59E0B' : '#EF4444') }}; border-radius: 4px;"></div>
                                </div>
                                <span style="font-size: 12px; font-weight: 700; color: {{ $pourcentage >= 100 ? '#10B981' : ($pourcentage >= 50 ? '#F59E0B' : '#EF4444') }};">
                                    {{ $pourcentage }}%
                                </span>
                            </div>
                        </td>

                        {{-- Documents fournis --}}
                        <td>
                            <span style="font-size: 13px; font-weight: 600; color: #1E293B;">
                                {{ $dossier->documents_fournis_count }} / 12
                            </span>
                            <small style="color: #64748B;">pièces</small>
                        </td>

                        {{-- Date de création --}}
                        <td>
                            <span style="font-size: 13px;">
                                <i class="fas fa-calendar-alt" style="color: #64748B; font-size: 11px;"></i>
                                {{ $dossier->date_creation->format('d/m/Y') }}
                            </span>
                        </td>

                        {{-- Validation --}}
                        <td>
                            @if($dossier->date_validation)
                                <span style="font-size: 12px; color: #10B981;">
                                    <i class="fas fa-check-circle"></i>
                                    {{ $dossier->date_validation->format('d/m/Y') }}
                                </span>
                            @else
                                <span style="color: #94A3B8; font-size: 12px;">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="projet-actions">
                                <a href="{{ route('admin.dossiers.show', $dossier) }}" class="projet-action-btn view" title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.dossiers.destroy', $dossier) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="projet-action-btn delete" 
                                            onclick="return confirm('Supprimer ce dossier et tous ses documents ?');" title="Supprimer">
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
        @if($dossiers->hasPages())
        <div class="projets-pagination">
            <span class="projets-pagination-info">
                Affichage de <strong>{{ $dossiers->firstItem() }}-{{ $dossiers->lastItem() }}</strong> sur <strong>{{ $dossiers->total() }}</strong> dossiers
            </span>
            {{ $dossiers->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="projets-empty">
            <div class="projets-empty-icon"><i class="fas fa-folder-open"></i></div>
            <h4>Aucun dossier trouvé</h4>
            <p>Aucun dossier ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.dossiers.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection