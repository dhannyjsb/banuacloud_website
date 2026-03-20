<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('max_mind_database_syncs', function (Blueprint $table): void {
            $table->id();
            $table->string('edition_id', 80)->unique();
            $table->string('database_path');
            $table->string('status', 40)->default('pending')->index();
            $table->boolean('file_exists')->default(false);
            $table->text('message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('checked_at')->nullable()->index();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('max_mind_database_syncs');
    }
};
