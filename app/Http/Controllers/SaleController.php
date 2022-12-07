<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

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

        $sales = Sale::with('currentStatus')->paginate($per_page);
        return response()->json(['pedidos' => $sales], 200);
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
            'routeDeliveries'
            // 'inspecciones',
            // TODO: 'incidencias'
        ])->where('code_sale', $sale_id)->first();
        if ($sale) {
            return response()->json(['pedido' => $sale], 200);
        }
        return response()->json(['pedido' => "No hay informacion acerca de este pedido"], 200);
    }
}
