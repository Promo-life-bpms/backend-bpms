<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductRouteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DeliveryRouteController;
use App\Http\Controllers\IncidenceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiOdooController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\OrderPurchaseController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SmallBoxUserController;
use App\Http\Controllers\SpentController;
use App\Http\Controllers\UploadImageController;
use App\Http\Controllers\UserCenterController;
use App\Notifications\Acces;
use App\Models\User;

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
Route::post('setTracking/v1', [ApiOdooController::class, 'setTracking']);

Route::get('users', [AuthController::class, 'allUsers']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('syncUsers', [AuthController::class, 'syncUsers']);
//Acceso
Route::get('Acces', [AuthController::class, 'Acces']);
Route::get('userAccess', [AuthController::class, 'userAccess']);


Route::group(['middleware' => 'auth'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);

    Route::get('pedidos', [SaleController::class, 'index']);
    //estadisticas
    Route::get('estadistica', [SaleController::class, 'estadisticas']);
    //calendario
    Route::get('calendario', [SaleController::class, 'calendario']);

    Route::get('pedidos/{pedido}', [SaleController::class, 'show']);

    Route::post('pedido/{pedido}/inspections', [InspectionController::class, 'store']);
    Route::get('inspections/{inspection}', [InspectionController::class, 'show']);

    // Detalle de  OC, OT
    Route::get('pedidos/{pedido}/orders/{order}', [OrderPurchaseController::class, 'show']);

    // Recepciones de Inventario
    Route::post('orders/{order}/receptions', [ReceptionController::class, 'saveReception']);

    Route::get('orders/{order}/receptions/{reception}', [ReceptionController::class, 'getReception']);



    // Seccion para actualizar el estatus de maquila

    // Seccion de Incidencias

    // Detalle de la incidencia con las compras relacionadas
    // localhost/pedidos/PED456/incidencias/INC-423
    Route::get('incidencias/{incidencia}', [IncidenceController::class, 'show']);
    // Crear una incidencia
    Route::post('pedidos/{pedido}/incidencia', [IncidenceController::class, 'store']);
    Route::patch('incidencias/{incidencia}', [IncidenceController::class, 'update']);

    // Vista de status de incidencia
    Route::post('order/{compra}/updatestatus', [OrderPurchaseController::class, 'store']);

    // Route::get('order/{compra}/updatestatus', [OrderPurchaseController::class, 'show']);

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
    Route::put('rutas-de-entrega/{ruta}/updateStatus', [DeliveryRouteController::class, 'updateStatus']);
    Route::patch('rutas-de-entrega/{ruta}/pedido/{pedido}', [DeliveryRouteController::class, 'updateInfoChofer']);
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
    //notificaciones
    // Route::get('notificacion', [DeliveryRouteController::class, 'store' ]);


    //Ver pedidos de cada vendedor
    Route::get('pedidos-vendedor/viewPedidosPorVendedor', [SaleController::class, 'viewPedidosPorVendedor']);

    Route::post('/image/upload', [UploadImageController::class, 'uploadImage']);
    Route::post('/image/delete', [UploadImageController::class, 'deleteImage']);


    //CAJA CHICA

    //Ordenes
    Route::get('caja-chica/mis-ordenes/', [SmallBoxUserController::class, 'showUserRequests']);

    //Spent
    Route::get('caja-chica/gastos/ver/', [SpentController::class, 'show']);
    Route::post('caja-chica/gastos/crear/', [SpentController::class, 'store']);
    Route::post('caja-chica/gastos/editar/', [SpentController::class, 'update']);
    Route::post('caja-chica/gastos/borrar/', [SpentController::class, 'delete']);

    //PurchaseRequest
    Route::get('caja-chica/solicitudes-de-compra/ver/', [PurchaseRequestController::class, 'show']);
    Route::post('caja-chica/solicitudes-de-compra/crear/', [PurchaseRequestController::class, 'store']);
    Route::post('caja-chica/solicitudes-de-compra/editar/', [PurchaseRequestController::class, 'update']);
    Route::post('caja-chica/solicitudes-de-compra/borrar/', [PurchaseRequestController::class, 'delete']);
    Route::post('caja-chica/aprobar-solicitud/', [PurchaseRequestController::class, 'approved']);
    Route::post('caja-chica/rechazar-solicitud/', [PurchaseRequestController::class, 'rejected']);
    Route::post('caja-chica/confirmar-entrega/', [PurchaseRequestController::class, 'confirmDelivered']);
    Route::post('caja-chica/realizar-devolucion/', [PurchaseRequestController::class, 'createDevolution']);

    //Center
    Route::get('caja-chica/centros-de-costos/ver/', [CenterController::class, 'show']);
    Route::post('caja-chica/centros-de-costos/crear/', [CenterController::class, 'store']);
    Route::post('caja-chica/centros-de-costos/editar/', [CenterController::class, 'update']);
    Route::post('caja-chica/centros-de-costos/borrar/', [CenterController::class, 'delete']);

    //UserCenter 
    Route::get('caja-chica/usuarios-centro-de-costos/ver/', [UserCenterController::class, 'show']);
    Route::get('caja-chica/usuarios-ccentro-de-costos/crear/', [UserCenterController::class, 'store']);
    Route::get('caja-chica/usuarios-ccentro-de-costos/eliminar/', [UserCenterController::class, 'delete']);

    //Companies
    Route::get('caja-chica/centros-de-costos/ver/', [CompaniesController::class, 'show']);
    Route::post('caja-chica/centros-de-costos/crear/', [CompaniesController::class, 'store']);
    Route::post('caja-chica/centros-de-costos/editar/', [CompaniesController::class, 'update']);
    Route::post('caja-chica/centros-de-costos/borrar/', [CompaniesController::class, 'delete']);

    //Exportar
    Route::post('caja-chica/generar-reporte/', [SmallBoxUserController::class, 'report']);

});

