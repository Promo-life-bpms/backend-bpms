<?php

namespace App\Http\Controllers;

use App\Models\CodeOrderDeliveryRoute;
use App\Models\DeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\ProductDeliveryRoute;
use App\Models\ProductRemission;
use App\Models\Remission;
use App\Models\Role;
use App\Models\Status;
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
            "rutas_de_entrega" => $ruta,
            "mensaje" => "OK",
            "display_message" => "Acceso de rutas correcto",

        ], 200);
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
        $choferes = $rolChofer->users;

        // traer los productos de las ordenes de compra o trabajo que no han sido entregados o cancelados
        $per_page = 10;

        if ($request->per_page) {
            $per_page = $request->per_page;
        }
        $orderPurchase = OrderPurchase::whereIn('status', ["Cancelado", "Confirmado"])->paginate($per_page);
        return response()->json(['pedidos' => $orderPurchase, "choferes" => $choferes], 200);
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
            'code_orders.*.code_sale' => 'required',
            'code_orders.*.code_order' => 'required',
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
            return response()->json(["errors" => $validation->getMessageBag()], 422);
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
            'status' => 'creado',
            'is_active' => 1,
        ]);
        //crear los productos de esa ruta de entrega
        //  $ruta->productsDeliveryRoute()->create
        //retornar un mensaje
        foreach ($request->code_orders as $codeOrder) {
            $codeOrder = (object)$codeOrder;

            $codeOrderRoute =  $ruta->codeDeliveryRoute()->create([
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

        return response()->json('Ruta creada exitosamente', Response::HTTP_CREATED);
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
        $ruta = DeliveryRoute::find($id);
        // Chequeaos si encontró o no la ruta
        if (!$ruta) {
            // Se devuelve un array errors con los errores detectados y código 404
            return response()->json(['errors' => (['code' => 404, 'message' => 'No se encuentra esa ruta de entrega.'])], 404);
        }
        $ordenes = $ruta->codeDeliveryRoute;
        foreach ($ordenes as $ordenDeCompra) {
            $ordenDeCompra->productDeliveryRoute;
        }

        // Devolvemos la información encontrada.
        return response()->json(['deliveryroute' => $ruta]);
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
        /* 
            Quiero que se actualize la ruta de entrega, se actualicen las ordendes existentes 
            y la nuevas se guarden, lo mismo con los productos, y eliminar las ordendes y productos
            que no esten en la solicitud pero si en los registros

            Revisar que exista la ruta de entrega
            
            Si no existe
                Retornar Mensaje de No encontrado 404
            Si existe
                Actualiza la informacion de la ruta de entrega

                Por cada orden de compra que llega en la solicitud, revisar si existe o no
                Si existe
                    Entonces Actualizar la informacion de la orden de compra en ruta de entrega
                            Por cada Producto
                                Si existe
                                    Actualizar la informacion de los productos
                                Si no
                                    Crear un producto nuevo
                Si no
                    Crear las ordenes de compra que no estan en la ruta de entrega
                        guardan sus productos

                Por cada orden de compra en la base de datos
                    Si no existe en la solicitud
                        Entonces
                            Elimiar el registro (Elimiar sus productos relacionados)
                    Si existe
                        Por cada producto en esa ruta de entrega 
                            Si no existe el produto en la solicitud
                            Entonces
                                Elimiar ese producto

            Retornar mensaje de actualizacion completa

        
        */
        $ruta = DeliveryRoute::find($id);

        if (!$ruta) {
            // Retornar mensaje
            return response()->json([
                'msg' => "ruta no encontrada"
            ], 404);
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
                $ruta->codeDeliveryRoute()->create([
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

                //$product->save();
            }
        }
        foreach ($ruta->codeDeliveryRoute as $codeOrderDB) {

            $existeEnElRequest = false;
            
            foreach ($request->code_orders as $codeOrderRequest) {
                $codeOrderRequest = (object)$codeOrderRequest;
                if ($codeOrderDB->id == $codeOrderRequest->id) {
                    $existeEnElRequest = true;
                }
            }
            
            if($existeEnElRequest == false){
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
        return response()->json('Ruta actualizada correctamente!');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryRoute $deliveryRoute)
    {
        $deliveryRoute->is_active = false;
        $deliveryRoute->save();
        return response()->json('Ruta eliminada correctamente!');
    }

    public function setRemisiones(Request $request)
    {



        $validation = Validator::make($request->all(), [
            'comments' => 'required',
            'satisfaction' => 'required',
            'delivered' => 'required',
            'delivery_signature' => 'required',
            'received' => 'required',
            'signature_received' => 'required',
            'delivery_route_id' => 'required',
            'user_chofer_id' => 'required',
            'status' => 'required',

            'product_remission' => 'required|array',
            'product_remission.*.remission_id' => 'required',
            'product_remission.*.delivered_quantity' => 'required',



        ]);
        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 422);
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
            'code_remission' => "RUT-" . str_pad($idinc, 5, "0", STR_PAD_LEFT),
            'comments' => $request->comments,
            'satisfaction' => $request->satisfaction,
            'delivered' => $request->delivered,
            'delivery_signature' => $request->delivery_signature,
            'received' => $request->received,
            'signature_received' => $request->signature_received,
            'delivery_route_id' => $request->delivery_route_id,
            'user_chofer_id' => $request->user_chofer_id,
            'status' => 1
        ]);
        //crear los productos de esa remision de entrega
        //  $remision->productsDeliveryRoute()->create
        //retornar un mensaje
        foreach ($request->product_remission as $product) {
            $product = (object)$product;

            $remision->productRemission()->create([
                'remission_id' => $product->remission_id,
                'delivered_quantity' => $product->delivered_quantity,

            ]);
        }

        return response()->json('Remision creada exitosamente', Response::HTTP_CREATED);
    }

    public function viewRemision()
    {
        $remision = Remission::where("status", 1)->get();
        return response()->json([
            "remisiones" => $remision,
            "mensaje" => "OK",
            "display_message" => "Acceso de remisiones correcto",

        ], 200);
    }

    public function showRemision($id)
    {
        $remision = Remission::find($id);
        $remision->productRemission;
        return json_encode($remision);
    }

    public function cancelRemision($id)
    {
        // Encontrar la remision por ID
        $remision = Remission::where("id", $id)->first();
        if (!$remision) {
            return response()->json(["msg" => "No encontrado"], 404);
        }
        // Revisar si no esta cancelado

        // Marcar como cancelada la remision
        $remision->status = 2;
        $remision->save();
        return response()->json(["msg" => "Remision cancelada"], 200);
    }
}
