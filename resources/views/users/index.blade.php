@extends('layouts.app')

@section('content')
<div class="card">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h2 style="margin:0;">
      {{ isset($trashed) && $trashed ? 'Usuarios eliminados' : 'Usuarios' }}
    </h2>

    <div style="display:flex; gap:8px;">
      @if(isset($trashed) && $trashed)
        <a class="btn btn-secondary" href="{{ route('users.index') }}">Ver activos</a>
      @else
        <a class="btn btn-secondary" href="{{ route('users.trashed') }}">Ver eliminados</a>
        <a class="btn btn-primary" href="{{ route('users.create') }}">Nuevo usuario</a>
      @endif
    </div>
  </div>

  <div class="table-responsive">
    <table id="tbl" class="display" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Estado</th>
          <th>{{ isset($trashed) && $trashed ? 'Eliminado el' : '—' }}</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>
              @if(isset($trashed) && $trashed)
                <span class="badge badge-danger">Inactivo</span>
              @else
                <span class="badge badge-success">Activo</span>
              @endif
            </td>
            <td>
              @if(isset($trashed) && $trashed)
                {{ optional($u->deleted_at)->format('Y-m-d H:i') }}
              @else
                —
              @endif
            </td>
            <td>
              @if(isset($trashed) && $trashed)
                <form action="{{ route('users.restore', $u->id) }}" method="POST" style="display:inline-block" data-confirm="¿Restaurar usuario?">
                  @csrf @method('PATCH')
                  <button class="btn btn-primary"><i class="fa-solid fa-rotate-left"></i> Restaurar</button>
                </form>
                <form action="{{ route('users.force', $u->id) }}" method="POST" style="display:inline-block" data-confirm="¿Eliminar definitivamente? Esta acción no se puede deshacer">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Eliminar definitivo</button>
                </form>
              @else
                <a class="btn btn-secondary" href="{{ route('users.edit', $u) }}">Editar</a>
                <form action="{{ route('users.destroy', $u) }}" method="POST" style="display:inline-block"  data-confirm="¿¿Enviar a papelera este usuario?">
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
