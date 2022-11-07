<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\IncidenciaController;
use App\Models\Incidencia;
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

