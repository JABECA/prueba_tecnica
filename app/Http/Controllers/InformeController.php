<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Models\Viaje;
use App\Models\Ciudad;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class InformeController extends Controller
{
    public static function middleware(): array
    {
        return [ new Middleware('auth') ];
    }

   
    public function index()
    {
        return view('informes.index');
    }

    /***Pregunta 3.1) Contar cuantos carros hay de cada color*/

    public function colores()
    {
        $rows = Carro::query()
            ->select('color', DB::raw('COUNT(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('color')
            ->orderByDesc('total')
            ->get();

        return view('informes.colores', compact('rows'));
    }

    /***Pregunta 3.2) Vehículos con viajes DESDE Medellín desde 2025-10-08 en adelante: mostrar placa, ciudad destino y horas*/

    public function medellin()
    {
        $desde = '2025-10-08';

        $rows = DB::table('viajes as v')
            ->join('carros as c', 'c.idcarro', '=', 'v.idcarro')
            ->leftJoin('ciudades as co', 'co.idciudad', '=', 'v.idciudad_origen')
            ->leftJoin('ciudades as cd', 'cd.idciudad', '=', 'v.idciudad_destino')
            ->whereDate('v.fecha', '>=', $desde)
            ->where('co.nombre', '=', 'Medellin')
            ->select([
                'c.placa',
                DB::raw('COALESCE(cd.nombre,"—") as destino'),
                'v.tiempo_horas',
                'v.fecha',
            ])
            ->orderByDesc('v.fecha')
            ->get();

        return view('informes.medellin', compact('rows','desde'));
    }

    /***Pregunta 3.3) Promedio de horas de viaje del carro BBB456 + fecha de registro del carro*/

    public function promedioCarro()
    {
        $placa = 'BBB456';

        $carro = Carro::where('placa', $placa)->first();

        $promedio = Viaje::query()
            ->whereHas('carro', fn($q) => $q->where('placa', $placa))
            ->avg('tiempo_horas');

        // Puede ser null si no hay viajes aún
        return view('informes.promedio', [
            'placa'      => $placa,
            'promedio'   => $promedio ? round($promedio, 2) : null,
            'fechaIngreso' => $carro?->fecha_ingreso,
        ]);
    }

    /***Pregunta 3.4) Carros que aún no tienen viajes registrados*/

    public function sinViajes()
    {
        $rows = DB::table('carros as c')
            ->leftJoin('viajes as v', 'v.idcarro', '=', 'c.idcarro')
            ->whereNull('v.idviaje')
            ->whereNull('c.deleted_at')
            ->select('c.placa','c.color','c.fecha_ingreso')
            ->orderBy('c.placa')
            ->get();

        return view('informes.sin_viajes', compact('rows'));
    }

    /***Pregunta 3.5) Carros que viajaron entre 2025-09-26 y 2025-10-26, mostrando placa y entre qué ciudades viajaron */

    public function entreFechas()
    {
        $desde = '2025-09-26';
        $hasta = '2025-10-26';

        $rows = DB::table('viajes as v')
            ->join('carros as c', 'c.idcarro', '=', 'v.idcarro')
            ->leftJoin('ciudades as co', 'co.idciudad', '=', 'v.idciudad_origen')
            ->leftJoin('ciudades as cd', 'cd.idciudad', '=', 'v.idciudad_destino')
            ->whereBetween('v.fecha', [$desde.' 00:00:00', $hasta.' 23:59:59'])
            ->select([
                'c.placa',
                DB::raw('COALESCE(co.nombre,"—") as origen'),
                DB::raw('COALESCE(cd.nombre,"—") as destino'),
                'v.tiempo_horas',
                'v.fecha',
            ])
            ->orderByDesc('v.fecha')
            ->get();

        return view('informes.entre_fechas', compact('rows','desde','hasta'));
    }

    
    /**Pregunta 3.6 Vehículos con viajes donde la ciudad (origen o destino) tenga estado 0 (inactiva) **/

    // ciudad  origen cero o inactiva
    public function ciudadesOrigenCero()
    {
        $rows = DB::table('viajes as v')
            ->join('carros as c', 'c.idcarro', '=', 'v.idcarro')
            ->leftJoin('ciudades as co', 'co.idciudad', '=', 'v.idciudad_origen')
            ->leftJoin('ciudades as cd', 'cd.idciudad', '=', 'v.idciudad_destino')
            ->where('co.activo', '=', 0)
            ->select([
                'c.placa',
                DB::raw('COALESCE(co.nombre,"—") as origen'),
                DB::raw('COALESCE(cd.nombre,"—") as destino'),
                'v.tiempo_horas',
                'v.fecha',
                DB::raw('co.activo as flag_inactivo'), // 0
            ])
            ->orderByDesc('v.fecha')
            ->get();

        $scope = 'origen'; // para el título en la vista
        return view('informes.estado_cero', compact('rows','scope'));
    }

    // ciudad  destino cero o inactiva
    public function ciudadesDestinoCero()
    {
        $rows = DB::table('viajes as v')
            ->join('carros as c', 'c.idcarro', '=', 'v.idcarro')
            ->leftJoin('ciudades as co', 'co.idciudad', '=', 'v.idciudad_origen')
            ->leftJoin('ciudades as cd', 'cd.idciudad', '=', 'v.idciudad_destino')
            ->where('cd.activo', '=', 0)
            ->select([
                'c.placa',
                DB::raw('COALESCE(co.nombre,"—") as origen'),
                DB::raw('COALESCE(cd.nombre,"—") as destino'),
                'v.tiempo_horas',
                'v.fecha',
                DB::raw('cd.activo as flag_inactivo'), // 0
            ])
            ->orderByDesc('v.fecha')
            ->get();

        $scope = 'destino';
        return view('informes.estado_cero', compact('rows','scope'));
    }

    // ciudad  origen o destino cero o inactivas

    public function ciudadesEstadoCero()
    {
        $rows = DB::table('viajes as v')
            ->join('carros as c', 'c.idcarro', '=', 'v.idcarro')
            ->leftJoin('ciudades as co', 'co.idciudad', '=', 'v.idciudad_origen')
            ->leftJoin('ciudades as cd', 'cd.idciudad', '=', 'v.idciudad_destino')
            ->where(function($q){
                $q->where('co.activo', '=', 0)
                  ->orWhere('cd.activo', '=', 0);
            })
            ->select([
                'c.placa',
                DB::raw('COALESCE(co.nombre,"—") as origen'),
                DB::raw('COALESCE(cd.nombre,"—") as destino'),
                'v.tiempo_horas',
                'v.fecha',
                DB::raw('IFNULL(co.activo, cd.activo) as alguna_inactiva'),
            ])
            ->orderByDesc('v.fecha')
            ->get();
        $scope = 'ambos';
        return view('informes.estado_cero', compact('rows', 'scope'));
    }






}
