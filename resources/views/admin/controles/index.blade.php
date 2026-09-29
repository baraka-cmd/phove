@extends('layouts.admin')

@section('title', 'Commission de Contrôle | PHOVE')

@section('page_title', 'Commission de Contrôle')
@section('page_icon', 'search-dollar')
@section('page_subtitle', 'Contrôles trimestriels des finances et opérations — Article 35 des Statuts PHOVE.')

@php
    $breadcrumb = [ 'Commission de Contrôle' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-controlle.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="projets-stats-grid">
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-search-dollar"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $totalControles }}</div>
                <div class="projet-stat-label">Total contrôles</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #D1FAE5; color: #065F46;"><i class="fas fa-check-circle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #065F46;">{{ $controlesValides }}</div>
                <div class="projet-stat-label">Contrôles validés</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #FEF3C7; color: #92400E;"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #92400E;">{{ $controlesNonConformes }}</div>
                <div class="projet-stat-label">Non conformes</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #DBEAFE; color: #1E40AF;"><i class="fas fa-calendar-alt"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #1E40AF;">{{ $stats['conformes'] }}</div>
                <div class="projet-stat-label">Conformes {{ now()->year }}</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.controles.index') }}" method="GET" class="projets-toolbar">
        <div class="projets-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="controleSearch" value="{{ request('recherche') }}" 
                   class="projets-search-input" placeholder="Rechercher une référence, un contrôleur...">
        </div>

        <div class="projets-filtres">
            <select name="annee" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Toutes les années</option>
                @foreach($annees as $anneeVal)
                    <option value="{{ $anneeVal }}" {{ request('annee') == $anneeVal ? 'selected' : '' }}>{{ $anneeVal }}</option>
                @endforeach
            </select>

            <select name="trimestre" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les trimestres</option>
                <option value="T1" {{ request('trimestre') === 'T1' ? 'selected' : '' }}>T1 (Jan-Mar)</option>
                <option value="T2" {{ request('trimestre') === 'T2' ? 'selected' : '' }}>T2 (Avr-Juin)</option>
                <option value="T3" {{ request('trimestre') === 'T3' ? 'selected' : '' }}>T3 (Juil-Sep)</option>
                <option value="T4" {{ request('trimestre') === 'T4' ? 'selected' : '' }}>T4 (Oct-Déc)</option>
            </select>

            <select name="statut" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="planifie" {{ request('statut') === 'planifie' ? 'selected' : '' }}>Planifié</option>
                <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="termine" {{ request('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>Validé</option>
                <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Rejeté</option>
            </select>

            <select name="type" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="financier" {{ request('type') === 'financier' ? 'selected' : '' }}>Financier</option>
                <option value="operationnel" {{ request('type') === 'operationnel' ? 'selected' : '' }}>Opérationnel</option>
                <option value="global" {{ request('type') === 'global' ? 'selected' : '' }}>Global</option>
            </select>
        </div>

        @if(request('recherche') || request('annee') || request('trimestre') || request('statut') || request('type'))
            <a href="{{ route('admin.controles.index') }}" class="projets-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.controles.create') }}" class="projets-add-btn">
            <i class="fas fa-plus-circle"></i> Nouveau contrôle
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES CONTRÔLES --}}
    {{-- ============================================ --}}
    <div class="projets-table-card">
        @if($controles->count() > 0)
        <div class="table-responsive">
            <table class="table-projets">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Période</th>
                        <th>Type</th>
                        <th>Contrôleur</th>
                        <th>Entrées</th>
                        <th>Sorties</th>
                        <th>Écart</th>
                        <th>Conformité</th>
                        <th>Statut</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($controles as $controle)
                    <tr>
                        {{-- Référence --}}
                        <td>
                            <span style="font-size: 12px; color: #1A5B9C; font-weight: 700;">
                                <i class="fas fa-barcode"></i> {{ $controle->reference }}
                            </span>
                        </td>

                        {{-- Période --}}
                        <td>
                            <span style="font-size: 13px;">
                                {{ $controle->trimestreLibelle() }} {{ $controle->annee }}
                            </span>
                            <br>
                            <small style="color: #64748B;">{{ $controle->periodeFormatee() }}</small>
                        </td>

                        {{-- Type --}}
                        <td>
                            <span class="projet-badge" style="background: #E8F0F8; color: #1A5B9C;">
                                <i class="fas fa-tag"></i> {{ $controle->typeLibelle() }}
                            </span>
                        </td>

                        {{-- Contrôleur --}}
                        <td>
                            <span style="font-size: 13px; color: #334155;">{{ $controle->nomControlePar() }}</span>
                        </td>

                        {{-- Entrées --}}
                        <td>
                            <span style="font-size: 13px; font-weight: 600; color: #10B981;">
                                +{{ number_format($controle->total_entrees, 0, ',', ' ') }} $
                            </span>
                        </td>

                        {{-- Sorties --}}
                        <td>
                            <span style="font-size: 13px; font-weight: 600; color: #EF4444;">
                                -{{ number_format($controle->total_sorties, 0, ',', ' ') }} $
                            </span>
                        </td>

                        {{-- Écart --}}
                        <td>
                            <span style="font-size: 13px; font-weight: 700; color: {{ $controle->ecart >= 0 ? '#1E293B' : '#EF4444' }};">
                                {{ number_format($controle->ecart, 0, ',', ' ') }} $
                            </span>
                        </td>

                        {{-- Conformité --}}
                        <td>
                            @if($controle->conformite)
                                <span class="projet-publication-badge projet-publie">
                                    <i class="fas fa-check-circle"></i> Conforme
                                </span>
                            @else
                                <span class="projet-publication-badge" style="background: #FEE2E2; color: #B91C1C;">
                                    <i class="fas fa-exclamation-triangle"></i> Non conforme
                                </span>
                            @endif
                        </td>

                        {{-- Statut --}}
                        <td>
                            @if($controle->statut === 'planifie')
                                <span class="projet-publication-badge" style="background: #F1F5F9; color: #64748B;">
                                    <i class="fas fa-calendar"></i> Planifié
                                </span>
                            @elseif($controle->statut === 'en_cours')
                                <span class="projet-publication-badge" style="background: #DBEAFE; color: #1E40AF;">
                                    <i class="fas fa-spinner"></i> En cours
                                </span>
                            @elseif($controle->statut === 'termine')
                                <span class="projet-publication-badge" style="background: #FEF3C7; color: #92400E;">
                                    <i class="fas fa-check"></i> Terminé
                                </span>
                            @elseif($controle->statut === 'valide')
                                <span class="projet-publication-badge projet-publie">
                                    <i class="fas fa-check-circle"></i> Validé
                                </span>
                            @else
                                <span class="projet-publication-badge" style="background: #FEE2E2; color: #B91C1C;">
                                    <i class="fas fa-times-circle"></i> Rejeté
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="projet-actions">
                                <a href="{{ route('admin.controles.show', $controle) }}" class="projet-action-btn view" title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$controle->estValide())
                                <a href="{{ route('admin.controles.edit', $controle) }}" class="projet-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if($controle->estTermine())
                                <form action="{{ route('admin.controles.valider', $controle) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="projet-action-btn edit" title="Valider" style="background: #D1FAE5; color: #065F46;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                @if(!$controle->estValide())
                                <form action="{{ route('admin.controles.destroy', $controle) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="projet-action-btn delete" 
                                            onclick="return confirm('Supprimer ce contrôle ?');" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($controles->hasPages())
        <div class="projets-pagination">
            <span class="projets-pagination-info">
                Affichage de <strong>{{ $controles->firstItem() }}-{{ $controles->lastItem() }}</strong> sur <strong>{{ $controles->total() }}</strong> contrôles
            </span>
            {{ $controles->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="projets-empty">
            <div class="projets-empty-icon"><i class="fas fa-search-dollar"></i></div>
            <h4>Aucun contrôle trouvé</h4>
            <p>Aucun contrôle ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.controles.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection