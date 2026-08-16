<?php

namespace Tests\Feature;

use App\Mail\NewsletterSubscriptionConfirmationMail;
use App\Models\MailTemplate;
use App\Models\NewsletterSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_confirmation_email_is_sent_after_subscribing(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/subscribe', [
            'email' => ' Subscriber@Example.com ',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'subscriber@example.com');

        Mail::assertSent(NewsletterSubscriptionConfirmationMail::class, function ($mail) {
            return $mail->hasTo('subscriber@example.com')
                && $mail->subscription->email === 'subscriber@example.com'
                && $mail->template === null;
        });
    }

    public function test_duplicate_subscription_returns_a_clean_json_response_without_an_accept_header(): void
    {
        Mail::fake();

        NewsletterSubscription::create(['email' => 'subscriber@example.com']);

        $response = $this->post('/api/subscribe', [
            'email' => ' Subscriber@Example.com ',
        ]);

        $response
            ->assertUnprocessable()
            ->assertHeader('content-type', 'application/json')
            ->assertExactJson([
                'success' => false,
                'message' => 'This email is already subscribed to the newsletter.',
                'errors' => [
                    'email' => ['This email is already subscribed to the newsletter.'],
                ],
            ]);

        $this->assertDatabaseCount('newsletter_subscriptions', 1);

        Mail::assertNothingSent();
    }

    public function test_subscription_confirmation_uses_the_configured_default_template(): void
    {
        Mail::fake();

        $template = MailTemplate::create([
            'name' => 'Subscription welcome',
            'type' => MailTemplate::TYPE_SUBSCRIPTION,
            'subject' => 'Welcome [[email]]',
            'body' => '<p>Thanks, [[email]]!</p>',
            'is_default' => true,
        ]);

        $this->postJson('/api/subscribe', [
            'email' => 'subscriber@example.com',
        ])->assertCreated();

        Mail::assertSent(NewsletterSubscriptionConfirmationMail::class, function ($mail) use ($template) {
            $html = $mail->render();

            return $mail->template->is($template)
                && $mail->envelope()->subject === 'Welcome subscriber@example.com'
                && str_contains($html, '<p>Thanks, subscriber@example.com!</p>');
        });
    }
}
