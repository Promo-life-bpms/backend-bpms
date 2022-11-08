<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseApi;

class HomeController extends Controller
{
    public function dashboard()
    {
        // Obtener el usuario que inicio sesion

        // Obtener el rol que tiene ese usuario

        // Validar que tenga acceso a esta informacion

        // Obtener pedidos de el usuario agrupados por dias

        // Obtener las incidencias de cada pedido agrupados por dias

        // Obtener la informacion del los pedidos con los maquiladore


        $dato = [
            'pedidos' => [
                "domingo" => 4,
                "lunes" => 3,
                "martes" => 7,
                "miercoles" => 4,
                "jueves" => 9,
                "viernes" => 6,
                "sabado" => 3,
            ],
            'incidencias' => [
                "domingo" => 4,
                "lunes" => 5,
                "martes" => 1,
                "miercoles" => 2,
                "jueves" => 1,
                "viernes" => 6,
                "sabado" => 3,
            ],
            "maquilador" => [
                "completo" => 7,
                "pendiente" => 5,
                "total" => 12,
            ],
            "ordendesDeEntrega" => [
                [
                    "user_id" => 13,
                    "nombre" => "Andres",
                    "entregas" => 2,
                    "pendientes" => 1
                ],
                [
                    "user_id" => 42,
                    "nombre" => "Jorge",
                    "entregas" => 10,
                    "pendientes" => 5
                ],
                [
                    "user_id" => 12,
                    "nombre" => "Gerardo",
                    "entregas" => 5,
                    "pendientes" => 2
                ],
            ],
            "seguimientoPedidos" => [
                [
                    "numPedido" => "PED245",
                    "status" => 2,
                    "pedido_id" => 15
                ],
                [
                    "numPedido" => "PED354",
                    "status" => 2,
                    "pedido_id" => 15
                ],
                [
                    "numPedido" => "PED",
                    "status" => 7,
                    "pedido_id" => 15
                ],
                [
                    "numPedido" => "PED25",
                    "status" => 3,
                    "pedido_id" => 15
                ],
                [
                    "numPedido" => "PED145",
                    "status" => 5,
                    "pedido_id" => 15
                ],
            ]

        ];
        return response()->json($dato, ResponseApi::HTTP_OK);
    }
}
