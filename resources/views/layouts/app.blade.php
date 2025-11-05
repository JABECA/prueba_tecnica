<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'prueba_tecnica') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])

  <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

  {{-- DataTables --}}
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

  {{-- DataTables Responsive (opcional) --}}
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

  {{-- Font Awesome (iconos) --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  {{-- SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- estilos de app layout -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  
</head>
<body>
  <header>
    {{-- Hamburguesa (móvil) --}}
    <button class="btn-burger" id="btn-burger" aria-label="Abrir menú">
      <i class="fa-solid fa-bars"></i>
    </button>

    <!-- <div class="brand">Prueba Técnica</div> -->

    <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center ms-2">
      <img src="{{ asset('img/logo.png') }}" alt="Logo" style="height:35px; width:auto; margin-right:8px;">
    </a>

    <div style="margin-left:auto; display:flex; gap:10px; align-items:center; color:#fff">
      @auth
        <span>{{ auth()->user()->name }}</span>
        <form action="{{ route('logout') }}" method="POST">@csrf
          <button class="btn btn-secondary">Salir</button>
        </form>
      @endauth
    </div>
  </header>

  <div class="layout">
    <aside class="sidebar" id="sidebar">
      <h3 class="brand">Prueba Técnica</h3>
      <ul style="list-style:none; padding:0; margin:0;">
        <li><a href="{{ route('users.index') }}"    class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i><span>Usuarios</span></a></li>
        <li><a href="{{ route('ciudades.index') }}" class="nav-link {{ request()->routeIs('ciudades.*') ? 'active' : '' }}"><i class="fa-solid fa-city"></i><span>Ciudades</span></a></li>
        <li><a href="{{ route('carros.index') }}"   class="nav-link {{ request()->routeIs('carros.*') ? 'active' : '' }}"><i class="fa-solid fa-car-side"></i><span>Carros</span></a></li>
        <li><a href="{{ route('viajes.index') }}"   class="nav-link {{ request()->routeIs('viajes.*') ? 'active' : '' }}"><i class="fa-solid fa-route"></i><span>Viajes</span></a></li>
        <li style="margin-bottom:10px;">
          <a href="{{ route('informes.index') }}"
             class="nav-link {{ request()->routeIs('informes.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-column"></i><span>Informes</span>
          </a>
        </li>
      </ul>
    </aside>

    {{-- Overlay (solo móvil) --}}
    <div class="overlay" id="overlay"></div>

    <main>
      @if(session('ok')) <div class="flash">{{ session('ok') }}</div> @endif
      <script src="{{ asset('js/app.js') }}"></script>
      @yield('content')
    </main>
  </div>

 
  @stack('scripts')
</body>
</html>
