<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\CiudadController;
use App\Http\Controllers\CarroController;
use App\Http\Controllers\ViajeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InformeController;


Route::get('/dashboard', function () {
    return redirect()->route('viajes.index');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/', function () {
    return redirect()->route('viajes.index');
});



Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('ciudades', CiudadController::class)->except(['show']);
    Route::resource('carros', CarroController::class)->except(['show']);
    Route::resource('viajes', ViajeController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);

    Route::get('carros/trashed', [\App\Http\Controllers\CarroController::class, 'trashed'])->name('carros.trashed');
    Route::patch('carros/{id}/restore', [\App\Http\Controllers\CarroController::class, 'restore'])->name('carros.restore');
    Route::delete('carros/{id}/force', [\App\Http\Controllers\CarroController::class, 'forceDelete'])->name('carros.force');

    Route::get('ciudades/trashed', [\App\Http\Controllers\CiudadController::class, 'trashed'])->name('ciudades.trashed');
    Route::patch('ciudades/{id}/restore', [\App\Http\Controllers\CiudadController::class, 'restore'])->name('ciudades.restore');
    Route::delete('ciudades/{id}/force', [\App\Http\Controllers\CiudadController::class, 'forceDelete'])->name('ciudades.force');

    Route::get  ('viajes/trashed',         [\App\Http\Controllers\ViajeController::class, 'trashed'])->name('viajes.trashed');
    Route::patch('viajes/{id}/restore',    [\App\Http\Controllers\ViajeController::class, 'restore'])->name('viajes.restore');
    Route::delete('viajes/{id}/force',     [\App\Http\Controllers\ViajeController::class, 'forceDelete'])->name('viajes.force');
    Route::get('viajes/datatable',         [\App\Http\Controllers\ViajeController::class, 'datatable'])->name('viajes.datatable');

    Route::get('users/trashed', [\App\Http\Controllers\UserController::class, 'trashed'])->name('users.trashed');
    Route::patch('users/{id}/restore', [\App\Http\Controllers\UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force', [\App\Http\Controllers\UserController::class, 'forceDelete'])->name('users.force');


    Route::get('informes',                [InformeController::class, 'index'])->name('informes.index');
    Route::get('informes/colores',        [InformeController::class, 'colores'])->name('informes.colores');
    Route::get('informes/medellin',       [InformeController::class, 'medellin'])->name('informes.medellin');
    Route::get('informes/promedio-bbb456',[InformeController::class, 'promedioCarro'])->name('informes.promedio');
    Route::get('informes/sin-viajes',     [InformeController::class, 'sinViajes'])->name('informes.sinViajes');
    Route::get('informes/entre-fechas',   [InformeController::class, 'entreFechas'])->name('informes.entreFechas');
    Route::get('informes/ciudades-estado0',[InformeController::class, 'ciudadesEstadoCero'])->name('informes.estadoCero');
    Route::get('informes/ciudad-origen-estado0',  [InformeController::class, 'ciudadesOrigenCero'])->name('informes.origenCero');
    Route::get('informes/ciudad-destino-estado0', [InformeController::class, 'ciudadesDestinoCero'])->name('informes.destinoCero');


});

require __DIR__.'/auth.php';
