{{-- ============================================ --}}
{{-- PARTIAL — CARTE PARTENAIRE --}}
{{-- -------------------------------------------- --}}
{{-- Utilisé dans : --}}
{{--   - public/home.blade.php (section partenaires) --}}
{{--   - public/partenaires/index.blade.php --}}
{{--   - public/projets/show.blade.php (partenaires du projet) --}}
{{-- --}}
{{-- Variable attendue : $partenaire (App\Models\Partenaire) --}}
{{-- Variable optionnelle : $showType (bool, afficher le type — défaut true) --}}
{{-- ============================================ --}}

@php
    /**
     * Prépare les données d'affichage.
     */

    // URL du logo via media.show (ou null si absent)
    $logoUrl = $partenaire->logo
        ? route('media.show', ['path' => ltrim($partenaire->logo, '/')])
        : null;

    // Sigle ou initiales (fallback)
    $sigle = $partenaire->sigleOuInitiales();

    // Libellé du type (Bailleur de fonds, International…)
    $typeLibelle = $partenaire->typeLibelle();

    // Lien vers la page des partenaires
    $lienDetail = url('/partenaires');

    // Affichage conditionnel du type
    $afficherType = $showType ?? true;

    // Nom raccourci (max 40 caractères)
    $nomAffiche = \Illuminate\Support\Str::limit($partenaire->nom, 40);

    // Site web externe (si présent)
    $siteWeb = $partenaire->urlSiteWeb();
@endphp

<div class="partner-item" title="{{ $partenaire->nom }} — {{ $typeLibelle }}">

    <a href="{{ $lienDetail }}" class="partner-link" aria-label="Voir les partenaires — {{ $partenaire->nom }}">

        {{-- ---------------------------------------- --}}
        {{-- LOGO (ou fallback) --}}
        {{-- ---------------------------------------- --}}
        <div class="partner-logo-wrapper">

            @if($logoUrl)
                <img src="{{ $logoUrl }}"
                     alt="Logo {{ $partenaire->nom }}"
                     class="partner-logo"
                     loading="lazy"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                {{-- Fallback affiché si l'image échoue --}}
                <div class="partner-fallback partner-fallback-hidden">
                    <i class="fas fa-handshake"></i>
                    <span>{{ $sigle }}</span>
                </div>
            @else
                <div class="partner-fallback">
                    <i class="fas fa-handshake"></i>
                    <span>{{ $sigle }}</span>
                </div>
            @endif

        </div>

        {{-- ---------------------------------------- --}}
        {{-- INFORMATIONS --}}
        {{-- ---------------------------------------- --}}
        <div class="partner-info">

            {{-- Nom --}}
            <h3 class="partner-name">{{ $nomAffiche }}</h3>

            {{-- Sigle + Type --}}
            <div class="partner-meta">

                @if($partenaire->sigle)
                    <span class="partner-sigle-badge">{{ $partenaire->sigle }}</span>
                @endif

                @if($afficherType)
                    <span class="partner-type-badge partner-type-{{ $partenaire->type }}">
                        {{ $typeLibelle }}
                    </span>
                @endif

            </div>

            {{-- Site web (petit lien discret) --}}
            @if($siteWeb)
                <span class="partner-website" title="Site officiel">
                    <i class="fas fa-external-link-alt"></i>
                    {{ parse_url($siteWeb, PHP_URL_HOST) }}
                </span>
            @endif

        </div>

    </a>
</div>