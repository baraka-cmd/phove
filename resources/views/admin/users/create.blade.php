@extends('layouts.admin')

@section('title', 'Créer un utilisateur | PHOVE')

@section('page_title', 'Nouvel utilisateur')
@section('page_icon', 'user-plus')
@section('page_subtitle', 'Créez un nouveau compte avec un rôle hiérarchique PHOVE. Tous les champs marqués d\'un astérisque (*) sont obligatoires.')

@php
    $breadcrumb = ['Utilisateurs' => route('admin.users.index'), 'Créer' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-users.css') }}">
@endsection

@section('content')

    <div class="user-form-card">

        {{-- En-tête --}}
        <div class="user-form-header">
            <div class="user-form-header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h2>Créer un utilisateur</h2>
                <p>Remplissez les informations pour créer un nouveau compte avec un rôle PHOVE.</p>
            </div>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
            @csrf

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
                               value="{{ old('nom') }}" 
                               placeholder="Ex: KABOYI MUSAMUGUMA Philippe" 
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
                               value="{{ old('email') }}" 
                               placeholder="phovemonde@gmail.com" 
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
                               value="{{ old('telephone') }}" 
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
                        Photo <small style="font-weight: 400; color: #64748B;">(optionnelle)</small>
                    </label>
                    <div class="user-photo-upload">
                        <label for="photo_profil" class="user-photo-preview" id="photoPreview">
                            <img id="photoPreviewImg" src="" alt="Aperçu" style="display: none;">
                            <div class="user-photo-placeholder" id="photoPlaceholder">
                                <i class="fas fa-user"></i>
                            </div>
                        </label>
                        <div class="user-photo-info">
                            <p>Cliquez pour choisir une photo</p>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> JPG, PNG ou WebP</li>
                                <li><i class="fas fa-check-circle"></i> Taille max : 2 Mo</li>
                                <li><i class="fas fa-check-circle"></i> Format carré recommandé</li>
                            </ul>
                            <input type="file" name="photo_profil" id="photo_profil" 
                                   accept="image/jpeg,image/jpg,image/png,image/webp" 
                                   style="display: none;">
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
            {{-- SECTION 3 : SÉCURITÉ --}}
            {{-- ============================================ --}}
            <div class="user-form-section">
                <div class="user-form-section-title">
                    <i class="fas fa-lock"></i> Sécurité
                </div>

                <div class="user-form-grid">
                    <div class="user-form-group">
                        <label for="mot_de_passe" class="user-form-label">
                            Mot de passe <span class="required">*</span>
                        </label>
                        <input type="password" name="mot_de_passe" id="mot_de_passe" 
                               class="user-form-input @error('mot_de_passe') is-invalid @enderror" 
                               placeholder="Minimum 8 caractères" 
                               required>
                        @error('mot_de_passe')
                            <span class="user-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="user-form-group">
                        <label for="mot_de_passe_confirmation" class="user-form-label">
                            Confirmer le mot de passe <span class="required">*</span>
                        </label>
                        <input type="password" name="mot_de_passe_confirmation" id="mot_de_passe_confirmation" 
                               class="user-form-input" 
                               placeholder="Répétez le mot de passe" 
                               required>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4 : RÔLE & STATUT --}}
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
                            @foreach(\App\Enums\Roles::adminRoles() as $code => $libelle)
                                <option value="{{ $code }}" {{ old('role', 'editeur') === $code ? 'selected' : '' }}>
                                    {{ $libelle }}
                                </option>
                            @endforeach
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
                            <option value="actif" {{ old('statut', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                            <option value="suspendu" {{ old('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                        @error('statut')
                            <span class="user-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- INFO PERMISSIONS PAR RÔLE --}}
            {{-- ============================================ --}}
            <div style="padding: 16px 20px; background: #E8F0F8; border-radius: 12px; display: flex; gap: 12px; align-items: flex-start;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #1A5B9C; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div style="font-size: 13px; color: #334155; line-height: 1.6;">
                    <strong style="color: #1E293B;">Permissions par rôle :</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 18px; list-style: disc;">
                        <li><strong>Super Admin</strong> : Accès total, gestion des utilisateurs et paramètres.</li>
                        <li><strong>Directeur Général</strong> : Coordination globale, validation des salaires et rapports.</li>
                        <li><strong>Chef de Mission</strong> : Coordination continentale, suivi des projets.</li>
                        <li><strong>Chargé de Programme</strong> : Coordination nationale, création de projets.</li>
                        <li><strong>Secrétaire</strong> : Gestion des documents et dossiers du personnel.</li>
                        <li><strong>Comptable</strong> : Gestion financière, salaires, cotisations.</li>
                        <li><strong>Caissier</strong> : Encaissements, paiements.</li>
                        <li><strong>Éditeur</strong> : Rédaction des articles et contenus.</li>
                    </ul>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- ACTIONS --}}
            {{-- ============================================ --}}
            <div class="user-form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Créer l'utilisateur
                </button>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Prévisualisation photo
            const photoInput = document.getElementById('photo_profil');
            const photoPreview = document.getElementById('photoPreview');
            const photoPreviewImg = document.getElementById('photoPreviewImg');
            const photoPlaceholder = document.getElementById('photoPlaceholder');

            if (photoInput && photoPreview) {
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
                    }
                });
            }

            // Validation formulaire
            const form = document.getElementById('userForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const nom = document.getElementById('nom')?.value?.trim();
                    const email = document.getElementById('email')?.value?.trim();
                    const mdp = document.getElementById('mot_de_passe')?.value;
                    const mdpConf = document.getElementById('mot_de_passe_confirmation')?.value;
                    const role = document.getElementById('role')?.value;
                    const statut = document.getElementById('statut')?.value;
                    let errors = [];

                    if (!nom) errors.push('Le nom est obligatoire.');
                    if (!email) errors.push('L\'email est obligatoire.');
                    if (!mdp) errors.push('Le mot de passe est obligatoire.');
                    if (mdp && mdp.length < 8) errors.push('Le mot de passe doit contenir au moins 8 caractères.');
                    if (mdp !== mdpConf) errors.push('Les mots de passe ne correspondent pas.');
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