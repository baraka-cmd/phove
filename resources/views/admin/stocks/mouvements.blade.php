@extends('layouts.admin')

@section('title', 'Mouvements de Stock | PHOVE')

@section('page_title', 'Mouvements de Stock')
@section('page_icon', 'exchange-alt')
@section('page_subtitle', 'Historique complet des entrées et sorties de stock.')

@php
    $breadcrumb = [
        ['label' => 'Stocks', 'url' => route('admin.stocks.index')],
        'Mouvements'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 2 CARTES --}}
    {{-- ============================================ --}}
    <div class="projets-stats-grid" style="grid-template-columns: repeat(2, 1fr); max-width: 600px;">
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #D1FAE5; color: #065F46;"><i class="fas fa-arrow-down"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #065F46;">{{ number_format($totalEntrees, 0, ',', ' ') }}</div>
                <div class="projet-stat-label">Total entrées</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #FEE2E2; color: #B91C1C;"><i class="fas fa-arrow-up"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #B91C1C;">{{ number_format($totalSorties, 0, ',', ' ') }}</div>
                <div class="projet-stat-label">Total sorties</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.stocks.mouvements') }}" method="GET" class="projets-toolbar">
        <div class="projets-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="mouvementSearch" value="{{ request('recherche') }}" 
                   class="projets-search-input" placeholder="Rechercher un motif, un destinataire, une référence...">
        </div>

        <div class="projets-filtres">
            <select name="type" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="entree" {{ request('type') === 'entree' ? 'selected' : '' }}>Entrées</option>
                <option value="sortie" {{ request('type') === 'sortie' ? 'selected' : '' }}>Sorties</option>
            </select>

            <select name="stock_id" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les articles</option>
                @foreach($stocks as $stockItem)
                    <option value="{{ $stockItem->id }}" {{ request('stock_id') == $stockItem->id ? 'selected' : '' }}>
                        {{ Str::limit($stockItem->designation, 40) }}
                    </option>
                @endforeach
            </select>

            <input type="date" name="date" class="projets-filtre-select" value="{{ request('date') }}" 
                   onchange="this.form.submit()" title="Filtrer par date">
        </div>

        @if(request('recherche') || request('type') || request('stock_id') || request('date'))
            <a href="{{ route('admin.stocks.mouvements') }}" class="projets-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES MOUVEMENTS --}}
    {{-- ============================================ --}}
    <div class="projets-table-card">
        @if($mouvements->count() > 0)
        <div class="table-responsive">
            <table class="table-projets">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Motif</th>
                        <th>Destinataire / Fournisseur</th>
                        <th>Référence</th>
                        <th>Projet</th>
                        <th>Effectué par</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mouvements as $mouvement)
                    <tr>
                        {{-- Date --}}
                        <td>
                            <span style="font-size: 13px; white-space: nowrap;">
                                <i class="fas fa-calendar-alt" style="color: #64748B; font-size: 11px;"></i>
                                {{ $mouvement->date_mouvement->format('d/m/Y') }}
                            </span>
                        </td>

                        {{-- Type --}}
                        <td>
                            @if($mouvement->estEntree())
                                <span class="projet-publication-badge projet-publie">
                                    <i class="fas fa-arrow-down"></i> Entrée
                                </span>
                            @else
                                <span class="projet-publication-badge" style="background: #FEE2E2; color: #B91C1C;">
                                    <i class="fas fa-arrow-up"></i> Sortie
                                </span>
                            @endif
                        </td>

                        {{-- Article --}}
                        <td>
                            <span style="font-size: 13px; font-weight: 600; color: #1E293B;">
                                {{ $mouvement->stock ? $mouvement->stock->designation : '—' }}
                            </span>
                            @if($mouvement->stock)
                            <br><small style="color: #64748B;">{{ $mouvement->stock->reference }}</small>
                            @endif
                        </td>

                        {{-- Quantité --}}
                        <td>
                            <span style="font-size: 14px; font-weight: 700; color: {{ $mouvement->estEntree() ? '#10B981' : '#EF4444' }};">
                                {{ $mouvement->quantiteFormatee() }}
                            </span>
                        </td>

                        {{-- Motif --}}
                        <td>
                            <span style="font-size: 13px; color: #334155;">{{ $mouvement->motif ?? '—' }}</span>
                        </td>

                        {{-- Destinataire / Fournisseur --}}
                        <td>
                            <span style="font-size: 13px; color: #334155;">{{ $mouvement->destinataire ?? '—' }}</span>
                        </td>

                        {{-- Référence --}}
                        <td>
                            @if($mouvement->reference_document)
                                <span style="font-size: 12px; color: #1A5B9C;">
                                    <i class="fas fa-file-alt"></i> {{ $mouvement->reference_document }}
                                </span>
                            @else
                                <span style="color: #94A3B8; font-size: 12px;">—</span>
                            @endif
                        </td>

                        {{-- Projet --}}
                        <td>
                            @if($mouvement->projet)
                                <span style="font-size: 12px; color: #1A5B9C;">
                                    <i class="fas fa-project-diagram"></i>
                                    {{ Str::limit($mouvement->projet->titre, 30) }}
                                </span>
                            @else
                                <span style="color: #94A3B8; font-size: 12px;">—</span>
                            @endif
                        </td>

                        {{-- Effectué par --}}
                        <td>
                            <span style="font-size: 12px; color: #64748B;">
                                <i class="fas fa-user"></i> {{ $mouvement->nomEffectuePar() }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($mouvements->hasPages())
        <div class="projets-pagination">
            <span class="projets-pagination-info">
                Affichage de <strong>{{ $mouvements->firstItem() }}-{{ $mouvements->lastItem() }}</strong> sur <strong>{{ $mouvements->total() }}</strong> mouvements
            </span>
            {{ $mouvements->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="projets-empty">
            <div class="projets-empty-icon"><i class="fas fa-exchange-alt"></i></div>
            <h4>Aucun mouvement trouvé</h4>
            <p>Aucun mouvement ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.stocks.mouvements') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection