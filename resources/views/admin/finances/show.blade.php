@extends('layouts.admin')

@section('title', 'Détail du rapport — ' . $rapport->titre)

@section('page_title', 'Détail du rapport financier')
@section('page_icon', 'file-invoice')
@section('page_subtitle', 'Rapport : ' . $rapport->titre)

@php
    $breadcrumb = [
        ['label' => 'Finances', 'url' => route('admin.finances.index')],
        'Détail du rapport'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-finances.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE --}}
    {{-- ============================================ --}}
    <div class="finance-show-header">
        <div class="finance-show-icon">
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="finance-show-info">
            <h2>{{ $rapport->titre }}</h2>
            <p>
                <span class="finance-type-badge finance-type-{{ $rapport->type }}" style="margin-right: 8px;">
                    {{ $rapport->typeLibelle() }}
                </span>
                <span class="finance-statut-badge finance-statut-{{ $rapport->statut }}" style="margin-right: 8px;">
                    {{ $rapport->statutLibelle() }}
                </span>
                <span class="finance-public-badge finance-public-{{ $rapport->public ? 'oui' : 'non' }}">
                    <i class="fas fa-{{ $rapport->public ? 'eye' : 'eye-shield' }}"></i>
                    {{ $rapport->public ? 'Visible publiquement' : 'Privé' }}
                </span>
                &nbsp;|&nbsp; Période : <strong>{{ $rapport->periode }}</strong>
                &nbsp;|&nbsp; Créé le {{ $rapport->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <a href="{{ route('admin.finances.telechargerPdf', $rapport) }}" class="btn btn-accent btn-sm">
                <i class="fas fa-file-pdf"></i> Télécharger PDF
            </a>
            @if(!$rapport->public)
            <form action="{{ route('admin.finances.publier', $rapport) }}" method="POST" style="display: inline;">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-globe"></i> Publier
                </button>
            </form>
            @endif
            <a href="{{ route('admin.finances.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE DE DÉTAIL --}}
    {{-- ============================================ --}}
    <div class="finance-show-grid">

        {{-- COLONNE GAUCHE : INFORMATIONS --}}
        <div>
            <div class="finance-detail-card">
                <div class="finance-detail-card-header">
                    <i class="fas fa-info-circle"></i> Informations du rapport
                </div>
                <div class="finance-detail-card-body">
                    <div class="finance-detail-row">
                        <span class="finance-detail-label"><i class="fas fa-file-alt"></i> Titre</span>
                        <span class="finance-detail-value">{{ $rapport->titre }}</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label"><i class="fas fa-tag"></i> Type</span>
                        <span class="finance-detail-value">{{ $rapport->typeLibelle() }}</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label"><i class="fas fa-calendar-alt"></i> Période</span>
                        <span class="finance-detail-value">{{ $rapport->periode }}</span>
                    </div>
                    @if($rapport->date_debut)
                    <div class="finance-detail-row">
                        <span class="finance-detail-label"><i class="fas fa-calendar-plus"></i> Date début</span>
                        <span class="finance-detail-value">{{ $rapport->date_debut->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($rapport->date_fin)
                    <div class="finance-detail-row">
                        <span class="finance-detail-label"><i class="fas fa-calendar-minus"></i> Date fin</span>
                        <span class="finance-detail-value">{{ $rapport->date_fin->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    <div class="finance-detail-row">
                        <span class="finance-detail-label"><i class="fas fa-circle"></i> Statut</span>
                        <span class="finance-detail-value">{{ $rapport->statutLibelle() }}</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label"><i class="fas fa-eye"></i> Visibilité</span>
                        <span class="finance-detail-value">{{ $rapport->public ? 'Public' : 'Privé' }}</span>
                    </div>
                    @if($rapport->date_validation)
                    <div class="finance-detail-row">
                        <span class="finance-detail-label"><i class="fas fa-check-circle"></i> Validé le</span>
                        <span class="finance-detail-value">{{ $rapport->date_validation->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($rapport->commentaire)
            <div class="finance-detail-card" style="margin-top: 24px;">
                <div class="finance-detail-card-header">
                    <i class="fas fa-sticky-note"></i> Commentaire
                </div>
                <div class="finance-detail-card-body">
                    <div style="font-size: 13px; color: #555; line-height: 1.8; white-space: pre-line;">
                        {{ $rapport->commentaire }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE : RÉCAPITULATIF FINANCIER --}}
        <div>
            {{-- Entrées --}}
            <div class="finance-detail-card">
                <div class="finance-detail-card-header">
                    <i class="fas fa-sign-in-alt" style="background: var(--color-success);"></i> Entrées
                </div>
                <div class="finance-detail-card-body">
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Dons reçus</span>
                        <span class="finance-detail-value positive">{{ number_format($rapport->dons_recus, 2, ',', ' ') }} $</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Cotisations reçues</span>
                        <span class="finance-detail-value positive">{{ number_format($rapport->cotisations_recues, 2, ',', ' ') }} $</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Subventions reçues</span>
                        <span class="finance-detail-value positive">{{ number_format($rapport->subventions_recues, 2, ',', ' ') }} $</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Autres entrées</span>
                        <span class="finance-detail-value positive">{{ number_format($rapport->autres_entrees, 2, ',', ' ') }} $</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0 0 0; margin-top: 8px; border-top: 2px solid var(--color-success-light); font-size: 14px; font-weight: 700;">
                        <span>Total entrées</span>
                        <span class="finance-detail-value positive" style="font-size: 16px;">
                            {{ number_format($rapport->total_entrees, 2, ',', ' ') }} $
                        </span>
                    </div>
                </div>
            </div>

            {{-- Sorties --}}
            <div class="finance-detail-card" style="margin-top: 24px;">
                <div class="finance-detail-card-header">
                    <i class="fas fa-sign-out-alt" style="background: var(--color-accent);"></i> Sorties
                </div>
                <div class="finance-detail-card-body">
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Dépenses projets</span>
                        <span class="finance-detail-value negative">{{ number_format($rapport->depenses_projets, 2, ',', ' ') }} $</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Salaires</span>
                        <span class="finance-detail-value negative">{{ number_format($rapport->salaires, 2, ',', ' ') }} $</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Logistique</span>
                        <span class="finance-detail-value negative">{{ number_format($rapport->logistique, 2, ',', ' ') }} $</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Frais administratifs</span>
                        <span class="finance-detail-value negative">{{ number_format($rapport->administratif, 2, ',', ' ') }} $</span>
                    </div>
                    <div class="finance-detail-row">
                        <span class="finance-detail-label">Autres sorties</span>
                        <span class="finance-detail-value negative">{{ number_format($rapport->autres_sorties, 2, ',', ' ') }} $</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0 0 0; margin-top: 8px; border-top: 2px solid var(--color-error-light); font-size: 14px; font-weight: 700;">
                        <span>Total sorties</span>
                        <span class="finance-detail-value negative" style="font-size: 16px;">
                            {{ number_format($rapport->total_sorties, 2, ',', ' ') }} $
                        </span>
                    </div>
                </div>
            </div>

            {{-- Solde --}}
            <div class="finance-detail-card" style="margin-top: 24px; background: {{ $rapport->solde >= 0 ? 'var(--color-success-light)' : 'var(--color-error-light)' }}; border: 2px solid {{ $rapport->solde >= 0 ? 'var(--color-success)' : 'var(--color-accent)' }};">
                <div class="finance-detail-card-body" style="text-align: center; padding: 24px;">
                    <div style="font-size: 12px; font-weight: 600; color: {{ $rapport->solde >= 0 ? 'var(--color-success)' : 'var(--color-accent)' }}; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                        {{ $rapport->solde >= 0 ? 'Solde excédentaire' : 'Solde déficitaire' }}
                    </div>
                    <div style="font-size: 2rem; font-weight: 800; color: {{ $rapport->solde >= 0 ? 'var(--color-success)' : 'var(--color-accent)' }};">
                        {{ number_format($rapport->solde, 2, ',', ' ') }} $
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS --}}
    {{-- ============================================ --}}
    <div style="display: flex; gap: 10px; margin-top: 28px;">
        <a href="{{ route('admin.finances.telechargerPdf', $rapport) }}" class="btn btn-accent">
            <i class="fas fa-file-pdf"></i> Télécharger en PDF
        </a>
        @if(!$rapport->public)
        <form action="{{ route('admin.finances.publier', $rapport) }}" method="POST" style="display: inline;">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-success">
                <i class="fas fa-globe"></i> Publier ce rapport
            </button>
        </form>
        @endif
        <a href="{{ route('admin.finances.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <form action="{{ route('admin.finances.destroy', $rapport) }}" method="POST" style="display: inline; margin-left: auto;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce rapport ?');">
                <i class="fas fa-trash-alt"></i> Supprimer
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-finances.js') }}"></script>
@endsection