<?php

namespace App\Mail;

use App\Models\EventCollaborator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CollaboratorInvitedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly EventCollaborator $collaborator,
        public readonly string $signedUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Você foi convidado para fazer check-in em {$this->collaborator->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.collaborators.invited',
        );
    }
}
