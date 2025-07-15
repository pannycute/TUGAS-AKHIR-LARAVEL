<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing enum and recreate it with 'confirmed' status
            $table->enum('status', ['pending', 'proses', 'selesai', 'confirmed'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert back to original enum values
            $table->enum('status', ['pending', 'proses', 'selesai'])->change();
        });
    }
}; 