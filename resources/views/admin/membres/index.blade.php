@extends('layouts.admin')

@section('title', 'Gestion des membres')

@section('page_title', 'Membres')
@section('page_icon', 'users')
@section('page_subtitle', 'Gérez l\'ensemble des membres de l\'organisation : adhérents, fondateurs, sympathisants.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-membres.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- STATISTIQUES RAPIDES --}}
    {{-- ============================================ --}}
    <div class="membres-stats-grid">
        <div class="membre-stat-card">
            <div class="membre-stat-icon"><i class="fas fa-users"></i></div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $totalMembres }}</div>
                <div class="membre-stat-label">Membres au total</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon"><i class="fas fa-user-check"></i></div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $membresActifs }}</div>
                <div class="membre-stat-label">Membres actifs</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon"><i class="fas fa-clock"></i></div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $membresEnAttente }}</div>
                <div class="membre-stat-label">En attente de validation</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon"><i class="fas fa-ban"></i></div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $membresSuspendus }}</div>
                <div class="membre-stat-label">Suspendus / Exclus</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS : RECHERCHE + FILTRES + AJOUT --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.membres.index') }}" method="GET" class="membres-toolbar">
        {{-- Recherche --}}
        <div class="membres-search">
            <i class="fas fa-search"></i>
            <input type="text" 
                   name="recherche" 
                   id="membreSearch"
                   value="{{ request('recherche') }}" 
                   class="membres-search-input" 
                   placeholder="Rechercher par nom, email, téléphone...">
        </div>

        {{-- Filtres --}}
        <div class="membres-filtres">
            <select name="categorie" class="membres-filtre-select">
                <option value="">Toutes les catégories</option>
                <option value="initiateur" {{ request('categorie') === 'initiateur' ? 'selected' : '' }}>Initiateur</option>
                <option value="fondateur" {{ request('categorie') === 'fondateur' ? 'selected' : '' }}>Fondateur</option>
                <option value="adherent" {{ request('categorie') === 'adherent' ? 'selected' : '' }}>Adhérent</option>
                <option value="honneur" {{ request('categorie') === 'honneur' ? 'selected' : '' }}>Honneur</option>
                <option value="sympathisant" {{ request('categorie') === 'sympathisant' ? 'selected' : '' }}>Sympathisant</option>
            </select>

            <select name="statut" class="membres-filtre-select">
                <option value="">Tous les statuts</option>
                <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="suspendu" {{ request('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                <option value="exclu" {{ request('statut') === 'exclu' ? 'selected' : '' }}>Exclu</option>
                <option value="deserteur" {{ request('statut') === 'deserteur' ? 'selected' : '' }}>Déserteur</option>
                <option value="demissionnaire" {{ request('statut') === 'demissionnaire' ? 'selected' : '' }}>Démissionnaire</option>
            </select>

            <select name="zone" class="membres-filtre-select">
                <option value="">Toutes les zones</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone }}" {{ request('zone') === $zone ? 'selected' : '' }}>{{ $zone }}</option>
                @endforeach
            </select>
        </div>

        {{-- Bouton réinitialiser --}}
        @if(request('recherche') || request('categorie') || request('statut') || request('zone'))
            <a href="{{ route('admin.membres.index') }}" class="membre-action-btn" title="Réinitialiser les filtres" style="flex-shrink: 0;">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.membres.create') }}" class="membres-add-btn">
            <i class="fas fa-user-plus"></i> Ajouter un membre
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES MEMBRES --}}
    {{-- ============================================ --}}
    <div class="membres-table-card">
        @if($membres->count() > 0)
        <div class="table-responsive">
            <table class="table-membres">
                <thead>
                    <tr>
                        <th>Membre</th>
                        <th>Sexe</th>
                        <th>Catégorie</th>
                        <th>Adresse</th>
                        <th>Statut</th>
                        <th>Adhésion</th>
                        <th>Cotisations</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($membres as $membre)
                        @include('admin.partials.membre-card', ['membre' => $membre])
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($membres->hasPages())
        <div class="membres-pagination">
            <span class="membres-pagination-info">
                Affichage de <strong>{{ $membres->firstItem() }}-{{ $membres->lastItem() }}</strong> sur <strong>{{ $membres->total() }}</strong> membres
            </span>
            {{ $membres->links() }}
        </div>
        @endif

        @else
        {{-- État vide --}}
        <div class="membres-empty">
            <div class="membres-empty-icon">
                <i class="fas fa-users-slash"></i>
            </div>
            <h4>Aucun membre trouvé</h4>
            <p>Aucun membre ne correspond à vos critères de recherche ou de filtres.</p>
            <a href="{{ route('admin.membres.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
            </a>
        </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- MODALE CHANGEMENT DE STATUT --}}
    {{-- ============================================ --}}
    <div class="modal-overlay" id="statusModal">
        <div class="modal modal-delete">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-exchange-alt"></i> Changer le statut</h3>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p>Vous allez changer le statut de : <strong id="statusMembreNom">—</strong></p>
                <form action="" method="POST" id="statusForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="statut" id="statusAction">
                    <div class="form-group">
                        <label class="form-label">Motif (optionnel)</label>
                        <textarea name="motif" class="form-textarea" rows="2" placeholder="Raison du changement de statut..."></textarea>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-outline btn-sm modal-close-btn">Annuler</button>
                        <button type="submit" class="btn btn-accent btn-sm">Confirmer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-membres.js') }}"></script>
    <script>
        // Fermer la modale avec le bouton Annuler
        document.querySelector('.modal-close-btn')?.addEventListener('click', function () {
            document.getElementById('statusModal').classList.remove('active');
        });
        document.querySelector('.modal-close')?.addEventListener('click', function () {
            document.getElementById('statusModal').classList.remove('active');
        });
        document.getElementById('statusModal')?.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('active');
        });
    </script>
@endsection