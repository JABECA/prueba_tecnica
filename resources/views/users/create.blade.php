@extends('layouts.app')

@section('content')
<div class="card">
  <h2>Nuevo Usuario</h2>

  <form method="POST" action="{{ route('users.store') }}" class="grid">
    @csrf

    <div>
      <label>Nombre</label>
      <input type="text" name="name" value="{{ old('name') }}" class="form-control" style="width:100%" required>
      @error('name') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label>Email</label>
      <input type="email" name="email" value="{{ old('email') }}" class="form-control" style="width:100%" required>
      @error('email') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label>Contraseña</label>
      <input type="password" name="password" class="form-control" style="width:100%" required>
      @error('password') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label>Confirmar contraseña</label>
      <input type="password" name="password_confirmation" class="form-control" style="width:100%" required>
    </div>

    <div>
      <button class="btn btn-primary">Guardar</button>
      <a href="{{ route('users.index') }}" class="btn btn-secondary">Volver</a>
    </div>
  </form>
</div>
@endsection
