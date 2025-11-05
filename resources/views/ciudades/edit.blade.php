@extends('layouts.app')

@section('content')
<div class="card">
  <h2>Editar Ciudad #{{ $ciudad->idciudad }}</h2>

  <form method="POST" action="{{ route('ciudades.update', $ciudad) }}" class="grid">
    @csrf
    @method('PUT')

    <div>
      <label for="nombre">Nombre de la ciudad</label>
      <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $ciudad->nombre) }}" class="form-control" style="width:100%" required>
      @error('nombre') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label for="activo">
        <input type="checkbox" id="activo" name="activo" value="1" {{ old('activo', $ciudad->activo) ? 'checked' : '' }}>
        Activa
      </label>
    </div>

    <div>
      <button class="btn btn-primary">Actualizar</button>
      <a href="{{ route('ciudades.index') }}" class="btn btn-secondary">Volver</a>
    </div>
  </form>
</div>
@endsection
