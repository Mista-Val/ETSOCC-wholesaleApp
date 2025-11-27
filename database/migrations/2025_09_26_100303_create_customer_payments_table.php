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
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('location_id');
            $table->BigInteger('customer_id');
            $table->float('amount', 10, 2);
            $table->string('type')->nullable(); // BalancesLogTypes
            $table->string('remarks')->nullable();
            $table->string('payment_method')->nullable(); // PaymentMethods
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
