<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->enum('license_payment_type', [
                'till',
                'paybill',
                'phone',
            ])->default('till')->after('currency');

            $table->string('license_till_number')
                ->nullable()
                ->after('license_payment_type');

            $table->string('license_paybill_number')
                ->nullable()
                ->after('license_till_number');

            $table->string('license_account_number')
                ->nullable()
                ->after('license_paybill_number');

            $table->string('license_phone_number')
                ->nullable()
                ->after('license_account_number');

            $table->decimal('license_monthly_fee', 10, 2)
                ->default(400)
                ->after('license_phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn([
                'license_payment_type',
                'license_till_number',
                'license_paybill_number',
                'license_account_number',
                'license_phone_number',
                'license_monthly_fee',
            ]);
        });
    }
};