@extends('layouts.public')

@section('title', 'Transparence')

@section('meta_description', 'Consultez les rapports financiers, rapports annuels et documents officiels de PHOVE. La transparence et la redevabilité sont au cœur de notre gouvernance.')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/public-pages-common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/transparence.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- BANNIÈRE DE PAGE --}}
    {{-- ============================================ --}}
    <section class="page-header">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-search-dollar"></i>
            </div>
            <h1 class="page-header-title">Transparence</h1>
            <p class="page-header-subtitle">
                Conformément à nos valeurs d'intégrité et de transparence, nous publions nos rapports financiers, rapports annuels et documents officiels.
            </p>
            <nav class="page-breadcrumb">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Accueil</a>
                <i class="fas fa-chevron-right"></i>
                <span>Transparence</span>
            </nav>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION INTRODUCTION --}}
    {{-- ============================================ --}}
    <section class="page-section page-section-white">
        <div class="container">
            <div class="section-header">
                <span class="section-subtitle">Nos engagements</span>
                <h2 class="section-title">Une gestion transparente</h2>
                <p class="section-description">
                    La <strong>transparence</strong> et la <strong>redevabilité</strong> sont 
                    des valeurs fondamentales de PHOVE. Conformément aux <strong>articles 35 
                    et 38 des statuts</strong>, la Commission de Contrôle vérifie les opérations 
                    chaque trimestre et le Directeur Général dresse un rapport trimestriel et 
                    annuel au Conseil d'Administration et à l'Assemblée Générale.
                </p>
            </div>

            {{-- Valeurs --}}
            <div class="transparence-values">
                <div class="transparence-value-item">
                    <i class="fas fa-search"></i>
                    <span>Transparence</span>
                </div>
                <div class="transparence-value-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Intégrité</span>
                </div>
                <div class="transparence-value-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Rapports publics</span>
                </div>
                <div class="transparence-value-item">
                    <i class="fas fa-balance-scale"></i>
                    <span>Bonne gouvernance</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION ONGLETS --}}
    {{-- ============================================ --}}
    <section class="transparence-tabs-section">
        <div class="container">
            {{-- Navigation par onglets --}}
            <div class="transparence-tabs-nav">
                <a href="{{ route('transparence', ['tab' => 'financiers']) }}" 
                   class="transparence-tab {{ $tab === 'financiers' ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Rapports financiers</span>
                </a>
                <a href="{{ route('transparence', ['tab' => 'annuels']) }}" 
                   class="transparence-tab {{ $tab === 'annuels' ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Rapports annuels</span>
                </a>
                <a href="{{ route('transparence', ['tab' => 'documents']) }}" 
                   class="transparence-tab {{ $tab === 'documents' ? 'active' : '' }}">
                    <i class="fas fa-folder-open"></i>
                    <span>Documents officiels</span>
                </a>
            </div>

            {{-- Contenu des onglets --}}
            <div class="transparence-tabs-content">
                
                {{-- ============================================ --}}
                {{-- ONGLET 1 : RAPPORTS FINANCIERS --}}
                {{-- ============================================ --}}
                @if($tab === 'financiers')
                <div class="transparence-panel" id="panel-financiers">
                    @if($rapportsFinanciers->count() > 0)
                        <div class="rapports-grid">
                            @foreach($rapportsFinanciers as $rapport)
                            <div class="rapport-card">
                                <div class="rapport-card-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="rapport-card-content">
                                    <h3 class="rapport-card-title">{{ $rapport->titre }}</h3>
                                    <span class="rapport-card-periode">
                                        <i class="fas fa-calendar-alt"></i> 
                                        {{ $rapport->typeLibelle() }} — {{ $rapport->periode }}
                                    </span>
                                    <div class="rapport-card-montants">
                                        <div class="rapport-montant-item">
                                            <span class="montant-label">Entrées</span>
                                            <span class="montant-value montant-entrees">
                                                {{ number_format($rapport->total_entrees, 0, ',', ' ') }} $
                                            </span>
                                        </div>
                                        <div class="rapport-montant-item">
                                            <span class="montant-label">Sorties</span>
                                            <span class="montant-value montant-sorties">
                                                {{ number_format($rapport->total_sorties, 0, ',', ' ') }} $
                                            </span>
                                        </div>
                                        <div class="rapport-montant-item">
                                            <span class="montant-label">Solde</span>
                                            <span class="montant-value {{ $rapport->solde >= 0 ? 'montant-positif' : 'montant-negatif' }}">
                                                {{ number_format($rapport->solde, 0, ',', ' ') }} $
                                            </span>
                                        </div>
                                    </div>
                                    @if($rapport->fichier)
                                    <a href="{{ asset('storage/' . $rapport->fichier) }}" 
                                       class="btn btn-outline btn-sm rapport-download-btn" 
                                       target="_blank"
                                       title="Télécharger le rapport complet">
                                        <i class="fas fa-download"></i> Télécharger le rapport
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="transparence-empty">
                            <div class="transparence-empty-icon">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <h3>Aucun rapport financier disponible</h3>
                            <p>
                                Les rapports financiers seront publiés ici dès qu'ils seront 
                                validés par la Commission de Contrôle et le Directeur Général.
                            </p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- ============================================ --}}
                {{-- ONGLET 2 : RAPPORTS ANNUELS --}}
                {{-- ============================================ --}}
                @if($tab === 'annuels')
                <div class="transparence-panel" id="panel-annuels">
                    @if($rapportsAnnuels->count() > 0)
                        <div class="documents-grid">
                            @foreach($rapportsAnnuels as $document)
                            <a href="{{ asset('storage/' . $document->fichier) }}" 
                               class="document-card" 
                               target="_blank"
                               title="Télécharger {{ $document->titre }}">
                                <div class="document-card-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="document-card-content">
                                    <h3 class="document-card-title">{{ $document->titre }}</h3>
                                    @if($document->description)
                                    <p class="document-card-desc">{{ Str::limit($document->description, 100) }}</p>
                                    @endif
                                    <div class="document-card-meta">
                                        @if($document->date_document)
                                        <span class="document-meta-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $document->date_document->format('d/m/Y') }}
                                        </span>
                                        @endif
                                        @if($document->taille)
                                        <span class="document-meta-item">
                                            <i class="fas fa-weight"></i>
                                            {{ $document->tailleLisible() }}
                                        </span>
                                        @endif
                                        <span class="document-meta-item">
                                            <i class="fas fa-download"></i>
                                            {{ $document->telechargements }} téléchargement(s)
                                        </span>
                                    </div>
                                </div>
                                <div class="document-card-action">
                                    <i class="fas fa-download"></i>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="transparence-empty">
                            <div class="transparence-empty-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h3>Aucun rapport annuel disponible</h3>
                            <p>
                                Les rapports annuels seront publiés ici après validation 
                                par l'Assemblée Générale.
                            </p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- ============================================ --}}
                {{-- ONGLET 3 : DOCUMENTS OFFICIELS --}}
                {{-- ============================================ --}}
                @if($tab === 'documents')
                <div class="transparence-panel" id="panel-documents">
                    @if($documentsOfficiels->count() > 0)
                        <div class="documents-grid">
                            @foreach($documentsOfficiels as $document)
                            <a href="{{ asset('storage/' . $document->fichier) }}" 
                               class="document-card" 
                               target="_blank"
                               title="Télécharger {{ $document->titre }}">
                                <div class="document-card-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="document-card-content">
                                    <h3 class="document-card-title">{{ $document->titre }}</h3>
                                    <span class="document-card-type">
                                        <i class="fas fa-tag"></i> {{ $document->typeLibelle() }}
                                    </span>
                                    @if($document->description)
                                    <p class="document-card-desc">{{ Str::limit($document->description, 100) }}</p>
                                    @endif
                                    <div class="document-card-meta">
                                        @if($document->date_document)
                                        <span class="document-meta-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $document->date_document->format('d/m/Y') }}
                                        </span>
                                        @endif
                                        @if($document->signataires)
                                        <span class="document-meta-item">
                                            <i class="fas fa-signature"></i>
                                            {{ Str::limit($document->signataires, 40) }}
                                        </span>
                                        @endif
                                        @if($document->taille)
                                        <span class="document-meta-item">
                                            <i class="fas fa-weight"></i>
                                            {{ $document->tailleLisible() }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="document-card-action">
                                    <i class="fas fa-download"></i>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="transparence-empty">
                            <div class="transparence-empty-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h3>Aucun document officiel disponible</h3>
                            <p>
                                Les documents officiels (statuts du 30 août 2012, Règlement 
                                d'Ordre Intérieur, procès-verbaux) seront publiés ici prochainement.
                            </p>
                        </div>
                    @endif
                </div>
                @endif

            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION ENGAGEMENT --}}
    {{-- ============================================ --}}
    <section class="engagement-section">
        <div class="container">
            <div class="engagement-card">
                <div class="engagement-icon">
                    <i class="fas fa-quote-left"></i>
                </div>
                <p class="engagement-text">
                    « La corruption à tout niveau est à bannir dans l'organisation. 
                    La transparence et l'intégrité sont les piliers de la confiance 
                    que nous accordent nos bénéficiaires, nos partenaires et nos donateurs. »
                </p>
                <span class="engagement-author">— Apôtre Philippe KABOYI MUSAMUGUMA, Directeur Général</span>
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
                    La <span class="highlight-page">confiance</span> par la transparence
                </h2>
                <p class="cta-text-page">
                    Vous souhaitez en savoir plus sur notre gestion ou nos projets ? 
                    Contactez-nous, nous serons ravis de répondre à vos questions.
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
    <script src="{{ asset('assets/js/transparence.js') }}"></script>
@endsection