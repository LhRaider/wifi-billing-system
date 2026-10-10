<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD_DURATION_CONSTRAINT =
        'packages_duration_integrity_check';

    private const NEW_DURATION_CONSTRAINT =
        'packages_duration_minutes_check';

    private const OLD_SQLITE_INSERT_TRIGGER =
        'packages_duration_integrity_before_insert';

    private const OLD_SQLITE_UPDATE_TRIGGER =
        'packages_duration_integrity_before_update';

    private const NEW_SQLITE_INSERT_TRIGGER =
        'packages_duration_minutes_before_insert';

    private const NEW_SQLITE_UPDATE_TRIGGER =
        'packages_duration_minutes_before_update';

    public function up(): void
    {
        if (!Schema::hasTable('packages')) {
            throw new \RuntimeException(
                'The packages table does not exist.'
            );
        }

        if (!Schema::hasColumn('packages', 'duration_minutes')) {
            throw new \RuntimeException(
                'The packages.duration_minutes column does not exist.'
            );
        }

        if (!Schema::hasColumn('packages', 'is_unlimited')) {
            throw new \RuntimeException(
                'The packages.is_unlimited column does not exist.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Legacy Package Data
        |--------------------------------------------------------------------------
        |
        | At this point is_unlimited still means unlimited TIME.
        | Existing unlimited-time packages must never be silently converted
        | into unlimited-speed packages.
        |
        */

        $legacyUnlimitedCount = DB::table('packages')
            ->where('is_unlimited', 1)
            ->count();

        if ($legacyUnlimitedCount > 0) {
            throw new \RuntimeException(
                'Migration stopped because '
                .$legacyUnlimitedCount
                .' package(s) still use the old unlimited-duration setting. '
                .'Every package must first be given a real duration and the '
                .'old unlimited-duration setting must be disabled.'
            );
        }

        $invalidDurationCount = DB::table('packages')
            ->where(function ($query) {
                $query
                    ->whereNull('duration_minutes')
                    ->orWhere('duration_minutes', '<', 1);
            })
            ->count();

        if ($invalidDurationCount > 0) {
            throw new \RuntimeException(
                'Migration stopped because '
                .$invalidDurationCount
                .' package(s) have no valid duration. '
                .'Every package must have a duration of at least 1 minute.'
            );
        }

        $driver = DB::getDriverName();

        /*
        |--------------------------------------------------------------------------
        | Remove Historical Unlimited-Time Integrity Rule
        |--------------------------------------------------------------------------
        */

        if ($driver === 'sqlite') {
            DB::unprepared(
                'DROP TRIGGER IF EXISTS '
                .self::OLD_SQLITE_INSERT_TRIGGER
            );

            DB::unprepared(
                'DROP TRIGGER IF EXISTS '
                .self::OLD_SQLITE_UPDATE_TRIGGER
            );
        } else {
            DB::statement(
                'ALTER TABLE packages '
                .'DROP CHECK '.self::OLD_DURATION_CONSTRAINT
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rename Unlimited Flag
        |--------------------------------------------------------------------------
        |
        | From this point forward, unlimited means unlimited SPEED only.
        |
        */

        Schema::table('packages', function (Blueprint $table) {
            $table->renameColumn(
                'is_unlimited',
                'is_speed_unlimited'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Make Duration Mandatory
        |--------------------------------------------------------------------------
        |
        | Every package must have a finite positive duration.
        |
        */

        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('duration_minutes')
                ->nullable(false)
                ->change();
        });

        /*
        |--------------------------------------------------------------------------
        | Add Final Duration Integrity Protection
        |--------------------------------------------------------------------------
        */

        if ($driver === 'sqlite') {
            $this->createSqliteDurationTriggers();

            return;
        }

        DB::statement(
            'ALTER TABLE packages '
            .'ADD CONSTRAINT '.self::NEW_DURATION_CONSTRAINT
            .' CHECK (duration_minutes >= 1)'
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('packages')) {
            return;
        }

        if (
            !Schema::hasColumn('packages', 'duration_minutes')
            || !Schema::hasColumn('packages', 'is_speed_unlimited')
        ) {
            return;
        }

        $driver = DB::getDriverName();

        /*
        |--------------------------------------------------------------------------
        | Remove Final Duration Integrity Protection
        |--------------------------------------------------------------------------
        */

        if ($driver === 'sqlite') {
            DB::unprepared(
                'DROP TRIGGER IF EXISTS '
                .self::NEW_SQLITE_INSERT_TRIGGER
            );

            DB::unprepared(
                'DROP TRIGGER IF EXISTS '
                .self::NEW_SQLITE_UPDATE_TRIGGER
            );
        } else {
            DB::statement(
                'ALTER TABLE packages '
                .'DROP CHECK '.self::NEW_DURATION_CONSTRAINT
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Restore Historical Nullable Duration
        |--------------------------------------------------------------------------
        */

        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('duration_minutes')
                ->nullable()
                ->change();
        });

        /*
        |--------------------------------------------------------------------------
        | Restore Historical Column Name
        |--------------------------------------------------------------------------
        */

        Schema::table('packages', function (Blueprint $table) {
            $table->renameColumn(
                'is_speed_unlimited',
                'is_unlimited'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Restore Historical Unlimited-Time Integrity Rule
        |--------------------------------------------------------------------------
        */

        if ($driver === 'sqlite') {
            $this->createHistoricalSqliteDurationTriggers();

            return;
        }

        DB::statement(
            'ALTER TABLE packages '
            .'ADD CONSTRAINT '.self::OLD_DURATION_CONSTRAINT
            .' CHECK ('
            .'(is_unlimited = 0 '
            .'AND duration_minutes IS NOT NULL '
            .'AND duration_minutes >= 1) '
            .'OR '
            .'(is_unlimited = 1 '
            .'AND duration_minutes IS NULL)'
            .')'
        );
    }

    /**
     * Enforce the final package rule in SQLite:
     * every package requires duration_minutes >= 1.
     */
    private function createSqliteDurationTriggers(): void
    {
        DB::unprepared(
            'DROP TRIGGER IF EXISTS '
            .self::NEW_SQLITE_INSERT_TRIGGER
        );

        DB::unprepared(
            'DROP TRIGGER IF EXISTS '
            .self::NEW_SQLITE_UPDATE_TRIGGER
        );

        DB::unprepared(
            'CREATE TRIGGER '
            .self::NEW_SQLITE_INSERT_TRIGGER.'
             BEFORE INSERT ON packages
             FOR EACH ROW
             BEGIN
                 SELECT CASE
                     WHEN NEW.duration_minutes IS NULL
                          OR NEW.duration_minutes < 1
                     THEN RAISE(
                         ABORT,
                         \'Package duration must be at least 1 minute.\'
                     )
                 END;
             END'
        );

        DB::unprepared(
            'CREATE TRIGGER '
            .self::NEW_SQLITE_UPDATE_TRIGGER.'
             BEFORE UPDATE ON packages
             FOR EACH ROW
             BEGIN
                 SELECT CASE
                     WHEN NEW.duration_minutes IS NULL
                          OR NEW.duration_minutes < 1
                     THEN RAISE(
                         ABORT,
                         \'Package duration must be at least 1 minute.\'
                     )
                 END;
             END'
        );
    }

    /**
     * Restore the historical unlimited-duration rule during rollback
     * when the migration is running on SQLite.
     */
    private function createHistoricalSqliteDurationTriggers(): void
    {
        DB::unprepared(
            'DROP TRIGGER IF EXISTS '
            .self::OLD_SQLITE_INSERT_TRIGGER
        );

        DB::unprepared(
            'DROP TRIGGER IF EXISTS '
            .self::OLD_SQLITE_UPDATE_TRIGGER
        );

        DB::unprepared(
            'CREATE TRIGGER '
            .self::OLD_SQLITE_INSERT_TRIGGER.'
             BEFORE INSERT ON packages
             FOR EACH ROW
             BEGIN
                 SELECT CASE
                     WHEN NOT (
                         (
                             NEW.is_unlimited = 0
                             AND NEW.duration_minutes IS NOT NULL
                             AND NEW.duration_minutes >= 1
                         )
                         OR
                         (
                             NEW.is_unlimited = 1
                             AND NEW.duration_minutes IS NULL
                         )
                     )
                     THEN RAISE(
                         ABORT,
                         \'Invalid package duration configuration.\'
                     )
                 END;
             END'
        );

        DB::unprepared(
            'CREATE TRIGGER '
            .self::OLD_SQLITE_UPDATE_TRIGGER.'
             BEFORE UPDATE ON packages
             FOR EACH ROW
             BEGIN
                 SELECT CASE
                     WHEN NOT (
                         (
                             NEW.is_unlimited = 0
                             AND NEW.duration_minutes IS NOT NULL
                             AND NEW.duration_minutes >= 1
                         )
                         OR
                         (
                             NEW.is_unlimited = 1
                             AND NEW.duration_minutes IS NULL
                         )
                     )
                     THEN RAISE(
                         ABORT,
                         \'Invalid package duration configuration.\'
                     )
                 END;
             END'
        );
    }
};