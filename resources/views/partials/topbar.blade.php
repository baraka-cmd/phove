{{-- ============================================ --}}
{{-- TOP BAR — AHD-RDC --}}
{{-- Contacts, Réseaux sociaux, Langues --}}
{{-- ============================================ --}}
<div class="topbar" id="topbar">
    <div class="container">
        <div class="topbar-inner">

            {{-- Gauche : Contacts --}}
            <div class="topbar-left">
                <a href="tel:+243994656250" class="topbar-contact" title="Appeler AHD-RDC">
                    <i class="fas fa-phone-alt"></i>
                    <span>+243 994 656 250</span>
                </a>
                <a href="mailto:contact@ahd-rdc.org" class="topbar-contact" title="Envoyer un email">
                    <i class="fas fa-envelope"></i>
                    <span>contact@ahd-rdc.org</span>
                </a>
                <span class="topbar-contact" title="Localisation du siège">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Minova, Kalehe, Sud-Kivu, RD Congo</span>
                </span>
            </div>

            {{-- Droite : Réseaux sociaux + Langues --}}
            <div class="topbar-right">

                {{-- Réseaux sociaux --}}
                <div class="topbar-socials">
                    <a href="https://facebook.com/ahdrdc" target="_blank" rel="noopener noreferrer" class="topbar-social" title="Facebook AHD-RDC">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/ahdrdc" target="_blank" rel="noopener noreferrer" class="topbar-social" title="Twitter AHD-RDC">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                    <a href="https://instagram.com/ahdrdc" target="_blank" rel="noopener noreferrer" class="topbar-social" title="Instagram AHD-RDC">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://linkedin.com/company/ahdrdc" target="_blank" rel="noopener noreferrer" class="topbar-social" title="LinkedIn AHD-RDC">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://youtube.com/@ahdrdc" target="_blank" rel="noopener noreferrer" class="topbar-social" title="YouTube AHD-RDC">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>

                {{-- Séparateur --}}
                <span class="topbar-divider"></span>

                {{-- Sélecteur de langues nettoyé --}}
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