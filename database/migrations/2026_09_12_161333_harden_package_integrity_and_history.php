<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Safety checks before enforcing stricter constraints
        |--------------------------------------------------------------------------
        */

        if (DB::table('packages')->whereNull('admin_id')->exists()) {
            throw new RuntimeException(
                'Cannot harden packages because one or more packages have no admin_id. Assign every package to an ISP Admin first.'
            );
        }

        if (DB::table('packages')->whereNull('router_profile')->exists()) {
            throw new RuntimeException(
                'Cannot harden packages because one or more packages have no router_profile. Update those packages first.'
            );
        }

        $duplicateNames = DB::table('packages')
            ->select('admin_id', 'name')
            ->groupBy('admin_id', 'name')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicateNames) {
            throw new RuntimeException(
                'Cannot harden packages because an ISP Admin has duplicate package names. Rename duplicates first.'
            );
        }

        $duplicateProfiles = DB::table('packages')
            ->select('admin_id', 'router_profile')
            ->groupBy('admin_id', 'router_profile')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($duplicateProfiles) {
            throw new RuntimeException(
                'Cannot harden packages because an ISP Admin has duplicate router profiles. Rename duplicates first.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Package ownership
        |--------------------------------------------------------------------------
        */

        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')
                ->nullable(false)
                ->change();

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | Router profile must exist
        |--------------------------------------------------------------------------
        */

        Schema::table('packages', function (Blueprint $table) {
            $table->string('router_profile', 100)
                ->nullable(false)
                ->change();
        });

        /*
        |--------------------------------------------------------------------------
        | Tenant-level uniqueness
        |--------------------------------------------------------------------------
        */

        Schema::table('packages', function (Blueprint $table) {
            $table->unique(
                ['admin_id', 'name'],
                'packages_admin_name_unique'
            );

            $table->unique(
                ['admin_id', 'router_profile'],
                'packages_admin_router_profile_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Preserve subscription history
        |--------------------------------------------------------------------------
        |
        | A package must never be deleted automatically if subscriptions reference
        | it. Historical subscription records are part of the billing record.
        |
        */

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->restrictOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | Preserve payment history
        |--------------------------------------------------------------------------
        |
        | Payments are financial records and must never disappear just because
        | somebody attempts to delete a package.
        |
        */

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->cascadeOnDelete();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->cascadeOnDelete();
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropUnique('packages_admin_name_unique');
            $table->dropUnique('packages_admin_router_profile_unique');

            $table->dropForeign(['admin_id']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')
                ->nullable()
                ->change();

            $table->string('router_profile')
                ->nullable()
                ->change();

            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};