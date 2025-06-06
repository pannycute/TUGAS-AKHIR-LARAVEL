<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id('payment_method_id');
            $table->string('method_name', 100);
            $table->text('details')->nullable();
            $table->timestamps(); // otomatis buat created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
