<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'mail_template_id',
        'last_sent_at',
    ];

    protected $casts = [
        'last_sent_at' => 'datetime',
    ];

    public function mailTemplate(): BelongsTo
    {
        return $this->belongsTo(MailTemplate::class);
    }

    public function latestDelivery(): HasOne
    {
        return $this->hasOne(MonthlyNewsletterDelivery::class)->latestOfMany('completed_at');
    }
}
