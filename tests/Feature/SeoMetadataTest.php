<?php

namespace Tests\Feature;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_exposes_seo_metadata_through_the_api(): void
    {
        $blog = Blog::factory()->create(['slug' => 'seo-ready-post']);
        $blog->seo()->create([
            'title' => 'Search title',
            'description' => 'Search description',
            'og_image' => 'seo/social-card.jpg',
            'robots_index' => true,
            'robots_follow' => false,
        ]);

        $this->getJson('/api/blogs/seo-ready-post')
            ->assertOk()
            ->assertJsonPath('seo.title', 'Search title')
            ->assertJsonPath('seo.description', 'Search description')
            ->assertJsonPath('seo.robots', 'index,nofollow')
            ->assertJsonPath('seo.og_image', url('/storage/seo/social-card.jpg'));
    }

    public function test_noindex_content_is_excluded_from_the_sitemap(): void
    {
        $indexable = Blog::factory()->create(['slug' => 'indexable-post']);
        $hidden = Blog::factory()->create(['slug' => 'hidden-post']);
        $hidden->seo()->create([
            'robots_index' => false,
            'include_in_sitemap' => true,
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/blog/' . $indexable->slug, false)
            ->assertDontSee('/blog/' . $hidden->slug, false);
    }

    public function test_seo_score_reflects_completed_fields(): void
    {
        $blog = Blog::factory()->create();
        $seo = $blog->seo()->create([
            'title' => 'Search title',
            'description' => 'Search description',
            'og_image' => 'seo/card.jpg',
            'canonical_url' => 'https://neurontalks.am/blog/example',
            'schema_type' => 'Article',
        ]);

        $this->assertSame(100, $seo->score());
    }

    public function test_sitemap_can_be_generated_as_a_static_xml_file(): void
    {
        Blog::factory()->create(['slug' => 'generated-sitemap-post']);
        $path = storage_path('framework/testing/sitemap.xml');

        File::delete($path);

        $this->artisan('sitemap:generate', ['--path' => $path])
            ->assertSuccessful();

        $this->assertFileExists($path);
        $this->assertStringContainsString('/blog/generated-sitemap-post', File::get($path));

        File::delete($path);
    }
}
