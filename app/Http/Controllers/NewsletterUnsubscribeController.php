<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Contracts\View\View;

class NewsletterUnsubscribeController extends Controller
{
    public function __invoke(NewsletterSubscription $subscription): View
    {
        $subscription->delete();

        return view('newsletter-unsubscribed');
    }
}
