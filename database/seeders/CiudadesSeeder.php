<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ciudad;

class CiudadesSeeder extends Seeder
{
    public function run(): void
    {
        Ciudad::insert([
            ['nombre' => 'Cali', 'activo' => 1],
            ['nombre' => 'Bogota', 'activo' => 0],
            ['nombre' => 'Medellin', 'activo' => 1],
        ]);
    }
}
