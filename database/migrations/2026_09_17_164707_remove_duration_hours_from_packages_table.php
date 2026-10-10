<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('packages', 'duration_minutes')) {
            throw new \RuntimeException(
                'Cannot remove duration_hours because duration_minutes does not exist.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Unlimited Packages
        |--------------------------------------------------------------------------
        |
        | Unlimited packages do not have a finite service duration.
        |
        */

        DB::table('packages')
            ->where('is_unlimited', true)
            ->update([
                'duration_minutes' => null,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Backfill Legacy Finite Packages
        |--------------------------------------------------------------------------
        |
        | Any finite legacy package that still has no minute value is converted
        | from duration_hours before the legacy column is removed.
        |
        */

        if (Schema::hasColumn('packages', 'duration_hours')) {
            DB::table('packages')
                ->where('is_unlimited', false)
                ->whereNull('duration_minutes')
                ->whereNotNull('duration_hours')
                ->where('duration_hours', '>', 0)
                ->update([
                    'duration_minutes' => DB::raw(
                        'duration_hours * 60'
                    ),
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Finite Package Durations
        |--------------------------------------------------------------------------
        |
        | Never remove the legacy column if a finite package would be left
        | without a valid positive duration in minutes.
        |
        */

        $invalidFinitePackageExists = DB::table('packages')
            ->where('is_unlimited', false)
            ->where(function ($query) {
                $query
                    ->whereNull('duration_minutes')
                    ->orWhere('duration_minutes', '<', 1);
            })
            ->exists();

        if ($invalidFinitePackageExists) {
            throw new \RuntimeException(
                'Cannot remove duration_hours: one or more finite packages do not have a valid duration_minutes value.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Legacy Duration Column
        |--------------------------------------------------------------------------
        */

        if (Schema::hasColumn('packages', 'duration_hours')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('duration_hours');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('packages', 'duration_hours')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->integer('duration_hours')
                    ->nullable()
                    ->after('price');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Compatibility Rollback
        |--------------------------------------------------------------------------
        |
        | The old hours-only field cannot represent minute precision exactly.
        | A partial hour is rounded upward during rollback.
        |
        */

        DB::table('packages')
            ->where('is_unlimited', false)
            ->whereNotNull('duration_minutes')
            ->update([
                'duration_hours' => DB::raw(
                    'CEILING(duration_minutes / 60)'
                ),
            ]);
    }
};