<?php

namespace App\Console\Commands;

use App\Mail\MonthlyNewsletterMail;
use App\Models\Blog;
use App\Models\NewsletterSubscription;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

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
        NewsletterSubscription::query()->orderBy('id')->chunkById(100, function ($subscriptions) use ($posts, $month, &$sent) {
            foreach ($subscriptions as $subscription) {
                // Send immediately: the deployment does not run a queue worker.
                Mail::to($subscription->email)->send(
                    new MonthlyNewsletterMail($posts, $month->format('F Y'))
                );
                $subscription->update(['last_sent_at' => now()]);
                $sent++;
            }
        });

        $this->info("Newsletter sent to {$sent} subscriber(s).");

        return self::SUCCESS;
    }
}
