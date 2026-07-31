@extends('layouts.admin')

@section('title', 'Détail du partenaire — ' . $partenaire->nom)

@section('page_title', 'Détail du partenaire')
@section('page_icon', 'handshake')
@section('page_subtitle', 'Fiche détaillée du partenaire : ' . $partenaire->nom)

@php
    $breadcrumb = [
        ['label' => 'Partenaires', 'url' => route('admin.partenaires.index')],
        $partenaire->nom
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-partenaires.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE --}}
    {{-- ============================================ --}}
    <div class="partenaire-show-header">
        <div class="partenaire-show-logo">
            @if($partenaire->logo)
                <img src="{{ asset('storage/' . $partenaire->logo) }}" alt="{{ $partenaire->nom }}">
            @else
                <span>{{ Str::upper(Str::substr($partenaire->nom, 0, 2)) }}</span>
            @endif
        </div>
        <div class="partenaire-show-info">
            <h2>{{ $partenaire->nom }}</h2>
            <p>
                <span class="partenaire-type partenaire-type-{{ $partenaire->type }}" style="margin-right: 8px;">
                    <i class="fas fa-tag"></i> {{ $partenaire->typeLibelle() }}
                </span>
                <span class="partenaire-statut-badge partenaire-statut-{{ $partenaire->statut }}">
                    <i class="fas fa-circle"></i> {{ $partenaire->statut === 'actif' ? 'Actif' : 'Inactif' }}
                </span>
                &nbsp;|&nbsp;
                <span style="font-size: 13px; color: #888;">
                    <i class="fas fa-calendar-alt"></i> Partenaire depuis {{ $partenaire->created_at->format('d/m/Y') }}
                </span>
            </p>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <a href="{{ route('admin.partenaires.edit', $partenaire) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('admin.partenaires.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE DE DÉTAIL --}}
    {{-- ============================================ --}}
    <div class="partenaire-show-grid">

        {{-- COLONNE GAUCHE : INFORMATIONS --}}
        <div>
            <div class="partenaire-detail-card">
                <div class="partenaire-detail-card-header">
                    <i class="fas fa-info-circle"></i> Informations générales
                </div>
                <div class="partenaire-detail-card-body">
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-building"></i> Nom</span>
                        <span class="partenaire-detail-value">{{ $partenaire->nom }}</span>
                    </div>
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-tag"></i> Type</span>
                        <span class="partenaire-detail-value">{{ $partenaire->typeLibelle() }}</span>
                    </div>
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-circle"></i> Statut</span>
                        <span class="partenaire-detail-value">{{ $partenaire->statut === 'actif' ? 'Actif' : 'Inactif' }}</span>
                    </div>
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-calendar-plus"></i> Début du partenariat</span>
                        <span class="partenaire-detail-value">{{ $partenaire->date_debut ? $partenaire->date_debut->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-calendar-minus"></i> Fin du partenariat</span>
                        <span class="partenaire-detail-value">{{ $partenaire->date_fin ? $partenaire->date_fin->format('d/m/Y') : 'En cours' }}</span>
                    </div>
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-clock"></i> Dernière modification</span>
                        <span class="partenaire-detail-value">{{ $partenaire->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="partenaire-detail-card" style="margin-top: 24px;">
                <div class="partenaire-detail-card-header">
                    <i class="fas fa-address-book"></i> Coordonnées
                </div>
                <div class="partenaire-detail-card-body">
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-envelope"></i> Email</span>
                        <span class="partenaire-detail-value">{{ $partenaire->email ?? '—' }}</span>
                    </div>
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-phone-alt"></i> Téléphone</span>
                        <span class="partenaire-detail-value">{{ $partenaire->telephone ?? '—' }}</span>
                    </div>
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-map-marker-alt"></i> Adresse</span>
                        <span class="partenaire-detail-value">{{ $partenaire->adresse ?? '—' }}</span>
                    </div>
                    <div class="partenaire-detail-row">
                        <span class="partenaire-detail-label"><i class="fas fa-globe"></i> Site web</span>
                        <span class="partenaire-detail-value">
                            @if($partenaire->site_web)
                                <a href="{{ $partenaire->site_web }}" target="_blank" style="color: var(--color-primary);">
                                    {{ $partenaire->site_web }} <i class="fas fa-external-link-alt" style="font-size: 10px;"></i>
                                </a>
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            @if($partenaire->description)
            <div class="partenaire-detail-card" style="margin-top: 24px;">
                <div class="partenaire-detail-card-header">
                    <i class="fas fa-sticky-note"></i> Description
                </div>
                <div class="partenaire-detail-card-body">
                    <div style="font-size: 13px; color: #555; line-height: 1.8; white-space: pre-line;">
                        {{ $partenaire->description }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE : PROJETS --}}
        <div>
            <div class="partenaire-detail-card">
                <div class="partenaire-detail-card-header">
                    <i class="fas fa-project-diagram"></i> Projets associés 
                    <span style="font-size: 11px; color: #888; margin-left: 4px;">({{ $partenaire->projets->count() }})</span>
                </div>
                <div class="partenaire-detail-card-body">
                    @if($partenaire->projets->count() > 0)
                    <div class="partenaire-projets-list">
                        @foreach($partenaire->projets as $projet)
                        <div class="partenaire-projet-item">
                            <div style="min-width: 0;">
                                <a href="{{ route('admin.projets.show', $projet) }}" style="font-weight: 600;">
                                    {{ Str::limit($projet->titre, 50) }}
                                </a>
                                <div style="font-size: 11px; color: #888; margin-top: 2px;">
                                    <i class="fas fa-map-marker-alt"></i> {{ $projet->zone_geographique }} &nbsp;|&nbsp;
                                    <span class="projet-statut-badge projet-statut-{{ $projet->statut }}" style="font-size: 10px;">
                                        {{ $projet->statutLibelle() }}
                                    </span>
                                </div>
                            </div>
                            <span style="font-size: 12px; font-weight: 600; color: var(--color-primary); white-space: nowrap;">
                                {{ number_format($projet->budget_utilise, 0, ',', ' ') }} / {{ number_format($projet->budget_total, 0, ',', ' ') }} $
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align: center; padding: 30px 20px; color: #999;">
                        <i class="fas fa-project-diagram" style="font-size: 2rem; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                        <p style="font-size: 13px;">Aucun projet associé à ce partenaire.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS --}}
    {{-- ============================================ --}}
    <div style="display: flex; gap: 10px; margin-top: 28px;">
        <a href="{{ route('admin.partenaires.edit', $partenaire) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Modifier ce partenaire
        </a>
        <a href="{{ route('admin.partenaires.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <form action="{{ route('admin.partenaires.destroy', $partenaire) }}" method="POST" style="display: inline; margin-left: auto;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce partenaire ?');">
                <i class="fas fa-trash-alt"></i> Supprimer
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-partenaires.js') }}"></script>
@endsection