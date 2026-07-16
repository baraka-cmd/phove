@extends('layouts.admin')

@section('title', 'Gestion des dons')

@section('page_title', 'Dons reçus')
@section('page_icon', 'hand-holding-heart')
@section('page_subtitle', 'Gérez l\'ensemble des dons reçus : consultation, enregistrement, reçus fiscaux.')

@php
    $breadcrumb = [ 'Dons' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dons.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 3 CARTES + STATS SECONDAIRES --}}
    {{-- ============================================ --}}
    <div class="dons-stats-grid">
        <div class="don-stat-card">
            <div class="don-stat-icon"><i class="fas fa-coins"></i></div>
            <div class="don-stat-content">
                <div class="don-stat-value">{{ number_format($totalDons, 0, ',', ' ') }} $</div>
                <div class="don-stat-label">Total des dons</div>
                <div class="don-stat-sub">{{ $nombreDonateurs }} donateurs distincts</div>
            </div>
        </div>
        <div class="don-stat-card">
            <div class="don-stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="don-stat-content">
                <div class="don-stat-value">{{ number_format($totalDonsMois, 0, ',', ' ') }} $</div>
                <div class="don-stat-label">Dons ce mois</div>
                <div class="don-stat-sub">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</div>
            </div>
        </div>
        <div class="don-stat-card">
            <div class="don-stat-icon"><i class="fas fa-clock"></i></div>
            <div class="don-stat-content">
                <div class="don-stat-value">{{ $donsEnAttente }}</div>
                <div class="don-stat-label">Dons en attente</div>
                <div class="don-stat-sub down">À confirmer</div>
            </div>
        </div>
    </div>

    {{-- LIGNE STATS COMPLÉMENTAIRES --}}
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--spacing-lg); margin-bottom: var(--spacing-2xl);">
        <div style="background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 1.5rem; color: var(--color-success);"><i class="fas fa-check-circle"></i></span>
            <div>
                <span style="font-size: 12px; color: #888; display: block;">Total confirmé</span>
                <strong style="font-size: 1.1rem; color: #1a1a1a;">{{ number_format($totalDonsConfirmes, 0, ',', ' ') }} $</strong>
            </div>
        </div>
        <div style="background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 1.5rem; color: var(--color-primary);"><i class="fas fa-calculator"></i></span>
            <div>
                <span style="font-size: 12px; color: #888; display: block;">Don moyen</span>
                <strong style="font-size: 1.1rem; color: #1a1a1a;">{{ number_format($moyenneDon, 0, ',', ' ') }} $</strong>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRAPHIQUES --}}
    {{-- ============================================ --}}
    <div class="dons-charts-grid">
        {{-- Graphique barres : Dons par mois --}}
        <div class="dons-widget">
            <div class="dons-widget-header">
                <h3 class="dons-widget-title"><i class="fas fa-chart-bar"></i> Évolution des dons</h3>
                <span class="widget-badge primary">12 derniers mois</span>
            </div>
            <div class="dons-widget-body">
                <div style="height: 300px;">
                    <canvas id="donsBarChart" 
                            data-labels='@json($moisLabels)' 
                            data-values='@json($moisValues)'></canvas>
                </div>
            </div>
        </div>

        {{-- Graphique anneau : Par mode de paiement --}}
        <div class="dons-widget">
            <div class="dons-widget-header">
                <h3 class="dons-widget-title"><i class="fas fa-chart-pie"></i> Par mode de paiement</h3>
            </div>
            <div class="dons-widget-body">
                <div style="display: flex; align-items: center; gap: 24px;">
                    <div style="width: 180px; height: 180px; flex-shrink: 0; position: relative;">
                        <canvas id="donsDonutChart" 
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
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.dons.index') }}" method="GET" class="dons-toolbar">
        <div class="dons-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="donSearch" value="{{ request('recherche') }}" 
                   class="dons-search-input" placeholder="Rechercher par nom, email, référence...">
        </div>

        <div class="dons-filtres">
            <select name="statut" class="dons-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="confirme" {{ request('statut') === 'confirme' ? 'selected' : '' }}>Confirmé</option>
                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="annule" {{ request('statut') === 'annule' ? 'selected' : '' }}>Annulé</option>
            </select>

            <select name="mode_paiement" class="dons-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les modes</option>
                <option value="especes" {{ request('mode_paiement') === 'especes' ? 'selected' : '' }}>Espèces</option>
                <option value="virement_bancaire" {{ request('mode_paiement') === 'virement_bancaire' ? 'selected' : '' }}>Virement</option>
                <option value="mobile_money" {{ request('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                <option value="carte_bancaire" {{ request('mode_paiement') === 'carte_bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                <option value="paypal" {{ request('mode_paiement') === 'paypal' ? 'selected' : '' }}>PayPal</option>
            </select>

            <select name="mois" class="dons-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les mois</option>
                @foreach($moisDisponibles as $m)
                    <option value="{{ $m }}" {{ request('mois') === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>

            <select name="projet_id" class="dons-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les projets</option>
                @foreach($projets as $projet)
                    <option value="{{ $projet->id }}" {{ request('projet_id') == $projet->id ? 'selected' : '' }}>
                        {{ Str::limit($projet->titre, 40) }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('recherche') || request('statut') || request('mode_paiement') || request('mois') || request('projet_id'))
            <a href="{{ route('admin.dons.index') }}" class="dons-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.dons.create') }}" class="dons-add-btn">
            <i class="fas fa-plus-circle"></i> Enregistrer un don
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES DONS --}}
    {{-- ============================================ --}}
    <div class="dons-table-card">
        @if($dons->count() > 0)
        <div class="table-responsive">
            <table class="table-dons">
                <thead>
                    <tr>
                        <th>Donateur</th>
                        <th>Montant</th>
                        <th>Mode</th>
                        <th>Type</th>
                        <th>Projet</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Reçu</th>
                        <th style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dons as $don)
                    <tr>
                        {{-- Donateur --}}
                        <td>
                            <div class="don-cell-donateur">
                                <div class="don-cell-avatar">
                                    <span>{{ Str::upper(Str::substr($don->donateur_nom, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <span class="don-cell-nom">{{ $don->donateur_nom }}</span>
                                    @if($don->donateur_email)
                                        <span class="don-cell-email">{{ $don->donateur_email }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Montant --}}
                        <td>
                            <span class="don-montant">{{ number_format($don->montant, 0) }} {{ $don->devise }}</span>
                        </td>

                        {{-- Mode --}}
                        <td>
                            @php
                                $modeClass = match($don->mode_paiement) {
                                    'especes' => 'especes', 'virement_bancaire' => 'virement',
                                    'mobile_money' => 'mobile', 'carte_bancaire' => 'carte',
                                    'paypal' => 'paypal', default => 'autre'
                                };
                            @endphp
                            <span class="don-badge don-badge-{{ $modeClass }}">{{ $don->modePaiementLibelle() }}</span>
                        </td>

                        {{-- Type --}}
                        <td>
                            <span class="don-type-badge don-type-{{ $don->type_don }}">
                                <i class="fas fa-{{ $don->type_don === 'ponctuel' ? 'hand-holding-heart' : 'sync-alt' }}"></i>
                                {{ $don->type_don === 'ponctuel' ? 'Ponctuel' : 'Récurrent' }}
                            </span>
                        </td>

                        {{-- Projet --}}
                        <td>
                            @if($don->projet)
                                <span style="font-size: 12px;" title="{{ $don->projet->titre }}">
                                    {{ Str::limit($don->projet->titre, 30) }}
                                </span>
                            @else
                                <span style="color: #ccc;">—</span>
                            @endif
                        </td>

                        {{-- Date --}}
                        <td>
                            <span style="font-size: 13px; white-space: nowrap;">
                                {{ $don->date_don ? $don->date_don->format('d/m/Y') : '—' }}
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td>
                            <span class="don-badge don-badge-{{ $don->statut }}">
                                <i class="fas fa-{{ $don->statut === 'confirme' ? 'check-circle' : ($don->statut === 'en_attente' ? 'clock' : 'times-circle') }}"></i>
                                {{ $don->statut === 'confirme' ? 'Confirmé' : ($don->statut === 'en_attente' ? 'En attente' : 'Annulé') }}
                            </span>
                        </td>

                        {{-- Reçu --}}
                        <td>
                            @if($don->recu_genere)
                                <span class="don-recu-badge don-recu-genere">
                                    <i class="fas fa-file-invoice"></i> {{ $don->numero_recu }}
                                </span>
                            @else
                                <span class="don-recu-badge don-recu-non-genere">Non généré</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="don-actions">
                                <a href="{{ route('admin.dons.show', $don) }}" class="don-action-btn view" title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.dons.edit', $don) }}" class="don-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($don->recu_genere)
                                    <a href="{{ route('admin.dons.telechargerRecu', ['don' => $don, 'view' => 1]) }}" target="_blank" class="don-action-btn recu" title="Voir le reçu">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('admin.dons.telechargerRecu', $don) }}" class="don-action-btn recu" title="Télécharger le PDF">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <form action="{{ route('admin.dons.genererRecu', $don) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="don-action-btn recu" title="Générer le reçu">
                                            <i class="fas fa-file-invoice"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.dons.destroy', $don) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="don-action-btn delete btn-delete-don" title="Supprimer">
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

        @if($dons->hasPages())
        <div class="dons-pagination">
            <span class="dons-pagination-info">
                Affichage de <strong>{{ $dons->firstItem() }}-{{ $dons->lastItem() }}</strong> sur <strong>{{ $dons->total() }}</strong> dons
            </span>
            {{ $dons->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="dons-empty">
            <div class="dons-empty-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <h4>Aucun don trouvé</h4>
            <p>Aucun don ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.dons.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/admin-dons.js') }}"></script>
@endsection