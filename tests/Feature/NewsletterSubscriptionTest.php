<?php

namespace Tests\Feature;

use App\Mail\NewsletterSubscriptionConfirmationMail;
use App\Models\MailTemplate;
use App\Models\NewsletterSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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

    public function test_subscription_email_contains_a_signed_unsubscribe_link(): void
    {
        $subscription = NewsletterSubscription::create(['email' => 'subscriber@example.com']);
        $mail = new NewsletterSubscriptionConfirmationMail($subscription);

        $this->assertStringContainsString('Unsubscribe', $mail->render());
        $this->assertStringContainsString(e($subscription->unsubscribeUrl()), $mail->render());
    }

    public function test_subscriber_can_unsubscribe_with_a_signed_link(): void
    {
        $subscription = NewsletterSubscription::create(['email' => 'subscriber@example.com']);

        $this->assertStringContainsString('/api/newsletter/unsubscribe/', $subscription->unsubscribeUrl());

        $this->get($subscription->unsubscribeUrl())
            ->assertOk()
            ->assertSee('You have been unsubscribed');

        $this->assertModelMissing($subscription);
    }

    public function test_unsubscribe_link_rejects_an_invalid_signature(): void
    {
        $subscription = NewsletterSubscription::create(['email' => 'subscriber@example.com']);
        $url = URL::route('newsletter.unsubscribe', ['subscription' => $subscription]);

        $this->get($url)->assertForbidden();

        $this->assertModelExists($subscription);
    }

    public function test_legacy_signed_unsubscribe_link_remains_valid(): void
    {
        $subscription = NewsletterSubscription::create(['email' => 'subscriber@example.com']);
        $url = URL::signedRoute('newsletter.unsubscribe.legacy', ['subscription' => $subscription]);

        $this->get($url)
            ->assertOk()
            ->assertSee('You have been unsubscribed');

        $this->assertModelMissing($subscription);
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

    public function test_subscription_confirmation_uses_the_newest_welcome_template_when_none_is_default(): void
    {
        Mail::fake();

        MailTemplate::create([
            'name' => 'Old welcome',
            'type' => MailTemplate::TYPE_SUBSCRIPTION,
            'subject' => 'Old welcome',
            'body' => '<p>Old welcome</p>',
        ]);
        $template = MailTemplate::create([
            'name' => 'New welcome',
            'type' => MailTemplate::TYPE_SUBSCRIPTION,
            'subject' => 'New welcome [[email]]',
            'body' => '<p>New welcome, [[email]]!</p>',
        ]);

        $this->postJson('/api/subscribe', [
            'email' => 'subscriber@example.com',
        ])->assertCreated();

        Mail::assertSent(
            NewsletterSubscriptionConfirmationMail::class,
            fn ($mail) => $mail->template->is($template)
                && $mail->envelope()->subject === 'New welcome subscriber@example.com'
                && str_contains($mail->render(), '<p>New welcome, subscriber@example.com!</p>')
        );
    }

    public function test_subscription_confirmation_supports_existing_newsletter_templates(): void
    {
        Mail::fake();

        $template = MailTemplate::create([
            'name' => 'Subscribe',
            'type' => MailTemplate::TYPE_NEWSLETTER,
            'subject' => 'Subscription confirmed',
            'body' => '<p>Thanks for subscribing, [[email]]!</p>',
        ]);

        $this->postJson('/api/subscribe', [
            'email' => 'subscriber@example.com',
        ])->assertCreated();

        Mail::assertSent(
            NewsletterSubscriptionConfirmationMail::class,
            fn ($mail) => $mail->template->is($template)
                && str_contains($mail->render(), '<p>Thanks for subscribing, subscriber@example.com!</p>')
        );
    }
}
