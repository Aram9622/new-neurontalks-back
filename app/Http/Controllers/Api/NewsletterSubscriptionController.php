<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsletterSubscriptionRequest;
use App\Mail\NewsletterSubscriptionConfirmationMail;
use App\Models\MailTemplate;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterSubscriptionController extends Controller
{
    public function store(StoreNewsletterSubscriptionRequest $request): JsonResponse
    {
        $subscription = NewsletterSubscription::create($request->validated());

        try {
            Mail::to($subscription->email)->send(
                new NewsletterSubscriptionConfirmationMail(
                    $subscription,
                    MailTemplate::subscriptionDefault(),
                )
            );
        } catch (\Throwable $exception) {
            Log::warning('Unable to send newsletter subscription confirmation email.', [
                'subscription_id' => $subscription->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'You have successfully subscribed to the newsletter.',
            'data' => $subscription,
        ], 201);
    }
}
