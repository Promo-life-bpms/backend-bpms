<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ApiOdoo\ApiOdooController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesOrderController;
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




Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user-profile', [AuthController::class, 'userProfile']);
    Route::post('logout', [AuthController::class, 'logout']);
    // Rutas de los pedidos
    Route::get('pedidos', [SalesOrderController::class, 'index']);
    Route::get('pedidos/{lead}', [SalesOrderController::class, 'show']);
    Route::get('pedidos/{lead}/productos', [SalesOrderProductsController::class, 'index']);
});

Route::get('dashboard', [HomeController::class, 'dashboard']);
Route::get('users', [AuthController::class, 'allUsers']);
Route::post('setOrderSale', [ApiOdooController::class, 'setOrderSale']);
