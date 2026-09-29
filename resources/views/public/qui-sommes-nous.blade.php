@extends('layouts.public')

@section('title', 'Qui sommes-nous ?')

@section('meta_description', 'PHOVE est une organisation non gouvernementale apolitique à caractère social et spirituel, créée le 30 août 2012 à Goma. Découvrez notre histoire, notre fondateur et nos 10 membres fondateurs.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/qui-sommes-nous.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-users"></i>
            </div>
            <h1 class="page-header-title">Qui sommes-nous ?</h1>
            <p class="page-header-subtitle">
                Découvrez l'histoire, la mission et l'engagement de PHOVE au service des orphelins, veuves et personnes démunies de la République Démocratique du Congo.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Qui sommes-nous ?</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 1 : NOTRE HISTOIRE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="content-grid">
                {{-- Texte --}}
                <div class="content-block">
                    <span class="section-subtitle">Notre histoire</span>
                    <h2 class="section-title text-left">Une organisation née d'une inspiration divine</h2>
                    Commune de Karisimbi
                    <p class="content-block-text">
                        <strong>PHOVE (Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies)</strong> 
                        est une organisation non gouvernementale apolitique à caractère social et spirituel, 
                        créée le <strong>30 août 2012</strong> à Goma, dans la province du Nord-Kivu, 
                        en République Démocratique du Congo.
                    </p>

                    <p class="content-block-text">
                        L'organisation a été conçue par le serviteur de Dieu <strong>l'Apôtre Philippe 
                        KABOYI MUSAMUGUMA</strong> après avoir fait <strong>40 jours de jeûne et de prière</strong>. 
                        Il reçut une inspiration divine du Seigneur en date du <strong>20 avril 2012</strong> 
                        sur la montagne appelée <strong>KALAGANE</strong> à Bukavu, au Sud-Kivu.
                    </p>

                    <p class="content-block-text">
                        Le message s'est répandu dans toutes les églises de la province du Sud-Kivu et partout 
                        en RD Congo. Il fut bien accueilli à son passage, même dans les pays limitrophes : 
                        Rwanda, Uganda, Kenya, Tanzanie, Burundi, Zambie, Angola, Soudan et Afrique du Sud.
                    </p>

                    <div class="quote-block">
                        <p>
                            « Nous serviteurs de Dieu, conscients de nos responsabilités spirituelles, 
                            avons l'obligation de mettre en pratique la parole de Dieu : 
                            « Aimez-vous les uns les autres » — Romain 13:8. »
                        </p>
                    </div>
                </div>

                {{-- Image --}}
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/about/about-phove.jpg') }}" 
                         alt="PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600'">
                    <div class="content-image-badge">
                        <strong>{{ date('Y') - 2012 }}+</strong><br>
                        <small>années d'action</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
{{-- SECTION 2 : NOTRE FONDATEUR --}}
{{-- ============================================ --}}
<section class="page-section page-section-light" id="fondateur">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Le visionnaire</span>
            <h2 class="section-title">Notre Fondateur</h2>
            <p class="section-description">
                L'homme de Dieu qui a reçu l'inspiration divine de créer PHOVE.
            </p>
        </div>

        @php
            // Le fondateur principal = premier fondateur avec la fonction de DG
            $fondateurPrincipal = $fondateurs
                ->firstWhere('poste', 'like', '%Directeur%')
                ?? $fondateurs->first();
        @endphp

        @if($fondateurPrincipal)
        <div class="founder-highlight">
            <div class="founder-highlight-avatar">
                @php
                    $membrePrincipal = $fondateurPrincipal->membre;
                    $photoUrl = ($membrePrincipal && $membrePrincipal->photo)
                        ? route('media.show', ['path' => ltrim($membrePrincipal->photo, '/')])
                        : null;
                    $initiales = $membrePrincipal
                        ? \Illuminate\Support\Str::upper(
                            \Illuminate\Support\Str::substr($membrePrincipal->nom ?? '', 0, 1) .
                            \Illuminate\Support\Str::substr($membrePrincipal->prenom ?? '', 0, 1)
                        )
                        : 'PK';
                @endphp

                @if($photoUrl)
                    <img src="{{ $photoUrl }}"
                         alt="{{ $membrePrincipal->nomComplet() }} — Fondateur de PHOVE"
                         class="founder-highlight-img"
                         loading="lazy"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="founder-highlight-initials" style="display: none;">
                        {{ $initiales }}
                    </span>
                @else
                    <span class="founder-highlight-initials">{{ $initiales }}</span>
                @endif
            </div>

            <div class="founder-highlight-info">
                <h3 class="founder-highlight-name">
                    {{ $membrePrincipal?->nomComplet() ?? 'Fondateur de PHOVE' }}
                </h3>
                <span class="founder-highlight-role">
                    {{ $fondateurPrincipal->poste }}
                </span>
                <p class="founder-highlight-bio">
                    Apôtre missionnaire, serviteur de Dieu. Après 40 jours de jeûne et de prière
                    sur la montagne KALAGANE à Bukavu, il reçut l'inspiration divine de créer
                    PHOVE le 20 avril 2012. Depuis, il coordonne le programme et veille sur
                    son bon fonctionnement partout dans le monde.
                </p>
                <div class="founder-highlight-actions">
                    <a href="{{ url('/gouvernance') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-sitemap"></i> Voir la gouvernance
                    </a>
                    <a href="{{ url('/contact') }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-envelope"></i> Contacter
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>


{{-- ============================================ --}}
{{-- SECTION 3 : LES MEMBRES FONDATEURS --}}
{{-- ============================================ --}}
@if($fondateurs->count() > 0)
<section class="page-section page-section-white" id="fondateurs">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Les pionniers</span>
            <h2 class="section-title">Nos membres fondateurs</h2>
            <p class="section-description">
                Le 30 août 2012, {{ $fondateurs->count() }} serviteurs de Dieu engagés
                se sont réunis à Goma pour signer les statuts de PHOVE.
            </p>
        </div>

        <div class="founders-grid">
            @foreach($fondateurs as $fondateur)
                @include('public.partials.founder-card', ['fondateur' => $fondateur])
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ============================================ --}}
{{-- SECTION 4 : NOTRE IDENTITÉ --}}
{{-- ============================================ --}}
<section class="page-section page-section-light">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Notre identité</span>
            <h2 class="section-title">Ce qui nous définit</h2>
        </div>

        <div class="cards-grid">
            {{-- Carte 1 --}}
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="fas fa-gavel"></i>
                </div>
                <h3 class="info-card-title">Statut juridique</h3>
                <p class="info-card-text">
                    PHOVE est une <strong>Organisation Non Gouvernementale (ONG)</strong>
                    apolitique à caractère social, créée le 30 août 2012 à Goma,
                    régie par les présents statuts et le Règlement d'Ordre Intérieur (ROI).
                </p>
            </div>

            {{-- Carte 2 --}}
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h3 class="info-card-title">Caractère</h3>
                <p class="info-card-text">
                    Conformément à l'article 1 des statuts, PHOVE est une organisation
                    <strong>apolitique et à caractère social</strong>. Nous intervenons
                    sans distinction de race, de religion, d'ethnie ou d'appartenance politique.
                </p>
            </div>

            {{-- Carte 3 --}}
            <div class="info-card">
                <div class="info-card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="info-card-title">Durée</h3>
                <p class="info-card-text">
                    Conformément à l'article 2 des statuts, PHOVE est créée pour
                    une <strong>durée indéterminée</strong>. Notre engagement est permanent
                    et s'inscrit dans le long terme auprès des personnes vulnérables.
                </p>
            </div>
        </div>
    </div>
