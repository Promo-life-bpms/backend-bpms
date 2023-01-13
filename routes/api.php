<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductRouteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DeliveryRouteController;
use App\Http\Controllers\IncidenceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiOdooController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\OrderPurchaseController;

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

Route::post('setSale/v1', [ApiOdooController::class, 'setSale']);
Route::post('setPurchase/v1', [ApiOdooController::class, 'setPurchase']);
Route::post('setIncidence/v1', [ApiOdooController::class, 'setIncidence']);
Route::post('setReception/v1', [ApiOdooController::class, 'setReception']);
Route::post('setDelivery/v1', [ApiOdooController::class, 'setDelivery']);

Route::get('users', [AuthController::class, 'allUsers']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('syncUsers', [AuthController::class, 'syncUsers']);

Route::group(['middleware' => 'auth'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);

    Route::get('pedidos', [SaleController::class, 'index']);
    Route::get('pedidos/{pedido}', [SaleController::class, 'show']);

    Route::post('pedido/{pedido}/inspections', [InspectionController::class, 'store']);
    Route::get('inspections/{inspection}', [InspectionController::class, 'show']);

    // Modal de un detalle de OC, OT
    // localhost/pedidos/PED456/orders/OC-568
    // localhost/pedidos/PED456/orders/OT-423
    // Route::get('pedidos/{pedido}/orders/{order}', [SaleController::class, 'show']);

    // Recepciones de Inventario
    Route::post('orders/{order}/receptions', [ReceptionController::class, 'saveReception']);

    Route::get('orders/{order}/receptions/{reception}', [ReceptionController::class, 'getReception']);



    // Seccion para actualizar el estatus de maquila

    // Seccion de Incidencias

    // Detalle de la incidencia con las compras relacionadas
    // localhost/pedidos/PED456/incidencias/INC-423
    Route::get('incidencias/{incidencia}', [IncidenceController::class, 'show']);
    // Crear una incidencia
    Route::post('pedido/{pedido}/incidencias/', [IncidenceController::class, 'store']);

    // Vista de status de incidencia
    Route::post('order/{compra}/updatestatus', [OrderPurchaseController::class, 'store']);

    Route::get('order/{compra}/updatestatus', [OrderPurchaseController::class, 'show']);

    // SECCION RUTAS DE ENTREGA

    // Tabla de rutas de entrega
    Route::get('rutas-de-entrega', [DeliveryRouteController::class, 'index']);

    // Crear una ruta de entrega
    // Leer pedidos por agendar
    Route::get('pedidos-por-agendar', [DeliveryRouteController::class, 'create']);
    // Guardar la ruta de entrega
    Route::post('rutas-de-entrega', [DeliveryRouteController::class, 'store']);
    //ver una ruta de entrega
    Route::get('rutas-de-entrega/show/{id}', [DeliveryRouteController::class, 'show']);

    // Actualizar la ruta de entrega
    Route::put('rutas-de-entrega/{ruta}/update', [DeliveryRouteController::class, 'update']);

    // Eliminar ruta de entrega
    Route::delete('rutas-de-entrega/{deliveryRoute}', [DeliveryRouteController::class, 'destroy']);

    // Crear una remision
    Route::post('rutas-de-entrega/{ruta}/remision', [DeliveryRouteController::class, 'setRemisiones']);
    // Ver remision
    // Route::get('remision/viewRemision', [DeliveryRouteController::class, 'viewRemision']);
    //show remision por id
    Route::get('rutas-de-entrega/{ruta}/remision/{id}', [DeliveryRouteController::class, 'showRemision']);
    //cancelar remision
    Route::put('rutas-de-entrega/{ruta}/cancel-remision/{id}', [DeliveryRouteController::class, 'cancelRemision']);


    //Ver pedidos de cada vendedor
    Route::get('pedidos/viewPedidosPorVendedor', [SaleController::class, 'viewPedidosPorVendedor']);
});
