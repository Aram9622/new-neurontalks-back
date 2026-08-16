<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailTemplate extends Model
{
    public const TYPE_NEWSLETTER = 'newsletter';
    public const TYPE_SUBSCRIPTION = 'subscription';
    public const TYPE_AUDIT = 'audit';
    public const TYPE_CONTACT_NOTIFICATION = 'contact_notification';
    public const TYPE_CONTACT_REPLY = 'contact_reply';

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
        return static::preferredFor(self::TYPE_NEWSLETTER);
    }

    public static function auditDefault(): ?self
    {
        return static::preferredFor(self::TYPE_AUDIT);
    }

    public static function subscriptionDefault(): ?self
    {
        $subscriptionTemplate = static::preferredFor(self::TYPE_SUBSCRIPTION);

        if ($subscriptionTemplate) {
            return $subscriptionTemplate;
        }

        // Templates created before the subscription-confirmation type was added
        // were saved as newsletters. Keep those working for existing admins.
        return static::preferredFor(self::TYPE_NEWSLETTER);
    }

    public static function preferredFor(string $type): ?self
    {
        return static::query()
            ->where('type', $type)
            ->orderByDesc('is_default')
            ->latest('id')
            ->first();
    }
}
