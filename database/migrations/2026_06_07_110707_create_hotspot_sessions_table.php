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
        Schema::create('hotspot_sessions', function (Blueprint $table) {
            $table->id();

$table->foreignId('user_id')->constrained()->onDelete('cascade');

$table->string('mac_address')->nullable();
$table->string('ip_address')->nullable();

$table->datetime('login_time');
$table->datetime('logout_time')->nullable();

$table->bigInteger('data_used')->default(0);

$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_sessions');
    }
};
