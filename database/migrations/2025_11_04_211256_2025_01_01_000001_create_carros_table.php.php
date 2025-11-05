<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carros', function (Blueprint $table) {
            $table->integer('idcarro', true);
            $table->string('placa', 45)->nullable();
            $table->string('color', 45)->nullable();
            $table->timestamp('fecha_ingreso')->nullable()->useCurrent();
            $table->engine = 'InnoDB';
        });
    }

    public function down(): void {
        Schema::dropIfExists('carros');
    }
};