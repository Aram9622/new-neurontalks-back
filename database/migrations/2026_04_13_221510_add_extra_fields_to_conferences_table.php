<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title');
            $table->string('subtitle')->nullable()->after('slug');
            $table->string('button_title')->nullable();
            $table->string('button_link')->nullable();
            $table->string('main_image')->nullable();
            $table->string('video_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('conferences', function (Blueprint $table) {
            $table->dropColumn(['slug', 'subtitle', 'button_title', 'button_link', 'main_image', 'video_url']);
        });
    }
};