</section>

    {{-- ============================================ --}}
    {{-- SECTION 5 : ZONES D'INTERVENTION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="content-grid">
                {{-- Image --}}
                <div>
                    <img src="{{ asset('assets/images/about/about-phove.jpg') }}" 
                         alt="Zones d'intervention PHOVE" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=600'">
                </div>

                {{-- Texte --}}
                <div class="content-block">
                    <span class="section-subtitle">Notre présence</span>
                    <h2 class="section-title text-left">Zones d'intervention</h2>
                    
                    <p class="content-block-text">
                        Conformément à l'article 4 des statuts, PHOVE exerce ses activités 
                        à <strong>Goma</strong> en RD Congo en particulier et peut étendre 
                        ses activités <strong>partout dans le monde</strong> en cas de nécessité.
                    </p>

                    <p class="content-block-text">
                        Notre siège social est établi <strong>Avenue Jacarandes au N° 08, Quartier les volcans, 
                        Commune de Goma, Ville de Goma, Nord-Kivu</strong>. Le siège peut être transféré 
                        sur décision motivée des 2/3 des membres effectifs.
                    </p>

                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Goma</strong> — Nord-Kivu, RD Congo (Siège social)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Extensions nationales</strong> — Sur toute l'étendue de la RDC</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Extensions internationales</strong> — Rwanda, Uganda, Kenya, Tanzanie, Burundi, Zambie, Angola, Soudan, Afrique du Sud</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Stations, Bases et Antennes</strong> — Dans les pays, territoires et communes</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 6 : PUBLIC CIBLE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Notre raison d'être</span>
                <h2 class="section-title">Notre public cible</h2>
                <p class="section-description">
                    PHOVE se focalise sur l'assistance et l'accompagnement des personnes 
                    les plus vulnérables de nos communautés.
                </p>
            </div>

            <div class="cards-grid cards-grid-4">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-child"></i>
                    </div>
                    <h3 class="info-card-title">Orphelins</h3>
                    <p class="info-card-text">
                        Soins médicaux, Scolarisation, alimentation, habillement, logement et encadrement spirituel.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-female"></i>
                    </div>
                    <h3 class="info-card-title">Veuves</h3>
                    <p class="info-card-text">
                        Soins médicaux, Assistance sociale, accompagnement économique,logement et soutien spirituel.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-wheelchair"></i>
                    </div>
                    <h3 class="info-card-title">Personnes démunies</h3>
                    <p class="info-card-text">
                        Aide alimentaire, soins médicaux, logement et activités socio-économiques.
                    </p>
                </div>

                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="info-card-title">3ème âge</h3>
                    <p class="info-card-text">
                        Soins médicaux, protection, assistance, logement et accompagnement des personnes âgées.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA --}}
    {{-- ============================================ --}}
    <section class="cta-section-page">
        <div class="container">
            <div class="cta-content-page">
                <h2 class="cta-title-page">
                    Rejoignez notre <span class="highlight-page">mission humanitaire</span>
                </h2>
                <p class="cta-text-page">
                    Vous partagez nos valeurs de compassion et de solidarité ? 
                    Impliquez-vous à nos côtés pour faire la différence dans la vie 
                    des orphelins, veuves et personnes démunies de la RD Congo.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/devenir-membre') }}" class="btn-page-don">
                        <i class="fas fa-user-plus"></i> Devenir membre
                    </a>
                    <a href="{{ url('/contact') }}" class="btn-page-outline">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/qui-sommes-nous.js') }}"></script>
@endsection