<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carros', function (Blueprint $table) {
            // Evita duplicados de placa
            if (!Schema::hasColumn('carros', 'placa')) return;
            $table->unique('placa', 'ux_carros_placa');
        });

        Schema::table('ciudades', function (Blueprint $table) {
            // Nombre de ciudad único (opcional, recomendado)
            if (!Schema::hasColumn('ciudades', 'nombre')) return;
            $table->unique('nombre', 'ux_ciudades_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('carros', function (Blueprint $table) {
            $table->dropUnique('ux_carros_placa');
        });
        Schema::table('ciudades', function (Blueprint $table) {
            $table->dropUnique('ux_ciudades_nombre');
        });
    }
};
