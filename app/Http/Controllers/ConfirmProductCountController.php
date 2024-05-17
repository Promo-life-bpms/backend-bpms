<?php

namespace App\Http\Controllers;

use App\Models\ConfirmProductCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfirmProductCountController extends Controller
{
    public function ProductCount(Request $request)
    {
        $this->validate($request, [
            'id_product' => 'required',
            'confirmation_type' => 'required',
            'type' => 'required'
        ]);

        $infoConfirmRoute = DB::table('confirm_routes')->where('id_product_order', $request->id_product)->where('reception_type', $request->confirmation_type)
                                                    ->where('destination', $request->type)->orderBy('created_at', 'desc')
                                                    ->first();
        $type = $infoConfirmRoute->destination;
        $confirmation_type = $infoConfirmRoute->reception_type;
        $id_confirm_routes = $infoConfirmRoute->id;
        if(!$infoConfirmRoute)
        {
            return response()->json(['message' => 'Es posible que aún no se confirme la recepción del producto.'], 409);
            
        }else{
            ConfirmProductCount::create([
                'id_product' => $request->id_product,
                'type' => $type,
                'confirmation_type' => $confirmation_type,
                'id_confirm_routes' => $id_confirm_routes,
                'observation' => $request->observation
            ]);
        }
        return response()->json(['message' => 'Se confirmó el conteo de los productos'], 200);
    }
    public function ProductCountHistory($idProductOrder)
    {
        $ProductCountHistory = DB::table('confirm_product_counts')->where('id_product', $idProductOrder)->get();

    
        return response()->json(['Product_count_history' => $ProductCountHistory], 200);
    }
}
