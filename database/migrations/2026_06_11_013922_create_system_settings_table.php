<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('WiFi Billing System');
            $table->string('business_phone')->nullable();
            $table->string('currency')->default('KES');
            $table->boolean('mpesa_auto_activate')->default(true);
            $table->boolean('mikrotik_real_connection')->default(false);
            $table->unsignedBigInteger('default_router_id')->nullable();
            $table->text('support_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};