<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use App\Models\Viaje;
use App\Models\Carro;
use App\Models\Ciudad;

class ApiController extends Controller
{
    /**
     * GET /api/v1/vehiculos/{placa}/viajes?desde=YYYY-MM-DD&hasta=YYYY-MM-DD
     * Headers: X-API-KEY: <tu_api_key>
     *
     * Respuestas:
     * 200 OK: lista de viajes
     * 400 Bad Request: parámetros inválidos
     * 401 Unauthorized: API key inválida o ausente (middleware)
     * 404 Not Found: placa no existe
     */
    public function porPlaca(Request $request, string $placa)
    {
        // Validación básica de inputs
        try {
            $data = $request->validate([
                'desde' => ['nullable', 'date'],
                'hasta' => ['nullable', 'date'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'error'   => 'Bad Request',
                'message' => $e->validator->errors(),
            ], 400);
        }

        // Verificar existencia de carro
        $carro = DB::table('carros')->where('placa', $placa)->whereNull('deleted_at')->first();
        if (!$carro) {
            return response()->json([
                'error'   => 'Not Found',
                'message' => "No existe vehículo con placa {$placa}."
            ], 404);
        }

        // Construir consulta
        $q = DB::table('viajes as viaje')
            ->leftJoin('ciudades as co', 'co.idciudad', '=', 'viaje.idciudad_origen')
            ->leftJoin('ciudades as cd', 'cd.idciudad', '=', 'viaje.idciudad_destino')
            ->where('viaje.idcarro', $carro->idcarro)
            ->whereNull('viaje.deleted_at')
            ->select([
                'viaje.idviaje',
                DB::raw('COALESCE(co.nombre,"—") as ciudad_origen'),
                DB::raw('COALESCE(cd.nombre,"—") as ciudad_destino'),
                'viaje.tiempo_horas',
                'viaje.fecha',
            ])
            ->orderByDesc('viaje.fecha');

        // Filtros opcionales
        if (!empty($data['desde'])) {
            $q->where('viaje.fecha', '>=', $data['desde'].' 00:00:00');
        }
        if (!empty($data['hasta'])) {
            $q->where('viaje.fecha', '<=', $data['hasta'].' 23:59:59');
        }

        $viajes = $q->get();

        return response()->json([
            'data' => [
                'placa'  => $placa,
                'desde'  => $data['desde'] ?? null,
                'hasta'  => $data['hasta'] ?? null,
                'total'  => $viajes->count(),
                'viajes' => $viajes,
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        // Validación
        $v = Validator::make($request->all(), [
            'idcarro'          => 'required|integer|exists:carros,idcarro,deleted_at,NULL',
            'idciudad_origen'  => 'required|integer|exists:ciudades,idciudad,deleted_at,NULL',
            'idciudad_destino' => 'required|integer|different:idciudad_origen|exists:ciudades,idciudad,deleted_at,NULL',
            'tiempo_horas'     => 'required|integer|min:0|max:100000',
            // acepta "Y-m-d" o "Y-m-d H:i:s"
            'fecha'            => ['required', 'string', function ($attr, $val, $fail) {
                if (!preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/', $val)) {
                    $fail('El campo fecha debe tener formato Y-m-d o Y-m-d H:i:s.');
                }
            }],
        ], [
            'idcarro.required' => 'idcarro es obligatorio.',
            'idciudad_origen.required' => 'idciudad_origen es obligatorio.',
            'idciudad_destino.required' => 'idciudad_destino es obligatorio.',
            'tiempo_horas.required' => 'tiempo_horas es obligatorio.',
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 422,
                'error'  => 'Validation Error',
                'messages' => $v->errors(),
            ], 422);
        }

        $data = $v->validated();

        // Normalizar fecha: si viene solo YYYY-mm-dd, le agrega 00:00:00
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['fecha'])) {
            $data['fecha'] = $data['fecha'].' 00:00:00';
        }

        // Evita duplicados exactos en misma fecha (por día)
        $yaExiste = Viaje::where('idcarro', $data['idcarro'])
            ->where('idciudad_origen', $data['idciudad_origen'])
            ->where('idciudad_destino', $data['idciudad_destino'])
            ->whereDate('fecha', Carbon::parse($data['fecha'])->toDateString())
            ->exists();

        if ($yaExiste) {
            return response()->json([
                'status'  => 409,
                'error'   => 'Conflict',
                'message' => 'Ya existe un viaje con ese carro, origen, destino y fecha (día) registrados.',
            ], 409);
        }

        // Crear
        try {
            $viaje = Viaje::create([
                'idcarro'          => $data['idcarro'],
                'idciudad_origen'  => $data['idciudad_origen'],
                'idciudad_destino' => $data['idciudad_destino'],
                'tiempo_horas'     => $data['tiempo_horas'],
                'fecha'            => $data['fecha'],
            ]);

            return response()->json([
                'status'  => 201,
                'message' => 'Viaje creado correctamente.',
                'data'    => [
                    'idviaje' => $viaje->idviaje,
                ],
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'error'   => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function updateColor(Request $request, string $placa)
    {
        // Normalizo placa
        $placa = strtoupper(trim($placa));

        // Validar body
        $v = Validator::make($request->all(), [
            'color' => 'required|string|min:3|max:40',
        ]);

        if ($v->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $v->errors(),
            ], 422);
        }

        // Buscar vehículo por placa ignora soft-deleted
        $carro = Carro::whereNull('deleted_at')->where('placa', $placa)->first();

        if (!$carro) {
            return response()->json([
                'message' => 'Vehículo no encontrado por placa.',
            ], 404);
        }

        $old = $carro->color;
        $carro->color = $request->input('color');
        $carro->save();

        return response()->json([
            'message'       => 'Color actualizado',
            'placa'         => $carro->placa,
            'color_anterior'=> $old,
            'color_nuevo'   => $carro->color,
        ], 200);
    }




}
