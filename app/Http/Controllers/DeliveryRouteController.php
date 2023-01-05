<?php

namespace App\Http\Controllers;

use App\Models\CodeOrderDeliveryRoute;
use App\Models\DeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\ProductDeliveryRoute;
use App\Models\ProductRemission;
use App\Models\Remission;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Status;
use Exception;
use Facade\FlareClient\Api;
use Illuminate\Database\Console\DbCommand;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class DeliveryRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ruta = DeliveryRoute::where("is_active", true)->get();
        return response()->json([
            'msg' => "Acceso de rutas correcto",
            'data' => ["ruta" => $ruta],
        ], Response::HTTP_OK); //200


    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //pedidos por agendar
        //traer todos los usuarios que son choferes
        $rolChofer = Role::find(4);
        $choferes = [];
        foreach ($rolChofer->users as $chofer) {
            array_push($choferes, ['id' => $chofer->id, 'name' => $chofer->name]);
        }

        // traer los productos de las ordenes de compra o trabajo que no han sido entregados o cancelados
        $per_page = 10;

        if ($request->per_page) {
            $per_page = $request->per_page;
        }
        $pedidos = Sale::join('order_purchases', 'order_purchases.code_sale', 'sales.code_sale')->whereIn('order_purchases.status', ["Cancelado", "Confirmado"])->orderBy('sales.code_sale', 'ASC')->paginate($per_page);
        foreach ($pedidos as $pedido) {
            $pedido->orders = $pedido->orders()->whereIn('order_purchases.status', ["Cancelado", "Confirmado"])->get();
            foreach ($pedido->orders as $orden) {
                $orden->products;
            }
        }
        return response()->json([
            'msg' => 'Pedidos por agendar',
            'data' => [
                "pedidos" => $pedidos, "choferes" => $choferes
            ]
        ], response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validar que la informacion este correcta si no no se puede registrar
        // utilizar validator

        $validation = Validator::make($request->all(), [
            'date_of_delivery' => 'required',
            'user_chofer_id' => 'required',
            'type_of_product' => 'required',
            'code_orders' => 'required|array',
            'code_orders.*.code_sale' => 'required|exists:sales,code_sale',
            'code_orders.*.code_order' => 'required|exists:order_purchases,code_order',
            'code_orders.*.type_of_origin' => 'required',
            'code_orders.*.delivery_address' => 'required',
            'code_orders.*.type_of_destiny' => 'required',
            'code_orders.*.destiny_address' => 'required',
            'code_orders.*.hour' => 'required|date_format:H:i:s',
            'code_orders.*.attention_to' => 'required',
            'code_orders.*.action' => 'required',
            'code_orders.*.num_guide' => 'required',
            'code_orders.*.observations' => 'required',
            'code_orders.*.products' => 'required|array',
            'code_orders.*.products.*.product' => 'required',
            'code_orders.*.products.*.amount' => 'required'
        ]);
        if ($validation->fails()) {
            return response()->json(
                [
                    'msg' => "Error al validar informacion de la ruta de entrega",
                    'data' => ['errorValidacion' => $validation->getMessageBag()]
                ],
                response::HTTP_UNPROCESSABLE_ENTITY
            ); // 422
        }
        // crear una ruta de entrega con los campos de Deliveryroute y guardar esa ruta de entrega en una variable
        // ::create
        //codigo de ruta
        $maxINSP = DeliveryRoute::max('code_route');
        $idInsp = null;
        if (!$maxINSP) {
            $idInsp = 1;
        } else {
            $idInsp = (int) explode('-', $maxINSP)[1];
            $idInsp++;
        }
        //codigo de ruta
        $ruta = DeliveryRoute::create([
            'code_route' => "RUT-" . str_pad($idInsp, 5, "0", STR_PAD_LEFT),
            'date_of_delivery' => $request->date_of_delivery,
            'user_chofer_id' => $request->user_chofer_id,
            'type_of_product' => $request->type_of_product,
            'status' => 'Pendiente',
            'is_active' => 1,
        ]);
        //crear los productos de esa ruta de entrega
        //  $ruta->productsDeliveryRoute()->create
        //retornar un mensaje
        foreach ($request->code_orders as $codeOrder) {
            $codeOrder = (object)$codeOrder;

            $codeOrderRoute =  $ruta->codeOrderDeliveryRoute()->create([
                'code_sale' => $codeOrder->code_sale,
                'code_order' => $codeOrder->code_order,
                'type_of_origin' => $codeOrder->type_of_origin,
                'delivery_address' => $codeOrder->delivery_address,
                'type_of_destiny' => $codeOrder->type_of_destiny,
                'destiny_address' => $codeOrder->destiny_address,
                'hour' => $codeOrder->hour,
                'attention_to' => $codeOrder->attention_to,
                'action' => $codeOrder->action,
                'num_guide' => $codeOrder->num_guide,
                'observations' => $codeOrder->observations,
            ]);


            foreach ($codeOrder->products as $newProduct) {
                $newProduct = (object)$newProduct;
                $codeOrderRoute->productDeliveryRoute()->create([
                    'product' => $newProduct->product,
                    'amount' => $newProduct->amount,
                ]);
            }
        }

        return response()->json([
            'msg' => 'Ruta Creada Existosamente',
            'data' => [
                "ruta" =>  $ruta
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Corresponde con la ruta  rutas-de-entrega
        // Buscamos un study por el ID.
        $ruta = DeliveryRoute::where('code_route', $id)->first();
        // Chequeaos si encontró o no la ruta
        if (!$ruta) {
            // Se devuelve un array errors con los errores detectados y código 404
            return response()->json(['msg'  => 'No se encuentra esa ruta de entrega.'], response::HTTP_NOT_FOUND); //404
        }
        $ordenes = $ruta->codeOrderDeliveryRoute;
        foreach ($ordenes as $ordenDeCompra) {
            $ordenDeCompra->productDeliveryRoute;
        }

        $ruta->remissions;
        // Devolvemos la información encontrada.
        return response()->json(['msg' => 'Detalle de ruta de entrega',  'data' => ['ruta' => $ruta]], response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryRoute $deliveryRoute)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {

        $ruta = DeliveryRoute::where('code_route', $id)->first();

        if (!$ruta) {
            // Retornar mensaje
            return response()->json([
                'msg' => "ruta no encontrada"
            ], response::HTTP_NOT_FOUND);
        }
        $ruta->date_of_delivery = $request->date_of_delivery;
        $ruta->user_chofer_id = $request->user_chofer_id;
        $ruta->type_of_product = $request->type_of_product;
        $ruta->save();


        foreach ($request->code_orders as $codeOrder) {
            $codeOrderRequest = (object) $codeOrder;

            $codeOrderDB = CodeOrderDeliveryRoute::find($codeOrderRequest->code_sale);

            if ($codeOrderDB) {
                $codeOrderDB->code_sale = $codeOrderRequest->code_sale;
                $codeOrderDB->code_order = $codeOrderRequest->code_order;
                $codeOrderDB->type_of_origin = $codeOrderRequest->type_of_origin;
                $codeOrderDB->delivery_address = $codeOrderRequest->delivery_address;
                $codeOrderDB->type_of_destiny = $codeOrderRequest->type_of_destiny;
                $codeOrderDB->destiny_address = $codeOrderRequest->destiny_address;
                $codeOrderDB->hour = $codeOrderRequest->hour;
                $codeOrderDB->attention_to = $codeOrderRequest->attention_to;
                $codeOrderDB->action = $codeOrderRequest->action;
                $codeOrderDB->num_guide = $codeOrderRequest->num_guide;
                $codeOrderDB->observations = $codeOrderRequest->observations;
                $codeOrderDB->save();

                foreach ($codeOrderRequest->products as $product) {
                    $productRequest = (object)$product;
                    // $productsDB = $codeOrderDB->productDeliveryRoute;
                    ProductDeliveryRoute::updateOrCreate(
                        ['code_order_route_id' => $codeOrderDB->id, "product" => $productRequest->product],
                        ['amount' => $productRequest->amount]
                    );
                }
            }

            if (!$codeOrderDB) {
                $ruta->codeOrderDeliveryRoute()->create([
                    'code_sale' => $codeOrderRequest->code_sale,
                    'code_order' => $codeOrderRequest->code_order,
                    'type_of_origin' => $codeOrderRequest->type_of_origin,
                    'delivery_address' => $codeOrderRequest->delivery_address,
                    'type_of_destiny' => $codeOrderRequest->type_of_destiny,
                    'destiny_address' => $codeOrderRequest->destiny_address,
                    'hour' => $codeOrderRequest->hour,
                    'attention_to' => $codeOrderRequest->attention_to,
                    'action' => $codeOrderRequest->action,
                    'num_guide' => $codeOrderRequest->num_guide,
                    'observations' => $codeOrderRequest->observations,
                ]);
            }
        }
        foreach ($ruta->codeOrderDeliveryRoute as $codeOrderDB) {

            $existeEnElRequest = false;

            foreach ($request->code_orders as $codeOrderRequest) {
                $codeOrderRequest = (object)$codeOrderRequest;
                if ($codeOrderDB->id == $codeOrderRequest->id) {
                    $existeEnElRequest = true;
                }
            }

            if ($existeEnElRequest == false) {
                $codeOrderDB->delete();
            }
        }
        /* if (!$codeOrder) {
            $codeOrder = CodeOrderDeliveryRoute::find($id);
            $codeOrder->delete();
        } {
            foreach ($codeOrder->products as $product) {
                if (!$product) {
                    $product = ProductDeliveryRoute::find($id);
                    $product->delete();
                }
            }
        } */
        return response()->json(['msg' => 'Ruta actualizada correctamente!'], response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return \Illuminate\Http\Response
     */
    public function destroy($deliveryRoute)
    {
        $ruta = DeliveryRoute::where('code_route', $deliveryRoute)->first();
        // Chequeaos si encontró o no la ruta
        if (!$ruta) {
            return response()->json([(['msg' => 'No se encuentra esa ruta de entrega.'])], response::HTTP_NOT_FOUND);
        }
        try {
            foreach ($ruta->codeOrderDeliveryRoute as $codr) {
                $codr->productDeliveryRoute()->delete();
                $codr->delete();
            }
            $ruta->delete();
        } catch (Exception $e) {
            return response()->json([
                ([
                    'msg' => 'Error al eliminar esta ruta.', 'data' =>
                    ['error' => $e->getMessage()]
                ])
            ], response::HTTP_NOT_FOUND);
            //throw $th;
        }
        // Se devuelve un array errors con los errores detectados y código 404
        return response()->json(['msg' => 'Ruta eliminada correctamente!'], response::HTTP_OK); //200
    }

    public function setRemisiones(Request $request, $ruta)
    {

        $validation = Validator::make($request->all(), [
            'comments' => 'required',
            'satisfaction' => 'required',
            'delivered' => 'required',
            'delivery_signature' => 'required',
            'received' => 'required',
            'signature_received' => 'required',
            'user_chofer_id' => 'required',
            'status' => 'required',
            'product_remission' => 'required|array',
            // 'product_remission.*.remission_id' => 'required',
            'product_remission.*.delivered_quantity' => 'required',
            'product_remission.*.product' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                'msg' => "Error de validacion de la remision",
                'data' => ["errorValidacion" => $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY); //422
        }
        $deliveryRoute = DeliveryRoute::where('code_route', $ruta)->first();

        if (!$deliveryRoute) {
            return response()->json(['msg' => 'Ruta de entrega no encontrada.'], response::HTTP_NOT_FOUND); //404
        }

        // crear una ruta de entrega con los campos de Deliveryroute y guardar esa ruta de entrega en una variable
        // ::create
        //crear codigo de remision
        $maxINC = Remission::max('code_remission');
        $idinc = null;
        if (!$maxINC) {
            $idinc = 1;
        } else {
            $idinc = (int) explode('-', $maxINC)[1];
            $idinc++;
        }

        $remision = Remission::create([
            'code_remission' => "REM-" . str_pad($idinc, 5, "0", STR_PAD_LEFT),
            'comments' => $request->comments,
            'satisfaction' => $request->satisfaction,
            'delivered' => $request->delivered,
            'delivery_signature' => $request->delivery_signature,
            'received' => $request->received,
            'signature_received' => $request->signature_received,
            'delivery_route_id' => $deliveryRoute->id,
            'user_chofer_id' => $request->user_chofer_id,
            'status' => 1
        ]);
        //crear los productos de esa remision de entrega
        //  $remision->productsDeliveryRoute()->create
        //retornar un mensaje
        foreach ($request->product_remission as $product) {
            $product = (object)$product;

            $remision->productRemission()->create([
                'delivered_quantity' => $product->delivered_quantity,
                'product' => $product->product,
            ]);
        }

<<<<<<< HEAD
        return response()->json(['msg' => 'Remision creada exitosamente', 'data' => ["remision" => $remision]], Response::HTTP_CREATED);
=======
        // TODO: Crear una recepcion de inventario en caso de que el typo de origen sea Almacen


        return response()->json(['msg' => 'Remision creada exitosamente', 'data' => $remision], Response::HTTP_CREATED);
>>>>>>> dcf2e8ea7146f8fa4a27a87fb9e82764f1b144fd
    }

    public function viewRemision()
    {
        $remision = Remission::where("status", 1)->get();
        return response()->json([
            "msg" =>  "Acceso de remisiones correcto", 'data' => ["remision" => $remision]
        ], response::HTTP_OK); //200
    }

    public function showRemision($ruta, $id)
    {
        $deliveryRoute = DeliveryRoute::where('code_route', $ruta)->first();

        if (!$deliveryRoute) {
            return response()->json(['msg' =>  'Ruta de entrega no encontrada.'], response::HTTP_NOT_FOUND); //404
        }

        $remision = Remission::where('code_remission', $id)->first();

        if (!$remision) {
            return response()->json(['msg' =>  'Remision no encontrada.'], response::HTTP_NOT_FOUND); //404
        }
        $remision->productRemission;
<<<<<<< HEAD
        return response()->json(['msg' =>  'Remision encontrada.', 'data' => ["remision", $remision]], response::HTTP_OK); //200
=======
        return response()->json(['errors' => (['msg' => 'Remision encontrada.', 'data' => $remision])], 200);
>>>>>>> dcf2e8ea7146f8fa4a27a87fb9e82764f1b144fd
    }

    public function cancelRemision($ruta, $id)
    {
        $deliveryRoute = DeliveryRoute::where('code_route', $ruta)->first();

        if (!$deliveryRoute) {
            return response()->json(['msg' => 'Ruta de entrega no encontrada.'], response::HTTP_NOT_FOUND); //404
        }
        $remision = Remission::where('code_remission', $id)->first();

        if (!$remision) {
            return response()->json(['msg' =>  'Remision no encontrada.'], response::HTTP_NOT_FOUND); //404
        }

        if ($remision->status == 2) {
            return response()->json(["msg" => "Esta remision se encuentra actualmente cancelada"], response::HTTP_OK); //200
        }

        // Revisar si no esta cancelado

        // Marcar como cancelada la remision
        $remision->status = 2;
        $remision->save();
        return response()->json(["msg" => "Remision cancelada"], response::HTTP_OK); //200
    }
}
