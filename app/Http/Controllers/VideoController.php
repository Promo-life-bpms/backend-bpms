<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function storeVideoInfo()
    {
        $videos = [
            [
                'nombre' => 'Inicio de sesion',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/iniciodesesion.mp4',
                'rol' => '*',
            ],
            [
                'nombre' => 'Inicio',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/vistadeinicio.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            [
                'nombre' => 'Recepción de inventario',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/recepciondeinventario.mp4',
                'rol' => 'almacen, maquilador',
            ],
            [
                'nombre' => 'Modificar incidencia',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/modificarincidencia.mp4',
                'rol' => 'ventas, control_calidad, gerente-operaciones, jefe_de_logistica, logistica-y-mesa-de-control',
            ],
            [
                'nombre' => 'Crear incidencia',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearincidencias.mp4',
                'rol' => 'ventas, administrator, gerente-operaciones, control_calidad, jefe_de_logistica, maquilador, logistica-y-mesa-de-control',
            ],
            [
                'nombre' => 'Tabla pedidos',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/tablapedidos.mp4',
                'rol' => '*',
            ],
            [
                'nombre' => 'Ver recepcion de inventario',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/verrecepciondeinventario.mp4',
                'rol' => '*',
            ],
            [
                'nombre' => 'Modificar informacion de un pedido asignado a una ruta de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/modificarinformaciondeunpedidoasignadoaunarutadeentrega.mp4',
                'rol' => 'logistica-y-mesa-de-control, jefe_de_logistica, gerente-operaciones',
            ],
            [
                'nombre' => 'Fecha real de la solucion de una incidencia',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/fecharealdelasoluciondeunaincidencia.mp4',
                'rol' => 'ventas, control_calidad, gerente-operaciones, jefe_de_logistica, logistica-y-mesa-de-control',
            ],
            [
                'nombre' => 'Crear ruta de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearrutadeentrega.mp4',
                'rol' => 'administrator, compras, gerente-operaciones, jefe_de_logistica, logistica-y-mesa-de-control',
            ],
            [
                'nombre' => 'Ver incidencia',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/verincidencia.mp4',
                'rol' => '*',
            ],
            [
                'nombre' => 'Ver inspeccion de calidad',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/verinspecciondecalidad.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            [
                'nombre' => 'Ver  remision de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/verremisiondeentrega.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            [
                'nombre' => 'Detalle de un pedido ',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/detalledeunpedido.mp4',
                'rol' => '*',
            ],
            [
                'nombre' => 'Rutas de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/rutadeentrega.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            [
                'nombre' => 'Crear inspeccion de calidad',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearinspecciondecalidad.mp4',
                'rol' => 'control_calidad, gerente-operaciones',
            ],
            [
                'nombre' => 'Crear bitácora',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearbitacora.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            [
                'nombre' => 'Ver cambio de estado de una OT',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/vercambiodeestadoot.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            [
                'nombre' => 'Cambio de estado de una OT',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/cambiodeestadoot.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            [
                'nombre' => 'Crear remisiones de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearremisionesdeentrega.mp4',
                'rol' => 'gerente-operaciones, chofer',
            ],
        ];
        return response()->json(["videos" => $videos]);
    }
}
