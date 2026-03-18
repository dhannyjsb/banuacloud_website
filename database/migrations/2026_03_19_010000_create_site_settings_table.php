<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->boolean('maintenance_mode')->default(false);
            $table->string('site_name');
            $table->string('site_description', 500);
            $table->string('company_name');
            $table->string('company_email');
            $table->string('company_phone', 50);
            $table->string('company_whatsapp', 50);
            $table->string('company_address')->default('Indonesia');
            $table->string('social_instagram')->nullable();
            $table->string('social_linkedin')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_facebook')->nullable();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('order_alerts')->default(true);
            $table->boolean('support_alerts')->default(true);
            $table->boolean('two_factor_enabled')->default(false);
            $table->unsignedSmallInteger('session_timeout')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
