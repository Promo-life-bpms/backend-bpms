<?php

namespace App\Http\Controllers;

use App\Models\ConfirmRoute;
use App\Models\DeliveryRoute;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfirmRouteController extends Controller
{
    public function ConfirmationRoute(Request $request)
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
        $rutas = DeliveryRoute::where('code_sale', $sale_id)->where('type_of_destiny', 'Almacen PM')->get();
        $rutasConteo = [];
        $recepConteo =[];
        foreach ($rutas as $ruta) {

            $rutasConteo[] = $ruta->product_id;
            $recepConteo[] = ConfirmRoute::where('id_product_order', $ruta->product_id)->where('destination', 'Almacen PM')->get();
        }
        $conteoRut =  count($rutasConteo);
        $conteoRec = count($recepConteo);
        if($conteoRec >= 1){

        }
        return $conteoRec;
    }
}
