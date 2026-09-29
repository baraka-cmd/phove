@extends('layouts.admin')

@section('title', 'Paramètres généraux | PHOVE')

@section('page_title', 'Paramètres')
@section('page_icon', 'cog')
@section('page_subtitle', 'Configurez l\'identité visuelle, les coordonnées, les réseaux sociaux et le contenu des pages statiques de PHOVE.')

@php
    $breadcrumb = ['Paramètres' => null];

    $groups = [
        'identite' => ['label' => 'Identité', 'icon' => 'fa-building'],
        'contact' => ['label' => 'Contact', 'icon' => 'fa-address-book'],
        'social' => ['label' => 'Réseaux sociaux', 'icon' => 'fa-share-alt'],
        'pages' => ['label' => 'Pages statiques', 'icon' => 'fa-file-alt'],
        'seo' => ['label' => 'SEO', 'icon' => 'fa-search'],
        'maintenance' => ['label' => 'Maintenance', 'icon' => 'fa-tools'],
    ];

    $groupDescriptions = [
        'identite' => 'Nom, logo, favicon, slogan et description de l\'organisation PHOVE.',
        'contact' => 'Adresse postale, téléphones, email et boîte postale du siège de Goma.',
        'social' => 'Liens vers les comptes Facebook, Twitter, LinkedIn, YouTube, Instagram et WhatsApp.',
        'pages' => 'Contenu des pages institutionnelles : Qui sommes-nous, Mission & Vision, Nos valeurs, Mentions légales.',
        'seo' => 'Optimisation pour les moteurs de recherche : méta-titres, descriptions et mots-clés.',
        'maintenance' => 'Mode maintenance et message affiché aux visiteurs pendant les interruptions.',
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-settings.css') }}">
@endsection

@section('content')

<div class="settings-container">

    {{-- ============================================ --}}
    {{-- TABS DE NAVIGATION --}}
    {{-- ============================================ --}}
    <div class="settings-tabs">
        @foreach($groups as $groupKey => $group)
            <a href="{{ route('admin.parametres.index', ['group' => $groupKey]) }}" 
               class="settings-tab {{ $activeGroup === $groupKey ? 'active' : '' }}">
                <i class="fas {{ $group['icon'] }}"></i>
                <span>{{ $group['label'] }}</span>
            </a>
        @endforeach
    </div>

    {{-- ============================================ --}}
    {{-- CARTE PRINCIPALE --}}
    {{-- ============================================ --}}
    <div class="settings-card">

        {{-- Header --}}
        <div class="settings-card-header">
            <div class="settings-card-header-icon">
                <i class="fas {{ $groups[$activeGroup]['icon'] }}"></i>
            </div>
            <div>
                <h2>{{ $groups[$activeGroup]['label'] }}</h2>
                <p>{{ $groupDescriptions[$activeGroup] }}</p>
            </div>
        </div>

        {{-- Body --}}
        <div class="settings-card-body">
            <form action="{{ route('admin.parametres.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="group" value="{{ $activeGroup }}">

                @foreach($settings as $setting)
                    @php
                        $inputName    = 'setting_' . $setting->key;
                        $oldValue     = old($inputName, $setting->value);
                        $hasImage     = !empty($setting->value);
                        $imageUrl     = $hasImage
                            ? route('media.show', ['path' => ltrim($setting->value, '/')])
                            : null;
                        $switchOn     = (string) $oldValue === '1';
                        $placeholderSize = $setting->key === 'site_favicon' ? '32×32 px' : '200×60 px';
                    @endphp

                    <div class="settings-field-group">

                        {{-- Label et description --}}
                        <label class="settings-field-label">
                            {{ $setting->label }}
                        </label>
                        @if($setting->description)
                            <p class="settings-field-description">{{ $setting->description }}</p>
                        @endif

                        {{-- ============================================ --}}
                        {{-- TEXT --}}
                        {{-- ============================================ --}}
                        @if($setting->type === 'text')
                            <input type="text" 
                                   name="{{ $inputName }}" 
                                   class="settings-input" 
                                   value="{{ $oldValue }}" 
                                   placeholder="{{ $setting->description }}"
                                   maxlength="255">

                        {{-- ============================================ --}}
                        {{-- TEXTAREA --}}
                        {{-- ============================================ --}}
                        @elseif($setting->type === 'textarea')
                            <textarea name="{{ $inputName }}" 
                                      class="settings-input settings-textarea" 
                                      rows="6" 
                                      maxlength="5000">{{ $oldValue }}</textarea>

                        {{-- ============================================ --}}
                        {{-- IMAGE --}}
                        {{-- ============================================ --}}
                        @elseif($setting->type === 'image')
                            <div class="settings-image-upload">
                                <label for="file_{{ $setting->key }}" class="settings-image-preview">
                                    <img id="preview_{{ $setting->key }}" 
                                         src="{{ $imageUrl ?? '' }}" 
                                         alt="{{ $setting->label }}" 
                                         class="{{ $hasImage ? 'is-visible' : 'is-hidden' }}"
                                         loading="lazy">
                                    <div id="placeholder_{{ $setting->key }}" 
                                         class="settings-image-placeholder {{ $hasImage ? 'is-hidden' : 'is-visible' }}">
                                        <i class="fas fa-image"></i>
                                        <span>{{ $placeholderSize }}</span>
                                    </div>
                                </label>
                                <div class="settings-image-info">
                                    <p class="settings-image-status">
                                        @if($hasImage)
                                            <span class="settings-image-status-ok">
                                                <i class="fas fa-check-circle"></i> Image actuelle
                                            </span>
                                        @else
                                            <span class="settings-image-status-empty">Aucune image définie</span>
                                        @endif
                                    </p>
                                    <ul class="settings-image-rules">
                                        <li><i class="fas fa-check-circle"></i> JPG, PNG, WebP, ICO ou SVG</li>
                                        <li><i class="fas fa-check-circle"></i> Taille max : 2 Mo</li>
                                    </ul>
                                    <div class="settings-image-actions">
                                        <button type="button" 
                                                class="btn btn-outline btn-sm" 
                                                onclick="document.getElementById('file_{{ $setting->key }}').click();">
                                            <i class="fas fa-upload"></i> Choisir un fichier
                                        </button>
                                        <button type="button" 
                                                id="removeBtn_{{ $setting->key }}" 
                                                class="btn btn-outline btn-sm btn-remove-image {{ $hasImage ? '' : 'is-hidden' }}"
                                                data-preview="preview_{{ $setting->key }}"
                                                data-placeholder="placeholder_{{ $setting->key }}"
                                                data-input="file_{{ $setting->key }}"
                                                data-remove-flag="remove_{{ $setting->key }}">
                                            <i class="fas fa-trash-alt"></i> Supprimer
                                        </button>
                                    </div>
                                    <input type="file" 
                                           name="{{ $inputName }}" 
                                           id="file_{{ $setting->key }}" 
                                           class="settings-image-input"
                                           data-preview="preview_{{ $setting->key }}"
                                           data-placeholder="placeholder_{{ $setting->key }}"
                                           data-remove-btn="removeBtn_{{ $setting->key }}"
                                           data-remove-flag="remove_{{ $setting->key }}"
                                           accept="image/jpeg,image/jpg,image/png,image/webp,image/x-icon,image/vnd.microsoft.icon,image/svg+xml"
                                           hidden>
                                    <input type="hidden" name="remove_{{ $setting->key }}" id="remove_{{ $setting->key }}" value="0">
                                </div>
                            </div>

                        {{-- ============================================ --}}
                        {{-- URL --}}
                        {{-- ============================================ --}}
                        @elseif($setting->type === 'url')
                            <div class="settings-input-icon">
                                <i class="fas fa-link"></i>
                                <input type="url" 
                                       name="{{ $inputName }}" 
                                       class="settings-input" 
                                       value="{{ $oldValue }}" 
                                       placeholder="https://"
                                       maxlength="255">
                            </div>

                        {{-- ============================================ --}}
                        {{-- EMAIL --}}
                        {{-- ============================================ --}}
                        @elseif($setting->type === 'email')
                            <div class="settings-input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" 
                                       name="{{ $inputName }}" 
                                       class="settings-input" 
                                       value="{{ $oldValue }}" 
                                       placeholder="phovemonde@gmail.com"
                                       maxlength="255">
                            </div>

                        {{-- ============================================ --}}
                        {{-- PHONE --}}
                        {{-- ============================================ --}}
                        @elseif($setting->type === 'phone')
                            <div class="settings-input-icon">
                                <i class="fas fa-phone"></i>
                                <input type="text" 
                                       name="{{ $inputName }}" 
                                       class="settings-input" 
                                       value="{{ $oldValue }}" 
                                       placeholder="+243 000 000 000"
                                       maxlength="30">
                            </div>

                        {{-- ============================================ --}}
                        {{-- SWITCH --}}
                        {{-- ============================================ --}}
                        @elseif($setting->type === 'switch')
                            <div class="settings-switch-row">
                                <label class="settings-switch">
                                    <input type="checkbox" 
                                           name="{{ $inputName }}" 
                                           value="1" 
                                           {{ $switchOn ? 'checked' : '' }}
                                           data-state="state_{{ $setting->key }}">
                                    <div class="settings-switch-track">
                                        <div class="settings-switch-thumb"></div>
                                    </div>
                                </label>
                                <span class="settings-switch-state {{ $switchOn ? 'is-on' : 'is-off' }}" 
                                      id="state_{{ $setting->key }}">
                                    {{ $switchOn ? 'Activé' : 'Désactivé' }}
                                </span>
                            </div>
                        @endif

                    </div>
                @endforeach

                {{-- Info --}}
                <div class="settings-info-tip">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Conseil :</strong> Les modifications sont appliquées immédiatement après l'enregistrement. 
                        Utilisez <strong>Ctrl+S</strong> pour sauvegarder rapidement.
                    </div>
                </div>
            </form>
        </div>

        {{-- Footer avec boutons --}}
        <div class="settings-card-footer">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('settingsForm').submit();">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>

    </div>

</div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-settings.js') }}"></script>
@endsection