<?php

namespace App\Mail;

use App\Models\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterSubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterSubscription $subscription)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to the NeuronTalks newsletter');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.newsletter-subscription-confirmation');
    }
}
