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
        Schema::create('vehicle_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Ao deletar cliente, deleta veículos
            $table->foreignId('make_id')->nullable()->constrained(); // make_id pode ser nulo se não selecionado
            $table->foreignId('model_id')->nullable()->constrained(); // model_id pode ser nulo se não selecionado
            $table->year('year')->nullable(); // Para ano de fabricação (ex: 2023)
            $table->string('license_plate')->unique(); // Placa deve ser única e não nula
            $table->string('color')->nullable();
            $table->enum('type', ['MOTORCYCLE', 'CAR', 'PICKUP TRUCK', 'TRUCK'])->default('CAR'); // Sempre terá um valor padrão
            $table->decimal('mileage', 10, 2)->nullable(); 
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Para soft delete (restauração futura)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_clients');
    }
};
