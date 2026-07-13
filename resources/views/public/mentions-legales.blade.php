@extends('layouts.public')

@section('title', 'Mentions légales')

@section('meta_description', 'Mentions légales d\'AHD-RDC : statut juridique, siège social, conditions d\'utilisation et politique de confidentialité.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/mentions-legales.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-gavel"></i>
            </div>
            <h1 class="page-header-title">Mentions légales</h1>
            <p class="page-header-subtitle">
                Informations juridiques et conditions d'utilisation de la plateforme AHD-RDC.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Mentions légales</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CONTENU --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container container-narrow">
            
            {{-- Article 1 : Éditeur --}}
            <div class="legal-block">
                <div class="legal-number">1</div>
                <div class="legal-content">
                    <h2 class="legal-title">Éditeur de la plateforme</h2>
                    <p>La plateforme <strong>www.ahd-rdc.org</strong> est éditée par :</p>
                    <div class="legal-info-card">
                        <div class="legal-info-item">
                            <i class="fas fa-building"></i>
                            <div>
                                <strong>Raison sociale</strong>
                                <span>ACTION HUMANITAIRE POUR LE DEVELOPPEMENT EN RD CONGO</span>
                            </div>
                        </div>
                        <div class="legal-info-item">
                            <i class="fas fa-tag"></i>
                            <div>
                                <strong>Sigle</strong>
                                <span>AHD-RDC</span>
                            </div>
                        </div>
                        <div class="legal-info-item">
                            <i class="fas fa-gavel"></i>
                            <div>
                                <strong>Forme juridique</strong>
                                <span>Association Sans But Lucratif (ASBL) de droit congolais</span>
                            </div>
                        </div>
                        <div class="legal-info-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Date de création</strong>
                                <span>7 janvier 2017</span>
                            </div>
                        </div>
                        <div class="legal-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Siège social</strong>
                                <span>Minova, Territoire de Kalehe, Province du Sud-Kivu, République Démocratique du Congo</span>
                            </div>
                        </div>
                        <div class="legal-info-item">
                            <i class="fas fa-phone-alt"></i>
                            <div>
                                <strong>Téléphone</strong>
                                <span>+243 994 656 250</span>
                            </div>
                        </div>
                        <div class="legal-info-item">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong>
                                <span>contact@ahd-rdc.org</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Article 2 : Représentant légal --}}
            <div class="legal-block">
                <div class="legal-number">2</div>
                <div class="legal-content">
                    <h2 class="legal-title">Représentant légal</h2>
                    <p>
                        Conformément à l'article 33 de nos statuts, le <strong>Directeur Exécutif</strong> 
                        est le gestionnaire et mandataire légal qui représente l'association vis-à-vis 
                        des tiers de droit public ou de droit privé.
                    </p>
                    <div class="legal-info-card">
                        <div class="legal-info-item">
                            <i class="fas fa-user-tie"></i>
                            <div>
                                <strong>Directeur de la publication</strong>
                                <span>Dr AKSANTI DUNIA Steeng — Coordonnateur National</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Article 3 : Hébergement --}}
            <div class="legal-block">
                <div class="legal-number">3</div>
                <div class="legal-content">
                    <h2 class="legal-title">Hébergement</h2>
                    <p>
                        La plateforme AHD-RDC est hébergée par un prestataire spécialisé. 
                        Les données sont stockées sur des serveurs sécurisés garantissant 
                        la confidentialité et l'intégrité des informations.
                    </p>
                    <p>
                        Pour toute question relative à l'hébergement, veuillez nous contacter 
                        à l'adresse : <strong>contact@ahd-rdc.org</strong>.
                    </p>
                </div>
            </div>

            {{-- Article 4 : Propriété intellectuelle --}}
            <div class="legal-block">
                <div class="legal-number">4</div>
                <div class="legal-content">
                    <h2 class="legal-title">Propriété intellectuelle</h2>
                    <p>
                        L'ensemble du contenu de la plateforme (textes, images, logos, vidéos, 
                        documents téléchargeables) est la propriété exclusive d'AHD-RDC, 
                        sauf mention contraire explicite.
                    </p>
                    <p>
                        Toute reproduction, distribution, modification, adaptation, retransmission 
                        ou publication de ces éléments est strictement interdite sans l'accord 
                        écrit préalable d'AHD-RDC.
                    </p>
                    <div class="legal-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>
                            La violation de ces dispositions constitue une contrefaçon engageant 
                            la responsabilité civile et pénale de son auteur, conformément aux 
                            lois en vigueur en République Démocratique du Congo.
                        </span>
                    </div>
                </div>
            </div>

            {{-- Article 5 : Protection des données personnelles --}}
            <div class="legal-block">
                <div class="legal-number">5</div>
                <div class="legal-content">
                    <h2 class="legal-title">Protection des données personnelles</h2>
                    <p>
                        AHD-RDC s'engage à protéger la vie privée des utilisateurs de sa 
                        plateforme conformément aux principes suivants :
                    </p>
                    <ul class="check-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Collecte limitée</strong> : nous ne collectons que les données strictement nécessaires</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Finalité déterminée</strong> : les données sont collectées pour des objectifs précis et légitimes</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Consentement</strong> : aucun traitement sans consentement préalable</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Sécurité</strong> : mesures techniques et organisationnelles pour protéger les données</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Droit d'accès</strong> : vous pouvez demander l'accès, la rectification ou la suppression de vos données</span>
                        </li>
                    </ul>
                    <p>
                        Pour exercer vos droits, contactez-nous à : <strong>contact@ahd-rdc.org</strong>.
                    </p>
                </div>
            </div>

            {{-- Article 6 : Cookies --}}
            <div class="legal-block">
                <div class="legal-number">6</div>
                <div class="legal-content">
                    <h2 class="legal-title">Utilisation des cookies</h2>
                    <p>
                        La plateforme AHD-RDC peut utiliser des cookies pour améliorer 
                        l'expérience utilisateur. Les cookies sont de petits fichiers texte 
                        stockés sur votre appareil.
                    </p>
                    <p>
                        Vous pouvez à tout moment désactiver les cookies dans les paramètres 
                        de votre navigateur. Toutefois, certaines fonctionnalités du site 
                        pourraient ne plus être disponibles.
                    </p>
                </div>
            </div>

            {{-- Article 7 : Limitation de responsabilité --}}
            <div class="legal-block">
                <div class="legal-number">7</div>
                <div class="legal-content">
                    <h2 class="legal-title">Limitation de responsabilité</h2>
                    <p>
                        AHD-RDC s'efforce de maintenir des informations exactes et à jour 
                        sur sa plateforme. Toutefois, nous ne pouvons garantir l'exactitude, 
                        la précision ou l'exhaustivité des informations mises à disposition.
                    </p>
                    <p>
                        AHD-RDC ne saurait être tenue responsable des dommages directs ou 
                        indirects résultant de l'utilisation de la plateforme ou de 
                        l'impossibilité d'y accéder.
                    </p>
                </div>
            </div>

            {{-- Article 8 : Liens hypertextes --}}
            <div class="legal-block">
                <div class="legal-number">8</div>
                <div class="legal-content">
                    <h2 class="legal-title">Liens hypertextes</h2>
                    <p>
                        La plateforme peut contenir des liens vers des sites tiers. AHD-RDC 
                        n'exerce aucun contrôle sur ces sites et décline toute responsabilité 
                        quant à leur contenu.
                    </p>
                    <p>
                        La création de liens hypertextes vers la plateforme AHD-RDC est 
                        autorisée sans accord préalable, à condition qu'ils ne créent pas 
                        de confusion sur la source des informations.
                    </p>
                </div>
            </div>

            {{-- Article 9 : Droit applicable --}}
            <div class="legal-block">
                <div class="legal-number">9</div>
                <div class="legal-content">
                    <h2 class="legal-title">Droit applicable et juridiction compétente</h2>
                    <p>
                        Les présentes mentions légales sont soumises au <strong>droit congolais</strong>. 
                        Conformément à l'article 38 de nos statuts, tout litige sera réglé 
                        à l'amiable entre les parties.
                    </p>
                    <p>
                        En cas de non-conciliation, les cours et tribunaux de la République 
                        Démocratique du Congo seront seuls compétents.
                    </p>
                </div>
            </div>

            {{-- Article 10 : Contact --}}
            <div class="legal-block">
                <div class="legal-number">10</div>
                <div class="legal-content">
                    <h2 class="legal-title">Contact</h2>
                    <p>
                        Pour toute question relative aux présentes mentions légales ou à 
                        l'utilisation de la plateforme, vous pouvez nous contacter :
                    </p>
                    <div class="legal-contact-card">
                        <div class="legal-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span><strong>Email :</strong> contact@ahd-rdc.org</span>
                        </div>
                        <div class="legal-contact-item">
                            <i class="fas fa-phone-alt"></i>
                            <span><strong>Téléphone :</strong> +243 994 656 250</span>
                        </div>
                        <div class="legal-contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><strong>Adresse :</strong> Minova, Territoire de Kalehe, Sud-Kivu, RD Congo</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Date de mise à jour --}}
            <div class="legal-update">
                <i class="fas fa-sync-alt"></i>
                <span>Dernière mise à jour : {{ date('d/m/Y') }}</span>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/mentions-legales.js') }}"></script>
@endsection