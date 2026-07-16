@extends('layouts.admin')

@section('title', 'Nouvelle Assemblée Générale')

@section('page_title', 'Nouvelle Assemblée Générale')
@section('page_icon', 'users-cog')
@section('page_subtitle', 'Enregistrez une nouvelle Assemblée Générale (ordinaire ou extraordinaire).')

@php
    $breadcrumb = [
        ['label' => 'Gouvernance', 'url' => route('admin.gouvernance.index')],
        ['label' => 'Assemblées Générales', 'url' => route('admin.gouvernance.assemblies')],
        'Nouvelle AG'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance-assembly-form.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance.css') }}">
@endsection

@section('content')

    <div class="assembly-form-card">
        
        {{-- En-tête --}}
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f0;">
            <div style="width: 50px; height: 50px; border-radius: 14px; background: var(--color-primary); color: #fff; font-size: 1.3rem; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <h2 style="font-size: 1.3rem; font-weight: 700; color: #1a1a1a;">Nouvelle Assemblée Générale</h2>
                <p style="font-size: 13px; color: #888; margin: 4px 0 0 0;">Remplissez les informations relatives à l'Assemblée Générale.</p>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Veuillez corriger les erreurs ci-dessous.</span>
        </div>
        @endif

        <form action="{{ route('admin.gouvernance.assemblies.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- SECTION 1 : Informations générales --}}
            <h4 style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin-bottom: 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter);">
                <i class="fas fa-info-circle" style="color: var(--color-primary);"></i> Informations générales
            </h4>

            <div class="assembly-form-grid">
                <div class="assembly-form-group">
                    <label for="type" class="assembly-form-label">Type d'AG <span class="required">*</span></label>
                    <select name="type" id="type" class="assembly-form-select @error('type') error @enderror" required>
                        <option value="">Sélectionnez...</option>
                        <option value="ordinaire" {{ old('type') === 'ordinaire' ? 'selected' : '' }}>Ordinaire (janvier de chaque année)</option>
                        <option value="extraordinaire" {{ old('type') === 'extraordinaire' ? 'selected' : '' }}>Extraordinaire (urgence)</option>
                    </select>
                    @error('type') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="assembly-form-group">
                    <label for="statut" class="assembly-form-label">Statut <span class="required">*</span></label>
                    <select name="statut" id="statut" class="assembly-form-select @error('statut') error @enderror" required>
                        <option value="planifiee" {{ old('statut', 'planifiee') === 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                        <option value="tenue" {{ old('statut') === 'tenue' ? 'selected' : '' }}>Tenue</option>
                        <option value="annulee" {{ old('statut') === 'annulee' ? 'selected' : '' }}>Annulée</option>
                        <option value="reportee" {{ old('statut') === 'reportee' ? 'selected' : '' }}>Reportée</option>
                    </select>
                </div>

                <div class="assembly-form-group">
                    <label for="date_reunion" class="assembly-form-label">Date de réunion <span class="required">*</span></label>
                    <input type="date" name="date_reunion" id="date_reunion" class="assembly-form-input @error('date_reunion') error @enderror" value="{{ old('date_reunion', date('Y-m-d')) }}" required>
                    @error('date_reunion') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>

                <div class="assembly-form-group">
                    <label for="lieu" class="assembly-form-label">Lieu <span class="required">*</span></label>
                    <input type="text" name="lieu" id="lieu" class="assembly-form-input @error('lieu') error @enderror" value="{{ old('lieu') }}" placeholder="Ex: Siège social, Minova" required>
                    @error('lieu') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
                </div>
            </div>

            {{-- SECTION 2 : Participants --}}
            <h4 style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 28px 0 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter);">
                <i class="fas fa-users" style="color: var(--color-primary);"></i> Participants
            </h4>

            <div class="assembly-form-grid">
                <div class="assembly-form-group">
                    <label for="membres_total" class="assembly-form-label">Nombre total de membres effectifs</label>
                    <input type="number" name="membres_total" id="membres_total" class="assembly-form-input" value="{{ old('membres_total') }}" min="0" placeholder="Ex: 25">
                </div>

                <div class="assembly-form-group">
                    <label for="membres_presents" class="assembly-form-label">Membres présents</label>
                    <input type="number" name="membres_presents" id="membres_presents" class="assembly-form-input" value="{{ old('membres_presents') }}" min="0" placeholder="Ex: 18">
                </div>

                <div class="assembly-form-group">
                    <label for="membres_absents" class="assembly-form-label">Membres absents</label>
                    <input type="number" name="membres_absents" id="membres_absents" class="assembly-form-input" value="{{ old('membres_absents') }}" min="0" placeholder="Ex: 7">
                </div>

                {{-- Info quorum --}}
                <div class="assembly-form-group" style="display: flex; align-items: center; justify-content: center;">
                    <div id="quorumInfo" style="padding: 14px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; text-align: center; width: 100%; background: var(--color-bg-light); color: #888;">
                        <i class="fas fa-info-circle"></i> Remplissez les champs pour voir le quorum
                    </div>
                </div>
            </div>

            {{-- SECTION 3 : Contenu --}}
            <h4 style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 28px 0 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter);">
                <i class="fas fa-file-alt" style="color: var(--color-primary);"></i> Contenu de l'AG
            </h4>

            <div class="assembly-form-group assembly-form-full">
                <label for="ordre_du_jour" class="assembly-form-label">Ordre du jour</label>
                <textarea name="ordre_du_jour" id="ordre_du_jour" class="assembly-form-textarea" rows="3" placeholder="Points à l'ordre du jour...">{{ old('ordre_du_jour') }}</textarea>
            </div>

            <div class="assembly-form-group assembly-form-full">
                <label for="decisions" class="assembly-form-label">Décisions prises</label>
                <textarea name="decisions" id="decisions" class="assembly-form-textarea" rows="3" placeholder="Décisions adoptées lors de l'AG...">{{ old('decisions') }}</textarea>
            </div>

            <div class="assembly-form-group assembly-form-full">
                <label for="resolutions" class="assembly-form-label">Résolutions</label>
                <textarea name="resolutions" id="resolutions" class="assembly-form-textarea" rows="3" placeholder="Résolutions votées...">{{ old('resolutions') }}</textarea>
            </div>

            {{-- SECTION 4 : PV --}}
            <h4 style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 28px 0 18px; padding-bottom: 10px; border-bottom: 2px solid var(--color-primary-lighter);">
                <i class="fas fa-file-pdf" style="color: var(--color-primary);"></i> Procès-verbal
            </h4>

            <div class="assembly-form-group assembly-form-full">
                <label for="pv_fichier" class="assembly-form-label">Fichier PV (PDF)</label>
                <input type="file" name="pv_fichier" id="pv_fichier" class="assembly-form-input" accept=".pdf" style="padding: 8px;">
                <small style="color: var(--color-text-muted); font-size: 11px;">Format PDF uniquement — Max 10 Mo</small>
                @error('pv_fichier') <span class="membre-form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span> @enderror
            </div>

            {{-- Actions --}}
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
    <script src="{{ asset('assets/js/admin-gouvernance.js') }}"></script>
    <script>
        // Calcul automatique du quorum
        const membresTotal = document.getElementById('membres_total');
        const membresPresents = document.getElementById('membres_presents');
        const quorumInfo = document.getElementById('quorumInfo');

        function updateQuorum() {
            const total = parseInt(membresTotal.value) || 0;
            const presents = parseInt(membresPresents.value) || 0;

            if (total > 0 && presents >= 0) {
                const quorum = total / 2;
                const atteint = presents >= quorum;
                const pourcentage = total > 0 ? Math.round((presents / total) * 100) : 0;

                quorumInfo.style.background = atteint ? 'var(--color-success-light)' : 'var(--color-error-light)';
                quorumInfo.style.color = atteint ? 'var(--color-success)' : 'var(--color-accent)';
                quorumInfo.innerHTML = '<i class="fas fa-' + (atteint ? 'check-circle' : 'times-circle') + '"></i> ' +
                    'Quorum ' + (atteint ? 'atteint' : 'non atteint') + ' : ' + presents + '/' + total + ' présents (' + pourcentage + '%)' +
                    ' — Majorité simple requise : ' + Math.ceil(quorum) + ' membres';
            } else {
                quorumInfo.style.background = 'var(--color-bg-light)';
                quorumInfo.style.color = '#888';
                quorumInfo.innerHTML = '<i class="fas fa-info-circle"></i> Remplissez les champs pour voir le quorum';
            }
        }

        if (membresTotal && membresPresents) {
            membresTotal.addEventListener('input', updateQuorum);
            membresPresents.addEventListener('input', updateQuorum);
            updateQuorum(); // Calcul initial
        }
    </script>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-gouvernance-assembly-form.js') }}"></script>
@endsection