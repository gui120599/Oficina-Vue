<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('make_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
            $table->unique(['make_id', 'name']); // Nome do modelo único por marca
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
    }
};
