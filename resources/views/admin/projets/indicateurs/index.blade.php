@extends('layouts.admin')

@section('title', 'Indicateurs — ' . $projet->titre . ' | PHOVE')

@section('page_title', 'Indicateurs de performance')
@section('page_icon', 'tachometer-alt')
@section('page_subtitle', 'Gérez les indicateurs (KPI) du projet PHOVE : ' . Str::limit($projet->titre, 60))

@php
    $breadcrumb = [
        ['label' => 'Projets', 'url' => route('admin.projets.index')],
        ['label' => Str::limit($projet->titre, 40), 'url' => route('admin.projets.show', $projet)],
        'Indicateurs KPI'
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-indicateurs.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- EN-TÊTE PROJET --}}
    {{-- ============================================ --}}
    <div class="indicateurs-projet-header">
        <div class="indicateurs-projet-image">
            @if($projet->image_principale)
                <img src="{{ asset('storage/' . $projet->image_principale) }}" alt="{{ $projet->titre }}" loading="lazy">
            @else
                <div class="indicateurs-projet-image-placeholder">
                    <i class="fas fa-project-diagram"></i>
                </div>
            @endif
        </div>
        <div class="indicateurs-projet-info">
            <h2>{{ $projet->titre }}</h2>
            <p>
                <i class="fas fa-tag"></i> {{ $projet->domaineLibelle() }} &nbsp;|&nbsp;
                <i class="fas fa-map-marker-alt"></i> {{ $projet->zone_geographique }} &nbsp;|&nbsp;
                <i class="fas fa-circle"></i> {{ $projet->statutLibelle() }}
                @if($projet->code_projet)
                    &nbsp;|&nbsp; <i class="fas fa-barcode"></i> {{ $projet->code_projet }}
                @endif
            </p>
        </div>
        <div class="indicateurs-projet-back">
            <a href="{{ route('admin.projets.show', $projet) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-arrow-left"></i> Retour au projet
            </a>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="indicateurs-stats-grid">
        <div class="indicateur-stat-card">
            <div class="indicateur-stat-icon"><i class="fas fa-list-ul"></i></div>
            <div class="indicateur-stat-content">
                <div class="indicateur-stat-value">{{ $totalIndicateurs }}</div>
                <div class="indicateur-stat-label">Total indicateurs</div>
            </div>
        </div>
        <div class="indicateur-stat-card">
            <div class="indicateur-stat-icon" style="background: #D1FAE5; color: #065F46;"><i class="fas fa-check-circle"></i></div>
            <div class="indicateur-stat-content">
                <div class="indicateur-stat-value" style="color: #065F46;">{{ $indicateursAtteints }}</div>
                <div class="indicateur-stat-label">Objectifs atteints</div>
            </div>
        </div>
        <div class="indicateur-stat-card">
            <div class="indicateur-stat-icon" style="background: #DBEAFE; color: #1E40AF;"><i class="fas fa-spinner"></i></div>
            <div class="indicateur-stat-content">
                <div class="indicateur-stat-value" style="color: #1E40AF;">{{ $indicateursEnCours }}</div>
                <div class="indicateur-stat-label">En cours</div>
            </div>
        </div>
        <div class="indicateur-stat-card">
            <div class="indicateur-stat-icon" style="background: #FEF3C7; color: #92400E;"><i class="fas fa-chart-line"></i></div>
            <div class="indicateur-stat-content">
                <div class="indicateur-stat-value" style="color: #92400E;">{{ $tauxGlobal }}%</div>
                <div class="indicateur-stat-label">Taux global d'atteinte</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- GRAPHIQUES --}}
    {{-- ============================================ --}}
    <div class="indicateurs-charts-grid">
        
        {{-- Graphique barres horizontales : Progression par indicateur --}}
        <div class="indicateurs-widget">
            <div class="indicateurs-widget-header">
                <h3 class="indicateurs-widget-title">
                    <i class="fas fa-chart-bar"></i> Progression par indicateur
                </h3>
            </div>
            <div class="indicateurs-widget-body">
                <div style="height: {{ max(200, $totalIndicateurs * 50) }}px;">
                    <canvas id="indicateursBarChart" 
                            data-labels='@json($graphLabels)' 
                            data-values='@json($graphValues)'
                            data-colors='@json($graphColors)'></canvas>
                </div>
                @if($totalIndicateurs === 0)
                <div style="text-align: center; padding: 30px; color: #94A3B8;">
                    <i class="fas fa-chart-bar" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.4;"></i>
                    Aucun indicateur à afficher
                </div>
                @endif
            </div>
        </div>

        {{-- Graphique anneau : Atteints vs En cours --}}
        <div class="indicateurs-widget">
            <div class="indicateurs-widget-header">
                <h3 class="indicateurs-widget-title">
                    <i class="fas fa-chart-pie"></i> Objectifs atteints vs en cours
                </h3>
            </div>
            <div class="indicateurs-widget-body">
                <div style="display: flex; align-items: center; gap: 24px;">
                    <div style="width: 160px; height: 160px; flex-shrink: 0; position: relative;">
                        <canvas id="indicateursDonutChart" 
                                data-labels='@json($donutLabels)' 
                                data-values='@json($donutValues)'
                                data-colors='@json($donutColors)'></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <span style="font-size: 1.5rem; font-weight: 800; color: #1E293B; display: block;" id="donutCenterValue">0</span>
                            <span style="font-size: 10px; color: #64748B;">indicateurs</span>
                        </div>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px; font-size: 13px;">
                            <span style="width: 12px; height: 12px; border-radius: 3px; background: #10B981; flex-shrink: 0;"></span>
                            <span style="color: #334155;">Atteints</span>
                            <span style="margin-left: auto; font-weight: 700; color: #1E293B;">{{ $indicateursAtteints }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px; font-size: 13px;">
                            <span style="width: 12px; height: 12px; border-radius: 3px; background: #1A5B9C; flex-shrink: 0;"></span>
                            <span style="color: #334155;">En cours</span>
                            <span style="margin-left: auto; font-weight: 700; color: #1E293B;">{{ $indicateursEnCours }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- LISTE DES INDICATEURS --}}
    {{-- ============================================ --}}
    <div class="indicateurs-list-card">
        <div class="indicateurs-list-header">
            <h3 class="indicateurs-list-title">
                <i class="fas fa-list-check"></i> Liste des indicateurs
            </h3>
            <span style="font-size: 12px; color: #64748B;">{{ $totalIndicateurs }} indicateur(s)</span>
        </div>

        <div class="indicateurs-list-body">
            @forelse($indicateurs as $indicateur)
            <div class="indicateur-item">
                {{-- Icône --}}
                <div class="indicateur-item-icon {{ $indicateur->objectifAtteint() ? 'achieved' : 'progress' }}">
                    <i class="fas fa-{{ $indicateur->objectifAtteint() ? 'check-circle' : 'chart-line' }}"></i>
                </div>

                {{-- Contenu --}}
                <div class="indicateur-item-content">
                    <div class="indicateur-item-header">
                        <span class="indicateur-item-nom">{{ $indicateur->nom_indicateur }}</span>
                        <span class="indicateur-item-pourcentage {{ $indicateur->objectifAtteint() ? 'achieved' : 'progress' }}">
                            {{ $indicateur->pourcentage() }}%
                        </span>
                    </div>
                    <div class="indicateur-item-bar">
                        <div class="indicateur-item-fill {{ $indicateur->objectifAtteint() ? 'achieved' : 'progress' }}" 
                             style="width: {{ min($indicateur->pourcentage(), 100) }}%"></div>
                    </div>
                    <div class="indicateur-item-meta">
                        <span class="indicateur-item-valeur">
                            {{ number_format($indicateur->valeur_actuelle, 0, ',', ' ') }} / {{ number_format($indicateur->valeur_cible, 0, ',', ' ') }}
                        </span>
                        <span class="indicateur-item-unite">{{ $indicateur->unite }}</span>
                        @if($indicateur->domaine)
                        <span style="font-size: 11px; color: #1A5B9C;">
                            <i class="fas fa-{{ $indicateur->domaineIcone() }}"></i> {{ $indicateur->domaineLibelle() }}
                        </span>
                        @endif
                        @if($indicateur->date_mise_a_jour)
                        <span style="font-size: 11px; color: #94A3B8;">
                            <i class="fas fa-clock"></i> {{ $indicateur->date_mise_a_jour->format('d/m/Y') }}
                        </span>
                        @endif
                    </div>
                    @if($indicateur->description)
                    <p style="font-size: 11px; color: #64748B; margin-top: 6px; font-style: italic;">{{ $indicateur->description }}</p>
                    @endif
                </div>

                {{-- Mise à jour rapide --}}
                <form action="{{ route('admin.projets.indicateurs.updateValue', [$projet, $indicateur]) }}" method="POST" class="indicateur-update-form" style="display: flex; align-items: center; gap: 6px;">
                    @csrf
                    @method('PUT')
                    <input type="number" name="valeur_actuelle" value="{{ $indicateur->valeur_actuelle }}" 
                           class="indicateur-form-input" style="width: 90px; text-align: center;" min="0" title="Valeur actuelle">
                    <button type="submit" class="indicateur-form-submit" style="height: 34px; padding: 0 10px; font-size: 11px;" title="Mettre à jour">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </form>

                {{-- Actions --}}
                <div class="indicateur-item-actions">
                    <button class="indicateur-action-btn edit btn-edit-indicateur" 
                            data-id="{{ $indicateur->id }}"
                            data-nom="{{ $indicateur->nom_indicateur }}"
                            data-cible="{{ $indicateur->valeur_cible }}"
                            data-actuelle="{{ $indicateur->valeur_actuelle }}"
                            data-unite="{{ $indicateur->unite }}"
                            data-description="{{ $indicateur->description }}"
                            title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('admin.projets.indicateurs.destroy', [$projet, $indicateur]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="indicateur-action-btn delete" 
                                title="Supprimer" 
                                onclick="return confirm('Supprimer cet indicateur ?');">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="indicateurs-empty">
                <div class="indicateurs-empty-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <h4>Aucun indicateur</h4>
                <p>Ajoutez des indicateurs de performance pour suivre l'avancement de ce projet.</p>
            </div>
            @endforelse
        </div>

        {{-- Formulaire d'ajout rapide --}}
        <form action="{{ route('admin.projets.indicateurs.store', $projet) }}" method="POST" class="indicateur-form-inline">
            @csrf
            <div class="indicateur-form-group nom">
                <label class="indicateur-form-label">Nom de l'indicateur *</label>
                <input type="text" name="nom_indicateur" class="indicateur-form-input" 
                       placeholder="Ex: Orphelins pris en charge" required>
            </div>
            <div class="indicateur-form-group cible">
                <label class="indicateur-form-label">Cible *</label>
                <input type="number" name="valeur_cible" class="indicateur-form-input" 
                       placeholder="Ex: 200" min="1" required>
            </div>
            <div class="indicateur-form-group actuelle">
                <label class="indicateur-form-label">Valeur actuelle</label>
                <input type="number" name="valeur_actuelle" class="indicateur-form-input" 
                       placeholder="Ex: 120" min="0" value="0">
            </div>
            <div class="indicateur-form-group unite">
                <label class="indicateur-form-label">Unité *</label>
                <select name="unite" class="indicateur-form-input" required style="cursor: pointer;">
                    <option value="">Choisir...</option>
                    @foreach(\App\Models\IndicateurProjet::UNITES as $unite)
                        <option value="{{ $unite }}">{{ ucfirst($unite) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="indicateur-form-submit">
                <i class="fas fa-plus"></i> Ajouter
            </button>
        </form>
    </div>

    {{-- ============================================ --}}
    {{-- FORMULAIRE D'ÉDITION (caché) --}}
    {{-- ============================================ --}}
    <div class="indicateurs-list-card" id="editIndicateurCard" style="margin-top: var(--spacing-xl); display: none;">
        <div class="indicateurs-list-header">
            <h3 class="indicateurs-list-title">
                <i class="fas fa-edit"></i> Modifier l'indicateur
            </h3>
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('editIndicateurCard').style.display='none';">
                <i class="fas fa-times"></i> Fermer
            </button>
        </div>
        <div class="indicateurs-list-body">
            <form action="" method="POST" id="editIndicateurForm">
                @csrf
                @method('PUT')
                <div class="indicateur-form-inline" style="border-top: none;">
                    <div class="indicateur-form-group nom">
                        <label class="indicateur-form-label">Nom *</label>
                        <input type="text" name="nom_indicateur" id="edit_nom" class="indicateur-form-input" required>
                    </div>
                    <div class="indicateur-form-group cible">
                        <label class="indicateur-form-label">Cible *</label>
                        <input type="number" name="valeur_cible" id="edit_cible" class="indicateur-form-input" min="1" required>
                    </div>
                    <div class="indicateur-form-group actuelle">
                        <label class="indicateur-form-label">Valeur actuelle *</label>
                        <input type="number" name="valeur_actuelle" id="edit_actuelle" class="indicateur-form-input" min="0" required>
                    </div>
                    <div class="indicateur-form-group unite">
                        <label class="indicateur-form-label">Unité *</label>
                        <select name="unite" id="edit_unite" class="indicateur-form-input" required style="cursor: pointer;">
                            <option value="">Choisir...</option>
                            @foreach(\App\Models\IndicateurProjet::UNITES as $unite)
                                <option value="{{ $unite }}">{{ ucfirst($unite) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="indicateur-form-submit" style="background: #10B981;">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/admin-indicateurs.js') }}"></script>
    <script>
        // Gestion du formulaire d'édition
        const editCard = document.getElementById('editIndicateurCard');
        const editForm = document.getElementById('editIndicateurForm');

        document.querySelectorAll('.btn-edit-indicateur').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const nom = this.getAttribute('data-nom');
                const cible = this.getAttribute('data-cible');
                const actuelle = this.getAttribute('data-actuelle');
                const unite = this.getAttribute('data-unite');

                editForm.action = '/admin/projets/{{ $projet->id }}/indicateurs/' + id;
                document.getElementById('edit_nom').value = nom;
                document.getElementById('edit_cible').value = cible;
                document.getElementById('edit_actuelle').value = actuelle;
                document.getElementById('edit_unite').value = unite;

                editCard.style.display = 'block';
                editCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    </script>
@endsection