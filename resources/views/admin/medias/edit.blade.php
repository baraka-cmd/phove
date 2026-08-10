@extends('layouts.admin')

@section('title', 'Modifier le média')

@section('page_title', 'Modifier le média')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les métadonnées du média : titre, description, album, projet associé.')

@php
    $breadcrumb = ['Galerie Médias' => route('admin.medias.index'), 'Modifier' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-medias.css') }}">
@endsection

@section('content')

    <div class="media-form-card">

        {{-- En-tête --}}
        <div class="media-form-header">
            <div class="media-form-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier le média</h2>
                <p>
                    Fichier : <strong>{{ $media->titre ?: 'Sans titre' }}</strong> — 
                    <span class="media-card-type-badge {{ $media->type }}" style="position: static; display: inline-flex; margin-left: 6px;">
                        <i class="fas fa-{{ $media->type === 'image' ? 'image' : 'video' }}"></i>
                        {{ $media->type === 'image' ? 'Image' : 'Vidéo' }}
                    </span>
                    <span class="article-statut-badge article-statut-{{ $media->publier ? 'publie' : 'brouillon' }}" style="margin-left: 6px;">
                        <i class="fas fa-{{ $media->publier ? 'globe' : 'lock' }}"></i>
                        {{ $media->publier ? 'Public' : 'Privé' }}
                    </span>
                </p>
            </div>
        </div>

        <form action="{{ route('admin.medias.update', $media) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- ============================================ --}}
            {{-- PRÉVISUALISATION --}}
            {{-- ============================================ --}}
            <div class="media-form-preview">
                @if($media->type === 'image')
                    <img src="{{ $media->url() }}" alt="{{ $media->titre }}">
                @elseif($media->type === 'video')
                    <video controls>
                        <source src="{{ $media->url() }}" type="video/mp4">
                        Votre navigateur ne supporte pas la lecture de vidéos.
                    </video>
                @else
                    <i class="fas fa-file" style="font-size: 4rem; color: #ccc;"></i>
                @endif
            </div>

            {{-- ============================================ --}}
            {{-- INFORMATIONS PRINCIPALES --}}
            {{-- ============================================ --}}
            <div style="margin-bottom: 28px;">
                <div style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-info-circle" style="color: #fff; background: var(--color-primary); width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px;"></i>
                    Informations principales
                </div>

                <div class="media-form-grid">
                    {{-- Titre --}}
                    <div class="media-form-group">
                        <label for="titre" class="media-form-label">
                            Titre <span class="required">*</span>
                        </label>
                        <input type="text" name="titre" id="titre" 
                               class="media-form-input @error('titre') is-invalid @enderror" 
                               value="{{ old('titre', $media->titre) }}" 
                               placeholder="Ex: Distribution d'eau potable à Minova" 
                               maxlength="255" required autofocus>
                        @error('titre')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Auteur --}}
                    <div class="media-form-group">
                        <label for="auteur" class="media-form-label">
                            Auteur / Photographe
                        </label>
                        <input type="text" name="auteur" id="auteur" 
                               class="media-form-input @error('auteur') is-invalid @enderror" 
                               value="{{ old('auteur', $media->auteur) }}" 
                               placeholder="Ex: Dr AKSANTI DUNIA Steeng" 
                               maxlength="255">
                        @error('auteur')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="media-form-group media-form-full">
                        <label for="description" class="media-form-label">
                            Description / Légende
                        </label>
                        <textarea name="description" id="description" 
                                  class="media-form-textarea @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Une courte description du média pour le contexte..."
                                  maxlength="1000">{{ old('description', $media->description) }}</textarea>
                        <small style="font-size: 11px; color: var(--color-text-muted); margin-top: 4px; text-align: right; display: block;">
                            <span id="descriptionCounter">{{ strlen(old('description', $media->description ?? '')) }}</span>/1000 caractères
                        </small>
                        @error('description')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ORGANISATION --}}
            {{-- ============================================ --}}
            <div style="margin-bottom: 28px;">
                <div style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-folder-tree" style="color: #fff; background: #e67e22; width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px;"></i>
                    Organisation
                </div>

                <div class="media-form-grid">
                    {{-- Album --}}
                    <div class="media-form-group">
                        <label for="album" class="media-form-label">
                            <i class="fas fa-folder" style="color: #e67e22;"></i> Album
                        </label>
                        <input type="text" name="album" id="album" 
                               class="media-form-input @error('album') is-invalid @enderror" 
                               list="albumsList"
                               value="{{ old('album', $media->album) }}" 
                               placeholder="Ex: Projet Wash Minova, Cérémonie 2024..."
                               maxlength="255"
                               autocomplete="off">
                        <datalist id="albumsList">
                            @foreach($albums as $album)
                                <option value="{{ $album }}">
                            @endforeach
                        </datalist>
                        @error('album')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Projet lié --}}
                    <div class="media-form-group">
                        <label for="projet_id" class="media-form-label">
                            <i class="fas fa-project-diagram" style="color: var(--color-primary);"></i> Projet lié
                        </label>
                        <select name="projet_id" id="projet_id" class="media-form-select @error('projet_id') is-invalid @enderror">
                            <option value="">-- Aucun projet --</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id', $media->projet_id) == $projet->id ? 'selected' : '' }}>
                                    {{ Str::limit($projet->titre, 55) }}
                                </option>
                            @endforeach
                        </select>
                        @error('projet_id')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Ordre --}}
                    <div class="media-form-group">
                        <label for="ordre" class="media-form-label">
                            <i class="fas fa-sort-numeric-down" style="color: #8e44ad;"></i> Ordre d'affichage
                        </label>
                        <input type="number" name="ordre" id="ordre" 
                               class="media-form-input @error('ordre') is-invalid @enderror" 
                               value="{{ old('ordre', $media->ordre) }}" 
                               placeholder="0" 
                               min="0" 
                               max="9999">
                        @error('ordre')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Visibilité --}}
                    <div class="media-form-group">
                        <label for="publier" class="media-form-label">
                            <i class="fas fa-globe" style="color: var(--color-success);"></i> Visibilité
                        </label>
                        <select name="publier" id="publier" class="media-form-select @error('publier') is-invalid @enderror">
                            <option value="1" {{ old('publier', $media->publier) == '1' ? 'selected' : '' }}>Public — Visible sur le site</option>
                            <option value="0" {{ old('publier', $media->publier) == '0' ? 'selected' : '' }}>Privé — Usage interne uniquement</option>
                        </select>
                        @error('publier')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- LIEU ET DATE --}}
            {{-- ============================================ --}}
            <div style="margin-bottom: 28px;">
                <div style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: #fff; background: var(--color-accent); width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px;"></i>
                    Lieu et date de prise
                </div>

                <div class="media-form-grid">
                    {{-- Lieu --}}
                    <div class="media-form-group">
                        <label for="lieu" class="media-form-label">
                            <i class="fas fa-map-pin" style="color: var(--color-accent);"></i> Lieu
                        </label>
                        <input type="text" name="lieu" id="lieu" 
                               class="media-form-input @error('lieu') is-invalid @enderror" 
                               value="{{ old('lieu', $media->lieu) }}" 
                               placeholder="Ex: Minova, Territoire de Kalehe, Sud-Kivu"
                               maxlength="255">
                        @error('lieu')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Date de prise --}}
                    <div class="media-form-group">
                        <label for="date_prise" class="media-form-label">
                            <i class="fas fa-calendar-alt" style="color: var(--color-info);"></i> Date de prise
                        </label>
                        <input type="date" name="date_prise" id="date_prise" 
                               class="media-form-input @error('date_prise') is-invalid @enderror" 
                               value="{{ old('date_prise', $media->date_prise ? $media->date_prise->format('Y-m-d') : '') }}">
                        @error('date_prise')
                            <span class="media-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- STATISTIQUES DU FICHIER --}}
            {{-- ============================================ --}}
            <div style="margin-bottom: 28px;">
                <div style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-chart-bar" style="color: #fff; background: var(--color-info); width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px;"></i>
                    Informations techniques
                </div>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;">
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Type</div>
                        <div style="font-weight: 700; font-size: 1rem; color: #1a1a1a;">
                            <i class="fas fa-{{ $media->type === 'image' ? 'image' : 'video' }}" style="color: var(--color-primary); margin-right: 4px;"></i>
                            {{ $media->type === 'image' ? 'Image' : 'Vidéo' }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Taille</div>
                        <div style="font-weight: 700; font-size: 1rem; color: #1a1a1a;">
                            <i class="fas fa-weight-hanging" style="color: #e67e22; margin-right: 4px;"></i>
                            {{ $media->taille > 1048576 ? round($media->taille / 1048576, 1) . ' Mo' : round($media->taille / 1024, 1) . ' Ko' }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Format</div>
                        <div style="font-weight: 700; font-size: 1rem; color: #1a1a1a; text-transform: uppercase;">
                            <i class="fas fa-file" style="color: var(--color-info); margin-right: 4px;"></i>
                            {{ strtoupper(pathinfo($media->fichier, PATHINFO_EXTENSION)) }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 14px 16px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Ajouté le</div>
                        <div style="font-weight: 700; font-size: 1rem; color: #1a1a1a;">
                            <i class="fas fa-calendar-plus" style="color: var(--color-success); margin-right: 4px;"></i>
                            {{ $media->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- REMPLACER LE FICHIER --}}
            {{-- ============================================ --}}
            <div style="margin-bottom: 28px; padding: 20px 24px; background: #fffbfb; border: 2px dashed var(--color-error-light); border-radius: 12px;">
                <div style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-exchange-alt" style="color: var(--color-accent);"></i> Remplacer le fichier
                </div>
                <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 14px;">
                    Vous pouvez remplacer ce fichier par un nouveau. L'ancien fichier sera définitivement supprimé.
                </p>
                <form action="{{ route('admin.medias.replaceFile', $media) }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    @csrf
                    @method('PUT')
                    <input type="file" name="fichier" id="replaceFileInput" 
                           accept="image/jpeg,image/jpg,image/png,image/webp,image/gif,video/mp4,video/webm,video/ogg"
                           style="font-size: 13px;">
                    <button type="submit" class="btn btn-accent btn-sm" id="replaceFileBtn" disabled>
                        <i class="fas fa-sync-alt"></i> Remplacer
                    </button>
                </form>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="media-form-actions">
                <a href="{{ route('admin.medias.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Retour à la galerie
                </a>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.medias.show', $media) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> Voir les détails
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
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
    <script src="{{ asset('assets/js/admin-medias.js') }}"></script>
@endsection