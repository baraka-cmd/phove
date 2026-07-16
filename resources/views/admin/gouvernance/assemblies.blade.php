@extends('layouts.admin')

@section('title', 'Assemblées Générales')

@section('page_title', 'Assemblées Générales')
@section('page_icon', 'users-cog')
@section('page_subtitle', 'Gérez l\'historique des Assemblées Générales : ordinaires, extraordinaires, procès-verbaux.')

@php
    $breadcrumb = [
        ['label' => 'Gouvernance', 'url' => route('admin.gouvernance.index')],
        'Assemblées Générales'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance-assemblies.css') }}">
@endsection

@section('content')

    {{-- KPI --}}
    <div class="assemblies-stats-grid">
        <div class="assembly-stat-card">
            <div class="assembly-stat-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="assembly-stat-content">
                <div class="assembly-stat-value">{{ $stats['total'] }}</div>
                <div class="assembly-stat-label">Total AG</div>
            </div>
        </div>
        <div class="assembly-stat-card">
            <div class="assembly-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="assembly-stat-content">
                <div class="assembly-stat-value">{{ $stats['tenues'] }}</div>
                <div class="assembly-stat-label">AG tenues</div>
            </div>
        </div>
        <div class="assembly-stat-card">
            <div class="assembly-stat-icon"><i class="fas fa-clock"></i></div>
            <div class="assembly-stat-content">
                <div class="assembly-stat-value">{{ $stats['planifiees'] }}</div>
                <div class="assembly-stat-label">AG planifiées</div>
            </div>
        </div>
    </div>

    {{-- BARRE D'OUTILS --}}
    <form action="{{ route('admin.gouvernance.assemblies') }}" method="GET" class="assemblies-toolbar">
        <div class="assemblies-filtres">
            <select name="type" class="assemblies-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="ordinaire" {{ request('type') === 'ordinaire' ? 'selected' : '' }}>Ordinaire</option>
                <option value="extraordinaire" {{ request('type') === 'extraordinaire' ? 'selected' : '' }}>Extraordinaire</option>
            </select>
            <select name="statut" class="assemblies-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="planifiee" {{ request('statut') === 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                <option value="tenue" {{ request('statut') === 'tenue' ? 'selected' : '' }}>Tenue</option>
                <option value="annulee" {{ request('statut') === 'annulee' ? 'selected' : '' }}>Annulée</option>
                <option value="reportee" {{ request('statut') === 'reportee' ? 'selected' : '' }}>Reportée</option>
            </select>
            <select name="annee" class="assemblies-filtre-select" onchange="this.form.submit()">
                <option value="">Toutes les années</option>
                @foreach($annees as $annee)
                    <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                @endforeach
            </select>
        </div>

        @if(request('type') || request('statut') || request('annee'))
            <a href="{{ route('admin.gouvernance.assemblies') }}" class="assemblies-reset-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.gouvernance.assemblies.create') }}" class="assemblies-add-btn">
            <i class="fas fa-plus-circle"></i> Nouvelle AG
        </a>
    </form>

    {{-- TABLEAU --}}
    <div class="assemblies-table-card">
        @if($assemblies->count() > 0)
        <div class="table-responsive">
            <table class="table-assemblies">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Lieu</th>
                        <th>Participants</th>
                        <th>Quorum</th>
                        <th>Statut</th>
                        <th>PV</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assemblies as $assembly)
                    <tr>
                        <td>
                            <span class="assembly-type-badge assembly-type-{{ $assembly->type }}">
                                <i class="fas fa-{{ $assembly->type === 'ordinaire' ? 'calendar-check' : 'exclamation-circle' }}"></i>
                                {{ $assembly->type === 'ordinaire' ? 'Ordinaire' : 'Extraordinaire' }}
                            </span>
                        </td>
                        <td>
                            <span class="assembly-date">{{ \Carbon\Carbon::parse($assembly->date_reunion)->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            <span class="assembly-lieu">{{ $assembly->lieu }}</span>
                        </td>
                        <td>
                            <span class="assembly-participants">
                                <strong>{{ $assembly->membres_presents }}</strong>/{{ $assembly->membres_total }}
                            </span>
                            @if($assembly->membres_absents > 0)
                                <small style="color: #888; font-size: 11px; display: block;">{{ $assembly->membres_absents }} absents</small>
                            @endif
                        </td>
                        <td>
                            @if($assembly->membres_total > 0)
                                <span class="quorum-indicator {{ $assembly->quorum_atteint ? 'quorum-atteint' : 'quorum-non-atteint' }}">
                                    <i class="fas fa-{{ $assembly->quorum_atteint ? 'check-circle' : 'times-circle' }}"></i>
                                    {{ $assembly->quorum_atteint ? 'Atteint' : 'Non atteint' }}
                                </span>
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="assembly-statut-badge assembly-statut-{{ $assembly->statut }}">
                                @php
                                    $statutIcon = match($assembly->statut) {
                                        'planifiee' => 'clock', 'tenue' => 'check-circle',
                                        'annulee' => 'times-circle', 'reportee' => 'calendar-alt',
                                        default => 'circle'
                                    };
                                    $statutLibelle = match($assembly->statut) {
                                        'planifiee' => 'Planifiée', 'tenue' => 'Tenue',
                                        'annulee' => 'Annulée', 'reportee' => 'Reportée',
                                        default => $assembly->statut
                                    };
                                @endphp
                                <i class="fas fa-{{ $statutIcon }}"></i> {{ $statutLibelle }}
                            </span>
                        </td>
                        <td>
                            @if($assembly->pv_fichier)
                            <a href="{{ asset('storage/' . $assembly->pv_fichier) }}" target="_blank" class="assembly-pv-link" title="Télécharger le PV">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            @else
                            <span class="assembly-no-pv">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="assembly-actions">
                                <a href="{{ route('admin.gouvernance.assemblies.show', $assembly) }}" class="assembly-action-btn view" title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.gouvernance.assemblies.edit', $assembly) }}" class="assembly-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.gouvernance.assemblies.destroy', $assembly) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="assembly-action-btn delete btn-delete-assembly" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($assemblies->hasPages())
        <div class="assemblies-pagination">
            <span class="assemblies-pagination-info">
                Affichage de <strong>{{ $assemblies->firstItem() }}-{{ $assemblies->lastItem() }}</strong> sur <strong>{{ $assemblies->total() }}</strong> AG
            </span>
            {{ $assemblies->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="assemblies-empty">
            <div class="assemblies-empty-icon"><i class="fas fa-calendar-alt"></i></div>
            <h4>Aucune Assemblée Générale</h4>
            <p>Aucune AG n'a encore été enregistrée.</p>
            <a href="{{ route('admin.gouvernance.assemblies.create') }}" class="btn btn-primary btn-sm mt-3">
                <i class="fas fa-plus-circle"></i> Ajouter une AG
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-gouvernance-assemblies.js') }}"></script>
@endsection