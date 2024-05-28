<?php

namespace App\Http\Controllers;

use App\Models\ConfirmDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfirmDeliveryController extends Controller
{
    public function ConfirmDelivery(Request $request)
    {
        $this->validate($request, [
            'id_product' => 'required',
            'delivery_type' => 'required'
        ]);

        $verificar = DB::table('confirm_product_counts')->where('id_product', $request->id_product)->exists();

        if(!$verificar)
        {
            return response()->json(['message' => 'Aún no se ha realizado la confirmación del conteo del producto.'],409);
        }
        
        ConfirmDelivery::create([
            'id_order_purchase_product' => $request->id_product,
            'delivery_type' => $request->delivery_type
        ]);

        return response()->json(['message' => 'Confirmación de la entrega realizada correctamente.'], 200);
    }

    public function HistoryConfirmDelivery($id_product)
    {
        $DeliveryConfirmationHistory = DB::table('confirm_deliveries')->where('id_order_purchase_product', $id_product)->get();

        if($DeliveryConfirmationHistory->isEmpty()){
            return response()->json(['message' => 'No existe el historial de entrega de este producto.'], 409);
        }

        return response()->json(['delivery_confirmation_history' => $DeliveryConfirmationHistory], 200);
    }
}
