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
         * Every router must belong to an ISP Admin.
         * Do not silently delete or assign orphaned routers.
         */
        $orphanedRouters = DB::table('routers')
            ->whereNull('admin_id')
            ->count();

        if ($orphanedRouters > 0) {
            throw new \RuntimeException(
                "Cannot make routers.admin_id required because {$orphanedRouters} router(s) do not have an Admin assigned."
            );
        }

        /*
         * Remove the existing foreign key first.
         */
        Schema::table('routers', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        /*
         * Make admin_id mandatory.
         */
        Schema::table('routers', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')
                ->nullable(false)
                ->change();
        });

        /*
         * Recreate the foreign key.
         *
         * When an ISP Admin is permanently deleted,
         * its router configuration is also removed.
         */
        Schema::table('routers', function (Blueprint $table) {
            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        Schema::table('routers', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')
                ->nullable()
                ->change();
        });

        Schema::table('routers', function (Blueprint $table) {
            $table->foreign('admin_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};