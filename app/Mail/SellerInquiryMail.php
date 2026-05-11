<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SellerInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('sales@carspares.co.ke', 'Car Spares Sales'),
            subject: 'New buyer inquiry for '.$this->inquiry->product?->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.seller-inquiry',
        );
    }
}
