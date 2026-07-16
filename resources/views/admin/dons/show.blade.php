@extends('layouts.admin')

@section('title', 'Détail du don — ' . $don->donateur_nom)

@section('page_title', 'Détail du don')
@section('page_icon', 'hand-holding-heart')
@section('page_subtitle', 'Fiche détaillée du don de ' . $don->donateur_nom . ' — ' . number_format($don->montant, 0) . ' ' . $don->devise)

@php
    $breadcrumb = [
        ['label' => 'Dons', 'url' => route('admin.dons.index')],
        'Détail du don'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dons.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE --}}
    {{-- ============================================ --}}
    <div class="don-show-header">
        <div class="don-show-icon">
            <i class="fas fa-hand-holding-heart"></i>
        </div>
        <div class="don-show-info">
            <h2>Don de {{ $don->donateur_nom }}</h2>
            <p>
                <span class="don-badge don-badge-{{ $don->statut }}" style="margin-right: 8px;">
                    <i class="fas fa-{{ $don->statut === 'confirme' ? 'check-circle' : ($don->statut === 'en_attente' ? 'clock' : 'times-circle') }}"></i>
                    {{ $don->statut === 'confirme' ? 'Confirmé' : ($don->statut === 'en_attente' ? 'En attente' : 'Annulé') }}
                </span>
                @if($don->recu_genere)
                    <span class="don-recu-badge don-recu-genere">
                        <i class="fas fa-file-invoice"></i> Reçu n° {{ $don->numero_recu }}
                    </span>
                @endif
                &nbsp;|&nbsp; Enregistré le {{ $don->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <a href="{{ route('admin.dons.edit', $don) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('admin.dons.index') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE DE DÉTAIL --}}
    {{-- ============================================ --}}
    <div class="don-show-grid">

        {{-- COLONNE GAUCHE --}}
        <div>
            {{-- Informations du donateur --}}
            <div class="don-detail-card">
                <div class="don-detail-card-header">
                    <i class="fas fa-user"></i> Informations du donateur
                </div>
                <div class="don-detail-card-body">
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-user"></i> Nom complet</span>
                        <span class="don-detail-value">{{ $don->donateur_nom }}</span>
                    </div>
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-tag"></i> Type</span>
                        <span class="don-detail-value">
                            @php
                                $typeDonateur = match($don->type_donateur) {
                                    'particulier' => 'Particulier',
                                    'organisation' => 'Organisation',
                                    'etat' => 'État',
                                    'anonyme' => 'Anonyme',
                                    default => $don->type_donateur
                                };
                            @endphp
                            {{ $typeDonateur }}
                        </span>
                    </div>
                    @if($don->donateur_email)
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-envelope"></i> Email</span>
                        <span class="don-detail-value">{{ $don->donateur_email }}</span>
                    </div>
                    @endif
                    @if($don->donateur_telephone)
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-phone-alt"></i> Téléphone</span>
                        <span class="don-detail-value">{{ $don->donateur_telephone }}</span>
                    </div>
                    @endif
                    @if($don->donateur_adresse)
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-map-marker-alt"></i> Adresse</span>
                        <span class="don-detail-value">{{ $don->donateur_adresse }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Détails du don --}}
            <div class="don-detail-card" style="margin-top: 24px;">
                <div class="don-detail-card-header">
                    <i class="fas fa-coins"></i> Détails du don
                </div>
                <div class="don-detail-card-body">
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-dollar-sign"></i> Montant</span>
                        <span class="don-detail-value highlight">{{ number_format($don->montant, 2) }} {{ $don->devise }}</span>
                    </div>
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-sync-alt"></i> Type</span>
                        <span class="don-detail-value">{{ $don->type_don === 'ponctuel' ? 'Don ponctuel' : 'Don récurrent (mensuel)' }}</span>
                    </div>
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-credit-card"></i> Mode de paiement</span>
                        <span class="don-detail-value">{{ $don->modePaiementLibelle() }}</span>
                    </div>
                    @if($don->reference_transaction)
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-hashtag"></i> Référence</span>
                        <span class="don-detail-value">{{ $don->reference_transaction }}</span>
                    </div>
                    @endif
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-calendar-alt"></i> Date du don</span>
                        <span class="don-detail-value">{{ $don->date_don ? $don->date_don->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-circle"></i> Statut</span>
                        <span class="don-detail-value">
                            <span class="don-badge don-badge-{{ $don->statut }}">
                                {{ $don->statut === 'confirme' ? 'Confirmé' : ($don->statut === 'en_attente' ? 'En attente' : 'Annulé') }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Reçu fiscal --}}
            <div class="don-detail-card" style="margin-top: 24px;">
                <div class="don-detail-card-header">
                    <i class="fas fa-file-invoice"></i> Reçu fiscal
                </div>
                <div class="don-detail-card-body">
                    @if($don->recu_genere)
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-hashtag"></i> Numéro de reçu</span>
                        <span class="don-detail-value highlight">{{ $don->numero_recu }}</span>
                    </div>
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-calendar-check"></i> Date d'émission</span>
                        <span class="don-detail-value">{{ $don->date_recu ? $don->date_recu->format('d/m/Y') : '—' }}</span>
                    </div>
                    @else
                    <div style="text-align: center; padding: 20px; color: #999;">
                        <i class="fas fa-file-invoice" style="font-size: 2rem; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                        <p style="font-size: 13px; margin-bottom: 12px;">Aucun reçu fiscal généré pour ce don.</p>
                        <form action="{{ route('admin.dons.genererRecu', $don) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-file-invoice"></i> Générer le reçu fiscal
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- COLONNE DROITE --}}
        <div>
            {{-- Projet lié --}}
            <div class="don-detail-card">
                <div class="don-detail-card-header">
                    <i class="fas fa-project-diagram"></i> Projet soutenu
                </div>
                <div class="don-detail-card-body">
                    @if($don->projet)
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-project-diagram"></i> Projet</span>
                        <span class="don-detail-value">{{ $don->projet->titre }}</span>
                    </div>
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-tag"></i> Domaine</span>
                        <span class="don-detail-value">{{ $don->projet->domaineLibelle() }}</span>
                    </div>
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-map-marker-alt"></i> Zone</span>
                        <span class="don-detail-value">{{ $don->projet->zone_geographique }}</span>
                    </div>
                    <div style="text-align: center; margin-top: 12px;">
                        <a href="{{ route('admin.projets.show', $don->projet) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i> Voir le projet
                        </a>
                    </div>
                    @else
                    <div style="text-align: center; padding: 20px; color: #999;">
                        <i class="fas fa-globe-africa" style="font-size: 2rem; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                        <p style="font-size: 13px;">Don général (non affecté à un projet spécifique).</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Message du donateur --}}
            @if($don->message_donateur)
            <div class="don-detail-card" style="margin-top: 24px;">
                <div class="don-detail-card-header">
                    <i class="fas fa-quote-right"></i> Message du donateur
                </div>
                <div class="don-detail-card-body">
                    <div style="font-size: 13px; color: #555; line-height: 1.8; font-style: italic; padding: 12px; background: var(--color-bg-light); border-radius: 10px;">
                        « {{ $don->message_donateur }} »
                    </div>
                </div>
            </div>
            @endif

            {{-- Informations d'enregistrement --}}
            <div class="don-detail-card" style="margin-top: 24px;">
                <div class="don-detail-card-header">
                    <i class="fas fa-info-circle"></i> Enregistrement
                </div>
                <div class="don-detail-card-body">
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-calendar-plus"></i> Créé le</span>
                        <span class="don-detail-value">{{ $don->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div class="don-detail-row">
                        <span class="don-detail-label"><i class="fas fa-sync-alt"></i> Mis à jour le</span>
                        <span class="don-detail-value">{{ $don->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS --}}
    {{-- ============================================ --}}
    <div style="display: flex; gap: 10px; margin-top: 28px;">
        <a href="{{ route('admin.dons.edit', $don) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Modifier ce don
        </a>
        @if($don->recu_genere)
            <a href="{{ route('admin.dons.telechargerRecu', ['don' => $don, 'view' => 1]) }}" target="_blank" class="btn btn-info">
                <i class="fas fa-eye"></i> Voir le reçu
            </a>
            <a href="{{ route('admin.dons.telechargerRecu', $don) }}" class="btn btn-accent">
                <i class="fas fa-download"></i> Télécharger le PDF
            </a>
        @else
            <form action="{{ route('admin.dons.genererRecu', $don) }}" method="POST" style="display: inline;">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-invoice"></i> Générer le reçu fiscal
                </button>
            </form>
        @endif
        <a href="{{ route('admin.dons.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-dons.js') }}"></script>
@endsection