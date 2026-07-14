<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    {{-- Titre dynamique --}}
    <title>@yield('title', 'Tableau de bord') | AHD-RDC Administration</title>
    
    {{-- Meta --}}
    <meta name="description" content="Interface d'administration AHD-RDC — Action Humanitaire pour le Développement en RD Congo.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/favicon.png') }}">
    
    {{-- Police Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    
    {{-- Styles admin --}}
    <link rel="stylesheet" href="{{ asset('assets/css/admin-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-topbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-footer.css') }}">
    
    {{-- Styles spécifiques par page --}}
    @yield('styles')
    
    {{-- Styles supplémentaires --}}
    @stack('css')
</head>
<body class="admin-body">

    {{-- ============================================ --}}
    {{-- SIDEBAR --}}
    {{-- ============================================ --}}
    @include('partials.admin-sidebar')

    {{-- ============================================ --}}
    {{-- CONTENU PRINCIPAL --}}
    {{-- ============================================ --}}
    <div class="admin-main">
        
        {{-- Topbar --}}
        @include('partials.admin-topbar')

        {{-- Contenu dynamique --}}
        <main class="admin-content">
            
            {{-- Titre de page --}}
            @hasSection('page_title')
            <div class="page-header-admin">
                <h1 class="page-title">
                    @hasSection('page_icon')
                        <i class="fas fa-@yield('page_icon')"></i>
                    @endif
                    @yield('page_title')
                </h1>
                @hasSection('page_subtitle')
                    <p class="page-subtitle">@yield('page_subtitle')</p>
                @endif
            </div>
            @endif

            {{-- Alertes flash --}}
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Contenu de la page --}}
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('partials.admin-footer')
    </div>

    {{-- ============================================ --}}
    {{-- LOADER GLOBAL --}}
    {{-- ============================================ --}}
    <div class="loader-overlay" id="globalLoader">
        <div class="loader-spinner"></div>
    </div>

    {{-- ============================================ --}}
    {{-- SCRIPTS --}}
    {{-- ============================================ --}}
    <script src="{{ asset('assets/js/admin-common.js') }}"></script>
    
    {{-- Scripts spécifiques par page --}}
    @yield('scripts')
    
    {{-- Scripts supplémentaires --}}
    @stack('js')

</body>
</html>