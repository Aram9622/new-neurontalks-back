<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NewsletterSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('newsletter_subscriptions', 'email'),
            ],
        ]);

        $subscription = NewsletterSubscription::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'You have successfully subscribed to the newsletter.',
            'data' => $subscription,
        ], 201);
    }
}
