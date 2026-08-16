<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailTemplate extends Model
{
    public const TYPE_NEWSLETTER = 'newsletter';
    public const TYPE_SUBSCRIPTION = 'subscription';
    public const TYPE_AUDIT = 'audit';

    protected $fillable = ['name', 'type', 'subject', 'body', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (self $template): void {
            if ($template->is_default) {
                static::query()->where('type', $template->type)
                    ->when($template->exists, fn ($query) => $query->whereKeyNot($template->getKey()))
                    ->update(['is_default' => false]);
            }
        });
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(NewsletterSubscription::class);
    }

    public static function newsletterDefault(): ?self
    {
        return static::query()->where('type', self::TYPE_NEWSLETTER)->where('is_default', true)->first();
    }

    public static function auditDefault(): ?self
    {
        return static::query()->where('type', self::TYPE_AUDIT)->where('is_default', true)->first();
    }

    public static function subscriptionDefault(): ?self
    {
        return static::query()->where('type', self::TYPE_SUBSCRIPTION)->where('is_default', true)->first();
    }
}
