@extends('layouts.admin')

@section('title', 'Membres en retard de cotisation')

@section('page_title', 'Membres en retard')
@section('page_icon', 'exclamation-triangle')
@section('page_subtitle', 'Membres actifs n\'ayant pas payé leur cotisation pour le mois sélectionné.')

@php
    $breadcrumb = [
        ['label' => 'Cotisations', 'url' => route('admin.cotisations.index')],
        'Membres en retard'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-cotisations.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI --}}
    {{-- ============================================ --}}
    <div class="cotisations-stats-grid">
        <div class="cotisation-stat-card">
            <div class="cotisation-stat-icon" style="background: var(--color-error-light); color: var(--color-accent);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="cotisation-stat-content">
                <div class="cotisation-stat-value">{{ $totalEnRetard }}</div>
                <div class="cotisation-stat-label">Membres en retard</div>
                <div class="cotisation-stat-sub down">{{ \Carbon\Carbon::createFromFormat('Y-m', $moisEnCours)->translatedFormat('F Y') }}</div>
            </div>
        </div>
        <a href="{{ route('admin.cotisations.regle', ['mois' => $moisEnCours]) }}" style="text-decoration: none; color: inherit;">
            <div class="cotisation-stat-card" style="cursor: pointer;">
                <div class="cotisation-stat-icon" style="background: var(--color-success-light); color: var(--color-success);">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="cotisation-stat-content">
                    <div class="cotisation-stat-value">{{ $totalEnRegle }}</div>
                    <div class="cotisation-stat-label">Membres en règle</div>
                </div>
            </div>
        </a>
        <div class="cotisation-stat-card">
            <div class="cotisation-stat-icon" style="background: var(--color-primary-lighter); color: var(--color-primary);">
                <i class="fas fa-users"></i>
            </div>
            <div class="cotisation-stat-content">
                <div class="cotisation-stat-value">{{ $totalActifs }}</div>
                <div class="cotisation-stat-label">Total membres actifs</div>
            </div>
        </div>
        <div class="cotisation-stat-card">
            <div class="cotisation-stat-icon" style="background: #fff3cd; color: #e67e22;">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="cotisation-stat-content">
                <div class="cotisation-stat-value">{{ $tauxPaiement }}%</div>
                <div class="cotisation-stat-label">Taux de paiement</div>
                <div class="recouvrement-progress">
                    <div class="recouvrement-progress-fill {{ $tauxPaiement >= 80 ? 'high' : ($tauxPaiement >= 50 ? 'medium' : 'low') }}" 
                         style="width: {{ $tauxPaiement }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FILTRE PAR MOIS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.cotisations.retard') }}" method="GET" class="cotisations-toolbar" style="margin-bottom: var(--spacing-xl);">
        <div class="cotisations-filtres">
            <select name="mois" class="cotisations-filtre-select" onchange="this.form.submit()">
                @foreach($moisDisponibles as $m)
                    <option value="{{ $m }}" {{ $moisEnCours === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>
        </div>
        <a href="{{ route('admin.cotisations.create') }}" class="cotisations-add-btn">
            <i class="fas fa-plus-circle"></i> Enregistrer un paiement
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU --}}
    {{-- ============================================ --}}
    <div class="cotisations-table-card">
        @if($membresEnRetard->count() > 0)
        <div class="table-responsive">
            <table class="table-cotisations">
                <thead>
                    <tr>
                        <th>Membre</th>
                        <th>Catégorie</th>
                        <th>Contact</th>
                        <th>Dernier paiement</th>
                        <th>Cotisation mensuelle</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($membresEnRetard as $membre)
                    <tr>
                        <td>
                            <div class="cotisation-cell-membre">
                                <div class="cotisation-cell-avatar">
                                    @if($membre->photo)
                                        <img src="{{ asset('storage/' . $membre->photo) }}" alt="{{ $membre->nomComplet() }}">
                                    @else
                                        <span>{{ Str::upper(Str::substr($membre->nom, 0, 1) . Str::substr($membre->prenom ?? '', 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="cotisation-cell-nom">{{ $membre->nomComplet() }}</span>
                                    <span class="cotisation-cell-categorie">
                                        {{ match($membre->categorie) {
                                            'initiateur' => 'Initiateur', 'fondateur' => 'Fondateur',
                                            'adherent' => 'Adhérent', 'honneur' => 'Honneur',
                                            'sympathisant' => 'Sympathisant', default => $membre->categorie
                                        } }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="membre-badge membre-badge-{{ $membre->categorie === 'fondateur' ? 'danger' : 'primary' }}">
                                {{ match($membre->categorie) {
                                    'initiateur' => 'Initiateur', 'fondateur' => 'Fondateur',
                                    'adherent' => 'Adhérent', 'honneur' => 'Honneur',
                                    'sympathisant' => 'Sympathisant', default => $membre->categorie
                                } }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size: 12px;">
                                <div><i class="fas fa-phone-alt" style="color: var(--color-accent); font-size: 10px;"></i> {{ $membre->telephone }}</div>
                                @if($membre->email)
                                <div style="color: var(--color-text-muted);"><i class="fas fa-envelope" style="font-size: 10px;"></i> {{ $membre->email }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php $dernierPaiement = $membre->cotisations->first(); @endphp
                            @if($dernierPaiement)
                                <span style="font-size: 13px;">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $dernierPaiement->mois)->translatedFormat('F Y') }}
                                </span>
                                <br>
                                <span style="font-size: 11px; color: var(--color-text-muted);">
                                    {{ $dernierPaiement->date_paiement ? $dernierPaiement->date_paiement->format('d/m/Y') : '—' }}
                                </span>
                            @else
                                <span style="color: var(--color-accent); font-size: 12px;">Jamais payé</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight: 600; font-size: 13px;">{{ number_format($membre->cotisation_mensuelle, 2) }} $</span>
                        </td>
                        <td>
                            <div class="membre-actions">
                                <a href="{{ route('admin.cotisations.create', ['membre_id' => $membre->id, 'mois' => $moisEnCours]) }}" 
                                   class="membre-action-btn" 
                                   title="Enregistrer un paiement"
                                   style="color: var(--color-success); border-color: var(--color-success);">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                <a href="{{ route('admin.membres.show', $membre) }}" 
                                   class="membre-action-btn view" 
                                   title="Voir le membre">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($membresEnRetard->hasPages())
        <div class="cotisations-pagination">
            {{ $membresEnRetard->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="cotisations-empty">
            <div class="cotisations-empty-icon"><i class="fas fa-check-circle" style="color: var(--color-success);"></i></div>
            <h4>Tous les membres sont en règle !</h4>
            <p>Tous les membres actifs ont payé leur cotisation pour {{ \Carbon\Carbon::createFromFormat('Y-m', $moisEnCours)->translatedFormat('F Y') }}.</p>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-cotisations.js') }}"></script>
@endsection