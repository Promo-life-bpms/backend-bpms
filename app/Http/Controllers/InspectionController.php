<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Sale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InspectionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $sale_id)
    {
        // Crear una inspeccion de calidad
        $validation = Validator::make($request->all(), [
            'date_inspeccion' => 'required|date:Y-m-d h:i:s',
            'type_product' => 'required|in:limpio,maquilado',
            'observations' => 'required|string',
            'user_signature_created' => 'required',
            'user_reviewed' => 'required',
            'user_signature_reviewed' => 'required',
            'quantity_revised' => 'required|numeric',
            'quantity_denied' => 'required|numeric',
            'features_quantity' => 'required|array',
            'products_selected' => 'required|array',
            'products_selected.*.product_id' => 'required',
            'products_selected.*.order_purchase_id' => 'required',
            'products_selected.*.quantity_selected' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 400);
        }
        $sale = Sale::where('code_sale', $sale_id)->first();
        if (!$sale) {
            return response()->json(["errors" => "No se ha encontrado el pedido"], 404);
        }
        $dataInspection = [
            'sale_id' => $sale->id,
            'user_created_id' => auth()->user()->id,
            'date_inspection' => $request->date_inspeccion,
            'type_product' => $request->type_product,
            'observations' => $request->observations,
            'user_created' => auth()->user()->name,
            'user_signature_created' => $request->user_signature_created,
            'user_reviewed' => $request->user_reviewed,
            'user_signature_reviewed' => $request->user_signature_reviewed,
            'quantity_revised' => $request->quantity_revised,
            'quantity_denied' => $request->quantity_denied,
            'features_quantity' => json_encode($request->features_quantity)
        ];
        try {
            $inspection = Inspection::create($dataInspection);
            foreach ($request->products_selected as $productSelected) {
                $dataProductSelected = [
                    "product_id" => $productSelected['product_id'],
                    "order_purchase_id" => $productSelected['order_purchase_id'],
                    "quantity_selected" => $productSelected['quantity_selected'],
                ];
                $inspection->productsSelected()->create($dataProductSelected);
            }
            return response()->json(["msg" => "Inspeccion Creada Correctamente"], 400);
        } catch (Exception $e) {
            return response()->json(["errors" => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function show($inspection)
    {
        // Detalle de la inspeccion
    }
}
