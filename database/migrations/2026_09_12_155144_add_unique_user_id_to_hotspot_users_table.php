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
        $duplicateUserIds = DB::table('hotspot_users')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');

        if ($duplicateUserIds->isNotEmpty()) {
            throw new \RuntimeException(
                'Cannot add unique user_id constraint to hotspot_users because duplicate customer records already exist. Resolve the duplicates first.'
            );
        }

        Schema::table('hotspot_users', function (Blueprint $table) {
            $table->unique(
                'user_id',
                'hotspot_users_user_id_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotspot_users', function (Blueprint $table) {
            $table->dropUnique(
                'hotspot_users_user_id_unique'
            );
        });
    }
};