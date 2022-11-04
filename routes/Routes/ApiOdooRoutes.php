<?php

use App\Http\Controllers\ApiOdoo\ApiOdooController;

Route::post('setSale/v1', [ApiOdooController::class, 'setSale']);
Route::post('setPurchase/v1', [ApiOdooController::class, 'setPurchase']);


