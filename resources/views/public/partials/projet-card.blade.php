{{-- ============================================ --}}
{{-- COMPOSANT CARTE PROJET RÉUTILISABLE — PHOVE --}}
{{-- Utilisé dans : liste projets, page d'accueil, projets connexes --}}
{{-- Variable attendue : $projet --}}
{{-- Variable optionnelle : $class (classes CSS supplémentaires) --}}
{{-- ============================================ --}}

@php
    // URL de l'image via la route custom media.show
    $imageUrl = $projet->image_principale
        ? route('media.show', ['path' => ltrim($projet->image_principale, '/')])
        : asset('assets/images/logo-phove.png');

    // Pourcentage du budget utilisé (0 si budget_total = 0)
    $pourcentage = $projet->budget_total > 0
        ? $projet->pourcentageBudget()
        : 0;

    // Mapping statut → icône
    $statutIcone = match($projet->statut) {
        'en_cours'       => 'spinner fa-pulse',
        'termine'        => 'check-circle',
        'en_preparation' => 'clock',
        'suspendu'       => 'pause-circle',
        default          => 'circle',
    };

    // Mapping statut → classe CSS (déjà alignée sur le CSS)
    $statutClasse = match($projet->statut) {
        'en_cours'       => 'en-cours',
        'termine'        => 'termine',
        'en_preparation' => 'preparation',
        'suspendu'       => 'suspendu',
        default          => 'en-cours',
    };
@endphp

<article class="project-card {{ $class ?? '' }}">

    {{-- Image du projet --}}
    <div class="project-card-image">
        <img src="{{ $imageUrl }}"
             alt="{{ $projet->titre }}"
             loading="lazy"
             onerror="this.src='{{ asset('assets/images/logo-phove.png') }}'">

        {{-- Badge statut --}}
        <span class="project-card-badge {{ $statutClasse }}">
            <i class="fas fa-{{ $statutIcone }}"></i>
            {{ $projet->statutLibelle() }}
        </span>
    </div>

    {{-- Corps de la carte --}}
    <div class="project-card-body">

        {{-- Domaine --}}
        <span class="project-card-domain">
            {{ $projet->domaineLibelle() }}
        </span>

        {{-- Titre --}}
        <h3 class="project-card-title">
            <a href="{{ route('projets.show', $projet->slug) }}" 
               title="Voir le projet : {{ $projet->titre }}">
                {{ Str::limit($projet->titre, 70) }}
            </a>
        </h3>

        {{-- Métadonnées --}}
        <div class="project-card-meta">
            <span>
                <i class="fas fa-map-marker-alt"></i>
                {{ $projet->zone_geographique }}
            </span>
            <span>
                <i class="fas fa-calendar-alt"></i>
                {{ $projet->date_debut->format('Y') }}
                @if($projet->date_fin)
                    - {{ $projet->date_fin->format('Y') }}
                @endif
            </span>
        </div>

        {{-- Description courte --}}
        <p class="project-card-description">
            {{ Str::limit(strip_tags($projet->description), 120) }}
        </p>

        {{-- Barre de progression du budget --}}
        @if($projet->budget_total > 0)
        <div class="project-card-progress">
            <div class="progress-label">
                <span>Budget utilisé</span>
                <span>{{ $pourcentage }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill {{ $pourcentage >= 100 ? 'completed' : '' }}"
                     style="width: {{ $pourcentage }}%"
                     data-width="{{ $pourcentage }}%">
                </div>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="project-card-footer">
            <a href="{{ route('projets.show', $projet->slug) }}" class="project-card-link">
                Détails du projet <i class="fas fa-arrow-right"></i>
            </a>

            {{-- Partenaires (logos miniatures) --}}
            @php
                $partenaires = $projet->relationLoaded('partenaires')
                    ? $projet->partenaires
                    : $projet->partenaires()->get();
            @endphp

            @if($partenaires->count() > 0)
            <div class="project-card-partners">
                @foreach($partenaires->take(3) as $partenaire)
                    <span class="project-partner-mini" title="{{ $partenaire->nom }}">
                        {{ Str::upper(Str::substr($partenaire->nom, 0, 2)) }}
                    </span>
                @endforeach
                @if($partenaires->count() > 3)
                    <span class="project-partner-mini projet-partner-more">
                        +{{ $partenaires->count() - 3 }}
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>
</article>