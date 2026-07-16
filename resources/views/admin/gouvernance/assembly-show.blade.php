@extends('layouts.admin')

@section('title', 'Détail de l\'Assemblée Générale')

@section('page_title', 'Détail de l\'Assemblée Générale')
@section('page_icon', 'users-cog')
@section('page_subtitle', 'Consultez les informations complètes de cette Assemblée Générale.')

@php
    $breadcrumb = [
        ['label' => 'Gouvernance', 'url' => route('admin.gouvernance.index')],
        ['label' => 'Assemblées Générales', 'url' => route('admin.gouvernance.assemblies')],
        'Détail AG'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance-assembly-show.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE --}}
    {{-- ============================================ --}}
    <div class="assembly-show-header">
        <div class="assembly-show-icon">
            <i class="fas fa-users-cog"></i>
        </div>
        <div class="assembly-show-info">
            <h2>
                {{ $assembly->type === 'ordinaire' ? 'Assemblée Générale Ordinaire' : 'Assemblée Générale Extraordinaire' }}
            </h2>
            <div class="assembly-show-meta">
                <span class="assembly-type-badge {{ $assembly->type }}">
                    <i class="fas fa-{{ $assembly->type === 'ordinaire' ? 'calendar-check' : 'exclamation-circle' }}"></i>
                    {{ $assembly->type === 'ordinaire' ? 'Ordinaire' : 'Extraordinaire' }}
                </span>
                <span class="assembly-statut-badge assembly-statut-{{ $assembly->statut }}">
                    <i class="fas fa-{{ 
                        $assembly->statut === 'planifiee' ? 'clock' : 
                        ($assembly->statut === 'tenue' ? 'check-circle' : 
                        ($assembly->statut === 'annulee' ? 'times-circle' : 'calendar-alt')) 
                    }}"></i>
                    {{ match($assembly->statut) {
                        'planifiee' => 'Planifiée',
                        'tenue' => 'Tenue',
                        'annulee' => 'Annulée',
                        'reportee' => 'Reportée',
                        default => $assembly->statut
                    } }}
                </span>
                <span style="font-size: 13px; color: var(--color-text-muted);">
                    <i class="fas fa-calendar-alt"></i> 
                    {{ \Carbon\Carbon::parse($assembly->date_reunion)->format('d/m/Y') }}
                </span>
                <span style="font-size: 13px; color: var(--color-text-muted);">
                    <i class="fas fa-map-marker-alt"></i> {{ $assembly->lieu }}
                </span>
            </div>
        </div>
        <div class="assembly-show-actions">
            <a href="{{ route('admin.gouvernance.assemblies.edit', $assembly) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('admin.gouvernance.assemblies') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE DE DÉTAIL --}}
    {{-- ============================================ --}}
    <div class="assembly-show-grid">

        {{-- COLONNE GAUCHE — Informations générales --}}
        <div>
            <div class="assembly-detail-card">
                <div class="assembly-detail-card-header">
                    <i class="fas fa-info-circle"></i> Informations générales
                </div>
                <div class="assembly-detail-card-body">
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-tag"></i> Type</span>
                        <span class="assembly-detail-value">
                            {{ $assembly->type === 'ordinaire' ? 'Assemblée Générale Ordinaire' : 'Assemblée Générale Extraordinaire' }}
                        </span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-calendar-alt"></i> Date de réunion</span>
                        <span class="assembly-detail-value">{{ \Carbon\Carbon::parse($assembly->date_reunion)->format('d/m/Y') }}</span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-map-marker-alt"></i> Lieu</span>
                        <span class="assembly-detail-value">{{ $assembly->lieu }}</span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-circle"></i> Statut</span>
                        <span class="assembly-detail-value">
                            <span class="assembly-statut-badge assembly-statut-{{ $assembly->statut }}">
                                {{ match($assembly->statut) {
                                    'planifiee' => 'Planifiée',
                                    'tenue' => 'Tenue',
                                    'annulee' => 'Annulée',
                                    'reportee' => 'Reportée',
                                    default => $assembly->statut
                                } }}
                            </span>
                        </span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-clock"></i> Enregistrée le</span>
                        <span class="assembly-detail-value">{{ $assembly->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-sync-alt"></i> Dernière modification</span>
                        <span class="assembly-detail-value">{{ $assembly->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>

            {{-- Contenu de l'AG --}}
            <div class="assembly-detail-card" style="margin-top: 24px;">
                <div class="assembly-detail-card-header">
                    <i class="fas fa-file-alt"></i> Contenu de l'AG
                </div>
                <div class="assembly-detail-card-body">
                    <div style="margin-bottom: 20px;">
                        <h5 style="font-size: 13px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">
                            <i class="fas fa-list-ul" style="color: var(--color-primary);"></i> Ordre du jour
                        </h5>
                        <div class="assembly-text-block">{{ $assembly->ordre_du_jour }}</div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <h5 style="font-size: 13px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">
                            <i class="fas fa-gavel" style="color: var(--color-primary);"></i> Décisions prises
                        </h5>
                        <div class="assembly-text-block">{{ $assembly->decisions }}</div>
                    </div>
                    <div>
                        <h5 style="font-size: 13px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">
                            <i class="fas fa-check-circle" style="color: var(--color-primary);"></i> Résolutions
                        </h5>
                        <div class="assembly-text-block">{{ $assembly->resolutions }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE — Participants + Quorum --}}
        <div>
            <div class="assembly-detail-card">
                <div class="assembly-detail-card-header">
                    <i class="fas fa-users"></i> Participants
                </div>
                <div class="assembly-detail-card-body">
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-users"></i> Total membres effectifs</span>
                        <span class="assembly-detail-value highlight">{{ $assembly->membres_total ?: '—' }}</span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-user-check"></i> Membres présents</span>
                        <span class="assembly-detail-value" style="color: var(--color-success); font-size: 1.1rem; font-weight: 700;">
                            {{ $assembly->membres_presents ?: '—' }}
                        </span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-user-times"></i> Membres absents</span>
                        <span class="assembly-detail-value" style="color: var(--color-accent);">
                            {{ $assembly->membres_absents ?: '—' }}
                        </span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-balance-scale"></i> Quorum</span>
                        <span class="assembly-detail-value">
                            @if($assembly->membres_total > 0)
                                @php
                                    $quorumMin = ceil($assembly->membres_total / 2);
                                    $atteint = $assembly->membres_presents >= $quorumMin;
                                @endphp
                                <span class="assembly-quorum-indicator {{ $atteint ? 'quorum-atteint' : 'quorum-non-atteint' }}">
                                    <i class="fas fa-{{ $atteint ? 'check-circle' : 'times-circle' }}"></i>
                                    {{ $atteint ? 'Atteint' : 'Non atteint' }} 
                                    ({{ $assembly->membres_presents }}/{{ $quorumMin }} requis)
                                </span>
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </span>
                    </div>
                    <div class="assembly-detail-row">
                        <span class="assembly-detail-label"><i class="fas fa-percentage"></i> Taux de participation</span>
                        <span class="assembly-detail-value">
                            @if($assembly->membres_total > 0)
                                {{ round(($assembly->membres_presents / $assembly->membres_total) * 100) }}%
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Procès-verbal --}}
            <div class="assembly-pv-section" style="margin-top: 24px;">
                <h3 class="assembly-pv-header">
                    <i class="fas fa-file-pdf"></i> Procès-verbal
                </h3>
                @if($assembly->pv_fichier)
                    <a href="{{ asset('storage/' . $assembly->pv_fichier) }}" target="_blank" class="assembly-pv-download">
                        <i class="fas fa-download"></i> Télécharger le PV (PDF)
                    </a>
                @else
                    <div class="assembly-pv-none">
                        <i class="fas fa-file-pdf"></i>
                        <span>Aucun procès-verbal n'a été enregistré pour cette Assemblée Générale.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Barre d'actions --}}
    <div class="assembly-show-actions-bar">
        <a href="{{ route('admin.gouvernance.assemblies.edit', $assembly) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Modifier cette AG
        </a>
        <a href="{{ route('admin.gouvernance.assemblies') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <form action="{{ route('admin.gouvernance.assemblies.destroy', $assembly) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-delete-assembly" style="margin-left: auto;">
                <i class="fas fa-trash-alt"></i> Supprimer
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-gouvernance-assembly-show.js') }}"></script>
@endsection