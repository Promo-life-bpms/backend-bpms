<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\ProductDeliveryRoute;
use App\Models\User;
use App\Models\Role;
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
        $ruta = DeliveryRoute::all();

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
            'user_chofer_id' => 'required|unique:id',
            'type_of_product' => 'required',
            'status' => 'required',
            'products' => 'required|array',
            'products.*.code_sale' => 'required',
            'products.*.code_order' => 'required',
            'products.*.type_of_origin' => 'required',
            'products.*.delivery_address' => 'required',
            'products.*.type_of_destiny' => 'required',
            'products.*.destiny_address' => 'required',
            'products.*.hour' => 'required',
            'products.*.attention_to' => 'required',
            'products.*.action' => 'required',
            'products.*.num_guide' => 'required',
            'products.*.observations' => 'required'

        ]);
        if ($validation->fails()) {
            return response()->json(["errors" => $validation], 422);
        }
        // crear una ruta de entrega con los campos de Deliveryroute y guardar esa ruta de entrega en una variable
        // ::create
        $ruta = DeliveryRoute::create([
            'date_of_delivery'=>$request->date_of_delivery,
            'user_chofer_id'=>$request->user_chofer_id,
            'type_of_product'=>$request->type_of_product,
            'status'=>$request->status
        ]);
        //crear los productos de esa ruta de entrega
        //  $ruta->productsDeliveryRoute()->create
        //retornar un mensaje
        foreach ($request->products as $newProduct) {
            $ruta->productsDeliveryRoute()->create([
                'date_of_delivery'=>$request->date_of_delivery,
                'num_order'=>$request->num_order,
                'type_of_origin'=>$request->type_of_origin,
                'delivery_address'=>$request->delivery_address,
                'type_of_destiny'=>$request->type_of_destiny,
                'destiny_address'=>$request->destiny_address,
                'hour'=>$request->hour,
                'attention_to'=>$request->attention_to,
                'action'=>$request->action,
                'num_guide'=>$request->num_guide,
                'observations'=>$request->observations
            ]);
        }

        return response()->json('Ruta creada exitosamente', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return \Illuminate\Http\Response
     */
    public function show(DeliveryRoute $deliveryRoute)
    {
        //
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
    public function update(Request $request, DeliveryRoute $deliveryRoute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryRoute $deliveryRoute)
    {
        //
    }
}
