@extends('layouts.app')

@section('content')
<div class="card">
  <h2>Nuevo Viaje</h2>
  <form method="POST" action="{{ route('viajes.store') }}" class="grid">
    @csrf
    <div>
      <label for="idcarro" >Carro (placa)</label>
      <select name="idcarro" id="idcarro" required class="form-control" style="width:100%">
        <option value="">-- Selecciona --</option>
        @foreach($carros as $c)
          <option value="{{ $c->idcarro }}">{{ $c->placa }}</option>
        @endforeach
      </select>
      @error('idcarro') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label for="idciudad_origen" >Ciudad Origen</label>
      <select name="idciudad_origen" id="idciudad_origen" class="form-control" style="width:100%">
        <option value="">-- Sin origen --</option>
        @foreach($ciudades as $c)
          <option value="{{ $c->idciudad }}">{{ $c->nombre }}</option>
        @endforeach
      </select>
      @error('idciudad_origen') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label for="idciudad_destino" >Ciudad Destino</label>
      <select name="idciudad_destino" id="idciudad_destino" class="form-control" style="width:100%">
        <option value="">-- Sin destino --</option>
        @foreach($ciudades as $c)
          <option value="{{ $c->idciudad }}">{{ $c->nombre }}</option>
        @endforeach
      </select>
      @error('idciudad_destino') <small style="color:#e02424">{{ $message }}</small> @enderror
    </div>

    <div>
      <label for="tiempo_horas" >Tiempo (horas)</label>
      <input type="number" id="tiempo_horas" name="tiempo_horas" min="0" class="form-control" style="width:100%">
    </div>

    <div>
      <label for="fecha" >Fecha</label>
      <input type="datetime-local" id="fecha" name="fecha" class="form-control" style="width:100%">
    </div>

    <div>
      <button class="btn btn-primary">Guardar</button>
      <a class="btn btn-secondary" href="{{ route('viajes.index') }}">Volver</a>
    </div>
  </form>
</div>
@endsection
