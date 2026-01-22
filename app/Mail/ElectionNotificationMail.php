<?php

namespace App\Mail;

use App\Models\Election;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ElectionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $election;
    public $type;
    public $voterName;

    /**
     * Create a new message instance.
     */
    public function __construct(Election $election, string $type, string $voterName)
    {
        $this->election = $election;
        $this->type = $type; // 'reminder', 'open', 'closed'
        $this->voterName = $voterName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'reminder' => "Reminder: {$this->election->title} is starting soon!",
            'open' => "Polls are OPEN: {$this->election->title}",
            'closed' => "Polls are CLOSED: {$this->election->title}",
            default => "Election Notification: {$this->election->title}",
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.election-notification',
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
