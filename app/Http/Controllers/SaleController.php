<?php

namespace App\Http\Controllers;

use App\Models\Incidence;
use App\Models\OrderPurchase;
use App\Models\Sale;
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
            'company' => 'required',
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
            ->whereBetween('additional_sale_information.planned_date', [$date_initial, $date_end])
            ->count();
        $fechaExpiracion = Carbon::parse($date_initial);
        $diasDiferencia = $fechaExpiracion->diffInDays($date_end);

        $salesAnterior = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->where('additional_sale_information.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('additional_sale_information.planned_date', [$fechaExpiracion->subDays($diasDiferencia), Carbon::parse($date_end)->subDays($diasDiferencia)])
            ->count();
        //return [$sales, $salesAnterior];
        $porcentajePedido = round(((($sales - $salesAnterior) / $salesAnterior) * 100), 0);

        $incidencia = Incidence::where('incidences.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('incidences.creation_date', [$date_initial, $date_end])
            ->count();

        $incidenciaAnterior = Incidence::where('incidences.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('incidences.creation_date', [$fechaExpiracion->subDays($diasDiferencia), Carbon::parse($date_end)->subDays($diasDiferencia)])
            ->count();
        $porcentajeIncidencia = round(((($incidencia - $incidenciaAnterior) / $incidenciaAnterior) * 100), 0);

        switch ($diasDiferencia) {
            case ($diasDiferencia <= 7):
                $msg = 'dia';
                break;

            case ($diasDiferencia > 7 && $diasDiferencia <= 31):

                $msg = 'dos dias';
                break;
            case ($diasDiferencia > 31 && $diasDiferencia <= 182):

                $msg = 'dos semanas';
                break;
            case ($diasDiferencia > 182 && $diasDiferencia <= 365):

                $msg = 'mes';
                break;


            default:
                $msg = 'no cumple';
        }
        $tiempoInicio = strtotime($date_initial);
        $tiempoFin = strtotime($date_end);
        $dia = 86400;
        $dos_dias = 172800;
        $semana = 604800;
        $mes = 2419200;

        while ($tiempoInicio <= $tiempoFin) {
            # Podemos recuperar la fecha actual y formatearla

            $fechaActual =  date("Y-m-d", $tiempoInicio);
            printf("Fecha dentro del periodo : %s\n ", $fechaActual);

            # Sumar el incremento para que en algún momento termine el ciclo
            $tiempoInicio += $dia;

          /*     if ($diasDiferencia > 7 && $diasDiferencia <= 31) {
                $fechaActual =  date("Y-m-d", $tiempoInicio);
                printf("Fecha dentro del periodo : %s\n ", $fechaActual);

                # Sumar el incremento para que en algún momento termine el ciclo
                $tiempoInicio += $dos_dias;} */
        }



        return [
            "pedidos" => $sales, "periodo_anterior" => $salesAnterior, "porcentaje" => $porcentajePedido . "%",
            "incidencias" => $incidencia, "incidencia_anterior" => $incidenciaAnterior, "porcentaje2" => $porcentajeIncidencia . "%",
            "dias" => $msg
        ];
        //grafica

    }
    public function calendario()
    {
        $ped = Sale::with(['moreInformation'])->get();
        return $ped;
    }
}
