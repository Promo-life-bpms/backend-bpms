<?php

namespace App\Http\Controllers;

use App\Models\ConfirmRouteReceipt;
use App\Models\Sale;
use App\Models\SaleStatusChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusOrdersController extends Controller
{
    public function StatusTwo($sale_id)
    {
        $sale = Sale::where('code_sale', $sale_id)->first();
        $idPedido = $sale->id;

        $Orders = DB::table('order_purchases')->where('code_sale', $sale_id)->where(function ($query) {
            $query->where('code_order', 'like', 'OC-%')->orWhere('code_order', 'like', 'OT-%');
        })->get();
    

        $NumOrders = [];
        foreach ($Orders as $Order){
            $idOrder = $Order->id;
            $productos_totales = DB::table('order_purchase_products')->where('order_purchase_id',$idOrder)->count();
            $NumOrders[] = $productos_totales;   
        }

        $OrdersFinales = array_sum($NumOrders);
        
        $registros = [];
        foreach ($Orders as $order){
            $id = $order->id;
            $ya = DB::table('order_confirmations')->where('order_purchase_id', $id)->count();
            $registros[] = $ya;
        }
        $ConfirmationOrders = array_sum($registros);

        if($OrdersFinales == $ConfirmationOrders)
        {
            $registros = DB::table('sale_status_changes')->where('sale_id',$idPedido)->get();
            if(!$registros){
                SaleStatusChange::create([
                    'sale_id' => $idPedido,
                    'status_id' => 15,
                ]);
                return response()->json(['message' => 'Está completo el paso dos', 'status'=> 200], 200);
            }
            return response()->json(['message' => 'Ya esta registrado el estado dos', 'status'=> 404], 404);           
        }else{
            return response()->json(['message' => 'Aún no esta completo', 'status'=> 404], 404);
        }      
    }

    public function confirmationOrderRoute(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
       $datos = DB::table('confirm_route_receipts')->where('id', $request->id)->get();
       
       return $datos;

    }
}
