<?php

namespace App\Console\Commands;

use App\Mail\MonthlyNewsletterMail;
use App\Models\Blog;
use App\Models\NewsletterDelivery;
use App\Models\NewsletterSubscription;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SendMonthlyNewsletter extends Command
{
    protected $signature = 'newsletter:send {--month= : Month to send in YYYY-MM format}';

    protected $description = 'Send the monthly blogs and news newsletter to all subscribers';

    public function handle(): int
    {
        try {
            $month = $this->option('month')
                ? CarbonImmutable::createFromFormat('!Y-m', $this->option('month'))
                : CarbonImmutable::now()->subMonth()->startOfMonth();
        } catch (\Throwable) {
            $this->error('The --month option must use YYYY-MM format.');

            return self::FAILURE;
        }

        $posts = Blog::query()
            ->whereBetween('created_at', [$month->startOfMonth(), $month->endOfMonth()])
            ->orderBy('created_at')
            ->get();

        if ($posts->isEmpty()) {
            $this->info("No blogs or news found for {$month->format('F Y')}.");

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;
        $deliveryMonth = $month->startOfMonth()->toDateString();

        NewsletterSubscription::query()->orderBy('id')->chunkById(100, function ($subscriptions) use ($posts, $month, $deliveryMonth, &$sent, &$failed) {
            foreach ($subscriptions as $subscription) {
                $claimToken = (string) Str::uuid();
                $created = NewsletterDelivery::query()->insertOrIgnore([
                    'newsletter_subscription_id' => $subscription->id,
                    'month' => $deliveryMonth,
                    'status' => 'processing',
                    'claim_token' => $claimToken,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (! $created) {
                    $claimed = NewsletterDelivery::query()
                        ->where('newsletter_subscription_id', $subscription->id)
                        ->where('month', $deliveryMonth)
                        ->where('status', 'failed')
                        ->update([
                            'status' => 'processing',
                            'claim_token' => $claimToken,
                            'failed_at' => null,
                            'updated_at' => now(),
                        ]);

                    if (! $claimed) {
                        continue;
                    }
                }

                try {
                    Mail::to($subscription->email)->send(
                        new MonthlyNewsletterMail($posts, $month->format('F Y'))
                    );

                    NewsletterDelivery::query()->where('claim_token', $claimToken)->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $subscription->update(['last_sent_at' => now()]);
                    $sent++;
                } catch (Throwable $exception) {
                    NewsletterDelivery::query()->where('claim_token', $claimToken)->update([
                        'status' => 'failed',
                        'failed_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->error("Failed to send newsletter to {$subscription->email}: {$exception->getMessage()}");
                    $failed++;
                }
            }
        });

        $this->info("Newsletter sent to {$sent} subscriber(s).");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
