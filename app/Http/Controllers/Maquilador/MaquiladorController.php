<?php

namespace App\Http\Controllers\Maquilador;

use App\Http\Controllers\Controller;



class MaquiladorController extends Controller
{
    //home maquilador
    public static function dashboard()
    {
        $rutas = [
            [
                "NUEVAS_REMISIONES" => 8
            ],
            [
                "PENDIENTES" => 10
            ],

        ];

        $tablaR = [
            [
                "id" => 8,
                "Fecha" => "12-08-22",
                "Nombre" => "Miriam",
                "Atencion" => "Juan",
                "Cantidad" => 10,
                "Observaciones" => "S/O",
                "Precio_Unitario" => 200,
                "Subtotal" => 400,
                "IVA" => 12,
                "Total" => 1000,
                "Status" => "En proceso"

            ],
            [
                "id" => 8,
                "Fecha" => "12-10-22",
                "Nombre" => "Tania",
                "Atencion" => "Jazz",
                "Cantidad" => 10,
                "Observaciones" => "S/O",
                "Precio_Unitario" => 12,
                "Subtotal" => 400,
                "IVA" => 5,
                "Total" => 600,
                "Status" => "En proceso"

            ],

        ];
        return response()->json([
            "rUTAS" => $rutas,
            "DETALLES_DE_REMISIONES" => $tablaR
        ]);
    }
    function Remisiones()
    {
        $tablaRe
            = [
                [
                    "id" => 8,
                    "Fecha" => "12-08-22",
                    "Nombre" => "Miriam",
                    "Atencion" => "Juan",
                    "Cantidad" => 10,
                    "Observaciones" => "S/O",
                    "Precio_Unitario" => 200,
                    "Subtotal" => 400,
                    "IVA" => 12,
                    "Total" => 1000,
                    "Status" => "En proceso"

                ],
                [
                    "id" => 8,
                    "Fecha" => "12-10-22",
                    "Nombre" => "Tania",
                    "Atencion" => "Jazz",
                    "Cantidad" => 10,
                    "Observaciones" => "S/O",
                    "Precio_Unitario" => 12,
                    "Subtotal" => 400,
                    "IVA" => 5,
                    "Total" => 600,
                    "Status" => "En proceso"

                ],

            ];
        return response()->json([

            "Remisiones" => $tablaRe
        ]);
    }

    function DetallesR()
    {
        $detalleRe = [
            "Cantidad" => 100,
            "Observaciones" => "",
            "Precio_Unitario" => 100,
            "Subtotal" => 200,
            "IVA" => 10,
            "Total" => 100

        ];
        return response()->json([

            "DETALLES_DE_REMISION" => $detalleRe
        ]);
    }
}
