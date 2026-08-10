@extends('layouts.admin')

@section('title', 'Détail du témoignage')

@section('page_title', 'Détail du témoignage')
@section('page_icon', 'eye')
@section('page_subtitle', 'Consultez le témoignage complet et les informations du bénéficiaire.')

@php
    $breadcrumb = ['Témoignages' => route('admin.temoignages.index'), 'Détail' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-temoignages.css') }}">
@endsection

@section('content')

    <div class="temoignage-show-container">

        {{-- Bouton retour --}}
        <a href="{{ route('admin.temoignages.index') }}" class="temoignage-show-back">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>

        {{-- Navigation précédent/suivant --}}
        <div class="temoignage-show-nav">
            <div>
                @if($precedent)
                    <a href="{{ route('admin.temoignages.show', $precedent) }}" class="temoignage-show-nav-link">
                        <i class="fas fa-chevron-left"></i> Témoignage précédent
                    </a>
                @endif
            </div>
            <div>
                @if($suivant)
                    <a href="{{ route('admin.temoignages.show', $suivant) }}" class="temoignage-show-nav-link">
                        Témoignage suivant <i class="fas fa-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Carte principale --}}
        <div class="temoignage-show-card">

            {{-- ============================================ --}}
            {{-- EN-TÊTE BÉNÉFICIAIRE --}}
            {{-- ============================================ --}}
            <div class="temoignage-show-header">
                <div class="temoignage-show-avatar">
                    @if($temoignage->photo)
                        <img src="{{ $temoignage->urlPhoto() }}" alt="{{ $temoignage->nomComplet() }}">
                    @else
                        {{ $temoignage->initiales() }}
                    @endif
                </div>
                <div class="temoignage-show-header-info">
                    <h2>{{ $temoignage->nomComplet() }}</h2>
                    <div class="temoignage-show-header-meta">
                        <span class="temoignage-show-header-badge temoignage-statut-{{ $temoignage->statut }}" style="background: rgba(255,255,255,0.15); color: #fff;">
                            <i class="fas fa-{{ 
                                $temoignage->statut === 'en_attente' ? 'clock' : 
                                ($temoignage->statut === 'approuve' ? 'check-circle' : 
                                ($temoignage->statut === 'rejete' ? 'times-circle' : 'archive'))
                            }}"></i>
                            {{ $temoignage->statutLibelle() }}
                        </span>
                        <span class="temoignage-show-header-badge" style="background: rgba(255,255,255,0.15); color: #fff;">
                            <i class="fas fa-{{ $temoignage->sexe === 'M' ? 'mars' : 'venus' }}"></i>
                            {{ $temoignage->sexe === 'M' ? 'Homme' : 'Femme' }}
                        </span>
                        @if($temoignage->age)
                            <span class="temoignage-show-header-badge" style="background: rgba(255,255,255,0.15); color: #fff;">
                                <i class="fas fa-birthday-cake"></i> {{ $temoignage->age }} ans
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- CORPS --}}
            {{-- ============================================ --}}
            <div class="temoignage-show-body">

                {{-- Contenu du témoignage --}}
                <div class="temoignage-show-contenu">
                    {{ $temoignage->contenu }}
                </div>

                {{-- Motif de rejet si rejeté --}}
                @if($temoignage->statut === 'rejete' && $temoignage->motif_rejet)
                    <div class="temoignage-show-rejet">
                        <div class="temoignage-show-rejet-title">
                            <i class="fas fa-exclamation-triangle"></i> Motif du rejet
                        </div>
                        <div class="temoignage-show-rejet-text">
                            {{ $temoignage->motif_rejet }}
                        </div>
                    </div>
                @endif

                {{-- Grille de métadonnées --}}
                <div class="temoignage-show-grid">
                    {{-- Colonne gauche : Informations bénéficiaire --}}
                    <div>
                        <div class="temoignage-show-section">
                            <div class="temoignage-show-section-title">
                                <i class="fas fa-user-circle"></i> Informations du bénéficiaire
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-primary-lighter); color: var(--color-primary);">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Nom complet</div>
                                    <div class="temoignage-show-field-value">{{ $temoignage->nomComplet() }}</div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: #fce4ec; color: #e91e63;">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Catégorie</div>
                                    <div class="temoignage-show-field-value">{{ $temoignage->categorieLibelle() }}</div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-success-light); color: var(--color-success);">
                                    <i class="fas fa-venus-mars"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Sexe</div>
                                    <div class="temoignage-show-field-value">{{ $temoignage->sexe === 'M' ? 'Homme' : 'Femme' }}</div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: #fff3cd; color: #e67e22;">
                                    <i class="fas fa-birthday-cake"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Âge</div>
                                    <div class="temoignage-show-field-value {{ $temoignage->age ? '' : 'empty' }}">
                                        {{ $temoignage->age ? $temoignage->age . ' ans' : 'Non renseigné' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Colonne droite : Localisation & Projet --}}
                    <div>
                        <div class="temoignage-show-section">
                            <div class="temoignage-show-section-title">
                                <i class="fas fa-map-marker-alt"></i> Localisation & Projet
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-error-light); color: var(--color-accent);">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Province</div>
                                    <div class="temoignage-show-field-value {{ $temoignage->province ? '' : 'empty' }}">
                                        {{ $temoignage->province ?: 'Non renseignée' }}
                                    </div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-info-light); color: var(--color-info);">
                                    <i class="fas fa-location-dot"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Localisation</div>
                                    <div class="temoignage-show-field-value {{ $temoignage->localisation ? '' : 'empty' }}">
                                        {{ $temoignage->localisation ?: 'Non renseignée' }}
                                    </div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-primary-lighter); color: var(--color-primary);">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Projet lié</div>
                                    <div class="temoignage-show-field-value {{ $temoignage->projet ? '' : 'empty' }}">
                                        @if($temoignage->projet)
                                            <a href="{{ route('admin.projets.show', $temoignage->projet) }}" style="color: var(--color-primary); text-decoration: underline;">
                                                {{ $temoignage->projet->titre }}
                                            </a>
                                        @else
                                            Aucun projet lié
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deuxième grille : Statut & Historique --}}
                <div class="temoignage-show-grid" style="margin-top: 0;">
                    <div>
                        <div class="temoignage-show-section">
                            <div class="temoignage-show-section-title">
                                <i class="fas fa-info-circle"></i> Statut & Traitement
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-warning-light); color: #856404;">
                                    <i class="fas fa-flag"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Statut actuel</div>
                                    <div class="temoignage-show-field-value">
                                        <span class="temoignage-statut-badge temoignage-statut-{{ $temoignage->statut }}">
                                            <i class="fas fa-{{ 
                                                $temoignage->statut === 'en_attente' ? 'clock' : 
                                                ($temoignage->statut === 'approuve' ? 'check-circle' : 
                                                ($temoignage->statut === 'rejete' ? 'times-circle' : 'archive'))
                                            }}"></i>
                                            {{ $temoignage->statutLibelle() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-info-light); color: var(--color-info);">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Traité par</div>
                                    <div class="temoignage-show-field-value {{ $temoignage->traitePar ? '' : 'empty' }}">
                                        {{ $temoignage->traitePar ? $temoignage->traitePar->nom : 'En attente de traitement' }}
                                    </div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-success-light); color: var(--color-success);">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Date de validation</div>
                                    <div class="temoignage-show-field-value {{ $temoignage->date_validation ? '' : 'empty' }}">
                                        {{ $temoignage->date_validation ? $temoignage->date_validation->format('d/m/Y à H:i') : 'Non validé' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="temoignage-show-section">
                            <div class="temoignage-show-section-title">
                                <i class="fas fa-clock"></i> Historique
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: var(--color-primary-lighter); color: var(--color-primary);">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Date de soumission</div>
                                    <div class="temoignage-show-field-value">
                                        {{ $temoignage->date_soumission ? $temoignage->date_soumission->format('d/m/Y à H:i') : $temoignage->created_at->format('d/m/Y à H:i') }}
                                    </div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: #fff3cd; color: #e67e22;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Dernière modification</div>
                                    <div class="temoignage-show-field-value">{{ $temoignage->updated_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>

                            <div class="temoignage-show-field">
                                <div class="temoignage-show-field-icon" style="background: #f3e5f5; color: #8e44ad;">
                                    <i class="fas fa-sort-numeric-down"></i>
                                </div>
                                <div>
                                    <div class="temoignage-show-field-label">Ordre d'affichage</div>
                                    <div class="temoignage-show-field-value">{{ $temoignage->ordre ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="temoignage-show-actions">
                <div class="temoignage-show-actions-left">
                    @if($temoignage->statut !== 'approuve')
                        <form action="{{ route('admin.temoignages.approuver', $temoignage) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-primary btn-sm btn-approve-temoignage" 
                                    data-nom="{{ $temoignage->nomComplet() }}">
                                <i class="fas fa-check-circle"></i> Approuver
                            </button>
                        </form>
                    @endif

                    @if($temoignage->statut === 'en_attente')
                        <button type="button" class="btn btn-accent btn-sm btn-reject-temoignage" 
                                data-url="{{ route('admin.temoignages.rejeter', $temoignage) }}">
                            <i class="fas fa-times-circle"></i> Rejeter
                        </button>
                    @endif

                    @if($temoignage->statut === 'approuve')
                        <form action="{{ route('admin.temoignages.archiver', $temoignage) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-outline btn-sm">
                                <i class="fas fa-archive"></i> Archiver
                            </button>
                        </form>
                    @endif

                    @if($temoignage->statut === 'archive')
                        <form action="{{ route('admin.temoignages.desarchiver', $temoignage) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-outline btn-sm">
                                <i class="fas fa-undo"></i> Désarchiver
                            </button>
                        </form>
                    @endif
                </div>

                <div class="temoignage-show-actions-right">
                    <a href="{{ route('admin.temoignages.edit', $temoignage) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="{{ route('admin.temoignages.destroy', $temoignage) }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce témoignage ?\n\nCette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-accent btn-sm">
                            <i class="fas fa-trash-alt"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>

        </div>
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

    {{-- Formulaire caché pour le rejet --}}
    <form id="rejectForm" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="motif_rejet" id="rejectMotifInput" value="">
    </form>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-temoignages.js') }}"></script>
@endsection