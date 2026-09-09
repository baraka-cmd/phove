@extends('layouts.admin')

@section('title', 'Créer un dossier | PHOVE')

@section('page_title', 'Créer un dossier personnel')
@section('page_icon', 'plus-circle')
@section('page_subtitle', 'Créez un nouveau dossier personnel et importez les 12 pièces exigées (Article 65 des Statuts).')

@php
    $breadcrumb = [
        ['label' => 'Dossiers', 'url' => route('admin.dossiers.index')],
        'Créer un dossier'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-folder-plus"></i> Nouveau dossier personnel
            </h2>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        @if($membresSansDossier->count() > 0)
        <form action="{{ route('admin.dossiers.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- SECTION 1 : Membre --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-user"></i> Membre concerné
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label for="membre_id" class="projet-form-label">Sélectionnez le membre <span class="required">*</span></label>
                        <select name="membre_id" id="membre_id" class="projet-form-select @error('membre_id') error @enderror" required>
                            <option value="">Sélectionnez un membre...</option>
                            @foreach($membresSansDossier as $membre)
                                <option value="{{ $membre->id }}" {{ old('membre_id') == $membre->id ? 'selected' : '' }}>
                                    {{ $membre->nomComplet() }} — {{ $membre->categorieLibelle() }}
                                </option>
                            @endforeach
                        </select>
                        @error('membre_id') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                        <span class="projet-form-hint">
                            Seuls les membres actifs sans dossier personnel sont listés.
                        </span>
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Informations du dossier --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-info-circle"></i> Informations du dossier
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group">
                        <label for="date_creation" class="projet-form-label">Date de création <span class="required">*</span></label>
                        <input type="date" name="date_creation" id="date_creation" class="projet-form-input @error('date_creation') error @enderror" 
                               value="{{ old('date_creation', date('Y-m-d')) }}" required>
                        @error('date_creation') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="notes" class="projet-form-label">Notes internes</label>
                        <textarea name="notes" id="notes" class="projet-form-textarea" rows="3" 
                                  placeholder="Notes sur le dossier...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : IMPORT DES 12 PIÈCES --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-upload"></i> Import des 12 pièces exigées
                </h4>
                <p style="font-size: 13px; color: #64748B; margin-bottom: 16px;">
                    Importez les pièces disponibles maintenant. Les pièces manquantes pourront être ajoutées plus tard.
                    Formats acceptés : JPG, PNG, WebP, PDF, DOC, DOCX — Max 5 Mo par fichier.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    @foreach($typesDocuments as $type => $label)
                    <div style="border: 2px dashed #CBD5E1; border-radius: 10px; padding: 14px 16px; background: #F8FAFC; transition: all 0.3s ease;" 
                         onmouseover="this.style.borderColor='#1A5B9C'; this.style.background='#E8F0F8';" 
                         onmouseout="this.style.borderColor='#CBD5E1'; this.style.background='#F8FAFC';">
                        <label for="{{ $type }}" style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 4px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #FFFFFF; display: flex; align-items: center; justify-content: center; color: #1A5B9C; flex-shrink: 0;">
                                <i class="fas fa-file-upload"></i>
                            </div>
                            <div style="flex: 1;">
                                <span style="font-size: 13px; font-weight: 600; color: #1E293B;">{{ $label }}</span>
                                <span style="font-size: 11px; color: #64748B; display: block;">
                                    Cliquez pour sélectionner un fichier
                                </span>
                            </div>
                        </label>
                        <input type="file" name="{{ $type }}" id="{{ $type }}" 
                               accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" 
                               style="display: none;"
                               onchange="updateFileName(this, '{{ $type }}')">
                        <span id="fileName_{{ $type }}" style="font-size: 11px; color: #10B981;">
                            Aucun fichier sélectionné
                        </span>
                        @error($type)
                            <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.dossiers.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-folder-plus"></i> Créer le dossier
                </button>
            </div>
        </form>
        @else
        <div style="text-align: center; padding: 40px 20px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #D1FAE5; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: #10B981;"></i>
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #1E293B; margin-bottom: 8px;">
                Tous les membres ont déjà un dossier
            </h3>
            <p style="font-size: 13px; color: #64748B; margin-bottom: 20px;">
                Tous les membres actifs disposent déjà d'un dossier personnel.
            </p>
            <a href="{{ route('admin.dossiers.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Retour à la liste des dossiers
            </a>
        </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script>
        // Mettre à jour le nom du fichier sélectionné
        function updateFileName(input, type) {
            const fileNameSpan = document.getElementById('fileName_' + type);
            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                fileNameSpan.textContent = '✓ ' + fileName + ' (' + fileSize + ' Mo)';
                fileNameSpan.style.color = '#10B981';
            } else {
                fileNameSpan.textContent = 'Aucun fichier sélectionné';
                fileNameSpan.style.color = '#10B981';
            }
        }
    </script>
@endsection