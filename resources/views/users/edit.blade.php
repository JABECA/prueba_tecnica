@extends('layouts.app')

@section('content')
<div class="card">
  <h2>Editar Usuario #{{ $user->id }}</h2>

  <form method="POST" action="{{ route('users.update', $user) }}" class="grid">
    @csrf
    @method('PUT')

    <div>
      <label>Nombre</label>
      <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" style="width:100%" required>
      @error('name') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" style="width:100%" required>
      @error('email') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label>Nueva contraseña (opcional)</label>
      <input type="password" name="password" class="form-control" style="width:100%">
      @error('password') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label>Confirmar nueva contraseña</label>
      <input type="password" name="password_confirmation" class="form-control" style="width:100%">
    </div>

    <div>
      <button class="btn btn-primary">Actualizar</button>
      <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver</a>
    </div>
  </form>
</div>
@endsection
