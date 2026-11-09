{{-- TOP BAR — PHOVE : coordonnées et réseaux configurés depuis l'administration --}}
@php
    $siteSettings = $siteSettings ?? [];
    $phone = data_get($siteSettings, 'contact_telephone_1');
    $email = data_get($siteSettings, 'contact_email');
    $address = data_get($siteSettings, 'contact_adresse');
    $socialLinks = [
        'facebook' => ['url' => data_get($siteSettings, 'social_facebook'), 'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
        'twitter' => ['url' => data_get($siteSettings, 'social_twitter'), 'icon' => 'fa-x-twitter', 'label' => 'X'],
        'instagram' => ['url' => data_get($siteSettings, 'social_instagram'), 'icon' => 'fa-instagram', 'label' => 'Instagram'],
        'linkedin' => ['url' => data_get($siteSettings, 'social_linkedin'), 'icon' => 'fa-linkedin-in', 'label' => 'LinkedIn'],
        'youtube' => ['url' => data_get($siteSettings, 'social_youtube'), 'icon' => 'fa-youtube', 'label' => 'YouTube'],
        'whatsapp' => ['url' => data_get($siteSettings, 'social_whatsapp') ? 'https://wa.me/' . preg_replace('/\\D+/', '', data_get($siteSettings, 'social_whatsapp')) : null, 'icon' => 'fa-whatsapp', 'label' => 'WhatsApp'],
    ];
@endphp
<div class="topbar" id="topbar">
    <div class="container">
        <div class="topbar-inner">
            <div class="topbar-left">
                @if($phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="topbar-contact" title="Appeler PHOVE">
                        <i class="fas fa-phone-alt"></i><span>{{ $phone }}</span>
                    </a>
                @endif
                @if($email)
                    <a href="mailto:{{ $email }}" class="topbar-contact" title="Envoyer un email">
                        <i class="fas fa-envelope"></i><span>{{ $email }}</span>
                    </a>
                @endif
                @if($address)
                    <span class="topbar-contact" title="Adresse du siège">
                        <i class="fas fa-map-marker-alt"></i><span>{{ $address }}</span>
                    </span>
                @endif
            </div>
            <div class="topbar-right">
                <div class="topbar-socials">
                    @foreach($socialLinks as $social)
                        @if(!empty($social['url']))
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="topbar-social" title="{{ $social['label'] }}">
                                <i class="fab {{ $social['icon'] }}"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
                @if(collect($socialLinks)->contains(fn ($social) => !empty($social['url'])))
                    <span class="topbar-divider"></span>
                @endif
                <div class="topbar-languages">
                    <button class="topbar-lang-btn" id="langToggle" title="Changer de langue">
                        <i class="fas fa-globe"></i><span id="currentLang">FR</span><i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="topbar-lang-dropdown" id="langDropdown">
                        <a href="#" class="topbar-lang-option active" data-lang="fr"><i class="fas fa-flag flag-fr"></i><span>Français</span></a>
                        <a href="#" class="topbar-lang-option" data-lang="en"><i class="fas fa-flag flag-en"></i><span>English</span></a>
                        <a href="#" class="topbar-lang-option" data-lang="sw"><i class="fas fa-flag flag-rdc"></i><span>Kiswahili</span></a>
                        <a href="#" class="topbar-lang-option" data-lang="ln"><i class="fas fa-flag flag-rdc"></i><span>Lingála</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>