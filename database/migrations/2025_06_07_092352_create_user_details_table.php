<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id('user_detail_id');
            
            // Gunakan unsignedBigInteger karena user_id di users adalah PK auto increment
            $table->unsignedBigInteger('user_id')->unique();

            // Definisi kolom tambahan
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->text('address');
            $table->string('phone_number', 20);
            $table->string('email', 255)->nullable();
            $table->timestamps();

            // Foreign key yang benar ke users.user_id
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_details');
    }
};
