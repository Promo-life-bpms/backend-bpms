<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function storeVideoInfo()
    {
        $videos = [
            /*  [
                'nombre' => 'Inicio de sesion',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/iniciodesesion.mp4',
                'rol' => '*'
            ], */

            [
                'nombre' => 'Nueva Solicitud',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/nuevasolicitud.mp4',
                'rol' => '*'
            ],
            [
                'nombre' => 'Autorización Manager / Administrador',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/autorizacionmangeryadministrador.mp4',
                'rol' => 'manager, administrator'
            ],
            [
                'nombre' => 'Autorización Caja Chica',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/autorizacioncajachica.mp4',
                'rol' => 'caja_chica'
            ],
            [
                'nombre' => 'Reporte General',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/reportegeneral.mp4',
                'rol' => 'caja_chica, administrator'
            ]
        ];
        return response()->json(["videos" => $videos]);
    }
}
