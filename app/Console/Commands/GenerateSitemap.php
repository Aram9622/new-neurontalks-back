<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate {--path= : Output path (defaults to public/sitemap.xml)}';

    protected $description = 'Generate the public XML sitemap';

    public function handle(SitemapService $sitemap, Filesystem $files): int
    {
        $path = $this->option('path') ?: public_path('sitemap.xml');

        $files->ensureDirectoryExists(dirname($path));
        $files->put($path, $sitemap->xml());

        $this->info("Sitemap generated: {$path}");

        return self::SUCCESS;
    }
}
