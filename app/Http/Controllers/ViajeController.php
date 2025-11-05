<?php

namespace App\Http\Controllers;

use App\Models\Viaje;
use App\Models\Carro;
use App\Models\Ciudad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


use Illuminate\Routing\Controllers\Middleware;


class ViajeController extends Controller
{
    

    public static function middleware(): array
    {
        // Protego el controlador con auth
        return [
            new Middleware('auth'),
        ];
    }

    // vista principal de los viajes, listo los viajes existentes
    public function index() {
        
        // Cargamos relaciones para DataTables
        $carros   = Carro::orderBy('placa')->whereNull('deleted_at')->get(['idcarro','placa']);
        $ciudades = Ciudad::orderBy('nombre')->whereNull('deleted_at')->get(['idciudad','nombre']);

        $viajes = Viaje::with(['carro','origen','destino'])
            ->whereNull('deleted_at')
            ->orderBy('idviaje','desc')->get();
        return view('viajes.index', compact('viajes', 'carros', 'ciudades'));
    }   

    // vista para crear viaje
    public function create() {
        return view('viajes.create', [
            'carros'  => Carro::orderBy('placa')->get(),
            'ciudades'=> Ciudad::orderBy('nombre')->get(),
        ]);
    }

    // almaceno los datos que viene de la vista crear viaje
    public function store(Request $request) {
        $data = $request->validate([
            'idcarro'           => 'required|integer|exists:carros,idcarro',
            'idciudad_origen'   => 'nullable|integer|exists:ciudades,idciudad',
            'idciudad_destino'  => 'nullable|integer|exists:ciudades,idciudad',
            'tiempo_horas'      => 'nullable|integer|min:0|max:100000',
            'fecha'             => 'nullable|date',
        ]);

        $duplicado = \App\Models\Viaje::where('idcarro', $data['idcarro'])
        ->where('idciudad_origen', $data['idciudad_origen'])
        ->where('idciudad_destino', $data['idciudad_destino'])
        ->whereDate('fecha', $data['fecha'])
        ->exists();

        if ($duplicado) {
            return back()->with('ok', 'Ya existe un registor igual en la bd. es decir un viaje con este carro, origen, destino y fecha');
        }

        Viaje::create($data);
        return redirect()->route('viajes.index')->with('ok','Viaje creado');
    }

    // vista para la edicion del viaje
    public function edit(Viaje $viaje) {
        return view('viajes.edit', [
            'viaje'   => $viaje,
            'carros'  => Carro::orderBy('placa')->get(),
            'ciudades'=> Ciudad::orderBy('nombre')->get(),
        ]);
    }

    // actualizar viaje
    public function update(Request $request, Viaje $viaje) {
        $data = $request->validate([
            'idcarro'           => 'required|integer|exists:carros,idcarro',
            'idciudad_origen'   => 'nullable|integer|exists:ciudades,idciudad',
            'idciudad_destino'  => 'nullable|integer|exists:ciudades,idciudad',
            'tiempo_horas'      => 'nullable|integer|min:0|max:100000',
            'fecha'             => 'nullable|date',
        ]);

        // Evita duplicado en otro viaje
        $duplicado = \App\Models\Viaje::where('idcarro', $data['idcarro'])
            ->where('idciudad_origen', $data['idciudad_origen'])
            ->where('idciudad_destino', $data['idciudad_destino'])
            ->whereDate('fecha', $data['fecha'])
            ->where('idviaje', '<>', $viaje->idviaje)
            ->exists();

        if ($duplicado) {
            return back()->with('ok', 'Ya existe un registor igual en la bd. es decir un viaje con este carro, origen, destino y fecha');
        }

        $viaje->update($data);
        return redirect()->route('viajes.index')->with('ok','Viaje actualizado');
    }

    // borrar viaje
    public function destroy(Viaje $viaje) {
        $viaje->delete();
        return redirect()->route('viajes.index')->with('ok','Viaje eliminado (Desactivado)');
    }

    // borrado suave o soft delete
    public function trashed() {

        $carros   = Carro::orderBy('placa')->whereNull('deleted_at')->get(['idcarro','placa']);
        $ciudades = Ciudad::orderBy('nombre')->whereNull('deleted_at')->get(['idciudad','nombre']);


        $viajes  = Viaje::onlyTrashed()->with(['carro','origen','destino'])
                   ->orderByDesc('idviaje')->get();
        $trashed = true;
        return view('viajes.index', compact('viajes','trashed', 'carros','ciudades'));
    }

    // restaurar viaje ras un soft delete
    public function restore($id) {
        $viaje = Viaje::onlyTrashed()->where('idviaje',$id)->firstOrFail();
        $viaje->restore();
        return redirect()->route('viajes.trashed')->with('ok','Viaje restaurado');
    }
 
    // forzar borrado permanente 
    public function forceDelete($id) {
        $viaje = Viaje::onlyTrashed()->where('idviaje',$id)->firstOrFail();
        $viaje->forceDelete();
        return redirect()->route('viajes.trashed')->with('ok','Viaje eliminado definitivamente');
    }

