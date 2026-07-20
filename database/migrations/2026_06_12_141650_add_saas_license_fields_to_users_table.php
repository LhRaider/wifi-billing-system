<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('id')->constrained('users')->nullOnDelete();

            $table->string('service_status')->default('trial')->after('role');
            $table->dateTime('trial_ends_at')->nullable()->after('service_status');
            $table->dateTime('service_expires_at')->nullable()->after('trial_ends_at');

            $table->string('business_name')->nullable()->after('service_expires_at');
            $table->string('business_phone')->nullable()->after('business_name');
            $table->string('business_till')->nullable()->after('business_phone');
            $table->string('business_paybill')->nullable()->after('business_till');
            $table->string('business_account')->nullable()->after('business_paybill');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_id');
            $table->dropColumn([
                'service_status',
                'trial_ends_at',
                'service_expires_at',
                'business_name',
                'business_phone',
                'business_till',
                'business_paybill',
                'business_account',
            ]);
        });
    }
};