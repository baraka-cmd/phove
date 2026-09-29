{{-- ============================================ --}}
{{-- PARTIAL — CARTE PROJET --}}
{{-- Variable attendue : $projet --}}
{{-- Variable optionnelle : $class --}}
{{-- ============================================ --}}

@php
    $imageUrl = $projet->image_principale
        ? route('media.show', ['path' => ltrim($projet->image_principale, '/')])
        : null;

    $pourcentage = $projet->budget_total > 0
        ? $projet->pourcentageBudget()
        : 0;

    $statutIcone = match($projet->statut) {
        'en_cours'       => 'spinner fa-pulse',
        'termine'        => 'check-circle',
        'en_preparation' => 'clock',
        'suspendu'       => 'pause-circle',
        default          => 'circle',
    };
@endphp

<article class="projet-card {{ $class ?? '' }}">

    {{-- Image --}}
    <div class="projet-card-image">
        @if($imageUrl)
            <img src="{{ $imageUrl }}"
                 alt="{{ $projet->titre }}"
                 class="projet-card-img"
                 loading="lazy"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="projet-card-image-placeholder" style="display: none;">
                <i class="fas fa-project-diagram"></i>
            </div>
        @else
            <div class="projet-card-image-placeholder">
                <i class="fas fa-project-diagram"></i>
            </div>
        @endif

        {{-- Badge statut --}}
        <span class="projet-card-badge projet-badge-{{ $projet->statut }}">
            <i class="fas fa-{{ $statutIcone }}"></i>
            {{ $projet->statutLibelle() }}
        </span>

        {{-- Badge domaine --}}
        <span class="projet-card-domain-badge">
            <i class="fas fa-tag"></i>
            {{ $projet->domaineLibelle() }}
        </span>
    </div>

    {{-- Corps --}}
    <div class="projet-card-body">

        <h3 class="projet-card-title">
            <a href="{{ route('projets.show', $projet->slug) }}">
                {{ Str::limit($projet->titre, 80) }}
            </a>
        </h3>

        <div class="projet-card-meta">
            @if($projet->zone_geographique)
                <span class="projet-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    {{ Str::limit($projet->zone_geographique, 30) }}
                </span>
            @endif
            @if($projet->date_debut)
                <span class="projet-meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    {{ $projet->date_debut->format('Y') }}
                </span>
            @endif
        </div>

        @if($projet->description)
            <p class="projet-card-description">
                {{ Str::limit(strip_tags($projet->description), 120) }}
            </p>
        @endif

        @if($projet->budget_total > 0)
            <div class="projet-card-progress">
                <div class="projet-progress-header">
                    <span class="projet-progress-label">
                        <i class="fas fa-chart-line"></i> Budget
                    </span>
                    <span class="projet-progress-percent">{{ $pourcentage }}%</span>
                </div>
                <div class="projet-progress-bar">
                    <div class="projet-progress-fill {{ $pourcentage >= 100 ? 'completed' : '' }}"
                         style="width: {{ $pourcentage }}%"></div>
                </div>
                <div class="projet-progress-amounts">
                    <span>{{ number_format($projet->budget_utilise, 0, ',', ' ') }} $</span>
                    <span>{{ number_format($projet->budget_total, 0, ',', ' ') }} $</span>
                </div>
            </div>
        @endif

        <div class="projet-card-footer">
            <a href="{{ route('projets.show', $projet->slug) }}" class="projet-card-link">
                Détails du projet <i class="fas fa-arrow-right"></i>
            </a>

            @php
                $partenaires = $projet->relationLoaded('partenaires')
                    ? $projet->partenaires
                    : $projet->partenaires()->get();
            @endphp

            @if($partenaires->count() > 0)
                <div class="projet-card-partners">
                    @foreach($partenaires->take(3) as $partenaire)
                        <span class="projet-partner-mini" title="{{ $partenaire->nom }}">
                            {{ Str::upper(Str::substr($partenaire->nom, 0, 2)) }}
                        </span>
                    @endforeach
                    @if($partenaires->count() > 3)
                        <span class="projet-partner-mini projet-partner-more">
                            +{{ $partenaires->count() - 3 }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</article>