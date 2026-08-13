<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsletterSubscriptionRequest;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;

class NewsletterSubscriptionController extends Controller
{
    public function store(StoreNewsletterSubscriptionRequest $request): JsonResponse
    {
        $subscription = NewsletterSubscription::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'You have successfully subscribed to the newsletter.',
            'data' => $subscription,
        ], 201);
    }
}
