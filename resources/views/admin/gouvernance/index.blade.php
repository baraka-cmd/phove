@extends('layouts.admin')

@section('title', 'Gouvernance')

@section('page_title', 'Gouvernance')
@section('page_icon', 'sitemap')
@section('page_subtitle', 'Gérez les organes de gouvernance : Conseil d\'Administration, Direction Exécutive, Commission de Contrôle et Assemblée Générale.')

@php
    $breadcrumb = [ 'Gouvernance' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance-index.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI : RÉSUMÉ --}}
    {{-- ============================================ --}}
    <div class="assemblies-stats-grid">
        <div class="assembly-stat-card">
            <div class="assembly-stat-icon"><i class="fas fa-user-tie"></i></div>
            <div class="assembly-stat-content">
                <div class="assembly-stat-value">{{ $stats['conseil_administration']['actifs'] }}</div>
                <div class="assembly-stat-label">Membres au CA</div>
            </div>
        </div>
        <div class="assembly-stat-card">
            <div class="assembly-stat-icon"><i class="fas fa-tasks"></i></div>
            <div class="assembly-stat-content">
                <div class="assembly-stat-value">{{ $stats['direction_executive']['actifs'] }}</div>
                <div class="assembly-stat-label">Direction Exécutive</div>
            </div>
        </div>
        <div class="assembly-stat-card">
            <div class="assembly-stat-icon"><i class="fas fa-search-dollar"></i></div>
            <div class="assembly-stat-content">
                <div class="assembly-stat-value">{{ $stats['commission_controle']['actifs'] }}</div>
                <div class="assembly-stat-label">Commission de Contrôle</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTE DERNIÈRE/PROCHAINE AG --}}
    {{-- ============================================ --}}
    @if($derniereAG || $prochaineAG)
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); margin-bottom: var(--spacing-2xl);">
        @if($derniereAG)
        <div style="background: #fff; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); border-left: 4px solid var(--color-success);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <span style="background: var(--color-success-light); color: var(--color-success); padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 600;">Dernière AG tenue</span>
            </div>
            <p style="font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 0 0 4px 0;">
                {{ $derniereAG->type === 'ordinaire' ? 'AG Ordinaire' : 'AG Extraordinaire' }}
            </p>
            <p style="font-size: 12px; color: #888; margin: 0;">
                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($derniereAG->date_reunion)->format('d/m/Y') }} — 
                <i class="fas fa-map-marker-alt"></i> {{ $derniereAG->lieu }} — 
                <i class="fas fa-users"></i> {{ $derniereAG->membres_presents }}/{{ $derniereAG->membres_total }} présents
            </p>
        </div>
        @endif

        @if($prochaineAG)
        <div style="background: #fff; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); border-left: 4px solid var(--color-info);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <span style="background: var(--color-info-light); color: var(--color-info); padding: 4px 10px; border-radius: 50px; font-size: 11px; font-weight: 600;">Prochaine AG planifiée</span>
            </div>
            <p style="font-size: 14px; font-weight: 600; color: #1a1a1a; margin: 0 0 4px 0;">
                {{ $prochaineAG->type === 'ordinaire' ? 'AG Ordinaire' : 'AG Extraordinaire' }}
            </p>
            <p style="font-size: 12px; color: #888; margin: 0;">
                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($prochaineAG->date_reunion)->format('d/m/Y') }} — 
                <i class="fas fa-map-marker-alt"></i> {{ $prochaineAG->lieu }}
            </p>
        </div>
        @endif
    </div>
    @endif

    {{-- ============================================ --}}
    {{-- CARTES DES 4 ORGANES --}}
    {{-- ============================================ --}}
    <div class="gouvernance-grid">

        {{-- CONSEIL D'ADMINISTRATION --}}
        <div class="gouvernance-organe-card">
            <div class="gouvernance-organe-header">
                <h3><i class="fas fa-user-tie"></i> Conseil d'Administration</h3>
                <span class="gouvernance-organe-badge">{{ $stats['conseil_administration']['actifs'] }} membre(s) actif(s)</span>
            </div>
            <div class="gouvernance-organe-body">
                @forelse($stats['conseil_administration']['membres'] as $organe)
                <div class="gouvernance-membre-item">
                    <div class="gouvernance-membre-avatar">
                        @if($organe->membre && $organe->membre->photo)
                            <img src="{{ asset('storage/' . $organe->membre->photo) }}" alt="{{ $organe->membre->nomComplet() }}">
                        @else
                            <span>{{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="gouvernance-membre-info">
                        <div class="gouvernance-membre-nom">{{ $organe->membre ? $organe->membre->nomComplet() : '—' }}</div>
                        <div class="gouvernance-membre-poste">{{ $organe->poste }}</div>
                        <div class="gouvernance-membre-dates">
                            Depuis {{ $organe->date_debut->format('d/m/Y') }}
                            @if($organe->date_fin) — {{ $organe->date_fin->format('d/m/Y') }} @endif
                        </div>
                    </div>
                </div>
                @empty
                <p style="text-align: center; color: #999; padding: 20px 0;">Aucun membre pour le moment.</p>
                @endforelse
            </div>
            <div class="gouvernance-organe-footer">
                <a href="{{ route('admin.gouvernance.organe', 'conseil_administration') }}">
                    Gérer le Conseil d'Administration <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- DIRECTION EXÉCUTIVE --}}
        <div class="gouvernance-organe-card">
            <div class="gouvernance-organe-header">
                <h3><i class="fas fa-tasks"></i> Direction Exécutive</h3>
                <span class="gouvernance-organe-badge">{{ $stats['direction_executive']['actifs'] }} membre(s) actif(s)</span>
            </div>
            <div class="gouvernance-organe-body">
                @forelse($stats['direction_executive']['membres'] as $organe)
                <div class="gouvernance-membre-item">
                    <div class="gouvernance-membre-avatar">
                        @if($organe->membre && $organe->membre->photo)
                            <img src="{{ asset('storage/' . $organe->membre->photo) }}" alt="{{ $organe->membre->nomComplet() }}">
                        @else
                            <span>{{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="gouvernance-membre-info">
                        <div class="gouvernance-membre-nom">{{ $organe->membre ? $organe->membre->nomComplet() : '—' }}</div>
                        <div class="gouvernance-membre-poste">{{ $organe->poste }}</div>
                        <div class="gouvernance-membre-dates">
                            Depuis {{ $organe->date_debut->format('d/m/Y') }}
                            @if($organe->date_fin) — {{ $organe->date_fin->format('d/m/Y') }} @endif
                        </div>
                    </div>
                </div>
                @empty
                <p style="text-align: center; color: #999; padding: 20px 0;">Aucun membre pour le moment.</p>
                @endforelse
            </div>
            <div class="gouvernance-organe-footer">
                <a href="{{ route('admin.gouvernance.organe', 'direction_executive') }}">
                    Gérer la Direction Exécutive <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- COMMISSION DE CONTRÔLE --}}
        <div class="gouvernance-organe-card">
            <div class="gouvernance-organe-header">
                <h3><i class="fas fa-search-dollar"></i> Commission de Contrôle</h3>
                <span class="gouvernance-organe-badge">{{ $stats['commission_controle']['actifs'] }} membre(s) actif(s)</span>
            </div>
            <div class="gouvernance-organe-body">
                @forelse($stats['commission_controle']['membres'] as $organe)
                <div class="gouvernance-membre-item">
                    <div class="gouvernance-membre-avatar">
                        @if($organe->membre && $organe->membre->photo)
                            <img src="{{ asset('storage/' . $organe->membre->photo) }}" alt="{{ $organe->membre->nomComplet() }}">
                        @else
                            <span>{{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="gouvernance-membre-info">
                        <div class="gouvernance-membre-nom">{{ $organe->membre ? $organe->membre->nomComplet() : '—' }}</div>
                        <div class="gouvernance-membre-poste">{{ $organe->poste }}</div>
                        <div class="gouvernance-membre-dates">
                            Depuis {{ $organe->date_debut->format('d/m/Y') }}
                            @if($organe->date_fin) — {{ $organe->date_fin->format('d/m/Y') }} @endif
                        </div>
                    </div>
                </div>
                @empty
                <p style="text-align: center; color: #999; padding: 20px 0;">Aucun membre pour le moment.</p>
                @endforelse
            </div>
            <div class="gouvernance-organe-footer">
                <a href="{{ route('admin.gouvernance.organe', 'commission_controle') }}">
                    Gérer la Commission de Contrôle <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- ASSEMBLÉE GÉNÉRALE --}}
        <div class="gouvernance-organe-card">
            <div class="gouvernance-organe-header">
                <h3><i class="fas fa-users-cog"></i> Assemblée Générale</h3>
                <span class="gouvernance-organe-badge">Tous les membres effectifs</span>
            </div>
            <div class="gouvernance-organe-body">
                <p style="font-size: 13px; color: #666; line-height: 1.6; margin-bottom: 16px;">
                    L'Assemblée Générale est l'organe suprême de l'organisation. Elle est composée de 
                    <strong>tous les membres effectifs</strong> et se réunit chaque année en janvier 
                    en session ordinaire (Article 25 des Statuts).
                </p>
                <div style="display: flex; gap: 12px; font-size: 12px; color: #888;">
                    <span><i class="fas fa-calendar-check"></i> Quorum : majorité simple</span>
                    <span><i class="fas fa-vote-yea"></i> Décisions : majorité 2/3</span>
                </div>
            </div>
            <div class="gouvernance-organe-footer">
                <a href="{{ route('admin.gouvernance.assemblies') }}">
                    Gérer les Assemblées Générales <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-gouvernance.js') }}"></script>
@endsection