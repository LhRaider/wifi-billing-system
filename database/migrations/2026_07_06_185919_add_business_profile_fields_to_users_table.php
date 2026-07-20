<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('network_name')->nullable();

            $table->string('business_address')->nullable();

            $table->enum('payment_type',[
                'till',
                'paybill',
                'phone'
            ])->default('till');

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'network_name',
                'business_address',
                'payment_type'
            ]);

        });
    }
};