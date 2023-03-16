<?php

namespace App\Http\Controllers;

use App\Models\Incidence;
use App\Models\OrderPurchase;
use App\Models\Sale;
use App\Models\StatusOT;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Vista de tabla de Pedidos
        // crear una var que se llame per_page = 10
        $per_page = 15;
        if ($request->per_page) {
            //Asignarle el valor al var per_page
            $per_page = $request->per_page;
        }


        $sales = null;
        if ($request->ordenes_proximas) {
            $sales =  Sale::with('moreInformation', 'lastStatus', "detailsOrders")
                ->join('order_purchases', 'order_purchases.code_sale', '=', 'sales.code_sale')
                //->where('order_purchases.planned_date', '>=', $fechaProxima)
                ->orderby('order_purchases.planned_date', 'ASC')
                ->paginate($per_page);
        } else {
            $sales = Sale::with('moreInformation', 'lastStatus', "detailsOrders")->paginate($per_page);
        }

        foreach ($sales as $sale) {
            if ($sale->lastStatus) {
                $sale->lastStatus->slug = $sale->lastStatus->status->slug;
                $sale->lastStatus->last_status = $sale->lastStatus->status->status;
                unset($sale->lastStatus->status);
                unset($sale->lastStatus->id);
                unset($sale->lastStatus->sale_id);
                unset($sale->lastStatus->status_id);
                unset($sale->lastStatus->updated_at);
            }
        }

        return response()->json([
            'msg' => 'Lista de pedidos', 'data' => ["sales" => $sales]
            // 'ordenes' => $ordenes
        ], response::HTTP_OK); //200
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show($sale_id)
    {
        // Vista de Detalle de los pedidos
        /*
        # Generales
        # OT, OC relacionadas
        # //TODO: Incidencias Relacionadas, Datos generales
        # //TODO: Inspecciones Relacionadas, Datos generales
        # //TODO: Historial de Cambios BPMS y Odoo
        # //TODO: Entregas Relacionadas, Datos generales
        */
        $sale = Sale::with([
            'moreInformation',
            'lastStatus',
            'saleProducts',
            'detailsOrders',
            'routeDeliveries',
            'inspections',
            'incidences',
            "ordersDeliveryRoute"
        ])->where('code_sale', $sale_id)->first();

        //Detalle del pedido seleccionado
        if ($sale) {
            foreach ($sale->routeDeliveries as $routeDelivery) {
                $routeDelivery->deliveryRoute->name_chofer = $routeDelivery->deliveryRoute->user->name;
                unset($routeDelivery->deliveryRoute->user);
            }
            $sale->lastStatus->slug = $sale->lastStatus->status->slug;
            $sale->lastStatus->last_status = $sale->lastStatus->status->status;
            unset($sale->lastStatus->status);
            unset($sale->lastStatus->id);
            unset($sale->lastStatus->sale_id);
            unset($sale->lastStatus->status_id);
            unset($sale->lastStatus->updated_at);

            return response()->json(['msg' => 'Detalle del pedido', 'data' => ["sale", $sale]], response::HTTP_OK); //200
        }

        return response()->json(['msg' => "No hay informacion acerca de este pedido"], response::HTTP_OK); //200
    }
    //Ver pedidos de cada vendedor
    public function viewPedidosPorVendedor()
    {
        $pedidos = auth()->user()->sales;
        return response()->json([
            'msg' => "Vizualizar mis pedidos",
            'data' => ["pedidos" => $pedidos],
        ], Response::HTTP_OK); //200
    }

    public function estadisticas(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'date_end' => 'required|date_format:Y-m-d',
            'date_initial' => 'required|date_format:Y-m-d',
            'company' => '',
        ]);
        if ($validation->fails()) {
            return response()->json(
                [
                    'msg' => "Error al validar informacion de la ruta de entrega",
                    'data' => ['errorValidacion' => $validation->getMessageBag()]
                ],
                response::HTTP_UNPROCESSABLE_ENTITY
            ); // 422
        }

        $date_end = date($request->date_end);
        $date_initial = date($request->date_initial);
        $company = $request->company;
        $sales = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->where('additional_sale_information.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('additional_sale_information.planned_date', [$date_initial, $date_end])->get()
            ->count();

        $fechaExpiracion = Carbon::parse($date_initial);
        $diasDiferencia = $fechaExpiracion->diffInDays($date_end);

        $salesAnterior = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->where('additional_sale_information.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('additional_sale_information.planned_date', [$fechaExpiracion->subDays($diasDiferencia), Carbon::parse($date_end)->subDays($diasDiferencia)])
            ->count();
        // return [$sales, $salesAnterior];
        if ($salesAnterior > 0) {
            $porcentajePedido = round(((($sales - $salesAnterior) / $salesAnterior) * 100), 0);
        } else {
            /*   return response()->json(
                [
                    'msg' => "Sin pedidos en el periodo seleccionado",
                ],

            ); */
        }

        $incidencia = Incidence::where('incidences.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('incidences.creation_date', [$date_initial, $date_end])
            ->count();

        $incidenciaAnterior = Incidence::where('incidences.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('incidences.creation_date', [$fechaExpiracion->subDays($diasDiferencia), Carbon::parse($date_end)->subDays($diasDiferencia)])
            ->count();



        if ($incidenciaAnterior > 0) {
            $porcentajeIncidencia = round(((($incidencia - $incidenciaAnterior) / $incidenciaAnterior) * 100), 0);
        } else {
            /*   return response()->json(
                [
                    'msg' => "Sin incidencias en el periodo seleccionado",
                ],

            ); */
        }



        $sale = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->where('additional_sale_information.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('additional_sale_information.planned_date', [$date_initial, $date_end])
            ->select('additional_sale_information.planned_date')
            ->select(\DB::raw('SUBSTRING_INDEX(additional_sale_information.planned_date, " ", 1) as planned_date'))
            ->get();
        $incidenciaPer = Incidence::where('incidences.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('incidences.creation_date', [$date_initial, $date_end])
            ->select('incidences.creation_date')
            ->select(\DB::raw('SUBSTRING_INDEX(incidences.creation_date, " ", 1) as creation_date'))
            ->get();

        $tiempoInicio = strtotime($date_initial);
        $tiempoFin = strtotime($date_end);
        $dia = 86400;
        $dos_dias = 172800;
        $semana = 604800;
        $mes = 2419200;

        while ($tiempoInicio <= $tiempoFin) {
            switch ($diasDiferencia) {
                case ($diasDiferencia <= 7):
                    $fechaActual = date("Y-m-d", $tiempoInicio);
                    printf("Fecha dentro del periodo : %s\n ", $fechaActual);
                    printf("Pedidos : %s\n ", $sale->where('planned_date', $fechaActual)->count());
                    printf("Incidencias : %s\n ", $incidenciaPer->where('creation_date', $fechaActual)->count());
                    $tiempoInicio += $dia;
                    break;

                case ($diasDiferencia > 7 && $diasDiferencia <= 31):
                    $fechaActual = date("Y-m-d", $tiempoInicio);
                    $fechaSiguiente = date("Y-m-d", $tiempoInicio + $dia);
                    printf("Fecha dentro del periodo : %s\n", $fechaActual);
                    printf("Pedidos : %s\n ", $sale->whereBetween('planned_date', [$fechaActual, $fechaSiguiente])->count());
                    printf("Incidencias : %s\n ", $incidenciaPer->whereBetween('creation_date', [$fechaActual, $fechaSiguiente])->count());
                    $tiempoInicio += $dos_dias;
                    break;
                case ($diasDiferencia > 31 && $diasDiferencia <= 182):

                    $fechaActual = date("Y-m-d", $tiempoInicio);
                    $fechaSiguiente = date("Y-m-d", $tiempoInicio + $semana);
                    printf("Fecha dentro del periodo : %s\n ", $fechaActual);
                    printf("Pedidos : %s\n ", $sale->whereBetween('planned_date', [$fechaActual, $fechaSiguiente])->count());
                    printf("Incidencias : %s\n ", $incidenciaPer->whereBetween('creation_date', [$fechaActual, $fechaSiguiente])->count());
                    $tiempoInicio += $semana;

                    break;
                case ($diasDiferencia > 182 && $diasDiferencia <= 365):
                    $fechaActual = date("Y-m-d", $tiempoInicio);
                    $fechaSiguiente = date("Y-m-d", $tiempoInicio + $mes);
                    printf("Fecha dentro del periodo : %s\n ", $fechaActual);
                    printf("Pedidos : %s\n ", $sale->whereBetween('planned_date', [$fechaActual, $fechaSiguiente])->count());
                    printf("Incidencias : %s\n ", $incidenciaPer->whereBetween('creation_date', [$fechaActual, $fechaSiguiente])->count());
                    $tiempoInicio += $mes;
                    break;


                default:
                    $msg = 'no cumple';
            }
        }

        $ot = OrderPurchase::with("lastStatusOT")
            ->where('order_purchases.code_order', 'LIKE',  'OT' . '%')
            ->where('order_purchases.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('order_purchases.planned_date', [$date_initial, $date_end])
            ->get();
        return $ot;

        $pendientes = OrderPurchase::join('status_o_t_s', 'status_o_t_s.id_order_purchases', 'order_purchases.id')
            ->where('order_purchases.code_order', 'LIKE', '%' . 'OT' . '%')
            ->where('order_purchases.company', 'LIKE', '%' . $company . '%')
            ->whereIn('status_o_t_s.status', ["Retrasado", "Pendiente", "En espera de entrega de maquilador", "Listo para recoger"])
            //->whereBetween('order_purchases.order_date', [$date_initial, $date_end])
            //->whereBetween('order_purchases.planned_date', [$date_initial, $date_end])
            ->get();
        return $pendientes;

        $completado = OrderPurchase::where('order_purchases.code_order', 'LIKE', '%' . 'OT' . '%')
            ->where('order_purchases.company', 'LIKE', '%' . $company . '%')
            ->whereIn('order_purchases.status', ["Listo para recoger", "Recepcion Inventario Parcial", "Recepcion Inventario Completo"])
            ->whereBetween('order_purchases.order_date', [$date_initial, $date_end])
            ->select('order_purchases.status')
            ->get();
        return [
            "pedidos" => $sales, "periodo_anterior" => $salesAnterior, "porcentaje" => $porcentajePedido . "%",
            "incidencias" => $incidencia, "incidencia_anterior" => $incidenciaAnterior, "porcentaje2" => $porcentajeIncidencia . "%",

        ];
        //grafica de pastel


    }
    public function calendario()
    {
        $ped = Sale::with(['moreInformation'])->get();
        return $ped;
    }
}
