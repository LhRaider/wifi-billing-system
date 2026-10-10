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
        if (!Schema::hasTable('subscriptions')) {
            return;
        }

        /*
         * Make sure no customer already has multiple
         * active subscriptions.
         */
        $duplicateActiveSubscriptions = DB::table('subscriptions')
            ->select(
                'user_id',
                DB::raw('COUNT(*) AS total')
            )
            ->where('status', 'active')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicateActiveSubscriptions->isNotEmpty()) {
            throw new \RuntimeException(
                'Cannot harden subscriptions because some customers have '
                .'multiple active subscriptions. Customer user IDs: '
                .$duplicateActiveSubscriptions
                    ->pluck('user_id')
                    ->implode(', ')
            );
        }

        /*
         * Every subscription must end after it starts.
         */
        $invalidPeriods = DB::table('subscriptions')
            ->whereColumn('end_time', '<=', 'start_time')
            ->pluck('id');

        if ($invalidPeriods->isNotEmpty()) {
            throw new \RuntimeException(
                'Cannot harden subscriptions because invalid subscription '
                .'periods exist. Subscription IDs: '
                .$invalidPeriods->implode(', ')
            );
        }

        /*
         * Add the helper column in a database-independent way.
         *
         * active_user_id contains the customer ID only while a
         * subscription is active.
         */
        if (!Schema::hasColumn('subscriptions', 'active_user_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('active_user_id')
                    ->nullable();
            });
        }

        /*
         * Populate existing subscriptions.
         */
        DB::table('subscriptions')->update([
            'active_user_id' => DB::raw(
                "CASE WHEN status = 'active' THEN user_id ELSE NULL END"
            ),
        ]);

        /*
         * MySQL and SQLite both permit multiple NULL values in a
         * unique index. This permits historical inactive rows while
         * allowing only one active subscription per customer.
         */
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unique(
                'active_user_id',
                'subscriptions_active_user_unique'
            );

            $table->index(
                ['status', 'end_time'],
                'subscriptions_status_end_time_index'
            );
        });

        /*
         * Database-level period and active-user synchronization rules.
         *
         * MySQL and SQLite use different trigger/check syntax, so each
         * database receives an equivalent implementation.
         */
        if (DB::getDriverName() === 'sqlite') {
            $this->createSqliteIntegrityTriggers();

            return;
        }

        $this->createMysqlIntegrityRules();
    }

    /**
     * Create MySQL integrity rules.
     */
    private function createMysqlIntegrityRules(): void
    {
        DB::statement("
            ALTER TABLE subscriptions
            ADD CONSTRAINT subscriptions_valid_period_check
            CHECK (end_time > start_time)
        ");

        DB::unprepared("
            CREATE TRIGGER subscriptions_set_active_user_before_insert
            BEFORE INSERT ON subscriptions
            FOR EACH ROW
            SET NEW.active_user_id =
                CASE
                    WHEN NEW.status = 'active' THEN NEW.user_id
                    ELSE NULL
                END
        ");

        DB::unprepared("
            CREATE TRIGGER subscriptions_set_active_user_before_update
            BEFORE UPDATE ON subscriptions
            FOR EACH ROW
            SET NEW.active_user_id =
                CASE
                    WHEN NEW.status = 'active' THEN NEW.user_id
                    ELSE NULL
                END
        ");
    }

    /**
     * Create equivalent SQLite integrity rules for automated tests.
     */
    private function createSqliteIntegrityTriggers(): void
    {
        /*
         * SQLite cannot add a named CHECK constraint to an existing
         * table in the same way as MySQL. Equivalent BEFORE triggers
         * reject invalid subscription periods.
         */
        DB::unprepared("
            CREATE TRIGGER subscriptions_valid_period_before_insert
            BEFORE INSERT ON subscriptions
            FOR EACH ROW
            WHEN NEW.end_time <= NEW.start_time
            BEGIN
                SELECT RAISE(
                    ABORT,
                    'Subscription end_time must be after start_time'
                );
            END
        ");

        DB::unprepared("
            CREATE TRIGGER subscriptions_valid_period_before_update
            BEFORE UPDATE OF start_time, end_time ON subscriptions
            FOR EACH ROW
            WHEN NEW.end_time <= NEW.start_time
            BEGIN
                SELECT RAISE(
                    ABORT,
                    'Subscription end_time must be after start_time'
                );
            END
        ");

        /*
         * SQLite does not support MySQL's SET NEW.column syntax.
         * AFTER triggers synchronize active_user_id instead.
         *
         * The unique index still provides the same one-active-
         * subscription-per-customer protection.
         */
        DB::unprepared("
            CREATE TRIGGER subscriptions_set_active_user_after_insert
            AFTER INSERT ON subscriptions
            FOR EACH ROW
            BEGIN
                UPDATE subscriptions
                SET active_user_id =
                    CASE
                        WHEN NEW.status = 'active' THEN NEW.user_id
                        ELSE NULL
                    END
                WHERE id = NEW.id;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER subscriptions_set_active_user_after_update
            AFTER UPDATE OF status, user_id ON subscriptions
            FOR EACH ROW
            WHEN
                NEW.active_user_id IS NOT
                CASE
                    WHEN NEW.status = 'active' THEN NEW.user_id
                    ELSE NULL
                END
            BEGIN
                UPDATE subscriptions
                SET active_user_id =
                    CASE
                        WHEN NEW.status = 'active' THEN NEW.user_id
                        ELSE NULL
                    END
                WHERE id = NEW.id;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('subscriptions')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::unprepared("
                DROP TRIGGER IF EXISTS
                subscriptions_set_active_user_after_update
            ");

            DB::unprepared("
                DROP TRIGGER IF EXISTS
                subscriptions_set_active_user_after_insert
            ");

            DB::unprepared("
                DROP TRIGGER IF EXISTS
                subscriptions_valid_period_before_update
            ");

            DB::unprepared("
                DROP TRIGGER IF EXISTS
                subscriptions_valid_period_before_insert
            ");
        } else {
            DB::unprepared("
                DROP TRIGGER IF EXISTS
                subscriptions_set_active_user_before_update
            ");

            DB::unprepared("
                DROP TRIGGER IF EXISTS
                subscriptions_set_active_user_before_insert
            ");

            DB::statement("
                ALTER TABLE subscriptions
                DROP CHECK subscriptions_valid_period_check
            ");
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_status_end_time_index');
            $table->dropUnique('subscriptions_active_user_unique');
        });

        if (Schema::hasColumn('subscriptions', 'active_user_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn('active_user_id');
            });
        }
    }
};