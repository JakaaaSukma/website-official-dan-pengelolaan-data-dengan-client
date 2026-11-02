<?php

namespace App\Mail;

use App\Models\HrMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HrNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public HrMessage $hrMessage;
    public string $recipientName;

    /**
     * Create a new message instance.
     */
    public function __construct(HrMessage $hrMessage, string $recipientName = 'User')
    {
        $this->hrMessage = $hrMessage;
        $this->recipientName = $recipientName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $priorityPrefix = match ($this->hrMessage->priority) {
            'urgent' => '🚨 [URGENT] ',
            'high' => '⚠️ [HIGH] ',
            default => '',
        };

        return new Envelope(
            subject: $priorityPrefix . 'Notifikasi HR: ' . $this->hrMessage->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.hr-notification',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
