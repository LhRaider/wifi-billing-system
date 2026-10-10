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
        /*
        |--------------------------------------------------------------------------
        | Checkout Request ID Uniqueness
        |--------------------------------------------------------------------------
        |
        | A previous migration attempt may already have created this index
        | before MySQL returned an error. Checking first makes this migration
        | safe and allows Laravel's migration history to remain synchronized
        | with the actual database schema.
        |
        */

        if (
            !Schema::hasIndex(
                'license_payments',
                'license_payments_checkout_request_id_unique'
            )
        ) {
            Schema::table(
                'license_payments',
                function (Blueprint $table) {
                    $table->unique(
                        'checkout_request_id',
                        'license_payments_checkout_request_id_unique'
                    );
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (
            Schema::hasIndex(
                'license_payments',
                'license_payments_checkout_request_id_unique'
            )
        ) {
            Schema::table(
                'license_payments',
                function (Blueprint $table) {
                    $table->dropUnique(
                        'license_payments_checkout_request_id_unique'
                    );
                }
            );
        }
    }
};