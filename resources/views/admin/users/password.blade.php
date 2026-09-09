@extends('layouts.admin')

@section('title', 'Changer le mot de passe | PHOVE')

@section('page_title', 'Changer le mot de passe')
@section('page_icon', 'key')
@section('page_subtitle', 'Modifiez le mot de passe du compte de ' . $user->nom . '.')

@php
    $breadcrumb = ['Utilisateurs' => route('admin.users.index'), $user->nom => route('admin.users.show', $user), 'Mot de passe' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-users.css') }}">
    <style>
        .password-card {
            background: #FFFFFF;
            border-radius: 18px;
            padding: 36px 40px;
            max-width: 550px;
            margin: 0 auto;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .password-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 2px solid #E2E8F0;
        }

        .password-header-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: #F59E0B;
            color: #FFFFFF;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .password-header h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1E293B;
            margin: 0;
        }

        .password-header p {
            font-size: 13px;
            color: #64748B;
            margin: 4px 0 0 0;
        }

        .password-info-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            background: #E8F0F8;
            border-radius: 12px;
            border: 1px solid rgba(26, 91, 156, 0.15);
            margin-bottom: 24px;
        }

        .password-user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
            background: #1A5B9C;
            color: #FFFFFF;
        }

        .password-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .password-user-info {
            flex: 1;
            min-width: 0;
        }

        .password-user-nom {
            font-size: 14px;
            font-weight: 700;
            color: #1E293B;
        }

        .password-user-email {
            font-size: 12px;
            color: #64748B;
        }

        .password-form-group {
            margin-bottom: 20px;
        }

        .password-form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 7px;
        }

        .password-form-label .required {
            color: #EF4444;
        }

        .password-form-input {
            width: 100%;
            padding: 12px 16px;
            font-family: var(--font-primary);
            font-size: 14px;
            border: 2px solid #CBD5E1;
            border-radius: 10px;
            outline: none;
            transition: all 0.25s ease;
            background: #F8FAFC;
            color: #1E293B;
            letter-spacing: 2px;
        }

        .password-form-input:focus {
            border-color: #1A5B9C;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(26, 91, 156, 0.08);
        }

        .password-form-input::placeholder {
            letter-spacing: 0;
        }

        .password-form-error {
            font-size: 11px;
            color: #EF4444;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .password-strength {
            margin-top: 8px;
        }

        .password-strength-bar {
            height: 4px;
            background: #CBD5E1;
            border-radius: 2px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .password-strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s ease, background 0.3s ease;
            width: 0%;
        }

        .password-strength-text {
            font-size: 11px;
            margin-top: 4px;
            font-weight: 500;
        }

        .password-requirements {
            background: #F8FAFC;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }

        .password-requirements h4 {
            font-size: 12px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements ul li {
            font-size: 12px;
            color: #64748B;
            line-height: 2;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }

        .password-requirements ul li.valid {
            color: #10B981;
        }

        .password-requirements ul li i {
            font-size: 10px;
        }

        .password-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 2px solid #E2E8F0;
        }

        @media (max-width: 768px) {
            .password-card {
                padding: 24px 18px;
            }
        }
    </style>
@endsection

@section('content')

    <div class="password-card">

        {{-- En-tête --}}
        <div class="password-header">
            <div class="password-header-icon">
                <i class="fas fa-key"></i>
            </div>
            <div>
                <h2>Changer le mot de passe</h2>
                <p>Définissez un nouveau mot de passe pour cet utilisateur.</p>
            </div>
        </div>

        {{-- Info utilisateur --}}
        <div class="password-info-card">
            <div class="password-user-avatar">
                @if($user->photo_profil)
                    <img src="{{ asset('storage/' . $user->photo_profil) }}" alt="{{ $user->nom }}" loading="lazy">
                @else
                    {{ $user->initiales() }}
                @endif
            </div>
            <div class="password-user-info">
                <div class="password-user-nom">{{ $user->nom }}</div>
                <div class="password-user-email">
                    <i class="fas fa-envelope" style="font-size: 10px; margin-right: 4px;"></i> {{ $user->email }}
                </div>
            </div>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('admin.users.password.update', $user) }}" method="POST" id="passwordForm">
            @csrf
            @method('PUT')

            <div class="password-form-group">
                <label for="mot_de_passe" class="password-form-label">
                    Nouveau mot de passe <span class="required">*</span>
                </label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" 
                       class="password-form-input @error('mot_de_passe') is-invalid @enderror" 
                       placeholder="Minimum 8 caractères" 
                       required autofocus>
                @error('mot_de_passe')
                    <span class="password-form-error">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </span>
                @enderror

                {{-- Barre de force --}}
                <div class="password-strength">
                    <div class="password-strength-bar">
                        <div id="passwordStrengthFill" class="password-strength-fill"></div>
                    </div>
                    <div id="passwordStrengthText" class="password-strength-text"></div>
                </div>
            </div>

            <div class="password-form-group">
                <label for="mot_de_passe_confirmation" class="password-form-label">
                    Confirmer le mot de passe <span class="required">*</span>
                </label>
                <input type="password" name="mot_de_passe_confirmation" id="mot_de_passe_confirmation" 
                       class="password-form-input" 
                       placeholder="Répétez le mot de passe" 
                       required>
                <div id="passwordMatchText" style="font-size: 11px; margin-top: 4px;"></div>
            </div>

            {{-- Exigences --}}
            <div class="password-requirements">
                <h4><i class="fas fa-shield-alt" style="color: #1A5B9C;"></i> Exigences de sécurité</h4>
                <ul>
                    <li id="req-length"><i class="fas fa-times-circle" style="color: #94A3B8;"></i> Au moins 8 caractères</li>
                    <li id="req-uppercase"><i class="fas fa-times-circle" style="color: #94A3B8;"></i> Au moins une lettre majuscule</li>
                    <li id="req-lowercase"><i class="fas fa-times-circle" style="color: #94A3B8;"></i> Au moins une lettre minuscule</li>
                    <li id="req-number"><i class="fas fa-times-circle" style="color: #94A3B8;"></i> Au moins un chiffre</li>
                    <li id="req-special"><i class="fas fa-times-circle" style="color: #94A3B8;"></i> Au moins un caractère spécial (@$!%*?&)</li>
                </ul>
            </div>

            {{-- Actions --}}
            <div class="password-form-actions">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer le mot de passe
                </button>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const passwordInput = document.getElementById('mot_de_passe');
            const confirmInput = document.getElementById('mot_de_passe_confirmation');
            const strengthFill = document.getElementById('passwordStrengthFill');
            const strengthText = document.getElementById('passwordStrengthText');
            const matchText = document.getElementById('passwordMatchText');

            const reqLength = document.getElementById('req-length');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqLowercase = document.getElementById('req-lowercase');
            const reqNumber = document.getElementById('req-number');
            const reqSpecial = document.getElementById('req-special');

            if (passwordInput) {
                passwordInput.addEventListener('input', function () {
                    const password = this.value;
                    
                    const hasLength = password.length >= 8;
                    const hasUppercase = /[A-Z]/.test(password);
                    const hasLowercase = /[a-z]/.test(password);
                    const hasNumber = /[0-9]/.test(password);
                    const hasSpecial = /[@$!%*?&]/.test(password);

                    const score = [hasLength, hasUppercase, hasLowercase, hasNumber, hasSpecial].filter(Boolean).length;

                    const percentage = (score / 5) * 100;
                    strengthFill.style.width = percentage + '%';

                    let color, text;
                    switch (true) {
                        case score <= 1:
                            color = '#EF4444';
                            text = 'Très faible';
                            break;
                        case score === 2:
                            color = '#F59E0B';
                            text = 'Faible';
                            break;
                        case score === 3:
                            color = '#F59E0B';
                            text = 'Moyen';
                            break;
                        case score === 4:
                            color = '#10B981';
                            text = 'Fort';
                            break;
                        case score === 5:
                            color = '#1A5B9C';
                            text = 'Très fort';
                            break;
                        default:
                            color = '#CBD5E1';
                            text = '';
                    }

                    strengthFill.style.background = color;
                    strengthText.textContent = text;
                    strengthText.style.color = color;

                    updateRequirement(reqLength, hasLength);
                    updateRequirement(reqUppercase, hasUppercase);
                    updateRequirement(reqLowercase, hasLowercase);
                    updateRequirement(reqNumber, hasNumber);
                    updateRequirement(reqSpecial, hasSpecial);

                    checkPasswordMatch();
                });
            }

            if (confirmInput) {
                confirmInput.addEventListener('input', checkPasswordMatch);
            }

            function updateRequirement(element, isValid) {
                if (!element) return;
                const icon = element.querySelector('i');
                if (isValid) {
                    element.classList.add('valid');
                    icon.className = 'fas fa-check-circle';
                    icon.style.color = '#10B981';
                } else {
                    element.classList.remove('valid');
                    icon.className = 'fas fa-times-circle';
                    icon.style.color = '#94A3B8';
                }
            }

            function checkPasswordMatch() {
                if (!passwordInput || !confirmInput || !matchText) return;
                
                const password = passwordInput.value;
                const confirm = confirmInput.value;

                if (!confirm) {
                    matchText.textContent = '';
                    confirmInput.style.borderColor = '';
                    return;
                }

                if (password === confirm) {
                    matchText.textContent = '✅ Les mots de passe correspondent';
                    matchText.style.color = '#10B981';
                    confirmInput.style.borderColor = '#10B981';
                } else {
                    matchText.textContent = '❌ Les mots de passe ne correspondent pas';
                    matchText.style.color = '#EF4444';
                    confirmInput.style.borderColor = '#EF4444';
                }
            }

            const form = document.getElementById('passwordForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const password = passwordInput?.value || '';
                    const confirm = confirmInput?.value || '';
                    let errors = [];

                    if (!password) errors.push('Le mot de passe est obligatoire.');
                    if (password.length < 8) errors.push('Le mot de passe doit contenir au moins 8 caractères.');
                    if (password !== confirm) errors.push('Les mots de passe ne correspondent pas.');

                    if (errors.length > 0) {
                        e.preventDefault();
                        alert(errors.join('\n'));
                    }
                });
            }

            if (passwordInput && passwordInput.value) {
                passwordInput.dispatchEvent(new Event('input'));
            }
        });
    </script>
@endsection