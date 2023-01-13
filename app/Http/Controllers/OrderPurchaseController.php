<?php

namespace App\Http\Controllers;

use App\Models\OrderPurchase;
use App\Models\Sale;
use App\Models\Status;
use App\Models\StatusOT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Return_;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\HttpFoundation\Response;

class OrderPurchaseController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $compra)
    {
        //
        $validation = Validator::make($request->all(), [
            'hora' => 'required',
            'status' => 'required',

            'status_purchase_products' => 'required|array',
            'status_purchase_products.*.id_order_purchase_products' => 'required',
            'status_purchase_products.*.cantidad_seleccionada' => 'required'
        ]);
        if ($validation->fails()) {
            return response()->json([
                'msg' => "Error al ingresar los datos",
                'data' =>
                ["errorValidacion", $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY); //422
        }
        $compra = OrderPurchase::where('code_order', $compra)->first();
        if(!$compra){
            return response()->json(["errors" => "No se ha encontrado la OT"], 404);
        }

        foreach ($request->status_purchase_products as $newProductStatus) {
            $statusOT = StatusOT::create([
                'hora' => $request->hora,
                'id_order_purchases' => $compra->id,
                'status' => $request->status,
                'id_order_purchase_products' => $newProductStatus["id_order_purchase_products"],
                'cantidad_seleccionada' => $newProductStatus["cantidad_seleccionada"],
            ]);
        }
        return response()->json(["msg" => "Orden de Compra creada", 'data' => ["statusOT", $statusOT]], response::HTTP_CREATED); //201
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderPurchase  $orderPurchase
     * @return \Illuminate\Http\Response
     */
    public function show($id_order_purchases)
    {

        $id_order_purchases = OrderPurchase::where('code_order', $id_order_purchases)->get();
        if(!$id_order_purchases){
            return response()->json(["msg" => "No se ha encontrado la OT"], response::HTTP_NOT_FOUND);
        }
        return response()->json(["msg" => "Orden de compra", 'data' => ["id_order_purchases",$id_order_purchases]], response::HTTP_OK);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderPurchase  $orderPurchase
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderPurchase $orderPurchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderPurchase  $orderPurchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrderPurchase $orderPurchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderPurchase  $orderPurchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderPurchase $orderPurchase)
    {
        //
    }
}
