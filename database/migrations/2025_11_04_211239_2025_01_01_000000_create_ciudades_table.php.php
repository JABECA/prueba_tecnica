<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ciudades', function (Blueprint $table) {
            $table->integer('idciudad', true);
            $table->string('nombre', 45)->nullable();
            $table->boolean('activo')->nullable();
            $table->engine = 'InnoDB';
        });
    }

    public function down(): void {
        Schema::dropIfExists('ciudades');
    }
};