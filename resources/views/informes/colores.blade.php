@extends('layouts.app')
@section('content')
<div class="card">
  <h2 style="margin:0 0 12px 0;">Total Carros por color</h2>
  <div class="table-responsive">
    <table id="tbl" class="display" style="width:100%">
      <thead><tr><th>Color del carro</th><th>Total</th></tr></thead>
      <tbody>
        @foreach($rows as $r)
          <tr>
            <td>{{ $r->color ?? '—' }}</td>
            <td>{{ $r->total }}</td>
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
