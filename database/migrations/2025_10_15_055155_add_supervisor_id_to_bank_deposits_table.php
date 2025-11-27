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
        Schema::table('external_cash_outflows', function (Blueprint $table) {
            // 1. Add the foreign key column. Assuming 'supervisors' is the table name.
            // Adjust 'supervisors' if your table is named differently (e.g., 'users').
            $table->foreignId('supervisor_id')->after('remarks')
                  ->nullable() // Use nullable() if existing records should default to null
                  ->constrained('users') // Links to the 'id' column of the 'supervisors' table
                  ->onDelete('set null'); // Optional: What happens when the supervisor record is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_cash_outflows', function (Blueprint $table) {
            // 1. Drop the foreign key constraint
            $table->dropConstrainedForeignId('supervisor_id'); 
            
            // Note: If you used a specific constraint name, you'd use $table->dropForeign(['supervisor_id']);
            // And then $table->dropColumn('supervisor_id');
        });
    }
};