<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Conference;
use App\Models\Execution;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class SitemapService
{
    public function urls(): array
    {
        $frontendUrl = rtrim(config('app.frontend_url'), '/');
        $urls = [
            $this->url($frontendUrl . '/', now()->toAtomString(), '1.0'),
            $this->url($frontendUrl . '/projects', now()->toAtomString(), '0.8'),
            $this->url($frontendUrl . '/services', now()->toAtomString(), '0.8'),
            $this->url($frontendUrl . '/blog', now()->toAtomString(), '0.8'),
            $this->url($frontendUrl . '/conferences', now()->toAtomString(), '0.8'),
        ];

        foreach ([
            Blog::class => ['/blog/', '0.7'],
            Project::class => ['/projects/', '0.7'],
            Service::class => ['/services/', '0.7'],
            Conference::class => ['/conferences/', '0.7'],
            Execution::class => ['/executions/', '0.6'],
        ] as $modelClass => [$path, $priority]) {
            $this->items($modelClass)->each(function ($item) use (&$urls, $frontendUrl, $path, $priority) {
                $urls[] = $this->url(
                    $frontendUrl . $path . $item->slug,
                    $item->updated_at->toAtomString(),
                    $priority,
                );
            });
        }

        return $urls;
    }

    public function xml(): string
    {
        return view('sitemap', ['urls' => $this->urls()])->render();
    }

    private function items(string $modelClass): Collection
    {
        return $modelClass::query()
            ->where(function ($query) {
                $query->whereDoesntHave('seo')
                    ->orWhereHas('seo', fn ($seoQuery) => $seoQuery
                        ->where('robots_index', true)
                        ->where('include_in_sitemap', true));
            })
            ->get();
    }

    private function url(string $location, string $lastModified, string $priority): array
    {
        return [
            'loc' => $location,
            'lastmod' => $lastModified,
            'priority' => $priority,
        ];
    }
}
