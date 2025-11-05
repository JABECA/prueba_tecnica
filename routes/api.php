<?php 

use App\Http\Controllers\Api\ApiController;

Route::prefix('v1')->middleware('api.key')->group(function () {
    
    Route::get('vehiculos/{placa}/viajes', [ApiController::class, 'porPlaca'])->name('api.v1.viajes.porPlaca'); // 2.1
    Route::post('viajes', [ApiController::class, 'store'])->name('api.v1.viajes.store');  // 2.2
    Route::match(['put','patch'], 'vehiculos/{placa}/color', [ApiController::class, 'updateColor']); // 2.3

});


