<?php

namespace App\Http\Controllers;

use App\Models\CodeOrderDeliveryRoute;
use App\Models\DeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\ProductDeliveryRoute;
use App\Models\Remission;
use App\Models\Role;
use App\Models\Status;
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

        $ruta = DeliveryRoute::create([
           
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
    public function update(Request $request, DeliveryRoute $deliveryRoute, $id)
    {

        // 
        $ruta = DeliveryRoute::findOrFail($id); {

            $ruta->date_of_delivery = $request->date_of_delivery;
            $ruta->user_chofer_id = $request->user_chofer_id;
            $ruta->type_of_product = $request->type_of_product;
        }

        foreach ($request->code_orders as $codeOrder) {
            $codeOrder = (object)$codeOrder;

            $coderOrderRoute = CodeOrderDeliveryRoute::find($codeOrder->id);

            $coderOrderRoute->update([
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

            foreach ($codeOrder->products as $product) {
                $product = (object)$product;

                $codeOrderRoute = ProductDeliveryRoute::find($product->id);

                $codeOrderRoute->update([
                    'product' => $product->product,
                    'amount' => $product->amount,
                ]);
            }
        }
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

    public function setRemisiones(Request $request){



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

        $remision = Remission::create([
            'comments' => $request->comments,
            'satisfaction' => $request->satisfaction,
            'delivered' => $request->delivered,
            'delivery_signature' => $request->delivery_signature,
            'received' => $request->received,
            'signature_received' => $request->signature_received,
            'delivery_route_id' => $request->delivery_route_id,
            'user_chofer_id' => $request->user_chofer_id,
            'status' => 1,2,
          
          
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
}