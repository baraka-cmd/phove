<?php

namespace App\Mail;

use App\Models\Don;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Le don concerné.
     */
    public Don $don;

    /**
     * Créer une nouvelle instance du message.
     */
    public function __construct(Don $don)
    {
        $this->don = $don;
    }

    /**
     * Définir l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AHD-RDC — Confirmation de votre don n° ' . $this->don->numero_recu,
            from: env('MAIL_FROM_ADDRESS', 'contact@ahd-rdc.org'),
            to: $this->don->donateur_email,
        );
    }

    /**
     * Définir le contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.don-confirmation',
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