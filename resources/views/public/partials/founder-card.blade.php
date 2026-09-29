{{-- ============================================ --}}
{{-- PARTIAL — CARTE FONDATEUR --}}
{{-- Variable attendue : $fondateur (Organe avec ->membre) --}}
{{-- ============================================ --}}

@php
    $membre = $fondateur->membre;

    $photoUrl = ($membre && $membre->photo)
        ? route('media.show', ['path' => ltrim($membre->photo, '/')])
        : null;

    $initiales = $membre
        ? \Illuminate\Support\Str::upper(
            \Illuminate\Support\Str::substr($membre->nom ?? '', 0, 1) .
            \Illuminate\Support\Str::substr($membre->prenom ?? '', 0, 1)
        )
        : '?';

    $localisation = $membre?->adresse
        ? \Illuminate\Support\Str::limit($membre->adresse, 30)
        : ($membre?->province ?? null);
@endphp

<div class="founder-card">
    <div class="founder-avatar">
        @if($photoUrl)
            <img src="{{ $photoUrl }}"
                 alt="{{ $membre->nomComplet() }}"
                 class="founder-img"
                 loading="lazy"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <span class="founder-initiales" style="display: none;">{{ $initiales }}</span>
        @else
            <span class="founder-initiales">{{ $initiales }}</span>
        @endif
    </div>

    <div class="founder-info">
        <h4 class="founder-name">{{ $membre?->nomComplet() ?? '—' }}</h4>

        @if($fondateur->poste)
            <span class="founder-role">{{ $fondateur->poste }}</span>
        @endif

        @if($localisation)
            <span class="founder-location">
                <i class="fas fa-map-marker-alt"></i> {{ $localisation }}
            </span>
        @endif
    </div>
</div>