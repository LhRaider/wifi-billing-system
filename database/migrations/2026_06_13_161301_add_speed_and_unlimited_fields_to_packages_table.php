<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('download_speed')->nullable()->after('speed');
            $table->string('upload_speed')->nullable()->after('download_speed');
            $table->boolean('is_unlimited')->default(false)->after('duration_hours');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['download_speed', 'upload_speed', 'is_unlimited']);
        });
    }
};