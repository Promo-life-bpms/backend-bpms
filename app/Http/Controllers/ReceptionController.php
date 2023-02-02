<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\Reception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderPurchaseProduct;
use App\Models\ReceptionProduct;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Component\HttpFoundation\Response;

class ReceptionController extends Controller
{
    public $CantidadRecibida = array();
    public function saveReception(Request $request, $order)
    {

        // Obtener la recepcion del los productos
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|bail',
            //modificacion
            'products.*.odoo_product_id' => 'required|exists:order_purchase_products,odoo_product_id',
            'products.*.done' => 'required|numeric',

        ]);


        if ($validator->fails()) {
            return response()->json(($validator->getMessageBag()));
        }

        $orderPurchase = OrderPurchase::where('code_order', $order)->first();
        if (!$orderPurchase) {
            // Retornar mensaje
            return response()->json([
                'msg' => "Orden de compra no encontrada"
            ], Response::HTTP_NOT_FOUND);
        }
        $receptionsOfOrderPurchase = $orderPurchase->receptions;

        // Revisar si hay registros de recepciones de la orden de compra
        if (count($receptionsOfOrderPurchase) > 0) {
            // Variable de acceso global, para guardar los prodcuts (Tipo Array) [odoo_product_id, cantdad recibida]
            $cantidadesRecibida = [];
            $orderPurchaseproducts = $orderPurchase->products;
            foreach ($orderPurchaseproducts as $p) {
                array_push($cantidadesRecibida, [
                    "odoo_product_id" => $p->odoo_product_id,
                    "quantity"=> 0,
                ]);  
            }

            
            $errors = [];
            foreach ($cantidadesRecibida as $key => $CantidadRecibida) {
                $productSearch =  $orderPurchase->products()->where("odoo_product_id", $CantidadRecibida["odoo_product_id"])->first();
                $cantidadOrdenada =  $productSearch->quantity;
                foreach ($request->products as $productRequest) {
                    if ($CantidadRecibida["odoo_product_id"] == $productRequest["odoo_product_id"]) {
                        if ($productRequest["done"] <= ($cantidadOrdenada -  (int)$CantidadRecibida["quantity"])) {
                        } else {
                            array_push($errors, ["msg" => "Cantidad superada", "product" => $productSearch]);
                        }
                    }
                }
            }

            if (count($errors) > 0) {
                return response()->json($errors, 400);
            }

            /*  $receptionsOfOrderPurchase->productsReception;
            // Revisar la suma de la cantidad recibida del producto
            foreach ($orderPurchase->products as $requestCantidad) {
                $requestCantidad = (object)$requestCantidad;


                $quantity_delivered  = OrderPurchaseProduct::select('quantity')->first();
                $quantity_ordered  = OrderPurchaseProduct::select('quantity_ordered')->first();

                //  Revisar que la cantidad en el parametro done, sea menor o igual a la resta de cantidad pedida menos cantidad recibida
                if ($quantity_ordered <= $quantity_delivered) {

                    $resta = $quantity_ordered - $quantity_delivered;
                    return $resta;

                    /*  $resta1 = $
                         return $resta;
                    $total = ReceptionProduct::where('done', '<=', $resta);
                    return $total;
                    if ($total - $quantity_ordered) {
                    }
                }
            } */
        }

        //validar si no hya una recpecion con productos ya creada (done,quantity) que el valor sea <=0
        if( !$receptionsOfOrderPurchase )  {
            $cantidadesRecibida = [];  
            $orderPurchaseproducts = $orderPurchase->products;
            if (!$orderPurchaseproducts){
          
            foreach ($orderPurchaseproducts as $p) {
                
                array_push($cantidadesRecibida, [
                    "odoo_product_id" => $p->odoo_product_id,
                    "quantity"=>  0,
                ]);  
            }
        }

            foreach ($receptionsOfOrderPurchase as $receptionOfOrderPurchase) {
                foreach ($receptionOfOrderPurchase->productsReception as $productsReception) {
                    // sumar la cantidad recibida a mi arreglo, relacionado con el producto
                    foreach ($cantidadesRecibida as $key => $pCantidadRecibida) {
                        if ($pCantidadRecibida["odoo_product_id"] == $productsReception->odoo_product_id) {
                            $cantidadesRecibida[$key]["quantity"] = $cantidadesRecibida[$key]["quantity"] + $productsReception->done;
                        }
                    }
                }
            }
            $errors = [];
            foreach ($cantidadesRecibida as $key => $CantidadRecibida) {
                $productSearch =  $orderPurchase->products()->where("odoo_product_id", $CantidadRecibida["odoo_product_id"])->first();
                $cantidadOrdenada =  $productSearch->quantity;
                foreach ($request->products as $productRequest) {
                    if ($CantidadRecibida["odoo_product_id"] == $productRequest["odoo_product_id"]) {
                        if ($productRequest["done"] <= ($cantidadOrdenada -  (int)$CantidadRecibida["quantity"])) {
                        } else {
                            array_push($errors, ["msg" => "Cantidad superada", "product" => $productSearch]);
                        }
                    }
                }
            }

            if (count($errors) > 0) {
                return response()->json($errors, 400);
            }

        }

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

        //vaidacion no debe superar la cantidad pedida o demanda incial //return
        if ($receptionDB) {

            $errors = [];
            foreach ($reception->products as $productRequest) {
                $productRequest = (object)$productRequest;
                $product = OrderPurchaseProduct::where("odoo_product_id", "=", $productRequest->odoo_product_id)->first();

                $dataProduct =  [
                    "odoo_product_id" => $productRequest->odoo_product_id,
                    "product" => $product->product,
                    "code_reception" => $code_reception,
                    "initial_demand" => $product->quantity,
                    "done" => $productRequest->done,
                ];

                // TODO:  Actualizar la cantidad recibida

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
