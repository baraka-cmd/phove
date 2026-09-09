@extends('layouts.admin')

@section('title', 'Tableau de bord | PHOVE')

@section('page_title', 'Tableau de bord')
@section('page_icon', 'tachometer-alt')
@section('page_subtitle', 'Vue d\'ensemble complète de la plateforme PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dashboard.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- CARTE DE BIENVENUE --}}
    {{-- ============================================ --}}
    <div class="welcome-card">
        <div class="welcome-icon">
            <i class="fas fa-hand-sparkles"></i>
        </div>
        <div class="welcome-content">
            <h2 class="welcome-title">
                @php
                    $heure = now()->hour;
                    $salutation = 'Bonjour';
                    if ($heure < 12) $salutation = 'Bonjour';
                    elseif ($heure < 18) $salutation = 'Bon après-midi';
                    else $salutation = 'Bonsoir';
                @endphp
                {{ $salutation }}, {{ Auth::user()->nom ?? 'Administrateur' }} !
            </h2>
            <p class="welcome-text">
                Bienvenue dans votre espace d'administration PHOVE. Voici un aperçu complet de l'ensemble des activités : projets, membres, dons, stocks, salaires et bien plus.
            </p>
            <p class="welcome-time">
                <span><i class="fas fa-calendar-alt"></i> {{ now()->format('d/m/Y') }}</span>
                <span><i class="fas fa-clock"></i> <span id="dashboardClock">--:--</span></span>
                <span><i class="fas fa-code-branch"></i> v1.0.0</span>
                <span><i class="fas fa-role"></i> {{ Auth::user()->roleLibelle() }}</span>
            </p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 1 : 6 CARTES KPI --}}
    {{-- ============================================ --}}
    <div class="kpi-grid" style="grid-template-columns: repeat(6, 1fr);">
        @php
            $totalProjets = \App\Models\Projet::count();
            $projetsEnCours = \App\Models\Projet::where('statut', 'en_cours')->count();
            $totalMembres = \App\Models\Membre::count();
            $membresActifs = \App\Models\Membre::where('statut', 'actif')->count();
            $totalDons = \App\Models\Don::sum('montant');
            $donsMois = \App\Models\Don::whereMonth('created_at', now()->month)->sum('montant');
            $messagesNonLus = \App\Models\Contact::where('lu', false)->count();
            $totalStocks = \App\Models\Stock::count();
            $stocksFaibles = \App\Models\Stock::where('statut', 'faible')->count();
            $totalSalaires = \App\Models\Salaire::where('mois', now()->format('Y-m'))->count();
            $salairesPayes = \App\Models\Salaire::where('mois', now()->format('Y-m'))->where('statut', 'paye')->count();
        @endphp

        {{-- KPI 1 : Projets --}}
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fas fa-project-diagram"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $totalProjets }}</div>
                <div class="kpi-label">Projets</div>
                <div class="kpi-change up">
                    <i class="fas fa-spinner"></i> {{ $projetsEnCours }} en cours
                </div>
            </div>
        </div>

        {{-- KPI 2 : Membres --}}
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fas fa-users"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $totalMembres }}</div>
                <div class="kpi-label">Membres</div>
                <div class="kpi-change up">
                    <i class="fas fa-user-check"></i> {{ $membresActifs }} actifs
                </div>
            </div>
        </div>

        {{-- KPI 3 : Dons --}}
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ number_format($totalDons, 0, ',', ' ') }} $</div>
                <div class="kpi-label">Dons totaux</div>
                <div class="kpi-change up">
                    <i class="fas fa-calendar-check"></i> {{ number_format($donsMois, 0, ',', ' ') }} $ ce mois
                </div>
            </div>
        </div>

        {{-- KPI 4 : Messages --}}
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fas fa-envelope-open-text"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $messagesNonLus }}</div>
                <div class="kpi-label">Messages non lus</div>
                <div class="kpi-change {{ $messagesNonLus > 0 ? 'down' : 'up' }}">
                    <i class="fas {{ $messagesNonLus > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                    {{ $messagesNonLus > 0 ? 'Action requise' : 'Tout est lu' }}
                </div>
            </div>
        </div>

        {{-- KPI 5 : Stocks --}}
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fas fa-boxes"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $totalStocks }}</div>
                <div class="kpi-label">Articles en stock</div>
                <div class="kpi-change {{ $stocksFaibles > 0 ? 'down' : 'up' }}">
                    <i class="fas fa-exclamation-triangle"></i> {{ $stocksFaibles }} en alerte
                </div>
            </div>
        </div>

        {{-- KPI 6 : Salaires --}}
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $totalSalaires }}</div>
                <div class="kpi-label">Salaires {{ now()->format('m/Y') }}</div>
                <div class="kpi-change {{ $salairesPayes === $totalSalaires && $totalSalaires > 0 ? 'up' : 'down' }}">
                    <i class="fas fa-check-circle"></i> {{ $salairesPayes }}/{{ $totalSalaires }} payés
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 2 : GRAPHIQUE ANNEAU + BARRES --}}
    {{-- ============================================ --}}
    <div class="dashboard-row">
        
        {{-- GRAPHIQUE ANNEAU : Projets par domaine PHOVE --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-chart-pie"></i> Projets par domaine PHOVE</h3>
                <span class="widget-badge primary">{{ $totalProjets }} projets</span>
            </div>
            <div class="widget-body">
                @php
                    $domainesDonut = [
                        'Santé & Nutrition' => \App\Models\Projet::where('domaine', 'sante_nutrition')->count(),
                        'Éducation & Formation' => \App\Models\Projet::where('domaine', 'education')->count(),
                        'Protection Sociale' => \App\Models\Projet::where('domaine', 'protection')->count(),
                        'Agriculture & Élevage' => \App\Models\Projet::where('domaine', 'securite_alimentaire')->count(),
                        'Infrastructures' => \App\Models\Projet::where('domaine', 'infrastructure')->count(),
                        'Socio-Économique' => \App\Models\Projet::where('domaine', 'economique')->count(),
                        'Spirituel' => \App\Models\Projet::where('domaine', 'spirituel')->count(),
                        'Psychosocial' => \App\Models\Projet::where('domaine', 'psychosocial')->count(),
                        'Judiciaire' => \App\Models\Projet::where('domaine', 'judiciaire')->count(),
                    ];
                    $domainesLabels = array_keys($domainesDonut);
                    $domainesValues = array_values($domainesDonut);
                    $donutTotal = array_sum($domainesValues);
                    $domainesColors = [
                        'Santé & Nutrition' => '#EF4444',
                        'Éducation & Formation' => '#3B82F6',
                        'Protection Sociale' => '#8B5CF6',
                        'Agriculture & Élevage' => '#10B981',
                        'Infrastructures' => '#F59E0B',
                        'Socio-Économique' => '#06B6D4',
                        'Spirituel' => '#6366F1',
                        'Psychosocial' => '#EC4899',
                        'Judiciaire' => '#A855F7',
                    ];
                @endphp
                <div class="chart-donut-container">
                    <div class="chart-donut-canvas">
                        <canvas id="donutChart" 
                                data-labels='@json($domainesLabels)' 
                                data-values='@json($domainesValues)'></canvas>
                        <div class="chart-donut-center">
                            <span class="chart-donut-center-value" id="donutCenterValue">{{ $donutTotal }}</span>
                            <span class="chart-donut-center-label">projets</span>
                        </div>
                    </div>
                    <div class="chart-donut-legend">
                        @foreach($domainesDonut as $nom => $valeur)
                            @if($valeur > 0)
                            <div class="chart-donut-legend-item">
                                <span class="chart-donut-legend-color" style="background: {{ $domainesColors[$nom] }}"></span>
                                <span>{{ $nom }}</span>
                                <span class="chart-donut-legend-value">{{ $valeur }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAPHIQUE BARRES : Dons par mois --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-chart-bar"></i> Dons par mois</h3>
                <span class="widget-badge success">{{ date('Y') }}</span>
            </div>
            <div class="widget-body">
                <div class="chart-container" style="height: 320px;">
                    @php
                        $moisLabels = [];
                        $moisValues = [];
                        $moisNoms = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
                        for ($i = 1; $i <= 12; $i++) {
                            $moisLabels[] = $moisNoms[$i - 1];
                            $moisValues[] = \App\Models\Don::whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->sum('montant');
                        }
                    @endphp
                    <canvas id="barChart" 
                            data-labels='@json($moisLabels)' 
                            data-values='@json($moisValues)'></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 3 : INDICATEURS PAR DOMAINE + ACTIVITÉS --}}
    {{-- ============================================ --}}
    <div class="dashboard-row">
        
        {{-- INDICATEURS PAR DOMAINE --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-tachometer-alt"></i> Indicateurs par domaine</h3>
                <span class="widget-badge warning">24 indicateurs</span>
            </div>
            <div class="widget-body">
                @php
                    $indicateursDomaines = \App\Models\IndicateurProjet::whereNull('projet_id')
                        ->selectRaw('domaine, COUNT(*) as total, SUM(CASE WHEN valeur_actuelle >= valeur_cible THEN 1 ELSE 0 END) as atteints')
                        ->groupBy('domaine')
                        ->get();
                @endphp
                @foreach($indicateursDomaines as $stat)
                    @php
                        $pourcentage = $stat->total > 0 ? round(($stat->atteints / $stat->total) * 100) : 0;
                        $couleur = $pourcentage >= 100 ? '#10B981' : ($pourcentage >= 50 ? '#F59E0B' : '#EF4444');
                        $domaineLibelle = \App\Models\IndicateurProjet::DOMAINES[$stat->domaine] ?? $stat->domaine;
                    @endphp
                    <div style="padding: 8px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="font-size: 12px; font-weight: 600; color: #1E293B;">{{ $domaineLibelle }}</span>
                            <span style="font-size: 11px; color: #64748B;">{{ $stat->atteints }}/{{ $stat->total }} atteints — {{ $pourcentage }}%</span>
                        </div>
                        <div style="height: 6px; background: #E2E8F0; border-radius: 3px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $pourcentage }}%; background: {{ $couleur }}; border-radius: 3px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ACTIVITÉS RÉCENTES --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-history"></i> Activités récentes</h3>
            </div>
            <div class="widget-body">
                <ul class="widget-list">
                    @php
                        $activites = collect();
                        foreach(\App\Models\Don::latest()->take(3)->get() as $don) {
                            $activites->push([
                                'icon' => 'fa-heart', 'color' => 'danger',
                                'titre' => $don->donateur_nom . ' a fait un don',
                                'sous_titre' => number_format($don->montant, 0) . ' $ — ' . $don->created_at->diffForHumans()
                            ]);
                        }
                        foreach(\App\Models\Membre::latest()->take(2)->get() as $membre) {
                            $activites->push([
                                'icon' => 'fa-user-plus', 'color' => 'success',
                                'titre' => $membre->nomComplet() . ' a adhéré',
                                'sous_titre' => $membre->categorieLibelle() . ' — ' . $membre->created_at->diffForHumans()
                            ]);
                        }
                        foreach(\App\Models\Contact::latest()->take(2)->get() as $msg) {
                            $activites->push([
                                'icon' => 'fa-envelope', 'color' => 'warning',
                                'titre' => $msg->nom . ' a envoyé un message',
                                'sous_titre' => $msg->motifLibelle() . ' — ' . $msg->created_at->diffForHumans()
                            ]);
                        }
                    @endphp
                    @foreach($activites as $activite)
                    <li class="widget-list-item">
                        <div class="widget-list-avatar {{ $activite['color'] }}">
                            <i class="fas {{ $activite['icon'] }}"></i>
                        </div>
                        <div class="widget-list-content">
                            <div class="widget-list-title">{{ $activite['titre'] }}</div>
                            <div class="widget-list-subtitle">{{ $activite['sous_titre'] }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 4 : TOP PROJETS + DERNIERS DONS --}}
    {{-- ============================================ --}}
    <div class="dashboard-row">
        
        {{-- TOP 5 PROJETS --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-trophy"></i> Top 5 projets par budget</h3>
                <span class="widget-badge warning">Classement</span>
            </div>
            <div class="widget-body">
                @php
                    $topProjets = \App\Models\Projet::orderBy('budget_utilise', 'desc')->take(5)->get();
                @endphp
                @foreach($topProjets as $index => $projet)
                <div style="display: flex; align-items: center; gap: 10px; padding: 8px 0;">
                    <span style="width: 28px; height: 28px; border-radius: 50%; background: {{ $index === 0 ? '#F59E0B' : '#E8F0F8' }}; color: {{ $index === 0 ? '#FFFFFF' : '#1A5B9C' }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0;">
                        {{ $index + 1 }}
                    </span>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 13px; font-weight: 600; color: #1E293B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $projet->titre }}
                        </div>
                        <div style="font-size: 11px; color: #64748B;">
                            {{ $projet->domaineLibelle() }} — {{ number_format($projet->budget_utilise, 0, ',', ' ') }} $ / {{ number_format($projet->budget_total, 0, ',', ' ') }} $
                        </div>
                    </div>
                    <span style="font-size: 12px; font-weight: 700; color: #1A5B9C;">{{ $projet->pourcentageBudget() }}%</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- DERNIERS DONS --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-heart"></i> Derniers dons</h3>
            </div>
            <div class="widget-body">
                <ul class="widget-list">
                    @php $derniersDons = \App\Models\Don::latest()->take(5)->get(); @endphp
                    @forelse($derniersDons as $don)
                    <li class="widget-list-item">
                        <div class="widget-list-avatar danger"><i class="fas fa-heart"></i></div>
                        <div class="widget-list-content">
                            <div class="widget-list-title">{{ $don->donateur_nom }}</div>
                            <div class="widget-list-subtitle">{{ $don->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="widget-list-value positive">{{ number_format($don->montant, 0) }} $</div>
                    </li>
                    @empty
                    <li class="widget-list-item"><div class="widget-list-content"><span style="color:#94A3B8;">Aucun don</span></div></li>
                    @endforelse
                </ul>
            </div>
            <div class="widget-footer">
                <a href="{{ route('admin.dons.index') }}">Voir tous les dons <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 5 : NOUVEAUX MEMBRES + MESSAGES --}}
    {{-- ============================================ --}}
    <div class="dashboard-row">
        
        {{-- NOUVEAUX MEMBRES --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-user-plus"></i> Nouveaux membres</h3>
            </div>
            <div class="widget-body">
                <ul class="widget-list">
                    @php $nouveauxMembres = \App\Models\Membre::latest()->take(5)->get(); @endphp
                    @forelse($nouveauxMembres as $membre)
                    <li class="widget-list-item">
                        <div class="widget-list-avatar success"><i class="fas fa-user"></i></div>
                        <div class="widget-list-content">
                            <div class="widget-list-title">{{ $membre->nomComplet() }}</div>
                            <div class="widget-list-subtitle">{{ $membre->categorieLibelle() }} — {{ $membre->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="status-badge {{ $membre->statut === 'actif' ? 'success' : 'warning' }}">{{ $membre->statutLibelle() }}</span>
                    </li>
                    @empty
                    <li class="widget-list-item"><div class="widget-list-content"><span style="color:#94A3B8;">Aucun membre</span></div></li>
                    @endforelse
                </ul>
            </div>
            <div class="widget-footer">
                <a href="{{ route('admin.membres.index') }}">Voir tous les membres <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        {{-- MESSAGES EN ATTENTE --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-envelope"></i> Messages en attente</h3>
                <span class="widget-badge danger">{{ $messagesNonLus }} non lus</span>
            </div>
            <div class="widget-body">
                <ul class="widget-list">
                    @php $messagesEnAttente = \App\Models\Contact::where('lu', false)->latest()->take(5)->get(); @endphp
                    @forelse($messagesEnAttente as $msg)
                    <li class="widget-list-item">
                        <div class="widget-list-avatar warning"><i class="fas fa-envelope"></i></div>
                        <div class="widget-list-content">
                            <div class="widget-list-title">{{ $msg->nom }} — {{ Str::limit($msg->sujet, 25) }}</div>
                            <div class="widget-list-subtitle">{{ $msg->motifLibelle() }} — {{ $msg->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="status-badge danger">Non lu</span>
                    </li>
                    @empty
                    <li class="widget-list-item"><div class="widget-list-content"><span style="color:#94A3B8;">Aucun message</span></div></li>
                    @endforelse
                </ul>
            </div>
            <div class="widget-footer">
                <a href="{{ route('admin.contacts.index') }}">Voir tous les messages <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 6 : RÉPARTITION PAR DOMAINE --}}
    {{-- ============================================ --}}
    <div class="widget" style="margin-bottom: 0;">
        <div class="widget-header">
            <h3 class="widget-title"><i class="fas fa-chart-bar"></i> Répartition détaillée des projets par domaine</h3>
        </div>
        <div class="widget-body">
            @php
                $domainesStats = \App\Models\IndicateurProjet::DOMAINES;
                $totalProjetsStats = \App\Models\Projet::count();
            @endphp
            @foreach($domainesStats as $code => $nom)
                @php
                    $count = \App\Models\Projet::where('domaine', $code)->count();
                    $percent = $totalProjetsStats > 0 ? round(($count / $totalProjetsStats) * 100) : 0;
                @endphp
                <div class="domain-bar-item" style="padding: 10px 0;">
                    <div class="domain-bar-header">
                        <span class="domain-bar-name">{{ $nom }}</span>
                        <span class="domain-bar-value">{{ $count }} projet(s) — {{ $percent }}%</span>
                    </div>
                    <div class="domain-bar">
                        <div class="domain-bar-fill" style="width: {{ max($percent, 2) }}%; background: #1A5B9C; min-width: 4px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/admin-dashboard.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            updateDashboardClock();
            setInterval(updateDashboardClock, 30000);
            function updateDashboardClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const clockEl = document.getElementById('dashboardClock');
                if (clockEl) clockEl.textContent = hours + ':' + minutes;
            }
        });
    </script>
@endsection