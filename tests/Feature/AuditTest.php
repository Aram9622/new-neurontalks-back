<?php

namespace Tests\Feature;

use App\Mail\AuditNotificationMail;
use App\Models\MailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_an_audit_and_sends_the_configured_email(): void
    {
        Mail::fake();

        $template = MailTemplate::create([
            'name' => 'Audit notification',
            'type' => MailTemplate::TYPE_AUDIT,
            'subject' => 'Audit from [[name]]',
            'body' => '<p>[[improve]]</p>',
            'is_default' => true,
        ]);

        $response = $this->postJson('/api/audit', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+123456789',
            'message' => 'Please audit our website.',
            'improve' => 'Improve conversion rate.',
        ]);

        $response->assertCreated()->assertJsonPath('data.improve', 'Improve conversion rate.');
        $this->assertDatabaseHas('audits', ['email' => 'jane@example.com', 'improve' => 'Improve conversion rate.']);
        Mail::assertSent(AuditNotificationMail::class, fn (AuditNotificationMail $mail) => $mail->template->is($template));
    }

    public function test_improve_is_required(): void
    {
        Mail::fake();

        $this->postJson('/api/audit', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Please audit our website.',
        ])->assertUnprocessable()->assertJsonValidationErrors('improve');

        $this->assertDatabaseCount('audits', 0);
        Mail::assertNothingSent();
    }
}
