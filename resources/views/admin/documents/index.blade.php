@extends('layouts.admin')

@section('title', 'Documents officiels')

@section('page_title', 'Documents officiels')
@section('page_icon', 'folder-open')
@section('page_subtitle', 'Gérez les documents officiels : statuts, procès-verbaux, rapports, conventions et autres fichiers administratifs.')

@php
    $breadcrumb = ['Documents officiels' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-documents.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="documents-stats-grid">
        <div class="document-stat-card">
            <div class="document-stat-icon">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="document-stat-content">
                <div class="document-stat-value">{{ $totalDocuments }}</div>
                <div class="document-stat-label">Total documents</div>
            </div>
        </div>
        <div class="document-stat-card">
            <div class="document-stat-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div class="document-stat-content">
                <div class="document-stat-value">{{ $documentsPublics }}</div>
                <div class="document-stat-label">Documents publics</div>
            </div>
        </div>
        <div class="document-stat-card">
            <div class="document-stat-icon">
                <i class="fas fa-lock"></i>
            </div>
            <div class="document-stat-content">
                <div class="document-stat-value">{{ $documentsPrives }}</div>
                <div class="document-stat-label">Documents privés</div>
            </div>
        </div>
        <div class="document-stat-card">
            <div class="document-stat-icon">
                <i class="fas fa-download"></i>
            </div>
            <div class="document-stat-content">
                <div class="document-stat-value">{{ $totalTelechargements }}</div>
                <div class="document-stat-label">Téléchargements</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.documents.index') }}" method="GET" class="documents-toolbar">
        <div class="documents-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="documentSearch" value="{{ request('recherche') }}" 
                   class="documents-search-input" placeholder="Rechercher un document...">
        </div>

        <div class="documents-filtres">
            <select name="type" class="documents-filtre-select">
                <option value="">Tous les types</option>
                <option value="statuts" {{ request('type') === 'statuts' ? 'selected' : '' }}>Statuts</option>
                <option value="roi" {{ request('type') === 'roi' ? 'selected' : '' }}>Règlement d'Ordre Intérieur</option>
                <option value="proces_verbal" {{ request('type') === 'proces_verbal' ? 'selected' : '' }}>Procès-verbal</option>
                <option value="convention" {{ request('type') === 'convention' ? 'selected' : '' }}>Convention de partenariat</option>
                <option value="rapport_annuel" {{ request('type') === 'rapport_annuel' ? 'selected' : '' }}>Rapport annuel</option>
                <option value="rapport_activite" {{ request('type') === 'rapport_activite' ? 'selected' : '' }}>Rapport d'activité</option>
                <option value="guide_procedure" {{ request('type') === 'guide_procedure' ? 'selected' : '' }}>Guide de procédure</option>
                <option value="politique_interne" {{ request('type') === 'politique_interne' ? 'selected' : '' }}>Politique interne</option>
                <option value="communique_officiel" {{ request('type') === 'communique_officiel' ? 'selected' : '' }}>Communiqué officiel</option>
                <option value="autre" {{ request('type') === 'autre' ? 'selected' : '' }}>Autre document</option>
            </select>

            <select name="public" class="documents-filtre-select">
                <option value="">Toutes les visibilités</option>
                <option value="oui" {{ request('public') === 'oui' ? 'selected' : '' }}>Public</option>
                <option value="non" {{ request('public') === 'non' ? 'selected' : '' }}>Privé</option>
            </select>

            <select name="annee" class="documents-filtre-select">
                <option value="">Toutes les années</option>
                @foreach($annees as $annee)
                    <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                @endforeach
            </select>
        </div>

        @if(request('recherche') || request('type') || request('public') || request('annee'))
            <a href="{{ route('admin.documents.index') }}" class="documents-reset-btn" title="Réinitialiser les filtres">
                <i class="fas fa-times"></i>
            </a>
        @endif

        <a href="{{ route('admin.documents.create') }}" class="documents-add-btn">
            <i class="fas fa-upload"></i> Importer un document
        </a>
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES DOCUMENTS --}}
    {{-- ============================================ --}}
    <div class="documents-table-card">
        @if($documents->count() > 0)
        <div class="table-responsive">
            <table class="table-documents">
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Version</th>
                        <th>Visibilité</th>
                        <th>Téléchargements</th>
                        <th style="width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $document)
                    <tr>
                        {{-- Document --}}
                        <td>
                            <div class="document-cell-titre">
                                <div class="document-cell-icon {{ $document->extension === 'pdf' ? 'pdf' : (in_array($document->extension, ['doc', 'docx']) ? 'doc' : (in_array($document->extension, ['xls', 'xlsx']) ? 'xls' : (in_array($document->extension, ['ppt', 'pptx']) ? 'ppt' : 'autre'))) }}">
                                    <i class="fas fa-{{ 
                                        $document->extension === 'pdf' ? 'file-pdf' : 
                                        (in_array($document->extension, ['doc', 'docx']) ? 'file-word' : 
                                        (in_array($document->extension, ['xls', 'xlsx']) ? 'file-excel' : 
                                        (in_array($document->extension, ['ppt', 'pptx']) ? 'file-powerpoint' : 'file-alt')))
                                    }}"></i>
                                </div>
                                <div class="document-cell-info">
                                    <div class="document-cell-nom">{{ Str::limit($document->titre, 55) }}</div>
                                    <div class="document-cell-meta">
                                        <span><i class="fas fa-weight-hanging"></i> {{ $document->tailleLisible() }}</span>
                                        <span><i class="fas fa-file"></i> {{ strtoupper($document->extension) }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Type --}}
                        <td>
                            <span class="document-type-badge document-type-{{ $document->type }}">
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
                                {{ Str::limit($document->typeLibelle(), 28) }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td>
                            <span style="font-size: 12px; white-space: nowrap;">
                                {{ $document->date_document ? $document->date_document->format('d/m/Y') : '—' }}
                            </span>
                        </td>

                        {{-- Version --}}
                        <td>
                            <span style="font-size: 12px; font-weight: 600; color: #1a1a1a;">
                                v{{ $document->version ?: '1.0' }}
                            </span>
                        </td>

                        {{-- Visibilité --}}
                        <td>
                            <span class="document-visibilite-badge document-visibilite-{{ $document->public ? 'public' : 'prive' }}">
                                <i class="fas fa-{{ $document->public ? 'globe' : 'lock' }}"></i>
                                {{ $document->public ? 'Public' : 'Privé' }}
                            </span>
                        </td>

                        {{-- Téléchargements --}}
                        <td>
                            <div class="document-telechargements">
                                <strong>{{ $document->telechargements }}</strong>
                                <br><i class="fas fa-download"></i>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="document-actions">
                                <a href="{{ route('admin.documents.show', $document) }}" class="document-action-btn view" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.documents.edit', $document) }}" class="document-action-btn edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.documents.download', $document) }}" class="document-action-btn download" title="Télécharger">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="{{ route('admin.documents.togglePublic', $document) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="document-action-btn visibility" 
                                            title="{{ $document->public ? 'Passer en privé' : 'Rendre public' }}">
                                        <i class="fas fa-{{ $document->public ? 'lock' : 'globe' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="document-action-btn delete btn-delete-document" 
                                            data-titre="{{ $document->titre }}" title="Supprimer">
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
        @if($documents->hasPages())
        <div class="documents-pagination">
            {{ $documents->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="documents-empty">
            <div class="documents-empty-icon"><i class="fas fa-folder-open"></i></div>
            <h4>Aucun document trouvé</h4>
            <p>
                @if(request('recherche') || request('type') || request('public') || request('annee'))
                    Aucun document ne correspond à vos critères de recherche.
                @else
                    La bibliothèque de documents est vide. Importez vos premiers documents officiels.
                @endif
            </p>
            @if(request('recherche') || request('type') || request('public') || request('annee'))
                <a href="{{ route('admin.documents.index') }}" class="btn btn-outline btn-sm">
                    <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
                </a>
            @else
                <a href="{{ route('admin.documents.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-upload"></i> Importer un document
                </a>
            @endif
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-documents.js') }}"></script>
@endsection