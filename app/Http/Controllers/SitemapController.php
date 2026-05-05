<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Project;
use App\Models\Service;
use App\Models\Conference;
use App\Models\Execution;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $frontendUrl = rtrim(env('FRONTEND_URL', 'https://neuron-talk.vercel.app'), '/');

        $urls = [];

        // Статические страницы
        $urls[] = ['loc' => $frontendUrl . '/', 'lastmod' => now()->toAtomString(), 'priority' => '1.0'];
        $urls[] = ['loc' => $frontendUrl . '/projects', 'lastmod' => now()->toAtomString(), 'priority' => '0.8'];
        $urls[] = ['loc' => $frontendUrl . '/services', 'lastmod' => now()->toAtomString(), 'priority' => '0.8'];
        $urls[] = ['loc' => $frontendUrl . '/blog', 'lastmod' => now()->toAtomString(), 'priority' => '0.8'];
        $urls[] = ['loc' => $frontendUrl . '/conferences', 'lastmod' => now()->toAtomString(), 'priority' => '0.8'];

        // Динамические страницы - Блоги
        Blog::all()->each(function ($item) use (&$urls, $frontendUrl) {
            $urls[] = [
                'loc' => $frontendUrl . '/blog/' . $item->slug,
                'lastmod' => $item->updated_at->toAtomString(),
                'priority' => '0.7'
            ];
        });

        // Динамические страницы - Проекты
        Project::all()->each(function ($item) use (&$urls, $frontendUrl) {
            $urls[] = [
                'loc' => $frontendUrl . '/projects/' . $item->slug,
                'lastmod' => $item->updated_at->toAtomString(),
                'priority' => '0.7'
            ];
        });

        // Динамические страницы - Услуги
        Service::all()->each(function ($item) use (&$urls, $frontendUrl) {
            $urls[] = [
                'loc' => $frontendUrl . '/services/' . $item->slug,
                'lastmod' => $item->updated_at->toAtomString(),
                'priority' => '0.7'
            ];
        });

        // Динамические страницы - Конференции
        Conference::all()->each(function ($item) use (&$urls, $frontendUrl) {
            $urls[] = [
                'loc' => $frontendUrl . '/conferences/' . $item->slug,
                'lastmod' => $item->updated_at->toAtomString(),
                'priority' => '0.7'
            ];
        });

        // Динамические страницы - Executions (если есть такие страницы на фронте)
        Execution::all()->each(function ($item) use (&$urls, $frontendUrl) {
            $urls[] = [
                'loc' => $frontendUrl . '/executions/' . $item->slug,
                'lastmod' => $item->updated_at->toAtomString(),
                'priority' => '0.6'
            ];
        });

        return response()->view('sitemap', compact('urls'))->header('Content-Type', 'text/xml');
    }
}
