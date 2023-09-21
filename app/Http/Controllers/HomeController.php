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


class HomeController extends Controller
{
    public function dashboard()
    {
        // Obtener el usuario que inicio sesion
        $user = Auth::user();
        //return $user;
        // Obtener el rol que tiene ese usuario
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
