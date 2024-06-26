<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductRouteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DeliveryRouteController;
use App\Http\Controllers\IncidenceController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiOdooController;

use App\Http\Controllers\CenterController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\EstimationSmallBoxController;
use App\Http\Controllers\EventualesController;
use App\Http\Controllers\ExchangeReturnController;
use App\Http\Controllers\BinnacleController;
use App\Http\Controllers\CheckList as ControllersCheckList;
use App\Http\Controllers\ConfirmDeliveryController;
use App\Http\Controllers\ConfirmProductCountController;
use App\Http\Controllers\ConfirmRouteController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ExcelRutaController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\LackOfMoneyEventualsController;
use App\Http\Controllers\ManagerHasDepartmentController;
use App\Http\Controllers\OrderConfirmationController;
use App\Http\Controllers\ReceptionController;
use App\Http\Controllers\OrderPurchaseController;
use App\Http\Controllers\OrdersGroup as ControllersOrdersGroup;
use App\Http\Controllers\Pruebas;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\QualityIncidentsFormController;
use App\Http\Controllers\SmallBoxUserController;
use App\Http\Controllers\SpentController;
use App\Http\Controllers\StatusOrdersController;
use App\Http\Controllers\TemporyCompanyController;
use App\Http\Controllers\UploadImageController;
use App\Http\Controllers\UserCenterController;
use App\Models\EstimationSmallBox;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDetailsController;
use App\Models\CheckList;
use App\Models\OrdersGroup;
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

// Route::get('users', [AuthController::class, 'allUsers']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('syncUsers', [AuthController::class, 'syncUsers']);
//Acceso
Route::get('Acces', [AuthController::class, 'Acces']);
Route::get('userAccess', [AuthController::class, 'userAccess']);

