{{-- ============================================ --}}
{{-- COMPOSANT CARTE ZONE D'INTERVENTION --}}
{{-- Utilisé dans : page liste projets, page d'accueil --}}
{{-- ============================================ --}}

<a href="{{ route('projets.index', ['zone' => $zoneNom]) }}" 
   class="zone-card {{ $active ?? false ? 'zone-card-active' : '' }}" 
   title="Voir les projets à {{ $zoneNom }}"
   data-zone="{{ $zoneNom }}">
    
    {{-- Icône de la zone --}}
    <div class="zone-card-icon">
        @if($zoneIcon === 'minova')
            <i class="fas fa-mountain"></i>
        @elseif($zoneIcon === 'kirotshe')
            <i class="fas fa-hill-rockslide"></i>
        @elseif($zoneIcon === 'kalehe')
            <i class="fas fa-tree"></i>
        @elseif($zoneIcon === 'goma')
            <i class="fas fa-city"></i>
        @else
            <i class="fas fa-map-pin"></i>
        @endif
    </div>

    {{-- Contenu --}}
    <div class="zone-card-content">
        <h3 class="zone-card-name">{{ $zoneNom }}</h3>
        
        <span class="zone-card-province">
            <i class="fas fa-location-dot"></i>
            {{ $zoneProvince ?? 'Sud-Kivu / Nord-Kivu' }}
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