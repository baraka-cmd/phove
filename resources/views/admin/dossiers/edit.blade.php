@extends('layouts.admin')

@section('title', 'Modifier le dossier | PHOVE')

@section('page_title', 'Modifier le dossier')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations du dossier : ' . $dossier->nomMembre())

@php
    $breadcrumb = [
        ['label' => 'Dossiers', 'url' => route('admin.dossiers.index')],
        ['label' => $dossier->nomMembre(), 'url' => route('admin.dossiers.show', $dossier)],
        'Modifier'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-edit"></i> Modifier le dossier
            </h2>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.dossiers.update', $dossier) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION 1 : Membre (lecture seule) --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-user"></i> Membre concerné
                </h4>

                <div class="projet-form-grid">
                    <div class="projet-form-group projet-form-full">
                        <label class="projet-form-label">Membre</label>
                        <input type="text" class="projet-form-input" value="{{ $dossier->nomMembre() }}" disabled readonly>
                        <span class="projet-form-hint">Le membre ne peut pas être modifié.</span>
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
                               value="{{ old('date_creation', $dossier->date_creation->format('Y-m-d')) }}" required>
                        @error('date_creation') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group">
                        <label for="statut" class="projet-form-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="projet-form-select @error('statut') error @enderror" required>
                            <option value="en_attente" {{ old('statut', $dossier->statut) === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="incomplet" {{ old('statut', $dossier->statut) === 'incomplet' ? 'selected' : '' }}>Incomplet</option>
                            <option value="complet" {{ old('statut', $dossier->statut) === 'complet' ? 'selected' : '' }}>Complet</option>
                        </select>
                        @error('statut') <span class="projet-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="projet-form-group projet-form-full">
                        <label for="notes" class="projet-form-label">Notes internes</label>
                        <textarea name="notes" id="notes" class="projet-form-textarea" rows="3" 
                                  placeholder="Notes sur le dossier...">{{ old('notes', $dossier->notes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : GESTION DES 12 PIÈCES --}}
            <div class="projet-form-section">
                <h4 class="projet-form-section-title">
                    <i class="fas fa-upload"></i> Gestion des 12 pièces exigées
                </h4>
                <p style="font-size: 13px; color: #64748B; margin-bottom: 16px;">
                    Importez de nouvelles pièces ou remplacez les pièces existantes. Laissez vide pour conserver la pièce actuelle.
                    Formats acceptés : JPG, PNG, WebP, PDF, DOC, DOCX — Max 5 Mo.
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    @foreach($dossier->documents as $document)
                    <div style="border: 2px dashed {{ $document->estFourni() ? '#10B981' : '#F59E0B' }}; border-radius: 10px; padding: 14px 16px; background: {{ $document->estFourni() ? '#F0FDF4' : '#FEF3C7' }}; transition: all 0.3s ease;" 
                         onmouseover="this.style.borderColor='#1A5B9C';" 
                         onmouseout="this.style.borderColor='{{ $document->estFourni() ? '#10B981' : '#F59E0B' }}';">
                        <label for="edit_file_{{ $document->id }}" style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 6px;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #FFFFFF; display: flex; align-items: center; justify-content: center; color: {{ $document->estFourni() ? '#10B981' : '#F59E0B' }}; flex-shrink: 0;">
                                <i class="fas fa-{{ $document->estFourni() ? 'check-circle' : 'exclamation-triangle' }}"></i>
                            </div>
                            <div style="flex: 1;">
                                <span style="font-size: 13px; font-weight: 600; color: #1E293B;">{{ $document->typeLibelle() }}</span>
                                <span style="font-size: 11px; display: block; color: {{ $document->estFourni() ? '#10B981' : '#F59E0B' }};">
                                    @if($document->estFourni())
                                        ✓ Fourni le {{ $document->date_upload ? $document->date_upload->format('d/m/Y') : 'N/A' }} ({{ $document->extension() }})
                                    @else
                                        ⚠ Manquant — Cliquez pour importer
                                    @endif
                                </span>
                            </div>
                        </label>
                        <input type="file" name="documents[{{ $document->id }}]" id="edit_file_{{ $document->id }}" 
                               accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx" 
                               style="display: none;"
                               onchange="updateFileName(this, '{{ $document->id }}')">
                        <span id="fileName_{{ $document->id }}" style="font-size: 11px; color: #1A5B9C;">
                            @if($document->estFourni())
                                Cliquez pour remplacer le fichier
                            @else
                                Aucun fichier sélectionné
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="projet-form-actions">
                <a href="{{ route('admin.dossiers.show', $dossier) }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>

        {{-- Suppression --}}
        <form action="{{ route('admin.dossiers.destroy', $dossier) }}" method="POST" style="margin-top: 16px; text-align: right;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement ce dossier et tous ses documents ?');">
                <i class="fas fa-trash-alt"></i> Supprimer le dossier
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    <script>
        function updateFileName(input, documentId) {
            const fileNameSpan = document.getElementById('fileName_' + documentId);
            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                fileNameSpan.textContent = '✓ ' + fileName + ' (' + fileSize + ' Mo)';
                fileNameSpan.style.color = '#1A5B9C';
            }
        }
    </script>
@endsection