@extends('layouts.admin')

@section('title', 'Rapports financiers')

@section('page_title', 'Rapports financiers')
@section('page_icon', 'chart-pie')
@section('page_subtitle', 'Tableau de bord financier : entrées, sorties, solde, rapports générés.')

@php
    $breadcrumb = [ 'Finances' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-finances.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="finances-stats-grid">
        <div class="finance-stat-card">
            <div class="finance-stat-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="finance-stat-content">
                <div class="finance-stat-value">{{ number_format($totalEntrees, 0, ',', ' ') }} $</div>
                <div class="finance-stat-label">Total des entrées</div>
                <div class="finance-stat-sub positive">Dons + Cotisations</div>
            </div>
        </div>
        <div class="finance-stat-card">
            <div class="finance-stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="finance-stat-content">
                <div class="finance-stat-value">{{ number_format($totalSorties, 0, ',', ' ') }} $</div>
                <div class="finance-stat-label">Total des sorties</div>
                <div class="finance-stat-sub negative">Budgets projets utilisés</div>
            </div>
        </div>
        <div class="finance-stat-card">
            <div class="finance-stat-icon"><i class="fas fa-balance-scale"></i></div>
            <div class="finance-stat-content">
                <div class="finance-stat-value" style="color: {{ $solde >= 0 ? 'var(--color-success)' : 'var(--color-accent)' }};">
                    {{ number_format($solde, 0, ',', ' ') }} $
                </div>
                <div class="finance-stat-label">Solde</div>
                <div class="finance-stat-sub {{ $solde >= 0 ? 'positive' : 'negative' }}">
                    {{ $solde >= 0 ? 'Excédentaire' : 'Déficitaire' }}
                </div>
            </div>
        </div>
        <div class="finance-stat-card">
            <div class="finance-stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="finance-stat-content">
                <div class="finance-stat-value">{{ number_format($totalEntreesMois, 0, ',', ' ') }} $</div>
                <div class="finance-stat-label">Entrées ce mois</div>
                <div class="finance-stat-sub">{{ now()->translatedFormat('F Y') }}</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SYNTHÈSE MENSUELLE --}}
    {{-- ============================================ --}}
    <div class="synthese-grid">
        {{-- Entrées --}}
        <div class="synthese-card">
            <h4><i class="fas fa-sign-in-alt"></i> Détail des entrées (ce mois)</h4>
            <div class="synthese-row">
                <span class="synthese-label">Dons reçus</span>
                <span class="synthese-value positive">{{ number_format($totalDonsMois, 0, ',', ' ') }} $</span>
            </div>
            <div class="synthese-row">
                <span class="synthese-label">Cotisations</span>
                <span class="synthese-value positive">{{ number_format($totalCotisationsMois, 0, ',', ' ') }} $</span>
            </div>
            <div class="synthese-total">
                <span>Total entrées</span>
                <span class="synthese-value positive">{{ number_format($totalEntreesMois, 0, ',', ' ') }} $</span>
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="synthese-card">
            <h4><i class="fas fa-chart-bar"></i> Statistiques globales</h4>
            <div class="synthese-row">
                <span class="synthese-label">Total dons (confirmés)</span>
                <span class="synthese-value">{{ number_format($totalDons, 0, ',', ' ') }} $</span>
            </div>
            <div class="synthese-row">
                <span class="synthese-label">Total cotisations</span>
                <span class="synthese-value">{{ number_format($totalCotisations, 0, ',', ' ') }} $</span>
            </div>
            <div class="synthese-row">
                <span class="synthese-label">Nombre de donateurs</span>
                <span class="synthese-value">{{ $nombreDonateurs }}</span>
            </div>
            <div class="synthese-row">
                <span class="synthese-label">Membres à jour (ce mois)</span>
                <span class="synthese-value">{{ $nombreMembresCotisations }}</span>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRAPHIQUES --}}
    {{-- ============================================ --}}
    <div class="finances-charts-grid">
        {{-- Barres empilées : Entrées par mois --}}
        <div class="finances-widget">
            <div class="finances-widget-header">
                <h3 class="finances-widget-title">
                    <i class="fas fa-chart-bar"></i> Entrées mensuelles (12 derniers mois)
                </h3>
                <span class="widget-badge primary">Dons + Cotisations</span>
            </div>
            <div class="finances-widget-body">
                <div style="height: 320px;">
                    <canvas id="financesStackedBarChart" 
                            data-labels='@json($moisLabels)' 
                            data-dons='@json($donsMensuels)'
                            data-cotisations='@json($cotisationsMensuelles)'></canvas>
                </div>
            </div>
        </div>

        {{-- Anneau : Répartition des entrées --}}
        <div class="finances-widget">
            <div class="finances-widget-header">
                <h3 class="finances-widget-title">
                    <i class="fas fa-chart-pie"></i> Répartition des entrées
                </h3>
            </div>
            <div class="finances-widget-body">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div style="width: 180px; height: 180px; flex-shrink: 0; position: relative;">
                        <canvas id="financesDonutChart" 
                                data-labels='@json($entreesLabels)' 
                                data-values='@json($entreesValues)'
                                data-colors='@json($entreesColors)'></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <span style="font-size: 1.1rem; font-weight: 800; color: #1a1a1a; display: block;" id="donutCenterValue">0 $</span>
                            <span style="font-size: 10px; color: #888;">total entrées</span>
                        </div>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px; font-size: 13px;">
                            <span style="width: 12px; height: 12px; border-radius: 3px; background: #28a745; flex-shrink: 0;"></span>
                            <span style="color: #555;">Dons</span>
                            <span style="margin-left: auto; font-weight: 700; color: #1a1a1a;">{{ number_format($totalDons, 0, ',', ' ') }} $</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; font-size: 13px;">
                            <span style="width: 12px; height: 12px; border-radius: 3px; background: #16558F; flex-shrink: 0;"></span>
                            <span style="color: #555;">Cotisations</span>
                            <span style="margin-left: auto; font-weight: 700; color: #1a1a1a;">{{ number_format($totalCotisations, 0, ',', ' ') }} $</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- RAPPORTS GÉNÉRÉS --}}
    {{-- ============================================ --}}
    <div class="finances-table-card">
        <div class="finances-table-header">
            <h3 class="finances-table-title">
                <i class="fas fa-file-invoice"></i> Rapports financiers générés
            </h3>
            <a href="{{ route('admin.finances.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Nouveau rapport
            </a>
        </div>

        @if($rapports->count() > 0)
        <div class="table-responsive">
            <table class="table-finances">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th>Entrées</th>
                        <th>Sorties</th>
                        <th>Solde</th>
                        <th>Statut</th>
                        <th>Public</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapports as $rapport)
                    <tr>
                        <td>
                            <span style="font-weight: 600; color: #1a1a1a;">{{ $rapport->titre }}</span>
                        </td>
                        <td>
                            <span class="finance-type-badge finance-type-{{ $rapport->type }}">
                                {{ $rapport->typeLibelle() }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size: 13px;">{{ $rapport->periode }}</span>
                        </td>
                        <td>
                            <span style="color: var(--color-success); font-weight: 600;">
                                {{ number_format($rapport->total_entrees, 0, ',', ' ') }} $
                            </span>
                        </td>
                        <td>
                            <span style="color: var(--color-accent); font-weight: 600;">
                                {{ number_format($rapport->total_sorties, 0, ',', ' ') }} $
                            </span>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: {{ $rapport->solde >= 0 ? 'var(--color-success)' : 'var(--color-accent)' }};">
                                {{ number_format($rapport->solde, 0, ',', ' ') }} $
                            </span>
                        </td>
                        <td>
                            <span class="finance-statut-badge finance-statut-{{ $rapport->statut }}">
                                {{ $rapport->statutLibelle() }}
                            </span>
                        </td>
                        <td>
                            <span class="finance-public-badge finance-public-{{ $rapport->public ? 'oui' : 'non' }}">
                                <i class="fas fa-{{ $rapport->public ? 'eye' : 'eye-slash' }}"></i>
                                {{ $rapport->public ? 'Public' : 'Privé' }}
                            </span>
                        </td>
                        <td>
                            <div class="finance-actions">
                                <a href="{{ route('admin.finances.show', $rapport) }}" class="finance-action-btn view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.finances.telechargerPdf', $rapport) }}" class="finance-action-btn pdf" title="Télécharger PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if(!$rapport->public)
                                <form action="{{ route('admin.finances.publier', $rapport) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="finance-action-btn publish" title="Publier">
                                        <i class="fas fa-globe"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.finances.destroy', $rapport) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="finance-action-btn delete btn-delete-rapport" title="Supprimer">
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

        @if($rapports->hasPages())
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-top: 1px solid #eef0f4;">
            <span style="font-size: 12px; color: #888;">
                Affichage de <strong>{{ $rapports->firstItem() }}-{{ $rapports->lastItem() }}</strong> sur <strong>{{ $rapports->total() }}</strong> rapports
            </span>
            {{ $rapports->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div style="text-align: center; padding: 60px 20px;">
            <div style="width: 70px; height: 70px; border-radius: 50%; background: var(--color-bg-light); color: #bbb; font-size: 1.8rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h4 style="font-size: 1rem; color: #1a1a1a; margin-bottom: 6px;">Aucun rapport financier</h4>
            <p style="font-size: 13px; color: #888; margin-bottom: 16px;">Créez votre premier rapport financier.</p>
            <a href="{{ route('admin.finances.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle"></i> Nouveau rapport
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/admin-finances.js') }}"></script>
@endsection