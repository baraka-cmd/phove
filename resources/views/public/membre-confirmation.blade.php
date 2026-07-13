@extends('layouts.public')

@section('title', 'Demande d\'adhésion enregistrée')

@section('meta_description', 'Votre demande d\'adhésion à AHD-RDC a été enregistrée avec succès. Découvrez les prochaines étapes.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/membre-confirmation.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- SECTION CONFIRMATION --}}
    {{-- ============================================ --}}
    <section class="membre-confirmation-section">
        <div class="container">
            <div class="membre-confirmation-card">

                {{-- Icône de succès --}}
                <div class="membre-confirmation-icon-wrapper">
                    <div class="membre-confirmation-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                {{-- Titre --}}
                <h1 class="membre-confirmation-title">
                    Demande d'adhésion <span class="highlight">enregistrée</span> !
                </h1>
                <p class="membre-confirmation-subtitle">
                    Merci <strong>{{ $membre->nomComplet() }}</strong>, votre demande d'adhésion a été enregistrée avec succès.
                    @if($membre->email)
                        Un email de confirmation a été envoyé à <strong>{{ $membre->email }}</strong>.
                    @endif
                </p>

                {{-- Récapitulatif --}}
                <div class="membre-confirmation-details">
                    <h3><i class="fas fa-clipboard-list"></i> Récapitulatif de votre demande</h3>
                    <div class="membre-confirmation-grid">
                        <div class="membre-confirmation-row">
                            <span class="membre-confirmation-label">
                                <i class="fas fa-user"></i> Identité
                            </span>
                            <span class="membre-confirmation-value">{{ $membre->nomComplet() }}</span>
                        </div>
                        <div class="membre-confirmation-row">
                            <span class="membre-confirmation-label">
                                <i class="fas fa-venus-mars"></i> Sexe
                            </span>
                            <span class="membre-confirmation-value">{{ $membre->sexe === 'M' ? 'Masculin' : 'Féminin' }}</span>
                        </div>
                        <div class="membre-confirmation-row">
                            <span class="membre-confirmation-label">
                                <i class="fas fa-map-marker-alt"></i> Adresse
                            </span>
                            <span class="membre-confirmation-value">{{ $membre->adresse }}</span>
                        </div>
                        <div class="membre-confirmation-row">
                            <span class="membre-confirmation-label">
                                <i class="fas fa-phone-alt"></i> Téléphone
                            </span>
                            <span class="membre-confirmation-value">{{ $membre->telephone }}</span>
                        </div>
                        @if($membre->email)
                        <div class="membre-confirmation-row">
                            <span class="membre-confirmation-label">
                                <i class="fas fa-envelope"></i> Email
                            </span>
                            <span class="membre-confirmation-value">{{ $membre->email }}</span>
                        </div>
                        @endif
                        <div class="membre-confirmation-row">
                            <span class="membre-confirmation-label">
                                <i class="fas fa-tag"></i> Catégorie
                            </span>
                            <span class="membre-confirmation-value">
                                @if($membre->categorie === 'adherent')
                                    Membre adhérent
                                @elseif($membre->categorie === 'sympathisant')
                                    Membre sympathisant
                                @else
                                    Membre d'honneur
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Prochaines étapes --}}
                <div class="membre-prochaines-etapes">
                    <h3><i class="fas fa-tasks"></i> Prochaines étapes</h3>
                    <div class="membre-etapes-grid">
                        <div class="membre-etape-card">
                            <div class="membre-etape-numero">1</div>
                            <div class="membre-etape-content">
                                <h4>Examen du dossier</h4>
                                <p>Votre dossier sera examiné par le Conseil d'Administration lors de sa prochaine session (tous les 3 mois).</p>
                            </div>
                        </div>
                        <div class="membre-etape-card">
                            <div class="membre-etape-numero">2</div>
                            <div class="membre-etape-content">
                                <h4>Décision de l'Assemblée Générale</h4>
                                <p>L'Assemblée Générale statue sur votre adhésion à la majorité des 2/3 des membres présents.</p>
                            </div>
                        </div>
                        <div class="membre-etape-card">
                            <div class="membre-etape-numero">3</div>
                            <div class="membre-etape-content">
                                <h4>Paiement des frais</h4>
                                <p>En cas d'acceptation, vous devrez vous acquitter des frais d'adhésion de <strong>50 $</strong>.</p>
                            </div>
                        </div>
                        <div class="membre-etape-card">
                            <div class="membre-etape-numero">4</div>
                            <div class="membre-etape-content">
                                <h4>Bienvenue parmi nous !</h4>
                                <p>Vous recevrez votre carte de membre et pourrez participer pleinement à la vie de l'organisation.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informations importantes --}}
                <div class="membre-info-importantes">
                    <div class="membre-info-card">
                        <i class="fas fa-coins"></i>
                        <div>
                            <strong>Frais d'adhésion</strong>
                            <p>50 $ (cinquante dollars américains) — Article 4 du ROI</p>
                        </div>
                    </div>
                    <div class="membre-info-card">
                        <i class="fas fa-sync-alt"></i>
                        <div>
                            <strong>Cotisation mensuelle</strong>
                            <p>50 $ par mois — Article 39 du ROI</p>
                        </div>
                    </div>
                    <div class="membre-info-card">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <strong>Délai de traitement</strong>
                            <p>Votre demande sera traitée dans un délai de 1 à 3 mois</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="membre-confirmation-actions">
                    <a href="{{ url('/') }}" class="btn-cta-don">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
                    <a href="{{ url('/transparence/documents-officiels') }}" class="btn-cta-outline">
                        <i class="fas fa-file-alt"></i> Consulter nos statuts
                    </a>
                    <a href="{{ url('/faire-un-don') }}" class="btn-cta-accent">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
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
                    <span class="highlight-page">Merci</span> pour votre engagement
                </h2>
                <p class="cta-text-page">
                    Votre adhésion renforce notre capacité à agir pour le développement 
                    et la paix en République Démocratique du Congo.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/contact') }}" class="btn-page-don">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                    <a href="{{ route('projets.index') }}" class="btn-page-outline">
                        <i class="fas fa-list"></i> Voir nos projets
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/membre-confirmation.js') }}"></script>
@endsection