<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttributionMail extends Mailable
{
    use Queueable, SerializesModels;

    protected object $attribution;

    protected User $user;

    protected $patient;

    public function __construct(object $attribution, User $user, $patient = null)
    {
        $this->attribution = $attribution;
        $this->user = $user;
        $this->patient = $patient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notification : Attribution de dossier médicale',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $attribution = $this->attribution;
        $user = $this->attribution->user;
        $patient = $this->attribution->patient;

        return new Content(
            view: 'user.emails.attribution',
            with: [
                'attribution' => $this->attribution,
                'user' => $this->user,
                'patient' => $this->patient,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
