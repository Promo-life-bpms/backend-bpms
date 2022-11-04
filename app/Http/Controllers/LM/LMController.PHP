<?php

namespace App\Http\Controllers\LM;


use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;


class LMController extends Controller
{

    function dashboardLM()
    {
        $rutas = [
            "RUTA_DE_ENTREGAS" => 8,
            "ENTREGAS MATERIAL LIMPIO" => 3
        ];
        //rutas de entregas
        $entregas = [
            "Pedido_264364" => [
                "status" => 2,
                "Recorrido" => "Recogido"
            ],
            "Pedido_2643641" => [
                "Status" => 6,
                "Recorrido" => "En camino"
            ]

        ];
        //rutas de entregas de material limpio
        $material = [
            "Pedido_264364" => [
                "status" => 2,
                "Recorrido" => "Recogido"
            ],
            "Pedido_2643641" => [
                "Status" => 6,
                "Recorrido" => "En camino"
            ]

        ];
        //ordenes de entrega
        $ordenEntrega = [
            [

                "Nombre_chofer" => "juan",
                "entregas" => 13,
                "Pendientes" => 1,
            ],

            [

                "Nombre_chofer" => "jesus",
                "entregas" => 10,
                "Pendientes" => 2,
            ]
        ];
        // calendario de entregas
        $calendarioEntregas = [
            [
                "22/11/2021",
                "PED122416",
                "PED134567",
            ], [
                "23/11/2021",
                "PED122890",
                "PED134562",
            ]
        ];
        return response()->json([
            "Rutas" => $rutas, "RUTA_DE_ENTREGAS" => $entregas,
            "RUTA_DE_ENTREGA_DE_MATERIAL_LIMPIO" => $material,
            "ORDENES_DE_ENTREGA" => $ordenEntrega,
            "CALENDARIO_DE_ENTREGAS" => $calendarioEntregas
        ], Response::HTTP_OK);
    }
    //Rutas de entrega
    function rutasen()
    {
        $rutaEntrega = [
            [
                "Pedido" => "241232",
                "Fecha_de_entrega" => "27-09-2022",
                "Hora_de_entrega" => "9am-11am",
                "Status" => 5,
                "Recorrido" => "Recogido"
            ],
            [
                "Pedido" => "243542",
                "Fecha_de_entrega" => "27-09-2022",
                "Hora_de_entrega" => "11am",
                "Status" => 5,
                "Recorrido" => "En camino"
            ],


        ];
        return response()->json([
            "RUTAS_DE_ENTREGA" => $rutaEntrega
        ], Response::HTTP_OK);
    }
    public function pedidoentrega($pedido)
    {
        $detalleEn = [
            "PEDIDO" => "121212",
            "FECHA" => "10-09-22",
            "EMPRES" => "PROMOLIFE",
            "FACTURA" => "REMISIONADO",
            "RECOGER" => "ALMACEN",
            "CANTIDAD" => "100",
            "PRODUCTO" => "SOMBREROS",
            "LOGO" => "Nombre del logo",
            "CLIENTE" => "Nombre del cliente",
            "ATENCION_A" => "Nombre",
            "DIRECCION_DE_ENTREGA" => "datos",
            "HORA" => "9-13"


        ];

        return response()->json([
            "Detalles_del_pedido" => $detalleEn
        ]);
    }
    function rutasmat()
    {
        $rutaMaterial = [
            [
                "Pedido" => "241232",
                "Producto" => "Clindro color negro",
                "Destino" => "Bodega",
                "Status" => 5,
                "Recorrido" => "Pendiente"
            ],
            [
                "Pedido" => "243542",
                "Producto" => "Mochilas color negro",
                "Destino" => "Bodega",
                "Status" => 5,
                "Recorrido" => "En camino"
            ],


        ];
        return response()->json([
            "RUTAS_DE_ENTREGA_DE_MATERIAL_LIMPIO" => $rutaMaterial
        ], Response::HTTP_OK);
    }
    public function pedidomaterial($pedido)
    {
        $detalleMat = [
            "PEDIDO" => "121212",
            "FECHA" => "10-09-22",
            "EMPRES" => "PROMOLIFE",
            "FACTURA" => "REMISIONADO",
            "RECOGER" => "ALMACEN",
            "CANTIDAD" => "100",
            "PRODUCTO" => "SOMBREROS",
            "LOGO" => "Nombre del logo",
            "CLIENTE" => "Nombre del cliente",
            "ATENCION_A" => "Nombre",
            "DIRECCION_DE_ENTREGA" => "datos",
            "HORA" => "9-13"


        ];

        return response()->json([
            "Detalles_del_pedido" => $detalleMat
        ]);
    }
    public function pedidos()
    {
        $pedidos = [
            [
                "Num_pedido" => "241232",
                "Fecha" => "Cotizacion, Compromiso, Previstas",
                "General" => "Cliente, Comercial, Total",
                "Status" => 2,
                "id" => 56,
            ],
            [
                "Num_pedido" => "243542",
                "Fecha" => "Cotizacion, Compromiso, Previstas",
                "General" => "Cliente, Comercial, Total",
                "Status" => 3,
                "Id" => 50,
            ]
        ];

        return response()->json([
            "pedidos" => $pedidos
        ], Response::HTTP_OK);
    }

    public function pedidoLM($pedido)
    {
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
                "fecha" => "20-09-22",
                "Nombre" => "Lola",
                "Cantidad" => "100->97",
                "Hora" => "12:32",
            ],
            [
                "fecha" => "21-09-22",
                "Nombre" => "Julieta",
                "Cantidad" => "100->80",
                "Hora" => "13:05",
            ]
        ];

        return response()->json([
            "pedido" => $pedido
        ], Response::HTTP_OK);
    }
}
