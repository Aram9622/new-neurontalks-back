<?php

namespace App\Mail;

use App\Models\Audit;
use App\Models\MailTemplate;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuditNotificationMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Audit $audit,
        public ?MailTemplate $template = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->replacePlaceholders(
            $this->template?->subject ?? 'New audit request from [[name]] - NeuronTalks'
        ));
    }

    public function content(): Content
    {
        if (! $this->template) {
            return new Content(view: 'emails.audit-notification');
        }

        return new Content(view: 'emails.template', with: [
            'subject' => $this->replacePlaceholders($this->template->subject),
            'body' => $this->replacePlaceholders($this->template->body),
        ]);
    }

    private function replacePlaceholders(string $value): string
    {
        return str_replace(
            ['[[name]]', '[[email]]', '[[phone]]', '[[message]]', '[[improve]]'],
            [e($this->audit->name), e($this->audit->email), e($this->audit->phone ?? ''), e($this->audit->message), e($this->audit->improve)],
            $value,
        );
    }
}
