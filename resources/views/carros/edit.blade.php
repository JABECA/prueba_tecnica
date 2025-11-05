@extends('layouts.app')

@section('content')
<div class="card">
  <h2>Editar Carro #{{ $carro->idcarro }}</h2>

  <form method="POST" action="{{ route('carros.update', $carro) }}" class="grid">
    @csrf
    @method('PUT')

    <div>
      <label for="placa">Placa</label>
      <input type="text" name="placa" id="placa" value="{{ old('placa', $carro->placa) }}" class="form-control" style="width:100%; text-transform:uppercase" required>
      @error('placa') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label for="color">Color</label>
      <input type="text" id="color" name="color" value="{{ old('color', $carro->color) }}" class="form-control" style="width:100%">
      @error('color') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label for="fecha_ingreso">Fecha de ingreso</label>
      <input type="datetime-local" name="fecha_ingreso"
             id="fecha_ingreso" 
             value="{{ old('fecha_ingreso', \Carbon\Carbon::parse($carro->fecha_ingreso)->format('Y-m-d\TH:i')) }}"
             class="form-control" style="width:100%">
      @error('fecha_ingreso') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <button class="btn btn-primary">Actualizar</button>
      <a href="{{ route('carros.index') }}" class="btn btn-secondary">Volver</a>
    </div>
  </form>
</div>
@endsection
