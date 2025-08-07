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
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('puesto');
            $table->decimal('sueldo_diario', 8, 2);
            $table->decimal('dias_trabajados', 8, 2);
            $table->decimal('sueldo_semanal', 8, 2);
            $table->decimal('deposito_bancario', 10, 2)->nullable();
            $table->decimal('sueldo_a_pagar', 10, 2);
            $table->decimal('sueldo_fijo', 10, 2);
            $table->decimal('valor_bono', 10, 2)->nullable();
            $table->decimal('bono_obtenido', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominas');
    }
};

