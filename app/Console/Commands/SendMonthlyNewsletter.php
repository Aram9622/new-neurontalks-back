<?php

namespace App\Console\Commands;

use App\Mail\MonthlyNewsletterMail;
use App\Models\Blog;
use App\Models\MailTemplate;
use App\Models\NewsletterSubscription;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
        $defaultTemplate = MailTemplate::newsletterDefault();
        NewsletterSubscription::query()->with('mailTemplate')->orderBy('id')->chunkById(100, function ($subscriptions) use ($posts, $month, $defaultTemplate, &$sent) {
            foreach ($subscriptions as $subscription) {
                $template = $subscription->mailTemplate ?? $defaultTemplate;
                $delivery = [
                    'newsletter_subscription_id' => $subscription->id,
                    'newsletter_month' => $month->toDateString(),
                ];
                $claimed = DB::table('monthly_newsletter_deliveries')->insertOrIgnore([
                    ...$delivery,
                    'mail_template_id' => $template?->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($claimed === 0) {
                    continue;
                }

                // Send immediately: the deployment does not run a queue worker.
                try {
                    Mail::to($subscription->email)->send(
                        new MonthlyNewsletterMail($posts, $month->format('F Y'), $template)
                    );
                } catch (\Throwable $exception) {
                    // Release only this failed claim so a later command run can retry it.
                    DB::table('monthly_newsletter_deliveries')->where($delivery)->delete();

                    throw $exception;
                }

                DB::table('monthly_newsletter_deliveries')
                    ->where($delivery)
                    ->update(['completed_at' => now(), 'updated_at' => now()]);
                $subscription->update(['last_sent_at' => now()]);
                $sent++;
            }
        });

        $this->info("Newsletter sent to {$sent} subscriber(s).");

        return self::SUCCESS;
    }
}
