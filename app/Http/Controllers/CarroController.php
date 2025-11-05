<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;


use Illuminate\Routing\Controllers\Middleware;

class CarroController extends Controller
{
    

    public static function middleware(): array
    {
        // Protejo el controlador con auth
        return [
            new Middleware('auth'),
        ];
    }

    public function index() {
        $carros = Carro::orderBy('idcarro','desc')->whereNull('deleted_at')->get();
        return view('carros.index', compact('carros'));
    }

    public function create() { return view('carros.create'); }

    public function store(Request $request) {
        $data = $request->validate([
            'placa' => [
                'required','string','max:45',
                'regex:/^(?:[A-Z]{3}\d{3}|[A-Z]{3}\d{2}[A-Z])$/',
                'unique:carros,placa',
                ],
            'color' => 'nullable|string|max:45',
            'fecha_ingreso' => 'nullable|date',
        ]);
        $data['placa'] = Str::upper(trim($data['placa'])); // Normalizo la placa sin espacion y en mayusculas
        Carro::create($data);
        return redirect()->route('carros.index')->with('ok','Carro creado');
    }

    public function edit(Carro $carro) {
        return view('carros.edit', compact('carro'));
    }

    public function update(Request $request, Carro $carro) {
        
        // valido campos del formulario
        $data = $request->validate([
            'placa' => [
                'required','string','max:45',
                'regex:/^(?:[A-Z]{3}\d{3}|[A-Z]{3}\d{2}[A-Z])$/',
                'unique:carros,placa,' . $carro->idcarro . ',idcarro',
            ],
            'color' => 'nullable|string|max:45',
            'fecha_ingreso' => 'nullable|date',
        ]);
        $data['placa'] = Str::upper(trim($data['placa'])); // Normalizo la placa sin espacion y en mayusculas
        $carro->update($data); // actualizo el carro enviado en el request
        return redirect()->route('carros.index')->with('ok','Carro actualizado'); // redirecciono a la vista proncipal de carros
    }

    public function destroy(Carro $carro) {

        // valido no borrar el vehiculo si tiene viajes realizados
        if ($carro->viajes()->exists()) {
            return back()->with('ok', 'No se puede eliminar: el carro tiene viajes asociados.');
        }
        try {
            $carro->delete(); // borro el vehiculo si no tiene viajes realizados
            return redirect()->route('carros.index')->with('ok','Carro eliminado (Desactivado)'); // redirecciono a la vista principal de carros
        } catch (QueryException $e) {
            return back()->with('ok', 'No se puede eliminar el carro por restricciones de integridad.'); // Por si el llave foranea bloquea
        }

    }

    public function trashed()
    {
        // Solo eliminados 
        $carros = Carro::onlyTrashed()->orderByDesc('idcarro')->get();
        $trashed = true; // bandera para la vista
        return view('carros.index', compact('carros', 'trashed'));
    }

    public function restore($id)
    {
        $carro = Carro::onlyTrashed()->where('idcarro', $id)->firstOrFail();
        $carro->restore();
        return redirect()->route('carros.trashed')->with('ok', 'Carro restaurado');
    }

    public function forceDelete($id)
    {
        $carro = Carro::onlyTrashed()->where('idcarro', $id)->firstOrFail();

        // Seguridad: si tiene viajes (históricos) no permitir purga permanente (opcional)
        if ($carro->viajes()->exists()) {
            return back()->with('ok', 'No se puede eliminar definitivamente: el carro tiene viajes asociados.');
        }

        $carro->forceDelete();
        return redirect()->route('carros.trashed')->with('ok', 'Carro eliminado definitivamente');
    }

}
