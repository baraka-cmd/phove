<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Titre dynamique --}}
    <title>@yield('title', 'PHOVE') | Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies</title>

    {{-- Meta tags SEO --}}
    <meta name="description" content="@yield('meta_description', 'PHOVE (Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies) est une ONG congolaise apolitique à caractère social et spirituel, créée le 30 août 2012 à Goma. Sa mission : venir en aide aux orphelins, veuves, personnes de 3ème âge et personnes démunies.')">
    <meta name="keywords" content="@yield('meta_keywords', 'PHOVE, ONG, humanitaire, orphelins, veuves, personnes démunies, RD Congo, Goma, Nord-Kivu, aide sociale, éducation, santé, agriculture, formation professionnelle, assistance spirituelle')">
    <meta name="author" content="PHOVE">
    <meta name="robots" content="index, follow">

    {{-- Open Graph (partage réseaux sociaux) --}}
    <meta property="og:title" content="@yield('title', 'PHOVE') | Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies">
    <meta property="og:description" content="@yield('meta_description', 'PHOVE — ONG humanitaire congolaise engagée dans l\'assistance aux orphelins, veuves et personnes démunies : éducation, santé, agriculture, formation professionnelle et accompagnement spirituel.')">
    <meta property="og:image" content="{{ asset('assets/images/logo-phove.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="PHOVE">
    <meta property="og:locale" content="fr_FR">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'PHOVE')">
    <meta name="twitter:description" content="@yield('meta_description', 'PHOVE — ONG humanitaire congolaise : compassion avec la population.')">
    <meta name="twitter:image" content="{{ asset('assets/images/logo-phove.png') }}">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon-phove.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/favicon-phove.png') }}">

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