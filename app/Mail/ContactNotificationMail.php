<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Le message de contact concerné.
     */
    public Contact $contact;

    /**
     * Créer une nouvelle instance du message.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Définir l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'AHD-RDC — Nouveau message de contact : ' . $this->contact->sujet,
            from: $this->contact->email,
            replyTo: [$this->contact->email],
            to: env('MAIL_FROM_ADDRESS', 'contact@ahd-rdc.org'),
        );
    }

    /**
     * Définir le contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-notification',
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