@extends('layouts.admin')

@section('title', 'Boîte de réception')

@section('page_title', 'Messages')
@section('page_icon', 'envelope-open-text')
@section('page_subtitle', 'Gérez les messages reçus via le formulaire de contact. Répondez, classez et archivez les conversations.')

@php
    $breadcrumb = ['Messages' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-contacts.css') }}">
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 4 CARTES --}}
    {{-- ============================================ --}}
    <div class="contacts-stats-grid">
        <div class="contact-stat-card">
            <div class="contact-stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="contact-stat-content">
                <div class="contact-stat-value">{{ $totalMessages }}</div>
                <div class="contact-stat-label">Total messages</div>
                <div class="contact-stat-sub">{{ $messagesCeMois }} ce mois-ci</div>
            </div>
        </div>
        <div class="contact-stat-card">
            <div class="contact-stat-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="contact-stat-content">
                <div class="contact-stat-value">{{ $messagesNonLus }}</div>
                <div class="contact-stat-label">Messages non lus</div>
                <div class="contact-stat-sub">Nécessitent votre attention</div>
            </div>
        </div>
        <div class="contact-stat-card">
            <div class="contact-stat-icon">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="contact-stat-content">
                <div class="contact-stat-value">{{ $messagesEnCours }}</div>
                <div class="contact-stat-label">En cours</div>
                <div class="contact-stat-sub">En attente de traitement</div>
            </div>
        </div>
        <div class="contact-stat-card">
            <div class="contact-stat-icon">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="contact-stat-content">
                <div class="contact-stat-value">{{ $messagesTraites }}</div>
                <div class="contact-stat-label">Messages traités</div>
                <div class="contact-stat-sub">{{ $traitesCeMois }} ce mois-ci</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABS DE FILTRE RAPIDE --}}
    {{-- ============================================ --}}
    <div class="contacts-tabs">
        <a href="{{ route('admin.contacts.index') }}" class="contacts-tab {{ !request('statut') ? 'active' : '' }}">
            <i class="fas fa-inbox"></i> Toutes les conversations
            <span class="badge-count">{{ $totalMessages }}</span>
        </a>
        <a href="{{ route('admin.contacts.index', ['statut' => 'nouveau']) }}" class="contacts-tab {{ request('statut') === 'nouveau' ? 'active' : '' }}">
            <i class="fas fa-envelope"></i> Non lus
            @if($messagesNonLus > 0)
                <span class="badge-count">{{ $messagesNonLus }}</span>
            @endif
        </a>
        <a href="{{ route('admin.contacts.index', ['statut' => 'en_cours']) }}" class="contacts-tab {{ request('statut') === 'en_cours' ? 'active' : '' }}">
            <i class="fas fa-spinner"></i> En cours
            @if($messagesEnCours > 0)
                <span class="badge-count">{{ $messagesEnCours }}</span>
            @endif
        </a>
        <a href="{{ route('admin.contacts.index', ['statut' => 'traite']) }}" class="contacts-tab {{ request('statut') === 'traite' ? 'active' : '' }}">
            <i class="fas fa-check-double"></i> Traités
            <span class="badge-count">{{ $messagesTraites }}</span>
        </a>
        <a href="{{ route('admin.contacts.index', ['statut' => 'archive']) }}" class="contacts-tab {{ request('statut') === 'archive' ? 'active' : '' }}">
            <i class="fas fa-archive"></i> Archivés
            <span class="badge-count">{{ $messagesArchives }}</span>
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- BARRE DE RECHERCHE --}}
    {{-- ============================================ --}}
    <form action="{{ route('admin.contacts.index') }}" method="GET" class="contacts-toolbar">
        <div class="contacts-search">
            <i class="fas fa-search"></i>
            <input type="text" name="recherche" id="contactSearch" value="{{ request('recherche') }}" 
                   class="contacts-search-input" placeholder="Rechercher par nom, email, sujet...">
        </div>

        <select name="motif" class="contacts-filtre-select">
            <option value="">Tous les motifs</option>
            <option value="partenariat" {{ request('motif') === 'partenariat' ? 'selected' : '' }}>Partenariat</option>
            <option value="don" {{ request('motif') === 'don' ? 'selected' : '' }}>Faire un don</option>
            <option value="adhesion" {{ request('motif') === 'adhesion' ? 'selected' : '' }}>Adhésion</option>
            <option value="benevolat" {{ request('motif') === 'benevolat' ? 'selected' : '' }}>Bénévolat</option>
            <option value="information" {{ request('motif') === 'information' ? 'selected' : '' }}>Information</option>
            <option value="presse" {{ request('motif') === 'presse' ? 'selected' : '' }}>Presse</option>
            <option value="plainte" {{ request('motif') === 'plainte' ? 'selected' : '' }}>Plainte</option>
            <option value="autre" {{ request('motif') === 'autre' ? 'selected' : '' }}>Autre</option>
        </select>

        @if(request('recherche') || request('motif') || request('statut'))
            <a href="{{ route('admin.contacts.index') }}" class="contacts-reset-btn" title="Réinitialiser les filtres">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>

    {{-- ============================================ --}}
    {{-- LISTE DES CONVERSATIONS --}}
    {{-- ============================================ --}}
    <div class="contacts-list-card">
        @if($conversations->count() > 0)
            @foreach($conversations as $conversation)
                @php
                    $avatarColors = ['blue', 'green', 'orange', 'red', 'purple'];
                    $avatarColor = $avatarColors[crc32($conversation->email) % count($avatarColors)];
                    $initiales = strtoupper(substr($conversation->nom, 0, 1));
                    if (strlen($conversation->nom) > 0) {
                        $parts = explode(' ', $conversation->nom);
                        $initiales = strtoupper(substr($parts[0], 0, 1));
                        if (count($parts) > 1) {
                            $initiales .= strtoupper(substr(end($parts), 0, 1));
                        }
                    }
                @endphp
                <div class="contacts-list-item {{ $conversation->non_lus > 0 ? 'non-lu' : '' }}">
                    {{-- Avatar --}}
                    <div class="contact-list-avatar {{ $avatarColor }}">
                        {{ $initiales }}
                        @if($conversation->non_lus > 0)
                            <span class="contact-list-unread-dot"></span>
                        @endif
                    </div>

                    {{-- Contenu --}}
                    <a href="{{ route('admin.contacts.show', urlencode($conversation->email)) }}" class="contact-list-content" style="text-decoration: none; color: inherit; display: block;">
                        <div class="contact-list-header">
                            <span class="contact-list-name">
                                {{ $conversation->nom }}
                                @if($conversation->organisation)
                                    <span class="contact-list-org">{{ $conversation->organisation }}</span>
                                @endif
                            </span>
                            <span class="contact-list-date">
                                <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($conversation->derniere_date)->diffForHumans() }}
                            </span>
                        </div>
                        <div class="contact-list-sujet">{{ $conversation->dernier_sujet ?: 'Sans objet' }}</div>
                        <div class="contact-list-preview">{{ Str::limit($conversation->dernier_message, 120) }}</div>
                    </a>

                    {{-- Métadonnées --}}
                    <div class="contact-list-meta">
                        <span class="contact-statut-badge contact-statut-{{ $conversation->dernier_statut }}">
                            <i class="fas fa-{{ 
                                $conversation->dernier_statut === 'nouveau' ? 'envelope' : 
                                ($conversation->dernier_statut === 'en_cours' ? 'spinner' : 
                                ($conversation->dernier_statut === 'traite' ? 'check-double' : 'archive'))
                            }}"></i>
                            {{ $conversation->dernier_statut === 'nouveau' ? 'Nouveau' : 
                               ($conversation->dernier_statut === 'en_cours' ? 'En cours' : 
                               ($conversation->dernier_statut === 'traite' ? 'Traité' : 'Archivé')) }}
                        </span>
                        <div class="contact-list-count">
                            <span class="count-badge" title="{{ $conversation->total_messages }} message(s) au total">
                                <i class="fas fa-comments"></i> {{ $conversation->total_messages }}
                            </span>
                            @if($conversation->non_lus > 0)
                                <span class="non-lu-badge" title="{{ $conversation->non_lus }} message(s) non lu(s)">
                                    <i class="fas fa-envelope"></i> {{ $conversation->non_lus }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions rapides --}}
                    <div class="contact-list-actions">
                        <a href="{{ route('admin.contacts.show', urlencode($conversation->email)) }}" class="contact-action-btn read" title="Voir la conversation">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('admin.contacts.destroyConversation') }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="email" value="{{ $conversation->email }}">
                            <button type="submit" class="contact-action-btn delete btn-delete-conversation" 
                                    data-email="{{ $conversation->email }}" 
                                    data-count="{{ $conversation->total_messages }}" 
                                    title="Supprimer la conversation">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- Pagination --}}
            @if($conversations->hasPages())
            <div class="contacts-pagination">
                {{ $conversations->links('pagination.custom') }}
            </div>
            @endif
        @else
            <div class="contacts-empty">
                <div class="contacts-empty-icon"><i class="fas fa-inbox"></i></div>
                <h4>Boîte de réception vide</h4>
                <p>
                    @if(request('recherche') || request('motif') || request('statut'))
                        Aucun message ne correspond à vos critères de recherche.
                    @else
                        Aucun message n'a encore été reçu. Les messages envoyés via le formulaire de contact apparaîtront ici.
                    @endif
                </p>
                @if(request('recherche') || request('motif') || request('statut'))
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-sync-alt"></i> Réinitialiser les filtres
                    </a>
                @endif
            </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-contacts.js') }}"></script>
@endsection