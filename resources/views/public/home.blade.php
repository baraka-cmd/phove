@extends('layouts.public')

@section('title', 'Accueil')

@section('meta_description', data_get($siteSettings ?? [], 'meta_description', 'PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies. ONG humanitaire congolaise créée en 2012 à Goma. Compassion avec la population.'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/public-home.css') }}">
    
    <link rel="stylesheet" href="{{ asset('assets/css/projets-index.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- HERO SECTION — SLIDER --}}
    {{-- ============================================ --}}
    <section class="hero" id="hero">
        {{-- Slides --}}
        <div class="hero-slider">
            <div class="hero-slide active" style="background-image: url('{{ img_url(data_get($siteSettings ?? [], 'home_hero_image_1') ?: 'assets/images/slide1.jpg') }}');">
                <div class="hero-overlay"></div>
            </div>
            <div class="hero-slide" style="background-image: url('{{ img_url(data_get($siteSettings ?? [], 'home_hero_image_2') ?: 'assets/images/slide2.jpg') }}');">
                <div class="hero-overlay"></div>
            </div>
            <div class="hero-slide" style="background-image: url('{{ img_url(data_get($siteSettings ?? [], 'home_hero_image_3') ?: 'assets/images/slide3.jpg') }}');">
                <div class="hero-overlay"></div>
            </div>
        </div>

        {{-- Indicateurs --}}
        <div class="hero-indicators">
            <span class="hero-indicator active" data-slide="0"></span>
            <span class="hero-indicator" data-slide="1"></span>
            <span class="hero-indicator" data-slide="2"></span>
        </div>

        {{-- Contenu centré --}}
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">
                    <i class="fas fa-dot-circle"></i> {{ data_get($siteSettings ?? [], 'home_hero_badge') ?: 'Depuis 2012 — Goma, Nord-Kivu, RD Congo' }}
                </span>
                <h1 class="hero-title">
                    <span class="highlight">{{ data_get($siteSettings ?? [], 'home_hero_title') ?: 'Compassion avec la population' }}</span>
                </h1>
                <p class="hero-description">
                    {{ data_get($siteSettings ?? [], 'home_hero_description') ?: 'PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies — vient en aide aux plus vulnérables : éducation, santé, agriculture, formation professionnelle, assistance sociale et spirituelle.' }}
                </p>
                <div class="hero-buttons">
                    <a href="{{ url('/faire-un-don') }}" class="hero-btn-primary">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                    <a href="{{ url('/qui-sommes-nous') }}" class="hero-btn-secondary">
                        <i class="fas fa-info-circle"></i> Découvrir notre mission
                    </a>
                </div>
            </div>
        </div>

        {{-- Flèches de navigation --}}
        <button class="hero-arrow hero-arrow-left" id="heroPrev" aria-label="Slide précédent">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="hero-arrow hero-arrow-right" id="heroNext" aria-label="Slide suivant">
            <i class="fas fa-chevron-right"></i>
        </button>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION COMPTEURS — IMPACT EN CHIFFRES --}}
    {{-- ============================================ --}}
    <section class="counters-section">
        <div class="container">
            <div class="counters-grid">
                {{-- Compteur 1 : Projets --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="{{ $totalProjets ?? 4 }}">0</span>
                    </div>
                    <div class="counter-label">Projets humanitaires</div>
                    <p class="counter-desc">En cours et réalisés</p>
                </div>

                {{-- Compteur 2 : Bénéficiaires --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="5000">0</span>
                        <span class="counter-plus">+</span>
                    </div>
                    <div class="counter-label">Bénéficiaires atteints</div>
                    <p class="counter-desc">Orphelins, veuves, personnes démunies</p>
                </div>

                {{-- Compteur 3 : Domaines --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="9">0</span>
                    </div>
                    <div class="counter-label">Domaines d'intervention</div>
                    <p class="counter-desc">Spirituel, social, éducatif, sanitaire</p>
                </div>

                {{-- Compteur 4 : Années --}}
                <div class="counter-item">
                    <div class="counter-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="counter-number">
                        <span class="counter-value" data-target="{{ date('Y') - 2012 }}">0</span>
                        <span class="counter-plus">+</span>
                    </div>
                    <div class="counter-label">Années d'expérience</div>
                    <p class="counter-desc">Au service des plus démunis</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION À PROPOS — QUI SOMMES-NOUS ? --}}
    {{-- ============================================ --}}
    <section class="about-section" id="about">
        <div class="container">
            <div class="about-grid">
                {{-- Image --}}
                <div class="about-image-wrapper">
                    <img src="{{ img_url(data_get($siteSettings ?? [], 'home_about_image') ?: 'assets/images/about/about-phove.jpg') }}" 
                         alt="PHOVE — Action humanitaire auprès des orphelins et veuves" 
                         class="about-image"
                         onerror="this.src='https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600'">
                    <div class="about-image-badge">
                        <span class="badge-number">{{ date('Y') - 2012 }}+</span>
                        <span class="badge-text">Années d'action<br>humanitaire</span>
                    </div>
                </div>

                {{-- Contenu --}}
                <div class="about-content">
                    <span class="section-subtitle">Qui sommes-nous ?</span>
                    <h2 class="section-title">
                        Une organisation au service des orphelins, veuves et personnes démunies
                    </h2>

                    <p class="about-text">
                        <strong>PHOVE (Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies)</strong> 
                        est une organisation non gouvernementale apolitique à caractère social et spirituel, 
                        créée le <strong>30 août 2012</strong> à Goma, dans la province du Nord-Kivu, 
                        en République Démocratique du Congo.
                    </p>

                    <p class="about-text">
                        Fondée par l'<strong>Apôtre Philippe KABOYI MUSAMUGUMA</strong> après 40 jours 
                        de jeûne et de prière sur la montagne KALAGANE à Bukavu, PHOVE est née d'une 
                        inspiration divine reçue le <strong>20 avril 2012</strong>. Sa mission : 
                        venir en aide aux personnes vulnérables — orphelins, veuves, personnes de 
                        3ème âge et personnes démunies.
                    </p>

                    <div class="about-values-list">
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Compassion</strong>
                                <span>Au service des plus démunis</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Solidarité</strong>
                                <span>Ensemble pour le bien-être de tous</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Foi en Dieu</strong>
                                <span>Guidés par les Saintes Écritures</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Intégrité</strong>
                                <span>Transparence et redevabilité</span>
                            </div>
                        </div>
                        <div class="about-value-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Charité</strong>
                                <span>Amour du prochain selon Romain 13:8</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('/qui-sommes-nous') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> En savoir plus sur notre histoire
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION DOMAINES D'INTERVENTION --}}
    {{-- ============================================ --}}
    <section class="domains-section" id="domains">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Ce que nous faisons</span>
                <h2 class="section-title">Nos domaines d'intervention</h2>
                <p class="section-description">
                    Conformément aux articles 8 et 9 de nos statuts, PHOVE intervient dans 
                    les domaines suivants pour répondre aux besoins essentiels des personnes 
                    vulnérables.
                </p>
            </div>

            <div class="domains-grid">
                {{-- Domaine 1 --}}
                <a href="{{ url('/projets?domaine=sante_nutrition') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-heartbeat"></i></div>
                    <h3 class="domain-title">Santé & Nutrition</h3>
                    <p class="domain-description">
                        Soins médicaux, alimentation, lutte contre la malnutrition 
                        et accompagnement des malades traumatisés.
                    </p>
                </a>

                {{-- Domaine 2 --}}
                <a href="{{ url('/projets?domaine=education') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3 class="domain-title">Éducation & Formation</h3>
                    <p class="domain-description">
                        Scolarisation des orphelins, alphabétisation, formation 
                        professionnelle : coupe couture, informatique, menuiserie.
                    </p>
                </a>

                {{-- Domaine 3 --}}
                <a href="{{ url('/projets?domaine=protection') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3 class="domain-title">Protection Sociale</h3>
                    <p class="domain-description">
                        Assistance aux veuves et orphelins, lutte contre les violences, 
                        assistance judiciaire et accompagnement psychosocial.
                    </p>
                </a>

                {{-- Domaine 4 --}}
                <a href="{{ url('/projets?domaine=securite_alimentaire') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-seedling"></i></div>
                    <h3 class="domain-title">Agriculture & Élevage</h3>
                    <p class="domain-description">
                        Soutien à l'agriculture, l'élevage et la pêche. Ouverture 
                        et réhabilitation des routes de desserte agricole.
                    </p>
                </a>

                {{-- Domaine 5 --}}
                <a href="{{ url('/projets?domaine=wash') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-tint"></i></div>
                    <h3 class="domain-title">Eau & Assainissement</h3>
                    <p class="domain-description">
                        Accès à l'eau potable, hygiène communautaire et 
                        assainissement des milieux.
                    </p>
                </a>

                {{-- Domaine 6 --}}
                <a href="{{ url('/projets?domaine=spirituel') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-dove"></i></div>
                    <h3 class="domain-title">Accompagnement Spirituel</h3>
                    <p class="domain-description">
                        Encadrement spirituel des fidèles, formation biblique, 
                        soutien moral et préparation spirituelle.
                    </p>
                </a>

                {{-- Domaine 7 --}}
                <a href="{{ url('/projets?domaine=infrastructure') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-building"></i></div>
                    <h3 class="domain-title">Infrastructures</h3>
                    <p class="domain-description">
                        Construction et réhabilitation des infrastructures 
                        hospitalières, scolaires et dépôts agricoles.
                    </p>
                </a>

                {{-- Domaine 8 --}}
                <a href="{{ url('/projets?domaine=economique') }}" class="domain-card">
                    <div class="domain-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <h3 class="domain-title">Activités Économiques</h3>
                    <p class="domain-description">
                        Accompagnement des activités socio-économiques, 
                        lutte contre l'oisiveté juvénile et le vagabondage.
                    </p>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
{{-- SECTION PROJETS RÉCENTS --}}
{{-- ============================================ --}}
<section class="projets-liste-section" id="projets">
    <div class="container">

        {{-- En-tête --}}
        <div class="section-header">
            <span class="section-subtitle">Nos actions</span>
            <h2 class="section-title">Projets en cours et réalisés</h2>
            <p class="section-description">
                Découvrez les projets que nous menons sur le terrain pour améliorer 
                les conditions de vie des orphelins, veuves et personnes démunies.
            </p>
        </div>

        {{-- Grille des projets --}}
        <div class="projets-grid" id="projetsGrid">
            @forelse($projets as $projet)
                @include('public.partials.projet-card', ['projet' => $projet])
            @empty
                <div class="projets-empty">
                    <div class="projets-empty-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3>Aucun projet publié pour le moment</h3>
                    <p>Revenez bientôt pour découvrir nos actions sur le terrain.</p>
                </div>
            @endforelse
        </div>

        {{-- Bouton voir tous --}}
        <div class="projets-section-footer">
            <a href="{{ route('projets.index') }}" class="btn btn-outline">
                <i class="fas fa-list-ul"></i> Voir tous les projets
            </a>
        </div>

    </div>
</section>

    {{-- ============================================ --}}
    {{-- SECTION ACTUALITÉS RÉCENTES (DYNAMIQUE) --}}
    {{-- ============================================ --}}
    @if(isset($articles) && $articles->count() > 0)
    <section class="news-section" id="news">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Nos publications</span>
                <h2 class="section-title">Actualités & Événements récents</h2>
                <p class="section-description">
                    Restez informés de nos dernières actions, événements et publications 
                    sur le terrain et au sein de l'organisation.
                </p>
            </div>

            <div class="news-grid">
                @foreach($articles as $article)
                <article class="news-card">
                    <div class="news-card-image">
                        @if($article->image)
                            <img src="{{ img_url($article->image) }}" 
                                 alt="{{ $article->titre }}" 
                                 loading="lazy"
                                 onerror="this.src='{{ asset('assets/images/slide1.jpg') }}'">
                        @else
                            <div class="news-card-placeholder">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        @endif

                        {{-- Badge catégorie --}}
                        <span class="news-card-badge news-badge-{{ $article->categorie }}">
                            <i class="fas fa-{{ 
                                $article->categorie === 'actualite' ? 'bolt' : 
                                ($article->categorie === 'projet' ? 'project-diagram' : 
                                ($article->categorie === 'evenement' ? 'calendar-star' : 
                                ($article->categorie === 'rapport' ? 'file-alt' : 
                                ($article->categorie === 'temoignage' ? 'quote-right' : 'bullhorn'))))
                            }}"></i>
                            {{ $article->categorieLibelle() }}
                        </span>

                        {{-- Badge épinglé --}}
                        @if($article->epingle)
                        <span class="news-card-pin" title="À la une">
                            <i class="fas fa-thumbtack"></i>
                        </span>
                        @endif
                    </div>

                    <div class="news-card-body">
                        <div class="news-card-meta">
                            <span>
                                <i class="fas fa-calendar-alt"></i>
                                {{ $article->date_publication ? $article->date_publication->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                            </span>
                            <span>
                                <i class="fas fa-eye"></i>
                                {{ $article->vues ?? 0 }}
                            </span>
                        </div>

                        <h3 class="news-card-title">
                            <a href="{{ route('actualites.show', $article->slug) }}">
                                {{ $article->titre }}
                            </a>
                        </h3>

                        <p class="news-card-excerpt">
                            {{ $article->extraitOuDefaut(120) }}
                        </p>

                        <div class="news-card-footer">
                            <a href="{{ route('actualites.show', $article->slug) }}" class="news-card-link">
                                Lire la suite <i class="fas fa-arrow-right"></i>
                            </a>
                            @if($article->auteur)
                            <span class="news-card-author">
                                <i class="fas fa-user-edit"></i>
                                {{ $article->auteur->nom }}
                            </span>
                            @endif
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <div class="section-footer">
                <a href="{{ route('actualites.index') }}" class="btn btn-outline">
                    <i class="fas fa-newspaper"></i> Voir toutes les actualités
                </a>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================ --}}
{{-- SECTION TÉMOIGNAGES (DYNAMIQUE) --}}
{{-- ============================================ --}}
@if($temoignages->count() > 0)
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Ils témoignent</span>
            <h2 class="section-title">L'impact de nos actions</h2>
            <p class="section-description">
                Les voix de ceux qui ont bénéficié de l'accompagnement de PHOVE
                dans leur communauté.
            </p>
        </div>

        <div class="testimonials-grid">
            @foreach($temoignages as $temoignage)
                @include('public.partials.testimonial-card', ['temoignage' => $temoignage])
            @endforeach
        </div>
    </div>
</section>
@endif

    {{-- ============================================ --}}
{{-- SECTION PARTENAIRES (DYNAMIQUE) --}}
{{-- ============================================ --}}
<section class="partners-section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Ils nous font confiance</span>
            <h2 class="section-title">Nos partenaires internationaux</h2>
            <p class="section-description">
                Organisations internationales et institutions qui soutiennent notre mission humanitaire.
            </p>
        </div>

        <div class="partners-grid">

            {{-- Partenaires dynamiques --}}
            @forelse($partenaires as $partenaire)
                @include('public.partials.partner-card', ['partenaire' => $partenaire])
            @empty
                <div class="partners-empty">
                    <i class="fas fa-handshake"></i>
                    <p>Aucun partenaire publié pour le moment.</p>
                </div>
            @endforelse

            {{-- Carte "Devenir partenaire" (toujours affichée en dernier) --}}
            <div class="partner-item">
                <a href="{{ url('/devenir-partenaire') }}" class="partner-become-link">
                    <div class="partner-become-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <span class="partner-become-title">Devenir partenaire</span>
                    <span class="partner-become-desc">
                        Rejoignez notre mission humanitaire
                    </span>
                </a>
            </div>

        </div>
    </div>
</section>

    {{-- ============================================ --}}
    {{-- SECTION CTA — APPEL À L'ACTION --}}
    {{-- ============================================ --}}
    {{-- ============================================ --}}
    {{-- SECTION GALERIE — 9 PHOTOS RÉCENTES --}}
    {{-- ============================================ --}}
    <section class="home-gallery-section" id="galerie-recente">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Nos actions en images</span>
                <h2 class="section-title">Notre galerie</h2>
                <p class="section-description">
                    Quelques moments de nos actions humanitaires sur le terrain.
                    Découvrez les images qui témoignent de notre engagement auprès des communautés.
                </p>
            </div>

            @if(isset($galerieAccueil) && $galerieAccueil->isNotEmpty())
                <div class="home-gallery-grid">
                    @foreach($galerieAccueil as $media)
                        @php
                            $imageUrl = $media->urlMiniature();
                            $imageTitre = $media->titre ?: 'Action humanitaire de PHOVE';
                        @endphp
                        @if($imageUrl)
                            <a class="home-gallery-item"
                               href="{{ route('galerie.index') }}"
                               aria-label="Découvrir la galerie : {{ $imageTitre }}">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $imageTitre }}"
                                     loading="lazy"
                                     decoding="async">
                                <span class="home-gallery-overlay">
                                    <span class="home-gallery-caption">{{ $imageTitre }}</span>
                                    <span class="home-gallery-zoom" aria-hidden="true">
                                        <i class="fas fa-images"></i>
                                    </span>
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="home-gallery-empty">
                    <i class="fas fa-images" aria-hidden="true"></i>
                    <p>Les photos de nos actions seront bientôt disponibles.</p>
                </div>
            @endif

            <div class="home-gallery-footer">
                <a href="{{ route('galerie.index') }}" class="btn btn-primary">
                    <i class="fas fa-images"></i> Voir toute la galerie
                </a>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">
                    Vous aussi, <span class="highlight">faites la différence</span>
                </h2>
                <p class="cta-text">
                    Chaque don, chaque adhésion, chaque action compte. Rejoignez-nous dans notre 
                    mission d'assistance aux orphelins, veuves et personnes démunies. 
                    « Aime ton prochain comme toi-même » — Romain 13:8.
                </p>
                <div class="cta-buttons">
                    <a href="{{ url('/faire-un-don') }}" class="btn btn-accent btn-lg">
                        <i class="fas fa-heart"></i> Faire un don maintenant
                    </a>
                    <a href="{{ url('/devenir-membre') }}" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-user-plus"></i> Devenir membre
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-home.js') }}"></script>
@endsection