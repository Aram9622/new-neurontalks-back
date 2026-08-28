<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_card')->default('summary_large_image');
            $table->string('schema_type')->nullable();
            $table->json('schema_data')->nullable();
            $table->boolean('include_in_sitemap')->default(true);
            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id']);
        });

        $now = now();
        foreach ([
            'blogs' => App\Models\Blog::class,
            'projects' => App\Models\Project::class,
            'services' => App\Models\Service::class,
            'conferences' => App\Models\Conference::class,
            'executions' => App\Models\Execution::class,
        ] as $table => $modelClass) {
            DB::table($table)
                ->select('id')
                ->orderBy('id')
                ->chunkById(500, function ($records) use ($modelClass, $now) {
                    DB::table('seo_metadata')->insert(
                        $records->map(fn ($record): array => [
                            'seoable_type' => $modelClass,
                            'seoable_id' => $record->id,
                            'robots_index' => true,
                            'robots_follow' => true,
                            'twitter_card' => 'summary_large_image',
                            'include_in_sitemap' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])->all()
                    );
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_metadata');
    }
};
