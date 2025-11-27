<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_final_cash_destinations_table.php

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
        Schema::create('final_cash_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('final_destination');
            $table->string('cash_handler_name');
            $table->string('responsible_person');
            $table->decimal('amount', 15, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_cash_destinations');
    }
};