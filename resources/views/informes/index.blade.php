@extends('layouts.app')

@section('content')
<div class="card">
  <h2 style="margin:0 0 12px 0;">Informes Consultas</h2>
  <ul style="margin:0; padding-left:18px; line-height:1.9;">
    <li><a href="{{ route('informes.colores') }}">1) Carros por color</a></li>
    <li><a href="{{ route('informes.medellin') }}">2) Viajes desde Medellín (desde 2025-10-08)</a></li>
    <li><a href="{{ route('informes.promedio') }}">3) Promedio horas del carro BBB456</a></li>
    <li><a href="{{ route('informes.sinViajes') }}">4) Carros sin viajes</a></li>
    <li><a href="{{ route('informes.entreFechas') }}">5) Viajes entre 2025-09-26 y 2025-10-26</a></li>

    <li><a href="{{ route('informes.estadoCero') }}">6a) Viajes con ciudades origen o destino inactivas  (estado 0)</a></li>
    <li><a href="{{ route('informes.origenCero') }}">6b) Viajes con ciudades origen inactivas (estado 0)</a></li>
    <li><a href="{{ route('informes.destinoCero') }}">6c) Viajes con ciudades destino inactivas (estado 0)</a></li>
 
  </ul>
</div>
@endsection
