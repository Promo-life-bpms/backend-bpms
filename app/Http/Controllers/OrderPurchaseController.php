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
    public function store(Request $request)
    {
        //
        $validation = Validator::make($request->all(), [
            'hora' => 'required',
            'id_order_purchases' => 'required',
            'status' => 'required',

            'status_purchase_products' => 'required|array',
            'status_purchase_products.*.id_order_purchase_products' => 'required',
            'status_purchase_products.*.cantidad_seleccionada' => 'required'
        ]);
        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 422);
        }

        foreach ($request->status_purchase_products as $newProductStatus) {
            $statusOT = StatusOT::create([
                'hora' => $request->hora,
                'id_order_purchases' => $request->id_order_purchases,
                'status' => $request->status,
                'id_order_purchase_products' => $newProductStatus["id_order_purchase_products"],
                'cantidad_seleccionada' => $newProductStatus["cantidad_seleccionada"],
            ]);
        }
        return response()->json(["msg" => $statusOT], 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderPurchase  $orderPurchase
     * @return \Illuminate\Http\Response
     */
    public function show(StatusOT $statusOT)
    {
        $statusOT = StatusOT::all();
        return response()->json(["msg" => $statusOT], 201);
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
