@extends('layouts.app')

@section('content')
<div class="card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h2 style="margin:0;">
      {{ isset($trashed) && $trashed ? 'Ciudades eliminadas' : 'Ciudades' }}
    </h2>

    <div style="display:flex; gap:8px;">
      @if(isset($trashed) && $trashed)
        <a class="btn btn-secondary" href="{{ route('ciudades.index') }}">Ver activas</a>
      @else
        <a class="btn btn-secondary" href="{{ route('ciudades.trashed') }}">Ver eliminadas</a>
        <a class="btn btn-primary" href="{{ route('ciudades.create') }}">Nueva ciudad</a>
      @endif
    </div>
  </div>

  <div class="table-responsive">
    <table id="tbl" class="display" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Estado</th>
          <th>{{ isset($trashed) && $trashed ? 'Eliminada el' : 'Activo' }}</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($ciudades as $c)
          <tr>
            <td>{{ $c->idciudad }}</td>
            <td>{{ $c->nombre }}</td>
            <td>
              @if(isset($trashed) && $trashed)
                <span class="badge badge-danger">Inactiva</span>
              @else
                @if($c->activo)
                  <span class="badge badge-success">Activa</span>
                @else
                  <span class="badge badge-warning">Deshabilitada</span>
                @endif
              @endif
            </td>
            <td>
              @if(isset($trashed) && $trashed)
                {{ optional($c->deleted_at)->format('Y-m-d H:i') }}
              @else
                {{ $c->activo ? 'Sí' : 'No' }}
              @endif
            </td>
            <td>
              @if(isset($trashed) && $trashed)
                <form action="{{ route('ciudades.restore', $c->idciudad) }}" method="POST" style="display:inline-block" data-confirm="¿Restaurar ciudad?">
                  @csrf @method('PATCH')
                  <button class="btn btn-primary"><i class="fa-solid fa-rotate-left"></i> Restaurar</button>
                </form>
                <form action="{{ route('ciudades.force', $c->idciudad) }}" method="POST" style="display:inline-block" data-confirm="¿Eliminar definitivamente? Esta acción no se puede deshacer.">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Eliminar definitivo</button>
                </form>
              @else
                <a class="btn btn-secondary" href="{{ route('ciudades.edit', $c) }}">Editar</a>
                <form action="{{ route('ciudades.destroy', $c) }}" method="POST" style="display:inline-block" data-confirm="¿¿Enviar a papelera esta ciudad?">
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
<script>$(function(){ $('#tbl').DataTable({responsive: true, autoWidth: false, language: { url: '{{ asset('vendor/datatables/i18n/es-ES.json') }}' }}); });</script>
@endpush
