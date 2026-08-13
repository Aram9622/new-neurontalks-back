<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('newsletter');
            $table->string('subject');
            $table->longText('body');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::table('newsletter_subscriptions', function (Blueprint $table) {
            $table->foreignId('mail_template_id')->nullable()->after('email')
                ->constrained()->nullOnDelete();
        });

        Schema::table('monthly_newsletter_deliveries', function (Blueprint $table) {
            $table->foreignId('mail_template_id')->nullable()->after('newsletter_month')
                ->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('monthly_newsletter_deliveries', fn (Blueprint $table) => $table->dropConstrainedForeignId('mail_template_id'));
        Schema::table('newsletter_subscriptions', fn (Blueprint $table) => $table->dropConstrainedForeignId('mail_template_id'));
        Schema::dropIfExists('mail_templates');
    }
};
