@extends('layouts.admin')

@section('title', 'Ajouter des médias')

@section('page_title', 'Ajouter des médias')
@section('page_icon', 'cloud-upload-alt')
@section('page_subtitle', 'Importez des photos et vidéos dans la galerie. Glissez-déposez vos fichiers ou cliquez pour les sélectionner.')

@php
    $breadcrumb = ['Galerie Médias' => route('admin.medias.index'), 'Ajouter' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-medias.css') }}">
@endsection

@section('content')

    <div class="media-form-card" style="max-width: 900px;">

        {{-- En-tête --}}
        <div class="media-form-header">
            <div class="media-form-header-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div>
                <h2>Importer des médias</h2>
                <p>Sélectionnez jusqu'à 20 fichiers. Formats acceptés : JPG, PNG, WebP, GIF (max 10 Mo), MP4, WebM, OGG (max 50 Mo).</p>
            </div>
        </div>

        {{-- Formulaire --}}
        <form id="uploadForm" action="{{ route('admin.medias.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- ============================================ --}}
            {{-- ZONE DE DRAG & DROP --}}
            {{-- ============================================ --}}
            <div id="uploadZone" class="upload-zone">
                <div class="upload-zone-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3>Glissez-déposez vos fichiers ici</h3>
                <p>ou cliquez pour parcourir votre ordinateur</p>
                <div class="upload-zone-formats">
                    <span><i class="fas fa-check-circle"></i> JPG</span>
                    <span><i class="fas fa-check-circle"></i> PNG</span>
                    <span><i class="fas fa-check-circle"></i> WebP</span>
                    <span><i class="fas fa-check-circle"></i> GIF</span>
                    <span><i class="fas fa-check-circle"></i> MP4</span>
                    <span><i class="fas fa-check-circle"></i> WebM</span>
                    <span><i class="fas fa-check-circle"></i> OGG</span>
                </div>
                <button type="button" class="upload-zone-btn" onclick="document.getElementById('fileInput').click();">
                    <i class="fas fa-folder-open"></i> Parcourir les fichiers
                </button>
                <input type="file" name="fichiers[]" id="fileInput" multiple 
                    accept="image/jpeg,image/jpg,image/png,image/webp,image/gif,video/mp4,video/webm,video/ogg" 
                    style="display: none;">
            </div>

            {{-- ============================================ --}}
            {{-- APERÇU DES FICHIERS SÉLECTIONNÉS --}}
            {{-- ============================================ --}}
            <div id="uploadPreviewGrid" class="upload-preview-grid"></div>

            {{-- ============================================ --}}
            {{-- BARRE DE PROGRESSION --}}
            {{-- ============================================ --}}
            <div id="uploadProgressContainer" class="upload-progress-container">
                <div class="upload-progress-bar">
                    <div id="uploadProgressFill" class="upload-progress-fill" style="width: 0%;"></div>
                </div>
                <div id="uploadProgressText" class="upload-progress-text">Préparation du téléversement...</div>
            </div>

            {{-- ============================================ --}}
            {{-- PARAMÈTRES D'IMPORTATION --}}
            {{-- ============================================ --}}
            <div style="margin-top: 32px; padding-top: 24px; border-top: 2px solid #f0f0f0;">
                <div style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-cog" style="color: #fff; background: var(--color-primary); width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px;"></i>
                    Paramètres d'importation
                </div>

                <div class="media-form-grid">
                    {{-- Projet lié --}}
                    <div class="media-form-group">
                        <label for="projet_id" class="media-form-label">
                            <i class="fas fa-project-diagram" style="color: var(--color-primary); margin-right: 4px;"></i>
                            Projet lié
                            <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel)</small>
                        </label>
                        <select name="projet_id" id="projet_id" class="media-form-select">
                            <option value="">-- Aucun projet --</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                    {{ Str::limit($projet->titre, 55) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Album --}}
                    <div class="media-form-group">
                        <label for="album" class="media-form-label">
                            <i class="fas fa-folder" style="color: #e67e22; margin-right: 4px;"></i>
                            Album
                            <small style="font-weight: 400; color: var(--color-text-muted);">(optionnel — créez ou choisissez un dossier)</small>
                        </label>
                        <input type="text" name="album" id="album" 
                               class="media-form-input" 
                               list="albumsList"
                               value="{{ old('album') }}" 
                               placeholder="Ex: Projet Wash Minova, Cérémonie 2024..."
                               maxlength="255"
                               autocomplete="off">
                        <datalist id="albumsList">
                            @foreach($albums as $album)
                                <option value="{{ $album }}">
                            @endforeach
                        </datalist>
                    </div>

                    {{-- Visibilité --}}
                    <div class="media-form-group media-form-full">
                        <label class="media-form-label">
                            <i class="fas fa-globe" style="color: var(--color-success); margin-right: 4px;"></i>
                            Visibilité
                        </label>
                        <div style="display: flex; gap: 16px; margin-top: 4px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 500; color: #1a1a1a; padding: 10px 18px; border: 2px solid #e0e4e8; border-radius: 8px; transition: all 0.2s ease;">
                                <input type="radio" name="publier" value="1" {{ old('publier', '1') == '1' ? 'checked' : '' }} style="accent-color: var(--color-primary);">
                                <i class="fas fa-globe" style="color: var(--color-success);"></i> Public
                                <small style="color: var(--color-text-muted);">Visible sur le site</small>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 500; color: #1a1a1a; padding: 10px 18px; border: 2px solid #e0e4e8; border-radius: 8px; transition: all 0.2s ease;">
                                <input type="radio" name="publier" value="0" {{ old('publier') === '0' ? 'checked' : '' }} style="accent-color: var(--color-primary);">
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
                    <strong style="color: #1a1a1a;">Conseils pour vos médias :</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 18px; list-style: disc;">
                        <li>Privilégiez les images au format <strong>WebP</strong> pour un meilleur compromis qualité/poids.</li>
                        <li>Redimensionnez vos photos à <strong>1200×800 px</strong> maximum avant importation.</li>
                        <li>Nommez vos fichiers de manière descriptive (ex: <em>distribution-eau-minova-2024.jpg</em>).</li>
                        <li>Les miniatures sont générées automatiquement pour les images.</li>
                        <li>Organisez vos médias par <strong>albums</strong> pour les retrouver facilement.</li>
                    </ul>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="media-form-actions">
                <a href="{{ route('admin.medias.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Retour à la galerie
                </a>
                <button type="submit" id="submitUploadBtn" class="btn btn-primary" disabled>
                    <i class="fas fa-cloud-upload-alt"></i> Aucun fichier sélectionné
                </button>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script>
        // Initialiser le submit button
        document.addEventListener('DOMContentLoaded', function () {
            const submitBtn = document.getElementById('submitUploadBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Aucun fichier sélectionné';
            }

            // Highlight du radio button sélectionné
            const radioLabels = document.querySelectorAll('.media-form-group input[type="radio"]');
            radioLabels.forEach(radio => {
                radio.addEventListener('change', function () {
                    const allLabels = this.closest('div').querySelectorAll('label');
                    allLabels.forEach(label => {
                        label.style.borderColor = '#e0e4e8';
                        label.style.background = '#fff';
                    });
                    if (this.checked) {
                        const label = this.closest('label');
                        label.style.borderColor = 'var(--color-primary)';
                        label.style.background = 'var(--color-primary-lighter)';
                    }
                });

                // Appliquer l'état initial
                if (radio.checked) {
                    const label = radio.closest('label');
                    label.style.borderColor = 'var(--color-primary)';
                    label.style.background = 'var(--color-primary-lighter)';
                }
            });
        });
    </script>
    <script src="{{ asset('assets/js/admin-medias.js') }}"></script>
@endsection