@extends('layouts.app')

@section('content')
@php
  $isTrashed = isset($trashed) && $trashed;
@endphp

<div class="card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px;">
    <h2 style="margin:0;">{{ $isTrashed ? 'Viajes eliminados' : 'Viajes' }}</h2>

    <div style="display:flex; gap:8px;">
      @if($isTrashed)
        <a class="btn btn-secondary" href="{{ route('viajes.index') }}">
          <i class="fa-solid fa-list"></i> Ver activos
        </a>
      @else
        <a class="btn btn-secondary" href="{{ route('viajes.trashed') }}">
          <i class="fa-solid fa-trash-can"></i> Ver eliminados
        </a>
        <a class="btn btn-primary" href="{{ route('viajes.create') }}">
          <i class="fa-solid fa-plus"></i> Nuevo viaje
        </a>
      @endif
    </div>
  </div>

{{-- ====== FILTROS ====== --}}
<div class="card" style="margin-bottom:12px;">
  <div class="filters">
    <div class="filter-item">
      <label for="f_car">Carro</label>
      <select id="f_car" class="input">
        <option value="">-- Todos --</option>
        @foreach($carros as $c)
          <option value="{{ $c->idcarro }}">{{ $c->placa }}</option>
        @endforeach
      </select>
    </div>

    <div class="filter-item">
      <label for="f_origin">Origen</label>
      <select id="f_origin" class="input">
        <option value="">-- Todos --</option>
        @foreach($ciudades as $c)
          <option value="{{ $c->idciudad }}">{{ $c->nombre }}</option>
        @endforeach
      </select>
    </div>

    <div class="filter-item">
      <label for="f_dest">Destino</label>
      <select id="f_dest" class="input">
        <option value="">-- Todos --</option>
        @foreach($ciudades as $c)
          <option value="{{ $c->idciudad }}">{{ $c->nombre }}</option>
        @endforeach
      </select>
    </div>

    {{-- Rango de fechas en su propia fila (sin scroll) --}}
    <div class="filter-item range-item">
      <label>Rango de fechas</label>
      <div class="range-row">
        <input id="f_from" type="date" class="input" placeholder="Desde">
        <input id="f_to"   type="date" class="input" placeholder="Hasta">
        <button id="f_reset" type="button" class="btn btn-secondary">Limpiar</button>
      </div>
    </div>
  </div>
</div>
{{-- ====== /FILTROS ====== --}}

  <div class="table-responsive">
    <table id="tbl-viajes" class="display nowrap" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Placa</th>
          <th>Origen</th>
          <th>Destino</th>
          <th>Tiempo (h)</th>
          <th>{{ $isTrashed ? 'Eliminado el' : 'Fecha' }}</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        {{-- Server-side: DataTables llena via AJAX --}}
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
  const $tbl = $('#tbl-viajes').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    ajax: {
      url: '{{ route('viajes.datatable') }}',
      type: 'GET',
      cache: false,
      data: function (d) {
        // Devolver un NUEVO objeto con los filtros incluidos evita problemas
        return $.extend({}, d, {
          trashed:  {{ (isset($trashed) && $trashed) ? 1 : 0 }},
          car_id:   $('#f_car').val()    || null,
          origin_id:$('#f_origin').val() || null,
          dest_id:  $('#f_dest').val()   || null,
          date_from:$('#f_from').val()   || null,
          date_to:  $('#f_to').val()     || null,
          _ts: Date.now() // anti-cache
        });
      }
    },
    order: [[0, 'desc']],
    columns: [
      { data: 'idviaje',      name: 'idviaje' },
      { data: 'placa',        name: 'placa',        orderable:false, defaultContent:'' },
      { data: 'origen',       name: 'origen',       orderable:false, defaultContent:'' },
      { data: 'destino',      name: 'destino',      orderable:false, defaultContent:'' },
      { data: 'tiempo_horas', name: 'tiempo_horas' },
      { data: 'fecha',        name: 'fecha' },
      { data: 'acciones',     name: 'acciones',     orderable:false, searchable:false, defaultContent:'' },
    ],
    language: { url: '{{ asset('vendor/datatables/i18n/es-ES.json') }}' }
  });

  // Disparar recarga cuando cambien los filtros
  $('#f_car, #f_origin, #f_dest, #f_from, #f_to')
    .on('change', function () { $tbl.draw(); });

  // Reset de filtros
  $('#f_reset').on('click', function(){
    $('#f_car').val('');
    $('#f_origin').val('');
    $('#f_dest').val('');
    $('#f_from').val('');
    $('#f_to').val('');
    $tbl.draw();
  });
});
</script>
@endpush
