@extends('layouts.public')

@section('title', 'Candidature bénévole enregistrée')

@section('meta_description', 'Votre candidature bénévole a été enregistrée avec succès. AHD-RDC vous contactera dans les plus brefs délais.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/devenir-benevole.css') }}">
    <style>
        .benevole-confirmation-icon-wrapper { margin-bottom: 24px; }
        .benevole-confirmation-icon {
            width: 100px; height: 100px; border-radius: 50%;
            background: var(--color-primary-lighter); color: var(--color-primary);
            font-size: 3rem; display: flex; align-items: center; justify-content: center;
            margin: 0 auto; animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }
        @keyframes scaleIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .benevole-confirmation-title {
            font-size: 2.2rem; font-weight: 800; color: #1a1a1a; margin-bottom: 12px;
            animation: fadeInUp 0.6s ease 0.3s forwards; opacity: 0;
        }
        .benevole-confirmation-title .highlight { color: var(--color-primary); }
        .benevole-confirmation-subtitle {
            font-size: 0.95rem; color: #666; margin-bottom: 30px; line-height: 1.7;
            animation: fadeInUp 0.6s ease 0.5s forwards; opacity: 0;
        }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .benevole-next-steps {
            background: var(--color-bg-light); border-radius: 14px; padding: 24px;
            margin-bottom: 28px; text-align: left;
            animation: fadeInUp 0.6s ease 0.7s forwards; opacity: 0;
        }
        .benevole-next-steps h3 {
            font-size: 1.05rem; font-weight: 700; color: #1a1a1a;
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
            padding-bottom: 12px; border-bottom: 2px solid var(--border-color-light);
        }
        .benevole-next-steps h3 i { color: var(--color-accent); }
        .benevole-steps-list { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 12px; }
        .benevole-steps-list li {
            display: flex; align-items: flex-start; gap: 12px; font-size: 14px; color: #555; line-height: 1.6;
        }
        .benevole-steps-list li .step-num {
            width: 30px; height: 30px; border-radius: 50%; background: var(--color-primary);
            color: #fff; font-size: 13px; font-weight: 700; display: flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .benevole-confirmation-actions {
            display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;
            animation: fadeInUp 0.6s ease 0.9s forwards; opacity: 0;
        }
        .btn-benevole-primary {
            background: var(--color-primary); color: #fff; padding: 13px 26px; border-radius: 10px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px; transition: all 0.3s ease; border: 2px solid var(--color-primary);
        }
        .btn-benevole-primary:hover { background: var(--color-primary-dark); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(22, 85, 143, 0.3); color: #fff; }
        .btn-benevole-outline {
            background: transparent; color: var(--color-primary); padding: 13px 26px; border-radius: 10px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px; transition: all 0.3s ease; border: 2px solid var(--color-primary);
        }
        .btn-benevole-outline:hover { background: var(--color-primary); color: #fff; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(22, 85, 143, 0.3); }
        .btn-benevole-accent {
            background: var(--color-accent); color: #fff; padding: 13px 26px; border-radius: 10px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px; transition: all 0.3s ease; border: 2px solid var(--color-accent);
        }
        .btn-benevole-accent:hover { background: var(--color-accent-hover); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(211, 47, 47, 0.3); color: #fff; }
        @media (max-width: 768px) {
            .benevole-confirmation-actions { flex-direction: column; }
            .btn-benevole-primary, .btn-benevole-outline, .btn-benevole-accent { width: 100%; justify-content: center; }
        }
    </style>
@endsection

@section('content')

    <section class="benevole-form-section" style="padding-top: 100px;">
        <div class="container">
            <div class="benevole-form-wrapper" style="text-align: center; background: #fff; border-radius: 24px; padding: 50px 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">

                <div class="benevole-confirmation-icon-wrapper">
                    <div class="benevole-confirmation-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <h1 class="benevole-confirmation-title">
                    Candidature <span class="highlight">enregistrée</span> !
                </h1>
                <p class="benevole-confirmation-subtitle">
                    Merci pour votre engagement ! Votre candidature bénévole a été enregistrée avec succès. 
                    Notre équipe va l'examiner et reviendra vers vous dans les plus brefs délais.
                </p>

                <div class="benevole-next-steps">
                    <h3><i class="fas fa-tasks"></i> Prochaines étapes</h3>
                    <ul class="benevole-steps-list">
                        <li>
                            <span class="step-num">1</span>
                            <span>Notre équipe examine votre candidature sous <strong>48 à 72 heures</strong>.</span>
                        </li>
                        <li>
                            <span class="step-num">2</span>
                            <span>Nous vous contactons par <strong>téléphone ou email</strong> pour un entretien.</span>
                        </li>
                        <li>
                            <span class="step-num">3</span>
                            <span>Si votre profil correspond, nous définissons ensemble vos <strong>missions</strong>.</span>
                        </li>
                        <li>
                            <span class="step-num">4</span>
                            <span>Vous recevez une <strong>attestation de bénévolat</strong> valorisable.</span>
                        </li>
                    </ul>
                </div>

                <div class="benevole-confirmation-actions">
                    <a href="{{ url('/') }}" class="btn-benevole-primary">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
                    <a href="{{ route('projets.index') }}" class="btn-benevole-outline">
                        <i class="fas fa-list"></i> Voir nos projets
                    </a>
                    <a href="{{ url('/faire-un-don') }}" class="btn-benevole-accent">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                </div>

            </div>
        </div>
    </section>

    <section class="cta-section-page">
        <div class="container">
            <div class="cta-content-page">
                <h2 class="cta-title-page"><span class="highlight-page">Merci</span> pour votre engagement</h2>
                <p class="cta-text-page">Chaque bénévole est un maillon essentiel de notre chaîne de solidarité.</p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/contact') }}" class="btn-page-don"><i class="fas fa-envelope"></i> Nous contacter</a>
                    <a href="{{ url('/devenir-membre') }}" class="btn-page-outline"><i class="fas fa-user-plus"></i> Devenir membre</a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
@endsection