<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    protected $table = 'seo_metadata';

    protected $guarded = [];

    protected $casts = [
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
        'schema_data' => 'array',
        'include_in_sitemap' => 'boolean',
    ];

    protected $hidden = [
        'seoable_type',
        'seoable_id',
    ];

    protected $appends = [
        'robots',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getRobotsAttribute(): string
    {
        return ($this->robots_index ? 'index' : 'noindex')
            . ','
            . ($this->robots_follow ? 'follow' : 'nofollow');
    }

    public function score(): int
    {
        $checks = [
            filled($this->title),
            filled($this->description),
            filled($this->og_image),
            filled($this->canonical_url),
            filled($this->schema_type),
        ];

        return (int) round(collect($checks)->filter()->count() / count($checks) * 100);
    }
}
