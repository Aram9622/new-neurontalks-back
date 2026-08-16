<?php

namespace Tests\Feature;

use App\Mail\AdminContactNotificationMail;
use App\Mail\ContactReplyMail;
use App\Models\Contact;
use App\Models\MailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMailTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_notification_uses_the_admin_template(): void
    {
        Mail::fake();

        $template = MailTemplate::create([
            'name' => 'Contact received',
            'type' => MailTemplate::TYPE_CONTACT_NOTIFICATION,
            'subject' => 'Message from [[name]]',
            'body' => '<p>[[email]] sent: [[message]]</p>',
        ]);

        $this->postJson('/api/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+123456789',
            'message' => 'Please contact me.',
        ])->assertCreated();

        Mail::assertSent(AdminContactNotificationMail::class, function ($mail) use ($template) {
            return $mail->template->is($template)
                && $mail->envelope()->subject === 'Message from Jane Doe'
                && str_contains($mail->render(), '<p>jane@example.com sent: Please contact me.</p>');
        });
    }

    public function test_contact_reply_can_use_a_separate_admin_template(): void
    {
        $contact = Contact::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Can you help?',
        ]);
        $template = MailTemplate::create([
            'name' => 'Contact answer',
            'type' => MailTemplate::TYPE_CONTACT_REPLY,
            'subject' => 'Hello [[name]]',
            'body' => '<p>Your question: [[message]]</p><p>Our answer: [[reply]]</p>',
        ]);

        $mail = new ContactReplyMail($contact, 'Yes, we can.', $template);

        $this->assertSame('Hello Jane Doe', $mail->envelope()->subject);
        $this->assertStringContainsString(
            '<p>Your question: Can you help?</p><p>Our answer: Yes, we can.</p>',
            $mail->render(),
        );
    }
}
