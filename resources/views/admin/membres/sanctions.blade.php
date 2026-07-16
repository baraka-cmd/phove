@extends('layouts.admin')

@section('title', 'Sanctions et suspensions')

@section('page_title', 'Sanctions & Suspensions')
@section('page_icon', 'ban')
@section('page_subtitle', 'Gérez les membres suspendus et exclus. Consultez l\'historique des sanctions.')

@php
    $breadcrumb = [
        ['label' => 'Membres', 'url' => route('admin.membres.index')],
        'Sanctions'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-membres.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI --}}
    {{-- ============================================ --}}
    <div class="membres-stats-grid">
        <div class="membre-stat-card">
            <div class="membre-stat-icon" style="background: var(--color-error-light); color: var(--color-accent);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $totalSanctions }}</div>
                <div class="membre-stat-label">Total sanctions</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon" style="background: var(--color-warning-light); color: #856404;">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $totalSuspendus }}</div>
                <div class="membre-stat-label">Membres suspendus</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon" style="background: var(--color-error-light); color: #721c24;">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">{{ $totalExclus }}</div>
                <div class="membre-stat-label">Membres exclus</div>
            </div>
        </div>
        <div class="membre-stat-card">
            <div class="membre-stat-icon" style="background: var(--color-info-light); color: var(--color-info);">
                <i class="fas fa-book"></i>
            </div>
            <div class="membre-stat-content">
                <div class="membre-stat-value">
                    {{ \App\Models\Membre::where('statut', 'actif')->count() }}
                </div>
                <div class="membre-stat-label">Membres actifs</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE D'OUTILS --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.membres.sanctions') }}" method="GET" class="membres-toolbar">
        <div class="membres-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" value="{{ request('recherche') }}" 
                   class="membres-search-input" placeholder="Rechercher par nom...">
        </div>
        <div class="membres-filtres">
            <select name="statut" class="membres-filtre-select" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                <option value="suspendu" {{ request('statut') === 'suspendu' ? 'selected' : '' }}>Suspendus</option>
                <option value="exclu" {{ request('statut') === 'exclu' ? 'selected' : '' }}>Exclus</option>
            </select>
        </div>
        @if(request('recherche') || request('statut'))
            <a href="{{ route('admin.membres.sanctions') }}" class="membre-action-btn" title="Réinitialiser">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>

    {{-- ============================================ --}}
    {{-- TABLEAU DES SANCTIONS --}}
    {{-- ============================================ --}}
    <div class="membres-table-card">
        @if($sanctions->count() > 0)
        <div class="table-responsive">
            <table class="table-membres">
                <thead>
                    <tr>
                        <th>Membre</th>
                        <th>Catégorie</th>
                        <th>Statut</th>
                        <th>Motif de la sanction</th>
                        <th>Date sanction</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sanctions as $membre)
                    <tr>
                        {{-- Identité --}}
                        <td>
                            <div class="membre-cell-identite">
                                <div class="membre-cell-avatar">
                                    @if($membre->photo)
                                        <img src="{{ asset('storage/' . $membre->photo) }}" alt="{{ $membre->nomComplet() }}">
                                    @else
                                        <span>{{ Str::upper(Str::substr($membre->nom, 0, 1) . Str::substr($membre->prenom ?? '', 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="membre-cell-nom">{{ $membre->nomComplet() }}</span>
                                    <small>{{ $membre->telephone }}</small>
                                </div>
                            </div>
                        </td>

                        {{-- Catégorie --}}
                        <td>
                            @php
                                $catBadge = match($membre->categorie) {
                                    'initiateur', 'fondateur' => 'danger',
                                    'adherent' => 'success',
                                    'honneur' => 'warning',
                                    'sympathisant' => 'info',
                                    default => 'primary'
                                };
                                $catLibelle = match($membre->categorie) {
                                    'initiateur' => 'Initiateur',
                                    'fondateur' => 'Fondateur',
                                    'adherent' => 'Adhérent',
                                    'honneur' => 'Honneur',
                                    'sympathisant' => 'Sympathisant',
                                    default => $membre->categorie
                                };
                            @endphp
                            <span class="membre-badge membre-badge-{{ $catBadge }}">{{ $catLibelle }}</span>
                        </td>

                        {{-- Statut --}}
                        <td>
                            <span class="membre-badge membre-badge-{{ $membre->statut === 'suspendu' ? 'warning' : 'danger' }}">
                                <i class="fas fa-{{ $membre->statut === 'suspendu' ? 'pause-circle' : 'ban' }}"></i>
                                {{ $membre->statut === 'suspendu' ? 'Suspendu' : 'Exclu' }}
                            </span>
                        </td>

                        {{-- Motif --}}
                        <td>
                            <div style="max-width: 250px;">
                                <span style="font-size: 13px; color: #555; line-height: 1.5;">
                                    {{ $membre->motif_sanction ?? '—' }}
                                </span>
                            </div>
                        </td>

                        {{-- Date sanction --}}
                        <td>
                            <span style="font-size: 13px;">
                                <i class="fas fa-calendar-alt" style="color: var(--color-text-muted); font-size: 11px;"></i> 
                                {{ $membre->date_sanction ? $membre->date_sanction->format('d/m/Y') : '—' }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                {{-- Voir --}}
                                <a href="{{ route('admin.membres.show', $membre) }}" 
                                   class="membre-action-btn view" 
                                   title="Voir la fiche">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Réintégrer --}}
                                <form action="{{ route('admin.membres.reintegrer', $membre) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="membre-action-btn edit" 
                                            title="Réintégrer le membre"
                                            style="color: var(--color-success); border-color: var(--color-success);"
                                            onclick="return confirm('Réintégrer {{ $membre->nomComplet() }} ? Son statut passera à Actif.');">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>

                                {{-- Modifier --}}
                                <a href="{{ route('admin.membres.edit', $membre) }}" 
                                   class="membre-action-btn" 
                                   title="Modifier le membre">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($sanctions->hasPages())
        <div class="membres-pagination">
            <span class="membres-pagination-info">
                Affichage de <strong>{{ $sanctions->firstItem() }}-{{ $sanctions->lastItem() }}</strong> sur <strong>{{ $sanctions->total() }}</strong> sanctions
            </span>
            {{ $sanctions->links('pagination.custom') }}
        </div>
        @endif

        @else
        <div class="membres-empty">
            <div class="membres-empty-icon">
                <i class="fas fa-check-circle" style="color: var(--color-success);"></i>
            </div>
            <h4>Aucune sanction active</h4>
            <p>Aucun membre n'est actuellement suspendu ou exclu.</p>
            <a href="{{ route('admin.membres.index') }}" class="btn btn-outline btn-sm mt-3">
                <i class="fas fa-users"></i> Voir tous les membres
            </a>
        </div>
        @endif
    </div>

{{-- ============================================ --}}
{{-- MODALE DE SUSPENSION --}}
{{-- ============================================ --}}
<div class="modal-overlay" id="suspendModal">
    <div class="modal" style="max-width: 520px;">
        <div class="modal-header">
            <h3 class="modal-title" style="color: #856404;">
                <i class="fas fa-pause-circle"></i> Suspendre le membre
            </h3>
            <button class="modal-close" id="closeSuspendModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size: 14px; color: #555; margin-bottom: 16px;">
                Vous allez suspendre : <strong id="suspendMembreNom">—</strong>
            </p>
            <form action="" method="POST" id="suspendForm">
                @csrf
                @method('PUT')
                
                {{-- L'input hidden manquant pour stocker la valeur du select si ce n'est pas "autre" --}}
                <input type="hidden" name="motif_sanction" id="motif_suspend_hidden" disabled>

                <div class="form-group">
                    <label for="motif_suspend_select" class="form-label">
                        Motif de la suspension <span class="required">*</span>
                    </label>
                    <select id="motif_suspend_select" class="form-select" onchange="toggleMotifSuspend()">
                        <option value="">Sélectionnez un motif...</option>
                        <optgroup label="Articles 8 & 35 — Fautes graves">
                            <option value="Détournement des fonds">Détournement des fonds</option>
                            <option value="Trahison et indiscrétion">Trahison et indiscrétion</option>
                            <option value="Ivresse pendant les heures de service">Ivresse pendant les heures de service</option>
                            <option value="Débauche">Débauche</option>
                            <option value="Harcèlement sexuel">Harcèlement sexuel</option>
                            <option value="Faux et usage de faux">Faux et usage de faux</option>
                            <option value="Violation des droits des bénéficiaires">Violation des droits des bénéficiaires</option>
                            <option value="Discrimination et exclusion">Discrimination et exclusion</option>
                        </optgroup>
                        <optgroup label="Article 10 — Fautes légères">
                            <option value="Manque de moralité">Manque de moralité</option>
                            <option value="Manque de crédibilité">Manque de crédibilité</option>
                            <option value="Refus de payer les cotisations mensuelles">Refus de payer les cotisations mensuelles</option>
                        </optgroup>
                        <optgroup label="Article 12 — Absences">
                            <option value="Absence non justifiée à 3 AG consécutives">Absence non justifiée à 3 AG consécutives</option>
                        </optgroup>
                        <optgroup label="Article 34 — Non-respect">
                            <option value="Non-paiement des cotisations dans les délais">Non-paiement des cotisations dans les délais</option>
                            <option value="Non-respect des statuts et du ROI">Non-respect des statuts et du ROI</option>
                            <option value="Comportement immoral, indiscipline notoire">Comportement immoral, indiscipline notoire</option>
                            <option value="Déclarations calomnieuses ou diffamatoires">Déclarations calomnieuses ou diffamatoires</option>
                        </optgroup>
                        <option value="autre">📝 Autre motif (à préciser)</option>
                    </select>
                </div>

                {{-- ID corrigé ici : motif_suspend_autre_group --}}
                <div class="form-group" id="motif_suspend_autre_group" style="display: none;">
                    <label for="motif_suspend" class="form-label">Précisez le motif <span class="required">*</span></label>
                    {{-- ID corrigé ici : motif_suspend --}}
                    <textarea id="motif_suspend" class="form-textarea" rows="3" 
                              placeholder="Décrivez le motif de la suspension..."></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 18px;">
                    <button type="button" class="btn btn-outline btn-sm" id="cancelSuspend">Annuler</button>
                    <button type="submit" class="btn btn-accent btn-sm">
                        <i class="fas fa-pause-circle"></i> Confirmer la suspension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODALE D'EXCLUSION --}}
{{-- ============================================ --}}
<div class="modal-overlay" id="exclureModal">
    <div class="modal" style="max-width: 520px;">
        <div class="modal-header">
            <h3 class="modal-title" style="color: var(--color-accent);">
                <i class="fas fa-ban"></i> Exclure le membre
            </h3>
            <button class="modal-close" id="closeExclureModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size: 14px; color: #555; margin-bottom: 6px;">
                Vous allez exclure définitivement : <strong id="exclureMembreNom">—</strong>
            </p>
            <p style="font-size: 12px; color: var(--color-accent); margin-bottom: 16px;">
                <i class="fas fa-exclamation-triangle"></i> Cette action est irréversible (sauf réintégration manuelle).
            </p>
            <form action="" method="POST" id="exclureForm">
                @csrf
                @method('PUT')

                {{-- L'input hidden pour l'exclusion --}}
                <input type="hidden" name="motif_sanction" id="motif_exclure_hidden" disabled>

                <div class="form-group">
                    <label for="motif_exclure_select" class="form-label">
                        Motif de l'exclusion <span class="required">*</span>
                    </label>
                    <select id="motif_exclure_select" class="form-select" onchange="toggleMotifExclure()">
                        <option value="">Sélectionnez un motif...</option>
                        <optgroup label="Articles 8 & 35 — Fautes graves">
                            <option value="Détournement établi des fonds">Détournement établi des fonds</option>
                            <option value="Trahison et indiscrétion">Trahison et indiscrétion</option>
                            <option value="Ivresse pendant les heures de service">Ivresse pendant les heures de service</option>
                            <option value="Débauche">Débauche</option>
                            <option value="Harcèlement sexuel">Harcèlement sexuel</option>
                            <option value="Faux et usage de faux">Faux et usage de faux</option>
                            <option value="Violation des droits des bénéficiaires">Violation des droits des bénéficiaires</option>
                            <option value="Immoralité">Immoralité</option>
                            <option value="Discrimination et exclusion">Discrimination et exclusion</option>
                        </optgroup>
                        <option value="autre">📝 Autre motif (à préciser)</option>
                    </select>
                </div>

                {{-- ID corrigé ici : motif_exclure_autre_group --}}
                <div class="form-group" id="motif_exclure_autre_group" style="display: none;">
                    <label for="motif_exclure" class="form-label">Précisez le motif <span class="required">*</span></label>
                    {{-- ID corrigé ici : motif_exclure --}}
                    <textarea id="motif_exclure" class="form-textarea" rows="3" 
                              placeholder="Décrivez le motif de l'exclusion..."></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 18px;">
                    <button type="button" class="btn btn-outline btn-sm" id="cancelExclure">Annuler</button>
                    <button type="submit" class="btn btn-accent btn-sm">
                        <i class="fas fa-ban"></i> Confirmer l'exclusion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-membres.js') }}"></script>
    <script>
        // ============================================================
