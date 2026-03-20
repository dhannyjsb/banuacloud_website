<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitor_visits', function (Blueprint $table): void {
            $table->string('isp_name', 160)->nullable()->after('city_name')->index();
            $table->string('organization_name', 160)->nullable()->after('isp_name')->index();
            $table->unsignedInteger('autonomous_system_number')->nullable()->after('organization_name')->index();
            $table->string('autonomous_system_organization', 160)->nullable()->after('autonomous_system_number')->index();
        });
    }

    public function down(): void
    {
        Schema::table('visitor_visits', function (Blueprint $table): void {
            $table->dropColumn([
                'isp_name',
                'organization_name',
                'autonomous_system_number',
                'autonomous_system_organization',
            ]);
        });
    }
};
