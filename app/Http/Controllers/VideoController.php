<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    public function storeVideoInfo()
    {
        $user = auth()->user();

        //Primero obtenemos los roles
        $tienerol = DB::table('role_user')->where('user_id', $user->id)->first();
        $rol = $tienerol->role_id;

        ///VIDEOS PARA ADMINISTRADOR

        switch ($rol) {
            case 1:
                /////VIDEOS PARA ADMINISTRADOR/////
                //dd(1);
                $videos = [
                    [
                        'nombre' => 'Nueva Solicitud',
                        'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/nuevasolicitud.mp4',
                        'rol' => '*'
                    ],
                    [
                        'nombre' => 'Autorización Manager / Administrador',
                        'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/autorizacionmangeryadministrador.mp4',
                        'rol' => 'administrator, manager'
                    ],
                    [
                        'nombre' => 'Reporte General',
                        'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/reportegeneral.mp4',
                        'rol' => 'caja_chica, administrator'
                    ],

                ];
                break;
            case 13:
                /////VIDEOS PARA MANAGER/////
                //dd(2);
                $videos = [
                    [
                        'nombre' => 'Nueva Solicitud',
                        'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/nuevasolicitud.mp4',
                        'rol' => '*'
                    ],
                    [
                        'nombre' => 'Autorización Manager / Administrador',
                        'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/autorizacionmangeryadministrador.mp4',
                        'rol' => 'administrator, manager'
                    ],
                ];
                break;
            case 43:
                /////VIDEOS PARA CAJA CHICA/////
                //dd(3);
                $videos = [
                    [
                        'nombre' => 'Nueva Solicitud',
                        'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/nuevasolicitud.mp4',
                        'rol' => '*'
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
                    ],
                ];
                break;
            default:
                /////CUALQUIERA QUE NO SEA LOS ANTERIORES/////
                $videos = [
                    [
                        'nombre' => 'Nueva Solicitud',
                        'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/nuevasolicitud.mp4',
                        'rol' => '*'
                    ]
                ];
                break;
        }

        /*$videos = [
              [
                'nombre' => 'Inicio de sesion',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/iniciodesesion.mp4',
                'rol' => '*'

            ], */
        /*
            [
                'nombre' => 'Nueva Solicitud',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/nuevasolicitud.mp4',
                'rol' => '*',
            ],
            [
                'nombre' => 'Autorización Manager / Administrador',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/autorizacionmangeryadministrador.mp4',
                'rol' => 'manager, administrator',

            ],
            [
                'nombre' => 'Autorización Caja Chica',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/autorizacioncajachica.mp4',
                'rol' => 'caja_chica',
            ],
            [
                'nombre' => 'Reporte General',
                'url' => 'https://dev-api-bpms.promolife.lat/storage/capacitacion/reportegeneral.mp4',
                'rol' => 'caja_chica, administrator',

            ],
        ];*/
        return response()->json(["videos" => $videos]);
    }
}
