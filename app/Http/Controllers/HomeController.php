<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Ventas\VentasController;
use App\Http\Controllers\Control\ControlController;
use App\Http\Controllers\LM\LMController;
use App\Http\Controllers\Maquilador\MaquiladorController;
use App\Http\Controllers\Chofer\ChoferController;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseApi;
use Illuminate\Support\Facades\Auth;


/**
 * Clase HomeController
 *
 * Controlador para la página de inicio.
 */
class HomeController extends Controller
{
    /**
     * Método para mostrar el panel de control.
     *
     * @return mixed
     */
    public function dashboard()
    {
        // Obtener el usuario que inició sesión
        $user = Auth::user();

        // Obtener el rol que tiene ese usuario y redirigir al controlador correspondiente
        switch ($user->whatRoles[0]->name) {
            case 'ventas':
                return VentasController::dashboard();
                break;
            case 'control_calidad':
                return ControlController::dashboard();
                break;
            case 'logistica':
                return LMController::dashboard();
                break;
            case 'maquilador':
                return MaquiladorController::dashboard();
                break;
            case 'chofer':
                return ChoferController::dashboard();
                break;
        }
    }
}
