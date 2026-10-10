<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const CONSTRAINT_NAME = 'packages_duration_integrity_check';

    private const SQLITE_INSERT_TRIGGER =
        'packages_duration_integrity_before_insert';

    private const SQLITE_UPDATE_TRIGGER =
        'packages_duration_integrity_before_update';

    public function up(): void
    {
        if (
            !Schema::hasTable('packages')
            || !Schema::hasColumn('packages', 'duration_minutes')
            || !Schema::hasColumn('packages', 'is_unlimited')
        ) {
            throw new \RuntimeException(
                'Cannot add package duration constraint because the required package columns do not exist.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Existing Data
        |--------------------------------------------------------------------------
        |
        | At this historical point in the migration chain:
        |
        | - finite packages require duration_minutes >= 1
        | - unlimited-duration packages require duration_minutes = NULL
        |
        | A later migration changes "unlimited" to mean unlimited speed.
        |
        */

        $invalidPackageExists = DB::table('packages')
            ->where(function ($query) {
                $query
                    ->where(function ($finite) {
                        $finite
                            ->where('is_unlimited', false)
                            ->where(function ($duration) {
                                $duration
                                    ->whereNull('duration_minutes')
                                    ->orWhere('duration_minutes', '<', 1);
                            });
                    })
                    ->orWhere(function ($unlimited) {
                        $unlimited
                            ->where('is_unlimited', true)
                            ->whereNotNull('duration_minutes');
                    });
            })
            ->exists();

        if ($invalidPackageExists) {
            throw new \RuntimeException(
                'Cannot add package duration constraint because one or more packages contain an invalid duration configuration.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Database Duration Integrity
        |--------------------------------------------------------------------------
        */

        if (DB::getDriverName() === 'sqlite') {
            $this->createSqliteIntegrityTriggers();

            return;
        }

        $this->createMysqlIntegrityConstraint();
    }

    /**
     * Create the MySQL CHECK constraint.
     */
    private function createMysqlIntegrityConstraint(): void
    {
        DB::statement(
            'ALTER TABLE packages
             ADD CONSTRAINT '.self::CONSTRAINT_NAME.'
             CHECK (
                 (
                     is_unlimited = 0
                     AND duration_minutes IS NOT NULL
                     AND duration_minutes >= 1
                 )
                 OR
                 (
                     is_unlimited = 1
                     AND duration_minutes IS NULL
                 )
             )'
        );
    }

    /**
     * Create equivalent SQLite integrity protection.
     */
    private function createSqliteIntegrityTriggers(): void
    {
        DB::unprepared(
            'DROP TRIGGER IF EXISTS '.self::SQLITE_INSERT_TRIGGER
        );

        DB::unprepared(
            'DROP TRIGGER IF EXISTS '.self::SQLITE_UPDATE_TRIGGER
        );

        DB::unprepared(
            'CREATE TRIGGER '.self::SQLITE_INSERT_TRIGGER.'
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
            'CREATE TRIGGER '.self::SQLITE_UPDATE_TRIGGER.'
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

    public function down(): void
    {
        if (!Schema::hasTable('packages')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared(
                'DROP TRIGGER IF EXISTS '.self::SQLITE_INSERT_TRIGGER
            );

            DB::unprepared(
                'DROP TRIGGER IF EXISTS '.self::SQLITE_UPDATE_TRIGGER
            );

            return;
        }

        DB::statement(
            'ALTER TABLE packages
             DROP CHECK '.self::CONSTRAINT_NAME
        );
    }
};