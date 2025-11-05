@extends('layouts.app')
@section('content')
<div class="card">
  <h2 style="margin:0 0 12px 0;">Carros que aun no tienen viajes registrados</h2>
  <div class="table-responsive">
    <table id="tbl" class="display" style="width:100%">
      <thead><tr><th>Placa</th><th>Color</th><th>Fecha ingreso</th></tr></thead>
      <tbody>
        @foreach($rows as $r)
          <tr>
            <td>{{ $r->placa }}</td>
            <td>{{ $r->color }}</td>
            <td>{{ $r->fecha_ingreso }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
@push('scripts')
<script>$(function(){ $('#tbl').DataTable({responsive:true}); });</script>
@endpush
