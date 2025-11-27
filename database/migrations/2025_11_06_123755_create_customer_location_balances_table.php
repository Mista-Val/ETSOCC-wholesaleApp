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
        Schema::create('customer_location_balances', function (Blueprint $table) {
            $table->id();
            
            // Link to the Customer
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            
            // Link to the Location (Outlet/Warehouse)
            // Adjust 'locations' to your actual location/outlet table name if different
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            
            // The available down payment balance
            $table->decimal('balance', 10, 2)->default(0);

            $table->timestamps();

            // Enforce that a customer can only have ONE balance entry per location
            $table->unique(['customer_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_location_balances');
    }
};