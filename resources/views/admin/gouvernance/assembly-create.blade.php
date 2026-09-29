@extends('layouts.admin')

@section('title', 'Nouvelle Assemblée Générale | PHOVE')

@section('page_title', 'Nouvelle Assemblée Générale')
@section('page_icon', 'users-cog')
@section('page_subtitle', 'Enregistrez une nouvelle Assemblée Générale (ordinaire ou extraordinaire).')

@php
    $breadcrumb = [
        ['label' => 'Gouvernance', 'url' => route('admin.gouvernance.index')],
        ['label' => 'Assemblées Générales', 'url' => route('admin.gouvernance.assemblies')],
        'Nouvelle AG',
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance-assembly-form.css') }}">
@endsection

@section('content')

    <div class="assembly-form-card">

        {{-- ============================================ --}}
        {{-- EN-TÊTE DE LA CARTE --}}
        {{-- ============================================ --}}
        <div class="assembly-form-header">
            <div class="assembly-form-header-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="assembly-form-header-text">
                <h2>Nouvelle Assemblée Générale</h2>
                <p>Remplissez les informations relatives à l'Assemblée Générale.</p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- ALERTE D'ERREURS GLOBALES --}}
        {{-- ============================================ --}}
        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Veuillez corriger les erreurs ci-dessous.</span>
            </div>
        @endif

        <form action="{{ route('admin.gouvernance.assemblies.store') }}"
              method="POST"
              enctype="multipart/form-data"
              novalidate>
            @csrf

            {{-- ============================================ --}}
            {{-- SECTION 1 : INFORMATIONS GÉNÉRALES --}}
            {{-- ============================================ --}}
            <div class="assembly-form-section">
                <h4 class="assembly-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations générales
                </h4>

                <div class="assembly-form-grid">

                    {{-- Type d'AG --}}
                    <div class="assembly-form-group">
                        <label for="type" class="assembly-form-label">
                            Type d'AG <span class="required">*</span>
                        </label>
                        <select name="type"
                                id="type"
                                class="assembly-form-select @error('type') error @enderror"
                                required>
                            <option value="">Sélectionnez...</option>
                            <option value="ordinaire" {{ old('type') === 'ordinaire' ? 'selected' : '' }}>
                                Ordinaire (janvier de chaque année)
                            </option>
                            <option value="extraordinaire" {{ old('type') === 'extraordinaire' ? 'selected' : '' }}>
                                Extraordinaire (urgence)
                            </option>
                        </select>
                        @error('type')
                            <span class="assembly-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Statut --}}
                    <div class="assembly-form-group">
                        <label for="statut" class="assembly-form-label">
                            Statut <span class="required">*</span>
                        </label>
                        <select name="statut"
                                id="statut"
                                class="assembly-form-select @error('statut') error @enderror"
                                required>
                            <option value="planifiee" {{ old('statut', 'planifiee') === 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                            <option value="tenue"     {{ old('statut') === 'tenue'     ? 'selected' : '' }}>Tenue</option>
                            <option value="annulee"   {{ old('statut') === 'annulee'   ? 'selected' : '' }}>Annulée</option>
                            <option value="reportee"  {{ old('statut') === 'reportee'  ? 'selected' : '' }}>Reportée</option>
                        </select>
                        @error('statut')
                            <span class="assembly-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Date de réunion --}}
                    <div class="assembly-form-group">
                        <label for="date_reunion" class="assembly-form-label">
                            Date de réunion <span class="required">*</span>
                        </label>
                        <input type="date"
                               name="date_reunion"
                               id="date_reunion"
                               class="assembly-form-input @error('date_reunion') error @enderror"
                               value="{{ old('date_reunion', date('Y-m-d')) }}"
                               required>
                        @error('date_reunion')
                            <span class="assembly-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Lieu --}}
                    <div class="assembly-form-group">
                        <label for="lieu" class="assembly-form-label">
                            Lieu <span class="required">*</span>
                        </label>
                        <input type="text"
                               name="lieu"
                               id="lieu"
                               class="assembly-form-input @error('lieu') error @enderror"
                               value="{{ old('lieu') }}"
                               placeholder="Ex: Siège social, Minova"
                               maxlength="255"
                               required>
                        @error('lieu')
                            <span class="assembly-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : PARTICIPANTS & QUORUM --}}
            {{-- ============================================ --}}
            <div class="assembly-form-section">
                <h4 class="assembly-form-section-title">
                    <i class="fas fa-users"></i> Participants
                </h4>

                <div class="assembly-form-grid assembly-form-grid-4">

                    {{-- Membres total --}}
                    <div class="assembly-form-group">
                        <label for="membres_total" class="assembly-form-label">
                            Membres effectifs
                        </label>
                        <input type="number"
                               name="membres_total"
                               id="membres_total"
                               class="assembly-form-input"
                               value="{{ old('membres_total') }}"
                               min="0"
                               placeholder="Ex: 25">
                    </div>

                    {{-- Membres présents --}}
                    <div class="assembly-form-group">
                        <label for="membres_presents" class="assembly-form-label">
                            Membres présents
                        </label>
                        <input type="number"
                               name="membres_presents"
                               id="membres_presents"
                               class="assembly-form-input"
                               value="{{ old('membres_presents') }}"
                               min="0"
                               placeholder="Ex: 18">
                    </div>

                    {{-- Membres absents --}}
                    <div class="assembly-form-group">
                        <label for="membres_absents" class="assembly-form-label">
                            Membres absents
                        </label>
                        <input type="number"
                               name="membres_absents"
                               id="membres_absents"
                               class="assembly-form-input"
                               value="{{ old('membres_absents') }}"
                               min="0"
                               placeholder="Ex: 7">
                    </div>

                    {{-- Affichage du quorum --}}
                    <div class="assembly-form-group">
                        <label class="assembly-form-label">Quorum</label>
                        <div id="quorumInfo"
                             class="assembly-quorum-box"
                             data-state="empty">
                            <i class="fas fa-info-circle"></i>
                            Remplissez les champs pour voir le quorum
                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3 : CONTENU DE L'AG --}}
            {{-- ============================================ --}}
            <div class="assembly-form-section">
                <h4 class="assembly-form-section-title">
                    <i class="fas fa-file-alt"></i> Contenu de l'AG
                </h4>

                <div class="assembly-form-grid">

                    {{-- Ordre du jour --}}
                    <div class="assembly-form-group assembly-form-full">
                        <label for="ordre_du_jour" class="assembly-form-label">
                            Ordre du jour
                        </label>
                        <textarea name="ordre_du_jour"
                                  id="ordre_du_jour"
                                  class="assembly-form-textarea"
                                  rows="3"
                                  maxlength="2000"
                                  placeholder="Points à l'ordre du jour...">{{ old('ordre_du_jour') }}</textarea>
                    </div>

                    {{-- Décisions --}}
                    <div class="assembly-form-group assembly-form-full">
                        <label for="decisions" class="assembly-form-label">
                            Décisions prises
                        </label>
                        <textarea name="decisions"
                                  id="decisions"
                                  class="assembly-form-textarea"
                                  rows="3"
                                  maxlength="5000"
                                  placeholder="Décisions adoptées lors de l'AG...">{{ old('decisions') }}</textarea>
                    </div>

                    {{-- Résolutions --}}
                    <div class="assembly-form-group assembly-form-full">
                        <label for="resolutions" class="assembly-form-label">
                            Résolutions
                        </label>
                        <textarea name="resolutions"
                                  id="resolutions"
                                  class="assembly-form-textarea"
                                  rows="3"
                                  maxlength="5000"
                                  placeholder="Résolutions votées...">{{ old('resolutions') }}</textarea>
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4 : PROCÈS-VERBAL --}}
            {{-- ============================================ --}}
            <div class="assembly-form-section">
                <h4 class="assembly-form-section-title">
                    <i class="fas fa-file-pdf"></i> Procès-verbal
                </h4>

                <div class="assembly-form-grid">

                    <div class="assembly-form-group assembly-form-full">
                        <label for="pv_fichier" class="assembly-form-label">
                            Fichier PV (PDF)
                        </label>
                        <input type="file"
                               name="pv_fichier"
                               id="pv_fichier"
                               class="assembly-form-file"
                               accept="application/pdf">
                        <small class="assembly-form-hint">
                            <i class="fas fa-info-circle"></i>
                            Format PDF uniquement — Max 10 Mo
                        </small>
                        @error('pv_fichier')
                            <span class="assembly-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="assembly-form-actions">
                <a href="{{ route('admin.gouvernance.assemblies') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer l'AG
                </button>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script src="{{ asset('assets/js/admin-gouvernance-assembly-form.js') }}"></script>
@endsection