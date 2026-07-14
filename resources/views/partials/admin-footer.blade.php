{{-- ============================================ --}}
{{-- FOOTER ADMINISTRATEUR — AHD-RDC --}}
{{-- ============================================ --}}
<footer class="admin-footer">
    
    {{-- Informations de session --}}
    <div class="footer-session">
        <div class="footer-session-item">
            <i class="fas fa-user-shield"></i>
            <span>Connecté en tant que <strong>
                @if(Auth::user()->role === 'super_admin')
                    Super Administrateur
                @elseif(Auth::user()->role === 'admin')
                    Administrateur
                @else
                    Éditeur
                @endif
            </strong></span>
        </div>
        <div class="footer-session-item">
            <i class="fas fa-globe"></i>
            <span>Session active depuis <strong>{{ \Carbon\Carbon::parse(session('login_time', now()))->diffForHumans(null, true) }}</strong></span>
        </div>
    </div>

    {{-- Copyright + Heure --}}
    <div class="footer-copyright">
        <span>&copy; {{ date('Y') }} <strong>AHD-RDC</strong></span>
        <span class="footer-slogan">« Être ensemble pour le bien de la communauté »</span>
        <span style="margin-left: 12px;">
            <i class="fas fa-clock"></i> 
            <span class="footer-clock-time" id="footerClock">00:00:00</span>
        </span>
    </div>

    {{-- Version + Liens --}}
    <div class="footer-version">
        <div class="footer-links">
            <a href="{{ url('/') }}" target="_blank" title="Voir le site public">
                <i class="fas fa-external-link-alt"></i> Site public
            </a>
            <span class="footer-links-divider"></span>
            <a href="#" title="Documentation">
                <i class="fas fa-book"></i> Aide
            </a>
            <span class="footer-links-divider"></span>
            <a href="#" title="Signaler un problème">
                <i class="fas fa-bug"></i> Support
            </a>
        </div>
        <span class="footer-version-badge">
            <i class="fas fa-code-branch"></i> v1.0.0
        </span>
    </div>

</footer>