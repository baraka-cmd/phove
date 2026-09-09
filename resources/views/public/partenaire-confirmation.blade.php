@extends('layouts.public')

@section('title', 'Proposition de partenariat enregistrée')

@section('meta_description', 'Votre proposition de partenariat a été enregistrée avec succès. PHOVE vous contactera dans les plus brefs délais.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/devenir-partenaire.css') }}">
    <style>
        .partenaire-confirmation-icon-wrapper { margin-bottom: 24px; }
        .partenaire-confirmation-icon {
            width: 100px; height: 100px; border-radius: 50%;
            background: #E8F0F8; color: #1A5B9C;
            font-size: 3rem; display: flex; align-items: center; justify-content: center;
            margin: 0 auto; animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }
        @keyframes scaleIn { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .partenaire-confirmation-title {
            font-size: 2.2rem; font-weight: 800; color: #1E293B; margin-bottom: 12px;
            animation: fadeInUp 0.6s ease 0.3s forwards; opacity: 0;
        }
        .partenaire-confirmation-title .highlight { color: #1A5B9C; }
        .partenaire-confirmation-subtitle {
            font-size: 0.95rem; color: #334155; margin-bottom: 30px; line-height: 1.7;
            animation: fadeInUp 0.6s ease 0.5s forwards; opacity: 0;
        }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .partenaire-next-steps {
            background: #F8FAFC; border-radius: 14px; padding: 24px;
            margin-bottom: 28px; text-align: left;
            animation: fadeInUp 0.6s ease 0.7s forwards; opacity: 0;
        }
        .partenaire-next-steps h3 {
            font-size: 1.05rem; font-weight: 700; color: #1E293B;
            margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
            padding-bottom: 12px; border-bottom: 2px solid #E2E8F0;
        }
        .partenaire-next-steps h3 i { color: #F59E0B; }
        .partenaire-steps-list { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 12px; }
        .partenaire-steps-list li {
            display: flex; align-items: flex-start; gap: 12px; font-size: 14px; color: #334155; line-height: 1.6;
        }
        .partenaire-steps-list li .step-num {
            width: 30px; height: 30px; border-radius: 50%; background: #1A5B9C;
            color: #FFFFFF; font-size: 13px; font-weight: 700; display: flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .partenaire-confirmation-actions {
            display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;
            animation: fadeInUp 0.6s ease 0.9s forwards; opacity: 0;
        }
        .btn-partenaire-primary {
            background: #1A5B9C; color: #FFFFFF; padding: 13px 26px; border-radius: 10px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px; transition: all 0.3s ease; border: 2px solid #1A5B9C;
        }
        .btn-partenaire-primary:hover { background: #124270; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(26, 91, 156, 0.3); color: #FFFFFF; }
        .btn-partenaire-outline {
            background: transparent; color: #1A5B9C; padding: 13px 26px; border-radius: 10px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px; transition: all 0.3s ease; border: 2px solid #1A5B9C;
        }
        .btn-partenaire-outline:hover { background: #1A5B9C; color: #FFFFFF; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(26, 91, 156, 0.3); }
        .btn-partenaire-accent {
            background: #F59E0B; color: #FFFFFF; padding: 13px 26px; border-radius: 10px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px; transition: all 0.3s ease; border: 2px solid #F59E0B;
        }
        .btn-partenaire-accent:hover { background: #D97706; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3); color: #FFFFFF; }
        @media (max-width: 768px) {
            .partenaire-confirmation-actions { flex-direction: column; }
            .btn-partenaire-primary, .btn-partenaire-outline, .btn-partenaire-accent { width: 100%; justify-content: center; }
        }
    </style>
@endsection

@section('content')

    <section class="partenaire-form-section" style="padding-top: 100px;">
        <div class="container">
            <div class="partenaire-form-wrapper" style="text-align: center; background: #FFFFFF; border-radius: 24px; padding: 50px 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.08);">

                <div class="partenaire-confirmation-icon-wrapper">
                    <div class="partenaire-confirmation-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <h1 class="partenaire-confirmation-title">
                    Proposition <span class="highlight">enregistrée</span> !
                </h1>
                <p class="partenaire-confirmation-subtitle">
                    Merci pour votre intérêt ! Votre proposition de partenariat a été enregistrée avec succès. 
                    Notre équipe PHOVE va l'examiner et reviendra vers vous dans les plus brefs délais.
                </p>

                <div class="partenaire-next-steps">
                    <h3><i class="fas fa-tasks"></i> Prochaines étapes</h3>
                    <ul class="partenaire-steps-list">
                        <li>
                            <span class="step-num">1</span>
                            <span>Notre équipe examine votre proposition sous <strong>5 à 7 jours ouvrés</strong>.</span>
                        </li>
                        <li>
                            <span class="step-num">2</span>
                            <span>Nous vous contactons pour un <strong>échange téléphonique</strong> ou une réunion.</span>
                        </li>
                        <li>
                            <span class="step-num">3</span>
                            <span>Nous définissons ensemble les <strong>modalités du partenariat</strong>.</span>
                        </li>
                        <li>
                            <span class="step-num">4</span>
                            <span>Signature d'une <strong>convention de partenariat</strong> et démarrage des activités.</span>
                        </li>
                    </ul>
                </div>

                <div class="partenaire-confirmation-actions">
                    <a href="{{ url('/') }}" class="btn-partenaire-primary">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
                    <a href="{{ route('partenaires') }}" class="btn-partenaire-outline">
                        <i class="fas fa-users"></i> Nos partenaires
                    </a>
                    <a href="{{ url('/faire-un-don') }}" class="btn-partenaire-accent">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                </div>

            </div>
        </div>
    </section>

    <section class="cta-section-page">
        <div class="container">
            <div class="cta-content-page">
                <h2 class="cta-title-page">Construisons <span class="highlight-page">ensemble</span> l'avenir</h2>
                <p class="cta-text-page">Chaque partenariat est une pierre apportée à l'édifice de la compassion envers les orphelins, veuves et personnes démunies.</p>
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