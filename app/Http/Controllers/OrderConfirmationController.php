<?php

namespace App\Http\Controllers;

use App\Models\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderConfirmationController extends Controller
{
    public function ConfirmOrderProducts(Request $request)
    {
        $ids = $request->id_order_products;
        if($request->value == 'general'){
            $this->validate($request,[
                'code_sale' => 'required',
                'order_purchase_id'  => 'required',
            ]);

            $registros = DB::table('order_confirmations')->where('order_purchase_id', $request->order_purchase_id)->get();
            if($registros){
                $productos_confirmados = count($registros);
                $productos_totales = DB::table('order_purchase_products')->where('order_purchase_id',$request->order_purchase_id)->count();
            
                // Si hay productos faltantes, crear confirmaciones para ellos
                if ($productos_confirmados < $productos_totales) {
                    $productos_faltantes = $productos_totales - $productos_confirmados;
                    $productos = DB::table('order_purchase_products')->where('order_purchase_id',$request->order_purchase_id)->get();
                    foreach ($productos as $producto){
                        $existe_registro = DB::table('order_confirmations')
                                            ->where('id_order_products', $producto->id)
                                            ->where('order_purchase_id', $request->order_purchase_id)
                                            ->exists();
                        if (!$existe_registro) {
                            OrderConfirmation::create([
                                'code_sale'=> $request->code_sale,
                                'order_purchase_id' => $request->order_purchase_id,
                                'id_order_products' => $producto->id,
                                'status' => 1,
                                'description'=> 'COMPLETADO'
                            ]);
                        }
                    }
                }
                //////////////SI HAY REGISTROS Y ACTUALIZA A GENERAL////////////////////////////////
                foreach ($registros as $registro) {
                    if ($registro->description == 'PARCIAL') {
                        DB::table('order_confirmations')->where('id', $registro->id)->update([
                            'description' => 'COMPLETADO'
                        ]);
                    }
                }   
            }else{
                $productos = DB::table('order_purchase_products')->where('order_purchase_id',$request->order_purchase_id)->get();
                foreach ($productos as $producto){
                    OrderConfirmation::create([
                        'code_sale'=> $request->code_sale,
                        'order_purchase_id' => $request->order_purchase_id,
                        'id_order_products' => $producto->id,
                        'status' => 1,
                        'description'=> 'COMPLETADO'
                    ]);
                }
            }
            return response()->json(['message' => 'Se confirmaron los pedidos, por General.', 'status' => 200], 200); 

        }elseif($request->value == 'parcial'){
            $this->validate($request,[
                'code_sale' => 'required',
                'order_purchase_id'  => 'required',
                'id_order_products' => 'required|array'
            ]);
            
            foreach ($ids as $id){
                OrderConfirmation::create([
                    'code_sale'=> $request->code_sale,
                    'order_purchase_id' => $request->order_purchase_id,
                    'id_order_products' => $id,
                    'status' => 1,
                    'description'=> 'PARCIAL'
                ]);
            }
            return response()->json(['message' => 'Se confirmaron los pedidos, por Parcial.', 'status' => 200], 200); 
        } 
         
    }
}
