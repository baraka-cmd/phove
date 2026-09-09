@extends('layouts.admin')

@section('title', 'Structure — ' . $structure->nom . ' | PHOVE')

@section('page_title', Str::limit($structure->nom, 60))
@section('page_icon', 'sitemap')
@section('page_subtitle', 'Fiche détaillée de la structure — ' . $structure->typeLibelle())

@php
    $breadcrumb = [
        ['label' => 'Structures', 'url' => route('admin.structures.index')],
        $structure->nom
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
            <i class="fas fa-{{ $structure->type === 'station' ? 'flag' : ($structure->type === 'base' ? 'building' : 'map-marker-alt') }}"></i>
        </div>
        <div style="flex: 1;">
            <h2 style="font-size: 1.3rem; font-weight: 700; color: #1E293B; margin: 0 0 4px 0;">{{ $structure->nom }}</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <span class="projet-badge" style="background: #DBEAFE; color: #1E40AF;">
                    <i class="fas fa-tag"></i> {{ $structure->typeLibelle() }}
                </span>
                <span class="projet-publication-badge {{ $structure->statut === 'actif' ? 'projet-publie' : 'projet-non-publie' }}">
                    <i class="fas fa-circle" style="font-size: 8px;"></i>
                    {{ $structure->statut === 'actif' ? 'Actif' : 'Inactif' }}
                </span>
                @if($structure->parent)
                <span class="projet-badge" style="background: #FEF3C7; color: #92400E;">
                    <i class="fas fa-level-up-alt"></i> Parent : {{ $structure->parent->nom }}
                </span>
                @endif
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <a href="{{ route('admin.structures.edit', $structure) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('admin.structures.index') }}" class="btn btn-outline btn-sm">
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
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Nom</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $structure->nom }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Type</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $structure->typeLibelle() }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Pays</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $structure->pays ?? '—' }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Province</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $structure->province ?? '—' }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Territoire</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $structure->territoire ?? '—' }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Adresse</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $structure->adresse ?? '—' }}</span>
                    </div>
                    <div style="display: flex; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Statut</span>
                        <span style="flex: 1; font-size: 13px; font-weight: 600; color: {{ $structure->statut === 'actif' ? '#10B981' : '#EF4444' }};">
                            {{ $structure->statut === 'actif' ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    <div style="display: flex; padding: 10px 0;">
                        <span style="width: 150px; font-size: 13px; color: #64748B; font-weight: 600;">Créée le</span>
                        <span style="flex: 1; font-size: 13px; color: #1E293B;">{{ $structure->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>

            @if($structure->description)
            <div class="card" style="margin-top: 24px; margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-file-alt"></i> Description</h2>
                </div>
                <div style="padding: 16px 24px;">
                    <p style="font-size: 13px; color: #334155; line-height: 1.8;">{{ $structure->description }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE : ENFANTS ET POSTES --}}
        <div>
            {{-- Enfants --}}
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-sitemap"></i> Structures enfants 
                        <span style="font-size: 11px; color: #64748B;">({{ $structure->enfants->count() }})</span>
                    </h2>
                </div>
                <div style="padding: 16px 24px;">
                    @if($structure->enfants->count() > 0)
                        @foreach($structure->enfants as $enfant)
                        <div style="display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                            <div style="width: 35px; height: 35px; border-radius: 8px; background: #E8F0F8; display: flex; align-items: center; justify-content: center; color: #1A5B9C; flex-shrink: 0;">
                                <i class="fas fa-{{ $enfant->type === 'station' ? 'flag' : ($enfant->type === 'base' ? 'building' : 'map-marker-alt') }}"></i>
                            </div>
                            <div style="flex: 1;">
                                <a href="{{ route('admin.structures.show', $enfant) }}" style="font-size: 13px; font-weight: 600; color: #1E293B;">
                                    {{ $enfant->nom }}
                                </a>
                                <div style="font-size: 11px; color: #64748B;">{{ $enfant->typeLibelle() }}</div>
                            </div>
                            <a href="{{ route('admin.structures.edit', $enfant) }}" class="projet-action-btn edit" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        @endforeach
                    @else
                        <p style="text-align: center; color: #94A3B8; padding: 20px 0; font-size: 13px;">
                            Aucune structure enfant.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Postes --}}
            <div class="card" style="margin-top: 24px; margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user-tie"></i> Postes rattachés 
                        <span style="font-size: 11px; color: #64748B;">({{ $structure->organes->count() }})</span>
                    </h2>
                </div>
                <div style="padding: 16px 24px;">
                    @if($structure->organes->count() > 0)
                        @foreach($structure->organes as $organe)
                        <div style="display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #E2E8F0;">
                            <div style="width: 35px; height: 35px; border-radius: 50%; background: #FEF3C7; display: flex; align-items: center; justify-content: center; color: #92400E; font-size: 13px; font-weight: 700; flex-shrink: 0;">
                                {{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 600; color: #1E293B;">
                                    {{ $organe->membre ? $organe->membre->nomComplet() : '—' }}
                                </div>
                                <div style="font-size: 11px; color: #64748B;">{{ $organe->poste }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p style="text-align: center; color: #94A3B8; padding: 20px 0; font-size: 13px;">
                            Aucun poste rattaché à cette structure.
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