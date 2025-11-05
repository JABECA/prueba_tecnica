@extends('layouts.app')

@section('content')
<div class="card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h2 style="margin:0;">
      {{ isset($trashed) && $trashed ? 'Carros eliminados' : 'Carros' }}
    </h2>

    <div style="display:flex; gap:8px;">
      @if(isset($trashed) && $trashed)
        <a class="btn btn-secondary" href="{{ route('carros.index') }}">Ver activos</a>
      @else
        <a class="btn btn-secondary" href="{{ route('carros.trashed') }}">Ver eliminados</a>
        <a class="btn btn-primary" href="{{ route('carros.create') }}">Nuevo carro</a>
      @endif
    </div>
  </div>

  <div class="table-responsive">
    <table id="tbl" class="display" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Placa</th>
          <th>Color</th>
          <th>Estado</th>
          <th>{{ isset($trashed) && $trashed ? 'Eliminado el' : 'Fecha ingreso' }}</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($carros as $c)
          <tr>
            <td>{{ $c->idcarro }}</td>
            <td>{{ $c->placa }}</td>
            <td>{{ $c->color }}</td>
            <td>
              @if(isset($trashed) && $trashed)
                <span class="badge badge-danger">Inactivo</span>
              @else
                <span class="badge badge-success">Activo</span>
              @endif
            </td>
            <td>
              @if(isset($trashed) && $trashed)
                {{ optional($c->deleted_at)->format('Y-m-d H:i') }}
              @else
                {{ $c->fecha_ingreso }}
              @endif
            </td>
            <td>
              @if(isset($trashed) && $trashed)
                <form action="{{ route('carros.restore', $c->idcarro) }}" method="POST" style="display:inline-block" data-confirm="¿Restaurar carro?">
                  @csrf @method('PATCH')
                  <button class="btn btn-primary"><i class="fa-solid fa-rotate-left"></i> Restaurar</button>
                </form>
                <form action="{{ route('carros.force', $c->idcarro) }}" method="POST" style="display:inline-block" data-confirm="¿Eliminar definitivamente? Esta acción no se puede deshacer">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Eliminar definitivo</button>
                </form>
              @else
                <a class="btn btn-secondary" href="{{ route('carros.edit', $c) }}">Editar</a>
                <form action="{{ route('carros.destroy', $c) }}" method="POST" style="display:inline-block" data-confirm="¿¿Enviar a papelera este carro?">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger"><i class="fa-solid fa-trash"></i> Eliminar</button>
                </form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<!-- <script>$(function(){ $('#tbl').DataTable(); });</script> -->
<script>$(function(){ $('#tbl').DataTable({responsive: true, autoWidth: false, language: { url: '{{ asset('vendor/datatables/i18n/es-ES.json') }}' }}); });</script>
@endpush
