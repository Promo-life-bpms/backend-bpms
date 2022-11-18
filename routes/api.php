<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\IncidenciaController;
use App\Models\Incidencia;
use Illuminate\Http\Request;
use App\Http\Controllers\Ventas\VentasController;
use App\Http\Controllers\Control\ControlController;
use App\Http\Controllers\LM\LMController;
use App\Http\Controllers\Chofer\ChoferController;
use App\Http\Controllers\Maquilador\MaquiladorController;
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


Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/Incidencia', [IncidenciaController::class, 'index']);

Route::post('/Incidencia', [IncidenciaController::class, 'store']);

Route::put('/Incidencia', [IncidenciaController::class, 'update']);

Route::delete('/Incidencia', [IncidenciaController::class, 'destroy']);

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
Route::get('pedidosAL/{pedido}', [ControlController::class, 'pedidoAl']);
Route::get('FormatodeInspeccion', [ControlController::class, 'FormatodeInspeccion']);
Route::post('FirmaElaboroAceptar', [ControlController::class, 'FirmaElaboroAceptar']);
Route::post('FirmaElaboroLimpiar', [ControlController::class, 'FirmaElaboroLimpiar']);
Route::post('FirmaRevisoAceptar', [ControlController::class, 'FirmaRevisoAceptar']);
Route::post('FirmaRevisoLimpiar', [ControlController::class, 'FirmaRevisoLimpiar']);
Route::post('GuardarIns', [ControlController::class, 'GuardarIns']);

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

// RUTAS DE CHOFER
//Home chofer
Route::get('dashboardChofer', [ChoferController::class, 'dashboardChofer']);
//rutas de entrega pagina
Route::get('RutasdeEntrega', [ChoferController::class, 'RutasdeEntrega']);
Route::get('RutasdeEntrega/{pedido}', [ChoferController::class, 'PedidoEntrega']);
//botones
Route::get('Confirmar', [ChoferController::class, 'Confirmar']);
Route::get('Rechazar', [ChoferController::class, 'Rechazar']);
//Rutas de material limpio
Route::get('RutasdeMaterial', [ChoferController::class, 'RutasdeMaterial']);
Route::get('RutasdeEntrega', [ChoferController::class, 'RutasdeEntrega']);
Route::get('RutasdeMaterial/{pedido}', [ChoferController::class, 'PedidoMaterial']);
//Rutas de pedidos
Route::get('SeguimientoPedidos', [ChoferController::class, 'Seguimientopedidos']);
//RUTAS DEL MAQUILADOR
//Home de maquilador
Route::get('dashboardMaq', [MaquiladorController::class, 'dashboardMaq']);
Route::get('Remisiones', [MaquiladorController::class, 'Remisiones']);
Route::get('Remisiones/{pedido}', [MaquiladorController::class, 'DetallesR']);


//Rutas de asignacion para Roles


// Seccion de Incidencias

// Detalle de la incidencia con las compras relacionadas
// localhost/pedidos/PED456/incidencias/INC-423
Route::get('incidencias/{incidencia}', [SaleController::class, 'show']);
// Crear una incidencia
Route::get('incidencias/create', [SaleController::class, 'show']);
Route::post('incidencias/store', [SaleController::class, 'show']);

//------------------------------------------------------------

// Seccion de Rutas de entrega

// Detalle de la ruta_entrega con las compras relacionadas
// localhost/pedidos/PED456/ruta_entrega/INC-423
Route::get('ruta_entrega/{ruta_entrega}', [SaleController::class, 'show']);
// Crear una incidencia
Route::get('ruta_entrega/create', [SaleController::class, 'show']);
Route::post('ruta_entrega/store', [SaleController::class, 'show']);
