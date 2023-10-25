<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function storeVideoInfo()
    {


        $video = [

            $videoInfo = [
                'nombre' => 'Inicio de sesion',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/iniciodesesion.mp4',
                'rol' => '*',
            ],
            $videoInfo = [
                'video' => 'Inicio',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/vistadeinicio.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            $videoInfo = [
                'video' => 'Recepción de inventario',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/recepciondeinventario.mp4',
                'rol' => 'almacen, maquilador',
            ],
            $videoInfo = [
                'video' => 'Modificar incidencia',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/modificarincidencia.mp4',
                'rol' => 'ventas, control_calidad, gerente-operaciones, jefe_de_logistica, logistica-y-mesa-de-control',
            ],
            $videoInfo = [
                'video' => 'Crear incidencia',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearincidencias.mp4',
                'rol' => 'ventas, administrator, gerente-operaciones, control_calidad, jefe_de_logistica, maquilador, logistica-y-mesa-de-control',
            ],
            $videoInfo = [
                'video' => 'Tabla pedidos',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/tablapedidos.mp4',
                'rol' => '*',
            ],
            $videoInfo = [
                'video' => 'Ver recepcion de inventario',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/verrecepciondeinventario.mp4',
                'rol' => '*',
            ],
            $videoInfo = [
                'video' => 'Modificar informacion de un pedido asignado a una ruta de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/modificarinformaciondeunpedidoasignadoaunarutadeentrega.mp4',
                'rol' => 'logistica-y-mesa-de-control, jefe_de_logistica, gerente-operaciones',
            ],
            $videoInfo = [
                'video' => 'Fecha real de la solucion de una incidencia',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/fecharealdelasoluciondeunaincidencia.mp4',
                'rol' => 'ventas, control_calidad, gerente-operaciones, jefe_de_logistica, logistica-y-mesa-de-control',
            ],
            $videoInfo = [
                'video' => 'Crear ruta de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearrutadeentrega.mp4',
                'rol' => 'administrator, compras, gerente-operaciones, jefe_de_logistica, logistica-y-mesa-de-control',
            ],

            $videoInfo = [
                'video' => 'Ver incidencia',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/verincidencia.mp4',
                'rol' => '*',
            ],
            $videoInfo = [
                'video' => 'Ver inspeccion de calidad',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/verinspecciondecalidad.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            $videoInfo = [
                'video' => 'Ver  remision de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/verremisiondeentrega.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            $videoInfo = [
                'video' => 'Detalle de un pedido ',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/detalledeunpedido.mp4',
                'rol' => '*',
            ],


            $videoInfo = [
                'video' => 'Rutas de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/rutadeentrega.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            $videoInfo = [
                'video' => 'Crear inspeccion de calidad',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearinspecciondecalidad.mp4',
                'rol' => 'control_calidad, gerente-operaciones',
            ],
            $videoInfo = [
                'video' => 'Crear bitácora',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearbitacora.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            $videoInfo = [
                'video' => 'Ver cambio de estado de una OT',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/vercambiodeestadoot.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],

            $videoInfo = [
                'video' => 'Cambio de estado de una OT',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/cambiodeestadoot.mp4',
                'rol' => 'administrator, almacen, chofer, control_calidad, compras, ventas, logistica-y-mesa-de-control, gerente, asistente_de_gerente, jefe_de_logistica, gerente-operaciones',
            ],
            $videoInfo = [
                'video' => 'Crear remisiones de entrega',
                'url' => 'https://api-bpms.promolife.lat/storage/capacitacion/crearremisionesdeentrega.mp4',
                'rol' => 'gerente-operaciones, chofer',
            ],






        ];
        return response()->json($video);
    }
}
