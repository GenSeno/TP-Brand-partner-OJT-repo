<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public Payment $payment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('noreply@tpinklab.com', 'TP Ink Lab'),
            subject: 'TP Ink Lab: Payment Acknowledgement #PA-' . str_pad($this->payment->id, 4, '0', STR_PAD_LEFT),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.payment-receipt',
            with: [
                'invoice' => $this->invoice,
                'payment' => $this->payment,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
