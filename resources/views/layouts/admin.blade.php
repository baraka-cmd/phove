<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >

    {{-- ============================================================
         TITRE DYNAMIQUE
    ============================================================ --}}
    <title>
        @yield('title', 'Tableau de bord') | PHOVE Administration
    </title>


    {{-- ============================================================
         META
    ============================================================ --}}
    <meta
        name="description"
        content="Interface d'administration PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies."
    >

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >


    {{-- ============================================================
         FAVICON
    ============================================================ --}}
    <link
        rel="icon"
        type="image/png"
        href="{{ asset('assets/images/favicon-phove.png') }}"
    >

    <link
        rel="apple-touch-icon"
        href="{{ asset('assets/images/favicon-phove.png') }}"
    >


    {{-- ============================================================
         POLICE POPPINS
    ============================================================ --}}
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet"
    >


    {{-- ============================================================
         FONT AWESOME
    ============================================================ --}}
    <link
        rel="stylesheet"
        href="{{ asset('assets/fontawesome/css/all.min.css') }}"
    >


    {{-- ============================================================
         CSS GLOBAL EXISTANT
         
         NE PAS MODIFIER LES CHEMINS EXISTANTS
    ============================================================ --}}
    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin-common.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin-sidebar.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin-topbar.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin-footer.css') }}"
    >


    {{-- ============================================================
         CSS SPÉCIFIQUE À LA PAGE
         
         SYSTÈME EXISTANT :
         
         @section('styles')
             <link rel="stylesheet" ...>
         @endsection
         
         Ce système reste actif pour les pages existantes.
    ============================================================ --}}
    @yield('styles')


    {{-- ============================================================
         CSS PAR STACK
         
         Anciennes / autres pages :
         
         @push('css')
             <link rel="stylesheet" ...>
         @endpush
    ============================================================ --}}
    @stack('css')


    {{-- ============================================================
         CSS PAR STACK MODERNE
         
         Nouvelles pages :
         
         @push('styles')
             <link rel="stylesheet" ...>
         @endpush
    ============================================================ --}}
    @stack('styles')


</head>


<body class="admin-body">


    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    @include('partials.admin-sidebar')


    {{-- ============================================================
         CONTENEUR PRINCIPAL
    ============================================================ --}}
    <div class="admin-main">


        {{-- ========================================================
             TOPBAR
        ========================================================= --}}
        @include('partials.admin-topbar')


        {{-- ========================================================
             CONTENU PRINCIPAL
        ========================================================= --}}
        <main class="admin-content">


            {{-- ====================================================
                 TITRE DE PAGE
                 
                 Système existant conservé.
            ===================================================== --}}
            @hasSection('page_title')

                <div class="page-header-admin">

                    <h1 class="page-title">

                        @hasSection('page_icon')

                            <i class="fas fa-@yield('page_icon')"></i>

                        @endif

                        @yield('page_title')

                    </h1>


                    @hasSection('page_subtitle')

                        <p class="page-subtitle">
                            @yield('page_subtitle')
                        </p>

                    @endif

                </div>

            @endif


            {{-- ====================================================
                 ALERTE SUCCESS GLOBALE
            ===================================================== --}}
            @if(session('success'))

                <div class="alert alert-success">

                    <i class="fas fa-check-circle"></i>

                    <span>
                        {{ session('success') }}
                    </span>

                </div>

            @endif


            {{-- ====================================================
                 ALERTE ERREUR GLOBALE
            ===================================================== --}}
            @if(session('error'))

                <div class="alert alert-error">

                    <i class="fas fa-exclamation-triangle"></i>

                    <span>
                        {{ session('error') }}
                    </span>

                </div>

            @endif


            {{-- ====================================================
                 ERREURS DE VALIDATION GLOBALES
            ===================================================== --}}
            @if($errors->any())

                <div class="alert alert-error">

                    <i class="fas fa-exclamation-triangle"></i>

                    <div>

                        <strong>
                            Impossible d'effectuer l'opération.
                        </strong>

                        <ul class="validation-error-list">

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            @endif


            {{-- ====================================================
                 CONTENU DE LA PAGE
            ===================================================== --}}
            @yield('content')


        </main>


        {{-- ========================================================
             FOOTER
        ========================================================= --}}
        @include('partials.admin-footer')


    </div>


    {{-- ============================================================
         LOADER GLOBAL
    ============================================================ --}}
    <div
        class="loader-overlay"
        id="globalLoader"
    >

        <div class="loader-spinner"></div>

    </div>


    {{-- ============================================================
         JAVASCRIPT GLOBAL EXISTANT
    ============================================================ --}}
    <script
        src="{{ asset('assets/js/admin-common.js') }}"
        defer
    ></script>


    {{-- ============================================================
         JAVASCRIPT SPÉCIFIQUE À LA PAGE
         
         SYSTÈME EXISTANT :
         
         @section('scripts')
             <script src="..."></script>
         @endsection
         
         Ce système reste actif.
    ============================================================ --}}
    @yield('scripts')


    {{-- ============================================================
         JAVASCRIPT PAR STACK
         
         Anciennes / autres pages :
         
         @push('js')
             <script src="..."></script>
         @endpush
    ============================================================ --}}
    @stack('js')


    {{-- ============================================================
         JAVASCRIPT PAR STACK MODERNE
         
         Nouvelles pages :
         
         @push('scripts')
             <script src="..." defer></script>
         @endpush
    ============================================================ --}}
    @stack('scripts')


</body>

</html>