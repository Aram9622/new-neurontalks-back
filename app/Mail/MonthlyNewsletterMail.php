<?php

namespace App\Mail;

use App\Models\MailTemplate;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class MonthlyNewsletterMail extends Mailable
{
    public function __construct(
        public Collection $posts,
        public string $month,
        public ?MailTemplate $template = null,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->template?->subject ?? "NeuronTalks newsletter — {$this->month}";

        return new Envelope(subject: $this->replacePlaceholders($subject));
    }

    public function content(): Content
    {
        if (! $this->template) {
            return new Content(view: 'emails.monthly-newsletter');
        }

        return new Content(view: 'emails.template', with: [
            'subject' => $this->replacePlaceholders($this->template->subject),
            'body' => $this->replacePlaceholders($this->template->body),
        ]);
    }

    private function replacePlaceholders(string $value): string
    {
        return str_replace('[[month]]', e($this->month), $value);
    }
}
