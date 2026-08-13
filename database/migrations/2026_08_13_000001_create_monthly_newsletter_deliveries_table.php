<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_newsletter_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_subscription_id')->constrained()->cascadeOnDelete();
            $table->date('newsletter_month');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['newsletter_subscription_id', 'newsletter_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_newsletter_deliveries');
    }
};
