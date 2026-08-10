@extends('layouts.admin')

@section('title', 'Gestion des témoignages')

@section('page_title', 'Témoignages')
@section('page_icon', 'quote-right')
@section('page_subtitle', 'Gérez les témoignages des bénéficiaires : validation, modération et publication.')

@php
    $breadcrumb = ['Témoignages' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-temoignages.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 5 CARTES --}}
    {{-- ============================================ --}}
    <div class="temoignages-stats-grid">
        <div class="temoignage-stat-card">
            <div class="temoignage-stat-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="temoignage-stat-content">
                <div class="temoignage-stat-value">{{ $totalTemoignages }}</div>
                <div class="temoignage-stat-label">Total témoignages</div>
            </div>
        </div>
        <div class="temoignage-stat-card">
            <div class="temoignage-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="temoignage-stat-content">
                <div class="temoignage-stat-value">{{ $temoignagesEnAttente }}</div>
                <div class="temoignage-stat-label">En attente</div>
            </div>
        </div>
        <div class="temoignage-stat-card">
            <div class="temoignage-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="temoignage-stat-content">
                <div class="temoignage-stat-value">{{ $temoignagesApprouves }}</div>
                <div class="temoignage-stat-label">Approuvés</div>
            </div>
        </div>
        <div class="temoignage-stat-card">
            <div class="temoignage-stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="temoignage-stat-content">
                <div class="temoignage-stat-value">{{ $temoignagesRejetes }}</div>
                <div class="temoignage-stat-label">Rejetés</div>
            </div>
        </div>
        <div class="temoignage-stat-card">
            <div class="temoignage-stat-icon">
                <i class="fas fa-archive"></i>
            </div>
            <div class="temoignage-stat-content">
                <div class="temoignage-stat-value">{{ $temoignagesArchives }}</div>
                <div class="temoignage-stat-label">Archivés</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.temoignages.index') }}" method="GET" class="temoignages-toolbar">
        <div class="temoignages-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="temoignageSearch" value="{{ request('recherche') }}" 
                   class="temoignages-search-input" placeholder="Rechercher un bénéficiaire...">
        </div>

        <div class="temoignages-filtres">
            <select name="statut" class="temoignages-filtre-select">
                <option value="">Tous les statuts</option>
                <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="approuve" {{ request('statut') === 'approuve' ? 'selected' : '' }}>Approuvés</option>
                <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>Rejetés</option>
                <option value="archive" {{ request('statut') === 'archive' ? 'selected' : '' }}>Archivés</option>
            </select>

            <select name="categorie" class="temoignages-filtre-select">
                <option value="">Toutes les catégories</option>
                <option value="femme" {{ request('categorie') === 'femme' ? 'selected' : '' }}>Femme</option>
                <option value="enfant" {{ request('categorie') === 'enfant' ? 'selected' : '' }}>Enfant</option>
                <option value="handicape" {{ request('categorie') === 'handicape' ? 'selected' : '' }}>Handicapé</option>
                <option value="troisieme_age" {{ request('categorie') === 'troisieme_age' ? 'selected' : '' }}>3ème âge</option>
                <option value="orphelin" {{ request('categorie') === 'orphelin' ? 'selected' : '' }}>Orphelin</option>
                <option value="deplace_interne" {{ request('categorie') === 'deplace_interne' ? 'selected' : '' }}>Déplacé interne</option>
                <option value="refugie" {{ request('categorie') === 'refugie' ? 'selected' : '' }}>Réfugié</option>
                <option value="rapatrie" {{ request('categorie') === 'rapatrie' ? 'selected' : '' }}>Rapatrié</option>
                <option value="autre" {{ request('categorie') === 'autre' ? 'selected' : '' }}>Autre</option>
            </select>

            <select name="province" class="temoignages-filtre-select">
                <option value="">Toutes les provinces</option>
                @foreach($provinces as $province)
                    <option value="{{ $province }}" {{ request('province') === $province ? 'selected' : '' }}>
                        {{ $province }}
                    </option>
                @endforeach
            </select>

            <select name="projet_id" class="temoignages-filtre-select">
                <option value="">Tous les projets</option>
                @foreach($projets as $projet)
                    <option value="{{ $projet->id }}" {{ request('projet_id') == $projet->id ? 'selected' : '' }}>
                        {{ Str::limit($projet->titre, 40) }}
                    </option>
                @endforeach
            </select>
        </div>

        @if(request('recherche') || request('statut') || request('categorie') || request('province') || request('projet_id'))
            <a href="{{ route('admin.temoignages.index') }}" class="temoignages-reset-btn" title="Réinitialiser les filtres">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.temoignages.create') }}" class="temoignages-add-btn">
            <i class="fas fa-plus-circle"></i> Nouveau témoignage
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES TÉMOIGNAGES --}}
    {{-- ============================================ --}}
    <div class="temoignages-table-card">
        @if($temoignages->count() > 0)
        <div class="table-responsive">
            <table class="table-temoignages">
                <thead>
                    <tr>
                        <th>Bénéficiaire</th>
                        <th>Témoignage</th>
                        <th>Catégorie</th>
                        <th>Projet</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($temoignages as $temoignage)
                    <tr>
                        {{-- Bénéficiaire --}}
                        <td>
                            <div class="temoignage-cell-beneficiaire">
                                <div class="temoignage-cell-avatar">
                                    @if($temoignage->photo)
                                        <img src="{{ $temoignage->urlPhoto() }}" alt="{{ $temoignage->nomComplet() }}">
                                    @else
                                        {{ $temoignage->initiales() }}
                                    @endif
                                </div>
                                <div class="temoignage-cell-info">
                                    <div class="temoignage-cell-nom">{{ $temoignage->nomComplet() }}</div>
                                    <div class="temoignage-cell-meta">
                                        <span><i class="fas fa-venus-mars"></i> {{ $temoignage->sexe === 'M' ? 'Homme' : 'Femme' }}</span>
                                        @if($temoignage->age)
                                            <span><i class="fas fa-birthday-cake"></i> {{ $temoignage->age }} ans</span>
                                        @endif
                                        @if($temoignage->localisation)
                                            <span><i class="fas fa-map-marker-alt"></i> {{ $temoignage->localisation }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Extrait du témoignage --}}
                        <td>
                            <div class="temoignage-cell-extrait" title="{{ e($temoignage->contenu) }}">
                                {{ $temoignage->extrait(150) }}
                            </div>
                        </td>

                        {{-- Catégorie --}}
                        <td>
                            @php
                                $catIcon = match($temoignage->categorie) {
                                    'femme' => 'female',
                                    'enfant' => 'child',
                                    'handicape' => 'wheelchair',
                                    'troisieme_age' => 'user-clock',
                                    'orphelin' => 'heart-broken',
                                    'deplace_interne' => 'home',
                                    'refugie' => 'globe-africa',
                                    'rapatrie' => 'undo-alt',
                                    default => 'user'
                                };
                            @endphp
                            <span class="temoignage-cat-badge temoignage-cat-{{ $temoignage->categorie }}">
                                <i class="fas fa-{{ $catIcon }}"></i>
                                {{ $temoignage->categorieLibelle() }}
                            </span>
                        </td>

                        {{-- Projet --}}
                        <td>
                            @if($temoignage->projet)
                                <span style="font-size: 13px; font-weight: 500; color: var(--color-primary);">
                                    <i class="fas fa-project-diagram"></i> 
                                    {{ Str::limit($temoignage->projet->titre, 35) }}
                                </span>
                            @else
                                <span style="font-size: 12px; color: #ccc;">—</span>
                            @endif
                        </td>

                        {{-- Statut --}}
                        <td>
                            <span class="temoignage-statut-badge temoignage-statut-{{ $temoignage->statut }}">
                                <i class="fas fa-{{ 
                                    $temoignage->statut === 'en_attente' ? 'clock' : 
                                    ($temoignage->statut === 'approuve' ? 'check-circle' : 
                                    ($temoignage->statut === 'rejete' ? 'times-circle' : 'archive'))
                                }}"></i>
                                {{ $temoignage->statutLibelle() }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td>
                            <span style="font-size: 12px; white-space: nowrap;">
                                {{ $temoignage->date_soumission ? $temoignage->date_soumission->format('d/m/Y') : $temoignage->created_at->format('d/m/Y') }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="temoignage-actions">
                                <a href="{{ route('admin.temoignages.show', $temoignage) }}" class="temoignage-action-btn view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.temoignages.edit', $temoignage) }}" class="temoignage-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($temoignage->statut !== 'approuve')
                                    <form action="{{ route('admin.temoignages.approuver', $temoignage) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="temoignage-action-btn approve btn-approve-temoignage" 
                                                data-nom="{{ $temoignage->nomComplet() }}" title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($temoignage->statut === 'en_attente')
                                    <button type="button" class="temoignage-action-btn reject btn-reject-temoignage" 
                                            data-url="{{ route('admin.temoignages.rejeter', $temoignage) }}" title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                                @if($temoignage->statut === 'approuve')
                                    <form action="{{ route('admin.temoignages.archiver', $temoignage) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="temoignage-action-btn archive" title="Archiver">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($temoignage->statut === 'archive')
                                    <form action="{{ route('admin.temoignages.desarchiver', $temoignage) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="temoignage-action-btn approve" title="Désarchiver">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.temoignages.destroy', $temoignage) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="temoignage-action-btn delete btn-delete-temoignage" 
                                            data-nom="{{ $temoignage->nomComplet() }}" title="Supprimer">
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

        {{-- Pagination --}}
        @if($temoignages->hasPages())
        <div class="temoignages-pagination">
            {{ $temoignages->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="temoignages-empty">
            <div class="temoignages-empty-icon"><i class="fas fa-quote-right"></i></div>
            <h4>Aucun témoignage trouvé</h4>
            <p>
                @if(request('recherche') || request('statut') || request('categorie') || request('province') || request('projet_id'))
                    Aucun témoignage ne correspond à vos critères de recherche.
                @else
                    Aucun témoignage n'a encore été enregistré. Les témoignages soumis par les bénéficiaires apparaîtront ici.
                @endif
            </p>
            @if(request('recherche') || request('statut') || request('categorie') || request('province') || request('projet_id'))
                <a href="{{ route('admin.temoignages.index') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
                </a>
            @else
                <a href="{{ route('admin.temoignages.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Ajouter un témoignage
                </a>
            @endif
        </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- MODALE DE REJET --}}
    {{-- ============================================ --}}
    <div id="rejectModal" class="temoignage-modal-overlay">
        <div class="temoignage-modal">
            <h3>
                <i class="fas fa-times-circle" style="color: var(--color-accent); margin-right: 8px;"></i>
                Rejeter le témoignage
            </h3>
            <p>Veuillez indiquer le motif du rejet. Ce motif sera enregistré dans l'historique.</p>
            <textarea id="rejectModalTextarea" class="temoignage-modal-textarea" 
                      placeholder="Ex: Contenu inapproprié, doublon, informations insuffisantes..." 
                      maxlength="1000"></textarea>
            <div class="temoignage-modal-actions">
                <button type="button" id="rejectModalCancel" class="btn btn-outline btn-sm">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="button" id="rejectModalSubmit" class="btn btn-accent btn-sm">
                    <i class="fas fa-times-circle"></i> Confirmer le rejet
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- FORMULAIRE CACHÉ POUR LE REJET --}}
    {{-- ============================================ --}}
    <form id="rejectForm" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="motif_rejet" id="rejectMotifInput" value="">
    </form>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-temoignages.js') }}"></script>
@endsection