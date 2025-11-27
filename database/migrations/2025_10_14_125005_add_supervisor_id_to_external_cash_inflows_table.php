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
        Schema::table('external_cash_inflows', function (Blueprint $table) {
            // Define the new column as a foreign key
            $table->foreignId('supervisor_id')
                  ->nullable() // Make it nullable if existing rows shouldn't break
                  ->constrained('users') // Assumes your user table is named 'users'
                  ->after('remarks'); // Place it after the 'remarks' column

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_cash_inflows', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropConstrainedForeignId('supervisor_id');
            
            // Drop the column itself
            $table->dropColumn('supervisor_id');
        });
    }
};