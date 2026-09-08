<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SkOrientasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $emailSubject,
        public string $emailBody,
        public string $attachmentPath,
        public string $attachmentName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.notifikasi-plain',
            with: ['body' => $this->emailBody],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('public', $this->attachmentPath)
                ->as($this->attachmentName)
                ->withMime('application/pdf'),
        ];
    }
}
