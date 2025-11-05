<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carro;

class CarrosSeeder extends Seeder
{
    public function run(): void
    {
        Carro::insert([
            ['placa' => 'AAA123', 'color' => 'Azul',  'fecha_ingreso' => '2025-07-10 16:36:14'],
            ['placa' => 'BBB456', 'color' => 'Verde', 'fecha_ingreso' => '2025-07-28 16:36:14'],
            ['placa' => 'CCC789', 'color' => 'Rojo',  'fecha_ingreso' => '2025-08-08 16:36:14'],
            ['placa' => 'DDD963', 'color' => 'Azul',  'fecha_ingreso' => '2025-08-20 16:36:15'],
            ['placa' => 'EEE852', 'color' => 'Rojo',  'fecha_ingreso' => '2025-08-28 16:36:15'],
            ['placa' => 'FFF741', 'color' => 'Azul',  'fecha_ingreso' => '2025-10-15 16:36:15'],

        ]);
    }
}
