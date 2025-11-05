@extends('layouts.app')

@section('content')
<div class="card">
  <h2>Editar Viaje #{{ $viaje->idviaje }}</h2>
  <form method="POST" action="{{ route('viajes.update', $viaje) }}" class="grid">
    @csrf @method('PUT')

    <div>
      <label>Carro (placa)</label>
      <select name="idcarro" required class="form-control" style="width:100%">
        @foreach($carros as $c)
          <option value="{{ $c->idcarro }}" @selected($c->idcarro==$viaje->idcarro)>{{ $c->placa }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label>Ciudad Origen</label>
      <select name="idciudad_origen" class="form-control" style="width:100%">
        <option value="">-- Sin origen --</option>
        @foreach($ciudades as $c)
          <option value="{{ $c->idciudad }}" @selected($c->idciudad==$viaje->idciudad_origen)>{{ $c->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label>Ciudad Destino</label>
      <select name="idciudad_destino" class="form-control" style="width:100%">
        <option value="">-- Sin destino --</option>
        @foreach($ciudades as $c)
          <option value="{{ $c->idciudad }}" @selected($c->idciudad==$viaje->idciudad_destino)>{{ $c->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label>Tiempo (horas)</label>
      <input type="number" name="tiempo_horas" value="{{ $viaje->tiempo_horas }}" min="0" class="form-control" style="width:100%">
    </div>

    <div>
      <label>Fecha</label>
      <input type="datetime-local" name="fecha" value="{{ \Carbon\Carbon::parse($viaje->fecha)->format('Y-m-d\TH:i') }}" class="form-control" style="width:100%">
    </div>

    <div>
      <button class="btn btn-primary">Actualizar</button>
      <a class="btn btn-secondary" href="{{ route('viajes.index') }}">Volver</a>
    </div>
  </form>
</div>
@endsection
