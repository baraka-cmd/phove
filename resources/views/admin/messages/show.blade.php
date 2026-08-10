@extends('layouts.admin')

@section('title', 'Conversation avec ' . $user->nom)

@section('page_title', 'Conversation')
@section('page_icon', 'comments')
@section('page_subtitle', 'Échange de messages avec ' . $user->nom . ' (' . $totalMessages . ' message(s)).')

@php
    $breadcrumb = ['Messagerie' => route('admin.messages.index'), $user->nom => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-contacts.css') }}">
    <style>
        .message-bubble {
            max-width: 75%;
            padding: 16px 20px;
            border-radius: 16px;
            margin-bottom: 16px;
            position: relative;
            line-height: 1.7;
            font-size: 14px;
            word-wrap: break-word;
        }
        .message-bubble.sent {
            background: var(--color-primary);
            color: #fff;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        .message-bubble.received {
            background: #f0f2f5;
            color: #1a1a1a;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }
        .message-bubble .message-sujet {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .message-bubble.received .message-sujet {
            border-bottom-color: rgba(0,0,0,0.08);
        }
        .message-bubble .message-meta {
            font-size: 10px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid rgba(255,255,255,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            opacity: 0.8;
        }
        .message-bubble.received .message-meta {
            border-top-color: rgba(0,0,0,0.06);
            color: var(--color-text-muted);
        }
        .message-bubble .message-status {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
        }
        .message-bubble.sent .message-status {
            color: rgba(255,255,255,0.7);
        }
        .message-divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }
        .message-divider span {
            background: #fff;
            padding: 4px 14px;
            font-size: 11px;
            color: var(--color-text-muted);
            font-weight: 600;
            border-radius: 50px;
            border: 1px solid #eef0f4;
            position: relative;
            z-index: 1;
        }
    </style>
@endsection

@section('content')

    <div class="contact-detail-container">

        {{-- Bouton retour --}}
        <a href="{{ route('admin.messages.index') }}" class="contact-detail-back">
            <i class="fas fa-arrow-left"></i> Retour à la messagerie
        </a>

        {{-- Carte principale --}}
        <div class="contact-detail-card">

            {{-- ============================================ --}}
            {{-- HEADER INTERLOCUTEUR --}}
            {{-- ============================================ --}}
            @php
                $avatarColors = ['blue', 'green', 'orange', 'red', 'purple'];
                $avatarColor = $avatarColors[crc32($user->nom) % count($avatarColors)];
                $initiales = strtoupper(substr($user->nom, 0, 1));
                $parts = explode(' ', $user->nom);
                $initiales = strtoupper(substr($parts[0], 0, 1));
                if (count($parts) > 1) {
                    $initiales .= strtoupper(substr(end($parts), 0, 1));
                }
            @endphp
            <div class="contact-detail-header">
                <div class="contact-detail-avatar {{ $avatarColor }}">
                    {{ $initiales }}
                </div>
                <div class="contact-detail-header-info">
                    <h2>{{ $user->nom }}</h2>
                    <div class="contact-detail-header-meta">
                        <span><i class="fas fa-envelope"></i> {{ $user->email }}</span>
                        @if($user->telephone)
                            <span><i class="fas fa-phone"></i> {{ $user->telephone }}</span>
                        @endif
                        <span>
                            <i class="fas fa-user-shield"></i> 
                            {{ $user->role === 'super_admin' ? 'Super Admin' : ($user->role === 'admin' ? 'Administrateur' : 'Éditeur') }}
                        </span>
                        <span><i class="fas fa-comments"></i> {{ $totalMessages }} message(s)</span>
                    </div>
                </div>
                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                    <form action="{{ route('admin.messages.archiverConversation', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-outline btn-sm" title="Archiver la conversation">
                            <i class="fas fa-archive"></i> Archiver
                        </button>
                    </form>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- MESSAGES --}}
            {{-- ============================================ --}}
            <div style="padding: 32px 36px; background: #fafbfc; min-height: 400px; max-height: 600px; overflow-y: auto;" id="messagesContainer">
                @php $previousDate = null; @endphp
                @forelse($messages as $message)
                    @php
                        $isSent = $message->expediteur_id === Auth::id();
                        $messageDate = $message->created_at->format('d/m/Y');
                        
                        // Afficher un séparateur de date si la date change
                        $showDateDivider = $previousDate !== $messageDate;
                        $previousDate = $messageDate;
                    @endphp

                    @if($showDateDivider)
                        <div class="message-divider">
                            <span>
                                <i class="far fa-calendar-alt"></i> 
                                {{ $message->created_at->format('d/m/Y') }}
                                @if($message->created_at->isToday())
                                    — Aujourd'hui
                                @elseif($message->created_at->isYesterday())
                                    — Hier
                                @endif
                            </span>
                        </div>
                    @endif

                    <div class="message-bubble {{ $isSent ? 'sent' : 'received' }}">
                        <div class="message-sujet">
                            <i class="fas fa-{{ $isSent ? 'paper-plane' : 'envelope' }}" style="margin-right: 6px;"></i>
                            {{ $message->sujet }}
                        </div>
                        <div style="white-space: pre-line;">{{ $message->contenu }}</div>
                        <div class="message-meta">
                            <span>
                                <i class="far fa-clock"></i> {{ $message->created_at->format('H:i') }}
                            </span>
                            <span class="message-status">
                                @if($isSent)
                                    @if($message->lu)
                                        <i class="fas fa-check-double" style="color: #fff;"></i> Lu {{ $message->date_lecture ? $message->date_lecture->format('H:i') : '' }}
                                    @else
                                        <i class="fas fa-check"></i> Envoyé
                                    @endif
                                @else
                                    @if($message->lu)
                                        <i class="fas fa-check-double" style="color: var(--color-success);"></i> Lu
                                    @else
                                        <i class="fas fa-envelope" style="color: var(--color-primary);"></i> Non lu
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 60px 20px; color: var(--color-text-muted);">
                        <i class="fas fa-comments" style="font-size: 3rem; opacity: 0.3; margin-bottom: 16px; display: block;"></i>
                        <p style="font-size: 14px;">Aucun message dans cette conversation.</p>
                        <p style="font-size: 13px;">Envoyez un premier message ci-dessous.</p>
                    </div>
                @endforelse
            </div>

            {{-- ============================================ --}}
            {{-- FORMULAIRE DE RÉPONSE --}}
            {{-- ============================================ --}}
            <div class="contact-reply-section">
                <div class="contact-reply-title">
                    <i class="fas fa-reply"></i> Répondre à {{ $user->nom }}
                </div>
                <form action="{{ route('admin.messages.repondre', $user) }}" method="POST" id="replyForm">
                    @csrf
                    <textarea name="contenu" id="replyTextarea" class="contact-reply-textarea" 
                              placeholder="Rédigez votre réponse..."
                              maxlength="5000" required></textarea>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <small style="font-size: 11px; color: var(--color-text-muted);">
                            <i class="fas fa-info-circle"></i> 
                            Votre message sera visible uniquement par <strong>{{ $user->nom }}</strong>
                            — <span id="replyCounter">0</span>/5000 caractères
                        </small>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane"></i> Envoyer
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Scroller automatiquement en bas de la conversation
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }

            // Compteur de caractères pour la réponse
            const replyTextarea = document.getElementById('replyTextarea');
            const replyCounter = document.getElementById('replyCounter');
            if (replyTextarea && replyCounter) {
                replyCounter.textContent = replyTextarea.value.length;
                replyTextarea.addEventListener('input', function () {
                    replyCounter.textContent = this.value.length;
                });
            }

            // Validation du formulaire de réponse
            const replyForm = document.getElementById('replyForm');
            if (replyForm && replyTextarea) {
                replyForm.addEventListener('submit', function (e) {
                    const contenu = replyTextarea.value.trim();
                    if (!contenu) {
                        e.preventDefault();
                        replyTextarea.style.borderColor = 'var(--color-accent)';
                        replyTextarea.style.background = '#fffbfb';
                        replyTextarea.focus();
                        return;
                    }
                    if (contenu.length < 5) {
                        e.preventDefault();
                        replyTextarea.style.borderColor = '#e67e22';
                        replyTextarea.style.background = '#fffdf5';
                        replyTextarea.focus();
                        return;
                    }
                });

                replyTextarea.addEventListener('focus', function () {
                    this.style.borderColor = '';
                    this.style.background = '';
                });
            }
        });
    </script>
    <script src="{{ asset('assets/js/admin-contacts.js') }}"></script>
@endsection