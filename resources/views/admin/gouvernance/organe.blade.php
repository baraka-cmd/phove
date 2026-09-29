@extends('layouts.admin')

@section('title', $organeLibelle)

@section('page_title', $organeLibelle)
@section('page_icon', 'sitemap')
@section('page_subtitle', 'Gérez les membres de cet organe : ajoutez, modifiez ou retirez des membres.')

@php
    $breadcrumb = [
        ['label' => 'Gouvernance', 'url' => route('admin.gouvernance.index')],
        $organeLibelle,
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance-organe.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance.css') }}">
@endsection

@section('content')

    <div class="gouvernance-detail-grid">

        {{-- ============================================ --}}
        {{-- TABLEAU DES MEMBRES DE L'ORGANE --}}
        {{-- ============================================ --}}
        <div class="gouvernance-table-card">

            @if($membres->count() > 0)
                <div class="table-responsive">
                    <table class="table-gouvernance">
                        <thead>
                            <tr>
                                <th>Membre</th>
                                <th>Poste</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Statut</th>
                                <th class="th-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($membres as $organe)
                                <tr>
                                    {{-- Membre --}}
                                    <td>
                                        <div class="gouvernance-membre-item">

                                            {{-- Avatar (photo ou initiales) --}}
                                            <div class="gouvernance-membre-avatar">
                                                @if($organe->membre && $organe->membre->photo)
                                                    <img src="{{ route('media.show', ['path' => ltrim($organe->membre->photo, '/')]) }}"
                                                         alt="{{ $organe->membre->nomComplet() }}"
                                                         loading="lazy"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <span style="display: none;">
                                                        {{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}
                                                    </span>
                                                @else
                                                    <span>
                                                        {{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Infos --}}
                                            <div class="gouvernance-membre-info">
                                                <div class="gouvernance-membre-nom">
                                                    {{ $organe->membre ? $organe->membre->nomComplet() : '—' }}
                                                </div>
                                                @if($organe->membre && $organe->membre->email)
                                                    <div class="gouvernance-membre-email">
                                                        {{ $organe->membre->email }}
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </td>

                                    {{-- Poste --}}
                                    <td>
                                        <span class="gouvernance-poste">
                                            {{ $organe->poste }}
                                        </span>
                                    </td>

                                    {{-- Date début --}}
                                    <td>
                                        <span class="gouvernance-date">
                                            {{ $organe->date_debut->format('d/m/Y') }}
                                        </span>
                                    </td>

                                    {{-- Date fin --}}
                                    <td>
                                        <span class="gouvernance-date">
                                            {{ $organe->date_fin ? $organe->date_fin->format('d/m/Y') : 'En cours' }}
                                        </span>
                                    </td>

                                    {{-- Statut --}}
                                    <td>
                                        <span class="membre-badge membre-badge-{{ $organe->actif ? 'success' : 'info' }}">
                                            {{ $organe->actif ? 'Actif' : 'Terminé' }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="membre-actions">

                                            {{-- Éditer --}}
                                            <button type="button"
                                                    class="membre-action-btn edit btn-edit-poste"
                                                    data-id="{{ $organe->id }}"
                                                    data-poste="{{ $organe->poste }}"
                                                    data-date-debut="{{ $organe->date_debut->format('Y-m-d') }}"
                                                    data-date-fin="{{ $organe->date_fin ? $organe->date_fin->format('Y-m-d') : '' }}"
                                                    data-actif="{{ $organe->actif }}"
                                                    data-ordre="{{ $organe->ordre }}"
                                                    title="Modifier le poste">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Retirer --}}
                                            @if($organe->actif)
                                                <form action="{{ route('admin.gouvernance.retirerMembre', $organe) }}"
                                                      method="POST"
                                                      class="form-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            class="membre-action-btn delete btn-retirer-membre"
                                                            data-nom="{{ $organe->membre ? $organe->membre->nomComplet() : '—' }}"
                                                            data-poste="{{ $organe->poste }}"
                                                            title="Retirer le membre">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="gouvernance-empty">
                    <i class="fas fa-users-slash"></i>
                    <p>Aucun membre dans cet organe pour le moment.</p>
                </div>
            @endif

        </div>

        {{-- ============================================ --}}
        {{-- FORMULAIRE D'AJOUT DE MEMBRE --}}
        {{-- ============================================ --}}
        <div class="gouvernance-form-card">

            <h3 class="gouvernance-form-title">
                <i class="fas fa-user-plus"></i> Ajouter un membre
            </h3>

            <form action="{{ route('admin.gouvernance.storeMembre') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="organe" value="{{ $organe }}">

                {{-- Membre à rattacher --}}
                <div class="gouvernance-form-group">
                    <label for="membre_id" class="gouvernance-form-label">
                        Membre <span class="required">*</span>
                    </label>
                    <select name="membre_id" id="membre_id" class="gouvernance-form-select" required>
                        <option value="">Sélectionnez un membre...</option>
                        @foreach($membresDisponibles as $m)
                            <option value="{{ $m->id }}" {{ old('membre_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nomComplet() }}
                            </option>
                        @endforeach
                    </select>
                    @error('membre_id')
                        <span class="gouvernance-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Photo --}}
                <div class="gouvernance-form-group">
                    <label for="photo" class="gouvernance-form-label">
                        Photo du membre <small>(optionnel — 2 Mo max)</small>
                    </label>
                    <div class="gouvernance-photo-upload">
                        <input type="file"
                               name="photo"
                               id="photo"
                               class="gouvernance-photo-input"
                               accept="image/jpeg,image/jpg,image/png,image/webp"
                               hidden>

                        <label for="photo" class="gouvernance-photo-preview" id="photoPreview">
                            <img id="photoPreviewImg"
                                 src=""
                                 alt="Aperçu"
                                 style="display: none;">
                            <div class="gouvernance-photo-placeholder" id="photoPlaceholder">
                                <i class="fas fa-camera"></i>
                                <span>Choisir une photo</span>
                            </div>
                        </label>

                        <button type="button"
                                class="gouvernance-photo-remove"
                                id="photoRemoveBtn"
                                style="display: none;"
                                title="Retirer la photo">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @error('photo')
                        <span class="gouvernance-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Poste --}}
                <div class="gouvernance-form-group">
                    <label for="poste" class="gouvernance-form-label">
                        Poste <span class="required">*</span>
                    </label>
                    <input type="text"
                           name="poste"
                           id="poste"
                           class="gouvernance-form-input"
                           value="{{ old('poste') }}"
                           placeholder="Ex: Président, Secrétaire..."
                           maxlength="255"
                           required>
                    @error('poste')
                        <span class="gouvernance-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Date début --}}
                <div class="gouvernance-form-group">
                    <label for="date_debut" class="gouvernance-form-label">
                        Date de début <span class="required">*</span>
                    </label>
                    <input type="date"
                           name="date_debut"
                           id="date_debut"
                           class="gouvernance-form-input"
                           value="{{ old('date_debut', date('Y-m-d')) }}"
                           required>
                    @error('date_debut')
                        <span class="gouvernance-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Date fin --}}
                <div class="gouvernance-form-group">
                    <label for="date_fin" class="gouvernance-form-label">
                        Date de fin <small>(optionnel)</small>
                    </label>
                    <input type="date"
                           name="date_fin"
                           id="date_fin"
                           class="gouvernance-form-input"
                           value="{{ old('date_fin') }}">
                    @error('date_fin')
                        <span class="gouvernance-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Ordre --}}
                <div class="gouvernance-form-group">
                    <label for="ordre" class="gouvernance-form-label">
                        Ordre d'affichage <small>(0 = premier)</small>
                    </label>
                    <input type="number"
                           name="ordre"
                           id="ordre"
                           class="gouvernance-form-input"
                           value="{{ old('ordre', 0) }}"
                           min="0">
                </div>

                {{-- Submit --}}
                <button type="submit" class="gouvernance-form-submit">
                    <i class="fas fa-plus-circle"></i> Ajouter à l'organe
                </button>

            </form>
        </div>

    </div>

    {{-- ============================================ --}}
    {{-- FORMULAIRE D'ÉDITION (caché par défaut) --}}
    {{-- ============================================ --}}
    <div class="gouvernance-form-card gouvernance-form-card-edit"
         id="editFormCard"
         style="display: none;">

        <h3 class="gouvernance-form-title">
            <i class="fas fa-edit"></i> Modifier le poste
        </h3>

        <form action="{{ route('admin.gouvernance.updateMembre', ['organe' => '__ID__']) }}"
              method="POST"
              id="editForm"
              data-action-base="{{ url('/admin/gouvernance/organes') }}">
            @csrf
            @method('PUT')

            <div class="gouvernance-form-group">
                <label for="edit_poste" class="gouvernance-form-label">
                    Poste <span class="required">*</span>
                </label>
                <input type="text" name="poste" id="edit_poste" class="gouvernance-form-input" required>
            </div>

            <div class="gouvernance-form-group">
                <label for="edit_date_debut" class="gouvernance-form-label">
                    Date de début <span class="required">*</span>
                </label>
                <input type="date" name="date_debut" id="edit_date_debut" class="gouvernance-form-input" required>
            </div>

            <div class="gouvernance-form-group">
                <label for="edit_date_fin" class="gouvernance-form-label">
                    Date de fin
                </label>
                <input type="date" name="date_fin" id="edit_date_fin" class="gouvernance-form-input">
            </div>

            <div class="gouvernance-form-group">
                <label for="edit_ordre" class="gouvernance-form-label">
                    Ordre d'affichage
                </label>
                <input type="number" name="ordre" id="edit_ordre" class="gouvernance-form-input" value="0" min="0">
            </div>

            <div class="gouvernance-form-group">
                <label class="gouvernance-checkbox-row">
                    <input type="checkbox" name="actif" id="edit_actif" value="1">
                    <span>Mandat actif</span>
                </label>
            </div>

            <button type="submit" class="gouvernance-form-submit">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>

        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script src="{{ asset('assets/js/admin-gouvernance.js') }}"></script>
    <script src="{{ asset('assets/js/admin-gouvernance-organe.js') }}"></script>
@endsection