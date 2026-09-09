{{-- ============================================ --}}
{{-- COMPOSANT CARTE ZONE D'INTERVENTION — PHOVE --}}
{{-- Utilisé dans : page liste projets, page d'accueil --}}
{{-- ============================================ --}}

<a href="{{ route('projets.index', ['zone' => $zoneNom]) }}" 
   class="zone-card {{ $active ?? false ? 'zone-card-active' : '' }}" 
   title="Voir les projets à {{ $zoneNom }}"
   data-zone="{{ $zoneNom }}">
    
    {{-- Icône de la zone --}}
    <div class="zone-card-icon">
        @if($zoneIcon === 'goma')
            <i class="fas fa-city"></i>
        @elseif($zoneIcon === 'nyiragongo')
            <i class="fas fa-volcano"></i>
        @elseif($zoneIcon === 'kiziba')
            <i class="fas fa-mountain"></i>
        @elseif($zoneIcon === 'muja')
            <i class="fas fa-seedling"></i>
        @elseif($zoneIcon === 'karisimbi')
            <i class="fas fa-building"></i>
        @elseif($zoneIcon === 'international')
            <i class="fas fa-globe-africa"></i>
        @else
            <i class="fas fa-map-pin"></i>
        @endif
    </div>

    {{-- Contenu --}}
    <div class="zone-card-content">
        <h3 class="zone-card-name">{{ $zoneNom }}</h3>
        
        <span class="zone-card-province">
            <i class="fas fa-location-dot"></i>
            {{ $zoneProvince ?? 'Nord-Kivu' }}
        </span>

        <div class="zone-card-stats">
            <div class="zone-stat">
                <span class="zone-stat-number">{{ $zoneProjetsCount ?? 0 }}</span>
                <span class="zone-stat-label">projet(s)</span>
            </div>
            <div class="zone-stat">
                <span class="zone-stat-number">{{ $zoneProjetsEnCours ?? 0 }}</span>
                <span class="zone-stat-label">en cours</span>
            </div>
        </div>
    </div>

    {{-- Flèche de navigation --}}
    <div class="zone-card-arrow">
        <i class="fas fa-arrow-right"></i>
    </div>

    {{-- Effet de survol : barre colorée --}}
    <div class="zone-card-bar"></div>
</a>