<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\Middleware;


class CiudadController extends Controller
{

    public static function middleware(): array
    {
        // Protege el controlador con auth
        return [
            new Middleware('auth'),
        ];
    }


    public function index() {
        $ciudades = Ciudad::orderBy('idciudad','desc')->whereNull('deleted_at')->get();
        return view('ciudades.index', compact('ciudades'));
    }

    public function create() { return view('ciudades.create'); }

    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|max:45|unique:ciudades,nombre',
            'activo' => 'nullable|boolean',
        ]);
        $data['activo'] = $request->boolean('activo');
        Ciudad::create($data);
        return redirect()->route('ciudades.index')->with('ok','Ciudad creada');
    }

    public function edit(Ciudad $ciudade) {
        return view('ciudades.edit', ['ciudad'=>$ciudade]);
    }

    public function update(Request $request, Ciudad $ciudade) {
        $data = $request->validate([
            'nombre' => 'required|string|max:45|unique:ciudades,nombre,'.$ciudade->idciudad.',idciudad',
            'activo' => 'nullable|boolean',
        ]);
        $data['activo'] = $request->boolean('activo');
        $ciudade->update($data);
        return redirect()->route('ciudades.index')->with('ok','Ciudad actualizada');
    }

    public function destroy(Ciudad $ciudade) {

        $tieneUsos = $ciudade->viajesOrigen()->exists() || $ciudade->viajesDestino()->exists();  // Evito borrar si la ciudad está usada en algún viaje
        if ($tieneUsos) { // si la validacion es verdadera no elimino la ciudad y regreso a la vista ciudades
            return back()->with('ok', 'No se puede eliminar: la ciudad está referenciada en viajes.');
        }
        $ciudade->delete(); // si tieneUsos es false elimino la ciudad
        return redirect()->route('ciudades.index')->with('ok','Ciudad eliminada (desactivada)'); // regreso a la vista principal de ciudades
    }


    public function trashed()
    {
        $ciudades = Ciudad::onlyTrashed()->orderByDesc('idciudad')->get();
        $trashed = true;
        return view('ciudades.index', compact('ciudades', 'trashed'));
    }

    public function restore($id)
    {
        $ciudad = Ciudad::onlyTrashed()->where('idciudad', $id)->firstOrFail();
        $ciudad->restore();
        return redirect()->route('ciudades.trashed')->with('ok', 'Ciudad restaurada');
    }

    public function forceDelete($id)
    {
        $ciudad = Ciudad::onlyTrashed()->where('idciudad', $id)->firstOrFail();

        // Si está referenciada en viajes, no permitir borrado
        $tieneUsos = $ciudad->viajesOrigen()->exists() || $ciudad->viajesDestino()->exists();
        if ($tieneUsos) {
            return back()->with('ok', 'No se puede eliminar definitivamente: la ciudad está referenciada en viajes.');
        }

        $ciudad->forceDelete();
        return redirect()->route('ciudades.trashed')->with('ok', 'Ciudad eliminada definitivamente');
    }

}
