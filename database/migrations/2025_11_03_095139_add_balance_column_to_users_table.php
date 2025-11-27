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
        Schema::table('locations', function (Blueprint $table) {
            // Add your new column here. A string column named 'phone_number',
            // which is nullable (can be empty) and placed after 'password'.
            $table->integer('balance')->default(0)->after('address'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // This method should reverse what up() did.
            // It drops the new column.
            $table->dropColumn('balance'); 
        });
    }
};