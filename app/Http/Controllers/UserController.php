<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller
{
    // Protege todo el controlador con auth 
    public static function middleware(): array
    {
        return [ new Middleware('auth') ];
    }

    // cargo la vista principal de usuarios con Datatable
    public function index()
    {
        $users = User::orderByDesc('id')->whereNull('deleted_at')->get();
        return view('users.index', compact('users'));
    }

    // vista para crear usuarios
    public function create()
    {
        return view('users.create');
    }

    // almaceno el usuario que viene de la vista
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);
        return redirect()->route('users.index')->with('ok', 'Usuario creado');
    }

    // vista para la edicion del usuario
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // actualizo el usuario
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,'.$user->id.',id,deleted_at,NULL',
            'password'              => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('users.index')->with('ok', 'Usuario actualizado');
    }

    // eliminacion del usuario
    public function destroy(User $user)
    {
        // (opcional) evitar que un usuario se elimine a sí mismo
        if (auth()->id() === $user->id) {
            return back()->with('ok', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('ok', 'Usuario eliminado (Desactivado)');
    }

    // borrado suave del usuario
    public function trashed()
    {
        $users = User::onlyTrashed()->orderByDesc('id')->get();
        $trashed = true;
        return view('users.index', compact('users', 'trashed'));
    }

    // restauraciond el usuario tras un soft delete o borrado suave
    public function restore($id)
    {
        $user = User::onlyTrashed()->where('id', $id)->firstOrFail();
        $user->restore();
        return redirect()->route('users.trashed')->with('ok', 'Usuario restaurado');
    }

    // forzar borrado permanente
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->where('id', $id)->firstOrFail();

        // Evita autodestruirte definitivamente (opcional)
        if (auth()->id() === $user->id) {
            return back()->with('ok', 'No puedes eliminarte definitivamente a ti mismo.');
        }

        $user->forceDelete();
        return redirect()->route('users.trashed')->with('ok', 'Usuario eliminado definitivamente');
    }
}
