@extends('layouts.public')

@section('title', 'Devenir membre')

@section('meta_description', 'Rejoignez AHD-RDC en devenant membre. Découvrez les conditions d\'adhésion et soumettez votre demande en ligne.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/devenir-membre.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1 class="page-header-title">Devenir membre</h1>
            <p class="page-header-subtitle">
                Rejoignez AHD-RDC et contribuez activement à notre mission humanitaire pour le développement des communautés vulnérables en RD Congo.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Devenir membre</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION POURQUOI ADHÉRER --}}
    {{-- ============================================ --}}
    <section class="adhesion-pourquoi-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Pourquoi nous rejoindre</span>
                <h2 class="section-title">Les avantages d'être membre</h2>
                <p class="section-description">
                    En adhérant à AHD-RDC, vous intégrez une communauté engagée pour le développement 
                    et la paix en République Démocratique du Congo.
                </p>
            </div>

            <div class="adhesion-avantages-grid">
                <div class="adhesion-avantage-card">
                    <div class="adhesion-avantage-icon">
                        <i class="fas fa-vote-yea"></i>
                    </div>
                    <h4>Droit de vote</h4>
                    <p>Participez aux Assemblées Générales et prenez part aux décisions importantes de l'organisation.</p>
                </div>
                <div class="adhesion-avantage-card">
                    <div class="adhesion-avantage-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>Formations</h4>
                    <p>Bénéficiez de formations et de voyages d'échange pour renforcer vos compétences.</p>
                </div>
                <div class="adhesion-avantage-card">
                    <div class="adhesion-avantage-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h4>Opportunités</h4>
                    <p>Soyez prioritaire pour les offres d'emploi et les postes vacants au sein de l'organisation.</p>
                </div>
                <div class="adhesion-avantage-card">
                    <div class="adhesion-avantage-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h4>Impact social</h4>
                    <p>Contribuez directement à l'amélioration des conditions de vie des communautés vulnérables.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION CATÉGORIES DE MEMBRES --}}
    {{-- ============================================ --}}
    <section class="adhesion-categories-section">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Nos membres</span>
                <h2 class="section-title">Catégories de membres</h2>
                <p class="section-description">
                    Conformément à l'article 13 de nos statuts, AHD-RDC comprend plusieurs catégories de membres.
                </p>
            </div>

            <div class="adhesion-categories-grid">
                <div class="adhesion-categorie-card">
                    <div class="adhesion-categorie-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>Membre adhérent</h4>
                    <p>
                        Personne physique ou morale qui exprime son désir d'adhésion et qui est agréée 
                        par l'Assemblée Générale. Le membre adhérent participe activement aux activités 
                        et aux décisions de l'organisation.
                    </p>
                    <ul class="adhesion-categorie-details">
                        <li><i class="fas fa-check-circle"></i> Droit de vote à l'AG</li>
                        <li><i class="fas fa-check-circle"></i> Éligible aux postes</li>
                        <li><i class="fas fa-check-circle"></i> Cotisation : 50 $/mois</li>
                        <li><i class="fas fa-check-circle"></i> Frais d'adhésion : 50 $</li>
                    </ul>
                </div>

                <div class="adhesion-categorie-card">
                    <div class="adhesion-categorie-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Membre sympathisant</h4>
                    <p>
                        Personne physique qui peut profiter des bienfaits de l'association sans se manifester 
                        ni adhérer ni contribuer matériellement. Le sympathisant soutient moralement 
                        la mission de l'organisation.
                    </p>
                    <ul class="adhesion-categorie-details">
                        <li><i class="fas fa-check-circle"></i> Accès aux activités</li>
                        <li><i class="fas fa-check-circle"></i> Soutien moral</li>
                        <li><i class="fas fa-check-circle"></i> Pas de cotisation obligatoire</li>
                    </ul>
                </div>

                <div class="adhesion-categorie-card">
                    <div class="adhesion-categorie-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h4>Membre d'honneur</h4>
                    <p>
                        Personne physique ou morale, étrangère à AHD-RDC, qui aide l'association 
                        matériellement ou financièrement à atteindre ses objectifs. Une reconnaissance 
                        spéciale pour les contributeurs exceptionnels.
                    </p>
                    <ul class="adhesion-categorie-details">
                        <li><i class="fas fa-check-circle"></i> Reconnaissance publique</li>
                        <li><i class="fas fa-check-circle"></i> Contribution libre</li>
                        <li><i class="fas fa-check-circle"></i> Invitations aux événements</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION CONDITIONS D'ADHÉSION --}}
    {{-- ============================================ --}}
    <section class="adhesion-conditions-section">
        <div class="container">
            <div class="adhesion-conditions-card">
                <div class="adhesion-conditions-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="adhesion-conditions-content">
                    <h3>Conditions d'adhésion</h3>
                    <p>
                        Conformément à l'article 4 du Règlement d'Ordre Intérieur et à l'article 18 des Statuts, 
                        toute personne souhaitant devenir membre doit :
                    </p>
                    <ul class="adhesion-conditions-list">
                        <li><i class="fas fa-check-circle"></i> Introduire une demande d'adhésion adressée au Conseil d'Administration</li>
                        <li><i class="fas fa-check-circle"></i> Se soumettre aux Statuts et au Règlement d'Ordre Intérieur</li>
                        <li><i class="fas fa-check-circle"></i> Payer les frais d'adhésion fixés à <strong>50 dollars américains</strong></li>
                        <li><i class="fas fa-check-circle"></i> Jouir d'une bonne réputation et être crédible</li>
                        <li><i class="fas fa-check-circle"></i> Être agréé par l'Assemblée Générale</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FORMULAIRE D'ADHÉSION --}}
    {{-- ============================================ --}}
    <section class="adhesion-form-section" id="adhesion-form">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Formulaire</span>
                <h2 class="section-title">Demande d'adhésion</h2>
                <p class="section-description">
                    Remplissez ce formulaire pour soumettre votre demande d'adhésion. 
                    Les champs marqués d'un <span class="required">*</span> sont obligatoires.
                </p>
            </div>

            <div class="adhesion-form-wrapper">
                <form action="{{ route('devenir-membre.store') }}" method="POST" id="adhesionForm" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- ============================================ --}}
                    {{-- ÉTAPE 1 : IDENTITÉ --}}
                    {{-- ============================================ --}}
                    <div class="adhesion-step">
                        <div class="adhesion-step-header">
                            <span class="adhesion-step-number">1</span>
                            <h2 class="adhesion-step-title">Votre identité</h2>
                        </div>

                        <div class="adhesion-form-grid">
                            <div class="adhesion-form-group">
                                <label for="nom" class="adhesion-label">
                                    Nom <span class="required">*</span>
                                </label>
                                <input type="text" id="nom" name="nom" class="adhesion-input @error('nom') error @enderror" value="{{ old('nom') }}" placeholder="Votre nom" required>
                                @error('nom')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="adhesion-form-group">
                                <label for="post_nom" class="adhesion-label">
                                    Post-nom <span class="required">*</span>
                                </label>
                                <input type="text" id="post_nom" name="post_nom" class="adhesion-input @error('post_nom') error @enderror" value="{{ old('post_nom') }}" placeholder="Votre post-nom" required>
                                @error('post_nom')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="adhesion-form-group">
                                <label for="prenom" class="adhesion-label">
                                    Prénom <span class="required">*</span>
                                </label>
                                <input type="text" id="prenom" name="prenom" class="adhesion-input @error('prenom') error @enderror" value="{{ old('prenom') }}" placeholder="Votre prénom" required>
                                @error('prenom')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="adhesion-form-group">
                                <label for="sexe" class="adhesion-label">
                                    Sexe <span class="required">*</span>
                                </label>
                                <select id="sexe" name="sexe" class="adhesion-select @error('sexe') error @enderror" required>
                                    <option value="">Sélectionnez...</option>
                                    <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Féminin</option>
                                </select>
                                @error('sexe')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="adhesion-form-group adhesion-form-full">
                                <label for="adresse" class="adhesion-label">
                                    Adresse complète <span class="required">*</span>
                                </label>
                                <input type="text" id="adresse" name="adresse" class="adhesion-input @error('adresse') error @enderror" value="{{ old('adresse') }}" placeholder="Votre adresse (ville, commune, province)" required>
                                @error('adresse')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- ÉTAPE 2 : COORDONNÉES --}}
                    {{-- ============================================ --}}
                    <div class="adhesion-step">
                        <div class="adhesion-step-header">
                            <span class="adhesion-step-number">2</span>
                            <h2 class="adhesion-step-title">Vos coordonnées</h2>
                        </div>

                        <div class="adhesion-form-grid">
                            <div class="adhesion-form-group">
                                <label for="telephone" class="adhesion-label">
                                    Téléphone <span class="required">*</span>
                                </label>
                                <input type="tel" id="telephone" name="telephone" class="adhesion-input @error('telephone') error @enderror" value="{{ old('telephone') }}" placeholder="+243 000 000 000" required>
                                @error('telephone')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="adhesion-form-group">
                                <label for="email" class="adhesion-label">
                                    Adresse email
                                </label>
                                <input type="email" id="email" name="email" class="adhesion-input @error('email') error @enderror" value="{{ old('email') }}" placeholder="votre@email.com">
                                @error('email')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="adhesion-form-group">
                                <label for="photo" class="adhesion-label">
                                    Photo passeport
                                </label>
                                <div class="adhesion-photo-upload">
                                    <input type="file" id="photo" name="photo" class="adhesion-file-input @error('photo') error @enderror" accept="image/jpeg,image/jpg,image/png">
                                    <label for="photo" class="adhesion-file-label" id="photoLabel">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Cliquez pour télécharger une photo</span>
                                        <small>JPG, JPEG ou PNG — Max 2 Mo</small>
                                    </label>
                                    <div class="adhesion-photo-preview" id="photoPreview" style="display:none;">
                                        <img src="" alt="Aperçu" id="photoPreviewImg">
                                        <button type="button" class="adhesion-photo-remove" id="photoRemove" title="Supprimer la photo">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('photo')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="adhesion-form-group">
                                <label for="categorie" class="adhesion-label">
                                    Catégorie de membre <span class="required">*</span>
                                </label>
                                <select id="categorie" name="categorie" class="adhesion-select @error('categorie') error @enderror" required>
                                    <option value="">Sélectionnez une catégorie...</option>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}" {{ old('categorie') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('categorie')
                                    <span class="adhesion-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="adhesion-form-group adhesion-form-full">
                                <label for="motivation" class="adhesion-label">
                                    Motivation (optionnel)
                                </label>
                                <textarea id="motivation" name="motivation" class="adhesion-textarea" rows="4" placeholder="Expliquez pourquoi vous souhaitez devenir membre d'AHD-RDC...">{{ old('motivation') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- ÉTAPE 3 : ENGAGEMENTS --}}
                    {{-- ============================================ --}}
                    <div class="adhesion-step">
                        <div class="adhesion-step-header">
                            <span class="adhesion-step-number">3</span>
                            <h2 class="adhesion-step-title">Vos engagements</h2>
                        </div>

                        <div class="adhesion-engagements">
                            <label class="adhesion-engagement-item">
                                <input type="checkbox" name="statuts_acceptes" value="1" class="adhesion-checkbox" required>
                                <span>
                                    Je déclare avoir lu et j'accepte sans réserve les 
                                    <strong>Statuts</strong> d'AHD-RDC (Articles 1 à 39).
                                </span>
                            </label>

                            <label class="adhesion-engagement-item">
                                <input type="checkbox" name="roi_accepte" value="1" class="adhesion-checkbox" required>
                                <span>
                                    Je déclare avoir lu et j'accepte sans réserve le 
                                    <strong>Règlement d'Ordre Intérieur (ROI)</strong> d'AHD-RDC.
                                </span>
                            </label>

                            <label class="adhesion-engagement-item">
                                <input type="checkbox" name="honnete" value="1" class="adhesion-checkbox" required>
                                <span>
                                    J'atteste sur l'honneur que les informations fournies sont exactes 
                                    et que je jouis d'une <strong>bonne réputation</strong>.
                                </span>
                            </label>

                            <label class="adhesion-engagement-item">
                                <input type="checkbox" name="conditions_acceptees" value="1" class="adhesion-checkbox" required>
                                <span>
                                    J'accepte de payer les <strong>frais d'adhésion de 50 $</strong> 
                                    et la <strong>cotisation mensuelle de 50 $</strong> si ma demande est acceptée 
                                    (Articles 4 et 39 du ROI).
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- RÉCAPITULATIF --}}
                    {{-- ============================================ --}}
                    <div class="adhesion-recap" id="adhesionRecap">
                        <h3><i class="fas fa-clipboard-check"></i> Récapitulatif de votre demande</h3>
                        <p class="adhesion-recap-info">Veuillez vérifier vos informations avant de soumettre.</p>
                        <div class="adhesion-recap-content" id="recapContent">
                            <div class="adhesion-recap-row">
                                <span>Identité</span>
                                <strong id="recapIdentite">—</strong>
                            </div>
                            <div class="adhesion-recap-row">
                                <span>Catégorie</span>
                                <strong id="recapCategorie">—</strong>
                            </div>
                            <div class="adhesion-recap-row">
                                <span>Téléphone</span>
                                <strong id="recapTelephone">—</strong>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================ --}}
                    {{-- SOUMISSION --}}
                    {{-- ============================================ --}}
                    <div class="adhesion-submit-section">
                        <button type="submit" class="adhesion-submit-btn" id="adhesionSubmitBtn">
                            <span class="btn-text">
                                <i class="fas fa-paper-plane"></i> Soumettre ma demande d'adhésion
                            </span>
                            <span class="btn-loader"></span>
                        </button>

                        <p class="adhesion-info-text">
                            <i class="fas fa-info-circle"></i> 
                            Votre demande sera examinée par le Conseil d'Administration lors de sa prochaine session. 
                            Vous recevrez une réponse par email ou par téléphone.
                        </p>
                    </div>
                </form>
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
                    <span class="highlight-page">Ensemble</span> pour le bien de la communauté
                </h2>
                <p class="cta-text-page">
                    Vous avez des questions sur l'adhésion ? Notre équipe est là pour vous répondre.
                </p>
                <div class="cta-buttons-page">
                    <a href="{{ url('/contact') }}" class="btn-page-don">
                        <i class="fas fa-envelope"></i> Nous contacter
                    </a>
                    <a href="{{ url('/faire-un-don') }}" class="btn-page-outline">
                        <i class="fas fa-heart"></i> Faire un don
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/public-pages-common.js') }}"></script>
    <script src="{{ asset('assets/js/devenir-membre.js') }}"></script>
@endsection