<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\Reception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReceptionController extends Controller
{
    public function saveReception(Request $request, $order)
    {
        // Obtener la recepcion del los productos
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|bail',
            'products.*.product' => 'required',
            'products.*.done' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(($validator->getMessageBag()));
        }

        $orderPurchase = OrderPurchase::where('code_order', $order)->first();

        if (!$orderPurchase) {
            return response()->json(['errors' => (['msg' => 'Ruta de entrega no encontrada.'])], 404);
        }

        $reception = (object)$request->all();

        $maxINC = Reception::where('code_reception', 'LIKE', "%REC-IN%")->max('code_reception');
        $idinc = null;
        if (!$maxINC) {
            $idinc = 1;
        } else {
            $idinc = (int) explode('-', $maxINC)[2];
            $idinc++;
        }

        $code_reception =  "REC-IN-" . str_pad($idinc, 5, "0", STR_PAD_LEFT);

        $dataReception = [
            'code_reception' => $code_reception,
            'code_order' => $order,
            'company' => $orderPurchase->sale->moreInformation->warehouse_company,
            'type_operation' => $orderPurchase->sale->moreInformation->warehouse_company . ': Recepciones',
            'planned_date' => now(),
            'effective_date' => now(),
            'status' => " ",
        ];
        $receptionDB = null;
        try {
            $receptionDB = Reception::create($dataReception);
        } catch (Exception $th) {
            return response()->json(['message' => 'Error al crear la orden de compra', 'error' => $th->getMessage()], 400);
        }

        if ($receptionDB) {
            $errors = [];
            foreach ($reception->products as $productRequest) {
                $productRequest = (object)$productRequest;
                $dataProduct =  [
                    "odoo_product_id" => " ",
                    "product" => $productRequest->product ?: " ",
                    "code_reception" => $code_reception,
                    "initial_demand" => 0,
                    "done" => $productRequest->done ?: 0,
                ];

                try {
                    $receptionDB->productsReception()->updateOrCreate(
                        [
                            "product" =>  $productRequest->product,
                            'reception_id' => $receptionDB->id
                        ],
                        $dataProduct
                    );
                } catch (Exception $th) {
                    array_push($errors, ['msg' => "Error al insertar el producto", 'error' => $th->getMessage()]);
                }
            }
            if (count($errors) > 0) {
                return response()->json(['message' => 'Error al insertar los productos', 'error' => json_encode($errors)], 400);
            }
        }
        return response()->json(['message' => 'Creacion de la recepcion de inventario', 'data' => $receptionDB], 200);
    }

    public function getReception($order, $reception)
    {
        $orderPurchase = OrderPurchase::where('code_order', $order)->first();

        if (!$orderPurchase) {
            return response()->json(['errors' => (['msg' => 'Ruta de entrega no encontrada.'])], 404);
        }

        $reception = $orderPurchase->receptions->where('code_reception', $reception)->first();

        if (!$reception) {
            return response()->json(['errors' => (['msg' => 'Recepcion no encontrada.'])], 404);
        }

        $reception->productsReception;

        return response()->json(['data' => $reception], 200);
    }
}
