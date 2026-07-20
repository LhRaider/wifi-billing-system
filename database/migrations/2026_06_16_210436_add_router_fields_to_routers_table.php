<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {

            if (!Schema::hasColumn('routers', 'router_type')) {
                $table->string('router_type')
                    ->default('MikroTik')
                    ->after('api_port');
            }

            if (!Schema::hasColumn('routers', 'notes')) {
                $table->text('notes')
                    ->nullable()
                    ->after('router_type');
            }

        });
    }

    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn([
                'router_type',
                'notes'
            ]);
        });
    }
};