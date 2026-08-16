<?php

namespace App\Mail;

use App\Models\MailTemplate;
use App\Models\NewsletterSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterSubscriptionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterSubscription $subscription,
        public ?MailTemplate $template = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->replacePlaceholders(
            $this->template?->subject ?? 'Welcome to the NeuronTalks newsletter'
        ));
    }

    public function content(): Content
    {
        if (! $this->template) {
            return new Content(view: 'emails.newsletter-subscription-confirmation');
        }

        return new Content(view: 'emails.template', with: [
            'subject' => $this->replacePlaceholders($this->template->subject),
            'body' => $this->replacePlaceholders($this->template->body),
        ]);
    }

    private function replacePlaceholders(string $value): string
    {
        return str_replace('[[email]]', e($this->subscription->email), $value);
    }
}
