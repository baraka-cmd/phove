@extends('layouts.admin')

@section('title', 'Conversation avec ' . $expediteur->nom)

@section('page_title', 'Conversation')
@section('page_icon', 'comments')
@section('page_subtitle', 'Historique complet des échanges avec ' . $expediteur->nom . ' (' . $totalMessagesExp . ' message(s)).')

@php
    $breadcrumb = ['Messages' => route('admin.contacts.index'), $expediteur->nom => null];
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-contacts.css') }}">
@endsection

@section('content')

    <div class="contact-detail-container">

        {{-- Bouton retour --}}
        <a href="{{ route('admin.contacts.index') }}" class="contact-detail-back">
            <i class="fas fa-arrow-left"></i> Retour à la boîte de réception
        </a>

        {{-- Navigation précédent/suivant --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                @if($precedent)
                    <a href="{{ route('admin.contacts.show', urlencode($precedent->email)) }}" class="contact-detail-back" style="margin-bottom: 0;">
                        <i class="fas fa-chevron-left"></i> Conversation précédente
                    </a>
                @endif
            </div>
            <div>
                @if($suivant)
                    <a href="{{ route('admin.contacts.show', urlencode($suivant->email)) }}" class="contact-detail-back" style="margin-bottom: 0;">
                        Conversation suivante <i class="fas fa-chevron-right"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Carte principale --}}
        <div class="contact-detail-card">
            <div class="contact-detail-grid">
                
                {{-- ============================================ --}}
                {{-- PARTIE PRINCIPALE : MESSAGES --}}
                {{-- ============================================ --}}
                <div class="contact-detail-main">

                    {{-- Header expéditeur --}}
                    @php
                        $avatarColors = ['blue', 'green', 'orange', 'red', 'purple'];
                        $avatarColor = $avatarColors[crc32($expediteur->email) % count($avatarColors)];
                        $initiales = strtoupper(substr($expediteur->nom, 0, 1));
                        $parts = explode(' ', $expediteur->nom);
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
                            <h2>{{ $expediteur->nom }}</h2>
                            <div class="contact-detail-header-meta">
                                <span><i class="fas fa-envelope"></i> {{ $expediteur->email }}</span>
                                @if($expediteur->telephone)
                                    <span><i class="fas fa-phone"></i> {{ $expediteur->telephone }}</span>
                                @endif
                                @if($expediteur->organisation)
                                    <span><i class="fas fa-building"></i> {{ $expediteur->organisation }}</span>
                                @endif
                                <span><i class="fas fa-comments"></i> {{ $totalMessagesExp }} message(s)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Liste des messages --}}
                    <div class="contact-detail-messages">
                        @foreach($messages as $message)
                            <div class="contact-message-item {{ !$message->lu ? 'non-lu' : '' }}" id="message-{{ $message->id }}">
                                <div class="contact-message-header">
                                    <div class="contact-message-sujet">
                                        @if(!$message->lu)
                                            <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--color-primary); margin-right: 8px;"></span>
                                        @endif
                                        {{ $message->sujet ?: 'Sans objet' }}
                                    </div>
                                    <div class="contact-message-date">
                                        <i class="far fa-calendar-alt"></i> {{ $message->created_at->format('d/m/Y à H:i') }}
                                        @if($message->created_at->diffInHours(now()) < 24)
                                            <span style="color: var(--color-primary); font-weight: 600; margin-left: 8px;">• {{ $message->created_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="contact-message-body">
                                    {{ $message->message }}
                                </div>
                                <div class="contact-message-footer">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span class="contact-statut-badge contact-statut-{{ $message->statut }}">
                                            <i class="fas fa-{{ 
                                                $message->statut === 'nouveau' ? 'envelope' : 
                                                ($message->statut === 'en_cours' ? 'spinner' : 
                                                ($message->statut === 'traite' ? 'check-double' : 'archive'))
                                            }}"></i>
                                            {{ $message->statutLibelle() }}
                                        </span>
                                        @if($message->lu)
                                            <span style="font-size: 11px; color: var(--color-text-muted);">
                                                <i class="fas fa-check"></i> Lu
                                                @if($message->date_lecture)
                                                    le {{ $message->date_lecture->format('d/m/Y à H:i') }}
                                                @endif
                                            </span>
                                        @endif
                                        @if($message->repondu)
                                            <span style="font-size: 11px; color: var(--color-success);">
                                                <i class="fas fa-reply"></i> Répondu
                                                @if($message->date_reponse)
                                                    le {{ $message->date_reponse->format('d/m/Y à H:i') }}
                                                @endif
                                            </span>
                                        @endif
                                        @if($message->traitePar)
                                            <span style="font-size: 11px; color: var(--color-text-muted);">
                                                <i class="fas fa-user-check"></i> {{ $message->traitePar->nom }}
                                            </span>
                                        @endif
                                    </div>
                                    <div style="display: flex; gap: 6px;">
                                        {{-- Marquer lu/non lu --}}
                                        <form action="{{ $message->lu ? route('admin.contacts.marquerNonLu', $message) : route('admin.contacts.marquerLu', $message) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="contact-action-btn read" 
                                                    title="{{ $message->lu ? 'Marquer comme non lu' : 'Marquer comme lu' }}">
                                                <i class="fas fa-{{ $message->lu ? 'envelope' : 'envelope-open' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Changer statut --}}
                                        <form action="{{ route('admin.contacts.changeStatut', $message) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <select name="statut" class="contacts-filtre-select contact-statut-select" 
                                                    style="width: auto; min-width: 130px; padding: 6px 30px 6px 10px; font-size: 11px;"
                                                    onchange="this.form.submit()">
                                                <option value="nouveau" {{ $message->statut === 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                                                <option value="en_cours" {{ $message->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                                                <option value="traite" {{ $message->statut === 'traite' ? 'selected' : '' }}>Traité</option>
                                                <option value="archive" {{ $message->statut === 'archive' ? 'selected' : '' }}>Archivé</option>
                                            </select>
                                        </form>

                                        {{-- Supprimer --}}
                                        <form action="{{ route('admin.contacts.destroy', $message) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="contact-action-btn delete btn-delete-contact" 
                                                    data-nom="{{ $expediteur->nom }}" title="Supprimer ce message">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ============================================ --}}
                    {{-- FORMULAIRE DE RÉPONSE --}}
                    {{-- ============================================ --}}
                    <div class="contact-reply-section">
                        <div class="contact-reply-title">
                            <i class="fas fa-reply"></i> Répondre à {{ $expediteur->nom }}
                        </div>
                        <form action="{{ route('admin.contacts.repondre', $messages->last()) }}" method="POST" id="replyForm">
                            @csrf
                            <textarea name="reponse" id="replyTextarea" class="contact-reply-textarea" 
                                      placeholder="Rédigez votre réponse... Elle sera envoyée par email à {{ $expediteur->email }}"
                                      maxlength="5000" required></textarea>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0;">
                                <small style="font-size: 11px; color: var(--color-text-muted);">
                                    <i class="fas fa-info-circle"></i> 
                                    Votre réponse sera envoyée depuis <strong>{{ env('MAIL_FROM_ADDRESS', 'contact@ahd-rdc.org') }}</strong>
                                    — <span id="replyCounter">0</span>/5000 caractères
                                </small>
                                <div style="display: flex; gap: 10px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-paper-plane"></i> Envoyer la réponse
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- ============================================ --}}
                    {{-- NOTE INTERNE --}}
                    {{-- ============================================ --}}
                    @php
                        $dernierMessage = $messages->last();
                    @endphp
                    <div class="contact-note-section">
                        <div class="contact-note-title">
                            <i class="fas fa-sticky-note"></i> Note interne
                            <small style="font-weight: 400; color: var(--color-text-muted); margin-left: 4px;">(visible uniquement par l'équipe administrative)</small>
                        </div>
                        <form action="{{ route('admin.contacts.note', $dernierMessage) }}" method="POST" id="noteForm">
                            @csrf
                            @method('PUT')
                            <textarea name="note_interne" id="noteTextarea" class="contact-note-textarea" 
                                      placeholder="Ajoutez une note interne concernant cet expéditeur..."
                                      maxlength="2000">{{ $dernierMessage->note_interne }}</textarea>
                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn btn-outline btn-sm">
                                    <i class="fas fa-save"></i> Enregistrer la note
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                {{-- ============================================ --}}
                {{-- SIDEBAR : INFORMATIONS EXPÉDITEUR --}}
                {{-- ============================================ --}}
                <div class="contact-detail-sidebar">
                    
                    {{-- Informations contact --}}
                    <div class="contact-sidebar-section">
                        <div class="contact-sidebar-title">
                            <i class="fas fa-address-card"></i> Contact
                        </div>
                        <div class="contact-sidebar-field">
                            <div class="contact-sidebar-label">Nom</div>
                            <div class="contact-sidebar-value">{{ $expediteur->nom }}</div>
                        </div>
                        <div class="contact-sidebar-field">
                            <div class="contact-sidebar-label">Email</div>
                            <div class="contact-sidebar-value">
                                <a href="mailto:{{ $expediteur->email }}">{{ $expediteur->email }}</a>
                            </div>
                        </div>
                        @if($expediteur->telephone)
                            <div class="contact-sidebar-field">
                                <div class="contact-sidebar-label">Téléphone</div>
                                <div class="contact-sidebar-value">
                                    <a href="tel:{{ $expediteur->telephone }}">{{ $expediteur->telephone }}</a>
                                </div>
                            </div>
                        @endif
                        @if($expediteur->organisation)
                            <div class="contact-sidebar-field">
                                <div class="contact-sidebar-label">Organisation</div>
                                <div class="contact-sidebar-value">{{ $expediteur->organisation }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- Statistiques --}}
                    <div class="contact-sidebar-section">
                        <div class="contact-sidebar-title">
                            <i class="fas fa-chart-bar"></i> Statistiques
                        </div>
                        <div class="contact-sidebar-field">
                            <div class="contact-sidebar-label">Messages échangés</div>
                            <div class="contact-sidebar-value">{{ $totalMessagesExp }}</div>
                        </div>
                        <div class="contact-sidebar-field">
                            <div class="contact-sidebar-label">Premier contact</div>
                            <div class="contact-sidebar-value">{{ $messages->first()->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="contact-sidebar-field">
                            <div class="contact-sidebar-label">Dernier contact</div>
                            <div class="contact-sidebar-value">{{ $messages->last()->created_at->format('d/m/Y à H:i') }}</div>
                        </div>
                        <div class="contact-sidebar-field">
                            <div class="contact-sidebar-label">Statut</div>
                            <div class="contact-sidebar-value">
                                <span class="contact-statut-badge contact-statut-{{ $dernierMessage->statut }}">
                                    <i class="fas fa-{{ 
                                        $dernierMessage->statut === 'nouveau' ? 'envelope' : 
                                        ($dernierMessage->statut === 'en_cours' ? 'spinner' : 
                                        ($dernierMessage->statut === 'traite' ? 'check-double' : 'archive'))
                                    }}"></i>
                                    {{ $dernierMessage->statutLibelle() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Assignation --}}
                    <div class="contact-sidebar-section">
                        <div class="contact-sidebar-title">
                            <i class="fas fa-user-check"></i> Assignation
                        </div>
                        <div class="contact-sidebar-field">
                            <form action="{{ route('admin.contacts.assigner', $dernierMessage) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select name="traite_par" class="contacts-filtre-select contact-statut-select" 
                                        style="width: 100%; font-size: 12px; margin-bottom: 8px;"
                                        onchange="this.form.submit()">
                                    <option value="">-- Non assigné --</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ $dernierMessage->traite_par == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="contact-sidebar-section">
                        <div class="contact-sidebar-title">
                            <i class="fas fa-cog"></i> Actions
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="mailto:{{ $expediteur->email }}" class="btn btn-outline btn-sm" style="width: 100%;">
                                <i class="fas fa-external-link-alt"></i> Ouvrir dans le client mail
                            </a>
                            <form action="{{ route('admin.contacts.destroyConversation') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="email" value="{{ $expediteur->email }}">
                                <button type="submit" class="btn btn-accent btn-sm btn-delete-conversation" 
                                        data-email="{{ $expediteur->email }}" 
                                        data-count="{{ $totalMessagesExp }}" 
                                        style="width: 100%;">
                                    <i class="fas fa-trash-alt"></i> Supprimer la conversation
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/admin-contacts.js') }}"></script>
@endsection