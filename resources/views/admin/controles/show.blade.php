@extends('layouts.admin')

@section('title', 'Contrôle — ' . $controle->reference . ' | PHOVE')

@section('page_title', 'Détail du contrôle')
@section('page_icon', 'search-dollar')
@section('page_subtitle', 'Rapport de contrôle : ' . $controle->reference . ' — ' . $controle->trimestreLibelle() . ' ' . $controle->annee)

@php
    $breadcrumb = [
        ['label' => 'Contrôles', 'url' => route('admin.controles.index')],
        $controle->reference
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE --}}
    {{-- ============================================ --}}
    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; background: #FFFFFF; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);">
        <div style="width: 60px; height: 60px; border-radius: 14px; background: #E8F0F8; display: flex; align-items: center; justify-content: center; color: #1A5B9C; font-size: 1.6rem; flex-shrink: 0;">
            <i class="fas fa-search-dollar"></i>
        </div>
        <div style="flex: 1;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #1E293B; margin: 0 0 4px 0;">{{ $controle->reference }}</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <span class="projet-badge" style="background: #E8F0F8; color: #1A5B9C;">
                    <i class="fas fa-calendar-alt"></i> {{ $controle->trimestreLibelle() }} {{ $controle->annee }}
                </span>
                <span class="projet-badge" style="background: #F1F5F9; color: #334155;">
                    <i class="fas fa-tag"></i> {{ $controle->typeLibelle() }}
                </span>
                @if($controle->conformite)
                    <span class="projet-publication-badge projet-publie">
                        <i class="fas fa-check-circle"></i> Conforme
                    </span>
                @else
                    <span class="projet-publication-badge" style="background: #FEE2E2; color: #B91C1C;">
                        <i class="fas fa-exclamation-triangle"></i> Non conforme
                    </span>
                @endif
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
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            @if($controle->estTermine())
            <form action="{{ route('admin.controles.valider', $controle) }}" method="POST" style="display: inline;">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-primary btn-sm" style="background: #10B981; border-color: #10B981;">
                    <i class="fas fa-check"></i> Valider
                </button>
            </form>
            @endif
            @if(!$controle->estValide())
            <a href="{{ route('admin.controles.edit', $controle) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @endif
            <a href="{{ route('admin.controles.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE DE DÉTAIL --}}
    {{-- ============================================ --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">

        {{-- COLONNE GAUCHE : INFORMATIONS --}}
        <div>
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-info-circle"></i> Informations</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Référence</span>
                        <span style="flex: 1; font-size: 13px; font-weight: 700; color: #1A5B9C;">{{ $controle->reference }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Trimestre</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $controle->trimestreLibelle() }} {{ $controle->annee }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Période contrôlée</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $controle->periodeFormatee() }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Date du contrôle</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $controle->date_controle->format('d/m/Y') }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Type</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $controle->typeLibelle() }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Contrôleur</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $controle->nomControlePar() }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Statut</span>
                        <span style="flex: 1; font-size: 13px; font-weight: 600; color: #1E293B;">{{ $controle->statutLibelle() }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Conformité</span>
                        <span style="flex: 1; font-size: 13px; font-weight: 700; color: {{ $controle->conformite ? '#10B981' : '#EF4444' }};">
                            {{ $controle->conformite ? '✓ Conforme' : '✗ Non conforme' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Validation --}}
            @if($controle->validePar || $controle->date_validation)
            <div class="card" style="margin-top: 24px; margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-check-circle"></i> Validation</h2>
                </div>
                <div style="padding: 16px 24px;">
                    @if($controle->validePar)
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Validé par</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $controle->nomValidePar() }}</span>
                    </div>
                    @endif
                    @if($controle->date_validation)
                    <div style="display: flex; padding: 10px 0;">
                        <span style="width: 160px; font-size: 13px; color: #64748B; font-weight: 600;">Date de validation</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $controle->date_validation->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE : MONTANTS --}}
        <div>
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-calculator"></i> Montants constatés</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="font-size: 13px; color: #334155;"><i class="fas fa-arrow-down" style="color: #10B981;"></i> Total entrées</span>
                        <span style="font-size: 14px; font-weight: 700; color: #10B981;">+{{ number_format($controle->total_entrees, 0, ',', ' ') }} $</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="font-size: 13px; color: #334155;"><i class="fas fa-arrow-up" style="color: #EF4444;"></i> Total sorties</span>
                        <span style="font-size: 14px; font-weight: 700; color: #EF4444;">-{{ number_format($controle->total_sorties, 0, ',', ' ') }} $</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0; background: #F8FAFC; border-radius: 10px; margin-top: 8px;">
                        <span style="font-size: 14px; font-weight: 700; color: #1E293B;">
                            <i class="fas fa-equals" style="color: #1A5B9C;"></i> ÉCART
                        </span>
                        <span style="font-size: 20px; font-weight: 800; color: {{ $controle->ecart >= 0 ? '#1A5B9C' : '#EF4444' }};">
                            {{ number_format($controle->ecart, 0, ',', ' ') }} $
                        </span>
                    </div>
                </div>
            </div>

            {{-- Documents vérifiés --}}
            @if($controle->documents_verifies)
            <div class="card" style="margin-top: 24px; margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-folder-open"></i> Documents vérifiés</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <p style="font-size: 13px; color: #334155; line-height: 1.8; white-space: pre-line;">{{ $controle->documents_verifies }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- OBSERVATIONS ET RECOMMANDATIONS --}}
    {{-- ============================================ --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
        @if($controle->observations)
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-comment"></i> Observations</h2>
            </div>
            <div style="padding: 16px 24px;">
                <p style="font-size: 13px; color: #334155; line-height: 1.8; white-space: pre-line;">{{ $controle->observations }}</p>
            </div>
        </div>
        @endif

        @if($controle->recommandations)
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-lightbulb"></i> Recommandations</h2>
            </div>
            <div style="padding: 16px 24px;">
                <p style="font-size: 13px; color: #334155; line-height: 1.8; white-space: pre-line;">{{ $controle->recommandations }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS FINALES --}}
    {{-- ============================================ --}}
    <div style="display: flex; gap: 10px; margin-top: 24px;">
        <a href="{{ route('admin.controles.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        @if(!$controle->estValide())
        <form action="{{ route('admin.controles.destroy', $controle) }}" method="POST" style="display: inline; margin-left: auto;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce contrôle ?');">
                <i class="fas fa-trash-alt"></i> Supprimer
            </button>
        </form>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection