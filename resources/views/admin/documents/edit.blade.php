@extends('layouts.admin')

@section('title', 'Modifier le document')

@section('page_title', 'Modifier le document')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les métadonnées du document. Vous pouvez également remplacer le fichier si nécessaire.')

@php
    $breadcrumb = ['Documents officiels' => route('admin.documents.index'), 'Modifier' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-documents.css') }}">
@endsection

@section('content')

    <div class="document-form-card">

        {{-- En-tête du formulaire --}}
        <div class="document-form-header">
            <div class="document-form-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier le document</h2>
                <p>
                    Document : <strong>{{ $document->titre }}</strong> —
                    <span class="document-type-badge document-type-{{ $document->type }}" style="margin-left: 6px;">
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
                    <span class="document-visibilite-badge document-visibilite-{{ $document->public ? 'public' : 'prive' }}" style="margin-left: 6px;">
                        <i class="fas fa-{{ $document->public ? 'globe' : 'lock' }}"></i>
                        {{ $document->public ? 'Public' : 'Privé' }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('admin.documents.update', $document) }}" method="POST" id="documentForm">
            @csrf
            @method('PUT')

            {{-- ============================================ --}}
            {{-- SECTION 1 : INFORMATIONS --}}
            {{-- ============================================ --}}
            <div class="document-form-section">
                <div class="document-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations du document
                </div>

                <div class="document-form-grid">
                    {{-- Titre --}}
                    <div class="document-form-group">
                        <label for="titre" class="document-form-label">
                            Titre du document <span class="required">*</span>
                        </label>
                        <input type="text" name="titre" id="titre" 
                               class="document-form-input @error('titre') is-invalid @enderror" 
                               value="{{ old('titre', $document->titre) }}" 
                               placeholder="Ex: Statuts de l'organisation AHD-RDC" 
                               maxlength="255" required autofocus>
                        @error('titre')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div class="document-form-group">
                        <label for="type" class="document-form-label">
                            Type de document <span class="required">*</span>
                        </label>
                        <select name="type" id="type" class="document-form-select @error('type') is-invalid @enderror" required>
                            <option value="">-- Sélectionnez un type --</option>
                            <option value="statuts" {{ old('type', $document->type) === 'statuts' ? 'selected' : '' }}>Statuts</option>
                            <option value="roi" {{ old('type', $document->type) === 'roi' ? 'selected' : '' }}>Règlement d'Ordre Intérieur</option>
                            <option value="proces_verbal" {{ old('type', $document->type) === 'proces_verbal' ? 'selected' : '' }}>Procès-verbal</option>
                            <option value="convention" {{ old('type', $document->type) === 'convention' ? 'selected' : '' }}>Convention de partenariat</option>
                            <option value="rapport_annuel" {{ old('type', $document->type) === 'rapport_annuel' ? 'selected' : '' }}>Rapport annuel</option>
                            <option value="rapport_activite" {{ old('type', $document->type) === 'rapport_activite' ? 'selected' : '' }}>Rapport d'activité</option>
                            <option value="guide_procedure" {{ old('type', $document->type) === 'guide_procedure' ? 'selected' : '' }}>Guide de procédure</option>
                            <option value="politique_interne" {{ old('type', $document->type) === 'politique_interne' ? 'selected' : '' }}>Politique interne</option>
                            <option value="communique_officiel" {{ old('type', $document->type) === 'communique_officiel' ? 'selected' : '' }}>Communiqué officiel</option>
                            <option value="autre" {{ old('type', $document->type) === 'autre' ? 'selected' : '' }}>Autre document</option>
                        </select>
                        @error('type')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Catégorie --}}
                    <div class="document-form-group">
                        <label for="categorie" class="document-form-label">
                            Catégorie <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel)</small>
                        </label>
                        <input type="text" name="categorie" id="categorie" 
                               class="document-form-input @error('categorie') is-invalid @enderror" 
                               value="{{ old('categorie', $document->categorie) }}" 
                               placeholder="Ex: Gouvernance, Finances, Juridique..." 
                               maxlength="100">
                        @error('categorie')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Version --}}
                    <div class="document-form-group">
                        <label for="version" class="document-form-label">
                            Version <small style="font-weight: 400; color: var(--color-text-muted);">(ex: 1.0, 2.1)</small>
                        </label>
                        <input type="text" name="version" id="version" 
                               class="document-form-input @error('version') is-invalid @enderror" 
                               value="{{ old('version', $document->version) }}" 
                               placeholder="1.0" 
                               maxlength="50">
                        @error('version')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : DATES & SIGNATAIRES --}}
            {{-- ============================================ --}}
            <div class="document-form-section">
                <div class="document-form-section-title">
                    <i class="fas fa-calendar-alt"></i> Dates & Signataires
                </div>

                <div class="document-form-grid">
                    {{-- Date du document --}}
                    <div class="document-form-group">
                        <label for="date_document" class="document-form-label">
                            Date du document
                        </label>
                        <input type="date" name="date_document" id="date_document" 
                               class="document-form-input @error('date_document') is-invalid @enderror" 
                               value="{{ old('date_document', $document->date_document ? $document->date_document->format('Y-m-d') : '') }}">
                        @error('date_document')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Date de signature --}}
                    <div class="document-form-group">
                        <label for="date_signature" class="document-form-label">
                            Date de signature <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel)</small>
                        </label>
                        <input type="date" name="date_signature" id="date_signature" 
                               class="document-form-input @error('date_signature') is-invalid @enderror" 
                               value="{{ old('date_signature', $document->date_signature ? $document->date_signature->format('Y-m-d') : '') }}">
                        @error('date_signature')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Signataires --}}
                    <div class="document-form-group document-form-full">
                        <label for="signataires" class="document-form-label">
                            Signataires <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel)</small>
                        </label>
                        <input type="text" name="signataires" id="signataires" 
                               class="document-form-input @error('signataires') is-invalid @enderror" 
                               value="{{ old('signataires', $document->signataires) }}" 
                               placeholder="Ex: Dr AKSANTI DUNIA Steeng, MAJINA DUNIA Judith" 
                               maxlength="500">
                        @error('signataires')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3 : DESCRIPTION & VISIBILITÉ --}}
            {{-- ============================================ --}}
            <div class="document-form-section">
                <div class="document-form-section-title">
                    <i class="fas fa-cog"></i> Description & Visibilité
                </div>

                <div class="document-form-grid">
                    {{-- Description --}}
                    <div class="document-form-group document-form-full">
                        <label for="description" class="document-form-label">
                            Description <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel)</small>
                        </label>
                        <textarea name="description" id="description" 
                                  class="document-form-textarea @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Décrivez brièvement le contenu et l'objet de ce document..."
                                  maxlength="2000">{{ old('description', $document->description) }}</textarea>
                        <small style="font-size: 11px; color: var(--color-text-muted); margin-top: 4px; text-align: right; display: block;">
                            <span id="descriptionCounter">0</span>/2000 caractères
                        </small>
                        @error('description')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Visibilité --}}
                    <div class="document-form-group document-form-full">
                        <label class="document-form-label">
                            Visibilité
                        </label>
                        <div style="display: flex; gap: 16px; margin-top: 4px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 500; color: #1a1a1a; padding: 10px 18px; border: 2px solid #e0e4e8; border-radius: 8px; transition: all 0.2s ease;">
                                <input type="radio" name="public" value="1" {{ old('public', $document->public) == '1' ? 'checked' : '' }} style="accent-color: var(--color-primary);">
                                <i class="fas fa-globe" style="color: var(--color-success);"></i> Public
                                <small style="color: var(--color-text-muted);">Visible sur le site</small>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 500; color: #1a1a1a; padding: 10px 18px; border: 2px solid #e0e4e8; border-radius: 8px; transition: all 0.2s ease;">
                                <input type="radio" name="public" value="0" {{ old('public', $document->public) == '0' ? 'checked' : '' }} style="accent-color: var(--color-primary);">
                                <i class="fas fa-lock" style="color: #888;"></i> Privé
                                <small style="color: var(--color-text-muted);">Usage interne uniquement</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4 : STATISTIQUES --}}
            {{-- ============================================ --}}
            <div class="document-form-section">
                <div class="document-form-section-title">
                    <i class="fas fa-chart-bar"></i> Statistiques du document
                </div>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;">
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Format</div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1a1a1a; text-transform: uppercase;">
                            <i class="fas fa-file" style="color: var(--color-accent); margin-right: 4px;"></i>
                            {{ $document->extension }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Taille</div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1a1a1a;">
                            <i class="fas fa-weight-hanging" style="color: #e67e22; margin-right: 4px;"></i>
                            {{ $document->tailleLisible() }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Téléchargements</div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1a1a1a;">
                            <i class="fas fa-download" style="color: var(--color-success); margin-right: 4px;"></i>
                            {{ $document->telechargements }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Importé le</div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1a1a1a;">
                            <i class="fas fa-calendar-plus" style="color: var(--color-primary); margin-right: 4px;"></i>
                            {{ $document->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="document-form-actions">
                <a href="{{ route('admin.documents.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.documents.show', $document) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> Voir les détails
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- ============================================ --}}
    {{-- REMPLACER LE FICHIER --}}
    {{-- ============================================ --}}
    <div class="document-form-card" style="margin-top: 24px;">
        <div class="document-form-header" style="border-bottom-color: var(--color-error-light);">
            <div class="document-form-header-icon" style="background: var(--color-accent);">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div>
                <h2>Remplacer le fichier</h2>
                <p>Vous pouvez remplacer le fichier actuel par une nouvelle version. L'ancien fichier sera définitivement supprimé.</p>
            </div>
        </div>

        <div style="padding: 8px 0;">
            {{-- Fichier actuel --}}
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: var(--color-bg-light); border-radius: 10px; margin-bottom: 16px;">
                <div class="document-upload-file-icon {{ $document->extension === 'pdf' ? 'pdf' : (in_array($document->extension, ['doc', 'docx']) ? 'doc' : (in_array($document->extension, ['xls', 'xlsx']) ? 'xls' : (in_array($document->extension, ['ppt', 'pptx']) ? 'ppt' : 'autre'))) }}" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                    <i class="fas fa-{{ 
                        $document->extension === 'pdf' ? 'file-pdf' : 
                        (in_array($document->extension, ['doc', 'docx']) ? 'file-word' : 
                        (in_array($document->extension, ['xls', 'xlsx']) ? 'file-excel' : 
                        (in_array($document->extension, ['ppt', 'pptx']) ? 'file-powerpoint' : 'file-alt')))
                    }}"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 13px; font-weight: 600; color: #1a1a1a;">Fichier actuel</div>
                    <div style="font-size: 12px; color: var(--color-text-muted);">
                        {{ strtoupper($document->extension) }} — {{ $document->tailleLisible() }} — 
                        <a href="{{ route('admin.documents.download', $document) }}" style="color: var(--color-primary); text-decoration: underline;">
                            <i class="fas fa-download"></i> Télécharger
                        </a>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.documents.replaceFile', $document) }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                @csrf
                @method('PUT')
                <input type="file" name="fichier" id="replaceFileInput" 
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv"
                       style="font-size: 13px; flex: 1; min-width: 200px;">
                <button type="submit" id="replaceFileBtn" class="btn btn-accent btn-sm" disabled>
                    <i class="fas fa-sync-alt"></i> Remplacer le fichier
                </button>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Compteur de caractères pour la description
            const description = document.getElementById('description');
            const counter = document.getElementById('descriptionCounter');
            if (description && counter) {
                counter.textContent = description.value.length;
                description.addEventListener('input', function () {
                    counter.textContent = this.value.length;
                });
            }

            // Highlight du radio button sélectionné
            const radioInputs = document.querySelectorAll('input[name="public"]');
            function updateRadioStyle() {
                const allLabels = document.querySelectorAll('.document-form-group input[name="public"]');
                allLabels.forEach(radio => {
                    const label = radio.closest('label');
                    label.style.borderColor = '#e0e4e8';
                    label.style.background = '#fff';
                    if (radio.checked) {
                        label.style.borderColor = 'var(--color-primary)';
                        label.style.background = 'var(--color-primary-lighter)';
                    }
                });
            }
            radioInputs.forEach(radio => {
                radio.addEventListener('change', updateRadioStyle);
            });
            updateRadioStyle();

            // Activer le bouton "Remplacer" seulement si un fichier est sélectionné
            const replaceFileInput = document.getElementById('replaceFileInput');
            const replaceFileBtn = document.getElementById('replaceFileBtn');
            if (replaceFileInput && replaceFileBtn) {
                replaceFileInput.addEventListener('change', function () {
                    replaceFileBtn.disabled = !this.files || this.files.length === 0;
                });
            }
        });
    </script>
    <script src="{{ asset('assets/js/admin-documents.js') }}"></script>
@endsection