<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_payments', function (Blueprint $table) {
            $table->string('checkout_request_id')
                ->nullable()
                ->unique()
                ->after('transaction_code');

            $table->string('merchant_request_id')
                ->nullable()
                ->after('checkout_request_id');

            $table->string('mpesa_receipt')
                ->nullable()
                ->after('merchant_request_id');

            $table->string('phone_number')
                ->nullable()
                ->after('mpesa_receipt');

            $table->timestamp('paid_at')
                ->nullable()
                ->after('phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('license_payments', function (Blueprint $table) {
            $table->dropColumn([
                'checkout_request_id',
                'merchant_request_id',
                'mpesa_receipt',
                'phone_number',
                'paid_at',
            ]);
        });
    }
};