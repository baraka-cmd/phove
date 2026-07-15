<?php

namespace App\Mail;

use App\Models\Membre;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdhesionApprouveeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Le membre concerné.
     */
    public Membre $membre;

    /**
     * Créer une nouvelle instance du message.
     */
    public function __construct(Membre $membre)
    {
        $this->membre = $membre;
    }

    /**
     * Définir l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AHD-RDC — Votre demande d\'adhésion a été approuvée ! 🎉',
            from: env('MAIL_FROM_ADDRESS', 'contact@ahd-rdc.org'),
            to: $this->membre->email,
        );
    }

    /**
     * Définir le contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.adhesion-approuvee',
        );
    }

    /**
     * Pièces jointes éventuelles.
     */
    public function attachments(): array
    {
        return [];
    }
}