<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Sale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

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
            'products_selected.*.odoo_product_id' => 'required|exists:order_purchase_products,odoo_product_id',
            'products_selected.*.code_order' => 'required|exists:order_purchases,code_order',
            'products_selected.*.quantity_selected' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['msg' => "Error al crear la inspeccion de calidad", 'data' => ["errorValidacion" => $validation->getMessageBag()]], response::HTTP_BAD_REQUEST); //400
        }
        $sale = Sale::where('code_sale', $sale_id)->first();
        if (!$sale) {
            return response()->json(["msg" => "No se ha encontrado el pedido"], response::HTTP_NOT_FOUND);
        }

        $maxINSP = Inspection::max('code_inspection');
        $idInsp = null;
        if (!$maxINSP) {
            $idInsp = 1;
        } else {
            $idInsp = (int) explode('-', $maxINSP)[1];
            $idInsp++;
        }

        $dataInspection = [
            'sale_id' => $sale->id,
            'code_inspection' => "INSP-" . str_pad($idInsp, 5, "0", STR_PAD_LEFT),
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
                    "odoo_product_id" => $productSelected['odoo_product_id'],
                    "code_order" => $productSelected['code_order'],
                    "quantity_selected" => $productSelected['quantity_selected'],
                ];
                $inspection->productsSelected()->create($dataProductSelected);
            }
            return response()->json([
                "msg" => "Inspeccion Creada Correctamente",
                'data' =>
                ["inspection" => $inspection]
            ], response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'msg' => "Inspeccion No Creada",
                'data' => ["error", $e->getMessage()]
            ], response::HTTP_BAD_REQUEST); //400
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function show($inspection_id)
    {
        $inspection = Inspection::with('productsSelected')->where('code_inspection', $inspection_id)->first();
        if ($inspection) {
            return response()->json([
                'msg' => "Inspeccion de calidad solicitada correctamente",
                'data' =>
                ["inspection" => $inspection]
            ], response::HTTP_OK); //200
        }
        return response()->json(["msg" => "Inspeccion No Encontrada"], response::HTTP_NOT_FOUND);
        // Detalle de la inspeccion
    }
}
