<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Titre dynamique --}}
    <title>@yield('title', 'AHD-RDC') | Action Humanitaire pour le Développement en RD Congo</title>

    {{-- Meta tags SEO --}}
    <meta name="description" content="@yield('meta_description', 'AHD-RDC est une organisation non gouvernementale engagée dans le développement communautaire, la protection, la cohésion sociale et la promotion de la paix dans la partie Est de la RD Congo.')">
    <meta name="keywords" content="@yield('meta_keywords', 'AHD-RDC, humanitaire, développement, RD Congo, Sud-Kivu, Nord-Kivu, paix, protection, santé, éducation, wash, sécurité alimentaire')">
    <meta name="author" content="AHD-RDC">
    <meta name="robots" content="index, follow">

    {{-- Open Graph (partage réseaux sociaux) --}}
    <meta property="og:title" content="@yield('title', 'AHD-RDC') | Action Humanitaire pour le Développement en RD Congo">
    <meta property="og:description" content="@yield('meta_description', 'ONG engagée dans le développement communautaire et la promotion de la paix en RD Congo.')">
    <meta property="og:image" content="{{ asset('assets/images/logo-ahd.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="AHD-RDC">
    <meta property="og:locale" content="fr_FR">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'AHD-RDC')">
    <meta name="twitter:description" content="@yield('meta_description', 'ONG engagée dans le développement communautaire en RD Congo.')">
    <meta name="twitter:image" content="{{ asset('assets/images/logo-ahd.png') }}">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/favicon.png') }}">

    {{-- Police Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Font Awesome local --}}
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">

    {{-- Styles communs site public --}}
    <link rel="stylesheet" href="{{ asset('assets/css/public-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/public-header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/public-footer.css') }}">

    {{-- Styles spécifiques par page --}}
    @yield('styles')

    {{-- Styles supplémentaires --}}
    @stack('css')
</head>
<body>

    {{-- ============================================ --}}
    {{-- TOP BAR --}}
    {{-- ============================================ --}}
    @include('partials.topbar')

    {{-- ============================================ --}}
    {{-- HEADER --}}
    {{-- ============================================ --}}
    @include('partials.header')

    {{-- ============================================ --}}
    {{-- FIL D'ARIANE (pages intérieures uniquement) --}}
    {{-- ============================================ --}}
    @hasSection('breadcrumb')
        <section class="breadcrumb-section">
            <div class="container">
                <div class="breadcrumb-content">
                    <h1 class="breadcrumb-title">@yield('page_title', '')</h1>
                    <nav class="breadcrumb-nav">
                        <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>@yield('breadcrumb')</span>
                    </nav>
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================ --}}
    {{-- CONTENU PRINCIPAL --}}
    {{-- ============================================ --}}
    <main class="main-content">
        @yield('content')
    </main>

    {{-- ============================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================ --}}
    @include('partials.footer')

    {{-- ============================================ --}}
    {{-- BOUTON SCROLL TO TOP --}}
    {{-- ============================================ --}}
    <button id="scrollToTop" class="scroll-to-top" title="Retour en haut">
        <i class="fas fa-arrow-up"></i>
    </button>

    {{-- ============================================ --}}
    {{-- SCRIPTS --}}
    {{-- ============================================ --}}
    <script src="{{ asset('assets/js/public-common.js') }}"></script>

    {{-- Scripts spécifiques par page --}}
    @yield('scripts')

    {{-- Scripts supplémentaires --}}
    @stack('js')

</body>
</html>