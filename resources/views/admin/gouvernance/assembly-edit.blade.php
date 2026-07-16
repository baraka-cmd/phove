@extends('layouts.admin')

@section('title', 'Modifier l\'Assemblée Générale')

@section('page_title', 'Modifier l\'Assemblée Générale')
@section('page_icon', 'edit')
@section('page_subtitle', 'Modifiez les informations de cette Assemblée Générale.')

@php
    $breadcrumb = [
        ['label' => 'Gouvernance', 'url' => route('admin.gouvernance.index')],
        ['label' => 'Assemblées Générales', 'url' => route('admin.gouvernance.assemblies')],
        'Modifier l\'AG'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance-assembly-edit.css') }}">
@endsection

@section('content')

    <div class="assembly-edit-card">

        {{-- En-tête --}}
        <div class="assembly-edit-header">
            <div class="assembly-edit-header-icon">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h2>Modifier l'Assemblée Générale</h2>
                <p>
                    {{ $assembly->type === 'ordinaire' ? 'AG Ordinaire' : 'AG Extraordinaire' }} 
                    du {{ \Carbon\Carbon::parse($assembly->date_reunion)->format('d/m/Y') }}
                </p>
            </div>
        </div>

        {{-- Messages d'erreur --}}
        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.gouvernance.assemblies.update', $assembly) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- SECTION 1 : Informations générales --}}
            <div class="assembly-edit-section">
                <h4 class="assembly-edit-section-title">
                    <i class="fas fa-info-circle"></i> Informations générales
                </h4>

                <div class="assembly-edit-grid">
                    <div class="assembly-edit-group">
                        <label for="type" class="assembly-edit-label">Type d'AG <span class="required">*</span></label>
                        <select name="type" id="type" class="assembly-edit-select @error('type') error @enderror" required>
                            <option value="">Sélectionnez...</option>
                            <option value="ordinaire" {{ old('type', $assembly->type) === 'ordinaire' ? 'selected' : '' }}>Ordinaire (janvier de chaque année)</option>
                            <option value="extraordinaire" {{ old('type', $assembly->type) === 'extraordinaire' ? 'selected' : '' }}>Extraordinaire (urgence)</option>
                        </select>
                        @error('type') <span class="assembly-edit-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="assembly-edit-group">
                        <label for="statut" class="assembly-edit-label">Statut <span class="required">*</span></label>
                        <select name="statut" id="statut" class="assembly-edit-select @error('statut') error @enderror" required>
                            <option value="planifiee" {{ old('statut', $assembly->statut) === 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                            <option value="tenue" {{ old('statut', $assembly->statut) === 'tenue' ? 'selected' : '' }}>Tenue</option>
                            <option value="annulee" {{ old('statut', $assembly->statut) === 'annulee' ? 'selected' : '' }}>Annulée</option>
                            <option value="reportee" {{ old('statut', $assembly->statut) === 'reportee' ? 'selected' : '' }}>Reportée</option>
                        </select>
                    </div>

                    <div class="assembly-edit-group">
                        <label for="date_reunion" class="assembly-edit-label">Date de réunion <span class="required">*</span></label>
                        <input type="date" name="date_reunion" id="date_reunion" 
                               class="assembly-edit-input @error('date_reunion') error @enderror" 
                               value="{{ old('date_reunion', \Carbon\Carbon::parse($assembly->date_reunion)->format('Y-m-d')) }}" required>
                        @error('date_reunion') <span class="assembly-edit-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>

                    <div class="assembly-edit-group">
                        <label for="lieu" class="assembly-edit-label">Lieu <span class="required">*</span></label>
                        <input type="text" name="lieu" id="lieu" 
                               class="assembly-edit-input @error('lieu') error @enderror" 
                               value="{{ old('lieu', $assembly->lieu) }}" placeholder="Ex: Siège social, Minova" required>
                        @error('lieu') <span class="assembly-edit-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2 : Participants --}}
            <div class="assembly-edit-section">
                <h4 class="assembly-edit-section-title">
                    <i class="fas fa-users"></i> Participants
                </h4>

                <div class="assembly-edit-grid">
                    <div class="assembly-edit-group">
                        <label for="membres_total" class="assembly-edit-label">Nombre total de membres effectifs</label>
                        <input type="number" name="membres_total" id="membres_total" 
                               class="assembly-edit-input" 
                               value="{{ old('membres_total', $assembly->membres_total) }}" min="0" placeholder="Ex: 25">
                    </div>

                    <div class="assembly-edit-group">
                        <label for="membres_presents" class="assembly-edit-label">Membres présents</label>
                        <input type="number" name="membres_presents" id="membres_presents" 
                               class="assembly-edit-input" 
                               value="{{ old('membres_presents', $assembly->membres_presents) }}" min="0" placeholder="Ex: 18">
                    </div>

                    <div class="assembly-edit-group">
                        <label for="membres_absents" class="assembly-edit-label">Membres absents</label>
                        <input type="number" name="membres_absents" id="membres_absents" 
                               class="assembly-edit-input" 
                               value="{{ old('membres_absents', $assembly->membres_absents) }}" min="0" placeholder="Ex: 7">
                    </div>

                    {{-- Info quorum --}}
                    <div class="assembly-edit-group" style="display: flex; align-items: center; justify-content: center;">
                        @php
                            $total = $assembly->membres_total ?? 0;
                            $presents = $assembly->membres_presents ?? 0;
                            $quorumMinimum = $total > 0 ? ceil($total / 2) : 0;
                            $atteint = $total > 0 && $presents >= $quorumMinimum;
                            $pourcentage = $total > 0 ? round(($presents / $total) * 100) : 0;
                        @endphp
                        <div id="quorumInfo" class="quorum-info-box {{ $total > 0 ? ($atteint ? 'quorum-info-atteint' : 'quorum-info-non-atteint') : 'quorum-info-default' }}">
                            @if($total > 0)
                                <i class="fas fa-{{ $atteint ? 'check-circle' : 'times-circle' }}"></i>
                                Quorum {{ $atteint ? 'atteint' : 'non atteint' }} : {{ $presents }}/{{ $total }} présents ({{ $pourcentage }}%)
                                — Majorité simple requise : {{ $quorumMinimum }} membres
                            @else
                                <i class="fas fa-info-circle"></i> Remplissez les champs pour voir le quorum
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Contenu --}}
            <div class="assembly-edit-section">
                <h4 class="assembly-edit-section-title">
                    <i class="fas fa-file-alt"></i> Contenu de l'AG
                </h4>

                <div class="assembly-edit-group assembly-edit-full">
                    <label for="ordre_du_jour" class="assembly-edit-label">Ordre du jour</label>
                    <textarea name="ordre_du_jour" id="ordre_du_jour" class="assembly-edit-textarea" rows="3" 
                              placeholder="Points à l'ordre du jour...">{{ old('ordre_du_jour', $assembly->ordre_du_jour) }}</textarea>
                </div>

                <div class="assembly-edit-group assembly-edit-full">
                    <label for="decisions" class="assembly-edit-label">Décisions prises</label>
                    <textarea name="decisions" id="decisions" class="assembly-edit-textarea" rows="3" 
                              placeholder="Décisions adoptées lors de l'AG...">{{ old('decisions', $assembly->decisions) }}</textarea>
                </div>

                <div class="assembly-edit-group assembly-edit-full">
                    <label for="resolutions" class="assembly-edit-label">Résolutions</label>
                    <textarea name="resolutions" id="resolutions" class="assembly-edit-textarea" rows="3" 
                              placeholder="Résolutions votées...">{{ old('resolutions', $assembly->resolutions) }}</textarea>
                </div>
            </div>

            {{-- SECTION 4 : PV --}}
            <div class="assembly-edit-section">
                <h4 class="assembly-edit-section-title">
                    <i class="fas fa-file-pdf"></i> Procès-verbal
                </h4>

                {{-- PV actuel --}}
                <div class="assembly-current-pv">
                    <i class="fas fa-file-pdf"></i>
                    @if($assembly->pv_fichier)
                        <span>PV actuel : <a href="{{ asset('storage/' . $assembly->pv_fichier) }}" target="_blank">
                            <i class="fas fa-download"></i> Télécharger le PV
                        </a></span>
                    @else
                        <span class="no-pv">Aucun PV enregistré pour cette AG.</span>
                    @endif
                </div>

                <div class="assembly-edit-group assembly-edit-full">
                    <label for="pv_fichier" class="assembly-edit-label">
                        @if($assembly->pv_fichier) Remplacer le PV (PDF) @else Ajouter un PV (PDF) @endif
                    </label>
                    <input type="file" name="pv_fichier" id="pv_fichier" class="assembly-file-input" accept=".pdf">
                    <small style="color: var(--color-text-muted); font-size: 11px; display: block; margin-top: 4px;">
                        Format PDF uniquement — Max 10 Mo. Laissez vide pour conserver le PV actuel.
                    </small>
                    @error('pv_fichier') <span class="assembly-edit-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Actions (DANS le formulaire) --}}
            <div class="assembly-edit-actions">
                <div>
                    {{-- Le bouton supprimer n'est pas un submit de ce formulaire --}}
                </div>
                <div class="assembly-edit-actions-right">
                    <a href="{{ route('admin.gouvernance.assemblies') }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-arrow-left"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>

        {{-- Formulaire de suppression SÉPARÉ (en dehors) --}}
        <form action="{{ route('admin.gouvernance.assemblies.destroy', $assembly) }}" method="POST" style="margin-top: 12px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-accent btn-sm" 
                    onclick="return confirm('Supprimer définitivement cette AG ?');">
                <i class="fas fa-trash-alt"></i> Supprimer cette AG
            </button>
        </form>

    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-gouvernance-assembly-edit.js') }}"></script>
@endsection