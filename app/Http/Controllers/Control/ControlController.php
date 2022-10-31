<?php

namespace App\Http\Controllers\Control;


use App\Http\Controllers\Controller;
use Dotenv\Util\Regex;
use Illuminate\Support\Facades\Request;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;
use Symfony\Component\HttpFoundation\Response;


class ControlController extends Controller
{
    public function dashboardCon()
    {
        //pedidos e incidencias
        $pedinc = [
            "Pedidos" => 1,
            "Incidencias" => 2,
            "dias"=> "lunes",
            "cantidad"=> "20"
        ];
        $maquilador = [
            "Completadas" => 7,
            "Pendientes" => 5
        ];
        //ordenes de entrega
        $orden = [
             [
                "Nombre_chofer"=>"juan",
                "entregas"=>13,
                "Pendientes"=>1,
            ],

             [
                "Nombre_chofer"=>"jesus",
                "entregas"=>10,
                "Pendientes"=>2,
            ]

        ];
        //seguimiento del pedido
        $tablapedido = [
            "Num_pedido" => "PED122416",
            "Status" => 3,
            "id" => 35,

        ];
        return response()->json([
            "PEDIDOS_E_INCIDENCIAS" => $pedinc,
            "Maquilador"=>$maquilador,
            "Ordenes_de_entrega"=>$orden,
             "Seguimiento_del_pedido"=>$tablapedido
        ], Response::HTTP_OK);
}

public function pedidoAl($pedido)
{
    //pedido en general
    $pedido = [
        "Status" => 2,
        "Num_pedido" => "PED122416",
        "Fecha" => "25-10-22",
        "Empresa" => "PROMOLIFE",
        "Factura" => "REMISIONADO",
        "Recoger" => "ALMACEN",
        "Cantidad" => "100",
        "Producto" => "LOREM IPSUM DOLOR SIT AMET",
        "Logo" => "NOMBRE DEL LOGO",
        "Cliente" => "NOMBRE DEL CLIENTE ",
        "Atencion_a" => "NOMBRE",
        "Direccion_de_entrega" => "DIRECCION",
        "Hora" => "9-13",

          [
            "20/09/22",
            "Josue",
            "Presupuesto, Pedido de venta",
            "13:46"
        ],
         [
            " 21/09/22",
            "ISA",
            "Pedido de venta",
            "11:00"
        ],
    ];

    return response()->json([
        "pedido" => $pedido
    ], Response::HTTP_OK);
}

public function almacen(){
    //tabla almacen
$almacen=[
    [
     "Pedido"=>"241232",
    "Empresa" => "BH",
    "Entrada" => "17-09-22",
    "Salida" => "20-09-22",
    "Proveedor" => "BOLSEC",
    "Producto" => "BOLSAS CELOFAN",
    "Cantidad" => 3000,
    "Observaciones" => "Sin observaciones",
    "Descripcion" => "241232",
    ],

    [
        "Pedido"=>"243542",
        "Empresa" => "BH",
        "Entrada" => "18-09-22",
        "Salida" => "sin datos",
        "Proveedor" => "DOBLE VELA",
        "Producto" => "CILINDRO COLOR NEGRO",
        "Cantidad" => 41,
        "Observaciones" => "Sin observaciones",
        "Descripcion" => "243542",
        ]

    ];
return response()->json([
"Almacen" => $almacen
], Response::HTTP_OK);
}
public function almacenShow($pedido){
    //pedido de almacen
   $pedAlmacen=[
        "EMPRESA" => "PROMOLIFE",
        "PEDIDO" => "PED121212",
        "OC" => "OC",
        "ACCION" => "REMISIONADO",
        "PROVEEDOR" => "ALMACEN",
        "N_PEDIDO" => 100,
        "OBSERVACIONES" => "Sin observaciones",
        "CANTIDAD" => 100,
        "LOGO" => "Nombre del logo",
        "DESCRIPCION" => "sin descripcion",

   [
     "fecha"=> "20-09-22",
    "Nombre" => "Emilio",
    "Cantidad" => "100->97",
    "Hora" => "12:32",
   ],
   [
    "fecha"=>"21-09-22",
    "Nombre" => "Jesus",
    "Cantidad" => "100->80",
    "Hora" => "13:05",
   ]

];
 return response()->json([
    "Almacen" => $pedAlmacen
    ], Response::HTTP_OK);
}
//seguimiento de pedido
public function segpedidos()
    {
        $pedidos = [
            [
               "Num_pedido"=>"241232",
               "Fecha"=>"Cotizacion, Compromiso, Previstas",
               "General"=>"Cliente, Comercial, Total",
               "Status"=>2,
               "id"=>56,
           ],
            [
               "Num_pedido"=>"243542",
               "Fecha"=>"Cotizacion, Compromiso, Previstas",
               "General"=>"Cliente, Comercial, Total",
               "Status"=>3,
               "Id"=>50,
           ]
            ];

        return response()->json([
            "pedidos" => $pedidos
        ], Response::HTTP_OK);
    }

}
