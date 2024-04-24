<?php

namespace App\Http\Controllers;

use App\Models\OrderConfirmation;
use Illuminate\Http\Request;

class OrderConfirmationController extends Controller
{
    public function ConfirmOrderProducts(Request $request)
    {
        $this->validate($request,[
            'code_sale' => 'required',
            'order_purchase_id'  => 'required',
            'id_order_products' => 'required'
        ]);
        if($request->value == 'general'){
            $confirmar = OrderConfirmation::create([
                'code_sale'=> $request->code_sale,
                'order_purchase_id' => $request->order_purchase_id,
                'id_order_products' => $request->id_order_products,
            ]);
        }
    }
}
