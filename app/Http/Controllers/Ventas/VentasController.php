<?php

namespace App\Http\Controllers\Ventas;


use App\Http\Controllers\Controller;
use Dotenv\Util\Regex;
use Illuminate\Support\Facades\Request;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;
use Symfony\Component\HttpFoundation\Response;


class VentasController extends Controller
{
    public function dashboard()
    {
        $incidencias = [
            "INCIDENCIAS" => 18,
            "SIN_INCIDENCIAS" => 11
        ];

        $maquilador = [
            "Completadas" => 7,
            "Pendientes" => 5
        ];
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

        $tablapedido = [
            "Num_pedido" => "PED122416",
            "Status" => 3,
            "id" => 56,

        ];

        return response()->json([
            "incidencias" => $incidencias, "maquilador" => $maquilador, "calendario_de_entregas" => $calendarioEntregas, "seguimiento_de_pedidos" => $tablapedido
        ], Response::HTTP_OK);
    }

    public function pedido($pedido)
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
                "20/09/22",
                "Juan",
                "Presupuesto, Pedido de venta",
                "13:46"
            ],
             [
                "21/09/22",
                "Lele",
                "Pedido de venta",
                "11:00"
            ],
        ];

        return response()->json([
            "pedido" => $pedido
        ], Response::HTTP_OK);
    }

    public function incidencias()
    {
        $incidencia = [
            "Pedido_256467" => [
                "Pedido"=>"256467",
                "Cliente"=>"GRUPONACIONAL PROVICIONAL SAB",
                "Proveedor"=>"BOLSEC",
                "Piezas_rechazadas"=>"12",
                "Observaciones"=>"PIEZAS ROTAS ",
                "Status"=>"PENDIENTE",
                "id"=>56
            ],
             [
                "Pedido"=>"252383",
                "Cliente"=>"GRUPONACIONAL PROVICIONAL SAB",
                "Proveedor"=>"BOLSEC",
                "Piezas_rechazadas"=>"12",
                "Observaciones"=>"EL COLOR NO ES EL CORRECTO",
                "Status"=>"PENDIENTE",
                "id"=>56
            ],
        ];

        return response()->json([
            $incidencia
        ], Response::HTTP_OK);
    }

    public function showIncidencia($pedido)
    {
        $pIncidencia = [
            "Num_pedido" => "PED122416",
            "Fecha" => "25-10-22",
            "Empresa" => "PROMOLIFE",
            "Factura" => "REMISIONADO",
            "Recoger" => "ALMACEN",
            "Cantidad" => 100,
            "Producto" => "LOREM IPSUM DOLOR SIT AMET",
            "Logo" => "NOMBRE DEL LOGO",
            "Cliente" => "NOMBRE DEL CLIENTE ",
            "Atencion_a" => "NOMBRE",
            "Direccion_de_entrega" => "DIRECCION",
            "Hora" => "9-13",
        ];

        $reporteincidencia = [
            "Fecha" => "20 de septiembre",
            "Nombre_del_inspector" => "juan",
            "Piezas_rechazadas" => "12",
            "Observaciones" => "sin observaciones",
            "Hora" => "12:20"

        ];

        return response()->json([
            "pedido" => $pIncidencia, "Reporte_de_incidencias" => $reporteincidencia
        ], Response::HTTP_OK);
    }
    //aprobar incidencias boton
    public function aprobarInc(Request $request)
    {
        return response()->json(["msg" => "incidencia aprobada", "status" => "ok"], 201);
    }

    public function rechazarInc(Request $request)
    {
        return response()->json([
            "msg" => "incidencia rechazada y enviada a ODDO correctamente",
        ], 201);
    }
    //seguimiento de pedido
    public function pedidos()
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
