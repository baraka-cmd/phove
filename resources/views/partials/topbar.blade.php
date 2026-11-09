{{-- ============================================ --}}
{{-- TOP BAR — PHOVE --}}
{{-- Contacts, Réseaux sociaux, Langues --}}
{{-- ============================================ --}}
@php
    $topbarPhone = data_get($siteSettings ?? [], 'contact_telephone_1') ?: '+243 970 872 910';
    $topbarEmail = data_get($siteSettings ?? [], 'contact_email') ?: 'phovemonde@gmail.com';
    $topbarAddress = data_get($siteSettings ?? [], 'contact_adresse') ?: 'Goma, Nord-Kivu, RD Congo';
@endphp
<div class="topbar" id="topbar">
    <div class="container">
        <div class="topbar-inner">

            {{-- Gauche : Contacts --}}
            <div class="topbar-left">
                <a href="tel:{{ preg_replace('/\\s+/', '', $topbarPhone) }}" class="topbar-contact" title="Appeler PHOVE">
                    <i class="fas fa-phone-alt"></i>
                    <span>{{ $topbarPhone }}</span>
                </a>
                <a href="mailto:{{ $topbarEmail }}" class="topbar-contact" title="Envoyer un email">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $topbarEmail }}</span>
                </a>
                <span class="topbar-contact" title="Localisation du siège">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $topbarAddress }}</span>
                </span>
            </div>

            {{-- Droite : Réseaux sociaux + Langues --}}
            <div class="topbar-right">

                {{-- Réseaux sociaux --}}
                <div class="topbar-socials">
                    @foreach ([
                        'social_facebook' => ['Facebook PHOVE', 'fab fa-facebook-f'],
                        'social_twitter' => ['X PHOVE', 'fab fa-x-twitter'],
                        'social_instagram' => ['Instagram PHOVE', 'fab fa-instagram'],
                        'social_tiktok' => ['TikTok PHOVE', 'fab fa-tiktok'],
                        'social_youtube' => ['YouTube PHOVE', 'fab fa-youtube'],
                        'social_whatsapp' => ['Chaîne WhatsApp PHOVE', 'fab fa-whatsapp'],
                    ] as $settingKey => [$socialTitle, $socialIcon])
                        @if (filled(data_get($siteSettings ?? [], $settingKey)))
                            <a href="{{ data_get($siteSettings ?? [], $settingKey) }}" target="_blank" rel="noopener noreferrer" class="topbar-social" title="{{ $socialTitle }}" aria-label="{{ $socialTitle }}">
                                <i class="{{ $socialIcon }}"></i>
                            </a>
                        @endif
                    @endforeach
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