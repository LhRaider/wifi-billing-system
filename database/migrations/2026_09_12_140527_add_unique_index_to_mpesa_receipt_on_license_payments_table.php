<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('license_payments', function (Blueprint $table) {
            $table->unique(
                'mpesa_receipt',
                'license_payments_mpesa_receipt_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('license_payments', function (Blueprint $table) {
            $table->dropUnique(
                'license_payments_mpesa_receipt_unique'
            );
        });
    }
};