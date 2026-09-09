@extends('layouts.admin')

@section('title', 'Article — ' . $stock->designation . ' | PHOVE')

@section('page_title', Str::limit($stock->designation, 60))
@section('page_icon', 'box')
@section('page_subtitle', 'Fiche détaillée de l\'article — Réf : ' . $stock->reference)

@php
    $breadcrumb = [
        ['label' => 'Stocks', 'url' => route('admin.stocks.index')],
        $stock->designation
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
            <i class="fas fa-box"></i>
        </div>
        <div style="flex: 1;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #1E293B; margin: 0 0 4px 0;">{{ $stock->designation }}</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <span class="projet-badge" style="background: #E8F0F8; color: #1A5B9C;">
                    <i class="fas fa-tag"></i> {{ $stock->categorieLibelle() }}
                </span>
                <span class="projet-badge" style="background: #F1F5F9; color: #334155;">
                    <i class="fas fa-barcode"></i> {{ $stock->reference }}
                </span>
                @if($stock->statut === 'disponible')
                    <span class="projet-publication-badge projet-publie">
                        <i class="fas fa-check-circle"></i> Disponible
                    </span>
                @elseif($stock->statut === 'faible')
                    <span class="projet-publication-badge" style="background: #FEF3C7; color: #92400E;">
                        <i class="fas fa-exclamation-triangle"></i> Stock faible
                    </span>
                @else
                    <span class="projet-publication-badge" style="background: #FEE2E2; color: #B91C1C;">
                        <i class="fas fa-times-circle"></i> Rupture
                    </span>
                @endif
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <a href="{{ route('admin.stocks.formEntree', $stock) }}" class="btn btn-primary btn-sm" style="background: #10B981; border-color: #10B981;">
                <i class="fas fa-arrow-down"></i> Entrée
            </a>
            <a href="{{ route('admin.stocks.formSortie', $stock) }}" class="btn btn-primary btn-sm" style="background: #F59E0B; border-color: #F59E0B;">
                <i class="fas fa-arrow-up"></i> Sortie
            </a>
            <a href="{{ route('admin.stocks.edit', $stock) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline btn-sm">
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
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Désignation</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $stock->designation }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Référence</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $stock->reference }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Catégorie</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $stock->categorieLibelle() }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Unité</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $stock->unite }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Quantité initiale</span>
                        <span style="flex: 1; font-size: 13px; font-weight: 700; color: #1E293B;">{{ number_format($stock->quantite_initiale, 2, ',', ' ') }} {{ $stock->unite }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Quantité actuelle</span>
                        <span style="flex: 1; font-size: 16px; font-weight: 800; color: {{ $stock->quantite_actuelle > 0 ? '#10B981' : '#EF4444' }};">{{ number_format($stock->quantite_actuelle, 2, ',', ' ') }} {{ $stock->unite }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Seuil d'alerte</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $stock->seuil_alerte !== null ? number_format($stock->seuil_alerte, 2, ',', ' ') . ' ' . $stock->unite : '—' }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Emplacement</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $stock->emplacement ?? '—' }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Créé le</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $stock->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>

            @if($stock->description)
            <div class="card" style="margin-top: 24px; margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-file-alt"></i> Description</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <p style="font-size: 13px; color: #334155; line-height: 1.8;">{{ $stock->description }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE : STATISTIQUES --}}
        <div>
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-chart-bar"></i> Statistiques</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <div style="display: flex; padding: 12px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="flex: 1; font-size: 13px; color: #334155;"><i class="fas fa-arrow-down" style="color: #10B981;"></i> Total entrées</span>
                        <span style="font-size: 14px; font-weight: 700; color: #10B981;">+{{ number_format($stock->totalEntrees(), 2, ',', ' ') }} {{ $stock->unite }}</span>
                    </div>
                    <div style="display: flex; padding: 12px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="flex: 1; font-size: 13px; color: #334155;"><i class="fas fa-arrow-up" style="color: #F59E0B;"></i> Total sorties</span>
                        <span style="font-size: 14px; font-weight: 700; color: #F59E0B;">-{{ number_format($stock->totalSorties(), 2, ',', ' ') }} {{ $stock->unite }}</span>
                    </div>
                    <div style="display: flex; padding: 12px 0;">
                        <span style="flex: 1; font-size: 13px; color: #334155;"><i class="fas fa-balance-scale" style="color: #1A5B9C;"></i> Stock actuel</span>
                        <span style="font-size: 16px; font-weight: 800; color: #1A5B9C;">{{ number_format($stock->quantite_actuelle, 2, ',', ' ') }} {{ $stock->unite }}</span>
                    </div>
                </div>
            </div>

            {{-- Derniers mouvements --}}
            <div class="card" style="margin-top: 24px; margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-exchange-alt"></i> Derniers mouvements 
                        <span style="font-size: 11px; color: #64748B;">({{ $stock->mouvements->count() }})</span>
                    </h2>
                </div>
                <div style="padding: 16px 24px;">
                    @if($stock->mouvements->count() > 0)
                        @foreach($stock->mouvements->take(10) as $mouvement)
                        <div style="display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: {{ $mouvement->estEntree() ? '#D1FAE5' : '#FEE2E2' }}; color: {{ $mouvement->estEntree() ? '#10B981' : '#EF4444' }};">
                                <i class="fas fa-{{ $mouvement->estEntree() ? 'arrow-down' : 'arrow-up' }}"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 600; color: #1E293B;">
                                    {{ $mouvement->quantiteFormatee() }}
                                </div>
                                <div style="font-size: 11px; color: #64748B;">
                                    {{ $mouvement->date_mouvement->format('d/m/Y') }}
                                    @if($mouvement->motif)
                                        — {{ $mouvement->motif }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p style="text-align: center; color: #94A3B8; padding: 20px 0; font-size: 13px;">
                            Aucun mouvement pour le moment.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection