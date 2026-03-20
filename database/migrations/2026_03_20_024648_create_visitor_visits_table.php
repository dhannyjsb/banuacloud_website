<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_visits', function (Blueprint $table): void {
            $table->id();
            $table->string('visitor_token', 80)->index();
            $table->string('path', 500)->index();
            $table->string('route_name', 120)->nullable()->index();
            $table->string('page_title')->nullable();
            $table->text('referrer_url')->nullable();
            $table->string('referrer_host')->nullable()->index();
            $table->string('source', 120)->default('Direct')->index();
            $table->string('medium', 120)->nullable()->index();
            $table->string('utm_campaign', 120)->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('country_code', 8)->nullable()->index();
            $table->string('country_name', 120)->nullable()->index();
            $table->string('city_name', 120)->nullable()->index();
            $table->timestamp('visited_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_visits');
    }
};
