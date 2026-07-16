@extends('layouts.admin')

@section('title', 'Fiche membre : ' . $membre->nomComplet())

@section('page_title', 'Fiche détaillée')
@section('page_icon', 'address-card')
@section('page_subtitle', 'Informations complètes du membre ' . $membre->nomComplet())

@php
    $breadcrumb = [
        ['label' => 'Membres', 'url' => route('admin.membres.index')],
        $membre->nomComplet()
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-membre-show.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-membres.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE DU MEMBRE --}}
    {{-- ============================================ --}}
    <div class="membre-show-header">
        <div class="membre-show-avatar">
            @if($membre->photo)
                <img src="{{ asset('storage/' . $membre->photo) }}" alt="{{ $membre->nomComplet() }}">
            @else
                <span>{{ Str::upper(Str::substr($membre->nom, 0, 1) . Str::substr($membre->prenom, 0, 1)) }}</span>
            @endif
        </div>
        <div class="membre-show-info">
            <h2>{{ $membre->nomComplet() }}</h2>
            <p>
                <span class="membre-badge membre-badge-{{ $membre->categorie === 'fondateur' || $membre->categorie === 'initiateur' ? 'danger' : ($membre->categorie === 'adherent' ? 'success' : 'primary') }}">
                    <i class="fas fa-tag"></i>
                    @php
                        echo match($membre->categorie) {
                            'initiateur' => 'Initiateur',
                            'fondateur' => 'Fondateur',
                            'adherent' => 'Adhérent',
                            'honneur' => 'Honneur',
                            'sympathisant' => 'Sympathisant',
                            default => $membre->categorie
                        };
                    @endphp
                </span>
                <span class="membre-badge membre-badge-{{ $membre->statut === 'actif' ? 'success' : ($membre->statut === 'en_attente' ? 'warning' : 'danger') }}">
                    <i class="fas fa-circle"></i>
                    @php
                        echo match($membre->statut) {
                            'actif' => 'Actif',
                            'en_attente' => 'En attente',
                            'suspendu' => 'Suspendu',
                            'exclu' => 'Exclu',
                            'deserteur' => 'Déserteur',
                            'demissionnaire' => 'Démissionnaire',
                            default => $membre->statut
                        };
                    @endphp
                </span>
                <span style="font-size: 13px; color: var(--color-text-muted);">
                    <i class="fas fa-calendar-alt"></i> Adhéré le {{ $membre->date_adhesion ? $membre->date_adhesion->format('d/m/Y') : '—' }}
                </span>
            </p>
        </div>
        <div style="margin-left: auto; display: flex; gap: 8px;">
            <a href="{{ route('admin.membres.edit', $membre) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <form action="{{ route('admin.membres.destroy', $membre) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-accent btn-sm btn-delete-membre" data-nom="{{ $membre->nomComplet() }}" onclick="return confirm('Supprimer définitivement {{ $membre->nomComplet() }} ?');">
                    <i class="fas fa-trash-alt"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRILLE DE DÉTAIL --}}
    {{-- ============================================ --}}
    <div class="membre-show-grid">
        
        {{-- COLONNE GAUCHE --}}
        <div>
            {{-- Carte : Informations personnelles --}}
            <div class="membre-detail-card">
                <div class="membre-detail-card-header">
                    <i class="fas fa-user"></i> Informations personnelles
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Nom complet</span>
                        <span class="membre-detail-value">{{ $membre->nomComplet() }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Sexe</span>
                        <span class="membre-detail-value">{{ $membre->sexe === 'M' ? 'Masculin' : 'Féminin' }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Catégorie</span>
                        <span class="membre-detail-value">
                            @php
                                echo match($membre->categorie) {
                                    'initiateur' => 'Initiateur',
                                    'fondateur' => 'Fondateur',
                                    'adherent' => 'Adhérent',
                                    'honneur' => 'Honneur',
                                    'sympathisant' => 'Sympathisant',
                                    default => $membre->categorie
                                };
                            @endphp
                        </span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Statut</span>
                        <span class="membre-detail-value">{{ ucfirst($membre->statut) }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Date d'adhésion</span>
                        <span class="membre-detail-value">{{ $membre->date_adhesion ? $membre->date_adhesion->format('d/m/Y') : '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Carte : Coordonnées --}}
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-address-book"></i> Coordonnées
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Adresse</span>
                        <span class="membre-detail-value">{{ $membre->adresse }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Téléphone</span>
                        <span class="membre-detail-value">{{ $membre->telephone }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Email</span>
                        <span class="membre-detail-value">{{ $membre->email ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Carte : Finances --}}
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-coins"></i> Informations financières
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Frais d'adhésion</span>
                        <span class="membre-detail-value">{{ number_format($membre->frais_adhesion, 2) }} $</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Cotisation mensuelle</span>
                        <span class="membre-detail-value">{{ number_format($membre->cotisation_mensuelle, 2) }} $</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Total cotisations payées</span>
                        <span class="membre-detail-value" style="color: var(--color-success); font-size: 1.1rem;">{{ number_format($totalCotisations, 2) }} $</span>
                    </div>
                </div>
            </div>

            {{-- Carte : Postes occupés --}}
            @if($membre->organes && $membre->organes->count() > 0)
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-sitemap"></i> Postes dans les organes
                </div>
                <div class="membre-detail-card-body">
                    @foreach($membre->organes as $organe)
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">
                            @php
                                echo match($organe->organe) {
                                    'assemblee_generale' => 'Assemblée Générale',
                                    'conseil_administration' => 'Conseil d\'Administration',
                                    'direction_executive' => 'Direction Exécutive',
                                    'commission_controle' => 'Commission de Contrôle',
                                    default => $organe->organe
                                };
                            @endphp
                        </span>
                        <span class="membre-detail-value">
                            {{ $organe->poste }}
                            @if($organe->actif)
                                <span class="membre-badge membre-badge-success" style="margin-left: 6px;">Actif</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- COLONNE DROITE --}}
        <div>
            {{-- Carte : Sanctions --}}
            @if($membre->motif_sanction)
            <div class="membre-detail-card" style="border-left: 4px solid var(--color-accent);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-ban" style="color: var(--color-accent);"></i> Sanction en cours
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Motif</span>
                        <span class="membre-detail-value">{{ $membre->motif_sanction }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Date</span>
                        <span class="membre-detail-value">{{ $membre->date_sanction ? $membre->date_sanction->format('d/m/Y') : '—' }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Carte : Historique des cotisations --}}
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-history"></i> Historique des cotisations
                </div>
                <div class="membre-detail-card-body" style="padding: 0;">
                    @if($membre->cotisations && $membre->cotisations->count() > 0)
                    <div class="table-responsive">
                        <table class="membre-cotisations-table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($membre->cotisations->take(12) as $cotisation)
                                <tr>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $cotisation->mois)->format('F Y') }}</td>
                                    <td style="font-weight: 600;">{{ number_format($cotisation->montant, 2) }} $</td>
                                    <td>{{ $cotisation->modePaiementLibelle() }}</td>
                                    <td>{{ $cotisation->date_paiement ? $cotisation->date_paiement->format('d/m/Y') : '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div style="padding: 24px; text-align: center; color: var(--color-text-muted);">
                        <i class="fas fa-info-circle"></i> Aucune cotisation enregistrée pour le moment.
                    </div>
                    @endif
                </div>
            </div>


            {{-- Carte : Historique des sanctions --}}
            @if($membre->motif_sanction)
            <div class="membre-detail-card sanction" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-history"></i> Historique des sanctions
                </div>
                <div class="membre-detail-card-body">
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Statut actuel</span>
                        <span class="membre-detail-value" style="color: {{ $membre->statut === 'suspendu' ? '#856404' : '#721c24' }}; font-weight: 700;">
                            {{ $membre->statut === 'suspendu' ? 'Suspendu' : 'Exclu' }}
                        </span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Motif</span>
                        <span class="membre-detail-value">{{ $membre->motif_sanction }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Date de sanction</span>
                        <span class="membre-detail-value">{{ $membre->date_sanction ? $membre->date_sanction->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="membre-detail-row">
                        <span class="membre-detail-label">Durée de la sanction</span>
                        <span class="membre-detail-value">
                            @if($membre->date_sanction)
                                {{ $membre->date_sanction->diffInDays(now()) }} jours
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @endif
            {{-- Carte : Actions rapides --}}
            {{-- Carte : Actions administratives --}}
            <div class="membre-detail-card" style="margin-top: var(--spacing-xl);">
                <div class="membre-detail-card-header">
                    <i class="fas fa-bolt"></i> Actions administratives
                </div>
                <div class="membre-detail-card-body" style="display: flex; flex-direction: column; gap: 10px;">
                    
                    {{-- Modifier --}}
                    <a href="{{ route('admin.membres.edit', $membre) }}" class="membre-action-link edit">
                        <i class="fas fa-edit"></i> Modifier ce membre
                    </a>

                    {{-- Selon le statut actuel --}}
                    @if($membre->statut === 'actif')
                        {{-- Suspendre --}}
                        <button class="membre-action-link suspend btn-suspend-membre" 
                                data-id="{{ $membre->id }}" 
                                data-nom="{{ $membre->nomComplet() }}">
                            <i class="fas fa-pause-circle"></i> Suspendre ce membre
                        </button>
                        {{-- Exclure --}}
                        <button class="membre-action-link suspend btn-exclure-membre" 
                                data-id="{{ $membre->id }}" 
                                data-nom="{{ $membre->nomComplet() }}"
                                style="background: #721c24; color: #f8d7da; border-color: #f5c6cb;">
                            <i class="fas fa-ban"></i> Exclure définitivement
                        </button>

                    @elseif($membre->statut === 'suspendu')
                        {{-- Réintégrer --}}
                        <form action="{{ route('admin.membres.reintegrer', $membre) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="membre-action-link activate" style="width: 100%;"
                                    onclick="return confirm('Réintégrer {{ $membre->nomComplet() }} ? Son statut passera à Actif.');">
                                <i class="fas fa-undo"></i> Réintégrer ce membre (annuler la suspension)
                            </button>
                        </form>
                        {{-- Exclure --}}
                        <button class="membre-action-link suspend btn-exclure-membre" 
                                data-id="{{ $membre->id }}" 
                                data-nom="{{ $membre->nomComplet() }}"
                                style="background: #721c24; color: #f8d7da; border-color: #f5c6cb;">
                            <i class="fas fa-ban"></i> Exclure définitivement
                        </button>

                    @elseif($membre->statut === 'exclu')
                        {{-- Réintégrer --}}
                        <form action="{{ route('admin.membres.reintegrer', $membre) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="membre-action-link activate" style="width: 100%;"
                                    onclick="return confirm('Réintégrer {{ $membre->nomComplet() }} ? Son statut passera à Actif.');">
                                <i class="fas fa-undo"></i> Réintégrer ce membre (annuler l'exclusion)
                            </button>
                        </form>

                    @elseif($membre->statut === 'en_attente')
                        {{-- Approuver --}}
                        <form action="{{ route('admin.membres.approuver', $membre) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="membre-action-link activate" style="width: 100%;"
                                    onclick="return confirm('Approuver la demande de {{ $membre->nomComplet() }} ?');">
                                <i class="fas fa-check-circle"></i> Approuver cette demande
                            </button>
                        </form>
                    @endif

                    {{-- Retour --}}
                    <a href="{{ route('admin.membres.index') }}" class="membre-action-link back">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
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