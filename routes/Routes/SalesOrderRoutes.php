<?php

use App\Http\Controllers\SalesOrderController;

Route::get('pedidos', [SalesOrderController::class, 'index']);
Route::get('pedidos/{lead}', [SalesOrderController::class, 'show']);
