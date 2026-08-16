<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminContactNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact,
        public ?MailTemplate $template = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->replacePlaceholders(
                $this->template?->subject ?? 'New Inquiry: [[name]] - NeuronTalks'
            ),
        );
    }

    public function content(): Content
    {
        if (! $this->template) {
            return new Content(view: 'emails.admin-notification');
        }

        return new Content(view: 'emails.template', with: [
            'subject' => $this->replacePlaceholders($this->template->subject),
            'body' => $this->replacePlaceholders($this->template->body),
        ]);
    }

    private function replacePlaceholders(string $value): string
    {
        return str_replace(
            ['[[name]]', '[[email]]', '[[phone]]', '[[message]]'],
            [e($this->contact->name), e($this->contact->email), e($this->contact->phone ?? ''), e($this->contact->message)],
            $value,
        );
    }
}
