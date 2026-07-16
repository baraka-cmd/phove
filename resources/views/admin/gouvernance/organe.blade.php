@extends('layouts.admin')

@section('title', $organeLibelle)

@section('page_title', $organeLibelle)
@section('page_icon', 'sitemap')
@section('page_subtitle', 'Gérez les membres de cet organe : ajoutez, modifiez ou retirez des membres.')

@php
    $breadcrumb = [
        ['label' => 'Gouvernance', 'url' => route('admin.gouvernance.index')],
        $organeLibelle
    ];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance-organe.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-gouvernance.css') }}">
@endsection

@section('content')

    <div class="gouvernance-detail-grid">

        {{-- ============================================ --}}
        {{-- TABLEAU DES MEMBRES --}}
        {{-- ============================================ --}}
        <div class="gouvernance-table-card">
            @if($membres->count() > 0)
            <div class="table-responsive">
                <table class="table-gouvernance">
                    <thead>
                        <tr>
                            <th>Membre</th>
                            <th>Poste</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($membres as $organe)
                        <tr>
                            {{-- Membre --}}
                            <td>
                                <div class="gouvernance-membre-item" style="padding: 0; border: none;">
                                    <div class="gouvernance-membre-avatar">
                                        @if($organe->membre && $organe->membre->photo)
                                            <img src="{{ asset('storage/' . $organe->membre->photo) }}" alt="{{ $organe->membre->nomComplet() }}">
                                        @else
                                            <span>{{ Str::upper(Str::substr($organe->membre->nom ?? '?', 0, 1) . Str::substr($organe->membre->prenom ?? '', 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div class="gouvernance-membre-info">
                                        <div class="gouvernance-membre-nom">{{ $organe->membre ? $organe->membre->nomComplet() : '—' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Poste --}}
                            <td>
                                <span style="font-size: 13px; font-weight: 600; color: var(--color-primary);">{{ $organe->poste }}</span>
                            </td>

                            {{-- Date début --}}
                            <td>
                                <span style="font-size: 13px;">{{ $organe->date_debut->format('d/m/Y') }}</span>
                            </td>

                            {{-- Date fin --}}
                            <td>
                                <span style="font-size: 13px;">{{ $organe->date_fin ? $organe->date_fin->format('d/m/Y') : 'En cours' }}</span>
                            </td>

                            {{-- Statut --}}
                            <td>
                                <span class="membre-badge membre-badge-{{ $organe->actif ? 'success' : 'info' }}">
                                    {{ $organe->actif ? 'Actif' : 'Terminé' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="membre-actions">
                                    {{-- Éditer --}}
                                    <a href="#" class="membre-action-btn edit btn-edit-poste"
                                       data-id="{{ $organe->id }}"
                                       data-poste="{{ $organe->poste }}"
                                       data-date-debut="{{ $organe->date_debut->format('Y-m-d') }}"
                                       data-date-fin="{{ $organe->date_fin ? $organe->date_fin->format('Y-m-d') : '' }}"
                                       data-actif="{{ $organe->actif }}"
                                       data-ordre="{{ $organe->ordre }}"
                                       title="Modifier le poste">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Retirer --}}
                                    @if($organe->actif)
                                    <form action="{{ route('admin.gouvernance.retirerMembre', $organe) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="membre-action-btn delete btn-retirer-membre"
                                                data-nom="{{ $organe->membre ? $organe->membre->nomComplet() : '—' }}"
                                                data-poste="{{ $organe->poste }}"
                                                title="Retirer le membre">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="text-align: center; padding: 40px 20px; color: #999;">
                <i class="fas fa-users-slash" style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                <p>Aucun membre dans cet organe pour le moment.</p>
            </div>
            @endif
        </div>

        {{-- ============================================ --}}
        {{-- FORMULAIRE D'AJOUT --}}
        {{-- ============================================ --}}
        <div class="gouvernance-form-card">
    <h3 class="gouvernance-form-title">
        <i class="fas fa-user-plus"></i> Ajouter un membre
    </h3>
    <form action="{{ route('admin.gouvernance.storeMembre') }}" method="POST">
        @csrf
        <input type="hidden" name="organe" value="{{ $organe }}">

        <div class="gouvernance-form-group">
            <label for="membre_id" class="gouvernance-form-label">Membre <span class="required">*</span></label>
            <select name="membre_id" id="membre_id" class="gouvernance-form-select" required>
                <option value="">Sélectionnez un membre...</option>
                @foreach($membresDisponibles as $m)
                    <option value="{{ $m->id }}">{{ $m->nomComplet() }}</option>
                @endforeach
            </select>
        </div>

        <div class="gouvernance-form-group">
            <label for="poste" class="gouvernance-form-label">Poste <span class="required">*</span></label>
            <input type="text" name="poste" id="poste" class="gouvernance-form-input" 
                   placeholder="Ex: Président, Secrétaire..." required>
        </div>

        <div class="gouvernance-form-group">
            <label for="date_debut" class="gouvernance-form-label">Date de début <span class="required">*</span></label>
            <input type="date" name="date_debut" id="date_debut" class="gouvernance-form-input" 
                   value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="gouvernance-form-group">
            <label for="date_fin" class="gouvernance-form-label">Date de fin (optionnel)</label>
            <input type="date" name="date_fin" id="date_fin" class="gouvernance-form-input">
        </div>

        <div class="gouvernance-form-group">
            <label for="ordre" class="gouvernance-form-label">Ordre d'affichage</label>
            <input type="number" name="ordre" id="ordre" class="gouvernance-form-input" 
                   value="0" min="0">
        </div>

        <button type="submit" class="gouvernance-form-submit">
            <i class="fas fa-plus-circle"></i> Ajouter à l'organe
        </button>
    </form>
</div>
    </div>

    {{-- ============================================ --}}
    {{-- FORMULAIRE D'ÉDITION (caché par défaut) --}}
    {{-- ============================================ --}}
    <div class="gouvernance-form-card" id="editFormCard" style="margin-top: var(--spacing-xl); display: none;">
        <h3 class="gouvernance-form-title">
            <i class="fas fa-edit"></i> Modifier le poste
        </h3>
        <form action="" method="POST" id="editForm">
            @csrf
            @method('PUT')

            <div class="gouvernance-form-group">
                <label for="edit_poste" class="gouvernance-form-label">Poste <span class="required">*</span></label>
                <input type="text" name="poste" id="edit_poste" class="gouvernance-form-input" required>
            </div>

            <div class="gouvernance-form-group">
                <label for="edit_date_debut" class="gouvernance-form-label">Date de début <span class="required">*</span></label>
                <input type="date" name="date_debut" id="edit_date_debut" class="gouvernance-form-input" required>
            </div>

            <div class="gouvernance-form-group">
                <label for="edit_date_fin" class="gouvernance-form-label">Date de fin</label>
                <input type="date" name="date_fin" id="edit_date_fin" class="gouvernance-form-input">
            </div>

            <div class="gouvernance-form-group">
                <label for="edit_ordre" class="gouvernance-form-label">Ordre d'affichage</label>
                <input type="number" name="ordre" id="edit_ordre" class="gouvernance-form-input" value="0" min="0">
            </div>

            <div class="gouvernance-form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #555;">
                    <input type="checkbox" name="actif" id="edit_actif" value="1" style="width: 18px; height: 18px; accent-color: var(--color-primary);">
                    Mandat actif
                </label>
            </div>

            <button type="submit" class="gouvernance-form-submit">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-gouvernance.js') }}"></script>
    <script>
        // Gestion du formulaire d'édition
        const editFormCard = document.getElementById('editFormCard');
        const editForm = document.getElementById('editForm');

        document.querySelectorAll('.btn-edit-poste').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const poste = this.getAttribute('data-poste');
                const dateDebut = this.getAttribute('data-date-debut');
                const dateFin = this.getAttribute('data-date-fin');
                const actif = this.getAttribute('data-actif');
                const ordre = this.getAttribute('data-ordre');

                editForm.action = '/admin/gouvernance/organes/' + id + '/update';
                document.getElementById('edit_poste').value = poste;
                document.getElementById('edit_date_debut').value = dateDebut;
                document.getElementById('edit_date_fin').value = dateFin;
                document.getElementById('edit_actif').checked = actif === '1';
                document.getElementById('edit_ordre').value = ordre;

                editFormCard.style.display = 'block';
                editFormCard.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-gouvernance-organe.js') }}"></script>
@endsection