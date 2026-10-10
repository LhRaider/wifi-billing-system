<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Add Minute-Based Package Duration
        |--------------------------------------------------------------------------
        |
        | Package duration is stored in minutes so the system can represent
        | durations such as 30 minutes, 90 minutes, 2 hours 30 minutes, and
        | longer packages without using fractional hours.
        |
        */

        if (!Schema::hasColumn('packages', 'duration_minutes')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->unsignedInteger('duration_minutes')
                    ->nullable()
                    ->after('duration_hours');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Convert Existing Durations
        |--------------------------------------------------------------------------
        |
        | Existing hour-based package durations are converted to minutes.
        | Existing values are preserved rather than replaced with defaults.
        |
        */

        if (
            Schema::hasColumn('packages', 'duration_hours')
            && Schema::hasColumn('packages', 'duration_minutes')
        ) {
            DB::table('packages')
                ->whereNull('duration_minutes')
                ->whereNotNull('duration_hours')
                ->update([
                    'duration_minutes' => DB::raw('duration_hours * 60'),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('packages', 'duration_minutes')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('duration_minutes');
            });
        }
    }
};