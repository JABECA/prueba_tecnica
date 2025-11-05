@extends('layouts.app')
@section('content')
<div class="card">
  <h2 style="margin:0 0 12px 0;">Promedio horas de viajes realizados del carro {{ $placa }} y fecha de registro</h2>

  <p style="margin:0 0 8px 0;">
    <strong>Fecha de registro:</strong>
    {{ $fechaIngreso ? \Carbon\Carbon::parse($fechaIngreso)->format('Y-m-d H:i:s') : '—' }}
  </p>

  <p style="font-size:1.2rem; margin:0;">
    <strong>Promedio de horas:</strong>
    {{ $promedio !== null ? $promedio : 'Sin viajes registrados' }}
  </p>
</div>
@endsection
