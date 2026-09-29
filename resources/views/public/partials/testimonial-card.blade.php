{{-- ============================================ --}}
{{-- PARTIAL — CARTE TÉMOIGNAGE --}}
{{-- Variable attendue : $temoignage --}}
{{-- ============================================ --}}

@php
    // Nom complet
    $nomComplet = $temoignage->nomComplet();

    // Initiales pour l'avatar (via méthode du modèle)
    $initiales = $temoignage->initiales();

    // Photo via media.show (ou null)
    $photoUrl = $temoignage->photo
        ? route('media.show', ['path' => ltrim($temoignage->photo, '/')])
        : null;

    // Rôle / localisation (ex: "Veuve bénéficiaire, Muja")
    $roleParts = [];

    if ($temoignage->categorieLibelle()) {
        $roleParts[] = $temoignage->categorieLibelle();
    }

    if ($temoignage->localisation) {
        $roleParts[] = $temoignage->localisation;
    } elseif ($temoignage->province) {
        $roleParts[] = $temoignage->province;
    }

    $roleAffiche = implode(', ', $roleParts);
@endphp

<blockquote class="testimonial-card">

    {{-- Icône guillemets --}}
    <div class="testimonial-quote">
        <i class="fas fa-quote-right"></i>
    </div>

    {{-- Contenu du témoignage --}}
    <p class="testimonial-text">
        {{ Str::limit($temoignage->contenu, 240) }}
    </p>

    {{-- Auteur --}}
    <div class="testimonial-author">

        {{-- Avatar (photo ou initiales) --}}
        <div class="testimonial-avatar">
            @if($photoUrl)
                <img src="{{ $photoUrl }}"
                     alt="{{ $nomComplet }}"
                     loading="lazy"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                {{-- Fallback si l'image échoue --}}
                <span class="testimonial-avatar-fallback" style="display: none;">
                    {{ $initiales }}
                </span>
            @else
                <span>{{ $initiales }}</span>
            @endif
        </div>

        {{-- Infos --}}
        <div class="testimonial-info">
            <span class="testimonial-name">{{ $nomComplet }}</span>

            @if($roleAffiche)
                <span class="testimonial-role">{{ $roleAffiche }}</span>
            @endif
        </div>

    </div>
</blockquote>