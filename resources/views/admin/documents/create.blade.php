@extends('layouts.admin')

@section('title', 'Importer un document')

@section('page_title', 'Importer un document')
@section('page_icon', 'upload')
@section('page_subtitle', 'Importez un document officiel dans la bibliothèque. Formats acceptés : PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV — Max 20 Mo.')

@php
    $breadcrumb = ['Documents officiels' => route('admin.documents.index'), 'Importer' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-documents.css') }}">
@endsection

@section('content')

    <div class="document-form-card">

        {{-- En-tête du formulaire --}}
        <div class="document-form-header">
            <div class="document-form-header-icon">
                <i class="fas fa-upload"></i>
            </div>
            <div>
                <h2>Importer un document</h2>
                <p>Remplissez les informations ci-dessous et sélectionnez le fichier à importer. Les champs marqués d'un astérisque (*) sont obligatoires.</p>
            </div>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" id="documentForm">
            @csrf

            {{-- ============================================ --}}
            {{-- SECTION 1 : FICHIER --}}
            {{-- ============================================ --}}
            <div class="document-form-section">
                <div class="document-form-section-title">
                    <i class="fas fa-file-upload"></i> Fichier
                </div>

                <div class="document-form-group">
                    <label class="document-form-label">
                        Sélectionnez le document <span class="required">*</span>
                    </label>

                    {{-- Zone d'upload --}}
                    <div id="uploadZone" class="document-upload-zone @error('fichier') is-invalid @enderror">
                        <div class="document-upload-icon" id="uploadIconMain">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="document-upload-text" id="uploadTextMain">Cliquez pour sélectionner un fichier</div>
                        <div class="document-upload-subtext" id="uploadSubtextMain">ou glissez-déposez votre document ici</div>
                        <div class="document-upload-formats">
                            <span>PDF</span>
                            <span>DOC</span>
                            <span>DOCX</span>
                            <span>XLS</span>
                            <span>XLSX</span>
                            <span>PPT</span>
                            <span>PPTX</span>
                            <span>TXT</span>
                            <span>CSV</span>
                        </div>
                    </div>

                    {{-- Infos du fichier sélectionné --}}
                    <div id="uploadFileInfo" class="document-upload-file-info" style="display: none;">
                        <div id="uploadFileIcon" class="document-upload-file-icon pdf">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div id="uploadFileName" class="document-upload-file-name"></div>
                            <div id="uploadFileSize" class="document-upload-file-size"></div>
                        </div>
                        <button type="button" id="uploadFileRemove" class="document-upload-file-remove" title="Retirer le fichier">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <input type="file" name="fichier" id="fichier" 
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv" 
                           style="display: none;">
                    @error('fichier')
                        <span class="document-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : INFORMATIONS --}}
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
                               value="{{ old('titre') }}" 
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
                            <option value="statuts" {{ old('type') === 'statuts' ? 'selected' : '' }}>Statuts</option>
                            <option value="roi" {{ old('type') === 'roi' ? 'selected' : '' }}>Règlement d'Ordre Intérieur</option>
                            <option value="proces_verbal" {{ old('type') === 'proces_verbal' ? 'selected' : '' }}>Procès-verbal</option>
                            <option value="convention" {{ old('type') === 'convention' ? 'selected' : '' }}>Convention de partenariat</option>
                            <option value="rapport_annuel" {{ old('type') === 'rapport_annuel' ? 'selected' : '' }}>Rapport annuel</option>
                            <option value="rapport_activite" {{ old('type') === 'rapport_activite' ? 'selected' : '' }}>Rapport d'activité</option>
                            <option value="guide_procedure" {{ old('type') === 'guide_procedure' ? 'selected' : '' }}>Guide de procédure</option>
                            <option value="politique_interne" {{ old('type') === 'politique_interne' ? 'selected' : '' }}>Politique interne</option>
                            <option value="communique_officiel" {{ old('type') === 'communique_officiel' ? 'selected' : '' }}>Communiqué officiel</option>
                            <option value="autre" {{ old('type') === 'autre' ? 'selected' : '' }}>Autre document</option>
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
                            Catégorie <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel — pour organiser vos documents)</small>
                        </label>
                        <input type="text" name="categorie" id="categorie" 
                               class="document-form-input @error('categorie') is-invalid @enderror" 
                               value="{{ old('categorie') }}" 
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
                               value="{{ old('version', '1.0') }}" 
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
            {{-- SECTION 3 : DATES & SIGNATAIRES --}}
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
                               value="{{ old('date_document', date('Y-m-d')) }}">
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
                               value="{{ old('date_signature') }}">
                        @error('date_signature')
                            <span class="document-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Signataires --}}
                    <div class="document-form-group document-form-full">
                        <label for="signataires" class="document-form-label">
                            Signataires <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel — noms des signataires)</small>
                        </label>
                        <input type="text" name="signataires" id="signataires" 
                               class="document-form-input @error('signataires') is-invalid @enderror" 
                               value="{{ old('signataires') }}" 
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
            {{-- SECTION 4 : DESCRIPTION & VISIBILITÉ --}}
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
                                  maxlength="2000">{{ old('description') }}</textarea>
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
                                <input type="radio" name="public" value="1" {{ old('public', '1') == '1' ? 'checked' : '' }} style="accent-color: var(--color-primary);">
                                <i class="fas fa-globe" style="color: var(--color-success);"></i> Public
                                <small style="color: var(--color-text-muted);">Visible sur le site</small>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 500; color: #1a1a1a; padding: 10px 18px; border: 2px solid #e0e4e8; border-radius: 8px; transition: all 0.2s ease;">
                                <input type="radio" name="public" value="0" {{ old('public') === '0' ? 'checked' : '' }} style="accent-color: var(--color-primary);">
                                <i class="fas fa-lock" style="color: #888;"></i> Privé
                                <small style="color: var(--color-text-muted);">Usage interne uniquement</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- CONSEILS --}}
            {{-- ============================================ --}}
            <div style="margin-top: 24px; padding: 18px 20px; background: var(--color-primary-lighter); border-radius: 12px; display: flex; gap: 14px; align-items: flex-start;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div style="font-size: 13px; color: var(--color-text-secondary); line-height: 1.6;">
                    <strong style="color: #1a1a1a;">Conseils pour vos documents :</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 18px; list-style: disc;">
                        <li>Privilégiez le format <strong>PDF</strong> pour les documents officiels destinés au public.</li>
                        <li>Nommez vos fichiers de manière descriptive avant importation (ex: <em>statuts-ahd-rdc-2024.pdf</em>).</li>
                        <li>Indiquez la <strong>version</strong> pour suivre l'évolution des documents.</li>
                        <li>Les documents <strong>publics</strong> apparaîtront sur la page Transparence du site.</li>
                        <li>Les documents <strong>privés</strong> sont réservés à l'administration interne.</li>
                    </ul>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="document-form-actions">
                <a href="{{ route('admin.documents.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Importer le document
                </button>
            </div>

        </form>
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
        });
    </script>
    <script src="{{ asset('assets/js/admin-documents.js') }}"></script>
@endsection