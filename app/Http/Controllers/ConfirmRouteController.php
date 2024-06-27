<?php

namespace App\Http\Controllers;

use App\Models\ConfirmRoute;
use App\Models\DeliveryRoute;
use App\Models\Sale;
use App\Models\SaleStatusChange;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfirmRouteController extends Controller
{
    public function ConfirmationRoute(Request $request, $sale_id)
    {
        $this->validate($request, [
            'id_product_order' => 'required',
            'destiny' => 'required',

        ]);

        $inforDelivery = DB::table('delivery_routes')->where('product_id', $request->id_product_order)
            ->where('status_delivery', 'Completo')
            ->where('type_of_destiny', $request->destiny)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$inforDelivery) {
            return response()->json(['message' => 'Aún no se actualiza la ruta.'], 409);
        } else {
            $type = $inforDelivery->type;
            $idDelivery =  $inforDelivery->id;
            ConfirmRoute::create([
                'id_product_order' => $request->id_product_order,
                'id_delivery_routes' => $idDelivery,
                'reception_type' => $type,
                'destination' => $request->destiny,
            ]);
        }
        /////////////////sattus recepcion
        $sale = Sale::where('code_sale', $sale_id)->first();
        $rutas = DeliveryRoute::where('code_sale', $sale_id)->where('type_of_destiny', 'Almacen PM')->get();
        $rutasConteo = [];
        $recepConteo = [];
        foreach ($rutas as $ruta) {

            $rutasConteo[] = $ruta->product_id;
            $recepsConteo[] = ConfirmRoute::where('id_product_order', $ruta->product_id)
                ->where('destination', 'Almacen PM')
                ->latest()
                ->first();
        }
        // return $recepsConteo;
        $conteoP = [];
        $conteoC = [];
        foreach ($recepsConteo as $recepConteo) {
            //return $recepConteo;
            if ($recepConteo->reception_type == 'Parcial') {
                $conteoP[] = $recepConteo->reception_type;
            } else if ($recepConteo->reception_type == 'Total') {
                $conteoC[] = $recepConteo->reception_type;
            }
        }

        $conteoRut =  count($rutasConteo);
        $conteoRecPar = count($conteoP);
        $conteoRecCom = count($conteoC);
        //$conteoRec = count($recepsConteo);
        $sale_status = SaleStatusChange::where('status_id', 34)->where('sale_id', $sale->id)->first();
        $status_sales = DB::table('statuses')->where('id', 34)->first();
        //return $conteoRecPar;
        if (empty($sale_status)) {
            if ($conteoRecPar >= 1  && $conteoRecPar <= $conteoRut) {
                SaleStatusChange::create([
                    'sale_id' => $sale->id,
                    'status_id' => 34,
                    'status' => 0,
                    'visible' => 0,
                    'status_name' => $status_sales->status,
                    'slug' => $status_sales->slug
                ]);
            } else if ($conteoRecCom == $conteoRut) {
                SaleStatusChange::create([
                    'sale_id' => $sale->id,
                    'status_id' => 34,
                    'status' => 0,
                    'visible' => 1,
                    'status_name' => $status_sales->status,
                    'slug' => $status_sales->slug
                ]);
            }
        } else {
            if ($conteoRecPar >= 1  && $conteoRecPar <= $conteoRut) {
                DB::table('sale_status_changes')->where('status_id', 34)->update([
                    'sale_id' => $sale->id,
                    'status_id' => 34,
                    'status' => 0,
                    'visible' => 0
                ]);
            } else if ($conteoRecCom == $conteoRut) {
                DB::table('sale_status_changes')->where('status_id', 34)->update([
                    'sale_id' => $sale->id,
                    'status_id' => 34,
                    'status' => 0,
                    'visible' => 1
                ]);
            }
        }
        return response()->json(['message' => 'Se confirmo la ruta del producto'], 200);
    }

    public function index($idProductOrder)

    {
        $History = DB::table('confirm_routes')->where('id_product_order', $idProductOrder)->get();

        $Historial = [];
        foreach ($History as $history) {
            $OrderConfirmatio = [
                'id'  => $history->id,
                'reception_type' => $history->reception_type,
                'destination'  => $history->destination,
                'created_at'  => $history->created_at,

            ];
            $Historial[] = $OrderConfirmatio;
        }
        return response()->json(['HistoryConfirmationRoutes' => $Historial], 200);
    }
    public function StatusRecepcion($sale_id)
    {
        $sale = Sale::where('code_sale', $sale_id)->first();
        $rutas = DeliveryRoute::where('code_sale', $sale_id)->where('type_of_destiny', 'Almacen PM')->get();
        $rutasConteo = [];
        $recepConteo = [];
        foreach ($rutas as $ruta) {

            $rutasConteo[] = $ruta->product_id;
            $recepsConteo[] = ConfirmRoute::where('id_product_order', $ruta->product_id)
                ->where('destination', 'Almacen PM')
                ->latest()
                ->first();
        }
        // return $recepsConteo;
        $conteoP = [];
        $conteoC = [];
        foreach ($recepsConteo as $recepConteo) {
            //return $recepConteo;
            if ($recepConteo->reception_type == 'Parcial') {
                $conteoP[] = $recepConteo->reception_type;
            } else if ($recepConteo->reception_type == 'Total') {
                $conteoC[] = $recepConteo->reception_type;
            }
        }

        $conteoRut =  count($rutasConteo);
        $conteoRecPar = count($conteoP);
        $conteoRecCom = count($conteoC);
        //$conteoRec = count($recepsConteo);
        $sale_status = SaleStatusChange::where('status_id', 34)->where('sale_id', $sale->id)->first();
        $status_sales = DB::table('statuses')->where('id', 34)->first();
        //return $conteoRecPar;
        if (empty($sale_status)) {
            if ($conteoRecPar >= 1  && $conteoRecPar <= $conteoRut) {
                SaleStatusChange::create([
                    'sale_id' => $sale->id,
                    'status_id' => 34,
                    'status' => 0,
                    'visible' => 0,
                    'status_name' => $status_sales->status,
                    'slug' => $status_sales->slug
                ]);
            } else if ($conteoRecCom == $conteoRut) {
                SaleStatusChange::create([
                    'sale_id' => $sale->id,
                    'status_id' => 34,
                    'status' => 0,
                    'visible' => 1,
                    'status_name' => $status_sales->status,
                    'slug' => $status_sales->slug
                ]);
            }
        } else {
            if ($conteoRecPar >= 1  && $conteoRecPar <= $conteoRut) {
                DB::table('sale_status_changes')->where('status_id', 34)->update([
                    'sale_id' => $sale->id,
                    'status_id' => 34,
                    'status' => 0,
                    'visible' => 0
                ]);
            } else if ($conteoRecCom == $conteoRut) {
                DB::table('sale_status_changes')->where('status_id', 34)->update([
                    'sale_id' => $sale->id,
                    'status_id' => 34,
                    'status' => 0,
                    'visible' => 1
                ]);
            }
        }
        return response()->json(['Cambios de status hecho'], 200);
    }
}
