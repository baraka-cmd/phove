@extends('layouts.public')

@section('title', 'Organigramme')

@section('meta_description', 'Découvrez la structure organisationnelle complète de PHOVE : Assemblée Générale, Conseil d\'Administration, Direction Générale, Chefs de Missions, Chargés de Programmes et les 24 postes opérationnels.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/organigramme.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <h1 class="page-header-title">Organigramme</h1>
            <p class="page-header-subtitle">
                La structure organisationnelle complète de PHOVE, conformément aux articles 10 à 17 des statuts.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ url('/qui-sommes-nous') }}">Qui sommes-nous ?</a>
                <i class="fas fa-chevron-right"></i>
                <a href="{{ url('/gouvernance') }}">Gouvernance</a>
                <i class="fas fa-chevron-right"></i>
                <span>Organigramme</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 1 : ORGANIGRAMME VISUEL — Pyramide PHOVE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Structure organisationnelle</span>
                <h2 class="section-title">Notre organisation pyramidale</h2>
                <p class="section-description">
                    Conformément aux <strong>articles 10 à 17 des statuts</strong>, voici la 
                    structure hiérarchique complète de PHOVE, du sommet stratégique à la base opérationnelle.
                </p>
            </div>

            {{-- Organigramme --}}
            <div class="org-chart">
                {{-- Niveau 1 : Assemblée Générale --}}
                <div class="org-level">
                    <div class="org-box org-box-ag">
                        <div class="org-box-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Assemblée Générale</h3>
                            <p class="org-box-subtitle">Organe suprême qui prend les décisions</p>
                            <span class="org-box-info">Tous les membres effectifs — Article 11a</span>
                        </div>
                    </div>
                </div>

                {{-- Lignes de connexion --}}
                <div class="org-connector">
                    <div class="org-line"></div>
                    <div class="org-split">
                        <div class="org-line-branch left"></div>
                        <div class="org-line-branch right"></div>
                    </div>
                </div>

                {{-- Niveau 2 : CA, CC, CS, CG --}}
                <div class="org-level org-level-4">
                    <div class="org-box org-box-ca">
                        <div class="org-box-icon"><i class="fas fa-user-tie"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Conseil d'Administration</h3>
                            <p class="org-box-subtitle">
                                {{ $membresCA->count() }} membre(s) actif(s)
                            </p>
                            <span class="org-box-info">Article 11b</span>
                        </div>
                    </div>

                    <div class="org-box org-box-cc">
                        <div class="org-box-icon"><i class="fas fa-search-dollar"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Commission de Contrôle</h3>
                            <p class="org-box-subtitle">
                                {{ $membresCC->count() }} membre(s) actif(s)
                            </p>
                            <span class="org-box-info">Article 11c — Trimestrielle</span>
                        </div>
                    </div>

                    <div class="org-box org-box-cs">
                        <div class="org-box-icon"><i class="fas fa-clipboard-check"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Comité de Suivi</h3>
                            <p class="org-box-subtitle">Collabore avec la CC</p>
                            <span class="org-box-info">Article 38</span>
                        </div>
                    </div>

                    <div class="org-box org-box-cg">
                        <div class="org-box-icon"><i class="fas fa-tasks"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Comité de Gestion</h3>
                            <p class="org-box-subtitle">Travaille sous le DG</p>
                            <span class="org-box-info">Article 39</span>
                        </div>
                    </div>
                </div>

                {{-- Ligne vers DG --}}
                <div class="org-connector">
                    <div class="org-line org-line-single"></div>
                </div>

                {{-- Niveau 3 : Direction Générale --}}
                <div class="org-level">
                    <div class="org-box org-box-dg">
                        <div class="org-box-icon"><i class="fas fa-user-tie"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Directeur Général & Coordonnateur</h3>
                            <p class="org-box-subtitle">
                                @if($directeurGeneral)
                                    {{ $directeurGeneral->membre->nomComplet() }}
                                @else
                                    Apôtre Philippe KABOYI MUSAMUGUMA
                                @endif
                            </p>
                            <span class="org-box-info">Mandat 6 ans renouvelable — Article 18</span>
                        </div>
                    </div>
                </div>

                {{-- Lignes vers les départements --}}
                <div class="org-connector">
                    <div class="org-line"></div>
                    <div class="org-split">
                        <div class="org-line-branch left"></div>
                        <div class="org-line-branch right"></div>
                    </div>
                </div>

                {{-- Niveau 4 : Chefs de Missions et Chargés de Programmes --}}
                <div class="org-level org-level-2">
                    <div class="org-box org-box-mission">
                        <div class="org-box-icon"><i class="fas fa-globe-africa"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Chefs de Missions</h3>
                            <p class="org-box-subtitle">
                                {{ $chefsMissions->count() }} membre(s)
                            </p>
                            <span class="org-box-info">Article 19 — Rapports 48h</span>
                        </div>
                    </div>

                    <div class="org-box org-box-programme">
                        <div class="org-box-icon"><i class="fas fa-clipboard-list"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Chargés des Programmes</h3>
                            <p class="org-box-subtitle">
                                {{ $chargesProgrammes->count() }} membre(s)
                            </p>
                            <span class="org-box-info">Article 20 — Rapports 48h</span>
                        </div>
                    </div>
                </div>

                {{-- Ligne vers services --}}
                <div class="org-connector">
                    <div class="org-line"></div>
                    <div class="org-split org-split-4">
                        <div class="org-line-branch"></div>
                        <div class="org-line-branch"></div>
                        <div class="org-line-branch"></div>
                        <div class="org-line-branch"></div>
                    </div>
                </div>

                {{-- Niveau 5 : Services administratifs et financiers --}}
                <div class="org-level org-level-4">
                    <div class="org-box org-box-sub">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-file-alt"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Secrétaires Exécutifs</h3>
                            <span class="org-box-info">Article 21</span>
                        </div>
                    </div>
                    <div class="org-box org-box-sub">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-calculator"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Comptables Sectionnaires</h3>
                            <span class="org-box-info">Article 22</span>
                        </div>
                    </div>
                    <div class="org-box org-box-sub">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-cash-register"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Trésoriers & Caissiers</h3>
                            <span class="org-box-info">Article 24</span>
                        </div>
                    </div>
                    <div class="org-box org-box-sub">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-user-cog"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Secrétaires Administratifs</h3>
                            <span class="org-box-info">Article 23</span>
                        </div>
                    </div>
                </div>

                {{-- Ligne vers opérations terrain --}}
                <div class="org-connector">
                    <div class="org-line org-line-single"></div>
                </div>

                {{-- Niveau 6 : Opérations terrain --}}
                <div class="org-level org-level-4">
                    <div class="org-box org-box-terrain">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Encadreurs Moniteurs</h3>
                            <span class="org-box-info">Article 25</span>
                        </div>
                    </div>
                    <div class="org-box org-box-terrain">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-brain"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Psychologues</h3>
                            <span class="org-box-info">Article 26</span>
                        </div>
                    </div>
                    <div class="org-box org-box-terrain">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-stethoscope"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Médecins</h3>
                            <span class="org-box-info">Article 27</span>
                        </div>
                    </div>
                    <div class="org-box org-box-terrain">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-dove"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Prédicateurs</h3>
                            <span class="org-box-info">Article 28 — Bénévoles</span>
                        </div>
                    </div>
                </div>

                {{-- Ligne vers logistique --}}
                <div class="org-connector">
                    <div class="org-line org-line-single"></div>
                </div>

                {{-- Niveau 7 : Logistique --}}
                <div class="org-level org-level-4">
                    <div class="org-box org-box-logistique">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-box"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Chargés de Ravitaillement</h3>
                            <span class="org-box-info">Article 29</span>
                        </div>
                    </div>
                    <div class="org-box org-box-logistique">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-warehouse"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Chargés de Magasins/Dépôts</h3>
                            <span class="org-box-info">Article 30</span>
                        </div>
                    </div>
                    <div class="org-box org-box-logistique">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-truck"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Chargés de Transport</h3>
                            <span class="org-box-info">Article 31</span>
                        </div>
                    </div>
                    <div class="org-box org-box-logistique">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-shield-alt"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Protocole & Sécurité</h3>
                            <span class="org-box-info">Article 32</span>
                        </div>
                    </div>
                </div>

                {{-- Ligne vers intendance --}}
                <div class="org-connector">
                    <div class="org-line org-line-single"></div>
                </div>

                {{-- Niveau 8 : Intendance --}}
                <div class="org-level org-level-3">
                    <div class="org-box org-box-intendance">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-utensils"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Cuisiniers</h3>
                            <span class="org-box-info">Article 33</span>
                        </div>
                    </div>
                    <div class="org-box org-box-intendance">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-tshirt"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Lavadeurs</h3>
                            <span class="org-box-info">Article 34</span>
                        </div>
                    </div>
                    <div class="org-box org-box-intendance">
                        <div class="org-box-icon org-box-icon-sm"><i class="fas fa-tree"></i></div>
                        <div class="org-box-content">
                            <h3 class="org-box-title">Jardiniers</h3>
                            <span class="org-box-info">Article 34</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2 : LÉGENDE --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-light">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Comprendre l'organigramme</span>
                <h2 class="section-title">Légende</h2>
            </div>

            <div class="legend-grid">
                <div class="legend-item">
                    <div class="legend-color legend-ag"></div>
                    <div class="legend-text">
                        <strong>Assemblée Générale</strong>
                        <span>Organe suprême — Article 11a</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-ca"></div>
                    <div class="legend-text">
                        <strong>Conseil d'Administration</strong>
                        <span>Exécute les décisions — Article 11b</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-cc"></div>
                    <div class="legend-text">
                        <strong>Commission de Contrôle</strong>
                        <span>Supervise — Article 11c</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-cs"></div>
                    <div class="legend-text">
                        <strong>Comité de Suivi</strong>
                        <span>Collabore avec la CC — Article 38</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-cg"></div>
                    <div class="legend-text">
                        <strong>Comité de Gestion</strong>
                        <span>Travaille sous le DG — Article 39</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-dg"></div>
                    <div class="legend-text">
                        <strong>Directeur Général</strong>
                        <span>Mandat 6 ans — Article 18</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-mission"></div>
                    <div class="legend-text">
                        <strong>Chefs de Missions & Chargés de Programmes</strong>
                        <span>Articles 19-20</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-sub"></div>
                    <div class="legend-text">
                        <strong>Services administratifs et financiers</strong>
                        <span>Articles 21-24</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-terrain"></div>
                    <div class="legend-text">
                        <strong>Opérations terrain</strong>
                        <span>Articles 25-28</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-logistique"></div>
                    <div class="legend-text">
                        <strong>Logistique</strong>
                        <span>Articles 29-32</span>
                    </div>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-intendance"></div>
                    <div class="legend-text">
                        <strong>Intendance</strong>
                        <span>Articles 33-34</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3 : FONCTIONNEMENT --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="content-grid">
                <div class="content-block">
                    <span class="section-subtitle">Principes</span>
                    <h2 class="section-title text-left">Comment fonctionne notre organisation</h2>

                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>L'Assemblée Générale</strong> est l'organe suprême qui prend les décisions (Article 11a)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Le Conseil d'Administration</strong> exécute les décisions de l'AG (Article 11b)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Le Directeur Général</strong> coordonne toutes les activités et représente PHOVE partout (Article 18)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Les Chefs de Missions</strong> coordonnent par continent et transmettent les rapports en 48h (Article 19)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Les Chargés de Programmes</strong> coordonnent par pays sous mandat du CA (Article 20)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>La Commission de Contrôle</strong> vérifie les opérations chaque trimestre (Article 35)</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Les <strong>Structures territoriales</strong> (Stations, Bases, Antennes) exécutent sur le terrain (Article 11d-g)</span>
                        </li>
                    </ul>
                </div>
                <div class="content-image-wrapper">
                    <img src="{{ asset('assets/images/about/about-phove.jpg') }}" 
                         alt="Structure PHOVE" 
                         class="content-image"
                         onerror="this.src='https://images.unsplash.com/photo-1521791136064-7986c2920216?w=600'">
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
                    Une <span class="highlight-page">structure</span> au service des plus démunis
                </h2>
                <p class="cta-text-page">
                    Notre organisation est conçue pour garantir l'efficacité, la transparence 
                    et la redevabilité dans toutes nos actions humanitaires.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/gouvernance') }}" class="btn-page-don">
                        <i class="fas fa-sitemap"></i> Voir la gouvernance
                    </a>
                    <a href="{{ url('/transparence/documents-officiels') }}" class="btn-page-outline">
                        <i class="fas fa-file-alt"></i> Documents officiels
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/organigramme.js') }}"></script>
@endsection