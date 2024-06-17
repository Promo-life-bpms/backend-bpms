<?php

namespace App\Http\Controllers;

use App\Models\OrdersGroup as ModelsOrdersGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrdersGroup extends Controller
{
    public function create(Request $request)
    {
        $data = $request->input('ordenes');

        $errors = [];
        $ordesrsGroup = [];

        foreach ($data as $index => $order) {
            $validator = Validator::make($order, [
                'code_order_oc' => 'required',
                'code_sale' => 'required',
                'description' => 'required',
                'product_id_oc' => 'required',
            ]);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors();
                continue;
            }

            $orderOC = DB::table('order_purchases')->where('code_order', $order['code_order_oc'])->first();
            $order_product = DB::table('order_purchase_products')->where('order_purchase_id', $orderOC->id)->first();
            $order_confirmation = DB::table('order_confirmations')
                ->where('code_sale', $order['code_sale'])
                ->where('id_order_products', $order_product->id)
                ->first();

            if ($order_confirmation || $order_product) {
                $order_group = ModelsOrdersGroup::create([
                    'code_order_oc' => $order['code_order_oc'],
                    'code_order_ot' => json_encode($order['code_order_ot']), // Convertimos array a JSON
                    'code_sale' => $order['code_sale'],
                    'description' => $order['description'],
                    'product_id_oc' => $order['product_id_oc'],
                    'product_id_ot' => json_encode($order['product_id_ot']), // Convertimos array a JSON
                    'planned_date' => $order['planned_date']
                ]);
                $ordesrsGroup[] = $order_group;
            } else {
                $errors[$index] = ['no se ha confirmado la orden'];
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        return response()->json(['ordenes' => $ordesrsGroup]);
    }

    /* public function create(Request $request)
    {

        $request->validate([
            'code_order_oc' => 'required',
            'code_order_ot' => 'required',
            'code_sale' => 'required',
            'description' => 'required',
            'product_id_oc' => 'required',
            'product_id_ot' => 'required',
        ]);

        //$sale =  DB::table('sales')->where('code_sale', $request->code_sale)->first();
        $orderOC = DB::table('order_purchases')->where('code_order', $request->code_order_oc)->first();
        $order_product = DB::table('order_purchase_products')->where('order_purchase_id', $orderOC->id)->first();
        $order_confirmation = DB::table('order_confirmations')->where('code_sale', $request->code_sale)->where('id_order_products', $order_product->id)->first();
        $ordesrsGroup = [];
        if ($order_confirmation || $order_product) {
            $order_group =  ModelsOrdersGroup::create([
                'code_order_oc' => $request->code_order_oc,
                'code_order_ot' => $request->code_order_ot,
                'code_sale' => $request->code_sale,
                'description' => $request->description,
                'product_id_oc' => $request->product_id_oc,
                'product_id_ot' => $request->product_id_ot,
                'planned_date' => $request->planned_date
            ]);
            $ordesrsGroup[] = $order_group;
        } elseif (empty($order_confirmation)) {
            return response()->json(['no se ha confirmado la orden']);
        }
        return response()->json(['ordenes' => $order_group]);
    } */
    public function update()
    {
    }
}
