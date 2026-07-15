{{-- ============================================ --}}
{{-- COMPOSANT LIGNE MEMBRE (TABLEAU RÉUTILISABLE) --}}
{{-- Utilisé dans : index membres --}}
{{-- ============================================ --}}

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

    {{-- Sexe --}}
    <td>
        <span style="font-size: 13px;">
            @if($membre->sexe === 'M')
                <i class="fas fa-mars" style="color: #2980b9;"></i> M
            @else
                <i class="fas fa-venus" style="color: #e84393;"></i> F
            @endif
        </span>
    </td>

    {{-- Catégorie --}}
    <td>
        @php
            $categorieBadge = match($membre->categorie) {
                'initiateur' => 'danger',
                'fondateur' => 'primary',
                'adherent' => 'success',
                'honneur' => 'warning',
                'sympathisant' => 'info',
                default => 'primary'
            };
            $categorieLibelle = match($membre->categorie) {
                'initiateur' => 'Initiateur',
                'fondateur' => 'Fondateur',
                'adherent' => 'Adhérent',
                'honneur' => 'Honneur',
                'sympathisant' => 'Sympathisant',
                default => $membre->categorie
            };
        @endphp
        <span class="membre-badge membre-badge-{{ $categorieBadge }}">{{ $categorieLibelle }}</span>
    </td>

    {{-- Adresse --}}
    <td>
        <span style="font-size: 13px;">
            <i class="fas fa-map-marker-alt" style="color: var(--color-accent); font-size: 10px;"></i> 
            {{ $membre->adresse }}
        </span>
    </td>

    {{-- Statut --}}
    <td>
        @php
            $statutBadge = match($membre->statut) {
                'actif' => 'success',
                'en_attente' => 'warning',
                'suspendu' => 'danger',
                'exclu' => 'danger',
                'deserteur' => 'info',
                'demissionnaire' => 'info',
                default => 'primary'
            };
            $statutLibelle = match($membre->statut) {
                'actif' => 'Actif',
                'en_attente' => 'En attente',
                'suspendu' => 'Suspendu',
                'exclu' => 'Exclu',
                'deserteur' => 'Déserteur',
                'demissionnaire' => 'Démissionnaire',
                default => $membre->statut
            };
        @endphp
        <span class="membre-badge membre-badge-{{ $statutBadge }}">{{ $statutLibelle }}</span>
    </td>

    {{-- Date d'adhésion --}}
    <td>
        <span style="font-size: 13px;">
            <i class="fas fa-calendar-alt" style="color: var(--color-text-muted); font-size: 11px;"></i> 
            {{ $membre->date_adhesion ? $membre->date_adhesion->format('d/m/Y') : '—' }}
        </span>
    </td>

    {{-- Cotisations --}}
    <td>
        @php
            $totalCotisations = $membre->cotisations ? $membre->cotisations->sum('montant') : 0;
        @endphp
        <span style="font-weight: 600; color: {{ $totalCotisations > 0 ? 'var(--color-success)' : 'var(--color-text-muted)' }};">
            {{ number_format($totalCotisations, 0, ',', ' ') }} $
        </span>
    </td>

    {{-- Actions --}}
    <td>
        <div class="membre-actions">
            {{-- Voir --}}
            <a href="{{ route('admin.membres.show', $membre) }}" 
               class="membre-action-btn view" 
               title="Voir la fiche détaillée"
               data-tooltip="Voir">
                <i class="fas fa-eye"></i>
            </a>

            {{-- Modifier --}}
            <a href="{{ route('admin.membres.edit', $membre) }}" 
               class="membre-action-btn edit" 
               title="Modifier le membre"
               data-tooltip="Modifier">
                <i class="fas fa-edit"></i>
            </a>

            {{-- Changer statut --}}
            <button class="membre-action-btn btn-change-status" 
                    data-id="{{ $membre->id }}" 
                    data-nom="{{ $membre->nomComplet() }}"
                    data-action="{{ $membre->statut === 'actif' ? 'suspendu' : 'actif' }}"
                    title="{{ $membre->statut === 'actif' ? 'Suspendre' : 'Activer' }}">
                <i class="fas fa-{{ $membre->statut === 'actif' ? 'ban' : 'check' }}"></i>
            </button>

            {{-- Supprimer --}}
            <form action="{{ route('admin.membres.destroy', $membre) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="membre-action-btn delete btn-delete-membre" 
                        data-nom="{{ $membre->nomComplet() }}"
                        title="Supprimer définitivement"
                        onclick="return confirm('Supprimer définitivement {{ $membre->nomComplet() }} ? Cette action est irréversible.');">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </td>
</tr>