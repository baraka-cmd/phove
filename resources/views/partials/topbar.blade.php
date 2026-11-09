{{-- ============================================ --}}
{{-- TOP BAR — PHOVE --}}
{{-- Contacts, Réseaux sociaux, Langues --}}
{{-- ============================================ --}}
<div class="topbar" id="topbar">
    <div class="container">
        <div class="topbar-inner">

            {{-- Gauche : Contacts --}}
            <div class="topbar-left">
                <a href="tel:{{ preg_replace('/\\s+/', '', data_get($siteSettings ?? [], 'contact_telephone_1', '{{ data_get($siteSettings ?? [], 'contact_telephone_1', '+243 970 872 910') }}')) }}" class="topbar-contact" title="Appeler PHOVE">
                    <i class="fas fa-phone-alt"></i>
                    <span>+243 970 872 910</span>
                </a>
                <a href="mailto:{{ data_get($siteSettings ?? [], 'contact_email', '{{ data_get($siteSettings ?? [], 'contact_email', 'phovemonde@gmail.com') }}') }}" class="topbar-contact" title="Envoyer un email">
                    <i class="fas fa-envelope"></i>
                    <span>phovemonde@gmail.com</span>
                </a>
                <span class="topbar-contact" title="Localisation du siège">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Goma, Nord-Kivu, RD Congo</span>
                </span>
            </div>

            {{-- Droite : Réseaux sociaux + Langues --}}
            <div class="topbar-right">

                {{-- Réseaux sociaux --}}
                <div class="topbar-socials">
                    <a href="{{ data_get($siteSettings ?? [], 'social_facebook') ?: 'https://facebook.com/phovemonde' }}" target="_blank" rel="noopener noreferrer" class="topbar-social" title="Facebook PHOVE">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="{{ data_get($siteSettings ?? [], 'social_twitter') ?: 'https://twitter.com/phovemonde' }}" target="_blank" rel="noopener noreferrer" class="topbar-social" title="Twitter PHOVE">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                    <a href="{{ data_get($siteSettings ?? [], 'social_instagram') ?: 'https://instagram.com/phovemonde' }}" target="_blank" rel="noopener noreferrer" class="topbar-social" title="Instagram PHOVE">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="{{ data_get($siteSettings ?? [], 'social_linkedin') ?: 'https://linkedin.com/company/phovemonde' }}" target="_blank" rel="noopener noreferrer" class="topbar-social" title="LinkedIn PHOVE">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="{{ data_get($siteSettings ?? [], 'social_youtube') ?: 'https://youtube.com/@phovemonde' }}" target="_blank" rel="noopener noreferrer" class="topbar-social" title="YouTube PHOVE">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>

                {{-- Séparateur --}}
                <span class="topbar-divider"></span>

                {{-- Sélecteur de langues --}}
                <div class="topbar-languages">
                    <button class="topbar-lang-btn" id="langToggle" title="Changer de langue">
                        <i class="fas fa-globe"></i>
                        <span id="currentLang">FR</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="topbar-lang-dropdown" id="langDropdown">
                        <a href="#" class="topbar-lang-option active" data-lang="fr">
                            <i class="fas fa-flag flag-fr"></i>
                            <span>Français</span>
                        </a>
                        <a href="#" class="topbar-lang-option" data-lang="en">
                            <i class="fas fa-flag flag-en"></i>
                            <span>English</span>
                        </a>
                        <a href="#" class="topbar-lang-option" data-lang="sw">
                            <i class="fas fa-flag flag-rdc"></i>
                            <span>Kiswahili</span>
                        </a>
                        <a href="#" class="topbar-lang-option" data-lang="ln">
                            <i class="fas fa-flag flag-rdc"></i>
                            <span>Lingála</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>