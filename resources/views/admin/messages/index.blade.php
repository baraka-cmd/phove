@extends('layouts.admin')

@section('title', 'Messagerie interne')

@section('page_title', 'Messagerie')
@section('page_icon', 'comments')
@section('page_subtitle', 'Communiquez avec les autres administrateurs et utilisateurs du système.')

@php
    $breadcrumb = ['Messagerie' => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-contacts.css') }}">
    <style>
        /* Styles spécifiques à la messagerie interne */
        .messages-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 24px;
            padding: 6px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.03);
            overflow-x: auto;
        }
        .messages-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-muted);
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.25s ease;
            border: none;
            background: transparent;
            cursor: pointer;
            font-family: var(--font-primary);
        }
        .messages-tab:hover {
            background: var(--color-bg-light);
            color: #1a1a1a;
        }
        .messages-tab.active {
            background: var(--color-primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(22, 85, 143, 0.25);
        }
        .messages-tab .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            border-radius: 50px;
            padding: 0 7px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(0, 0, 0, 0.08);
        }
        .messages-tab.active .badge-count {
            background: rgba(255, 255, 255, 0.25);
        }

        .conversation-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 600;
        }
        .conversation-type-recues {
            background: var(--color-primary-lighter);
            color: var(--color-primary);
        }
        .conversation-type-envoyees {
            background: var(--color-success-light);
            color: var(--color-success);
        }
    </style>
@endsection

@section('content')

    {{-- ============================================ --}}
    {{-- KPI — 3 CARTES --}}
    {{-- ============================================ --}}
    <div class="contacts-stats-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="contact-stat-card">
            <div class="contact-stat-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="contact-stat-content">
                <div class="contact-stat-value">{{ $totalMessages }}</div>
                <div class="contact-stat-label">Messages reçus</div>
            </div>
        </div>
        <div class="contact-stat-card">
            <div class="contact-stat-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="contact-stat-content">
                <div class="contact-stat-value">{{ $messagesNonLus }}</div>
                <div class="contact-stat-label">Messages non lus</div>
            </div>
        </div>
        <div class="contact-stat-card">
            <div class="contact-stat-icon">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="contact-stat-content">
                <div class="contact-stat-value">{{ $messagesEnvoyes }}</div>
                <div class="contact-stat-label">Messages envoyés</div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- TABS --}}
    {{-- ============================================ --}}
    <div class="messages-tabs">
        <a href="{{ route('admin.messages.index') }}" class="messages-tab {{ !request()->routeIs('admin.messages.envoyes') ? 'active' : '' }}">
            <i class="fas fa-inbox"></i> Boîte de réception
            @if($messagesNonLus > 0)
                <span class="badge-count">{{ $messagesNonLus }}</span>
            @endif
        </a>
        <a href="{{ route('admin.messages.envoyes') }}" class="messages-tab {{ request()->routeIs('admin.messages.envoyes') ? 'active' : '' }}">
            <i class="fas fa-paper-plane"></i> Messages envoyés
        </a>
        <a href="{{ route('admin.messages.composer') }}" class="messages-tab" style="margin-left: auto;">
            <i class="fas fa-pen"></i> Nouveau message
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- LISTE DES CONVERSATIONS --}}
    {{-- ============================================ --}}
    <div class="contacts-list-card">
        @if($conversations->count() > 0)
            @foreach($conversations as $conversation)
                @php
                    $interlocuteur = $conversation->interlocuteur;
                    if (!$interlocuteur) continue;
                    
                    $avatarColors = ['blue', 'green', 'orange', 'red', 'purple'];
                    $avatarColor = $avatarColors[crc32($interlocuteur->nom) % count($avatarColors)];
                    $initiales = strtoupper(substr($interlocuteur->nom, 0, 1));
                    $parts = explode(' ', $interlocuteur->nom);
                    $initiales = strtoupper(substr($parts[0], 0, 1));
                    if (count($parts) > 1) {
                        $initiales .= strtoupper(substr(end($parts), 0, 1));
                    }
                    
                    $isNonLu = $conversation->has_unread > 0;
                @endphp
                <div class="contacts-list-item {{ $isNonLu ? 'non-lu' : '' }}">
                    {{-- Avatar --}}
                    <div class="contact-list-avatar {{ $avatarColor }}">
                        {{ $initiales }}
                        @if($isNonLu)
                            <span class="contact-list-unread-dot"></span>
                        @endif
                    </div>

                    {{-- Contenu --}}
                    <a href="{{ route('admin.messages.show', $interlocuteur) }}" class="contact-list-content" style="text-decoration: none; color: inherit; display: block;">
                        <div class="contact-list-header">
                            <span class="contact-list-name">
                                {{ $interlocuteur->nom }}
                                <span style="font-size: 11px; font-weight: 400; color: var(--color-text-muted);">
                                    ({{ $interlocuteur->role === 'super_admin' ? 'Super Admin' : ($interlocuteur->role === 'admin' ? 'Admin' : 'Éditeur') }})
                                </span>
                            </span>
                            <span class="contact-list-date">
                                <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($conversation->derniere_date)->diffForHumans() }}
                            </span>
                        </div>
                        <div class="contact-list-preview" style="font-weight: {{ $isNonLu ? '700' : '400' }}; color: {{ $isNonLu ? '#1a1a1a' : '' }};">
                            {{ $conversation->type === 'envoye' ? 'Vous avez envoyé un message' : 'Dernier message reçu' }}
                        </div>
                    </a>

                    {{-- Métadonnées --}}
                    <div class="contact-list-meta">
                        <span class="conversation-type-badge conversation-type-{{ $conversation->type === 'recu' ? 'recues' : 'envoyees' }}">
                            <i class="fas fa-{{ $conversation->type === 'recu' ? 'arrow-down' : 'arrow-up' }}"></i>
                            {{ $conversation->type === 'recu' ? 'Reçu' : 'Envoyé' }}
                        </span>
                        <div class="contact-list-count">
                            <span class="count-badge" title="{{ $conversation->total_messages }} message(s)">
                                <i class="fas fa-comments"></i> {{ $conversation->total_messages }}
                            </span>
                            @if($conversation->non_lus > 0)
                                <span class="non-lu-badge" title="{{ $conversation->non_lus }} non lu(s)">
                                    <i class="fas fa-envelope"></i> {{ $conversation->non_lus }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions rapides --}}
                    <div class="contact-list-actions">
                        <a href="{{ route('admin.messages.show', $interlocuteur) }}" class="contact-action-btn read" title="Voir la conversation">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('admin.messages.archiverConversation', $interlocuteur) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="contact-action-btn archive" title="Archiver la conversation">
                                <i class="fas fa-archive"></i>
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
                <div class="contacts-empty-icon"><i class="fas fa-comments"></i></div>
                <h4>Aucune conversation</h4>
                <p>Vous n'avez pas encore de messages. Commencez par envoyer un message à un administrateur.</p>
                <a href="{{ route('admin.messages.composer') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-pen"></i> Nouveau message
                </a>
            </div>
        @endif
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-contacts.js') }}"></script>
@endsection