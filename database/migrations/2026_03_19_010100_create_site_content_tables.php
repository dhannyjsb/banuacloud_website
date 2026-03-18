<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_contents', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('subtitle', 500);
            $table->string('cta_primary', 100);
            $table->string('cta_secondary', 100);
            $table->timestamps();
        });

        Schema::create('feature_items', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('description', 500);
            $table->string('icon', 100);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('company');
            $table->text('content');
            $table->string('avatar')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('feature_items');
        Schema::dropIfExists('hero_contents');
    }
};
