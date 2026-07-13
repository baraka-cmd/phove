@extends('layouts.public')

@section('title', 'Faire un don')

@section('meta_description', 'Soutenez AHD-RDC en faisant un don. Votre générosité nous permet de mener nos projets humanitaires au Sud-Kivu et Nord-Kivu en RD Congo.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/don.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-heart"></i>
            </div>
            <h1 class="page-header-title">Faire un don</h1>
            <p class="page-header-subtitle">
                Votre générosité change des vies. Chaque contribution nous permet de continuer notre action humanitaire auprès des communautés vulnérables.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Faire un don</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION POURQUOI DONNER --}}
    {{-- ============================================ --}}
    <section class="don-pourquoi-section">
        <div class="container">
            <div class="don-impact-grid">
                <div class="don-impact-card">
                    <div class="don-impact-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h4>10 $</h4>
                    <p>Nourrit une famille pendant une semaine</p>
                </div>
                <div class="don-impact-card">
                    <div class="don-impact-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h4>25 $</h4>
                    <p>Fournit de l'eau potable à 50 personnes</p>
                </div>
                <div class="don-impact-card">
                    <div class="don-impact-icon">
                        <i class="fas fa-medkit"></i>
                    </div>
                    <h4>50 $</h4>
                    <p>Offre des soins de santé à 10 enfants</p>
                </div>
                <div class="don-impact-card">
                    <div class="don-impact-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>100 $</h4>
                    <p>Permet de former 5 jeunes à un métier</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FORMULAIRE DE DON --}}
    {{-- ============================================ --}}
    <section class="don-form-section">
        <div class="container">
            <div class="don-form-wrapper">
                <form action="{{ route('don.store') }}" method="POST" id="donForm" novalidate>
                    @csrf
                    <input type="hidden" name="montant" id="hiddenMontant" value="50">
                    <input type="hidden" name="montant_type" value="suggere">
                    {{-- ============================================ --}}
                    {{-- ÉTAPE 1 : MONTANT --}}
                    {{-- ============================================ --}}
                    <div class="don-step" id="step1">
                        <div class="don-step-header">
                            <span class="don-step-number">1</span>
                            <h2 class="don-step-title">Choisissez un montant</h2>
                        </div>

                        {{-- Montants suggérés --}}
                        <div class="don-montants-grid">
                            @foreach($montantsSuggeres as $montant)
                            <label class="don-montant-card" data-montant="{{ $montant }}">
                                <input type="radio" name="montant" value="{{ $montant }}" class="don-montant-radio" {{ $loop->first ? 'checked' : '' }}>
                                <span class="don-montant-value">{{ number_format($montant, 0) }} $</span>
                            </label>
                            @endforeach
                        </div>

                        {{-- Montant personnalisé --}}
                        <div class="don-montant-custom">
                            <label class="don-montant-card don-montant-card-custom" id="customMontantCard">
                                <input type="radio" name="montant_type" value="personnalise" class="don-montant-radio" id="customRadio">
                                <span class="don-montant-value">Autre montant</span>
                            </label>
                            <div class="don-montant-input-wrapper" id="customMontantWrapper">
                                <span class="don-montant-symbol">$</span>
                                <input type="number" 
                                       name="montant_personnalise" 
                                       id="customMontant" 
                                       class="don-montant-input" 
                                       placeholder="Entrez un montant" 
                                       min="1" 
                                       max="50000"
                                       disabled>
                                <span class="don-montant-currency">USD</span>
                            </div>
                        </div>

                        {{-- Devise --}}
                        <div class="don-devise">
                            <label class="don-label">Devise</label>
                            <select name="devise" class="don-select">
                                <option value="USD">USD — Dollar américain</option>
                                <option value="CDF">CDF — Franc congolais</option>
                            </select>
                        </div>

                        {{-- Type de don --}}
                        <div class="don-type">
                            <label class="don-label">Type de don</label>
                            <div class="don-type-grid">
                                <label class="don-type-card">
                                    <input type="radio" name="type_don" value="ponctuel" checked class="don-type-radio">
                                    <div class="don-type-content">
                                        <i class="fas fa-hand-holding-heart"></i>
                                        <span>Don ponctuel</span>
                                        <small>Une seule fois</small>
                                    </div>
                                </label>
                                <label class="don-type-card">
                                    <input type="radio" name="type_don" value="recurrent" class="don-type-radio">
                                    <div class="don-type-content">
                                        <i class="fas fa-sync-alt"></i>
                                        <span>Don récurrent</span>
                                        <small>Chaque mois</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- ÉTAPE 2 : AFFECTATION --}}
                    {{-- ============================================ --}}
                    <div class="don-step" id="step2">
                        <div class="don-step-header">
                            <span class="don-step-number">2</span>
                            <h2 class="don-step-title">Affectation de votre don</h2>
                        </div>

                        <div class="don-affectation-grid">
                            <label class="don-affectation-card">
                                <input type="radio" name="projet_id" value="" checked class="don-affectation-radio">
                                <div class="don-affectation-content">
                                    <div class="don-affectation-icon">
                                        <i class="fas fa-globe-africa"></i>
                                    </div>
                                    <h4>Là où les besoins sont les plus urgents</h4>
                                    <p>Votre don sera utilisé pour le projet le plus prioritaire.</p>
                                </div>
                            </label>

                            @foreach($projets as $projet)
                            <label class="don-affectation-card">
                                <input type="radio" name="projet_id" value="{{ $projet->id }}" class="don-affectation-radio">
                                <div class="don-affectation-content">
                                    <div class="don-affectation-icon">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                    <h4>{{ $projet->titre }}</h4>
                                    <p>{{ $projet->zone_geographique }} — {{ $projet->domaineLibelle() }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- ÉTAPE 3 : INFORMATIONS PERSONNELLES --}}
                    {{-- ============================================ --}}
                    <div class="don-step" id="step3">
                        <div class="don-step-header">
                            <span class="don-step-number">3</span>
                            <h2 class="don-step-title">Vos informations</h2>
                        </div>

                        <div class="don-form-grid">
                            <div class="don-form-group">
                                <label for="donateur_nom" class="don-label">
                                    Nom complet <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       id="donateur_nom" 
                                       name="donateur_nom" 
                                       class="don-input @error('donateur_nom') error @enderror" 
                                       value="{{ old('donateur_nom') }}" 
                                       placeholder="Votre nom complet" 
                                       required>
                                @error('donateur_nom')
                                    <span class="don-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="don-form-group">
                                <label for="donateur_email" class="don-label">
                                    Adresse email <span class="required">*</span>
                                </label>
                                <input type="email" 
                                       id="donateur_email" 
                                       name="donateur_email" 
                                       class="don-input @error('donateur_email') error @enderror" 
                                       value="{{ old('donateur_email') }}" 
                                       placeholder="votre@email.com" 
                                       required>
                                @error('donateur_email')
                                    <span class="don-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="don-form-group">
                                <label for="donateur_telephone" class="don-label">
                                    Téléphone
                                </label>
                                <input type="tel" 
                                       id="donateur_telephone" 
                                       name="donateur_telephone" 
                                       class="don-input" 
                                       value="{{ old('donateur_telephone') }}" 
                                       placeholder="+243 000 000 000">
                            </div>

                            <div class="don-form-group">
                                <label for="donateur_adresse" class="don-label">
                                    Adresse
                                </label>
                                <input type="text" 
                                       id="donateur_adresse" 
                                       name="donateur_adresse" 
                                       class="don-input" 
                                       value="{{ old('donateur_adresse') }}" 
                                       placeholder="Votre adresse (optionnel)">
                            </div>

                            <div class="don-form-group don-form-full">
                                <label for="message_donateur" class="don-label">
                                    Message (optionnel)
                                </label>
                                <textarea id="message_donateur" 
                                          name="message_donateur" 
                                          class="don-textarea" 
                                          rows="3" 
                                          placeholder="Un message d'encouragement, une dédicace...">{{ old('message_donateur') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- ÉTAPE 4 : MODE DE PAIEMENT --}}
                    {{-- ============================================ --}}
                    <div class="don-step" id="step4">
                        <div class="don-step-header">
                            <span class="don-step-number">4</span>
                            <h2 class="don-step-title">Mode de paiement</h2>
                        </div>

                        <div class="don-paiement-grid">
                            @foreach($modesPaiement as $value => $label)
                            <label class="don-paiement-card">
                                <input type="radio" name="mode_paiement" value="{{ $value }}" class="don-paiement-radio" {{ $loop->first ? 'checked' : '' }}>
                                <div class="don-paiement-content">
                                    <div class="don-paiement-icon">
                                        @if($value === 'mobile_money')
                                            <i class="fas fa-mobile-alt"></i>
                                        @elseif($value === 'virement_bancaire')
                                            <i class="fas fa-university"></i>
                                        @elseif($value === 'carte_bancaire')
                                            <i class="fas fa-credit-card"></i>
                                        @elseif($value === 'paypal')
                                            <i class="fab fa-paypal"></i>
                                        @else
                                            <i class="fas fa-money-bill-wave"></i>
                                        @endif
                                    </div>
                                    <span>{{ $label }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        {{-- Instructions selon le mode de paiement --}}
                        <div class="don-paiement-instructions" id="paiementInstructions">
                            <div class="don-instruction-card" data-paiement="mobile_money">
                                <h4><i class="fas fa-info-circle"></i> Instructions Mobile Money</h4>
                                <p>Après validation, vous recevrez les numéros pour effectuer votre paiement via Airtel Money, Orange Money ou M-Pesa.</p>
                            </div>
                            <div class="don-instruction-card" data-paiement="virement_bancaire" style="display:none;">
                                <h4><i class="fas fa-info-circle"></i> Instructions Virement bancaire</h4>
                                <p>Après validation, vous recevrez nos coordonnées bancaires pour effectuer le virement.</p>
                            </div>
                            <div class="don-instruction-card" data-paiement="carte_bancaire" style="display:none;">
                                <h4><i class="fas fa-info-circle"></i> Paiement par carte bancaire</h4>
                                <p>Vous serez redirigé vers une plateforme sécurisée pour effectuer votre paiement par carte bancaire.</p>
                            </div>
                            <div class="don-instruction-card" data-paiement="paypal" style="display:none;">
                                <h4><i class="fas fa-info-circle"></i> Paiement PayPal</h4>
                                <p>Vous serez redirigé vers PayPal pour finaliser votre don en toute sécurité.</p>
                            </div>
                            <div class="don-instruction-card" data-paiement="especes" style="display:none;">
                                <h4><i class="fas fa-info-circle"></i> Paiement en espèces</h4>
                                <p>Vous pouvez remettre votre don en espèces à notre bureau de Minova ou à nos représentants sur le terrain.</p>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- RÉCAPITULATIF --}}
                    {{-- ============================================ --}}
                    <div class="don-recap" id="donRecap">
                        <h3><i class="fas fa-clipboard-check"></i> Récapitulatif de votre don</h3>
                        <div class="don-recap-content">
                            <div class="don-recap-row">
                                <span>Montant</span>
                                <strong id="recapMontant">50 $</strong>
                            </div>
                            <div class="don-recap-row">
                                <span>Type</span>
                                <strong id="recapType">Don ponctuel</strong>
                            </div>
                            <div class="don-recap-row">
                                <span>Mode de paiement</span>
                                <strong id="recapPaiement">Mobile Money</strong>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- CONDITIONS + SOUMISSION --}}
                    {{-- ============================================ --}}
                    <div class="don-submit-section">
                        <label class="don-conditions">
                            <input type="checkbox" name="conditions_acceptees" value="1" required>
                            <span>Je certifie l'exactitude des informations fournies et j'accepte que mon don soit utilisé conformément à la mission d'AHD-RDC.</span>
                        </label>
                        @error('conditions_acceptees')
                            <span class="don-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                        @enderror

                        <button type="submit" class="don-submit-btn" id="donSubmitBtn">
                            <span class="btn-text">
                                <i class="fas fa-heart"></i> Valider mon don
                            </span>
                            <span class="btn-loader"></span>
                        </button>

                        <p class="don-securite">
                            <i class="fas fa-lock"></i> Paiement 100% sécurisé. Vos données sont protégées.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION TRANSPARENCE --}}
    {{-- ============================================ --}}
    <section class="don-transparence-section">
        <div class="container">
            <div class="don-transparence-card">
                <div class="don-transparence-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="don-transparence-content">
                    <h3>Votre don est sécurisé et transparent</h3>
                    <p>
                        AHD-RDC s'engage à utiliser chaque don de manière transparente et responsable. 
                        Consultez nos rapports financiers pour suivre l'impact de votre générosité.
                    </p>
                    <a href="{{ route('transparence', ['tab' => 'financiers']) }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-chart-pie"></i> Voir nos rapports financiers
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/don.js') }}"></script>
@endsection