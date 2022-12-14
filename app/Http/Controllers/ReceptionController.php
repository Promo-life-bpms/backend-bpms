<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\OrderPurchase;
use Illuminate\Http\Request;

class ReceptionController extends Controller
{
    public function setRemision(Request $request, $order)
    {
        $validator = Validator::make($request->all(), [
            'code_order' => 'required',
            // 'code_reception' => 'required',
            // 'company' => 'required',
            // 'type_operation' => 'required',
            // 'planned_date' => 'required|date:d-m-Y h:i:s',
            // 'effective_date' => 'required|date:d-m-Y h:i:s',
            'status' => 'required',
            'operations' => 'required|array|bail',
            // 'operations.*.code_reception' => 'required',
            // 'operations.*.odoo_product_id' => 'required',
            'operations.*.product' => 'required',
            // 'operations.*.initial_demand' => 'required|numeric',
            'operations.*.done' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(($validator->getMessageBag()));
        }

        $orderPurchase = OrderPurchase::where('code_order', $order)->first();

        if (!$orderPurchase) {
            return response()->json(['errors' => (['msg' => 'Ruta de entrega no encontrada.'])], 404);
        }

        $reception = (object)$request->all();
        $dataReception = [
            'code_reception' => $reception->code_reception ?: " ",
            'code_order' => $reception->code_order,
            'company' => $orderPurchase->sale->moreInformation->warehouse_company,
            'type_operation' => $reception->type_operation ?: " ",
            'planned_date' => now(),
            'effective_date' => now(),
            'status' => $reception->status ?: " ",
        ];
        $receptionDB = null;
        try {
            $receptionDB = Reception::updateOrCreate(['code_reception' => $reception->code_reception], $dataReception);
        } catch (Exception $th) {
            return response()->json(['message' => 'Error al crear la orden de compra', 'error' => $th->getMessage()], 400);
        }

        if ($receptionDB) {
            $errors = [];
            foreach ($reception->operations as $productRequest) {
                $productRequest = (object)$productRequest;
                $dataProduct =  [
                    "odoo_product_id" => $productRequest->odoo_product_id ?: " ",
                    "product" => $productRequest->product ?: " ",
                    "code_reception" => $productRequest->code_reception ?: " ",
                    "initial_demand" => $productRequest->initial_demand ?: 0,
                    "done" => $productRequest->done ?: 0,
                ];
                try {
                    $receptionDB->productsReception()->updateOrCreate(
                        [
                            "odoo_product_id" => $productRequest->odoo_product_id,
                            'reception_id' => $receptionDB->id
                        ],
                        $dataProduct
                    );
                } catch (Exception $th) {
                    array_push($errors, ['msg' => "Error al insertar el producto", 'error' => $th->getMessage()]);
                }
            }
            foreach ($receptionDB->productsReception as $productDB) {
                $existProduct = false;
                foreach ($reception->operations as $productRQ) {
                    if ($productDB->odoo_product_id == $productRQ['odoo_product_id']) {
                        $existProduct = true;
                    }
                }
                if (!$existProduct) {
                    $productDB->delete();
                }
            }
            if (count($errors) > 0) {
                return response()->json(['message' => 'Error al insertar los productos', 'error' => json_encode($errors)], 400);
            }
        }
        return response()->json(['message' => 'Actualizacion Completa'], 200);
    }
}
