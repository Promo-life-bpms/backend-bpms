<?php

namespace App\Http\Controllers;

use App\Models\OrderPurchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;

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



        if ($request->ordenes_proximas) {
            $sales =  Sale::with('currentStatus', "orders")
                ->join('order_purchases', 'order_purchases.code_sale', '=', 'sales.code_sale')
                //->where('order_purchases.planned_date', '>=', $fechaProxima)
                ->orderby('order_purchases.planned_date', 'ASC')
                ->paginate($per_page);
        } else {

            $sales = Sale::with('currentStatus', "orders")->paginate($per_page);
        }




        // $ordenes = OrderPurchase::where('planned_date','=', Carbon::now());
        return response()->json([
            'pedidos' => $sales,
            // 'ordenes' => $ordenes
        ], 200);
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
            'currentStatus',
            'saleProducts',
            'moreInformation',
            'orders',
            'routeDeliveries',
            'inspections',
            'incidences'
        ])->where('code_sale', $sale_id)->first();

        if ($sale) {
            return response()->json(['data' => $sale], 200);
        }

        return response()->json(['pedido' => "No hay informacion acerca de este pedido"], 200);
    }
}
