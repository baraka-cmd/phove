@extends('layouts.admin')

@section('title', 'Fiche membre : ' . $membre->nomComplet())

@section('page_title', 'Fiche détaillée')
@section('page_icon', 'address-card')
@section('page_subtitle', 'Informations complètes du membre ' . $membre->nomComplet())

@php
    $breadcrumb = [
        ['label' => 'Membres', 'url' => route('admin.membres.index')],
        $membre->nomComplet()
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-membre-show.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-membres.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE DU MEMBRE --}}
    {{-- ============================================ --}}
    <div class="membre-show-header">
        <div class="membre-show-avatar">
            @if($membre->photo)
                <img src="{{ asset('storage/' . $membre->photo) }}" alt="{{ $membre->nomComplet() }}">
            @else
                <span>{{ Str::upper(Str::substr($membre->nom, 0, 1) . Str::substr($membre->prenom, 0, 1)) }}</span>
            @endif
        </div>
        <div class="membre-show-info">
            <h2>{{ $membre->nomComplet() }}</h2>
            <p>
                <span class="membre-badge membre-badge-{{ $membre->categorie === 'fondateur' || $membre->categorie === 'initiateur' ? 'danger' : ($membre->categorie === 'adherent' ? 'success' : 'primary') }}">
                    <i class="fas fa-tag"></i>
                    @php
                        echo match($membre->categorie) {
                            'initiateur' => 'Initiateur',
                            'fondateur' => 'Fondateur',
                            'adherent' => 'Adhérent',
                            'honneur' => 'Honneur',
                            'sympathisant' => 'Sympathisant',
                            default => $membre->categorie
                        };
                    @endphp
                </span>
                <span class="membre-badge membre-badge-{{ $membre->statut === 'actif' ? 'success' : ($membre->statut === 'en_attente' ? 'warning' : 'danger') }}">
                    <i class="fas fa-circle"></i>
                    @php
                        echo match($membre->statut) {
                            'actif' => 'Actif',
                            'en_attente' => 'En attente',
                            'suspendu' => 'Suspendu',
                            'exclu' => 'Exclu',
                            'deserteur' => 'Déserteur',
                            'demissionnaire' => 'Démissionnaire',
                            default => $membre->statut
                        };
                    @endphp
                </span>
                <span style="font-size: 13px; color: var(--color-text-muted);">
                    <i class="fas fa-calendar-alt"></i> Adhéré le {{ $membre->date_adhesion ? $membre->date_adhesion->format('d/m/Y') : '—' }}
                </span>
            </p>
        </div>
        <div style="margin-left: auto; display: flex; gap: 8px;">
            <a href="{{ route('admin.membres.edit', $membre) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <form action="{{ route('admin.membres.destroy', $membre) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-accent btn-sm btn-delete-membre" data-nom="{{ $membre->nomComplet() }}" onclick="return confirm('Supprimer définitivement {{ $membre->nomComplet() }} ?');">
                    <i class="fas fa-trash-alt"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE DE DÉTAIL --}}
    {{-- ============================================ --}}
    <div class="membre-show-grid">
        
        {{-- COLONNE GAUCHE --}}
        <div>
            {{-- Carte : Informations personnelles --}}
            <div class="membre-detail-card">
                <div class="membre-detail-card-header">
                    <i class="fas fa-user"></i> Informations personnelles
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Nom complet</span>
                        <span class="membre-detail-value">{{ $membre->nomComplet() }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Sexe</span>
                        <span class="membre-detail-value">{{ $membre->sexe === 'M' ? 'Masculin' : 'Féminin' }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Catégorie</span>
                        <span class="membre-detail-value">
                            @php
                                echo match($membre->categorie) {
                                    'initiateur' => 'Initiateur',
                                    'fondateur' => 'Fondateur',
                                    'adherent' => 'Adhérent',
                                    'honneur' => 'Honneur',
                                    'sympathisant' => 'Sympathisant',
                                    default => $membre->categorie
                                };
                            @endphp
                        </span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Statut</span>
                        <span class="membre-detail-value">{{ ucfirst($membre->statut) }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Date d'adhésion</span>
                        <span class="membre-detail-value">{{ $membre->date_adhesion ? $membre->date_adhesion->format('d/m/Y') : '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Carte : Coordonnées --}}
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-address-book"></i> Coordonnées
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Adresse</span>
                        <span class="membre-detail-value">{{ $membre->adresse }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Téléphone</span>
                        <span class="membre-detail-value">{{ $membre->telephone }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Email</span>
                        <span class="membre-detail-value">{{ $membre->email ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Carte : Finances --}}
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-coins"></i> Informations financières
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Frais d'adhésion</span>
                        <span class="membre-detail-value">{{ number_format($membre->frais_adhesion, 2) }} $</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Cotisation mensuelle</span>
                        <span class="membre-detail-value">{{ number_format($membre->cotisation_mensuelle, 2) }} $</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Total cotisations payées</span>
                        <span class="membre-detail-value" style="color: var(--color-success); font-size: 1.1rem;">{{ number_format($totalCotisations, 2) }} $</span>
                    </div>
                </div>
            </div>

            {{-- Carte : Postes occupés --}}
            @if($membre->organes && $membre->organes->count() > 0)
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-sitemap"></i> Postes dans les organes
                </div>
                <div class="membre-detail-card-body">
                    @foreach($membre->organes as $organe)
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">
                            @php
                                echo match($organe->organe) {
                                    'assemblee_generale' => 'Assemblée Générale',
                                    'conseil_administration' => 'Conseil d\'Administration',
                                    'direction_executive' => 'Direction Exécutive',
                                    'commission_controle' => 'Commission de Contrôle',
                                    default => $organe->organe
                                };
                            @endphp
                        </span>
                        <span class="membre-detail-value">
                            {{ $organe->poste }}
                            @if($organe->actif)
                                <span class="membre-badge membre-badge-success" style="margin-left: 6px;">Actif</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE --}}
        <div>
            {{-- Carte : Sanctions --}}
            @if($membre->motif_sanction)
            <div class="membre-detail-card" style="border-left: 4px solid var(--color-accent);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-ban" style="color: var(--color-accent);"></i> Sanction en cours
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Motif</span>
                        <span class="membre-detail-value">{{ $membre->motif_sanction }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Date</span>
                        <span class="membre-detail-value">{{ $membre->date_sanction ? $membre->date_sanction->format('d/m/Y') : '—' }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Carte : Historique des cotisations --}}
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-history"></i> Historique des cotisations
                </div>
                <div class="membre-detail-card-body" style="padding: 0;">
                    @if($membre->cotisations && $membre->cotisations->count() > 0)
                    <div class="table-responsive">
                        <table class="membre-cotisations-table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($membre->cotisations->take(12) as $cotisation)
                                <tr>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $cotisation->mois)->format('F Y') }}</td>
                                    <td style="font-weight: 600;">{{ number_format($cotisation->montant, 2) }} $</td>
                                    <td>{{ $cotisation->modePaiementLibelle() }}</td>
                                    <td>{{ $cotisation->date_paiement ? $cotisation->date_paiement->format('d/m/Y') : '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div style="padding: 24px; text-align: center; color: var(--color-text-muted);">
                        <i class="fas fa-info-circle"></i> Aucune cotisation enregistrée pour le moment.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Carte : Actions rapides --}}
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-bolt"></i> Actions rapides
                </div>
                <div class="membre-detail-card-body" style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('admin.membres.edit', $membre) }}" class="btn btn-outline btn-sm" style="justify-content: center;">
                        <i class="fas fa-edit"></i> Modifier ce membre
                    </a>
                    @if($membre->statut === 'actif')
                    <form action="{{ route('admin.membres.changeStatus', $membre) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="statut" value="suspendu">
                        <button type="submit" class="btn btn-outline btn-sm" style="width: 100%; justify-content: center; color: var(--color-accent); border-color: var(--color-accent);">
                            <i class="fas fa-ban"></i> Suspendre ce membre
                        </button>
                    </form>
                    @elseif($membre->statut === 'suspendu')
                    <form action="{{ route('admin.membres.changeStatus', $membre) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="statut" value="actif">
                        <button type="submit" class="btn btn-outline btn-sm" style="width: 100%; justify-content: center; color: var(--color-success); border-color: var(--color-success);">
                            <i class="fas fa-check"></i> Réactiver ce membre
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('admin.membres.index') }}" class="btn btn-outline btn-sm" style="justify-content: center;">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-membres.js') }}"></script>
@endsection