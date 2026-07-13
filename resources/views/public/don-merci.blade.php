@extends('layouts.public')

@section('title', 'Merci pour votre don')

@section('meta_description', 'Merci pour votre générosité. Votre don permet à AHD-RDC de continuer son action humanitaire en République Démocratique du Congo.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/don-merci.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- SECTION REMERCIEMENT --}}
    {{-- ============================================ --}}
    <section class="merci-section">
        <div class="container">
            <div class="merci-card">

                {{-- Icône de succès --}}
                <div class="merci-icon-wrapper">
                    <div class="merci-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="merci-confetti">
                        <span></span><span></span><span></span><span></span><span></span>
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                </div>

                {{-- Titre --}}
                <h1 class="merci-title">
                    Merci pour votre <span class="highlight">générosité</span> !
                </h1>
                <p class="merci-subtitle">
                    Votre don a été enregistré avec succès. Vous allez recevoir un email de confirmation 
                    à l'adresse <strong>{{ $don->donateur_email }}</strong>.
                </p>

                {{-- Détails du don --}}
                <div class="merci-details">
                    <div class="merci-detail-row">
                        <span class="merci-detail-label">
                            <i class="fas fa-hashtag"></i> Numéro de don
                        </span>
                        <span class="merci-detail-value merci-detail-ref">{{ $don->numero_recu }}</span>
                    </div>
                    <div class="merci-detail-row">
                        <span class="merci-detail-label">
                            <i class="fas fa-dollar-sign"></i> Montant
                        </span>
                        <span class="merci-detail-value">{{ number_format($don->montant, 2) }} {{ $don->devise }}</span>
                    </div>
                    <div class="merci-detail-row">
                        <span class="merci-detail-label">
                            <i class="fas fa-sync-alt"></i> Type de don
                        </span>
                        <span class="merci-detail-value">{{ $don->type_don === 'ponctuel' ? 'Don ponctuel' : 'Don récurrent mensuel' }}</span>
                    </div>
                    <div class="merci-detail-row">
                        <span class="merci-detail-label">
                            <i class="fas fa-credit-card"></i> Mode de paiement
                        </span>
                        <span class="merci-detail-value">{{ $don->modePaiementLibelle() }}</span>
                    </div>
                    @if($don->projet)
                    <div class="merci-detail-row">
                        <span class="merci-detail-label">
                            <i class="fas fa-project-diagram"></i> Projet soutenu
                        </span>
                        <span class="merci-detail-value">{{ $don->projet->titre }}</span>
                    </div>
                    @endif
                    <div class="merci-detail-row">
                        <span class="merci-detail-label">
                            <i class="fas fa-calendar-alt"></i> Date
                        </span>
                        <span class="merci-detail-value">{{ $don->date_don->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>

                {{-- Message --}}
                @if($don->message_donateur)
                <div class="merci-message">
                    <i class="fas fa-quote-left"></i>
                    <p>{{ $don->message_donateur }}</p>
                </div>
                @endif

                {{-- Instructions de paiement --}}
                @if($don->mode_paiement === 'mobile_money')
                <div class="merci-instructions">
                    <h4><i class="fas fa-mobile-alt"></i> Instructions de paiement Mobile Money</h4>
                    <p>Veuillez effectuer votre paiement aux numéros suivants :</p>
                    <div class="merci-mobile-numbers">
                        <div class="merci-mobile-number">
                            <span class="merci-mobile-label">Airtel Money</span>
                            <strong>+243 994 656 250</strong>
                        </div>
                        <div class="merci-mobile-number">
                            <span class="merci-mobile-label">Orange Money</span>
                            <strong>+243 975 105 996</strong>
                        </div>
                        <div class="merci-mobile-number">
                            <span class="merci-mobile-label">M-Pesa</span>
                            <strong>+243 973 307 807</strong>
                        </div>
                    </div>
                    <p class="merci-instruction-note">
                        <i class="fas fa-info-circle"></i> 
                        Veuillez indiquer votre numéro de don <strong>{{ $don->numero_recu }}</strong> 
                        comme référence lors du paiement.
                    </p>
                </div>
                @endif

                @if($don->mode_paiement === 'virement_bancaire')
                <div class="merci-instructions">
                    <h4><i class="fas fa-university"></i> Instructions de virement bancaire</h4>
                    <p>Nos coordonnées bancaires vous seront envoyées par email dans les plus brefs délais.</p>
                </div>
                @endif

                @if($don->mode_paiement === 'especes')
                <div class="merci-instructions">
                    <h4><i class="fas fa-money-bill-wave"></i> Remise en espèces</h4>
                    <p>
                        Vous pouvez remettre votre don en espèces à notre bureau de 
                        <strong>Minova, Territoire de Kalehe, Sud-Kivu</strong> 
                        ou à nos représentants sur le terrain.
                    </p>
                </div>
                @endif

                {{-- Actions --}}
                <div class="merci-actions">
                    <a href="{{ url('/') }}" class="btn-cta-don">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
                    <a href="{{ route('projets.index') }}" class="btn-cta-outline">
                        <i class="fas fa-list"></i> Voir nos projets
                    </a>
                    <button class="merci-share-btn btn-share" 
                            data-title="Je viens de faire un don à AHD-RDC" 
                            data-text="Je soutiens l'action humanitaire d'AHD-RDC en République Démocratique du Congo. Rejoignez-moi !"
                            title="Partager">
                        <i class="fas fa-share-alt"></i> Partager
                    </button>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION IMPACT --}}
    {{-- ============================================ --}}
    <section class="merci-impact-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Votre impact</span>
                <h2 class="section-title">Grâce à vous, nous pouvons agir</h2>
                <p class="section-description">
                    Votre don va nous permettre de continuer nos projets humanitaires 
                    et d'améliorer les conditions de vie des communautés vulnérables.
                </p>
            </div>

            <div class="merci-impact-grid">
                <div class="merci-impact-card">
                    <div class="merci-impact-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h4>Sécurité alimentaire</h4>
                    <p>Soutien à la production agricole et distribution de vivres aux ménages vulnérables.</p>
                </div>
                <div class="merci-impact-card">
                    <div class="merci-impact-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h4>Eau potable</h4>
                    <p>Aménagement de points d'eau et sensibilisation à l'hygiène communautaire.</p>
                </div>
                <div class="merci-impact-card">
                    <div class="merci-impact-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Protection</h4>
                    <p>Protection des femmes, des enfants et des personnes les plus vulnérables.</p>
                </div>
                <div class="merci-impact-card">
                    <div class="merci-impact-icon">
                        <i class="fas fa-dove"></i>
                    </div>
                    <h4>Paix</h4>
                    <p>Promotion de la cohabitation pacifique et résolution des conflits communautaires.</p>
                </div>
            </div>

            <div class="merci-transparence-link">
                <a href="{{ route('transparence', ['tab' => 'financiers']) }}" class="btn btn-outline">
                    <i class="fas fa-chart-pie"></i> Consultez nos rapports financiers
                </a>
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
                    Ensemble, <span class="highlight-page">changeons</span> des vies
                </h2>
                <p class="cta-text-page">
                    Parlez de notre mission autour de vous. Plus nous serons nombreux, 
                    plus notre impact sera grand.
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
    <script src="{{ asset('assets/js/don-merci.js') }}"></script>
@endsection