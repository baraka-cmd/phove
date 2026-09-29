@extends('layouts.admin')

@section('title', $projet->titre . ' | PHOVE')

@section('page_title', Str::limit($projet->titre, 60))
@section('page_icon', 'project-diagram')
@section('page_subtitle', 'Fiche détaillée du projet — ' . $projet->statutLibelle())

@php
    $breadcrumb = [
        ['label' => 'Projets', 'url' => route('admin.projets.index')],
        Str::limit($projet->titre, 40),
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-projets-show.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE DU PROJET --}}
    {{-- ============================================ --}}
    <div class="projet-show-header">

        {{-- Image --}}
        <div class="projet-show-image">
            @if($projet->image_principale)
                <img src="{{ route('media.show', ['path' => ltrim($projet->image_principale, '/')]) }}"
                     alt="{{ $projet->titre }}"
                     loading="lazy"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="projet-show-image-placeholder" style="display: none;">
                    <i class="fas fa-project-diagram"></i>
                </div>
            @else
                <div class="projet-show-image-placeholder">
                    <i class="fas fa-project-diagram"></i>
                </div>
            @endif
        </div>

        {{-- Informations --}}
        <div class="projet-show-info">
            <h2>{{ $projet->titre }}</h2>

            <div class="projet-show-meta">
                <span class="projet-show-badge projet-show-badge-primary">
                    {{ $projet->domaineLibelle() }}
                </span>

                <span class="projet-show-badge {{ $projet->statut === 'en_cours' ? 'projet-show-badge-danger' : ($projet->statut === 'termine' ? 'projet-show-badge-success' : 'projet-show-badge-info') }}">
                    {{ $projet->statutLibelle() }}
                </span>

                @if($projet->code_projet)
                    <span class="projet-show-badge projet-show-badge-dark" title="Code officiel du projet">
                        <i class="fas fa-barcode"></i> {{ $projet->code_projet }}
                    </span>
                @endif

                @if($projet->publier)
                    <span class="projet-show-badge projet-show-badge-success">
                        <i class="fas fa-eye"></i> Publié
                    </span>
                @else
                    <span class="projet-show-badge projet-show-badge-warning">
                        <i class="fas fa-eye-slash"></i> Non publié
                    </span>
                @endif
            </div>
        </div>

        {{-- Actions rapides --}}
        <div class="projet-show-actions">
            <a href="{{ route('admin.projets.indicateurs.index', $projet) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-tachometer-alt"></i> Indicateurs
            </a>
            <a href="{{ route('admin.projets.edit', $projet) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE PRINCIPALE --}}
    {{-- ============================================ --}}
    <div class="projet-show-grid">

        {{-- ======================================== --}}
        {{-- COLONNE GAUCHE --}}
        {{-- ======================================== --}}
        <div>

            {{-- Informations générales --}}
            <div class="projet-detail-card">
                <div class="projet-detail-card-header">
                    <i class="fas fa-info-circle"></i> Informations générales
                </div>
                <div class="projet-detail-card-body">

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Code projet</span>
                        <span class="projet-detail-value">{{ $projet->code_projet ?: 'N/A' }}</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Domaine</span>
                        <span class="projet-detail-value">{{ $projet->domaineLibelle() }}</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Zone</span>
                        <span class="projet-detail-value">{{ $projet->zone_geographique }}</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Province</span>
                        <span class="projet-detail-value">{{ $projet->province ?? '—' }}</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Période</span>
                        <span class="projet-detail-value">
                            {{ $projet->date_debut->format('d/m/Y') }}
                            @if($projet->date_fin)
                                — {{ $projet->date_fin->format('d/m/Y') }}
                            @endif
                        </span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Statut</span>
                        <span class="projet-detail-value">{{ $projet->statutLibelle() }}</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Publié</span>
                        <span class="projet-detail-value">{{ $projet->publier ? 'Oui' : 'Non' }}</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Slug</span>
                        <span class="projet-detail-value projet-detail-value-small">{{ $projet->slug }}</span>
                    </div>

                </div>
            </div>

            {{-- Budget et financement --}}
            <div class="projet-detail-card projet-detail-card-mt">
                <div class="projet-detail-card-header">
                    <i class="fas fa-coins"></i> Budget et financement
                </div>
                <div class="projet-detail-card-body">

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Budget total</span>
                        <span class="projet-detail-value">{{ number_format($projet->budget_total, 0, ',', ' ') }} $</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Budget utilisé</span>
                        <span class="projet-detail-value">{{ number_format($projet->budget_utilise, 0, ',', ' ') }} $</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Progression</span>
                        <span class="projet-detail-value">{{ $projet->pourcentageBudget() }}%</span>
                    </div>

                    <div class="projet-budget-progress">
                        <div class="projet-budget-bar">
                            <div class="projet-budget-fill {{ $projet->pourcentageBudget() >= 100 ? 'full' : '' }}"
                                 style="width: {{ $projet->pourcentageBudget() }}%"></div>
                        </div>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Bailleur</span>
                        <span class="projet-detail-value">{{ $projet->bailleur ?? '—' }}</span>
                    </div>

                    <div class="projet-detail-row">
                        <span class="projet-detail-label">Source</span>
                        <span class="projet-detail-value">{{ $projet->source_financement ?? '—' }}</span>
                    </div>

                </div>
            </div>

            {{-- Partenaires --}}
            @if($projet->partenaires->count() > 0)
            <div class="projet-detail-card projet-detail-card-mt">
                <div class="projet-detail-card-header">
                    <i class="fas fa-handshake"></i> Partenaires ({{ $projet->partenaires->count() }})
                </div>
                <div class="projet-detail-card-body">

                    @foreach($projet->partenaires as $partenaire)
                        <div class="projet-partenaire-item">
                            <div class="projet-partenaire-logo">
                                {{ $partenaire->sigleOuInitiales() }}
                            </div>
                            <div>
                                <div class="projet-partenaire-nom">
                                    {{ $partenaire->nom }}
                                </div>
                                <div class="projet-partenaire-meta">
                                    {{ $partenaire->typeLibelle() }}
                                    @if($partenaire->pivot->role)
                                        — {{ $partenaire->pivot->role }}
                                    @endif
                                    @if($partenaire->pivot->contribution > 0)
                                        — {{ number_format($partenaire->pivot->contribution, 0, ',', ' ') }} $
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
            @endif

        </div>

        {{-- ======================================== --}}
        {{-- COLONNE DROITE --}}
        {{-- ======================================== --}}
        <div>

            {{-- Description --}}
            <div class="projet-detail-card">
                <div class="projet-detail-card-header">
                    <i class="fas fa-file-alt"></i> Description
                </div>
                <div class="projet-detail-card-body">
                    <div class="projet-text-block">{{ $projet->description }}</div>
                </div>
            </div>

            {{-- Objectifs --}}
            <div class="projet-detail-card projet-detail-card-mt">
                <div class="projet-detail-card-header">
                    <i class="fas fa-bullseye"></i> Objectifs
                </div>
                <div class="projet-detail-card-body">
                    <div class="projet-text-block">{{ $projet->objectifs ?? 'Non définis' }}</div>
                </div>
            </div>

            {{-- Résultats attendus --}}
            <div class="projet-detail-card projet-detail-card-mt">
                <div class="projet-detail-card-header">
                    <i class="fas fa-chart-bar"></i> Résultats attendus
                </div>
                <div class="projet-detail-card-body">
                    <div class="projet-text-block">{{ $projet->resultats_attendus ?? 'Non définis' }}</div>
                </div>
            </div>

            {{-- Indicateurs --}}
            @if($projet->indicateurs->count() > 0)
            <div class="projet-detail-card projet-detail-card-mt">
                <div class="projet-detail-card-header">
                    <i class="fas fa-tachometer-alt"></i> Indicateurs ({{ $projet->indicateurs->count() }})
                </div>
                <div class="projet-detail-card-body">

                    @foreach($projet->indicateurs->take(5) as $kpi)
                        <div class="projet-kpi-item">
                            <div class="projet-kpi-header">
                                <span class="projet-kpi-name">{{ $kpi->nom_indicateur }}</span>
                                <span class="projet-kpi-value">{{ $kpi->pourcentage() }}%</span>
                            </div>
                            <div class="projet-kpi-bar">
                                <div class="projet-kpi-fill {{ $kpi->objectifAtteint() ? 'achieved' : '' }}"
                                     style="width: {{ $kpi->pourcentage() }}%"></div>
                            </div>
                            <div class="projet-kpi-meta">
                                {{ $kpi->valeur_actuelle }}/{{ $kpi->valeur_cible }} {{ $kpi->unite }}
                            </div>
                        </div>
                    @endforeach

                    <div class="projet-kpi-actions">
                        <a href="{{ route('admin.projets.indicateurs.index', $projet) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-list"></i> Gérer tous les indicateurs
                        </a>
                    </div>

                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GALERIE MÉDIA --}}
    {{-- ============================================ --}}
    <div class="projet-detail-card projet-detail-card-mt">
        <div class="projet-detail-card-header projet-galerie-header">
            <div>
                <i class="fas fa-images"></i> Galerie média
                <span class="projet-galerie-count">
                    ({{ $projet->medias->count() }} média{{ $projet->medias->count() > 1 ? 's' : '' }})
                </span>
            </div>
            <a href="{{ route('admin.projets.edit', $projet) }}#galerie"
               class="btn btn-outline btn-sm">
                <i class="fas fa-plus-circle"></i> Ajouter des médias
            </a>
        </div>

        <div class="projet-detail-card-body">

            @if($projet->medias->count() > 0)

                <div class="projet-medias-grid">

                    @foreach($projet->medias as $media)
                        @php
                            $thumbUrl = $media->miniature
                                ? route('media.show', ['path' => ltrim($media->miniature, '/')])
                                : ($media->fichier
                                    ? route('media.show', ['path' => ltrim($media->fichier, '/')])
                                    : null);

                            $fichierUrl = $media->fichier
                                ? route('media.show', ['path' => ltrim($media->fichier, '/')])
                                : null;
                        @endphp

                        <a href="{{ $fichierUrl }}"
                           target="_blank"
                           rel="noopener"
                           class="projet-media-item"
                           title="{{ $media->titre }}">

                            <div class="projet-media-thumb">
                                @if($thumbUrl)
                                    <img src="{{ $thumbUrl }}"
                                         alt="{{ $media->titre }}"
                                         loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="projet-media-placeholder" style="display: none;">
                                        <i class="fas fa-{{ $media->type === 'video' ? 'video' : 'image' }}"></i>
                                    </div>
                                @else
                                    <div class="projet-media-placeholder">
                                        <i class="fas fa-{{ $media->type === 'video' ? 'video' : 'image' }}"></i>
                                    </div>
                                @endif

                                {{-- Badge type --}}
                                <span class="projet-media-type-badge projet-media-type-{{ $media->type }}">
                                    <i class="fas fa-{{ $media->type === 'video' ? 'play-circle' : 'camera' }}"></i>
                                    {{ $media->type === 'video' ? 'Vidéo' : 'Photo' }}
                                </span>

                                {{-- Overlay --}}
                                <span class="projet-media-overlay">
                                    <i class="fas fa-expand"></i>
                                </span>
                            </div>

                            <span class="projet-media-title">
                                {{ Str::limit($media->titre ?: 'Sans titre', 30) }}
                            </span>
                        </a>
                    @endforeach

                </div>

            @else
                <div class="projet-medias-empty">
                    <i class="fas fa-images"></i>
                    <p>Aucun média pour ce projet.</p>
                    <a href="{{ route('admin.projets.edit', $projet) }}#galerie" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle"></i> Ajouter des médias
                    </a>
                </div>
            @endif

        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACTIONS EN BAS --}}
    {{-- ============================================ --}}
    <div class="projet-show-actions-bar">
        <a href="{{ route('admin.projets.indicateurs.index', $projet) }}" class="btn btn-outline">
            <i class="fas fa-tachometer-alt"></i> Gérer les indicateurs
        </a>
        <a href="{{ route('admin.projets.edit', $projet) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Modifier le projet
        </a>
        <a href="{{ route('admin.projets.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script src="{{ asset('assets/js/admin-projets-show.js') }}"></script>
@endsection