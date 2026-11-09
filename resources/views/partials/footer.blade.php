{{-- ============================================ --}}
{{-- FOOTER — PHOVE --}}
{{-- Logo, Liens, Domaines, Newsletter, Copyright --}}
{{-- ============================================ --}}
<footer class="footer" id="footer">
    <div class="container">

        {{-- Rangée supérieure : 4 colonnes --}}
        <div class="footer-top">
            <div class="row">

                {{-- Colonne 1 : Logo + Présentation + Réseaux --}}
                <div class="col-4 footer-col">
                    <div class="footer-brand">
                        <a href="{{ url('/') }}" class="footer-logo">
                            <img src="{{ data_get($siteSettings ?? [], 'site_logo') ? media_url(data_get($siteSettings, 'site_logo')) : asset('assets/images/logo-phove.png') }}" alt="Logo {{ data_get($siteSettings ?? [], 'site_name', 'PHOVE') }}" class="footer-logo-img">
                            <div class="footer-logo-text">
                                <span class="footer-logo-name">{{ data_get($siteSettings ?? [], 'site_name', 'PHOVE') }}</span>
                                <span class="footer-logo-subtitle">{{ data_get($siteSettings ?? [], 'site_full_name', 'Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies') }}</span>
                            </div>
                        </a>
                    </div>
                    <p class="footer-description">
                        {{ data_get($siteSettings ?? [], 'site_description', 'Organisation non gouvernementale apolitique à caractère social et spirituel, créée le 30 août 2012 à Goma.') }}
                    </p>
                    <div class="footer-contact-list">
                        @if(data_get($siteSettings ?? [], 'contact_adresse'))
                            <div class="footer-contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ data_get($siteSettings, 'contact_adresse') }}</span>
                            </div>
                        @endif
                        @if(data_get($siteSettings ?? [], 'contact_telephone_1'))
                            <div class="footer-contact-item">
                                <i class="fas fa-phone-alt"></i>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', data_get($siteSettings, 'contact_telephone_1')) }}">{{ data_get($siteSettings, 'contact_telephone_1') }}</a>
                            </div>
                        @endif
                        @if(data_get($siteSettings ?? [], 'contact_email'))
                            <div class="footer-contact-item">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:{{ data_get($siteSettings, 'contact_email') }}">{{ data_get($siteSettings, 'contact_email') }}</a>
                            </div>
                        @endif
                    </div>
                    <div class="footer-socials">
                        @foreach([
                            ['url' => data_get($siteSettings ?? [], 'social_facebook'), 'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
                            ['url' => data_get($siteSettings ?? [], 'social_twitter'), 'icon' => 'fa-x-twitter', 'label' => 'X'],
                            ['url' => data_get($siteSettings ?? [], 'social_instagram'), 'icon' => 'fa-instagram', 'label' => 'Instagram'],
                            ['url' => data_get($siteSettings ?? [], 'social_linkedin'), 'icon' => 'fa-linkedin-in', 'label' => 'LinkedIn'],
                            ['url' => data_get($siteSettings ?? [], 'social_youtube'), 'icon' => 'fa-youtube', 'label' => 'YouTube'],
                        ] as $social)
                            @if($social['url'])
                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="footer-social" title="{{ $social['label'] }}">
                                    <i class="fab {{ $social['icon'] }}"></i>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Colonne 2 : Liens rapides --}}
                <div class="col-2 footer-col">
                    <h4 class="footer-title">Liens rapides</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/') }}"><i class="fas fa-chevron-right"></i> Accueil</a></li>
                        <li><a href="{{ url('/qui-sommes-nous') }}"><i class="fas fa-chevron-right"></i> Qui sommes-nous ?</a></li>
                        <li><a href="{{ url('/projets') }}"><i class="fas fa-chevron-right"></i> Nos projets</a></li>
                        <li><a href="{{ url('/actualites') }}"><i class="fas fa-chevron-right"></i> Actualités</a></li>
                        <li><a href="{{ url('/gouvernance') }}"><i class="fas fa-chevron-right"></i> Gouvernance</a></li>
                        <li><a href="{{ url('/contact') }}"><i class="fas fa-chevron-right"></i> Contact</a></li>
                        <li><a href="{{ url('/domaines-intervention') }}"><i class="fas fa-chevron-right"></i> Domaines d'intervention</a></li>
                        <li><a href="{{ url('/partenaires') }}"><i class="fas fa-chevron-right"></i> Nos Partenaires</a></li>
                        <li><a href="{{ route('transparence', ['tab' => 'financiers']) }}"><i class="fas fa-chevron-right"></i> Rapports financiers</a></li>
                        <li><a href="{{ route('transparence', ['tab' => 'annuels']) }}"><i class="fas fa-chevron-right"></i> Rapports annuels</a></li>
                        <li><a href="{{ route('transparence', ['tab' => 'documents']) }}"><i class="fas fa-chevron-right"></i> Documents officiels</a></li>
                    </ul>
                </div>

                {{-- Colonne 3 : Domaines d'intervention --}}
                <div class="col-3 footer-col">
                    <h4 class="footer-title">Nos domaines d'intervention</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/projets?domaine=sante_nutrition') }}"><i class="fas fa-heartbeat"></i> Santé-Nutrition</a></li>
                        <li><a href="{{ url('/projets?domaine=education') }}"><i class="fas fa-graduation-cap"></i> Éducation & Formation</a></li>
                        <li><a href="{{ url('/projets?domaine=protection') }}"><i class="fas fa-shield-alt"></i> Protection Enfants & Femmes</a></li>
                        <li><a href="{{ url('/projets?domaine=wash') }}"><i class="fas fa-tint"></i> Eau, Hygiène & Assainissement</a></li>
                        <li><a href="{{ url('/projets?domaine=securite_alimentaire') }}"><i class="fas fa-seedling"></i> Sécurité Alimentaire & Agriculture</a></li>
                        <li><a href="{{ url('/projets?domaine=environnement') }}"><i class="fas fa-tree"></i> Environnement</a></li>
                        <li><a href="{{ url('/projets?domaine=paix') }}"><i class="fas fa-dove"></i> Paix & Cohésion sociale</a></li>
                    </ul>
                </div>

                {{-- Colonne 4 : Impliquez-vous + Newsletter --}}
                <div class="col-3 footer-col">
                    <h4 class="footer-title">Impliquez-vous</h4>
                    <ul class="footer-links">
                        <li><a href="{{ url('/faire-un-don') }}"><i class="fas fa-heart text-accent"></i> Faire un don</a></li>
                        <li><a href="{{ url('/devenir-membre') }}"><i class="fas fa-user-plus"></i> Devenir membre</a></li>
                        <li><a href="{{ url('/devenir-benevole') }}"><i class="fas fa-handshake"></i> Devenir bénévole</a></li>
                        <li><a href="{{ url('/devenir-partenaire') }}"><i class="fas fa-building"></i> Devenir partenaire</a></li>
                    </ul>

                    {{-- Newsletter --}}
                    <div class="footer-newsletter">
                        <h5 class="footer-newsletter-title">
                            <i class="fas fa-paper-plane"></i> Newsletter
                        </h5>
                        <p class="footer-newsletter-text">
                            Recevez nos actualités et rapports d'activités directement par email.
                        </p>
                        <form action="{{ url('/newsletter/subscribe') }}" method="POST" class="footer-newsletter-form">
                            @csrf
                            <div class="newsletter-input-wrapper">
                                <input type="email" name="email" class="newsletter-input" placeholder="Votre adresse email" required>
                                <button type="submit" class="newsletter-btn" title="S'abonner">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Rangée inférieure : Copyright + Mentions --}}
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <p class="footer-copyright">
                    &copy; {{ date('Y') }} <strong>{{ data_get($siteSettings ?? [], 'site_name', 'PHOVE') }}</strong>. Tous droits réservés. 
                    <span class="footer-slogan">« {{ data_get($siteSettings ?? [], 'site_slogan', 'Compassion avec la population') }} »</span>
                </p>
                <div class="footer-bottom-links">
                    <a href="{{ url('/mentions-legales') }}">Mentions légales</a>
                    <span class="footer-bottom-divider">|</span>
                    <a href="{{ url('/politique-confidentialite') }}">Politique de confidentialité</a>
                    <span class="footer-bottom-divider">|</span>
                    <a href="{{ url('/transparence/documents-officiels') }}">Documents officiels</a>
                </div>
            </div>
        </div>
    </div>
</footer>