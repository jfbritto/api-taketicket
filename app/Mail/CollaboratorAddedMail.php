<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CollaboratorAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Event $event) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Você foi adicionado à equipe de check-in — {$this->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.collaborators.added',
        );
    }
}