// GESTION DU SELECT MOTIF SUSPENSION
// ============================================================
function toggleMotifSuspend() {
    const select = document.getElementById('motif_suspend_select');
    const autreGroup = document.getElementById('motif_suspend_autre_group');
    const hiddenInput = document.getElementById('motif_suspend_hidden');
    const textarea = document.getElementById('motif_suspend');
    
    if (!select || !autreGroup || !hiddenInput || !textarea) return;

    if (select.value === 'autre') {
        autreGroup.style.display = 'block';
        hiddenInput.removeAttribute('name');
        hiddenInput.disabled = true;
        
        textarea.setAttribute('name', 'motif_sanction');
        textarea.required = true;
        textarea.focus();
    } else if (select.value !== '') {
        autreGroup.style.display = 'none';
        hiddenInput.value = select.value;
        hiddenInput.setAttribute('name', 'motif_sanction');
        hiddenInput.disabled = false;
        
        textarea.removeAttribute('name');
        textarea.required = false;
        textarea.value = '';
    } else {
        autreGroup.style.display = 'none';
        hiddenInput.removeAttribute('name');
        hiddenInput.disabled = true;
        
        textarea.removeAttribute('name');
        textarea.required = false;
        textarea.value = '';
    }
}

// ============================================================
// GESTION DU SELECT MOTIF EXCLUSION
// ============================================================
function toggleMotifExclure() {
    const select = document.getElementById('motif_exclure_select');
    const autreGroup = document.getElementById('motif_exclure_autre_group');
    const hiddenInput = document.getElementById('motif_exclure_hidden');
    const textarea = document.getElementById('motif_exclure');
    
    if (!select || !autreGroup || !hiddenInput || !textarea) return;

    if (select.value === 'autre') {
        autreGroup.style.display = 'block';
        hiddenInput.removeAttribute('name');
        hiddenInput.disabled = true;
        
        textarea.setAttribute('name', 'motif_sanction');
        textarea.required = true;
        textarea.focus();
    } else if (select.value !== '') {
        autreGroup.style.display = 'none';
        hiddenInput.value = select.value;
        hiddenInput.setAttribute('name', 'motif_sanction');
        hiddenInput.disabled = false;
        
        textarea.removeAttribute('name');
        textarea.required = false;
        textarea.value = '';
    } else {
        autreGroup.style.display = 'none';
        hiddenInput.removeAttribute('name');
        hiddenInput.disabled = true;
        
        textarea.removeAttribute('name');
        textarea.required = false;
        textarea.value = '';
    }
}


        // ============================================================
        // MODALE SUSPENSION
        // ============================================================
        const suspendModal = document.getElementById('suspendModal');
        const suspendForm = document.getElementById('suspendForm');
        const suspendMembreNom = document.getElementById('suspendMembreNom');
        
        document.querySelectorAll('.btn-suspend-membre').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nom = this.getAttribute('data-nom');
                suspendForm.action = '/admin/membres/' + id + '/suspendre';
                suspendMembreNom.textContent = nom;
                
                // Réinitialiser le formulaire
                document.getElementById('motif_suspend_select').value = '';
                document.getElementById('motif_suspend_autre_group').style.display = 'none';
                document.getElementById('motif_suspend').value = '';
                document.getElementById('motif_suspend_hidden').value = '';
                document.getElementById('motif_suspend_hidden').disabled = true;
                document.getElementById('motif_suspend').removeAttribute('name');
                
                suspendModal.classList.add('active');
            });
        });

        document.getElementById('closeSuspendModal')?.addEventListener('click', () => suspendModal.classList.remove('active'));
        document.getElementById('cancelSuspend')?.addEventListener('click', () => suspendModal.classList.remove('active'));
        suspendModal?.addEventListener('click', function (e) { if (e.target === this) this.classList.remove('active'); });

        // ============================================================
        // MODALE EXCLUSION
        // ============================================================
        const exclureModal = document.getElementById('exclureModal');
        const exclureForm = document.getElementById('exclureForm');
        const exclureMembreNom = document.getElementById('exclureMembreNom');
        
        document.querySelectorAll('.btn-exclure-membre').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nom = this.getAttribute('data-nom');
                exclureForm.action = '/admin/membres/' + id + '/exclure';
                exclureMembreNom.textContent = nom;
                
                // Réinitialiser le formulaire
                document.getElementById('motif_exclure_select').value = '';
                document.getElementById('motif_exclure_autre_group').style.display = 'none';
                document.getElementById('motif_exclure').value = '';
                document.getElementById('motif_exclure_hidden').value = '';
                document.getElementById('motif_exclure_hidden').disabled = true;
                document.getElementById('motif_exclure').removeAttribute('name');
                
                exclureModal.classList.add('active');
            });
        });

        document.getElementById('closeExclureModal')?.addEventListener('click', () => exclureModal.classList.remove('active'));
        document.getElementById('cancelExclure')?.addEventListener('click', () => exclureModal.classList.remove('active'));
        exclureModal?.addEventListener('click', function (e) { if (e.target === this) this.classList.remove('active'); });
    </script>
@endsection