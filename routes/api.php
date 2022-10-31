<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Ventas\VentasController;
use App\Http\Controllers\Control\ControlController;
use App\Http\Controllers\LM\LMController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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

// RUTAS DE VENTAS

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

// RUTAS DE CALIDAD_CONTROL
Route::get('dashboardCon', [ControlController::class, 'dashboardCon']);

//segpedidos
Route::get('segpedidos', [ControlController::class, 'segpedidos']);
//almacen
Route::get('almacen', [ControlController::class, 'almacen']);
Route::get('almacen/{pedido}', [ControlController::class, 'almacenShow']);
// pedido
Route::get('pedidosAL/{pedido}', [ControlController::class, 'pedidoAl']);

// RUTAS DE lOGISTICA Y MESA DE CONTROL (LM)
Route::get('dashboardLM', [LMController::class, 'dashboardLM']);
//rutas
Route::get('rutasen', [LMController::class, 'rutasen']);
Route::get('rutasen/{pedido}', [LMController::class, 'pedidoentrega']);
Route::get('rutasmat', [LMController::class, 'rutasmat']);
Route::get('rutasmat/{pedido}', [LMController::class, 'pedidomaterial']);
//pedidos
Route::get('pedidos', [LMController::class, 'pedidos']);
Route::get('pedidosLM/{pedido}', [LMController::class, 'pedidoLM']);

