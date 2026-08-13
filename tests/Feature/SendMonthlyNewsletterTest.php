<?php

namespace Tests\Feature;

use App\Mail\MonthlyNewsletterMail;
use App\Models\Blog;
use App\Models\NewsletterDelivery;
use App\Models\NewsletterSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class SendMonthlyNewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_rerunning_a_month_does_not_resend_completed_deliveries(): void
    {
        Mail::fake();
        NewsletterSubscription::create(['email' => 'reader@example.com']);
        $this->createPost();

        $this->artisan('newsletter:send', ['--month' => '2026-07'])->assertSuccessful();
        $this->artisan('newsletter:send', ['--month' => '2026-07'])->assertSuccessful();

        Mail::assertSent(MonthlyNewsletterMail::class, 1);
        $this->assertDatabaseCount('newsletter_deliveries', 1);
        $this->assertDatabaseHas('newsletter_deliveries', ['status' => 'sent']);
    }

    public function test_rerun_retries_a_failed_delivery_without_resending_an_earlier_success(): void
    {
        Mail::fake();
        $sentSubscription = NewsletterSubscription::create(['email' => 'sent@example.com']);
        $failedSubscription = NewsletterSubscription::create(['email' => 'retry@example.com']);
        $this->createPost();

        NewsletterDelivery::create([
            'newsletter_subscription_id' => $sentSubscription->id,
            'month' => '2026-07-01',
            'status' => 'sent',
            'claim_token' => (string) Str::uuid(),
            'sent_at' => now(),
        ]);
        NewsletterDelivery::create([
            'newsletter_subscription_id' => $failedSubscription->id,
            'month' => '2026-07-01',
            'status' => 'failed',
            'claim_token' => (string) Str::uuid(),
            'failed_at' => now(),
        ]);

        $this->artisan('newsletter:send', ['--month' => '2026-07'])->assertSuccessful();

        Mail::assertSent(MonthlyNewsletterMail::class, 1);
        Mail::assertSent(MonthlyNewsletterMail::class, fn (MonthlyNewsletterMail $mail) => $mail->hasTo('retry@example.com'));
        Mail::assertNotSent(MonthlyNewsletterMail::class, fn (MonthlyNewsletterMail $mail) => $mail->hasTo('sent@example.com'));
        $this->assertDatabaseHas('newsletter_deliveries', [
            'newsletter_subscription_id' => $failedSubscription->id,
            'status' => 'sent',
        ]);
    }

    private function createPost(): void
    {
        Blog::create([
            'title' => 'July news',
            'slug' => 'july-news',
            'type' => 'news',
            'content' => 'Latest news',
            'created_at' => '2026-07-15 12:00:00',
            'updated_at' => '2026-07-15 12:00:00',
        ]);
    }
}
