@extends('layouts.admin')

@section('title', 'Gestion des Stocks | PHOVE')

@section('page_title', 'Gestion des Stocks')
@section('page_icon', 'boxes')
@section('page_subtitle', 'Gérez les vivres, médicaments, matériels et autres articles en stock.')

@php
    $breadcrumb = [ 'Stocks' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="projets-stats-grid">
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-boxes"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $totalArticles }}</div>
                <div class="projet-stat-label">Total articles</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #D1FAE5; color: #065F46;"><i class="fas fa-check-circle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #065F46;">{{ $articlesDisponibles }}</div>
                <div class="projet-stat-label">Disponibles</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #FEF3C7; color: #92400E;"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #92400E;">{{ $articlesFaibles }}</div>
                <div class="projet-stat-label">Stock faible</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #FEE2E2; color: #B91C1C;"><i class="fas fa-times-circle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #B91C1C;">{{ $articlesRupture }}</div>
                <div class="projet-stat-label">Rupture de stock</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.stocks.index') }}" method="GET" class="projets-toolbar">
        <div class="projets-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="stockSearch" value="{{ request('recherche') }}" 
                   class="projets-search-input" placeholder="Rechercher un article, une référence...">
        </div>

        <div class="projets-filtres">
            <select name="categorie" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Toutes les catégories</option>
                <option value="vivres" {{ request('categorie') === 'vivres' ? 'selected' : '' }}>Vivres</option>
                <option value="medicaments" {{ request('categorie') === 'medicaments' ? 'selected' : '' }}>Médicaments</option>
                <option value="habillement" {{ request('categorie') === 'habillement' ? 'selected' : '' }}>Habillement</option>
                <option value="abris" {{ request('categorie') === 'abris' ? 'selected' : '' }}>Abris</option>
                <option value="materiel" {{ request('categorie') === 'materiel' ? 'selected' : '' }}>Matériel</option>
                <option value="semences" {{ request('categorie') === 'semences' ? 'selected' : '' }}>Semences</option>
                <option value="outils" {{ request('categorie') === 'outils' ? 'selected' : '' }}>Outils aratoires</option>
                <option value="autre" {{ request('categorie') === 'autre' ? 'selected' : '' }}>Autre</option>
            </select>

            <select name="statut" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="disponible" {{ request('statut') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                <option value="faible" {{ request('statut') === 'faible' ? 'selected' : '' }}>Stock faible</option>
                <option value="rupture" {{ request('statut') === 'rupture' ? 'selected' : '' }}>Rupture</option>
            </select>
        </div>

        @if(request('recherche') || request('categorie') || request('statut'))
            <a href="{{ route('admin.stocks.index') }}" class="projets-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.stocks.mouvements') }}" class="btn btn-outline btn-sm" style="margin-left: 8px;">
            <i class="fas fa-exchange-alt"></i> Mouvements
        </a>

        <a href="{{ route('admin.stocks.create') }}" class="projets-add-btn">
            <i class="fas fa-plus-circle"></i> Nouvel article
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES STOCKS --}}
    {{-- ============================================ --}}
    <div class="projets-table-card">
        @if($stocks->count() > 0)
        <div class="table-responsive">
            <table class="table-projets">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Quantité actuelle</th>
                        <th>Seuil d'alerte</th>
                        <th>Statut</th>
                        <th>Emplacement</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $stock)
                    <tr>
                        {{-- Article --}}
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 38px; height: 38px; border-radius: 8px; background: #E8F0F8; display: flex; align-items: center; justify-content: center; color: #1A5B9C; flex-shrink: 0;">
                                    <i class="fas fa-{{ $stock->categorie === 'vivres' ? 'utensils' : ($stock->categorie === 'medicaments' ? 'pills' : ($stock->categorie === 'habillement' ? 'tshirt' : ($stock->categorie === 'abris' ? 'home' : ($stock->categorie === 'materiel' ? 'tools' : ($stock->categorie === 'semences' ? 'seedling' : 'box'))))) }}"></i>
                                </div>
                                <div>
                                    <div style="font-size: 13px; font-weight: 600; color: #1E293B;">{{ $stock->designation }}</div>
                                    <div style="font-size: 11px; color: #64748B;">Réf : {{ $stock->reference }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Catégorie --}}
                        <td>
                            <span class="projet-badge" style="background: #E8F0F8; color: #1A5B9C;">
                                <i class="fas fa-tag"></i> {{ $stock->categorieLibelle() }}
                            </span>
                        </td>

                        {{-- Quantité actuelle --}}
                        <td>
                            <span style="font-size: 14px; font-weight: 700; color: #1E293B;">
                                {{ number_format($stock->quantite_actuelle, 2, ',', ' ') }}
                            </span>
                            <small style="color: #64748B;">{{ $stock->unite }}</small>
                        </td>

                        {{-- Seuil d'alerte --}}
                        <td>
                            @if($stock->seuil_alerte !== null)
                                <span style="font-size: 13px; color: #F59E0B;">
                                    <i class="fas fa-bell"></i> {{ number_format($stock->seuil_alerte, 2, ',', ' ') }} {{ $stock->unite }}
                                </span>
                            @else
                                <span style="color: #94A3B8; font-size: 12px;">—</span>
                            @endif
                        </td>

                        {{-- Statut --}}
                        <td>
                            @if($stock->statut === 'disponible')
                                <span class="projet-publication-badge projet-publie">
                                    <i class="fas fa-check-circle"></i> Disponible
                                </span>
                            @elseif($stock->statut === 'faible')
                                <span class="projet-publication-badge" style="background: #FEF3C7; color: #92400E;">
                                    <i class="fas fa-exclamation-triangle"></i> Stock faible
                                </span>
                            @else
                                <span class="projet-publication-badge" style="background: #FEE2E2; color: #B91C1C;">
                                    <i class="fas fa-times-circle"></i> Rupture
                                </span>
                            @endif
                        </td>

                        {{-- Emplacement --}}
                        <td>
                            <span style="font-size: 13px; color: #334155;">
                                <i class="fas fa-map-marker-alt" style="font-size: 11px; color: #64748B;"></i>
                                {{ $stock->emplacement ?? '—' }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="projet-actions">
                                <a href="{{ route('admin.stocks.show', $stock) }}" class="projet-action-btn view" title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.stocks.formEntree', $stock) }}" class="projet-action-btn edit" title="Entrée de stock" style="background: #D1FAE5; color: #065F46;">
                                    <i class="fas fa-arrow-down"></i>
                                </a>
                                <a href="{{ route('admin.stocks.formSortie', $stock) }}" class="projet-action-btn edit" title="Sortie de stock" style="background: #FEF3C7; color: #92400E;">
                                    <i class="fas fa-arrow-up"></i>
                                </a>
                                <a href="{{ route('admin.stocks.edit', $stock) }}" class="projet-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.stocks.destroy', $stock) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="projet-action-btn delete" 
                                            onclick="return confirm('Supprimer cet article et tous ses mouvements ?');" title="Supprimer">
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
        @if($stocks->hasPages())
        <div class="projets-pagination">
            <span class="projets-pagination-info">
                Affichage de <strong>{{ $stocks->firstItem() }}-{{ $stocks->lastItem() }}</strong> sur <strong>{{ $stocks->total() }}</strong> articles
            </span>
            {{ $stocks->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="projets-empty">
            <div class="projets-empty-icon"><i class="fas fa-boxes"></i></div>
            <h4>Aucun article trouvé</h4>
            <p>Aucun article ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection