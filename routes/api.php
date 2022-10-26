<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Ventas\VentasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//rutas de inicio de sesion
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('users', [AuthController::class, 'allUsers']);

// rutas de ventas

// pagina principal
Route::get('dashboard', [VentasController::class, 'dashboard']);

// incidencias
Route::get('incidencias', [VentasController::class, 'incidencias']);
Route::get('incidencias/{pedido}', [VentasController::class, 'showIncidencia']);

Route::post('aprobarInc', [VentasController::class, 'aprobarInc']);
Route::post('rechazarInc', [VentasController::class, 'rechazarInc']);

// seguimiento de pedido
Route::get('pedidos', [VentasController::class, 'pedidos']);

// pedido
Route::get('pedidos/{pedido}', [VentasController::class, 'pedido']);
