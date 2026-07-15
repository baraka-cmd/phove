@extends('layouts.admin')

@section('title', 'Gestion des cotisations')

@section('page_title', 'Cotisations')
@section('page_icon', 'coins')
@section('page_subtitle', 'Gérez les cotisations mensuelles des membres et suivez les paiements.')

@php
    $breadcrumb = [
        ['label' => 'Membres', 'url' => route('admin.membres.index')],
        'Cotisations'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-cotisations.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
{{-- KPI : 5 CARTES STATISTIQUES --}}
{{-- ============================================ --}}
<div class="cotisations-stats-grid">
    {{-- Total collecté --}}
    <div class="cotisation-stat-card">
        <div class="cotisation-stat-icon"><i class="fas fa-coins"></i></div>
        <div class="cotisation-stat-content">
            <div class="cotisation-stat-value">{{ number_format($totalCollecte, 0, ',', ' ') }} $</div>
            <div class="cotisation-stat-label">Total collecté</div>
            <div class="cotisation-stat-sub">Depuis la création</div>
        </div>
    </div>

    {{-- Mois en cours --}}
    <div class="cotisation-stat-card">
        <div class="cotisation-stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="cotisation-stat-content">
            <div class="cotisation-stat-value">{{ number_format($collecteMois, 0, ',', ' ') }} $</div>
            <div class="cotisation-stat-label">Collecté ce mois</div>
            <div class="cotisation-stat-sub">{{ \Carbon\Carbon::createFromFormat('Y-m', $moisEnCours)->translatedFormat('F Y') }}</div>
        </div>
    </div>

    {{-- Taux de recouvrement --}}
    <div class="cotisation-stat-card">
        <div class="cotisation-stat-icon"><i class="fas fa-chart-line"></i></div>
        <div class="cotisation-stat-content">
            <div class="cotisation-stat-value">{{ $tauxRecouvrement }}%</div>
            <div class="cotisation-stat-label">Taux de recouvrement</div>
            <div class="recouvrement-progress">
                <div class="recouvrement-progress-fill {{ $tauxRecouvrement >= 80 ? 'high' : ($tauxRecouvrement >= 50 ? 'medium' : 'low') }}" 
                     style="width: {{ $tauxRecouvrement }}%" 
                     data-width="{{ $tauxRecouvrement }}%"></div>
            </div>
            <div class="cotisation-stat-sub">{{ $membresAyantPaye }}/{{ $totalMembresActifs }} membres</div>
        </div>
    </div>

    {{-- Membres en règle (cliquable) --}}
    <a href="{{ route('admin.cotisations.regle', ['mois' => $moisEnCours]) }}" style="text-decoration: none; color: inherit;">
        <div class="cotisation-stat-card" style="cursor: pointer;">
            <div class="cotisation-stat-icon"><i class="fas fa-user-check"></i></div>
            <div class="cotisation-stat-content">
                <div class="cotisation-stat-value">{{ $membresAyantPaye }}</div>
                <div class="cotisation-stat-label">Membres en règle</div>
                <div class="cotisation-stat-sub up"><i class="fas fa-check-circle"></i> Ont payé ce mois</div>
            </div>
        </div>
    </a>

    {{-- Membres en retard (cliquable) --}}
    <a href="{{ route('admin.cotisations.retard') }}" style="text-decoration: none; color: inherit;">
        <div class="cotisation-stat-card" style="cursor: pointer;">
            <div class="cotisation-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="cotisation-stat-content">
                <div class="cotisation-stat-value">{{ $membresEnRetard }}</div>
                <div class="cotisation-stat-label">Membres en retard</div>
                <div class="cotisation-stat-sub down"><i class="fas fa-clock"></i> N'ont pas payé ce mois</div>
            </div>
        </div>
    </a>
</div>
    

    {{-- ============================================ --}}
    {{-- GRAPHIQUES : BARRES + ANNEAU --}}
    {{-- ============================================ --}}
    <div class="cotisations-charts-grid">
        
        {{-- Graphique barres : Évolution des cotisations --}}
        <div class="cotisations-widget">
            <div class="cotisations-widget-header">
                <h3 class="cotisations-widget-title"><i class="fas fa-chart-bar"></i> Évolution des cotisations</h3>
                <span class="widget-badge primary">12 derniers mois</span>
            </div>
            <div class="cotisations-widget-body">
                <div style="height: 300px;">
                    <canvas id="cotisationsBarChart" 
                            data-labels='@json($moisLabels)' 
                            data-values='@json($moisValues)'></canvas>
                </div>
            </div>
        </div>

        {{-- Graphique anneau : Par mode de paiement --}}
        <div class="cotisations-widget">
            <div class="cotisations-widget-header">
                <h3 class="cotisations-widget-title"><i class="fas fa-chart-pie"></i> Par mode de paiement</h3>
            </div>
            <div class="cotisations-widget-body">
                <div style="display: flex; align-items: center; gap: 24px;">
                    <div style="width: 180px; height: 180px; flex-shrink: 0; position: relative;">
                        <canvas id="cotisationsDonutChart" 
                                data-labels='@json($modesLabels)' 
                                data-values='@json($modesValues)'
                                data-colors='@json(array_values($modesColors))'></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <span style="font-size: 1.2rem; font-weight: 800; color: #1a1a1a; display: block;" id="donutCenterValue">0 $</span>
                            <span style="font-size: 10px; color: #888;">total</span>
                        </div>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
                        @foreach($modesLabels as $index => $label)
                        <div style="display: flex; align-items: center; gap: 10px; font-size: 12px;">
                            <span style="width: 10px; height: 10px; border-radius: 3px; background: {{ array_values($modesColors)[$index] ?? '#ccc' }}; flex-shrink: 0;"></span>
                            <span style="color: #555;">{{ $label }}</span>
                            <span style="margin-left: auto; font-weight: 600; color: #1a1a1a;">{{ number_format($modesValues[$index] ?? 0, 0, ',', ' ') }} $</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS : RECHERCHE + FILTRES + AJOUT --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.cotisations.index') }}" method="GET" class="cotisations-toolbar">
        <div class="cotisations-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="cotisationSearch" value="{{ request('recherche') }}" 
                   class="cotisations-search-input" placeholder="Rechercher par nom de membre...">
        </div>

        <div class="cotisations-filtres">
            <select name="mois" class="cotisations-filtre-select">
                <option value="">Tous les mois</option>
                @foreach($moisDisponibles as $m)
                    <option value="{{ $m }}" {{ request('mois') === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>

            <select name="mode_paiement" class="cotisations-filtre-select">
                <option value="">Tous les modes</option>
                <option value="especes" {{ request('mode_paiement') === 'especes' ? 'selected' : '' }}>Espèces</option>
                <option value="virement_bancaire" {{ request('mode_paiement') === 'virement_bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                <option value="mobile_money" {{ request('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                <option value="autre" {{ request('mode_paiement') === 'autre' ? 'selected' : '' }}>Autre</option>
            </select>

            <select name="membre_id" class="cotisations-filtre-select">
                <option value="">Tous les membres</option>
                @foreach($membres as $membre)
                    <option value="{{ $membre->id }}" {{ request('membre_id') == $membre->id ? 'selected' : '' }}>
                        {{ $membre->nomComplet() }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('recherche') || request('mois') || request('mode_paiement') || request('membre_id'))
            <a href="{{ route('admin.cotisations.index') }}" class="membre-action-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.cotisations.create') }}" class="cotisations-add-btn">
            <i class="fas fa-plus-circle"></i> Enregistrer un paiement
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES COTISATIONS --}}
    {{-- ============================================ --}}
    <div class="cotisations-table-card">
        @if($cotisations->count() > 0)
        <div class="table-responsive">
            <table class="table-cotisations">
                <thead>
                    <tr>
                        <th>Membre</th>
                        <th>Mois</th>
                        <th>Montant</th>
                        <th>Mode</th>
                        <th>Référence</th>
                        <th>Date paiement</th>
                        <th>Enregistré le</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cotisations as $cotisation)
                    <tr>
                        {{-- Membre --}}
                        <td>
                            <div class="cotisation-cell-membre">
                                <div class="cotisation-cell-avatar">
                                    @if($cotisation->membre && $cotisation->membre->photo)
                                        <img src="{{ asset('storage/' . $cotisation->membre->photo) }}" alt="{{ $cotisation->membre->nomComplet() }}">
                                    @else
                                        <span>{{ Str::upper(Str::substr($cotisation->membre->nom ?? '?', 0, 1) . Str::substr($cotisation->membre->prenom ?? '', 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="cotisation-cell-nom">{{ $cotisation->membre ? $cotisation->membre->nomComplet() : '—' }}</span>
                                    <span class="cotisation-cell-categorie">
                                        {{ $cotisation->membre ? match($cotisation->membre->categorie) {
                                            'initiateur' => 'Initiateur', 'fondateur' => 'Fondateur',
                                            'adherent' => 'Adhérent', 'honneur' => 'Honneur',
                                            'sympathisant' => 'Sympathisant', default => $cotisation->membre->categorie
                                        } : '—' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Mois --}}
                        <td>
                            <span style="font-weight: 600; font-size: 13px;">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $cotisation->mois)->translatedFormat('F Y') }}
                            </span>
                        </td>

                        {{-- Montant --}}
                        <td>
                            <span class="cotisation-montant">{{ number_format($cotisation->montant, 2) }} $</span>
                        </td>

                        {{-- Mode --}}
                        <td>
                            <span class="cotisation-badge cotisation-badge-{{ $cotisation->mode_paiement }}">
                                <i class="fas fa-{{ $cotisation->mode_paiement === 'especes' ? 'money-bill-wave' : ($cotisation->mode_paiement === 'virement_bancaire' ? 'university' : ($cotisation->mode_paiement === 'mobile_money' ? 'mobile-alt' : 'ellipsis-h')) }}"></i>
                                {{ $cotisation->modePaiementLibelle() }}
                            </span>
                        </td>

                        {{-- Référence --}}
                        <td>
                            <span style="font-size: 12px;">{{ $cotisation->reference ?? '—' }}</span>
                        </td>

                        {{-- Date paiement --}}
                        <td>
                            <span style="font-size: 13px;">
                                {{ $cotisation->date_paiement ? $cotisation->date_paiement->format('d/m/Y') : '—' }}
                                @if($cotisation->estEnRetard())
                                    <span class="cotisation-retard"><i class="fas fa-exclamation-circle"></i> Retard</span>
                                @endif
                            </span>
                        </td>

                        {{-- Enregistré le --}}
                        <td>
                            <span style="font-size: 12px; color: var(--color-text-muted);">
                                {{ $cotisation->created_at->format('d/m/Y H:i') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($cotisations->hasPages())
        <div class="cotisations-pagination">
            {{ $cotisations->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="cotisations-empty">
            <div class="cotisations-empty-icon"><i class="fas fa-coins"></i></div>
            <h4>Aucune cotisation trouvée</h4>
            <p>Aucune cotisation ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.cotisations.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/admin-cotisations.js') }}"></script>
@endsection