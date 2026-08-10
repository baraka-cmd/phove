@extends('layouts.admin')

@section('title', 'Nouveau message')

@section('page_title', 'Nouveau message')
@section('page_icon', 'pen')
@section('page_subtitle', 'Rédigez un message à un autre administrateur ou utilisateur du système.')

@php
    $breadcrumb = ['Messagerie' => route('admin.messages.index'), 'Nouveau message' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-contacts.css') }}">
    <style>
        .composer-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .composer-card {
            background: #fff;
            border-radius: 18px;
            padding: 36px 40px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .composer-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .composer-header-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: var(--color-primary);
            color: #fff;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .composer-header h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        .composer-header p {
            font-size: 13px;
            color: #888;
            margin: 4px 0 0 0;
        }

        .composer-section {
            margin-bottom: 28px;
        }

        .composer-section:last-child {
            margin-bottom: 0;
        }

        .composer-section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--color-primary-lighter);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .composer-section-title i {
            color: #fff;
            background: var(--color-primary);
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .composer-form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 18px;
        }

        .composer-form-label {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 7px;
        }

        .composer-form-label .required {
            color: var(--color-accent);
        }

        .composer-form-input,
        .composer-form-select,
        .composer-form-textarea {
            width: 100%;
            padding: 12px 16px;
            font-family: var(--font-primary);
            font-size: 13px;
            border: 2px solid #e0e4e8;
            border-radius: 10px;
            outline: none;
            transition: all 0.25s ease;
            background: #fafbfc;
            color: #1a1a1a;
        }

        .composer-form-input:focus,
        .composer-form-select:focus,
        .composer-form-textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(22, 85, 143, 0.08);
        }

        .composer-form-select {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            padding-right: 40px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' stroke='%23777' stroke-width='1.8' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
        }

        .composer-form-textarea {
            resize: vertical;
            min-height: 200px;
            line-height: 1.7;
        }

        .composer-form-error {
            font-size: 11px;
            color: var(--color-accent);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .composer-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 2px solid #f0f0f0;
        }

        .composer-destinataire-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            background: var(--color-primary-lighter);
            border-radius: 12px;
            border: 1px solid rgba(22, 85, 143, 0.15);
            margin-top: 8px;
        }

        .composer-destinataire-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
            background: var(--color-primary);
            color: #fff;
        }

        .composer-destinataire-info {
            flex: 1;
            min-width: 0;
        }

        .composer-destinataire-nom {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .composer-destinataire-role {
            font-size: 11px;
            color: var(--color-text-muted);
        }

        .composer-users-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .composer-user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 2px solid #e0e4e8;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }

        .composer-user-card:hover {
            border-color: var(--color-primary);
            background: var(--color-primary-lighter);
        }

        .composer-user-card.selected {
            border-color: var(--color-primary);
            background: var(--color-primary-lighter);
            box-shadow: 0 0 0 3px rgba(22, 85, 143, 0.1);
        }

        .composer-user-card.selected::after {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: var(--color-primary);
            font-size: 14px;
            margin-left: auto;
        }

        .composer-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .composer-user-avatar.blue { background: var(--color-primary-lighter); color: var(--color-primary); }
        .composer-user-avatar.green { background: var(--color-success-light); color: var(--color-success); }
        .composer-user-avatar.orange { background: #fff3cd; color: #e67e22; }
        .composer-user-avatar.red { background: var(--color-error-light); color: var(--color-accent); }
        .composer-user-avatar.purple { background: #f3e5f5; color: #8e44ad; }

        .composer-user-info {
            flex: 1;
            min-width: 0;
        }

        .composer-user-nom {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .composer-user-role {
            font-size: 11px;
            color: var(--color-text-muted);
        }

        @media (max-width: 768px) {
            .composer-users-grid {
                grid-template-columns: 1fr;
            }
            .composer-card {
                padding: 24px 18px;
            }
        }
    </style>
@endsection

@section('content')

    <div class="composer-container">

        <div class="composer-card">

            {{-- En-tête --}}
            <div class="composer-header">
                <div class="composer-header-icon">
                    <i class="fas fa-pen"></i>
                </div>
                <div>
                    <h2>Nouveau message</h2>
                    <p>Sélectionnez un destinataire et rédigez votre message. Tous les champs marqués d'un astérisque (*) sont obligatoires.</p>
                </div>
            </div>

            {{-- Formulaire --}}
            <form action="{{ route('admin.messages.envoyer') }}" method="POST" id="composerForm">
                @csrf

                {{-- ============================================ --}}
                {{-- SECTION 1 : DESTINATAIRE --}}
                {{-- ============================================ --}}
                <div class="composer-section">
                    <div class="composer-section-title">
                        <i class="fas fa-user"></i> Destinataire <span style="color: var(--color-accent);">*</span>
                    </div>

                    <div class="composer-form-group">
                        <label class="composer-form-label">
                            <i class="fas fa-search" style="color: var(--color-text-muted);"></i> 
                            Rechercher un utilisateur
                        </label>
                        <input type="text" id="userSearch" class="composer-form-input" 
                               placeholder="Tapez le nom d'un administrateur ou utilisateur..." 
                               autocomplete="off">
                    </div>

                    {{-- Grille des utilisateurs --}}
                    <div class="composer-users-grid" id="usersGrid">
                        @foreach($utilisateurs as $utilisateur)
                            @php
                                $avatarColors = ['blue', 'green', 'orange', 'red', 'purple'];
                                $avatarColor = $avatarColors[crc32($utilisateur->nom) % count($avatarColors)];
                                $initiales = strtoupper(substr($utilisateur->nom, 0, 1));
                                $parts = explode(' ', $utilisateur->nom);
                                $initiales = strtoupper(substr($parts[0], 0, 1));
                                if (count($parts) > 1) {
                                    $initiales .= strtoupper(substr(end($parts), 0, 1));
                                }
                            @endphp
                            <div class="composer-user-card {{ old('destinataire_id') == $utilisateur->id || (isset($destinataire) && $destinataire->id == $utilisateur->id) ? 'selected' : '' }}" 
                                 data-user-id="{{ $utilisateur->id }}"
                                 data-user-nom="{{ $utilisateur->nom }}"
                                 data-user-role="{{ $utilisateur->role === 'super_admin' ? 'Super Admin' : ($utilisateur->role === 'admin' ? 'Administrateur' : 'Éditeur') }}">
                                <div class="composer-user-avatar {{ $avatarColor }}">
                                    {{ $initiales }}
                                </div>
                                <div class="composer-user-info">
                                    <div class="composer-user-nom">{{ $utilisateur->nom }}</div>
                                    <div class="composer-user-role">
                                        <i class="fas fa-{{ $utilisateur->role === 'super_admin' ? 'crown' : ($utilisateur->role === 'admin' ? 'user-shield' : 'user-edit') }}"></i>
                                        {{ $utilisateur->role === 'super_admin' ? 'Super Admin' : ($utilisateur->role === 'admin' ? 'Administrateur' : 'Éditeur') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Destinataire sélectionné --}}
                    <div id="selectedDestinataire" style="{{ old('destinataire_id') || isset($destinataire) ? '' : 'display: none;' }}">
                        <label class="composer-form-label" style="margin-top: 16px;">
                            <i class="fas fa-check-circle" style="color: var(--color-success);"></i> Destinataire sélectionné
                        </label>
                        <div class="composer-destinataire-card">
                            <div class="composer-destinataire-avatar" id="selectedAvatar">?</div>
                            <div class="composer-destinataire-info">
                                <div class="composer-destinataire-nom" id="selectedNom">—</div>
                                <div class="composer-destinataire-role" id="selectedRole">—</div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="destinataire_id" id="destinataireId" 
                           value="{{ old('destinataire_id', isset($destinataire) ? $destinataire->id : '') }}">
                    @error('destinataire_id')
                        <span class="composer-form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- ============================================ --}}
                {{-- SECTION 2 : MESSAGE --}}
                {{-- ============================================ --}}
                <div class="composer-section">
                    <div class="composer-section-title">
                        <i class="fas fa-envelope"></i> Message
                    </div>

                    <div class="composer-form-group">
                        <label for="sujet" class="composer-form-label">
                            Sujet <span class="required">*</span>
                        </label>
                        <input type="text" name="sujet" id="sujet" 
                               class="composer-form-input @error('sujet') is-invalid @enderror" 
                               value="{{ old('sujet') }}" 
                               placeholder="Ex: Réunion du CA, Point sur le projet Wash..." 
                               maxlength="255" required autofocus>
                        @error('sujet')
                            <span class="composer-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="composer-form-group">
                        <label for="contenu" class="composer-form-label">
                            Message <span class="required">*</span>
                        </label>
                        <textarea name="contenu" id="contenu" 
                                  class="composer-form-textarea @error('contenu') is-invalid @enderror" 
                                  placeholder="Rédigez votre message ici..." 
                                  maxlength="5000" required>{{ old('contenu') }}</textarea>
                        <small style="font-size: 11px; color: var(--color-text-muted); margin-top: 4px; text-align: right; display: block;">
                            <span id="contenuCounter">0</span>/5000 caractères
                        </small>
                        @error('contenu')
                            <span class="composer-form-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- INFO --}}
                {{-- ============================================ --}}
                <div style="padding: 16px 20px; background: var(--color-primary-lighter); border-radius: 12px; display: flex; gap: 12px; align-items: flex-start;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div style="font-size: 13px; color: var(--color-text-secondary); line-height: 1.6;">
                        <strong style="color: #1a1a1a;">Information :</strong> Ce message sera visible uniquement par le destinataire dans sa messagerie interne. Il ne s'agit pas d'un email public. Le destinataire recevra une notification dans sa barre latérale.
                    </div>
                </div>

                {{-- ============================================ --}}
                {{-- ACTIONS --}}
                {{-- ============================================ --}}
                <div class="composer-form-actions">
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Envoyer le message
                    </button>
                </div>

            </form>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ============================================================
            // SÉLECTION DU DESTINATAIRE
            // ============================================================
            const userCards = document.querySelectorAll('.composer-user-card');
            const destinataireId = document.getElementById('destinataireId');
            const selectedDestinataire = document.getElementById('selectedDestinataire');
            const selectedAvatar = document.getElementById('selectedAvatar');
            const selectedNom = document.getElementById('selectedNom');
            const selectedRole = document.getElementById('selectedRole');
            const userSearch = document.getElementById('userSearch');

            userCards.forEach(card => {
                card.addEventListener('click', function () {
                    // Désélectionner tous
                    userCards.forEach(c => c.classList.remove('selected'));
                    // Sélectionner celui-ci
                    this.classList.add('selected');
                    
                    const userId = this.getAttribute('data-user-id');
                    const userNom = this.getAttribute('data-user-nom');
                    const userRole = this.getAttribute('data-user-role');
                    
                    destinataireId.value = userId;
                    
                    if (selectedDestinataire) {
                        selectedDestinataire.style.display = 'block';
                        // Animation
                        selectedDestinataire.style.opacity = '0';
                        selectedDestinataire.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            selectedDestinataire.style.transition = 'all 0.3s ease';
                            selectedDestinataire.style.opacity = '1';
                            selectedDestinataire.style.transform = 'translateY(0)';
                        }, 50);
                    }
                    
                    if (selectedAvatar) {
                        const avatar = this.querySelector('.composer-user-avatar');
                        selectedAvatar.textContent = avatar ? avatar.textContent.trim() : '?';
                    }
                    if (selectedNom) selectedNom.textContent = userNom;
                    if (selectedRole) selectedRole.textContent = userRole;
                });
            });

            // Recherche d'utilisateurs
            if (userSearch) {
                userSearch.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    
                    userCards.forEach(card => {
                        const nom = card.getAttribute('data-user-nom').toLowerCase();
                        const role = card.getAttribute('data-user-role').toLowerCase();
                        
                        if (nom.includes(query) || role.includes(query)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }

            // ============================================================
            // COMPTEUR DE CARACTÈRES
            // ============================================================
            const contenu = document.getElementById('contenu');
            const contenuCounter = document.getElementById('contenuCounter');
            if (contenu && contenuCounter) {
                contenuCounter.textContent = contenu.value.length;
                contenu.addEventListener('input', function () {
                    contenuCounter.textContent = this.value.length;
                });
            }

            // ============================================================
            // VALIDATION DU FORMULAIRE
            // ============================================================
            const composerForm = document.getElementById('composerForm');
            if (composerForm) {
                composerForm.addEventListener('submit', function (e) {
                    let errors = [];
                    
                    if (!destinataireId.value) {
                        errors.push('Veuillez sélectionner un destinataire.');
                    }
                    
                    const sujet = document.getElementById('sujet')?.value?.trim();
                    if (!sujet) errors.push('Le sujet est obligatoire.');
                    
                    const contenuVal = contenu?.value?.trim();
                    if (!contenuVal) errors.push('Le contenu du message est obligatoire.');
                    
                    if (errors.length > 0) {
                        e.preventDefault();
                        
                        // Feedback visuel
                        if (!destinataireId.value) {
                            const grid = document.getElementById('usersGrid');
                            if (grid) {
                                grid.style.border = '2px solid var(--color-accent)';
                                grid.style.borderRadius = '12px';
                                grid.style.padding = '8px';
                                setTimeout(() => { grid.style.border = ''; grid.style.padding = ''; }, 3000);
                            }
                        }
                        
                        ['sujet', 'contenu'].forEach(id => {
                            const field = document.getElementById(id);
                            if (field && !field.value.trim()) {
                                field.style.borderColor = 'var(--color-accent)';
                                field.style.background = '#fffbfb';
                            }
                        });
                        
                        // Toast
                        showToast(errors.join('<br>'), 'error');
                    }
                });
            }

            // ============================================================
            // EFFACER LES ERREURS
            // ============================================================
            document.querySelectorAll('.composer-form-input, .composer-form-textarea').forEach(input => {
                input.addEventListener('focus', function () {
                    this.style.borderColor = '';
                    this.style.background = '';
                });
            });

            // ============================================================
            // TOAST
            // ============================================================
            function showToast(message, type = 'success', duration = 5000) {
                let container = document.querySelector('.toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'toast-container';
                    document.body.appendChild(container);
                }
                const toast = document.createElement('div');
                toast.className = 'toast toast-' + type;
                toast.innerHTML = `
                    <div class="toast-icon">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">${type === 'success' ? 'Succès' : 'Erreur'}</div>
                        <div class="toast-text">${message}</div>
                    </div>
                    <button class="toast-close" title="Fermer"><i class="fas fa-times"></i></button>
                    <div class="toast-progress"></div>
                `;
                container.appendChild(toast);
                toast.querySelector('.toast-close').addEventListener('click', () => {
                    toast.style.animation = 'toastOut 0.3s ease forwards';
                    setTimeout(() => toast.remove(), 300);
                });
                setTimeout(() => {
                    toast.style.animation = 'toastOut 0.3s ease forwards';
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }

            // ============================================================
            // INITIALISER L'ÉTAT SI DESTINATAIRE PRÉ-SÉLECTIONNÉ
            // ============================================================
            if (destinataireId.value) {
                const selectedCard = document.querySelector(`.composer-user-card[data-user-id="${destinataireId.value}"]`);
                if (selectedCard) {
                    selectedCard.click();
                }
            }
        });
    </script>
@endsection