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
        Schema::create('waybills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->string('waybill_number')->unique();
            $table->date('loading_date');
            $table->date('estimated_delivery_date');
            $table->string('warehouse_name');
            $table->string('loader_name');
            $table->string('loader_position');
            $table->string('outlet_name');
            $table->integer('number_of_packages');
            $table->integer('quantity');
            $table->string('receiver_name');
            $table->string('receiver_position');
            $table->text('shipping_remarks')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waybills');
    }
};