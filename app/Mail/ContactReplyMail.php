<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact,
        public string $replyMessage,
        public ?MailTemplate $template = null,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->replacePlaceholders(
                $this->template?->subject ?? 'Reply to your inquiry from Neuron'
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if (! $this->template) {
            return new Content(view: 'emails.contact-reply');
        }

        return new Content(view: 'emails.template', with: [
            'subject' => $this->replacePlaceholders($this->template->subject),
            'body' => $this->replacePlaceholders($this->template->body),
        ]);
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }

    private function replacePlaceholders(string $value): string
    {
        return str_replace(
            ['[[name]]', '[[email]]', '[[phone]]', '[[message]]', '[[reply]]'],
            [e($this->contact->name), e($this->contact->email), e($this->contact->phone ?? ''), e($this->contact->message), e($this->replyMessage)],
            $value,
        );
    }
}
