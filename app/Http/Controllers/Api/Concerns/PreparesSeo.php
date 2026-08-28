<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Database\Eloquent\Model;

trait PreparesSeo
{
    protected function prepareSeo(Model $model): void
    {
        if (! $model->relationLoaded('seo') || ! $model->seo) {
            return;
        }

        if ($model->seo->og_image) {
            $model->seo->og_image = asset('/storage/' . $model->seo->og_image);
        }

        $model->seo->append('robots');
    }
}