Route::group(['middleware' => 'auth'], function () {

    /////API PRUEBA///
    Route::get('status', [Pruebas::class, 'PruebasServidor']);
    // Apis de el userController
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'create']);
    Route::post('users/edit', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'delete']);
    Route::get('users/sendNewAccess/{id}', [UserController::class, 'sendNewAccess']);
    Route::get('syncUsers', [UserController::class, 'syncUsers']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);

    Route::get('pedidos', [SaleController::class, 'index']);
    //estadisticas
    Route::get('estadistica', [SaleController::class, 'estadisticas']);
    //calendario
    Route::get('calendario', [SaleController::class, 'calendario']);

    Route::get('pedidos/{pedido}', [SaleController::class, 'show']);
    //crear el check-list
    Route::post('crear-checklist/{pedido}', [ControllersCheckList::class, 'create']);
    //ver el chec-list
    Route::get('pedido-checklist/{pedido}', [ControllersCheckList::class, 'show']);
    Route::get('pedido/{pedido}', [SaleController::class, 'infoSales']);

    // Actualizar la ruta de entrega
    Route::put('pedidos/{pedido}/update_delivery_address_custom', [SaleController::class, 'updateDeliveryAddressCustom']);

    // Crear la bitacora
    Route::post('pedidos/{pedido}/bitacora/create', [BinnacleController::class, 'store']);

    // Crear y actualizar la inspeccion
    Route::post('pedido/{pedido}/inspections/{product}', [InspectionController::class, 'store']);
    Route::get('inspections/{inspection}', [InspectionController::class, 'show']);
    Route::post('pedido/files/inspections', [InspectionController::class, 'files']);

    // Detalle de  OC, OT
    Route::get('pedidos/{pedido}/orders/{order}', [OrderPurchaseController::class, 'show']);
    Route::post('pedidos-orders-groups', [ControllersOrdersGroup::class, 'create']);
    Route::post('pedidos-orders-groups/update/{sale}', [ControllersOrdersGroup::class, 'update']);
    // Recepciones de Inventario
    Route::post('reception/{code_order}', [ReceptionController::class, 'saveReception']);

    Route::get('orders/{order}/receptions/{reception}', [ReceptionController::class, 'getReception']);
    //confirmar un producto
    Route::post('recepctionsacepted/{code_order_route_id}', [ReceptionController::class, 'receptionAccept']);
    //Confirmar producto maquilado
    Route::post('receptionproduct/{order}/product/{odoo_product}', [ReceptionController::class, 'confirmation_manufactured_product']);
    //ver recepcion maquilada
    Route::get('recepctionsacepted/{order}/product/{odoo_product}', [ReceptionController::class, 'getReceptionConfirmed']);
    // Seccion para actualizar el estatus de maquila

    // Seccion de Incidencias

    // Detalle de la incidencia con las compras relacionadas
    // localhost/pedidos/PED456/incidencias/INC-423
    Route::get('incidencias/{incidencia}', [IncidenceController::class, 'show']);
    // Crear una incidencia
    Route::post('pedidos/{pedido}/incidencia', [IncidenceController::class, 'store']);
    Route::put('incidencias/{incidencia}', [IncidenceController::class, 'updateSolution']);
    Route::put('incidencias/{incidencia}/update', [IncidenceController::class, 'updateIncidenceComplete']);

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
    Route::post('rutas-de-entrega/{id}', [DeliveryRouteController::class, 'store']);
    //ver una ruta de entrega
    Route::get('rutas-de-entrega/show/{id}', [DeliveryRouteController::class, 'show']);
    // Actualizar la ruta de entrega
    Route::post('ruta-de-entrega/{prouduct_id}', [DeliveryRouteController::class, 'updateRuta']);
    // Route::put('rutas-de-entrega/{ruta}/updateStatus', [DeliveryRouteController::class, 'updateStatus']);
    Route::put('rutas-de-entrega/{ruta}/updateStatus', [DeliveryRouteController::class, 'updateStatus']);
    Route::patch('rutas-de-entrega/{ruta}/pedido/{pedido}', [DeliveryRouteController::class, 'updateInfoChofer']);
    // Eliminar ruta de entrega
    Route::delete('rutas-de-entrega/{deliveryRoute}', [DeliveryRouteController::class, 'destroy']);
    //Eliminar un pedido de una ruta
    Route::get('rutas-de-entrega/{ruta}/excel', [DeliveryRouteController::class, 'excelCompras']);
    //Rutas de Compras Completas
    Route::get('rutas-de-entrega/Compras-Completa', [DeliveryRouteController::class, 'DeliveryRoutePurchaseCompletas']);
    //Rutas de Compras Pendientes
    Route::get('rutas-de-entrega/Compras-Pendientes', [DeliveryRouteController::class, 'DeliveryRoutePurchasePendientes']);
    //Editar Rutas de Compras Pendientes
    Route::post('rutas-de-entrega/Editar/Compras-Pendientes', [DeliveryRouteController::class, 'updateDeliveryPurchasePendientes']);
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


    /////////////////////RUTAS  PARA CONFIRMAR LOS PEDIDOS //////////////////////
    Route::post('confirmation/order/products', [OrderConfirmationController::class, 'ConfirmOrderProducts']);


    //Route::post('status/two/{pedido}', [StatusOrdersController::class, 'StatusTwo']);
    /////////////////CONFIRMAR ORDENES DE PRODUCTOS///////////////
    Route::post('confirmation/route/product/{sale_id}', [ConfirmRouteController::class, 'ConfirmationRoute']);
    Route::get('history/confirmation/route/product/{idProductOrder}', [ConfirmRouteController::class, 'index']);
   // Route::get('status/route-confirmation/', [ConfirmRouteController::class, 'StatusRecepcion']);
    //////////////////CONTEO DEL PRODUCTO///////////////////////
    Route::post('product/count/confirmation', [ConfirmProductCountController::class, 'ProductCount']);
    Route::get('product/count/confirmation/history/{idProductOrder}', [ConfirmProductCountController::class, 'ProductCountHistory']);

    ////////////////CONFIRMAR LA ENTREGA DEL PRODUCTO//////////
    Route::post('confirm/product/delivery', [ConfirmDeliveryController::class, 'ConfirmDelivery']);
    Route::get('history/confirm/product/delivery/{idProduct}', [ConfirmDeliveryController::class, 'HistoryConfirmDelivery']);

    ///////////////////////////FORMULARIO PARA CREAR UNA INCIDENCIA///////////
    Route::post('first/part/of/the/incident/form/{code_sale}', [QualityIncidentsFormController::class, 'FirstPartOfTheIncidentForm']);
    Route::post('second/part/of/the/incident/form/{code_sale}', [QualityIncidentsFormController::class, 'SecondPartOfTheIncidentForm']);
    Route::post('third/part/of/the/incident/form/{code_sale}', [QualityIncidentsFormController::class, 'ThirdPartOfTheIncidentForm']);
    Route::get('incident/form/{idform}', [QualityIncidentsFormController::class, 'IncidentForm']);
    Route::post('edit/incident/form/{code_sale}', [QualityIncidentsFormController::class, 'UpdateFormInc']);

    //CAJA CHICA

    //Ordenes Usuario
    Route::get('caja-chica/mis-ordenes/', [SmallBoxUserController::class, 'showUserRequests']);
    Route::get('caja-chica/mis-ordenes/{page}', [SmallBoxUserController::class, 'showUserPageRequests']);
    //Ordenes Comprador
    Route::get('caja-chica/ordenes-comprador/', [SmallBoxUserController::class, 'showBuyerRequests']);
    Route::get('caja-chica/ordenes-comprador/{page}', [SmallBoxUserController::class, 'showBuyerPageRequests']);

    //Spent
    Route::get('caja-chica/gastos/ver/', [SpentController::class, 'show']);
    Route::post('caja-chica/gastos/crear/', [SpentController::class, 'store']);
    Route::post('caja-chica/gastos/editar', [SpentController::class, 'update']);
    Route::post('caja-chica/gastos/borrar', [SpentController::class, 'deactivateSpents']);
    Route::post('caja-chica/gastos/activate', [SpentController::class, 'activateSpents']);


    //PurchaseRequest
    Route::get('caja-chica/solicitudes-de-compra/ver/', [PurchaseRequestController::class, 'show']);
    Route::get('caja-chica/solicitudes-de-compra/ver/{page}', [PurchaseRequestController::class, 'showPage']);

    ////APIS DE PRUEBA///
    Route::get('caja-chica/solicitudes-de-compra/por-departamento/ver', [PurchaseRequestController::class, 'DepartmentPurchase']);
    Route::get('caja-chica/solicitudes-de-compra/por-departamento/ver/{page}', [PurchaseRequestController::class, 'DepartmentPage']);
    Route::post('caja-chica/aprobar-solicitud/por-department/', [PurchaseRequestController::class, 'approvedDepartment']);
    Route::post('caja-chica/editar/eventuales', [PurchaseRequestController::class, 'updateEventuales']);
    Route::post('caja-chica/add/eventual/finde', [PurchaseRequestController::class, 'EventualesFinde']);
    ///////////CONFIRMAR RETORNO DE DINERO////////////////
    Route::post('confirmation/returnormore/money/eventuales', [LackOfMoneyEventualsController::class, 'ConfirmationReturnMoneyEventuales']);

    Route::post('caja-chica/solicitudes-de-compra/crear/', [PurchaseRequestController::class, 'store']);
    //Route::post('caja-chica/solicitudes-de-compra/edit/date/', [PurchaseRequestController::class, 'editdate']);
    Route::post('caja-chica/solicitudes-de-compra/editar/', [PurchaseRequestController::class, 'update']);
    Route::post('caja-chica/solicitudes-de-compra/borrar/', [PurchaseRequestController::class, 'delete']);

    Route::post('caja-chica/aprobar-solicitud/', [PurchaseRequestController::class, 'approved']);
    Route::post('caja-chica/rechazar-solicitud/', [PurchaseRequestController::class, 'rejected']);
    Route::post('caja-chica/confirmar-entrega/', [PurchaseRequestController::class, 'confirmDelivered']);
    Route::post('caja-chica/confirmar-recibido/', [PurchaseRequestController::class, 'confirmReceived']);
    Route::post('caja-chica/realizar-devolucion/', [PurchaseRequestController::class, 'createDevolution']);
    Route::post('caja-chica/realizar-devolucion/confirmada/', [PurchaseRequestController::class, 'confirmationDevolution']);
    Route::post('caja-chica/realizar-devolucion/cancelation/', [PurchaseRequestController::class, 'cancelationDevolution']);
    Route::post('caja-chica/realizar-cancelacion/', [PurchaseRequestController::class, 'createCancellation']);
    Route::post('caja-chica/actualizar-pago', [PurchaseRequestController::class, 'updatePaymentMethod']);

    //actualizar el monto del pago//
    route::post('caja-chica/actualizar-pago-monto', [PurchaseRequestController::class, 'updatemoney']);

    //Administrador
    Route::get('caja-chica/administrador/solicitudes-de-compra/ver', [PurchaseRequestController::class, 'showAdministrador']);
    Route::post('caja-chica/administrador/solicitudes-de-compra/aprobar', [PurchaseRequestController::class, 'approvedByAdmin']);


    //Center
    Route::get('caja-chica/centros-de-costos/ver/', [CenterController::class, 'show']);
    Route::post('caja-chica/centros-de-costos/crear/', [CenterController::class, 'store']);
    Route::post('caja-chica/centros-de-costos/editar/', [CenterController::class, 'update']);
    Route::post('caja-chica/centros-de-costos/borrar', [CenterController::class, 'deactivateCenters']);
    Route::post('caja-chica/centros-de-costos/activate', [CenterController::class, 'activateCenters']);

    //UserCenter
    Route::get('caja-chica/usuarios-centro-de-costos/ver/', [UserCenterController::class, 'show']);
    Route::get('caja-chica/usuarios-centro-de-costos/crear/', [UserCenterController::class, 'store']);
    Route::get('caja-chica/usuarios-centro-de-costos/eliminar/', [UserCenterController::class, 'delete']);

    //Companies
    Route::get('caja-chica/companias/ver/', [CompaniesController::class, 'show']);
    Route::post('caja-chica/companias/crear/', [CompaniesController::class, 'store']);
    Route::post('caja-chica/companias/editar/', [CompaniesController::class, 'update']);
    Route::post('caja-chica/companias/borrar/', [CompaniesController::class, 'delete']);

    //Exportar
    Route::post('caja-chica/generar-reporte/', [SmallBoxUserController::class, 'report']);

    //Datos para solicitud
    Route::get('caja-chica/datos-solicitud/', [SmallBoxUserController::class, 'dataRequest']);


    //CAJA CHICA PRESUPUESTO//
    Route::post('caja-chica/estimate/', [EstimationSmallBoxController::class, 'create'])->name('estimate');
    Route::get('caja-chica/information/estimate', [EstimationSmallBoxController::class, 'index'])->name('information.estimate');
    Route::get('caja-chica/information/history', [EstimationSmallBoxController::class, 'ExpenseHistory'])->name('information.history');
    Route::post('caja-chica/estimate/return', [EstimationSmallBoxController::class, 'BudgetReturn'])->name('estimate.return');
    Route::get('caja-chica/devolution/product/history', [EstimationSmallBoxController::class, 'DevolutionHistory'])->name('devolution.product.history');
    Route::get('caja-chica/estimate/return/history', [EstimationSmallBoxController::class, 'HistoryOfTheReturnOfMoney'])->name('estimate.return.history');

    //CAJA CHICA AGREGAR EMPRESA/EVENTUALES //
    Route::post('caja-chica/newcompany', [TemporyCompanyController::class, 'store'])->name('newcompany');
    Route::post('caja-chica/delete', [TemporyCompanyController::class, 'delete'])->name('deletecompany');
    Route::get('caja-chica/company', [TemporyCompanyController::class, 'index'])->name('infocompany');
    Route::post('caja-chica/company/restore', [TemporyCompanyController::class, 'restore'])->name('name');

    ///CAJA CHICA REGRESAR DINERO QUE SOBRO DEL EFECTIVO///
    Route::post('caja-chica/return/excess/money', [ExchangeReturnController::class, 'ReturnExcessMoney']);
    Route::post('caja-chica/return/excess/money/confirmation', [ExchangeReturnController::class, 'ConfirmationReturnMoney']);

    /////CAJA CHICA/BPMS /CREAR DEPARTAMENTOS/
    ///VER DEPARTAMENTOS
    Route::get('view/departments', [DepartmentController::class, 'AllDepartments'])->name('view.department');
    Route::post('create/departments', [DepartmentController::class, 'AddDepartment'])->name('create.department');
    Route::post('updated/departments', [DepartmentController::class, 'UpdatedDepartment'])->name('updated.department');

    ///USERS DETAILS ///
    Route::get('users/department/{id_department}', [UserDetailsController::class, 'UserforDepartment'])->name('users.department');
    //Video
    Route::get('video', [VideoController::class, 'storeVideoInfo']);



    ///////////////////APIS PARA MANAGERS//////////////////////////////
    Route::get('managers/department', [ManagerHasDepartmentController::class, 'ViewManager']);
    Route::post('create/manager/department', [ManagerHasDepartmentController::class, 'CrearManager']);
    Route::post('delete/manager/department', [ManagerHasDepartmentController::class, 'DeleteManager']);
});
