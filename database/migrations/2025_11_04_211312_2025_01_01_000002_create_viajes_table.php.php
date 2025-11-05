<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('viajes', function (Blueprint $table) {
            $table->integer('idviaje', true);
            $table->integer('idcarro');
            $table->integer('idciudad_origen')->nullable();
            $table->integer('idciudad_destino')->nullable();
            $table->integer('tiempo_horas')->nullable();
            $table->timestamp('fecha')->nullable()->useCurrent();

            // FKs (opcional pero recomendado)
            $table->foreign('idcarro')->references('idcarro')->on('carros')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('idciudad_origen')->references('idciudad')->on('ciudades')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('idciudad_destino')->references('idciudad')->on('ciudades')->nullOnDelete()->cascadeOnUpdate();

            $table->engine = 'InnoDB';
        });
    }

    public function down(): void {
        Schema::dropIfExists('viajes');
    }
};