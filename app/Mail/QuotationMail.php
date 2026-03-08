<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quotation,
        protected string $pdfContent,
        protected array $groupedLines
    ) {}

    // Email subject
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@tpinklab.com', 'TP Ink Lab'),
            subject: 'TP Ink Lab: Quotation #'.$this->quotation->reference
        );
    }

    // Email body content
    public function content(): Content
    {
        return new Content(
            view: 'email.template',
            with: [
                'quotation' => $this->quotation,
                'lines' => $this->groupedLines,
            ]
        );
    }

    // Attach PDF
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->pdfContent,
                'Quotation-'.$this->quotation->reference.'.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
