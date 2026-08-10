@extends('layouts.admin')

@section('title', 'Détail du document')

@section('page_title', 'Détail du document')
@section('page_icon', 'eye')
@section('page_subtitle', 'Consultez les informations complètes du document, prévisualisez-le et téléchargez-le.')

@php
    $breadcrumb = ['Documents officiels' => route('admin.documents.index'), 'Détail' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-documents.css') }}">
@endsection

@section('content')

    <div class="document-show-container">

        {{-- Bouton retour --}}
        <a href="{{ route('admin.documents.index') }}" class="document-show-back">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>

        {{-- Navigation précédent/suivant --}}
        <div class="document-show-nav">
            <div>
                @if($precedent)
                    <a href="{{ route('admin.documents.show', $precedent) }}" class="document-show-nav-link">
                        <i class="fas fa-chevron-left"></i> Document précédent
                    </a>
                @endif
            </div>
            <div>
                @if($suivant)
                    <a href="{{ route('admin.documents.show', $suivant) }}" class="document-show-nav-link">
                        Document suivant <i class="fas fa-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Carte principale --}}
        <div class="document-show-card">

            {{-- ============================================ --}}
            {{-- EN-TÊTE --}}
            {{-- ============================================ --}}
            <div class="document-show-header">
                <div class="document-show-icon">
                    <i class="fas fa-{{ 
                        $document->extension === 'pdf' ? 'file-pdf' : 
                        (in_array($document->extension, ['doc', 'docx']) ? 'file-word' : 
                        (in_array($document->extension, ['xls', 'xlsx']) ? 'file-excel' : 
                        (in_array($document->extension, ['ppt', 'pptx']) ? 'file-powerpoint' : 'file-alt')))
                    }}"></i>
                </div>
                <div class="document-show-header-info">
                    <h2>{{ $document->titre }}</h2>
                    <div class="document-show-header-meta">
                        <span class="document-show-header-badge">
                            <i class="fas fa-{{ 
                                $document->type === 'statuts' ? 'balance-scale' : 
                                ($document->type === 'roi' ? 'gavel' : 
                                ($document->type === 'proces_verbal' ? 'clipboard' : 
                                ($document->type === 'convention' ? 'handshake' : 
                                ($document->type === 'rapport_annuel' ? 'chart-bar' : 
                                ($document->type === 'rapport_activite' ? 'chart-line' : 
                                ($document->type === 'guide_procedure' ? 'book' : 
                                ($document->type === 'politique_interne' ? 'shield-alt' : 
                                ($document->type === 'communique_officiel' ? 'bullhorn' : 'file-alt'))))))))
                            }}"></i>
                            {{ $document->typeLibelle() }}
                        </span>
                        <span class="document-show-header-badge">
                            <i class="fas fa-{{ $document->public ? 'globe' : 'lock' }}"></i>
                            {{ $document->public ? 'Document public' : 'Document privé' }}
                        </span>
                        @if($document->version)
                            <span class="document-show-header-badge">
                                <i class="fas fa-code-branch"></i> v{{ $document->version }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- CORPS --}}
            {{-- ============================================ --}}
            <div class="document-show-body">

                {{-- Aperçu PDF --}}
                @if($document->extension === 'pdf')
                    <div class="document-show-preview">
                        <iframe src="{{ route('admin.documents.preview', $document) }}" frameborder="0"></iframe>
                    </div>
                @else
                    <div class="document-show-preview">
                        <div class="document-show-preview-unavailable">
                            <i class="fas fa-file-alt"></i>
                            <p><strong>Aperçu non disponible</strong></p>
                            <p>L'aperçu est disponible uniquement pour les fichiers PDF. Veuillez télécharger le document pour le consulter.</p>
                            <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-primary btn-sm" style="margin-top: 12px;">
                                <i class="fas fa-download"></i> Télécharger le document
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Description --}}
                @if($document->description)
                    <div class="document-show-description">
                        {{ $document->description }}
                    </div>
                @else
                    <div class="document-show-description empty">
                        <i class="fas fa-pen" style="margin-right: 8px;"></i> Aucune description pour ce document.
                    </div>
                @endif

                {{-- Métadonnées --}}
                <div class="document-show-grid">
                    {{-- Colonne gauche : Informations générales --}}
                    <div>
                        <div class="document-show-section">
                            <div class="document-show-section-title">
                                <i class="fas fa-info-circle"></i> Informations générales
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-primary-lighter); color: var(--color-primary);">
                                    <i class="fas fa-heading"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Titre</div>
                                    <div class="document-show-field-value">{{ $document->titre }}</div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: #f3e5f5; color: #8e44ad;">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Type</div>
                                    <div class="document-show-field-value">{{ $document->typeLibelle() }}</div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-info-light); color: var(--color-info);">
                                    <i class="fas fa-folder"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Catégorie</div>
                                    <div class="document-show-field-value {{ $document->categorie ? '' : 'empty' }}">
                                        {{ $document->categorie ?: 'Non classé' }}
                                    </div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-success-light); color: var(--color-success);">
                                    <i class="fas fa-code-branch"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Version</div>
                                    <div class="document-show-field-value">v{{ $document->version ?: '1.0' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Colonne droite : Fichier & Visibilité --}}
                    <div>
                        <div class="document-show-section">
                            <div class="document-show-section-title">
                                <i class="fas fa-file"></i> Fichier & Visibilité
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-error-light); color: var(--color-accent);">
                                    <i class="fas fa-file"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Format</div>
                                    <div class="document-show-field-value" style="text-transform: uppercase;">{{ $document->extension }}</div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: #fff3cd; color: #e67e22;">
                                    <i class="fas fa-weight-hanging"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Taille</div>
                                    <div class="document-show-field-value">{{ $document->tailleLisible() }}</div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-success-light); color: var(--color-success);">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Visibilité</div>
                                    <div class="document-show-field-value">
                                        <span class="document-visibilite-badge document-visibilite-{{ $document->public ? 'public' : 'prive' }}">
                                            <i class="fas fa-{{ $document->public ? 'globe' : 'lock' }}"></i>
                                            {{ $document->public ? 'Public' : 'Privé' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-primary-lighter); color: var(--color-primary);">
                                    <i class="fas fa-download"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Téléchargements</div>
                                    <div class="document-show-field-value">{{ $document->telechargements }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deuxième grille : Dates & Signataires --}}
                <div class="document-show-grid" style="margin-top: 0;">
                    <div>
                        <div class="document-show-section">
                            <div class="document-show-section-title">
                                <i class="fas fa-calendar-alt"></i> Dates
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-primary-lighter); color: var(--color-primary);">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Date du document</div>
                                    <div class="document-show-field-value {{ $document->date_document ? '' : 'empty' }}">
                                        {{ $document->date_document ? $document->date_document->format('d/m/Y') : 'Non renseignée' }}
                                    </div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-success-light); color: var(--color-success);">
                                    <i class="fas fa-signature"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Date de signature</div>
                                    <div class="document-show-field-value {{ $document->date_signature ? '' : 'empty' }}">
                                        {{ $document->date_signature ? $document->date_signature->format('d/m/Y') : 'Non renseignée' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="document-show-section">
                            <div class="document-show-section-title">
                                <i class="fas fa-users"></i> Signataires & Import
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: #f3e5f5; color: #8e44ad;">
                                    <i class="fas fa-pen-fancy"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Signataires</div>
                                    <div class="document-show-field-value {{ $document->signataires ? '' : 'empty' }}">
                                        {{ $document->signataires ?: 'Non renseignés' }}
                                    </div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-info-light); color: var(--color-info);">
                                    <i class="fas fa-user-upload"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Importé par</div>
                                    <div class="document-show-field-value">
                                        {{ $document->uploadPar ? $document->uploadPar->nom : '—' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Historique --}}
                <div class="document-show-grid" style="margin-top: 0;">
                    <div>
                        <div class="document-show-section">
                            <div class="document-show-section-title">
                                <i class="fas fa-clock"></i> Historique
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: var(--color-success-light); color: var(--color-success);">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Date d'importation</div>
                                    <div class="document-show-field-value">{{ $document->created_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>

                            <div class="document-show-field">
                                <div class="document-show-field-icon" style="background: #fff3cd; color: #e67e22;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="document-show-field-label">Dernière modification</div>
                                    <div class="document-show-field-value">{{ $document->updated_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="document-show-actions">
                <div class="document-show-actions-left">
                    <a href="{{ route('admin.documents.download', $document) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-download"></i> Télécharger
                    </a>
                    @if($document->extension === 'pdf')
                        <a href="{{ route('admin.documents.preview', $document) }}" target="_blank" class="btn btn-outline btn-sm">
                            <i class="fas fa-external-link-alt"></i> Ouvrir dans un nouvel onglet
                        </a>
                    @endif
                    <form action="{{ route('admin.documents.togglePublic', $document) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-{{ $document->public ? 'outline' : 'primary' }} btn-sm">
                            <i class="fas fa-{{ $document->public ? 'lock' : 'globe' }}"></i>
                            {{ $document->public ? 'Passer en privé' : 'Rendre public' }}
                        </button>
                    </form>
                </div>

                <div class="document-show-actions-right">
                    <a href="{{ route('admin.documents.edit', $document) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce document ?\n\nCette action est irréversible et le fichier sera supprimé du serveur.');">
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
    <script src="{{ asset('assets/js/admin-documents.js') }}"></script>
@endsection