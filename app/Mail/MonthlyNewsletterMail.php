<?php

namespace App\Mail;

use App\Models\MailTemplate;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        $posts = $this->posts->map(fn ($post) => sprintf(
            '<article><h2>%s</h2><p>%s</p><p><a href="%s">Read more</a></p></article>',
            e($post->title), e(Str::limit(strip_tags($post->content ?? ''), 200)),
            e(url('/blogs/'.$post->slug))
        ))->implode('');

        return str_replace(['[[month]]', '[[posts]]'], [e($this->month), $posts], $value);
    }
}
