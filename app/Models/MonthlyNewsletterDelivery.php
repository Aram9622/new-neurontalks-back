<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyNewsletterDelivery extends Model
{
    protected $fillable = [
        'newsletter_subscription_id',
        'newsletter_month',
        'mail_template_id',
        'completed_at',
    ];

    protected $casts = [
        'newsletter_month' => 'date',
        'completed_at' => 'datetime',
    ];

    public function mailTemplate(): BelongsTo
    {
        return $this->belongsTo(MailTemplate::class);
    }
}
