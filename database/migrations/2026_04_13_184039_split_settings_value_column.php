<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('text_value')->nullable();
            $table->string('image_value')->nullable();
            $table->dropColumn('value'); // Удаляем старую проблемную колонку
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('value')->nullable();
            $table->dropColumn(['text_value', 'image_value']);
        });
    }
};
