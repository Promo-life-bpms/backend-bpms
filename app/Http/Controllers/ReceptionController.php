<?php

namespace App\Http\Controllers;

use App\Models\CodeOrderDeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\Reception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderPurchaseProduct;
use App\Models\ProductDeliveryRoute;
use App\Models\ReceptionConfirmationMaquilado;
use App\Models\Sale;
use App\Models\SaleStatusChange;
use Dflydev\DotAccessData\Data;
use Exception;
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

        $maquilador = auth()->user()->whatRoles()->where('id', 2)->get();

        if ($maquilador->isEmpty()) {
            $maquilador = false;
        } else {
            $maquilador = true;
        }

        if (!$maquilador) {
            // Revisar si hay registros de recepciones de la orden de compra
            if (count($receptionsOfOrderPurchase) > 0) {
                // Variable de acceso global, para guardar los prodcuts (Tipo Array) [odoo_product_id, cantdad recibida]
                $cantidadesRecibida = [];
                $orderPurchaseproducts = $orderPurchase->products;
                foreach ($orderPurchaseproducts as $p) {
                    array_push($cantidadesRecibida, [
                        "odoo_product_id" => $p->odoo_product_id,
                        "quantity" => 0,
                    ]);
                }

                $errors = [];
                foreach ($cantidadesRecibida as $key => $CantidadRecibida) {
                    $productSearch =  $orderPurchase->products()->where("odoo_product_id", $CantidadRecibida["odoo_product_id"])->first();
                    $cantidadOrdenada =  $productSearch->quantity;
                    foreach ($request->products as $productRequest) {
                        if ($CantidadRecibida["odoo_product_id"] == $productRequest["odoo_product_id"]) {
                            if ($productRequest["done"] <= ($cantidadOrdenada - (int)$CantidadRecibida["quantity"])) {
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

            //validar si no hya una recpecion con productos ya creada (done,quantity) que el valor sea <=0
            if (!$receptionsOfOrderPurchase) {
                $cantidadesRecibida = [];
                $orderPurchaseproducts = $orderPurchase->products;
                if (!$orderPurchaseproducts) {
                    foreach ($orderPurchaseproducts as $p) {
                        array_push($cantidadesRecibida, [
                            "odoo_product_id" => $p->odoo_product_id,
                            "quantity" =>  0,
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
            'user_id' => auth()->user()->id,
            'maquilador' => $maquilador,
        ];

        $receptionDB = null;
        try {
            $receptionDB = Reception::create($dataReception);
        } catch (Exception $th) {
            return response()->json(['message' => 'Error al crear la recepcion de inventario', 'error' => $th->getMessage()], 400);
        }

        //vaidacion no debe superar la cantidad pedida o demanda incial //return
        if ($receptionDB) {

            $errors = [];
            foreach ($reception->products as $productRequest) {
                $productRequest = (object)$productRequest;
                $product = OrderPurchaseProduct::where('order_purchase_id', $orderPurchase->id)
                    ->where("odoo_product_id", "=", $productRequest->odoo_product_id)->first();
                $dataProduct =  [
                    "odoo_product_id" => $productRequest->odoo_product_id,
                    "product" => $product->product,
                    "code_reception" => $receptionDB->code_reception,
                    "initial_demand" => $product->quantity,
                    "done" => $productRequest->done,
                ];
                //return $dataProduct;
                if (!$maquilador) {
                    $product->quantity_delivered = $product->quantity_delivered + $productRequest->done;
                    $product->save();
                }
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
            // return $s;
            if (count($errors) > 0) {
                return response()->json(['message' => 'Error al insertar los productos', 'error' => json_encode($errors)], 400);
            }

            foreach ($receptionDB->productsReception as $productRecep) {
                $productRecep->odoo_product_id;
                $product = OrderPurchaseProduct::where("odoo_product_id", "=", $productRequest->odoo_product_id)->first();

                $product->quantity_delivered;
                $productRecep->quantity_delivered = $product->quantity_delivered;
            }
        }
        $sale = $orderPurchase->sale;
        //return $sale;
        //contabilizar el inventario
        $pedidos = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->join("order_purchases", "order_purchases.code_sale", "sales.code_sale")
            ->join('order_purchase_products', 'order_purchase_products.order_purchase_id', 'order_purchases.id')
            ->where("sales.id", $sale->id)
            ->select("order_purchase_products.quantity_delivered")
            ->get();
        //return $pedidos;
        //return $productRecep->quantity_delivered;
        if ($pedidos > null) {

            SaleStatusChange::create([
                'sale_id' => $sale->id,
                "status_id" => 9
            ]);
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
        //Inventario contabilizado:

        $reception->productsReception;

        return response()->json(['data' => $reception], 200);
    }

    public function receptionAccept(Request $request, $code_order_route_id)
    {
        $user =  auth()->user();

        foreach ($user->whatRoles as $rol) {
            switch ($rol->name) {

                case ("compras" == $rol->name):

                    break;
                case ("administrator" == $rol->name):
                    break;
                default:
                    return response()->json(
                        [
                            'msg' => "No tienes autorizacion para subir la evidencia",
                        ],

                    );
                    break;
            }
        }
        $validation = Validator::make($request->all(), [
            'files_reception_accepted' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json(
                [
                    'msg' => "Error al validar informacion de la recepecion entregada",
                    'data' => ['errorValidacion' => $validation->getMessageBag()]
                ],
                response::HTTP_UNPROCESSABLE_ENTITY
            ); // 422
        }
        //$request->files_reception_accepted;
        $productDeliveryRoute = ProductDeliveryRoute::where('code_order_route_id', $code_order_route_id)->first();
        return $productDeliveryRoute;
        if ($productDeliveryRoute->files_reception_accepted == null) {
            $productDeliveryRoute->files_reception_accepted = $request->files_reception_accepted;
        }

        $productDeliveryRoute->save();
        /*   $productDeliveryRoute->files_reception_accepted = $dataFiles; */
        /*    $productDeliveryRoute->save(); */
        return response()->json(['message' => 'Se confirmo que el pedido llego a almacen', 'data' => $productDeliveryRoute], 200);
    }
    public function confirmation_manufactured_product(Request $request, $order, $odoo_product)
    {
        $orderPurchase = OrderPurchase::where('code_order', $order)->first();
        /*   foreach ($orderPurchase->products as $product) {
            $odoo_id = $product->odoo_product_id;
        } */
        $validation = Validator::make($request->all(), [
            'quantity_maquilada' => 'required',

        ]);
        if ($validation->fails()) {
            return response()->json(['msg' => "Error al crear confirmacion de producto maqulado", 'data' => ["errorValidacion" => $validation->getMessageBag()]], response::HTTP_BAD_REQUEST); //400
        }
        $dataConfirmation = [
            'code_order' => $order,
            'odoo_product_id' => $odoo_product,
            'quantity_maquilada' => $request->quantity_maquilada,
            'decrease' => $request->decrease,
            'product_clean' => $request->product_clean,
            'observations' => $request->observations
        ];

        $recepcion_Confirmation = ReceptionConfirmationMaquilado::create($dataConfirmation);

        return response()->json(['message' => 'Creacion de la confirmacion de los productos maquilados', 'data' => $recepcion_Confirmation], 200);
    }
    public function getReceptionConfirmed($order, $odoo_product)
    {
        $codeOrder = CodeOrderDeliveryRoute::where('code_order', $order)->first();

        if (!$codeOrder) {
            return response()->json(['errors' => (['msg' => 'Orden no encontrada.'])], 404);
        }
        $products =  $codeOrder->productDeliveryRoute[0]->odoo_product_id;

        $recepciones = ReceptionConfirmationMaquilado::where('odoo_product_id', $products)->get();


        if (!$recepciones) {
            return response()->json(['errors' => (['msg' => 'Recepcion no encontrada.'])], 404);
        }
        return response()->json(['Recepcion_maquilada_confirmada' => $recepciones], 200);
    }
}
