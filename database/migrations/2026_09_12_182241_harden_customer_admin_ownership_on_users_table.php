<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Existing Data
        |--------------------------------------------------------------------------
        */

        $customersWithoutAdmin = DB::table('users')
            ->where('role', 'customer')
            ->whereNull('admin_id')
            ->count();

        if ($customersWithoutAdmin > 0) {
            throw new \RuntimeException(
                "Cannot harden customer ownership because {$customersWithoutAdmin} customer(s) do not have an ISP Admin assigned."
            );
        }

        $privilegedUsersWithAdmin = DB::table('users')
            ->whereIn('role', ['admin', 'super_admin'])
            ->whereNotNull('admin_id')
            ->count();

        if ($privilegedUsersWithAdmin > 0) {
            throw new \RuntimeException(
                "Cannot harden customer ownership because {$privilegedUsersWithAdmin} Admin or Super Admin account(s) incorrectly have an admin_id."
            );
        }

        $customersWithInvalidAdmin = DB::table('users as customers')
            ->leftJoin(
                'users as admins',
                'admins.id',
                '=',
                'customers.admin_id'
            )
            ->where('customers.role', 'customer')
            ->where(function ($query) {
                $query
                    ->whereNull('admins.id')
                    ->orWhere('admins.role', '!=', 'admin');
            })
            ->count();

        if ($customersWithInvalidAdmin > 0) {
            throw new \RuntimeException(
                "Cannot harden customer ownership because {$customersWithInvalidAdmin} customer(s) are assigned to an invalid ISP Admin."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Clean Up Any Partial Previous Attempt
        |--------------------------------------------------------------------------
        */

        DB::unprepared(
            'DROP TRIGGER IF EXISTS users_validate_admin_before_insert'
        );

        DB::unprepared(
            'DROP TRIGGER IF EXISTS users_validate_admin_before_update'
        );

        /*
        |--------------------------------------------------------------------------
        | Harden Parent Admin Foreign Key
        |--------------------------------------------------------------------------
        |
        | Customers must never become silently orphaned when their ISP Admin is
        | deleted. Restrict deletion while dependent customer accounts exist.
        |
        */

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

        /*
        |--------------------------------------------------------------------------
        | Create Database-Specific Integrity Triggers
        |--------------------------------------------------------------------------
        */

        if (DB::getDriverName() === 'sqlite') {
            $this->createSqliteTriggers();

            return;
        }

        $this->createMysqlTriggers();
    }

    /**
     * Create MySQL ownership-validation triggers.
     */
    private function createMysqlTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER users_validate_admin_before_insert
            BEFORE INSERT ON users
            FOR EACH ROW
            BEGIN
                DECLARE valid_admin_count INT DEFAULT 0;

                IF NEW.role = 'customer' THEN

                    IF NEW.admin_id IS NULL THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Customer accounts must be assigned to an ISP Admin.';
                    END IF;

                    SELECT COUNT(*)
                    INTO valid_admin_count
                    FROM users
                    WHERE id = NEW.admin_id
                      AND role = 'admin';

                    IF valid_admin_count = 0 THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Customer admin_id must reference a valid ISP Admin.';
                    END IF;

                ELSEIF NEW.role = 'admin' OR NEW.role = 'super_admin' THEN

                    IF NEW.admin_id IS NOT NULL THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Admin and Super Admin accounts cannot belong to another Admin.';
                    END IF;

                ELSE

                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Invalid user role.';

                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER users_validate_admin_before_update
            BEFORE UPDATE ON users
            FOR EACH ROW
            BEGIN
                DECLARE valid_admin_count INT DEFAULT 0;
                DECLARE owned_customer_count INT DEFAULT 0;

                IF NEW.role = 'customer' THEN

                    IF NEW.admin_id IS NULL THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Customer accounts must be assigned to an ISP Admin.';
                    END IF;

                    IF NEW.admin_id = OLD.id THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'A customer cannot be assigned as their own ISP Admin.';
                    END IF;

                    SELECT COUNT(*)
                    INTO valid_admin_count
                    FROM users
                    WHERE id = NEW.admin_id
                      AND role = 'admin';

                    IF valid_admin_count = 0 THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Customer admin_id must reference a valid ISP Admin.';
                    END IF;

                ELSEIF NEW.role = 'admin' OR NEW.role = 'super_admin' THEN

                    IF NEW.admin_id IS NOT NULL THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Admin and Super Admin accounts cannot belong to another Admin.';
                    END IF;

                ELSE

                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Invalid user role.';

                END IF;

                IF OLD.role = 'admin' AND NEW.role <> 'admin' THEN

                    SELECT COUNT(*)
                    INTO owned_customer_count
                    FROM users
                    WHERE admin_id = OLD.id
                      AND role = 'customer';

                    IF owned_customer_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Cannot change the role of an ISP Admin that still owns customers.';
                    END IF;

                END IF;
            END
        ");
    }

    /**
     * Create equivalent SQLite ownership-validation triggers.
     */
    private function createSqliteTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER users_validate_admin_before_insert
            BEFORE INSERT ON users
            FOR EACH ROW
            BEGIN
                SELECT CASE
                    WHEN NEW.role NOT IN (
                        'customer',
                        'admin',
                        'super_admin'
                    )
                    THEN RAISE(
                        ABORT,
                        'Invalid user role.'
                    )
                END;

                SELECT CASE
                    WHEN NEW.role = 'customer'
                         AND NEW.admin_id IS NULL
                    THEN RAISE(
                        ABORT,
                        'Customer accounts must be assigned to an ISP Admin.'
                    )
                END;

                SELECT CASE
                    WHEN NEW.role = 'customer'
                         AND NEW.admin_id IS NOT NULL
                         AND NOT EXISTS (
                            SELECT 1
                            FROM users
                            WHERE id = NEW.admin_id
                              AND role = 'admin'
                         )
                    THEN RAISE(
                        ABORT,
                        'Customer admin_id must reference a valid ISP Admin.'
                    )
                END;

                SELECT CASE
                    WHEN NEW.role IN ('admin', 'super_admin')
                         AND NEW.admin_id IS NOT NULL
                    THEN RAISE(
                        ABORT,
                        'Admin and Super Admin accounts cannot belong to another Admin.'
                    )
                END;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER users_validate_admin_before_update
            BEFORE UPDATE ON users
            FOR EACH ROW
            BEGIN
                SELECT CASE
                    WHEN NEW.role NOT IN (
                        'customer',
                        'admin',
                        'super_admin'
                    )
                    THEN RAISE(
                        ABORT,
                        'Invalid user role.'
                    )
                END;

                SELECT CASE
                    WHEN NEW.role = 'customer'
                         AND NEW.admin_id IS NULL
                    THEN RAISE(
                        ABORT,
                        'Customer accounts must be assigned to an ISP Admin.'
                    )
                END;

                SELECT CASE
                    WHEN NEW.role = 'customer'
                         AND NEW.admin_id = OLD.id
                    THEN RAISE(
                        ABORT,
                        'A customer cannot be assigned as their own ISP Admin.'
                    )
                END;

                SELECT CASE
                    WHEN NEW.role = 'customer'
                         AND NEW.admin_id IS NOT NULL
                         AND NOT EXISTS (
                            SELECT 1
                            FROM users
                            WHERE id = NEW.admin_id
                              AND role = 'admin'
                         )
                    THEN RAISE(
                        ABORT,
                        'Customer admin_id must reference a valid ISP Admin.'
                    )
                END;

                SELECT CASE
                    WHEN NEW.role IN ('admin', 'super_admin')
                         AND NEW.admin_id IS NOT NULL
                    THEN RAISE(
                        ABORT,
                        'Admin and Super Admin accounts cannot belong to another Admin.'
                    )
                END;

                SELECT CASE
                    WHEN OLD.role = 'admin'
                         AND NEW.role <> 'admin'
                         AND EXISTS (
                            SELECT 1
                            FROM users
                            WHERE admin_id = OLD.id
                              AND role = 'customer'
                         )
                    THEN RAISE(
                        ABORT,
                        'Cannot change the role of an ISP Admin that still owns customers.'
                    )
                END;
            END
        ");
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        DB::unprepared(
            'DROP TRIGGER IF EXISTS users_validate_admin_before_insert'
        );

        DB::unprepared(
            'DROP TRIGGER IF EXISTS users_validate_admin_before_update'
        );

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }
};