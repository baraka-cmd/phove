@extends('layouts.admin')

@section('title', 'Gestion des budgets | PHOVE')

@section('page_title', 'Budgets des projets')
@section('page_icon', 'coins')
@section('page_subtitle', 'Suivez et gérez les budgets de tous les projets PHOVE : prévisionnel, dépensé, restant, alertes de dépassement.')

@php
    $breadcrumb = [
        ['label' => 'Projets', 'url' => route('admin.projets.index')],
        'Budgets'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-budgets.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 5 CARTES --}}
    {{-- ============================================ --}}
    <div class="budgets-stats-grid">
        <div class="budget-stat-card">
            <div class="budget-stat-icon"><i class="fas fa-coins"></i></div>
            <div class="budget-stat-content">
                <div class="budget-stat-value">{{ number_format($totalBudgetPrevisionnel, 0, ',', ' ') }} $</div>
                <div class="budget-stat-label">Budget prévisionnel total</div>
                <div class="budget-stat-sub">{{ $nombreProjetsAvecBudget }} projets budgétisés</div>
            </div>
        </div>
        <div class="budget-stat-card">
            <div class="budget-stat-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="budget-stat-content">
                <div class="budget-stat-value">{{ number_format($totalBudgetUtilise, 0, ',', ' ') }} $</div>
                <div class="budget-stat-label">Total dépensé</div>
                <div class="budget-stat-sub">{{ $tauxConsommationGlobal }}% du budget global</div>
            </div>
        </div>
        <div class="budget-stat-card">
            <div class="budget-stat-icon"><i class="fas fa-piggy-bank"></i></div>
            <div class="budget-stat-content">
                <div class="budget-stat-value">{{ number_format($budgetRestant, 0, ',', ' ') }} $</div>
                <div class="budget-stat-label">Budget restant</div>
                <div class="budget-stat-sub up">Disponible</div>
            </div>
        </div>
        <div class="budget-stat-card">
            <div class="budget-stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="budget-stat-content">
                <div class="budget-stat-value">{{ $tauxConsommationGlobal }}%</div>
                <div class="budget-stat-label">Taux de consommation</div>
                <div class="budget-stat-sub {{ $tauxConsommationGlobal > 90 ? 'down' : 'up' }}">
                    {{ $tauxConsommationGlobal > 90 ? 'Élevé' : 'Normal' }}
                </div>
            </div>
        </div>
        <div class="budget-stat-card">
            <div class="budget-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="budget-stat-content">
                <div class="budget-stat-value">{{ $projetsEnDepassement }}</div>
                <div class="budget-stat-label">Projets en dépassement</div>
                <div class="budget-stat-sub down">⚠️ Alerte</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRAPHIQUES --}}
    {{-- ============================================ --}}
    <div class="budgets-charts-grid">
        {{-- Graphique barres horizontales : Budget total vs utilisé --}}
        <div class="budgets-widget">
            <div class="budgets-widget-header">
                <h3 class="budgets-widget-title">
                    <i class="fas fa-chart-bar"></i> Budget prévisionnel vs utilisé (Top 10)
                </h3>
                <span class="widget-badge primary">Projets PHOVE</span>
            </div>
            <div class="budgets-widget-body">
                <div style="height: 380px;">
                    <canvas id="budgetsBarChart" 
                            data-labels='@json($graphLabels)' 
                            data-total='@json($graphBudgetTotal)'
                            data-utilise='@json($graphBudgetUtilise)'></canvas>
                </div>
            </div>
        </div>

        {{-- Graphique anneau : Dépenses par domaine --}}
        <div class="budgets-widget">
            <div class="budgets-widget-header">
                <h3 class="budgets-widget-title">
                    <i class="fas fa-chart-pie"></i> Dépenses par domaine
                </h3>
            </div>
            <div class="budgets-widget-body">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div style="width: 190px; height: 190px; flex-shrink: 0; position: relative;">
                        <canvas id="budgetsDonutChart" 
                                data-labels='@json($donutLabels)' 
                                data-values='@json($donutValues)'
                                data-colors='@json(array_values($domainesColors))'></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <span style="font-size: 1.1rem; font-weight: 800; color: #1E293B; display: block;" id="donutCenterValue">0 $</span>
                            <span style="font-size: 10px; color: #64748B;">total dépensé</span>
                        </div>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                        @foreach($donutLabels as $index => $label)
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 11px;">
                            <span style="width: 8px; height: 8px; border-radius: 2px; background: {{ array_values($domainesColors)[$index] ?? '#CBD5E1' }}; flex-shrink: 0;"></span>
                            <span style="color: #334155;">{{ $label }}</span>
                            <span style="margin-left: auto; font-weight: 600; color: #1E293B;">{{ number_format($donutValues[$index] ?? 0, 0, ',', ' ') }} $</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.projets.budgets') }}" method="GET" class="budgets-toolbar">
        <div class="budgets-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="budgetSearch" value="{{ request('recherche') }}" 
                   class="budgets-search-input" placeholder="Rechercher un projet, un code...">
        </div>

        <div class="budgets-filtres">
            <select name="domaine" class="budgets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les domaines</option>
                <option value="sante_nutrition" {{ request('domaine') === 'sante_nutrition' ? 'selected' : '' }}>Santé & Nutrition</option>
                <option value="education" {{ request('domaine') === 'education' ? 'selected' : '' }}>Éducation & Formation</option>
                <option value="protection" {{ request('domaine') === 'protection' ? 'selected' : '' }}>Protection Sociale</option>
                <option value="wash" {{ request('domaine') === 'wash' ? 'selected' : '' }}>Eau & Assainissement</option>
                <option value="securite_alimentaire" {{ request('domaine') === 'securite_alimentaire' ? 'selected' : '' }}>Sécurité Alimentaire & Agriculture</option>
                <option value="communication" {{ request('domaine') === 'communication' ? 'selected' : '' }}>Communication</option>
                <option value="environnement" {{ request('domaine') === 'environnement' ? 'selected' : '' }}>Environnement</option>
                <option value="paix" {{ request('domaine') === 'paix' ? 'selected' : '' }}>Paix & Cohésion</option>
                <option value="genre" {{ request('domaine') === 'genre' ? 'selected' : '' }}>Genre</option>
                <option value="spirituel" {{ request('domaine') === 'spirituel' ? 'selected' : '' }}>Spirituel</option>
                <option value="infrastructure" {{ request('domaine') === 'infrastructure' ? 'selected' : '' }}>Infrastructures</option>
                <option value="economique" {{ request('domaine') === 'economique' ? 'selected' : '' }}>Socio-Économique</option>
                <option value="judiciaire" {{ request('domaine') === 'judiciaire' ? 'selected' : '' }}>Judiciaire</option>
            </select>

            <select name="statut" class="budgets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="termine" {{ request('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="en_preparation" {{ request('statut') === 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                <option value="suspendu" {{ request('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
            </select>
        </div>

        @if(request('recherche') || request('domaine') || request('statut'))
            <a href="{{ route('admin.projets.budgets') }}" class="budgets-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES BUDGETS --}}
    {{-- ============================================ --}}
    <div class="budgets-table-card">
        @if($projets->count() > 0)
        <div class="table-responsive">
            <table class="table-budgets">
                <thead>
                    <tr>
                        <th>Projet</th>
                        <th>Code</th>
                        <th>Domaine</th>
                        <th>Budget total</th>
                        <th>Budget utilisé</th>
                        <th>Progression</th>
                        <th>Restant</th>
                        <th style="width: 160px;">Mise à jour</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projets as $projet)
                    @php
                        $pourcentage = $projet->budget_total > 0 ? round(($projet->budget_utilise / $projet->budget_total) * 100) : 0;
                        $restant = $projet->budget_total - $projet->budget_utilise;
                        $classeProgression = $pourcentage > 100 ? 'danger' : ($pourcentage > 80 ? 'warning' : 'normal');
                    @endphp
                    <tr>
                        {{-- Projet --}}
                        <td>
                            <div class="budget-cell-projet">
                                <div class="budget-cell-image">
                                    @if($projet->image_principale)
                                        <img src="{{ asset('storage/' . $projet->image_principale) }}" alt="{{ $projet->titre }}" loading="lazy">
                                    @else
                                        <div class="budget-cell-image-placeholder">
                                            <i class="fas fa-project-diagram"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="budget-cell-nom">{{ Str::limit($projet->titre, 50) }}</span>
                                    <span class="budget-cell-domaine">{{ $projet->domaineLibelle() }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Code --}}
                        <td>
                            @if($projet->code_projet)
                                <span class="projet-code-badge" title="Code officiel">
                                    <i class="fas fa-barcode"></i> {{ $projet->code_projet }}
                                </span>
                            @else
                                <span style="color: #94A3B8; font-size: 12px;">N/A</span>
                            @endif
                        </td>

                        {{-- Domaine --}}
                        <td>
                            @php
                                $domaineClass = match($projet->domaine) {
                                    'sante_nutrition' => 'sante', 'education' => 'education',
                                    'protection' => 'protection', 'wash' => 'wash',
                                    'securite_alimentaire' => 'alimentaire', 'communication' => 'communication',
                                    'environnement' => 'environnement', 'paix' => 'paix', 'genre' => 'genre',
                                    'spirituel' => 'spirituel', 'infrastructure' => 'infrastructure',
                                    'economique' => 'economique', 'judiciaire' => 'judiciaire',
                                    default => 'sante'
                                };
                            @endphp
                            <span class="projet-badge projet-badge-{{ $domaineClass }}">{{ $projet->domaineLibelle() }}</span>
                        </td>

                        {{-- Budget total --}}
                        <td>
                            <span class="budget-montant">{{ number_format($projet->budget_total, 0, ',', ' ') }} $</span>
                        </td>

                        {{-- Budget utilisé --}}
                        <td>
                            <span class="budget-montant" style="color: {{ $pourcentage > 100 ? '#EF4444' : ($pourcentage > 80 ? '#F59E0B' : '#10B981') }};">
                                {{ number_format($projet->budget_utilise, 0, ',', ' ') }} $
                            </span>
                        </td>

                        {{-- Progression --}}
                        <td>
                            <div class="budget-progress">
                                <div class="budget-progress-bar">
                                    <div class="budget-progress-fill {{ $classeProgression }}" 
                                         style="width: {{ min($pourcentage, 100) }}%"></div>
                                </div>
                                <span class="budget-progress-pourcent {{ $classeProgression }}">{{ $pourcentage }}%</span>
                            </div>
                            @if($pourcentage > 100)
                                <span class="budget-alert-badge">
                                    <i class="fas fa-exclamation-triangle"></i> Dépassement de {{ number_format(abs($restant), 0, ',', ' ') }} $
                                </span>
                            @endif
                        </td>

                        {{-- Restant --}}
                        <td>
                            <span class="budget-montant" style="color: {{ $restant < 0 ? '#EF4444' : '#10B981' }};">
                                {{ number_format($restant, 0, ',', ' ') }} $
                            </span>
                        </td>

                        {{-- Mise à jour rapide --}}
                        <td>
                            <form action="{{ route('admin.projets.updateBudget', $projet) }}" method="POST" class="budget-update-form" style="display: flex; align-items: center; gap: 6px;">
                                @csrf
                                @method('PUT')
                                <input type="number" name="budget_utilise" value="{{ $projet->budget_utilise }}" 
                                       class="don-form-input" style="width: 100px; text-align: center; padding: 8px 10px;" 
                                       min="0" step="0.01" title="Budget utilisé">
                                <button type="submit" class="budget-action-btn edit budget-update-btn" title="Mettre à jour">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($projets->hasPages())
        <div class="budgets-pagination">
            <span class="budgets-pagination-info">
                Affichage de <strong>{{ $projets->firstItem() }}-{{ $projets->lastItem() }}</strong> sur <strong>{{ $projets->total() }}</strong> projets
            </span>
            {{ $projets->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="budgets-empty">
            <div class="budgets-empty-icon"><i class="fas fa-coins"></i></div>
            <h4>Aucun projet trouvé</h4>
            <p>Aucun projet ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.projets.budgets') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/admin-budgets.js') }}"></script>
@endsection