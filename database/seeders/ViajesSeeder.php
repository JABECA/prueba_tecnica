<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Viaje;

class ViajesSeeder extends Seeder
{
    public function run(): void
    {
        Viaje::insert([
            ['idcarro' => '1', 'idciudad_origen' => '3', 'idciudad_destino' => '2', 'tiempo_horas' => '8',  'fecha' => '2025-09-15 17:03:53'],
            ['idcarro' => '2', 'idciudad_origen' => '2', 'idciudad_destino' => '3', 'tiempo_horas' => '6',  'fecha' => '2025-09-25 17:03:53'],
            ['idcarro' => '2', 'idciudad_origen' => '3', 'idciudad_destino' => '1', 'tiempo_horas' => '12', 'fecha' => '2025-09-29 17:03:53'],
            ['idcarro' => '3', 'idciudad_origen' => '1', 'idciudad_destino' => '2', 'tiempo_horas' => '10', 'fecha' => '2025-10-08 17:04:35'],
            ['idcarro' => '1', 'idciudad_origen' => '1', 'idciudad_destino' => '3', 'tiempo_horas' => '15', 'fecha' => '2025-10-10 17:23:29'],
            ['idcarro' => '2', 'idciudad_origen' => '1', 'idciudad_destino' => '2', 'tiempo_horas' => '7',  'fecha' => '2025-10-15 17:23:29'],
            ['idcarro' => '3', 'idciudad_origen' => '2', 'idciudad_destino' => '1', 'tiempo_horas' => '9',  'fecha' => '2025-10-29 17:23:54'],
        ]);
    }
}
