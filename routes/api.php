<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\Ventas\VentasController;
use App\Http\Controllers\Control\ControlController;
use App\Http\Controllers\LM\LMController;
use App\Http\Controllers\Chofer\ChoferController;
use App\Http\Controllers\Maquilador\MaquiladorController;
use App\Http\Controllers\ProductRouteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DeliveryRouteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesOrderProductsController;


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

include('Routes/ApiOdooRoutes.php');

//rutas de inicio de sesion
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('syncUsers', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    /*     Route::get('dashboard', [HomeController::class, 'dashboard']);
    Route::get('/Incidencia', [IncidenciaController::class, 'index']);

    Route::post('/Incidencia', [IncidenciaController::class, 'store']);

    Route::put('/Incidencia', [IncidenciaController::class, 'update']);

    Route::delete('/Incidencia', [IncidenciaController::class, 'destroy']);

    // RUTAS DE VENTAS
    // pagina principal
    //Route::get('dashboard', [VentasController::class, 'dashboard']);
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
    //Route::get('dashboardCon', [ControlController::class, 'dashboardCon']);
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
    //Route::get('dashboardLM', [LMController::class, 'dashboardLM']);
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
    //Route::get('dashboardChofer', [ChoferController::class, 'dashboardChofer']);
    //rutas de entrega pagina
    Route::get('RutasdeEntrega', [ChoferController::class, 'RutasdeEntrega']);
    Route::get('RutasdeEntrega/{pedido}', [ChoferController::class, 'PedidoEntrega']);
    //botones
    Route::post('Confirmar', [ChoferController::class, 'Confirmar']);
    Route::post('Rechazar', [ChoferController::class, 'Rechazar']);
    //Rutas de material limpio
    Route::get('RutasdeMaterial', [ChoferController::class, 'RutasdeMaterial']);

    Route::get('RutasdeMaterial/{pedido}', [ChoferController::class, 'PedidoMaterial']);
    //Rutas de pedidos
    Route::get('SeguimientoPedidos', [ChoferController::class, 'Seguimientopedidos']);
    //RUTAS DEL MAQUILADOR
    //Home de maquilador

    //Route::get('dashboardMaq', [MaquiladorController::class, 'dashboardMaq']);
    Route::get('Remisiones', [MaquiladorController::class, 'Remisiones']);
    Route::get('Remisiones/{pedido}', [MaquiladorController::class, 'DetallesR']);

    // Rutas Generales
    Route::get('pedidos', [SaleController::class, 'index']);
    Route::get('pedidos/{pedido}', [SaleController::class, 'show']);
    Route::get('pedidos-por-agendar', [ProductRouteController::class, 'productsToSchedule']); */


    Route::get('pedidos', [SaleController::class, 'index']);
    Route::get('pedidos/{pedido}', [SaleController::class, 'show']);
});

Route::get('users', [AuthController::class, 'allUsers']);



// Modal de un detalle de OC, OT
// localhost/pedidos/PED456/orders/OC-568
// localhost/pedidos/PED456/orders/OT-423
Route::get('pedidos/{pedido}/orders/{order}', [SaleController::class, 'show']);


// Seccion para actualizar el estatus de maquila

// Seccion de Incidencias

// Detalle de la incidencia con las compras relacionadas
// localhost/pedidos/PED456/incidencias/INC-423
Route::get('incidencias/{incidencia}', [SaleController::class, 'show']);

// Crear una incidencia
Route::get('incidencias/create', [SaleController::class, 'show']);
Route::post('incidencias/store', [SaleController::class, 'show']);


// Seccion de Inspeccion de Calidad

// Detalle de la Incidencia
// localhost/pedidos/PED456/inspeccion/INS-7688
Route::get('inspections/{inspection}', [SaleController::class, 'show']);

// Crear una inspeccion de calidad
Route::get('inspections/create', [SaleController::class, 'show']);
Route::post('inspections/store', [SaleController::class, 'show']);

// Actualizar una inspeccion de calidad
Route::get('inspections/{inspection}/edit', [SaleController::class, 'show']);
Route::put('inspections/{inspection}/update', [SaleController::class, 'show']);

// SECCION RUTAS DE ENTREGA

// Tabla de rutas de entrega
Route::get('rutas-de-entrega', [DeliveryRouteController::class, 'index']);

// Crear una ruta de entrega
// Leer pedidos por agendar
Route::get('rutas-de-entrega/create', [DeliveryRouteController::class, 'create']);
// Guardar la ruta de entrega
Route::post('rutas-de-entrega/store', [DeliveryRouteController::class, 'store']);
//ver una ruta de entrega
Route::get('rutas-de-entrega/show', [DeliveryRouteController::class, 'show']);
// Editar una ruta de entrega
// Leer informacion de la ruta de entrega
Route::get('rutas-de-entrega/{ruta}/edit', [DeliveryRouteController::class, 'productsToSchedule']);
// Actualizar la ruta de entrega
Route::put('rutas-de-entrega/{ruta}/update', [DeliveryRouteController::class, 'productsToSchedule']);

// Eliminar ruta de entrega
// Actualizar la ruta de entrega
Route::delete('rutas-de-entrega/{ruta}', [ProductRouteController::class, 'productsToSchedule']);

// Detalle de una ruta de entrega
Route::get('rutas-de-entrega/{ruta}', [ProductRouteController::class, 'productsToSchedule']);
Route::get('rutas-de-entrega/{ruta}/', [ProductRouteController::class, 'productsToSchedule']);
