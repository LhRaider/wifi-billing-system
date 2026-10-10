<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mpesa_environment')
                ->default('sandbox')
                ->after('payment_type');

            $table->text('mpesa_consumer_key')
                ->nullable()
                ->after('mpesa_environment');

            $table->text('mpesa_consumer_secret')
                ->nullable()
                ->after('mpesa_consumer_key');

            $table->string('mpesa_shortcode')
                ->nullable()
                ->after('mpesa_consumer_secret');

            $table->text('mpesa_passkey')
                ->nullable()
                ->after('mpesa_shortcode');

            $table->string('mpesa_transaction_type')
                ->default('CustomerPayBillOnline')
                ->after('mpesa_passkey');

            $table->boolean('mpesa_enabled')
                ->default(false)
                ->after('mpesa_transaction_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mpesa_environment',
                'mpesa_consumer_key',
                'mpesa_consumer_secret',
                'mpesa_shortcode',
                'mpesa_passkey',
                'mpesa_transaction_type',
                'mpesa_enabled',
            ]);
        });
    }
};