<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_payments', function (Blueprint $table) {
            $table->id();

            $table->integer('months');
            $table->decimal('amount', 10, 2);

            $table->string('payment_method')->nullable();
            $table->string('transaction_code')->nullable();

            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_payments');
    }
};