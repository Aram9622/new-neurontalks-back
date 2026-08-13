<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterDelivery extends Model
{
    protected $fillable = [
        'newsletter_subscription_id',
        'month',
        'status',
        'claim_token',
        'sent_at',
        'failed_at',
    ];

    protected $casts = [
        'month' => 'date',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
}
