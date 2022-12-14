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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

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
    public function store(Request $request, $sale_id)
    {
        //
        $validation = Validator::make($request->all(), [
            'hora' => 'required',
            'id_order_purchase' => 'required',
            'status' => 'required',

            'status_purchase_products' => 'required|array',
            'status_purchase_products.*.id_order_purchase_products' => 'required',
            'status_purchase_products.*.cantidad_seleccionada' => 'required'
        ]);
        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 422);
        }
        $statusOT = StatusOT::create([
            'hora' => $request->hora,
            'id_order_purchase' =>$request->id_order_purchase,
            'status'=>$request->status,
            'id_order_purchase_products' =>$request->id_order_purchase_products,
            'cantidad_seleccionada'=>$request->cantidad_seleccionada,
        ]);
        foreach ($request->statusOT as $statusOT) {
            $statusOT = (object)$statusOT;
            $statusOT =  $statusOT->statusOT()->create([
               'id_order_purchase_products' => $statusOT->id_order_purchase_products,
               'cantidad_seleccionada' => $statusOT->cantidad_seleccionada
            ]);

        }
        //
        return response()->json(["msg" => 'Este es el estatus'], 201);

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderPurchase  $orderPurchase
     * @return \Illuminate\Http\Response
     */
    public function show(OrderPurchase $orderPurchase)
    {
        //
        $status = Status::where('status')->first();
        if (!$status) {
            return response()->json(["errors" => "No se ha encontrado el Status"], 404);
        }
        return response()->json(["msj"=>$status]);
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
