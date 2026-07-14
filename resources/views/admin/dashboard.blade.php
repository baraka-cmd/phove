@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('page_title', 'Tableau de bord')
@section('page_icon', 'tachometer-alt')
@section('page_subtitle', 'Vue d\'ensemble complète de la plateforme AHD-RDC')

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
                Bienvenue dans votre espace d'administration. Voici un aperçu complet de l'ensemble des activités de la plateforme : projets, membres, dons, messages et bien plus.
            </p>
            <p class="welcome-time">
                <span><i class="fas fa-calendar-alt"></i> {{ now()->format('d/m/Y') }}</span>
                <span><i class="fas fa-clock"></i> <span id="dashboardClock">--:--</span></span>
                <span><i class="fas fa-code-branch"></i> v1.0.0</span>
            </p>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 1 : 4 CARTES KPI --}}
    {{-- ============================================ --}}
    <div class="kpi-grid">
        @php
            $totalProjets = \App\Models\Projet::count();
            $projetsEnCours = \App\Models\Projet::where('statut', 'en_cours')->count();
            $totalMembres = \App\Models\Membre::count();
            $membresActifs = \App\Models\Membre::where('statut', 'actif')->count();
            $totalDons = \App\Models\Don::sum('montant');
            $donsMois = \App\Models\Don::whereMonth('created_at', now()->month)->sum('montant');
            $messagesNonLus = \App\Models\Contact::where('lu', false)->count();
            $totalMessages = \App\Models\Contact::count();
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
            <div class="kpi-decoration"><i class="fas fa-project-diagram"></i></div>
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
            <div class="kpi-decoration"><i class="fas fa-users"></i></div>
        </div>

        {{-- KPI 3 : Dons reçus --}}
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ number_format($totalDons, 0, ',', ' ') }} $</div>
                <div class="kpi-label">Dons totaux</div>
                <div class="kpi-change up">
                    <i class="fas fa-calendar-check"></i> {{ number_format($donsMois, 0, ',', ' ') }} $ ce mois
                </div>
            </div>
            <div class="kpi-decoration"><i class="fas fa-dollar-sign"></i></div>
        </div>

        {{-- KPI 4 : Messages --}}
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fas fa-envelope-open-text"></i></div>
            <div class="kpi-content">
                <div class="kpi-value">{{ $messagesNonLus }}</div>
                <div class="kpi-label">Messages non lus</div>
                <div class="kpi-change {{ $messagesNonLus > 0 ? 'down' : 'up' }}">
                    <i class="fas {{ $messagesNonLus > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i> 
                    {{ $totalMessages }} messages au total
                </div>
            </div>
            <div class="kpi-decoration"><i class="fas fa-envelope"></i></div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 2 : GRAPHIQUE EN ANNEAU + BARRES VERTICALES --}}
    {{-- ============================================ --}}
    <div class="dashboard-row">
        
        {{-- GRAPHIQUE EN ANNEAU : Répartition des projets par domaine --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-chart-pie"></i> Projets par domaine</h3>
                <span class="widget-badge primary">{{ $totalProjets }} projets</span>
            </div>
            <div class="widget-body">
                @php
                    $domainesDonut = [
                        'Santé-Nutrition' => \App\Models\Projet::where('domaine', 'sante_nutrition')->count(),
                        'Éducation' => \App\Models\Projet::where('domaine', 'education')->count(),
                        'Protection' => \App\Models\Projet::where('domaine', 'protection')->count(),
                        'Eau & Assainissement' => \App\Models\Projet::where('domaine', 'wash')->count(),
                        'Sécurité Alimentaire' => \App\Models\Projet::where('domaine', 'securite_alimentaire')->count(),
                        'Communication' => \App\Models\Projet::where('domaine', 'communication')->count(),
                        'Environnement' => \App\Models\Projet::where('domaine', 'environnement')->count(),
                        'Paix & Cohésion' => \App\Models\Projet::where('domaine', 'paix')->count(),
                    ];
                    $domainesLabels = array_keys($domainesDonut);
                    $domainesValues = array_values($domainesDonut);
                    $donutTotal = array_sum($domainesValues);
                    $domainesColors = [
                        'Santé-Nutrition' => '#16558F', 'Éducation' => '#28a745', 'Protection' => '#8e44ad',
                        'Eau & Assainissement' => '#2980b9', 'Sécurité Alimentaire' => '#e67e22',
                        'Communication' => '#17a2b8', 'Environnement' => '#27ae60', 'Paix & Cohésion' => '#D32F2F'
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

        {{-- GRAPHIQUE BARRES VERTICALES : Dons par mois --}}
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
    {{-- LIGNE 3 : COURBE MEMBRES + ACTIVITÉS RÉCENTES --}}
    {{-- ============================================ --}}
    <div class="dashboard-row">
        
        {{-- GRAPHIQUE LINÉAIRE : Évolution des membres --}}
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-chart-line"></i> Évolution des adhésions</h3>
                <span class="widget-badge danger">12 derniers mois</span>
            </div>
            <div class="widget-body">
                <div class="chart-container" style="height: 300px;">
                    @php
                        $lineLabels = [];
                        $lineValues = [];
                        for ($i = 11; $i >= 0; $i--) {
                            $date = now()->subMonths($i);
                            $lineLabels[] = $moisNoms[$date->month - 1];
                            $lineValues[] = \App\Models\Membre::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count();
                        }
                    @endphp
                    <canvas id="lineChart" 
                            data-labels='@json($lineLabels)' 
                            data-values='@json($lineValues)'></canvas>
                </div>
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
                                'type' => 'don', 'icon' => 'fa-heart', 'color' => 'danger',
                                'titre' => $don->donateur_nom . ' a fait un don',
                                'sous_titre' => number_format($don->montant, 0) . ' $ — ' . $don->created_at->diffForHumans()
                            ]);
                        }
                        foreach(\App\Models\Membre::latest()->take(2)->get() as $membre) {
                            $activites->push([
                                'type' => 'membre', 'icon' => 'fa-user-plus', 'color' => 'success',
                                'titre' => $membre->nomComplet() . ' a adhéré',
                                'sous_titre' => ($membre->categorie === 'adherent' ? 'Membre adhérent' : ($membre->categorie === 'sympathisant' ? 'Sympathisant' : 'Membre d\'honneur')) . ' — ' . $membre->created_at->diffForHumans()
                            ]);
                        }
                        foreach(\App\Models\Contact::latest()->take(2)->get() as $msg) {
                            $activites->push([
                                'type' => 'message', 'icon' => 'fa-envelope', 'color' => 'warning',
                                'titre' => $msg->nom . ' a envoyé un message',
                                'sous_titre' => $msg->motifLibelle() . ' — ' . $msg->created_at->diffForHumans()
                            ]);
                        }
                        $activites = $activites->sortByDesc(function($a) { return strtotime($a['sous_titre'] ?? '0'); })->take(6);
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
    {{-- LIGNE 4 : TOP PROJETS PAR BUDGET (BARRES HORIZONTALES) --}}
    {{-- ============================================ --}}
    <div class="dashboard-row dashboard-row-full">
        <div class="widget">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fas fa-trophy"></i> Top 5 projets par budget utilisé</h3>
                <span class="widget-badge warning">Classement</span>
            </div>
            <div class="widget-body">
                <div class="chart-container" style="height: 260px;">
                    @php
                        $topProjets = \App\Models\Projet::orderBy('budget_utilise', 'desc')->take(5)->get();
                        $topLabels = [];
                        $topValues = [];
                        foreach ($topProjets as $p) {
                            $topLabels[] = $p->titre;
                            $topValues[] = (int) $p->budget_utilise;
                        }
                    @endphp
                    <canvas id="horizontalBarChart" 
                            data-labels='@json($topLabels)' 
                            data-values='@json($topValues)'></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 5 : 3 COLONNES — DONS, MEMBRES, MESSAGES --}}
    {{-- ============================================ --}}
    <div class="dashboard-row dashboard-row-3">
        
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
                    <li class="widget-list-item"><div class="widget-list-content"><span style="color:#999;">Aucun don</span></div></li>
                    @endforelse
                </ul>
            </div>
            <div class="widget-footer">
                <a href="{{ route('admin.dons.index') }}">Voir tous les dons <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

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
                            <div class="widget-list-subtitle">{{ $membre->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="status-badge {{ $membre->statut === 'actif' ? 'success' : 'warning' }}">{{ $membre->statut === 'actif' ? 'Actif' : 'En attente' }}</span>
                    </li>
                    @empty
                    <li class="widget-list-item"><div class="widget-list-content"><span style="color:#999;">Aucun membre</span></div></li>
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
                    <li class="widget-list-item"><div class="widget-list-content"><span style="color:#999;">Aucun message</span></div></li>
                    @endforelse
                </ul>
            </div>
            <div class="widget-footer">
                <a href="{{ route('admin.contacts.index') }}">Voir tous les messages <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LIGNE 6 : RÉPARTITION PAR DOMAINE (BARRES DE PROGRESSION) --}}
    {{-- ============================================ --}}
    <div class="widget" style="margin-bottom: 0;">
        <div class="widget-header">
            <h3 class="widget-title"><i class="fas fa-chart-bar"></i> Répartition détaillée des projets par domaine</h3>
        </div>
        <div class="widget-body">
            @php
                $domainesStats = [
                    ['nom' => 'Santé-Nutrition', 'valeur' => 'sante_nutrition', 'classe' => 'sante', 'couleur' => '#16558F'],
                    ['nom' => 'Éducation', 'valeur' => 'education', 'classe' => 'education', 'couleur' => '#28a745'],
                    ['nom' => 'Protection', 'valeur' => 'protection', 'classe' => 'protection', 'couleur' => '#8e44ad'],
                    ['nom' => 'Eau & Assainissement', 'valeur' => 'wash', 'classe' => 'wash', 'couleur' => '#2980b9'],
                    ['nom' => 'Sécurité Alimentaire', 'valeur' => 'securite_alimentaire', 'classe' => 'alimentaire', 'couleur' => '#e67e22'],
                    ['nom' => 'Communication', 'valeur' => 'communication', 'classe' => 'communication', 'couleur' => '#17a2b8'],
                    ['nom' => 'Environnement', 'valeur' => 'environnement', 'classe' => 'environnement', 'couleur' => '#27ae60'],
                    ['nom' => 'Paix & Cohésion', 'valeur' => 'paix', 'classe' => 'paix', 'couleur' => '#D32F2F'],
                ];
                $totalProjetsStats = \App\Models\Projet::count();
            @endphp
            @foreach($domainesStats as $domaine)
                @php
                    $count = \App\Models\Projet::where('domaine', $domaine['valeur'])->count();
                    $percent = $totalProjetsStats > 0 ? round(($count / $totalProjetsStats) * 100) : 0;
                @endphp
                <div class="domain-bar-item" style="padding: 10px 0;">
                    <div class="domain-bar-header">
                        <span class="domain-bar-name">{{ $domaine['nom'] }}</span>
                        <span class="domain-bar-value">{{ $count }} projet(s) — {{ $percent }}%</span>
                    </div>
                    <div class="domain-bar">
                        <div class="domain-bar-fill {{ $domaine['classe'] }}" style="width: {{ max($percent, 2) }}%; background: {{ $domaine['couleur'] }}; min-width: 4px;"></div>
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