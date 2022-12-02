<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductRouteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DeliveryRouteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiOdooController;
use App\Http\Controllers\InspectionController;

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

Route::get('users', [AuthController::class, 'allUsers']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('syncUsers', [AuthController::class, 'syncUsers']);

Route::group(['middleware' => 'auth'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);

    Route::get('pedidos', [SaleController::class, 'index']);
    Route::get('pedidos/{pedido}', [SaleController::class, 'show']);
});





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

// Detalle de la inspeccion
// localhost/pedidos/PED456/inspeccion/INS-7688
Route::get('inspections/{inspection}', [InspectionController::class, 'show']);

// Crear una inspeccion de calidad
Route::post('inspections', [InspectionController::class, 'store']);

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
