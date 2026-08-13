<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class MonthlyNewsletterMail extends Mailable
{
    public function __construct(
        public Collection $posts,
        public string $month
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "NeuronTalks newsletter — {$this->month}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.monthly-newsletter');
    }
}
