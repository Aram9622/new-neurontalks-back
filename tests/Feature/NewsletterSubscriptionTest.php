<?php

namespace Tests\Feature;

use App\Models\NewsletterSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_subscription_returns_a_clean_json_response_without_an_accept_header(): void
    {
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
    }
}
