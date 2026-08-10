@extends('layouts.admin')

@section('title', 'Détail du média')

@section('page_title', 'Détail du média')
@section('page_icon', 'info-circle')
@section('page_subtitle', 'Visualisez le média en grand et consultez toutes ses métadonnées.')

@php
    $breadcrumb = ['Galerie Médias' => route('admin.medias.index'), 'Détail' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-medias.css') }}">
    <style>
        /* Styles spécifiques à la page show */
        .media-detail-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .media-detail-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-primary);
            text-decoration: none;
            margin-bottom: 20px;
            padding: 8px 16px;
            border-radius: 8px;
            background: var(--color-primary-lighter);
            transition: all 0.3s ease;
        }

        .media-detail-back:hover {
            background: var(--color-primary);
            color: #fff;
            transform: translateX(-3px);
        }

        .media-detail-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .media-detail-viewer {
            width: 100%;
            background: #0f1a2e;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-height: 400px;
            max-height: 600px;
        }

        .media-detail-viewer img {
            max-width: 100%;
            max-height: 600px;
            object-fit: contain;
        }

        .media-detail-viewer video {
            max-width: 100%;
            max-height: 600px;
            outline: none;
        }

        .media-detail-viewer-placeholder {
            font-size: 6rem;
            color: rgba(255, 255, 255, 0.1);
        }

        .media-detail-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.2);
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            z-index: 10;
            text-decoration: none;
        }

        .media-detail-nav:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        .media-detail-nav.prev {
            left: 20px;
        }

        .media-detail-nav.next {
            right: 20px;
        }

        .media-detail-body {
            padding: 32px 36px;
        }

        .media-detail-header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 2px solid #f0f0f0;
        }

        .media-detail-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .media-detail-icon.image {
            background: var(--color-primary-lighter);
            color: var(--color-primary);
        }

        .media-detail-icon.video {
            background: var(--color-error-light);
            color: var(--color-accent);
        }

        .media-detail-header-info {
            flex: 1;
            min-width: 0;
        }

        .media-detail-header-info h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .media-detail-header-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .media-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .media-detail-section {
            margin-bottom: 28px;
        }

        .media-detail-section:last-child {
            margin-bottom: 0;
        }

        .media-detail-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--color-primary-lighter);
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .media-detail-section-title i {
            color: var(--color-primary);
            font-size: 12px;
        }

        .media-detail-field {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f5f6f8;
        }

        .media-detail-field:last-child {
            border-bottom: none;
        }

        .media-detail-field-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .media-detail-field-icon.blue {
            background: var(--color-primary-lighter);
            color: var(--color-primary);
        }

        .media-detail-field-icon.green {
            background: var(--color-success-light);
            color: var(--color-success);
        }

        .media-detail-field-icon.orange {
            background: #fff3cd;
            color: #e67e22;
        }

        .media-detail-field-icon.red {
            background: var(--color-error-light);
            color: var(--color-accent);
        }

        .media-detail-field-icon.purple {
            background: #f3e5f5;
            color: #8e44ad;
        }

        .media-detail-field-icon.teal {
            background: var(--color-info-light);
            color: var(--color-info);
        }

        .media-detail-field-label {
            font-size: 11px;
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }

        .media-detail-field-value {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            word-break: break-word;
        }

        .media-detail-field-value.empty {
            color: #ccc;
            font-style: italic;
            font-weight: 400;
        }

        .media-detail-field-value a {
            color: var(--color-primary);
            text-decoration: underline;
        }

        .media-detail-field-value a:hover {
            color: var(--color-primary-dark);
        }

        .media-detail-description {
            background: var(--color-bg-light);
            border-radius: 12px;
            padding: 18px 20px;
            font-size: 14px;
            color: var(--color-text-secondary);
            line-height: 1.8;
            margin-bottom: 28px;
        }

        .media-detail-description.empty {
            color: #ccc;
            font-style: italic;
            text-align: center;
            padding: 28px;
        }

        .media-detail-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 24px 36px;
            background: #fafbfc;
            border-top: 1px solid #eef0f4;
        }

        .media-detail-actions-left {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .media-detail-actions-right {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .media-detail-grid {
                grid-template-columns: 1fr;
            }

            .media-detail-viewer {
                min-height: 250px;
                max-height: 400px;
            }

            .media-detail-body {
                padding: 20px 16px;
            }

            .media-detail-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .media-detail-actions {
                flex-direction: column;
                gap: 12px;
                padding: 20px;
            }

            .media-detail-actions-left,
            .media-detail-actions-right {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')

    <div class="media-detail-container">

        {{-- Bouton retour --}}
        <a href="{{ route('admin.medias.index') }}" class="media-detail-back">
            <i class="fas fa-arrow-left"></i> Retour à la galerie
        </a>

        {{-- Carte principale --}}
        <div class="media-detail-card">

            {{-- ============================================ --}}
            {{-- VISIONNEUSE --}}
            {{-- ============================================ --}}
            <div class="media-detail-viewer">
                @if($media->type === 'image')
                    <img src="{{ $media->url() }}" alt="{{ $media->titre }}">
                @elseif($media->type === 'video')
                    <video controls>
                        <source src="{{ $media->url() }}" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                @else
                    <div class="media-detail-viewer-placeholder">
                        <i class="fas fa-file"></i>
                    </div>
                @endif

                {{-- Navigation précédent/suivant --}}
                @if($precedent)
                    <a href="{{ route('admin.medias.show', $precedent) }}" class="media-detail-nav prev" title="Média précédent">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                @if($suivant)
                    <a href="{{ route('admin.medias.show', $suivant) }}" class="media-detail-nav next" title="Média suivant">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @endif
            </div>

            {{-- ============================================ --}}
            {{-- CORPS --}}
            {{-- ============================================ --}}
            <div class="media-detail-body">

                {{-- En-tête --}}
                <div class="media-detail-header">
                    <div class="media-detail-icon {{ $media->type }}">
                        <i class="fas fa-{{ $media->type === 'image' ? 'image' : 'video' }}"></i>
                    </div>
                    <div class="media-detail-header-info">
                        <h2>{{ $media->titre ?: 'Sans titre' }}</h2>
                        <div class="media-detail-header-meta">
                            <span class="article-statut-badge article-statut-{{ $media->publier ? 'publie' : 'brouillon' }}">
                                <i class="fas fa-{{ $media->publier ? 'globe' : 'lock' }}"></i>
                                {{ $media->publier ? 'Public' : 'Privé' }}
                            </span>
                            @if($media->album)
                                <span class="article-cat-badge article-cat-actualite">
                                    <i class="fas fa-folder"></i> {{ $media->album }}
                                </span>
                            @endif
                            @if($media->projet)
                                <span class="article-cat-badge article-cat-projet">
                                    <i class="fas fa-project-diagram"></i> {{ Str::limit($media->projet->titre, 40) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                @if($media->description)
                    <div class="media-detail-description">
                        {{ $media->description }}
                    </div>
                @else
                    <div class="media-detail-description empty">
                        <i class="fas fa-pen" style="margin-right: 8px;"></i> Aucune description pour ce média.
                    </div>
                @endif

                {{-- Grille de métadonnées --}}
                <div class="media-detail-grid">
                    {{-- Colonne gauche --}}
                    <div>
                        <div class="media-detail-section">
                            <div class="media-detail-section-title">
                                <i class="fas fa-info-circle"></i> Informations générales
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon blue">
                                    <i class="fas fa-heading"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Titre</div>
                                    <div class="media-detail-field-value">{{ $media->titre ?: 'Sans titre' }}</div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon teal">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Type</div>
                                    <div class="media-detail-field-value">{{ $media->type === 'image' ? 'Image' : 'Vidéo' }}</div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon green">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Album</div>
                                    <div class="media-detail-field-value {{ $media->album ? '' : 'empty' }}">
                                        {{ $media->album ?: 'Non classé' }}
                                    </div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon purple">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Projet lié</div>
                                    <div class="media-detail-field-value {{ $media->projet ? '' : 'empty' }}">
                                        @if($media->projet)
                                            <a href="{{ route('admin.projets.show', $media->projet) }}">
                                                {{ $media->projet->titre }}
                                            </a>
                                        @else
                                            Aucun projet
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Colonne droite --}}
                    <div>
                        <div class="media-detail-section">
                            <div class="media-detail-section-title">
                                <i class="fas fa-camera"></i> Détails techniques
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon orange">
                                    <i class="fas fa-weight-hanging"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Taille du fichier</div>
                                    <div class="media-detail-field-value">
                                        {{ $media->taille > 1048576 ? round($media->taille / 1048576, 1) . ' Mo' : round($media->taille / 1024, 1) . ' Ko' }}
                                    </div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon blue">
                                    <i class="fas fa-file"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Format</div>
                                    <div class="media-detail-field-value" style="text-transform: uppercase;">
                                        {{ pathinfo($media->fichier, PATHINFO_EXTENSION) }}
                                    </div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon red">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Lieu</div>
                                    <div class="media-detail-field-value {{ $media->lieu ? '' : 'empty' }}">
                                        {{ $media->lieu ?: 'Non renseigné' }}
                                    </div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon teal">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Date de prise</div>
                                    <div class="media-detail-field-value {{ $media->date_prise ? '' : 'empty' }}">
                                        {{ $media->date_prise ? $media->date_prise->format('d/m/Y') : 'Non renseignée' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section supplémentaire --}}
                <div class="media-detail-grid" style="margin-top: 0;">
                    <div>
                        <div class="media-detail-section">
                            <div class="media-detail-section-title">
                                <i class="fas fa-user"></i> Crédits
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon green">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Auteur / Photographe</div>
                                    <div class="media-detail-field-value {{ $media->auteur ? '' : 'empty' }}">
                                        {{ $media->auteur ?: 'Non renseigné' }}
                                    </div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon blue">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Importé par</div>
                                    <div class="media-detail-field-value">
                                        {{ $media->uploadPar ? $media->uploadPar->nom : '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon teal">
                                    <i class="fas fa-sort-numeric-down"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Ordre d'affichage</div>
                                    <div class="media-detail-field-value">{{ $media->ordre ?? 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="media-detail-section">
                            <div class="media-detail-section-title">
                                <i class="fas fa-clock"></i> Historique
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon green">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Date d'ajout</div>
                                    <div class="media-detail-field-value">{{ $media->created_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon orange">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Dernière modification</div>
                                    <div class="media-detail-field-value">{{ $media->updated_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>

                            <div class="media-detail-field">
                                <div class="media-detail-field-icon purple">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div>
                                    <div class="media-detail-field-label">Slug / Identifiant</div>
                                    <div class="media-detail-field-value">
                                        <code style="background: #f5f5f5; padding: 2px 8px; border-radius: 4px; font-size: 12px;">#{{ $media->id }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="media-detail-actions">
                <div class="media-detail-actions-left">
                    <form action="{{ route('admin.medias.togglePublier', $media) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-{{ $media->publier ? 'outline' : 'primary' }} btn-sm">
                            <i class="fas fa-{{ $media->publier ? 'lock' : 'globe' }}"></i>
                            {{ $media->publier ? 'Passer en privé' : 'Publier' }}
                        </button>
                    </form>
                </div>

                <div class="media-detail-actions-right">
                    <a href="{{ route('admin.medias.edit', $media) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ $media->url() }}" download class="btn btn-outline btn-sm">
                        <i class="fas fa-download"></i> Télécharger
                    </a>
                    <form action="{{ route('admin.medias.destroy', $media) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce média ?\n\nCette action est irréversible.');">
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

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-medias.js') }}"></script>
@endsection