    /* Cargo los viajes con datatables server side para grandes cantidades de datos 
       Aplico filtros de busqueda
    */
     public function datatable(Request $request)
    {
        try {
            $isTrashed = (bool) $request->boolean('trashed', false);

            // Base query (Eloquent + relaciones)
            $q = Viaje::query()->with([
                'carro:idcarro,placa',
                'origen:idciudad,nombre',
                'destino:idciudad,nombre',
            ]);

            if ($isTrashed) {
                $q->onlyTrashed();
            }

            /* ====== FILTROS ====== */
            $q->when($request->filled('car_id'), function ($qq) use ($request) {
                $qq->where('idcarro', (int) $request->get('car_id'));
            });

            $q->when($request->filled('origin_id'), function ($qq) use ($request) {
                $qq->where('idciudad_origen', (int) $request->get('origin_id'));
            });

            $q->when($request->filled('dest_id'), function ($qq) use ($request) {
                $qq->where('idciudad_destino', (int) $request->get('dest_id'));
            });

            $from = $request->get('date_from');
            $to   = $request->get('date_to');

            if ($from && $to) {
                $q->whereBetween('fecha', [$from.' 00:00:00', $to.' 23:59:59']);
            } elseif ($from) {
                $q->where('fecha', '>=', $from.' 00:00:00');
            } elseif ($to) {
                $q->where('fecha', '<=', $to.' 23:59:59');
            }
            /* ====== /FILTROS ====== */

            // Búsqueda global
            $search = trim($request->input('search.value', ''));
            if ($search !== '') {
                $q->where(function ($w) use ($search) {
                    $w->where('idviaje', 'like', "%{$search}%")
                      ->orWhere('tiempo_horas', 'like', "%{$search}%")
                      ->orWhere('fecha', 'like', "%{$search}%")
                      ->orWhereHas('carro',  fn($c) => $c->where('placa', 'like', "%{$search}%"))
                      ->orWhereHas('origen', fn($c) => $c->where('nombre','like', "%{$search}%"))
                      ->orWhereHas('destino',fn($c) => $c->where('nombre','like', "%{$search}%"));
                });
            }

            // Conteos
            $recordsTotal = $isTrashed ? Viaje::onlyTrashed()->count() : Viaje::count();
            $recordsFiltered = (clone $q)->count();

            // Orden (solo columnas simples para no hacer joins en ORDER BY)
            $orderable = [
                0 => 'idviaje',
                4 => 'tiempo_horas',
                5 => 'fecha',
            ];
            $orderColIdx = (int) data_get($request->input('order', []), '0.column', 0);
            $orderDir    = data_get($request->input('order', []), '0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
            $orderCol    = $orderable[$orderColIdx] ?? 'idviaje';
            $q->orderBy($orderCol, $orderDir);

            // Paginación
            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $rows   = $q->skip($start)->take($length)->get();

            // Construcción de filas
            $data = $rows->map(function (Viaje $v) use ($isTrashed) {
                $editUrl = route('viajes.edit', $v->idviaje);
                $delUrl  = route('viajes.destroy', $v->idviaje);
                $csrf    = csrf_token();

                $acciones = '
                    <a href="'.$editUrl.'" class="btn btn-secondary">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <form action="'.$delUrl.'" method="POST" style="display:inline-block" data-confirm="¿Enviar a papelera este viaje?">
                        <input type="hidden" name="_token" value="'.$csrf.'">
                        <input type="hidden" name="_method" value="DELETE">
                        <button class="btn btn-danger"><i class="fa-solid fa-trash"></i> Eliminar</button>
                    </form>
                ';

                if ($isTrashed) {
                    $acciones = '
                        <form action="'.route('viajes.restore', $v->idviaje).'" method="POST" data-confirm="¿Restaurar viaje?">
                            <input type="hidden" name="_token" value="'.$csrf.'">
                            <input type="hidden" name="_method" value="PATCH">
                            <button class="btn btn-primary"><i class="fa-solid fa-rotate-left"></i> Restaurar</button>
                        </form>
                        <form action="'.route('viajes.force', $v->idviaje).'" method="POST" data-confirm="¿Eliminar definitivamente? Esta acción no se puede deshacer.">
                            <input type="hidden" name="_token" value="'.$csrf.'">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Eliminar definitivo</button>
                        </form>
                    ';
                }

                return [
                    'idviaje'      => $v->idviaje,
                    'placa'        => $v->carro?->placa,
                    'origen'       => $v->origen?->nombre,
                    'destino'      => $v->destino?->nombre,
                    'tiempo_horas' => $v->tiempo_horas,
                    'fecha'        => $isTrashed
                        ? optional($v->deleted_at)->format('Y-m-d H:i:s')
                        : Carbon::parse($v->fecha)->format('Y-m-d H:i:s'),
                    'acciones'     => $acciones,
                ];
            });

            return response()->json([
                'draw'            => (int) $request->input('draw'),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data'            => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('viajes.datatable error', ['msg' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json([
                'draw' => (int) $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error interno',
            ], 500);
        }
    }

}
