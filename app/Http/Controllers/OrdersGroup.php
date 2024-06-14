<?php

namespace App\Http\Controllers;

use App\Models\OrdersGroup as ModelsOrdersGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersGroup extends Controller
{
    public function create(Request $request)
    {

        $request->validate([
            'code_order_oc' => 'required',
            'code_order_ot' => 'required',
            'code_sale' => 'required',
            'description' => 'required',
            'product_id' => 'required',
        ]);

        //$sale =  DB::table('sales')->where('code_sale', $request->code_sale)->first();
        $orderOC = DB::table('order_purchases')->where('code_order', $request->code_order_oc)->first();
        $order_product = DB::table('order_purchase_products')->where('order_purchase_id', $orderOC->id)->first();
        $order_confirmation = DB::table('order_confirmations')->where('code_sale', $request->code_sale)->where('id_order_products', $order_product->id)->first();
        if (empty($order_confirmation)) {
            return response()->json(['no se ha confirmado la orden']);
        } else {
            $order_group =  ModelsOrdersGroup::create([
                'code_order_oc' => $request->code_order_oc,
                'code_order_ot' => $request->code_order_ot,
                'code_sale' => $request->code_sale,
                'description' => $request->description,
                'product_id' => $request->product_id,
                'planned_date' => $request->planned_date
            ]);
        }
        return response()->json(['ordenes' => $order_group]);
    }
    public function update()
    {
    }
}
