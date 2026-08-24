<?php

namespace Tests\Feature;

use App\Mail\MonthlyNewsletterMail;
use App\Models\Blog;
use App\Models\MailTemplate;
use App\Models\NewsletterSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class SendMonthlyNewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_the_template_attached_to_a_subscriber_and_records_it(): void
    {
        $template = MailTemplate::create([
            'name' => 'July subscribers',
            'type' => MailTemplate::TYPE_NEWSLETTER,
            'subject' => 'News for [[month]]',
            'body' => '<h1>Custom [[month]]</h1><p>Custom newsletter content</p>',
        ]);
        $this->createNewsletterData(['one@example.com']);
        NewsletterSubscription::query()->update(['mail_template_id' => $template->id]);
        Mail::fake();

        $this->assertSame(0, Artisan::call('newsletter:send', ['--month' => '2026-07']));

        Mail::assertSent(MonthlyNewsletterMail::class, function (MonthlyNewsletterMail $mail) use ($template) {
            return $mail->template->is($template)
                && $mail->envelope()->subject === 'News for July 2026'
                && str_contains($mail->render(), '<h1>Custom July 2026</h1>')
                && str_contains($mail->render(), 'Custom newsletter content')
                && str_contains($mail->render(), 'Unsubscribe');
        });
        $this->assertDatabaseHas('monthly_newsletter_deliveries', ['mail_template_id' => $template->id]);
    }

    public function test_rerunning_the_command_does_not_resend_completed_deliveries(): void
    {
        $this->createNewsletterData(['one@example.com', 'two@example.com']);
        Mail::fake();

        $this->assertSame(0, Artisan::call('newsletter:send', ['--month' => '2026-07']));
        $this->assertSame(0, Artisan::call('newsletter:send', ['--month' => '2026-07']));

        Mail::assertSentCount(2);
        $this->assertSame(2, DB::table('monthly_newsletter_deliveries')->whereNotNull('completed_at')->count());
    }

    public function test_a_failed_send_is_retried_without_resending_earlier_deliveries(): void
    {
        $this->createNewsletterData(['one@example.com', 'two@example.com', 'three@example.com']);
        $attempted = [];
        $this->mockMailSending($attempted, 'two@example.com');

        try {
            Artisan::call('newsletter:send', ['--month' => '2026-07']);
            $this->fail('The mail failure was not propagated.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Mail transport failed.', $exception->getMessage());
        }

        $this->assertSame(['one@example.com', 'two@example.com'], $attempted);
        $this->assertSame(1, DB::table('monthly_newsletter_deliveries')->whereNotNull('completed_at')->count());

        $retried = [];
        $this->mockMailSending($retried);
        $this->assertSame(0, Artisan::call('newsletter:send', ['--month' => '2026-07']));

        $this->assertSame(['two@example.com', 'three@example.com'], $retried);
        $this->assertSame(3, DB::table('monthly_newsletter_deliveries')->whereNotNull('completed_at')->count());
    }

    private function createNewsletterData(array $emails): void
    {
        Blog::create([
            'title' => 'July update',
            'slug' => 'july-update',
            'created_at' => '2026-07-15 12:00:00',
            'updated_at' => '2026-07-15 12:00:00',
        ]);

        foreach ($emails as $email) {
            NewsletterSubscription::create(['email' => $email]);
        }
    }

    private function mockMailSending(array &$attempted, ?string $failFor = null): void
    {
        Mail::shouldReceive('to')->andReturnUsing(function (string $email) use (&$attempted, $failFor) {
            return new class($email, $attempted, $failFor)
            {
                public function __construct(
                    private string $email,
                    private mixed &$attempted,
                    private ?string $failFor,
                ) {
                }

                public function send(): void
                {
                    $this->attempted[] = $this->email;

                    if ($this->email === $this->failFor) {
                        throw new RuntimeException('Mail transport failed.');
                    }
                }
            };
        });
    }
}
