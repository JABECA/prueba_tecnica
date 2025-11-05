@extends('layouts.app')
@section('content')
<div class="card">
  <h2 style="margin:0 0 12px 0;">Vehículos que han hecho viajes desde la ciudad de “Medellin”, desde el dia {{ $desde }} en adelante</h2>
  <div class="table-responsive">
    <table id="tbl" class="display" style="width:100%">
      <thead>
        <tr><th>Placa</th><th>Destino</th><th>Tiempo (h)</th><th>Fecha</th></tr>
      </thead>
      <tbody>
        @foreach($rows as $r)
          <tr>
            <td>{{ $r->placa }}</td>
            <td>{{ $r->destino }}</td>
            <td>{{ $r->tiempo_horas }}</td>
            <td>{{ $r->fecha }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
@push('scripts')
<script>$(function(){ $('#tbl').DataTable({responsive:true, order:[[3,'desc']]}); });</script>
@endpush
