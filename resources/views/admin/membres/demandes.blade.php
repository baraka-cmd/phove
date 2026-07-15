@extends('layouts.admin')

@section('title', 'Demandes d\'adhésion')

@section('page_title', 'Demandes d\'adhésion')
@section('page_icon', 'clock')
@section('page_subtitle', 'Gérez les demandes d\'adhésion en attente de validation.')

@php
    $breadcrumb = [
        ['label' => 'Membres', 'url' => route('admin.membres.index')],
        'Demandes d\'adhésion'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-membres.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- STATISTIQUES --}}
    {{-- ============================================ --}}
    <div class="membres-stats-grid">
        <div class="membre-stat-card">
            <div class="membre-stat-icon" style="background: var(--color-warning-light); color: #856404;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $totalDemandes }}</div>
                <div class="membre-stat-label">Demandes en attente</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon" style="background: var(--color-info-light); color: var(--color-info);">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $demandesAujourdhui }}</div>
                <div class="membre-stat-label">Nouvelles aujourd'hui</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon" style="background: var(--color-success-light); color: var(--color-success);">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ \App\Models\Membre::where('statut', 'actif')->count() }}</div>
                <div class="membre-stat-label">Membres actifs</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon" style="background: var(--color-error-light); color: var(--color-accent);">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ \App\Models\Membre::where('statut', 'exclu')->count() }}</div>
                <div class="membre-stat-label">Demandes rejetées</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE DE RECHERCHE --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.membres.demandes') }}" method="GET" class="membres-toolbar">
        <div class="membres-search">
            <i class="fas fa-search"></i>
            <input type="text" 
                   name="recherche" 
                   value="{{ request('recherche') }}" 
                   class="membres-search-input" 
                   placeholder="Rechercher par nom, email, téléphone...">
        </div>
        <div class="membres-filtres">
            <select name="categorie" class="membres-filtre-select">
                <option value="">Toutes les catégories</option>
                <option value="adherent" {{ request('categorie') === 'adherent' ? 'selected' : '' }}>Adhérent</option>
                <option value="sympathisant" {{ request('categorie') === 'sympathisant' ? 'selected' : '' }}>Sympathisant</option>
                <option value="honneur" {{ request('categorie') === 'honneur' ? 'selected' : '' }}>Honneur</option>
            </select>
        </div>
        @if(request('recherche') || request('categorie'))
            <a href="{{ route('admin.membres.demandes') }}" class="membre-action-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES DEMANDES --}}
    {{-- ============================================ --}}
    <div class="membres-table-card">
        @if($demandes->count() > 0)
        <div class="table-responsive">
            <table class="table-membres">
                <thead>
                    <tr>
                        <th>Demandeur</th>
                        <th>Sexe</th>
                        <th>Catégorie</th>
                        <th>Contact</th>
                        <th>Date de demande</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandes as $demande)
                    <tr>
                        {{-- Identité --}}
                        <td>
                            <div class="membre-cell-identite">
                                <div class="membre-cell-avatar">
                                    @if($demande->photo)
                                        <img src="{{ asset('storage/' . $demande->photo) }}" alt="{{ $demande->nomComplet() }}">
                                    @else
                                        <span>{{ Str::upper(Str::substr($demande->nom, 0, 1) . Str::substr($demande->prenom ?? '', 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="membre-cell-nom">{{ $demande->nomComplet() }}</span>
                                    <small>{{ $demande->email ?? $demande->telephone }}</small>
                                </div>
                            </div>
                        </td>

                        {{-- Sexe --}}
                        <td>
                            <span style="font-size: 13px;">
                                @if($demande->sexe === 'M')
                                    <i class="fas fa-mars" style="color: #2980b9;"></i> M
                                @else
                                    <i class="fas fa-venus" style="color: #e84393;"></i> F
                                @endif
                            </span>
                        </td>

                        {{-- Catégorie --}}
                        <td>
                            @php
                                $catBadge = match($demande->categorie) {
                                    'adherent' => 'success',
                                    'sympathisant' => 'info',
                                    'honneur' => 'warning',
                                    default => 'primary'
                                };
                                $catLibelle = match($demande->categorie) {
                                    'adherent' => 'Adhérent',
                                    'sympathisant' => 'Sympathisant',
                                    'honneur' => 'Honneur',
                                    default => $demande->categorie
                                };
                            @endphp
                            <span class="membre-badge membre-badge-{{ $catBadge }}">{{ $catLibelle }}</span>
                        </td>

                        {{-- Contact --}}
                        <td>
                            <div style="font-size: 13px;">
                                <div><i class="fas fa-phone-alt" style="color: var(--color-accent); font-size: 10px;"></i> {{ $demande->telephone }}</div>
                                @if($demande->email)
                                <div style="font-size: 11px; color: var(--color-text-muted);"><i class="fas fa-envelope" style="font-size: 10px;"></i> {{ $demande->email }}</div>
                                @endif
                            </div>
                        </td>

                        {{-- Date --}}
                        <td>
                            <span style="font-size: 13px;">
                                <i class="fas fa-calendar-alt" style="color: var(--color-text-muted); font-size: 11px;"></i> 
                                {{ $demande->created_at->format('d/m/Y') }}
                                <br>
                                <small style="color: var(--color-text-muted);">{{ $demande->created_at->diffForHumans() }}</small>
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                {{-- Voir dossier --}}
                                <a href="{{ route('admin.membres.show', $demande) }}" 
                                   class="membre-action-btn view" 
                                   title="Voir le dossier complet">
                                    <i class="fas fa-folder-open"></i>
                                </a>

                                {{-- Approuver --}}
                                <form action="{{ route('admin.membres.approuver', $demande) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="membre-action-btn edit" 
                                            title="Approuver la demande"
                                            onclick="return confirm('Approuver la demande de {{ $demande->nomComplet() }} ?');"
                                            style="color: var(--color-success); border-color: var(--color-success);">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                {{-- Rejeter --}}
                                <button class="membre-action-btn delete btn-reject-demande" 
                                        data-id="{{ $demande->id }}"
                                        data-nom="{{ $demande->nomComplet() }}"
                                        title="Rejeter la demande"
                                        style="color: var(--color-accent); border-color: var(--color-accent);">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($demandes->hasPages())
        <div class="membres-pagination">
            <span class="membres-pagination-info">
                Affichage de <strong>{{ $demandes->firstItem() }}-{{ $demandes->lastItem() }}</strong> sur <strong>{{ $demandes->total() }}</strong> demandes
            </span>
            {{ $demandes->links() }}
        </div>
        @endif

        @else
        {{-- État vide --}}
        <div class="membres-empty">
            <div class="membres-empty-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h4>Aucune demande en attente</h4>
            <p>Toutes les demandes d'adhésion ont été traitées. Les nouvelles demandes apparaîtront ici.</p>
            <a href="{{ route('admin.membres.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-users"></i> Voir tous les membres
            </a>
        </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- MODALE DE REJET --}}
    {{-- ============================================ --}}
    <div class="modal-overlay" id="rejectModal">
        <div class="modal" style="max-width: 480px;">
            <div class="modal-header">
                <h3 class="modal-title" style="color: var(--color-accent);">
                    <i class="fas fa-times-circle"></i> Rejeter la demande
                </h3>
                <button class="modal-close" id="closeRejectModal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p style="font-size: 14px; color: #555; margin-bottom: 16px;">
                    Vous allez rejeter la demande de : <strong id="rejectMembreNom">—</strong>
                </p>
                <form action="" method="POST" id="rejectForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="motif_rejet" class="form-label">
                            Motif du rejet <span class="required">*</span>
                        </label>
                        <textarea name="motif_rejet" id="motif_rejet" class="form-textarea" rows="3" 
                                  placeholder="Expliquez la raison du rejet de cette demande..." required></textarea>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 18px;">
                        <button type="button" class="btn btn-outline btn-sm" id="cancelReject">Annuler</button>
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="fas fa-times"></i> Confirmer le rejet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-membres.js') }}"></script>
    <script>
        // Gestion de la modale de rejet
        const rejectModal = document.getElementById('rejectModal');
        const rejectForm = document.getElementById('rejectForm');
        const rejectMembreNom = document.getElementById('rejectMembreNom');
        const closeRejectModal = document.getElementById('closeRejectModal');
        const cancelReject = document.getElementById('cancelReject');

        document.querySelectorAll('.btn-reject-demande').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nom = this.getAttribute('data-nom');
                rejectForm.action = '/admin/membres/' + id + '/rejeter';
                rejectMembreNom.textContent = nom;
                rejectModal.classList.add('active');
            });
        });

        [closeRejectModal, cancelReject].forEach(el => {
            el?.addEventListener('click', () => rejectModal.classList.remove('active'));
        });

        rejectModal?.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('active');
        });
    </script>
@endsection