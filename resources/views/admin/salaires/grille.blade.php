@extends('layouts.admin')

@section('title', 'Grille Salariale | PHOVE')

@section('page_title', 'Grille Salariale')
@section('page_icon', 'table')
@section('page_subtitle', 'Vue d\'ensemble des salaires par organe et par poste.')

@php
    $breadcrumb = [
        ['label' => 'Salaires', 'url' => route('admin.salaires.index')],
        'Grille salariale'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <style>
        .grille-organe-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            margin-bottom: 24px;
            border-left: 4px solid #1A5B9C;
        }
        .grille-organe-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #E2E8F0;
        }
        .grille-organe-title {
            font-size: 15px;
            font-weight: 700;
            color: #1E293B;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .grille-organe-title i {
            color: #1A5B9C;
        }
        .grille-organe-count {
            font-size: 12px;
            color: #64748B;
            font-weight: 600;
        }
        .grille-table {
            width: 100%;
            border-collapse: collapse;
        }
        .grille-table thead th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            color: #64748B;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #E2E8F0;
            white-space: nowrap;
        }
        .grille-table tbody td {
            padding: 10px 14px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #E2E8F0;
        }
        .grille-table tbody tr:hover {
            background: #F8FAFC;
        }
        .grille-table tbody tr:last-child td {
            border-bottom: none;
        }
        .grille-membre-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .grille-membre-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #E8F0F8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1A5B9C;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .grille-montant {
            font-weight: 700;
            color: #1E293B;
        }
        .grille-badge-paye {
            background: #D1FAE5;
            color: #065F46;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }
        .grille-badge-valide {
            background: #DBEAFE;
            color: #1E40AF;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }
        .grille-badge-vacant {
            background: #FEE2E2;
            color: #B91C1C;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE --}}
    {{-- ============================================ --}}
    <div style="background: #FFFFFF; border-radius: 16px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <h2 style="font-size: 1.1rem; font-weight: 700; color: #1E293B; margin-bottom: 4px;">
                    <i class="fas fa-table" style="color: #1A5B9C;"></i> Grille salariale par organe
                </h2>
                <p style="font-size: 13px; color: #64748B;">
                    Conformément à l'Article 63 des Statuts : seuls l'AG, la Commission de Contrôle et le CA sont bénévoles.
                </p>
            </div>
            <a href="{{ route('admin.salaires.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouveau salaire
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE PAR ORGANE --}}
    {{-- ============================================ --}}
    @if($organes->count() > 0)
        @foreach($organes as $organeNom => $organesGroupe)
        @php
            $totalOrgane = $organesGroupe->sum(function($o) {
                return $o->salaires->where('statut', 'paye')->sum('salaire_net');
            });
        @endphp
        <div class="grille-organe-card">
            <div class="grille-organe-header">
                <h3 class="grille-organe-title">
                    <i class="fas fa-{{ $organeNom === 'assemblee_generale' ? 'users-cog' : ($organeNom === 'conseil_administration' ? 'user-tie' : ($organeNom === 'direction_executive' ? 'tasks' : ($organeNom === 'commission_controle' ? 'search-dollar' : ($organeNom === 'comite_suivi' ? 'clipboard-check' : ($organeNom === 'comite_gestion' ? 'cogs' : 'sitemap'))))) }}"></i>
                    {{ $organesGroupe->first()->organeLibelle() }}
                </h3>
                <span class="grille-organe-count">
                    {{ $organesGroupe->count() }} poste(s) — Total payé : <strong>{{ number_format($totalOrgane, 2, ',', ' ') }} $</strong>
                </span>
            </div>

            <div class="table-responsive">
                <table class="grille-table">
                    <thead>
                        <tr>
                            <th>Membre</th>
                            <th>Poste</th>
                            <th>Structure</th>
                            <th>Niveau</th>
                            <th>Dernier salaire</th>
                            <th>Dernier mois</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($organesGroupe as $organe)
                        <tr>
                            {{-- Membre --}}
                            <td>
                                @if($organe->membre)
                                <div class="grille-membre-cell">
                                    <div class="grille-membre-avatar">
                                        {{ $organe->membre->initiales() }}
                                    </div>
                                    <span style="font-weight: 600; color: #1E293B;">{{ $organe->membre->nomComplet() }}</span>
                                </div>
                                @else
                                <span style="color: #94A3B8; font-style: italic;">Poste vacant</span>
                                @endif
                            </td>

                            {{-- Poste --}}
                            <td>{{ $organe->poste }}</td>

                            {{-- Structure --}}
                            <td>
                                @if($organe->structure)
                                    <span style="font-size: 12px;">{{ $organe->structure->nom }}</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>

                            {{-- Niveau --}}
                            <td>
                                @if($organe->niveau)
                                    <span style="font-size: 12px;">{{ $organe->niveauLibelle() }}</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>

                            {{-- Dernier salaire --}}
                            <td>
                                @if($organe->salaires->count() > 0)
                                    @php $dernierSalaire = $organe->salaires->first(); @endphp
                                    <span class="grille-montant">{{ number_format($dernierSalaire->salaire_net, 2, ',', ' ') }} $</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>

                            {{-- Dernier mois --}}
                            <td>
                                @if($organe->salaires->count() > 0)
                                    <span style="font-size: 12px;">{{ $organe->salaires->first()->mois }}</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td>
                                @if($organe->salaires->count() > 0 && $organe->salaires->first()->statut === 'paye')
                                    <span class="grille-badge-paye"><i class="fas fa-check-circle"></i> Payé</span>
                                @elseif($organe->salaires->count() > 0 && $organe->salaires->first()->statut === 'valide')
                                    <span class="grille-badge-valide"><i class="fas fa-check"></i> Validé</span>
                                @elseif(!$organe->membre)
                                    <span class="grille-badge-vacant"><i class="fas fa-user-slash"></i> Vacant</span>
                                @else
                                    <span style="color: #94A3B8;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    @else
    <div class="projets-empty">
        <div class="projets-empty-icon"><i class="fas fa-table"></i></div>
        <h4>Aucun organe actif</h4>
        <p>Aucun poste actif dans l'organigramme pour le moment.</p>
        <a href="{{ route('admin.gouvernance.index') }}" class="btn btn-outline btn-sm mt-3">
            <i class="fas fa-sitemap"></i> Gérer la gouvernance
        </a>
    </div>
    @endif

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
@endsection