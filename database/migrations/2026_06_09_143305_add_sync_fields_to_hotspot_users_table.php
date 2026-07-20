<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hotspot_users', function (Blueprint $table) {
            //
        });

        Schema::table('hotspot_users', function (Blueprint $table) {
    $table->boolean('router_synced')->default(false)->after('status');
    $table->string('last_sync_status')->nullable()->after('router_synced');
    $table->text('last_sync_message')->nullable()->after('last_sync_status');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotspot_users', function (Blueprint $table) {
            //
        });
        Schema::table('hotspot_users', function (Blueprint $table) {
    $table->dropColumn([
        'router_synced',
        'last_sync_status',
        'last_sync_message',
    ]);
});
    }
};
