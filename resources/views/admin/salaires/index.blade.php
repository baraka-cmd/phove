@extends('layouts.admin')

@section('title', 'Gestion des Salaires | PHOVE')

@section('page_title', 'Gestion des Salaires')
@section('page_icon', 'money-bill-wave')
@section('page_subtitle', 'Gérez les salaires du personnel : brouillons, validations, paiements.')

@php
    $breadcrumb = [ 'Salaires' ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES (Mois courant) --}}
    {{-- ============================================ --}}
    <div class="projets-stats-grid">
        <div class="projet-stat-card">
            <div class="projet-stat-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value">{{ $totalSalairesMois }}</div>
                <div class="projet-stat-label">Salaires {{ $moisCourant }}</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #DBEAFE; color: #1E40AF;"><i class="fas fa-check-circle"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #1E40AF;">{{ $totalValidesMois }}</div>
                <div class="projet-stat-label">Validés (à payer)</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #D1FAE5; color: #065F46;"><i class="fas fa-money-check"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #065F46;">{{ $totalPayesMois }}</div>
                <div class="projet-stat-label">Payés</div>
            </div>
        </div>
        <div class="projet-stat-card">
            <div class="projet-stat-icon" style="background: #FEF3C7; color: #92400E;"><i class="fas fa-dollar-sign"></i></div>
            <div class="projet-stat-content">
                <div class="projet-stat-value" style="color: #92400E;">{{ number_format($totalNetMois, 0, ',', ' ') }} $</div>
                <div class="projet-stat-label">Total net {{ $moisCourant }}</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.salaires.index') }}" method="GET" class="projets-toolbar">
        <div class="projets-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="salaireSearch" value="{{ request('recherche') }}" 
                   class="projets-search-input" placeholder="Rechercher un membre, une référence...">
        </div>

        <div class="projets-filtres">
            <select name="mois" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les mois</option>
                @foreach($moisDisponibles as $moisVal)
                    <option value="{{ $moisVal }}" {{ request('mois') === $moisVal ? 'selected' : '' }}>{{ $moisVal }}</option>
                @endforeach
            </select>

            <select name="statut" class="projets-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>Validé</option>
                <option value="paye" {{ request('statut') === 'paye' ? 'selected' : '' }}>Payé</option>
                <option value="annule" {{ request('statut') === 'annule' ? 'selected' : '' }}>Annulé</option>
            </select>
        </div>

        @if(request('recherche') || request('mois') || request('statut'))
            <a href="{{ route('admin.salaires.index') }}" class="projets-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.salaires.grille') }}" class="btn btn-outline btn-sm" style="margin-left: 8px;">
            <i class="fas fa-table"></i> Grille salariale
        </a>

        <a href="{{ route('admin.salaires.create') }}" class="projets-add-btn">
            <i class="fas fa-plus-circle"></i> Nouveau salaire
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES SALAIRES --}}
    {{-- ============================================ --}}
    <div class="projets-table-card">
        @if($salaires->count() > 0)
        <div class="table-responsive">
            <table class="table-projets">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Membre</th>
                        <th>Poste</th>
                        <th>Mois</th>
                        <th>Salaire net</th>
                        <th>Mode</th>
                        <th>Statut</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaires as $salaire)
                    <tr>
                        {{-- Référence --}}
                        <td>
                            <span style="font-size: 12px; color: #1A5B9C; font-weight: 600;">
                                <i class="fas fa-barcode"></i> {{ $salaire->reference }}
                            </span>
                        </td>

                        {{-- Membre --}}
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #E8F0F8; display: flex; align-items: center; justify-content: center; color: #1A5B9C; font-size: 12px; font-weight: 700; flex-shrink: 0;">
                                    {{ $salaire->membre ? $salaire->membre->initiales() : '?' }}
                                </div>
                                <span style="font-size: 13px; font-weight: 600; color: #1E293B;">
                                    {{ $salaire->nomMembre() }}
                                </span>
                            </div>
                        </td>

                        {{-- Poste --}}
                        <td>
                            <span style="font-size: 12px; color: #334155;">{{ $salaire->nomPoste() }}</span>
                        </td>

                        {{-- Mois --}}
                        <td>
                            <span style="font-size: 13px; font-weight: 600; color: #1E293B;">{{ $salaire->mois }}</span>
                        </td>

                        {{-- Salaire net --}}
                        <td>
                            <span style="font-size: 14px; font-weight: 700; color: #1E293B;">
                                {{ number_format($salaire->salaire_net, 2, ',', ' ') }} $
                            </span>
                        </td>

                        {{-- Mode --}}
                        <td>
                            <span style="font-size: 12px; color: #64748B;">
                                <i class="fas fa-{{ $salaire->mode_paiement === 'especes' ? 'money-bill' : ($salaire->mode_paiement === 'virement' ? 'university' : ($salaire->mode_paiement === 'mobile_money' ? 'mobile-alt' : 'check')) }}"></i>
                                {{ $salaire->modePaiementLibelle() }}
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td>
                            @if($salaire->statut === 'brouillon')
                                <span class="projet-publication-badge" style="background: #F1F5F9; color: #64748B;">
                                    <i class="fas fa-file"></i> Brouillon
                                </span>
                            @elseif($salaire->statut === 'valide')
                                <span class="projet-publication-badge" style="background: #DBEAFE; color: #1E40AF;">
                                    <i class="fas fa-check-circle"></i> Validé
                                </span>
                            @elseif($salaire->statut === 'paye')
                                <span class="projet-publication-badge projet-publie">
                                    <i class="fas fa-money-check"></i> Payé
                                </span>
                            @else
                                <span class="projet-publication-badge" style="background: #FEE2E2; color: #B91C1C;">
                                    <i class="fas fa-times-circle"></i> Annulé
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="projet-actions">
                                <a href="{{ route('admin.salaires.show', $salaire) }}" class="projet-action-btn view" title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($salaire->estBrouillon())
                                <form action="{{ route('admin.salaires.valider', $salaire) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="projet-action-btn edit" title="Valider" style="background: #DBEAFE; color: #1E40AF;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                @if($salaire->estValide())
                                <button type="button" class="projet-action-btn edit" title="Marquer payé" 
                                        style="background: #D1FAE5; color: #065F46;"
                                        onclick="document.getElementById('payerForm{{ $salaire->id }}').style.display='block';">
                                    <i class="fas fa-money-check"></i>
                                </button>
                                @endif
                                @if(!$salaire->estPaye())
                                <a href="{{ route('admin.salaires.edit', $salaire) }}" class="projet-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </div>
                            
                            {{-- Formulaire caché pour marquer payé --}}
                            @if($salaire->estValide())
                            <form action="{{ route('admin.salaires.payer', $salaire) }}" method="POST" id="payerForm{{ $salaire->id }}" style="display: none; margin-top: 6px;">
                                @csrf
                                @method('PUT')
                                <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" required style="padding: 6px 8px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 12px; width: 130px;">
                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 6px 10px; font-size: 11px;">
                                    <i class="fas fa-check"></i> Confirmer
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($salaires->hasPages())
        <div class="projets-pagination">
            <span class="projets-pagination-info">
                Affichage de <strong>{{ $salaires->firstItem() }}-{{ $salaires->lastItem() }}</strong> sur <strong>{{ $salaires->total() }}</strong> salaires
            </span>
            {{ $salaires->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="projets-empty">
            <div class="projets-empty-icon"><i class="fas fa-money-bill-wave"></i></div>
            <h4>Aucun salaire trouvé</h4>
            <p>Aucun salaire ne correspond à vos critères de recherche.</p>
            <a href="{{ route('admin.salaires.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection