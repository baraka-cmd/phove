{{-- ============================================ --}}
{{-- COMPOSANT CARTE PROJET RÉUTILISABLE — PHOVE --}}
{{-- Utilisé dans : liste projets, page d'accueil, projets connexes --}}
{{-- ============================================ --}}

<article class="projet-card {{ $class ?? '' }}" data-aos="fade-up">
    
    {{-- Image du projet --}}
    <div class="projet-card-image">
        <img src="{{ $projet->image_principale ? asset('storage/' . $projet->image_principale) : asset('assets/images/logo-phove.png') }}" 
             alt="{{ $projet->titre }}" 
             loading="lazy"
             class="projet-card-img"
             onerror="this.src='{{ asset('assets/images/logo-phove.png') }}'">
        
        {{-- Badge statut --}}
        <span class="projet-card-badge projet-badge-{{ $projet->statut }}">
            @if($projet->statut === 'en_cours')
                <i class="fas fa-spinner fa-pulse"></i> En cours
            @elseif($projet->statut === 'termine')
                <i class="fas fa-check-circle"></i> Terminé
            @elseif($projet->statut === 'en_preparation')
                <i class="fas fa-clock"></i> En préparation
            @elseif($projet->statut === 'suspendu')
                <i class="fas fa-pause-circle"></i> Suspendu
            @endif
        </span>

        {{-- Badge domaine --}}
        <span class="projet-card-domain-badge">
            <i class="fas fa-tag"></i> {{ $projet->domaineLibelle() }}
        </span>
    </div>

    {{-- Corps de la carte --}}
    <div class="projet-card-body">
        
        {{-- Titre --}}
        <h3 class="projet-card-title">
            <a href="{{ route('projets.show', $projet->slug) }}" title="Voir le projet : {{ $projet->titre }}">
                {{ Str::limit($projet->titre, 70) }}
            </a>
        </h3>

        {{-- Métadonnées --}}
        <div class="projet-card-meta">
            <span class="projet-meta-item" title="Zone d'intervention">
                <i class="fas fa-map-marker-alt"></i>
                {{ $projet->zone_geographique }}
            </span>
            <span class="projet-meta-item" title="Période">
                <i class="fas fa-calendar-alt"></i>
                {{ $projet->date_debut->format('Y') }}
                @if($projet->date_fin)
                    - {{ $projet->date_fin->format('Y') }}
                @endif
            </span>
        </div>

        {{-- Description courte --}}
        <p class="projet-card-description">
            {{ Str::limit(strip_tags($projet->description), 120) }}
        </p>

        {{-- Barre de progression du budget --}}
        @if($projet->budget_total > 0)
        <div class="projet-card-progress">
            <div class="projet-progress-header">
                <span class="projet-progress-label">
                    <i class="fas fa-chart-line"></i> Budget utilisé
                </span>
                <span class="projet-progress-percent">
                    {{ $projet->pourcentageBudget() }}%
                </span>
            </div>
            <div class="projet-progress-bar">
                <div class="projet-progress-fill {{ $projet->pourcentageBudget() >= 100 ? 'completed' : '' }}" 
                     style="width: {{ $projet->pourcentageBudget() }}%"
                     data-width="{{ $projet->pourcentageBudget() }}%">
                </div>
            </div>
            <div class="projet-progress-amounts">
                <span>{{ number_format($projet->budget_utilise, 0, ',', ' ') }} $</span>
                <span>{{ number_format($projet->budget_total, 0, ',', ' ') }} $</span>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="projet-card-footer">
            <a href="{{ route('projets.show', $projet->slug) }}" class="projet-card-link">
                <span>Détails du projet</span>
                <i class="fas fa-arrow-right"></i>
            </a>
            
            {{-- Partenaires (logos miniatures) --}}
            @if($projet->partenaires->count() > 0)
            <div class="projet-card-partners">
                @foreach($projet->partenaires->take(3) as $partenaire)
                    <span class="projet-partner-mini" title="{{ $partenaire->nom }}">
                        {{ Str::upper(Str::substr($partenaire->nom, 0, 2)) }}
                    </span>
                @endforeach
                @if($projet->partenaires->count() > 3)
                    <span class="projet-partner-mini projet-partner-more">
                        +{{ $projet->partenaires->count() - 3 }}
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>
</article>