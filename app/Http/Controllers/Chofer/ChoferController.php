<?php

namespace App\Http\Controllers\Chofer;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response;


class ChoferController extends Controller
{
    public static function dashboard()
    {
        $rutas = [
            "RUTA_DE_ENTREGAS" => 8,
            "ENTREGAS MATERIAL LIMPIO" => 3
        ];
        //entregas pendientes
        $pendiente = [
            [
                "Pedido" => "1020",
                "Fecha" => "11-08-22",
                "Recoger" => "Almacen",
                "Producto" => "Set de libreta",
                "Cantidad" => 120,
                "Cliente" => "GRUPO NACIONAL PROVICIONAL SAB",
                "Direccion de entrega" => "Av. cerro torres 395...",
                "Hora" => "9 a 1",
                "Atencion a " => "Elba Berenice",
                "Status" => "En proceso de entrega"
            ],
            [
                "Pedido" => "1302",
                "Fecha" => "12-08-22",
                "Recoger" => "Almacen",
                "Producto" => "Kit boligrafo de plastico...",
                "Cantidad" => 150,
                "Cliente" => "IXE BANCO S.A",
                "Direccion de entrega" => "Av. cerro torres 395...",
                "Hora" => "Cita 2pm ",
                "Atencion a " => "Fernando",
                "Status" => "En proceso de entrega"
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
            "Rutas" => $rutas,
            "ENTREGAS_PENDIENTES" => $pendiente,
            "ORDENES_DE_ENTREGA" => $ordenEntrega,
            "CALENDARIO_DE_ENTREGAS" => $calendarioEntregas
        ], Response::HTTP_OK);
    }
    function RutasdeEntrega()
    {
        //tabla de rutas de entrega
        $tabla = [
            [
                "Pedido" => "1302",
                "Fecha" => "11-08-22",
                "Recoger" => "Almacen",
                "Producto" => "Sombrero de ...",
                "Cantidad" => 150,
                "Cliente" => "IXE BANCO S.A",
                "Direccion de entrega" => "Av. cerro torres 395...",
                "Hora" => " 9 a 2pm ",
                "Atencion a " => "Berenice",
                "Status" => "En proceso de entrega"
            ],
            [
                "Pedido" => "1302",
                "Fecha" => "12-08-22",
                "Recoger" => "Almacen",
                "Producto" => "Kit boligrafo de plastico...",
                "Cantidad" => 150,
                "Cliente" => "IXE BANCO S.A",
                "Direccion de entrega" => "Av. cerro torres 395...",
                "Hora" => "Cita 2pm ",
                "Atencion a " => "Fernando",
                "Status" => "En proceso de entrega"
            ],

        ];
        return response()->json([
            "RUTAS_DE_ENTREGA" => $tabla
        ],);
    }

    function PedidoEntrega()
    {
        $detalle = [
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
            "Detalle_del_pedido" => $detalle
        ]);
    }
    //boton de confirmar entrega
    public function Confirmar(Request $request)
    {
        return response()->json(["msg" => "Entrega confirmada exitosamente", "status" => "ok"], 201);
    }
    //boton de rechazar entrega
    public function Rechazar(Request $request)
    {
        return response()->json([
            "msg" => "Entrega rechazada correctamente",
        ], 201);
    }

    function RutasdeMaterial()
    {
        $materialLimpio = [
            [
                "Empresa" => "BH",
                "Pedido" => "234234",
                "Accion" => "RECOGER",
                "Proveedor" => "BOLSEC",
                "Cantidad" => 3000,
                "Producto" => "BOLSAS CELOFAN",
                "Destino2" => "BODEGA",
                "N_Guias" => "SIN GUIAS",
            ],

            [
                "Empresa" => "BH",
                "Pedido" => "342542",
                "Accion" => "RECOGER",
                "Proveedor" => "DOBLE VELA",
                "Cantidad" => 42,
                "Producto" => "CILINDROS COLOR NEGRO",
                "Destino" => "GERMAN",
                "N_Guias" => "1231244",
            ],

        ];
        return response()->json([
            "RUTAS_DE_ENTREGA_DE_MATERIAL_LIMPIO" => $materialLimpio

        ]);
    }

    function PedidoMaterial()
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
            "Detalle_del_pedido" => $detalleMat
        ]);
    }
    //Pedidos
    function SeguimientoPedidos()
    {
        $segpedidos = [
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
            "SEGUIMIENTO_DE_PEDIDOS" => $segpedidos
        ], Response::HTTP_OK);
    }
}
