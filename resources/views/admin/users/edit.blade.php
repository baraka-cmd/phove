@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')

@section('page_title', 'Modifier l\'utilisateur')
@section('page_icon', 'user-edit')
@section('page_subtitle', 'Modifiez les informations du compte de ' . $user->nom . '. Les champs marqués d\'un astérisque (*) sont obligatoires.')

@php
    $breadcrumb = ['Utilisateurs' => route('admin.users.index'), $user->nom => route('admin.users.show', $user), 'Modifier' => null];
    $avatarColors = ['blue', 'green', 'orange', 'red', 'purple'];
    $avatarColor = $avatarColors[crc32($user->nom) % count($avatarColors)];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-users.css') }}">
    <style>
        .edit-info-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 22px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .edit-info-bar-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .edit-info-bar-icon.blue { background: var(--color-primary-lighter); color: var(--color-primary); }
        .edit-info-bar-icon.green { background: var(--color-success-light); color: var(--color-success); }
        .edit-info-bar-content { flex: 1; min-width: 0; }
        .edit-info-bar-content h4 { font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 2px; }
        .edit-info-bar-content p { font-size: 12px; color: var(--color-text-muted); margin: 0; }
    </style>
@endsection

@section('content')

    <div class="user-form-card">

        {{-- En-tête --}}
        <div class="user-form-header">
            <div class="user-form-header-icon">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h2>Modifier {{ $user->nom }}</h2>
                <p>
                    <span class="user-role-badge user-role-{{ $user->role }}" style="margin-right: 6px;">
                        <i class="fas fa-{{ $user->role === 'super_admin' ? 'crown' : ($user->role === 'admin' ? 'user-shield' : 'user-edit') }}"></i>
                        {{ $user->role === 'super_admin' ? 'Super Admin' : ($user->role === 'admin' ? 'Administrateur' : 'Éditeur') }}
                    </span>
                    <span class="user-statut-badge user-statut-{{ $user->statut }}">
                        <i class="fas fa-{{ $user->statut === 'actif' ? 'check-circle' : ($user->statut === 'inactif' ? 'pause-circle' : 'ban') }}"></i>
                        {{ $user->statut === 'actif' ? 'Actif' : ($user->statut === 'inactif' ? 'Inactif' : 'Suspendu') }}
                    </span>
                </p>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- BARRE D'INFO RAPIDE --}}
        {{-- ============================================ --}}
        <div class="edit-info-bar">
            <div class="edit-info-bar-icon green">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="edit-info-bar-content">
                <h4>Modification du compte</h4>
                <p>Créé le {{ $user->created_at->format('d/m/Y') }} — Dernière connexion : {{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y à H:i') : 'Jamais' }}</p>
            </div>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" id="userForm">
            @csrf
            @method('PUT')

            {{-- ============================================ --}}
            {{-- SECTION 1 : INFORMATIONS PERSONNELLES --}}
            {{-- ============================================ --}}
            <div class="user-form-section">
                <div class="user-form-section-title">
                    <i class="fas fa-user-circle"></i> Informations personnelles
                </div>

                <div class="user-form-grid">
                    <div class="user-form-group user-form-full">
                        <label for="nom" class="user-form-label">
                            Nom complet <span class="required">*</span>
                        </label>
                        <input type="text" name="nom" id="nom" 
                               class="user-form-input @error('nom') is-invalid @enderror" 
                               value="{{ old('nom', $user->nom) }}" 
                               placeholder="Ex: Dr AKSANTI DUNIA Steeng" 
                               maxlength="255" required autofocus>
                        @error('nom')
                            <span class="user-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="user-form-group">
                        <label for="email" class="user-form-label">
                            Adresse email <span class="required">*</span>
                        </label>
                        <input type="email" name="email" id="email" 
                               class="user-form-input @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" 
                               placeholder="exemple@ahd-rdc.org" 
                               maxlength="191" required>
                        @error('email')
                            <span class="user-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="user-form-group">
                        <label for="telephone" class="user-form-label">
                            Téléphone
                        </label>
                        <input type="text" name="telephone" id="telephone" 
                               class="user-form-input @error('telephone') is-invalid @enderror" 
                               value="{{ old('telephone', $user->telephone) }}" 
                               placeholder="+243 000 000 000" 
                               maxlength="20">
                        @error('telephone')
                            <span class="user-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : PHOTO DE PROFIL --}}
            {{-- ============================================ --}}
            <div class="user-form-section">
                <div class="user-form-section-title">
                    <i class="fas fa-camera"></i> Photo de profil
                </div>

                <div class="user-form-group">
                    <label class="user-form-label">
                        Photo <small style="font-weight: 400; color: var(--color-text-muted);">(laissez vide pour conserver la photo actuelle)</small>
                    </label>
                    <div class="user-photo-upload">
                        <label for="photo_profil" class="user-photo-preview" id="photoPreview">
                            @if($user->photo_profil)
                                <img id="photoPreviewImg" src="{{ asset('storage/' . $user->photo_profil) }}" alt="Photo actuelle" style="display: block;">
                                <div class="user-photo-placeholder" id="photoPlaceholder" style="display: none;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @else
                                <img id="photoPreviewImg" src="" alt="Aperçu" style="display: none;">
                                <div class="user-photo-placeholder" id="photoPlaceholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </label>
                        <div class="user-photo-info">
                            <p>
                                @if($user->photo_profil)
                                    <span style="color: var(--color-success);">
                                        <i class="fas fa-check-circle"></i> Photo actuelle
                                    </span>
                                    <br>
                                    <a href="{{ asset('storage/' . $user->photo_profil) }}" target="_blank" style="font-size: 12px; color: var(--color-primary); text-decoration: underline;">
                                        <i class="fas fa-external-link-alt"></i> Voir la photo
                                    </a>
                                    <br>
                                    <button type="button" id="removePhotoBtn" style="background: none; border: none; color: var(--color-accent); font-size: 12px; cursor: pointer; padding: 0; margin-top: 4px; text-decoration: underline;">
                                        <i class="fas fa-trash-alt"></i> Supprimer la photo
                                    </button>
                                @else
                                    Cliquez pour choisir une photo
                                @endif
                            </p>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> JPG, PNG ou WebP</li>
                                <li><i class="fas fa-check-circle"></i> Taille max : 2 Mo</li>
                                <li><i class="fas fa-check-circle"></i> Format carré recommandé</li>
                            </ul>
                            <input type="file" name="photo_profil" id="photo_profil" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp" 
                                   style="display: none;">
                            <input type="hidden" name="remove_photo" id="removePhotoFlag" value="0">
                        </div>
                    </div>
                    @error('photo_profil')
                        <span class="user-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3 : RÔLE & STATUT --}}
            {{-- ============================================ --}}
            <div class="user-form-section">
                <div class="user-form-section-title">
                    <i class="fas fa-shield-alt"></i> Rôle & Statut
                </div>

                <div class="user-form-grid">
                    <div class="user-form-group">
                        <label for="role" class="user-form-label">
                            Rôle <span class="required">*</span>
                        </label>
                        <select name="role" id="role" class="user-form-select @error('role') is-invalid @enderror" required>
                            <option value="">-- Sélectionnez un rôle --</option>
                            <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin — Tous les droits</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrateur — Gestion complète</option>
                            <option value="editeur" {{ old('role', $user->role) === 'editeur' ? 'selected' : '' }}>Éditeur — Contenu uniquement</option>
                        </select>
                        @error('role')
                            <span class="user-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="user-form-group">
                        <label for="statut" class="user-form-label">
                            Statut <span class="required">*</span>
                        </label>
                        <select name="statut" id="statut" class="user-form-select @error('statut') is-invalid @enderror" required>
                            <option value="">-- Sélectionnez un statut --</option>
                            <option value="actif" {{ old('statut', $user->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut', $user->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                            <option value="suspendu" {{ old('statut', $user->statut) === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                        @error('statut')
                            <span class="user-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Avertissement si modification de son propre compte --}}
                @if($user->id === Auth::id())
                    <div style="margin-top: 16px; padding: 14px 18px; background: #fffbfb; border: 1px solid var(--color-error-light); border-radius: 10px; display: flex; gap: 10px; align-items: flex-start;">
                        <i class="fas fa-exclamation-triangle" style="color: var(--color-accent); font-size: 16px; margin-top: 1px;"></i>
                        <div style="font-size: 12px; color: #721c24; line-height: 1.6;">
                            <strong>Attention :</strong> Vous modifiez votre propre compte. Évitez de changer votre rôle ou votre statut, car cela pourrait vous empêcher d'accéder à certaines fonctionnalités.
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4 : STATISTIQUES --}}
            {{-- ============================================ --}}
            <div class="user-form-section">
                <div class="user-form-section-title">
                    <i class="fas fa-chart-bar"></i> Statistiques du compte
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;">
                    <div style="background: var(--color-bg-light); padding: 16px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-sign-in-alt" style="color: var(--color-success); margin-right: 4px;"></i> Dernière connexion
                        </div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1a1a1a;">
                            @if($user->derniere_connexion)
                                <span style="{{ $user->derniere_connexion->diffInHours(now()) < 24 ? 'color: var(--color-success);' : '' }}">
                                    {{ $user->derniere_connexion->diffInHours(now()) < 24 
                                        ? $user->derniere_connexion->diffForHumans() 
                                        : $user->derniere_connexion->format('d/m/Y H:i') }}
                                </span>
                            @else
                                <span style="color: #ccc;">Jamais</span>
                            @endif
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 16px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-calendar-plus" style="color: var(--color-primary); margin-right: 4px;"></i> Créé le
                        </div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1a1a1a;">
                            {{ $user->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                    <div style="background: var(--color-bg-light); padding: 16px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 10px; color: var(--color-text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                            <i class="fas fa-clock" style="color: #e67e22; margin-right: 4px;"></i> Modifié le
                        </div>
                        <div style="font-weight: 700; font-size: 0.9rem; color: #1a1a1a;">
                            {{ $user->updated_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="user-form-actions">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.users.password', $user) }}" class="btn btn-outline" style="border-color: #e67e22; color: #e67e22;">
                        <i class="fas fa-key"></i> Changer le mot de passe
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

            // ============================================================
            // PRÉVISUALISATION PHOTO
            // ============================================================
            const photoInput = document.getElementById('photo_profil');
            const photoPreviewImg = document.getElementById('photoPreviewImg');
            const photoPlaceholder = document.getElementById('photoPlaceholder');
            const removePhotoBtn = document.getElementById('removePhotoBtn');
            const removePhotoFlag = document.getElementById('removePhotoFlag');

            if (photoInput) {
                photoInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Format non supporté. Utilisez JPG, PNG ou WebP.');
                            this.value = '';
                            return;
                        }
                        if (file.size > 2 * 1024 * 1024) {
                            alert('La photo ne doit pas dépasser 2 Mo.');
                            this.value = '';
                            return;
                        }
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            photoPreviewImg.src = e.target.result;
                            photoPreviewImg.style.display = 'block';
                            photoPlaceholder.style.display = 'none';
                        };
                        reader.readAsDataURL(file);

                        // Réinitialiser le flag de suppression
                        if (removePhotoFlag) removePhotoFlag.value = '0';
                        if (removePhotoBtn) removePhotoBtn.style.display = 'inline';
                    }
                });
            }

            // ============================================================
            // SUPPRESSION PHOTO
            // ============================================================
            if (removePhotoBtn && removePhotoFlag && photoPreviewImg && photoPlaceholder) {
                removePhotoBtn.addEventListener('click', function () {
                    if (confirm('Voulez-vous vraiment supprimer la photo de profil actuelle ?')) {
                        removePhotoFlag.value = '1';
                        photoPreviewImg.style.display = 'none';
                        photoPreviewImg.src = '';
                        photoPlaceholder.style.display = 'flex';
                        photoInput.value = '';
                        removePhotoBtn.style.display = 'none';
                    }
                });
            }

            // ============================================================
            // VALIDATION FORMULAIRE
            // ============================================================
            const form = document.getElementById('userForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const nom = document.getElementById('nom')?.value?.trim();
                    const email = document.getElementById('email')?.value?.trim();
                    const role = document.getElementById('role')?.value;
                    const statut = document.getElementById('statut')?.value;
                    let errors = [];

                    if (!nom) errors.push('Le nom est obligatoire.');
                    if (!email) errors.push('L\'email est obligatoire.');
                    if (!role) errors.push('Le rôle est obligatoire.');
                    if (!statut) errors.push('Le statut est obligatoire.');

                    if (errors.length > 0) {
                        e.preventDefault();
                        alert(errors.join('\n'));
                    }
                });
            }
        });
    </script>
@endsection