<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    {{-- Titre dynamique --}}
    <title>@yield('title', 'PHOVE') | Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies</title>
    
    {{-- Meta description --}}
    <meta name="description" content="Plateforme de gestion de l'ONG PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies.">
    <meta name="author" content="PHOVE">
    <meta name="robots" content="noindex, nofollow">
    
    {{-- CSRF Token pour les formulaires --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon-phove.png') }}">
    
    {{-- Police Poppins depuis Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Font Awesome en local --}}
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    
    {{-- Styles communs à toutes les pages d'authentification --}}
    <link rel="stylesheet" href="{{ asset('assets/css/auth-common.css') }}">
    
    {{-- Styles spécifiques à chaque page --}}
    @yield('styles')
    
    {{-- Styles supplémentaires par page --}}
    @stack('css')
</head>
<body class="auth-body">
    
    {{-- Conteneur principal --}}
    <div class="auth-wrapper">
        
        {{-- Colonne gauche : branding PHOVE --}}
        <div class="auth-branding">
            <div class="auth-branding-content">
                {{-- Logo --}}
                <div class="auth-logo">
                    <img src="{{ asset('assets/images/logo-phove.png') }}" alt="PHOVE Logo" class="auth-logo-img">
                </div>
                
                {{-- Nom de l'organisation --}}
                <h1 class="auth-org-name">
                    PHOVE
                </h1>
                
                {{-- Nom complet --}}
                <p class="auth-org-fullname">
                    Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies
                </p>
                
                {{-- Slogan --}}
                <p class="auth-slogan">
                    <i class="fas fa-quote-left"></i>
                    Compassion avec la population
                    <i class="fas fa-quote-right"></i>
                </p>
                
                {{-- Valeurs --}}
                <div class="auth-values">
                    <span class="auth-value-badge">
                        <i class="fas fa-check-circle"></i> Compassion
                    </span>
                    <span class="auth-value-badge">
                        <i class="fas fa-check-circle"></i> Solidarité
                    </span>
                    <span class="auth-value-badge">
                        <i class="fas fa-check-circle"></i> Foi en Dieu
                    </span>
                </div>
                
                {{-- Footer branding --}}
                <div class="auth-branding-footer">
                    <p>&copy; {{ date('Y') }} PHOVE. Tous droits réservés.</p>
                    <p class="auth-location">
                        <i class="fas fa-map-marker-alt"></i> Rue Georges BUSHU N°092, Katoyi, Karisimbi, Goma, RD Congo
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Colonne droite : formulaire --}}
        <div class="auth-form-side">
            <div class="auth-form-container">
                {{-- Contenu dynamique de la page --}}
                @yield('content')
            </div>
        </div>
        
    </div>
    
    {{-- Scripts communs --}}
    <script src="{{ asset('assets/js/auth-common.js') }}"></script>
    
    {{-- Scripts spécifiques à chaque page --}}
    @yield('scripts')
    
    {{-- Scripts supplémentaires --}}
    @stack('js')
    
</body>
</html>