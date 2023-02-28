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
use App\Models\User;
use App\Models\Status;
use Exception;
use Illuminate\Notifications\Notifiable;
use Facade\FlareClient\Api;
use Illuminate\Database\Console\DbCommand;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Notificacion;
use App\Models\OrderPurchaseProduct;
use App\Notifications\Notificacion as NotificationsNotificacion;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Support\Facades\DB;

class DeliveryRouteController extends Controller
{
    use Notifiable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        DB::statement("SET SQL_MODE=''");
        $rutas = DeliveryRoute::where("is_active", true)->get();
        foreach ($rutas as $ruta) {
            $ruta->user_chofer_name = $ruta->user->name;
            unset($ruta->user);
            $ruta->count_sales = count($ruta->codeOrderDeliveryRoute()->groupBy("code_sale")->get());
        }

        return response()->json([
            'msg' => "Acceso de rutas correcto",
            'data' => ["rutas" => $rutas],
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
        $pedidos = Sale::join('order_purchases', 'order_purchases.code_sale', 'sales.code_sale')->whereIn('order_purchases.status_bpm', ["Cancelado", "Confirmado"])->orderBy('sales.code_sale', 'ASC')->paginate($per_page);

        foreach ($pedidos as $pedido) {
            $pedido->orders = $pedido->orders()->whereIn('order_purchases.status_bpm', ["Cancelado", "Confirmado"])->get();
            $pedido->moreInformation;
            $pedido->client_name = $pedido->moreInformation->client_name;
            $pedido->client_contact = $pedido->moreInformation->client_contact;
            unset($pedido->moreInformation);
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
            'type_of_chofer' => 'required',
            'code_orders' => 'required|array',
            'code_orders.*.code_sale' => 'required|exists:sales,code_sale',
            'code_orders.*.type_of_origin' => 'required',
            'code_orders.*.origin_address' => 'required',
            'code_orders.*.type_of_destiny' => 'required',
            'code_orders.*.destiny_address' => 'required',
            'code_orders.*.hour' => 'required|date_format:H:i:s',
            'code_orders.*.attention_to' => 'required',
            'code_orders.*.action' => 'required',
            'code_orders.*.num_guide' => 'required',
            'code_orders.*.observations' => 'required',
            'code_orders.*.orders' => 'required|array',
            'code_orders.*.orders.*.code_order' => 'required|exists:order_purchases,code_order',
            'code_orders.*.orders.*.products' => 'required|array',
            'code_orders.*.orders.*.products.*.odoo_product_id' => 'required|exists:order_purchase_products,odoo_product_id',
            'code_orders.*.orders.*.products.*.amount' => 'required',
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

        // Validar que la informacion sea la correcta
        $errores = [];
        foreach ($request->code_orders as $saleOrder) {
            $saleOrder = (object)$saleOrder;
            $saleOrderBD = Sale::where('code_sale', $saleOrder->code_sale)->first();
            foreach ($saleOrder->orders as $orderRQ) {
                $orderRQ = (object) $orderRQ;
                $orderDB = OrderPurchase::where('code_sale', $saleOrder->code_sale)->where('code_order', $orderRQ->code_order)->first();
                if (!$orderDB) {
                    array_push($errores, 'La orden de compra ' . $orderRQ->code_order . ' no pertenece al pedido ' . $saleOrder->code_sale);
                    continue;
                }
                foreach ($orderRQ->products as $productRQ) {
                    $productRQ = (object) $productRQ;
                    $productDB = OrderPurchaseProduct::where('odoo_product_id', $productRQ->odoo_product_id)->where('order_purchase_id', $orderDB->id)->first();
                    if (!$productDB) {
                        array_push($errores, 'El producto ' . $productRQ->odoo_product_id . ' no pertenece a la orden de compra ' . $orderRQ->code_order);
                        continue;
                    }
                }
                // return $saleOrder->code_sale;
            }
        }
        if (count($errores) > 0) {
            return response()->json($errores, 400);
        }
        // crear una ruta de entrega con los campos de Deliveryroute y guardar esa ruta de entrega en una variable
        //codigo de ruta
        $maxINSP = DeliveryRoute::max('code_route');
        $idInsp = null;
        if (!$maxINSP) {
            $idInsp = 1;
        } else {
            $idInsp = (int) explode('-', $maxINSP)[1];
            $idInsp++;
        }

        $ruta = DeliveryRoute::create([
            'code_route' => "RUT-" . str_pad($idInsp, 5, "0", STR_PAD_LEFT),
            'date_of_delivery' => $request->date_of_delivery,
            'user_chofer_id' => $request->user_chofer_id,
            'type_of_product' => $request->type_of_product,
            'type_of_chofer' => $request->type_of_chofer,
            'status' => 'Pendiente',
            'is_active' => 1,
        ]);
        //crear los productos de esa ruta de entrega
        //  $ruta->productsDeliveryRoute()->create
        //retornar un mensaje
        foreach ($request->code_orders as $codeOrder) {
            $codeOrder = (object)$codeOrder;
            $dataSale = [
                'code_sale' => $codeOrder->code_sale,
                'type_of_origin' => $codeOrder->type_of_origin,
                'origin_address' => $codeOrder->origin_address,
                'type_of_destiny' => $codeOrder->type_of_destiny,
                'destiny_address' => $codeOrder->destiny_address,
                'hour' => $codeOrder->hour,
                'attention_to' => $codeOrder->attention_to,
                'action' => $codeOrder->action,
                'num_guide' => $codeOrder->num_guide,
                'observations' => $codeOrder->observations,
                'status' => 'Pendiente',
            ];

            foreach ($codeOrder->orders as $order) {
                $order = (object) $order;
                $dataSale['code_order'] = $order->code_order;
                $codeOrderRoute =  $ruta->codeOrderDeliveryRoute()->create($dataSale);
                foreach ($order->products as $newProduct) {
                    $newProduct = (object)$newProduct;
                    $codeOrderRoute->productDeliveryRoute()->create([
                        'odoo_product_id' => $newProduct->odoo_product_id,
                        'amount' => $newProduct->amount,
                    ]);
                }
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
        $ruta->user_chofer_name = $ruta->user->name;
        unset($ruta->user);
        //return $ruta;
        // Chequeaos si encontró o no la ruta
        if (!$ruta) {
            // Se devuelve un array errors con los errores detectados y código 404
            return response()->json(['msg'  => 'No se encuentra esa ruta de entrega.'], response::HTTP_NOT_FOUND); //404
        }
        DB::statement("SET SQL_MODE=''");
        $pedidos = Sale::join('code_order_delivery_routes', 'code_order_delivery_routes.code_sale', 'sales.code_sale')
            //->join('code_order_delivery_routes','code_order_delivery_routes.delivery_route_id','delivery_routes.id')
            ->join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->join("order_purchases", "order_purchases.code_sale", "sales.code_sale")
            ->join("order_purchase_products", "order_purchase_products.order_purchase_id", "order_purchases.id")
            ->join("product_delivery_routes", "product_delivery_routes.odoo_product_id", "order_purchase_products.odoo_product_id")
            ->where("code_order_delivery_routes.delivery_route_id", $ruta->id)
            ->select(
                'sales.*',
                'code_order_delivery_routes.type_of_origin',
                'code_order_delivery_routes.origin_address',
                'code_order_delivery_routes.type_of_destiny',
                'code_order_delivery_routes.destiny_address',
                'code_order_delivery_routes.hour',
                'code_order_delivery_routes.attention_to',
                'code_order_delivery_routes.action',
                'code_order_delivery_routes.num_guide',
                'code_order_delivery_routes.observations',
                'code_order_delivery_routes.status',
                'additional_sale_information.client_name',
                'additional_sale_information.client_contact',
                'additional_sale_information.warehouse_company',
                'additional_sale_information.planned_date',
                'additional_sale_information.company'
            )
            ->groupBy('sales.id')
            ->get();

        foreach ($pedidos as $pedido) {
            $pedido = $pedidos[1];
            $new = CodeOrderDeliveryRoute::join('remisiones', 'remisiones.delivery_route_id', 'code_order_delivery_routes.delivery_route_id')
                ->join('product_remission', 'product_remission.remission_id', 'remisiones.id')
                ->join('order_purchase_products', 'product_remission.order_purchase_product_id', 'order_purchase_products.id')
                ->where('code_order_delivery_routes.delivery_route_id', $ruta->id)
                ->where('code_order_delivery_routes.code_sale', $pedido->code_sale)
                // ->select('remisiones.code_remission')
                ->get();
            return $new;

            // $pedido->remission_id = $new ? $new->code_remission : null;
            unset($pedido->ordersDeliveryRoute);
            unset($pedido->status_id);
            return    $new;
            //return $pedido;
            //return $pedido->orders;
            DB::statement("SET SQL_MODE=''");
            $pedido->details_orders = $pedido->orders()
                ->join('order_purchase_products', 'order_purchase_products.order_purchase_id', 'order_purchases.id')
                ->join("product_delivery_routes", "product_delivery_routes.odoo_product_id", "order_purchase_products.odoo_product_id")
                ->join("code_order_delivery_routes", "code_order_delivery_routes.id", "product_delivery_routes.code_order_route_id")
                ->where("code_order_delivery_routes.delivery_route_id", $ruta->id)
                ->select("order_purchases.*")
                ->groupBy('order_purchases.id')
                ->get();
            // unset($pedido->details_orders);
            foreach ($pedido->details_orders as $productNew) {
                DB::statement("SET SQL_MODE=''");
                $productNew->products = $productNew->products()
                    ->join('product_delivery_routes', 'product_delivery_routes.odoo_product_id', 'order_purchase_products.odoo_product_id')
                    ->join("code_order_delivery_routes", "code_order_delivery_routes.id", "product_delivery_routes.code_order_route_id")
                    ->where('code_order_delivery_routes.delivery_route_id', $ruta->id)
                    ->where('order_purchase_products.order_purchase_id', $productNew->id)
                    ->select(
                        'order_purchase_products.*',
                        'product_delivery_routes.amount'
                    )
                    ->groupBy('order_purchase_products.id')
                    ->get();
            }
        }
        $ruta->pedidos = $pedidos;


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
    /*   public function update(Request $request,  $id)
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
                $codeOrderDB->origin_address = $codeOrderRequest->origin_address;
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
                    return $product;
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
                    'origin_address' => $codeOrderRequest->origin_address,
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

        return response()->json(['msg' => 'Ruta actualizada correctamente!'], response::HTTP_OK);
    } */
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request,  $id)
    {

        $validation = Validator::make($request->all(), [
            'status' => 'required|in:Cancelada',
            'elaborated' => 'required',
            'revised' => 'required',
            'reason' => 'required'
        ]);
        if ($validation->fails()) {

            return response()->json([
                'msg' => "Error de validacion de la remision",
                'data' => ["errorValidacion" => $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY); //422
        }
        $ruta = DeliveryRoute::where('code_route', $id)->first();

        if (!$ruta) {
            // Retornar mensaje
            return response()->json([
                'msg' => "ruta no encontrada"
            ], response::HTTP_NOT_FOUND);
        }
        //
        $user =  auth()->user();
        // return  $user =  auth()->user();

        foreach ($user->whatRoles as $rol) {
            if ("logistica-y-mesa-de-control" == $rol->name || "administrator" == $rol->name) {
                $ruta->status = $request->status;
                $ruta->elaborated = $request->elaborated;
                $ruta->revised = $request->revised;
                $ruta->reason = $request->reason;
                $ruta->save();
                return response()->json(['msg' => 'Status de la ruta actualizada correctamente'], response::HTTP_ACCEPTED);
            }
        }

        return response()->json(['msg' => 'Solo mesa de control puede modificar el status'], response::HTTP_BAD_REQUEST);
    }
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
            'status' => 'required|in:Liberada,Cancelada',
            'evidence' => 'required',
            'product_remission' => 'required_if:status,Liberada|array',
            // 'product_remission.*.remission_id' => 'required',
            'product_remission.*.delivered_quantity' => 'required',
            'product_remission.*.order_purchase_product_id' => 'required|exists:order_purchase_products,id',
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
        $newStatus = $request->status;
        if ($newStatus == 'Liberada') {
            $errores = [];
            foreach ($request->product_remission as $productRemision) {
                $product = OrderPurchaseProduct::find($productRemision["order_purchase_product_id"]);

                if (!$product->orderPurchase->codeOrderDeliveryRoute($deliveryRoute->id)) {
                    // return $deliveryRoute->id;
                    array_push($errores, "El producto con el order_purchase_id: '" . $productRemision["order_purchase_product_id"] . "' no pertecene a esa ruta de entrega");
                }
            }
            if (count($errores) > 0) {
                return response()->json($errores, 400);
            }
        }

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
            'status' => $request->status,
            'evidence' => $request->evidence,
        ]);
        //crear los productos de esa remision de entrega

        $newStatus = $request->status;
        if ($newStatus == 'Liberada') {
            foreach ($request->product_remission as $product) {
                $product = (object)$product;
                $remision->productRemission()->create([
                    'delivered_quantity' => $product->delivered_quantity,
                    'order_purchase_product_id' => $product->order_purchase_product_id,
                ]);
            }
        }
        foreach ($deliveryRoute->codeOrderDeliveryRoute->groupBy('code_sale')->first() as $pedido) {
            $entregaCompleta = "Entrega Completa";
            foreach ($deliveryRoute->codeOrderDeliveryRoute()->where('code_sale', $pedido->code_sale)->get() as $orderDR) {
                foreach ($orderDR->productDeliveryRoute as $product) {
                    // return  $deliveryRoute->remissions;
                    $cantidad_entregada = $deliveryRoute->remissions()
                        ->join('product_remission', 'product_remission.remission_id', 'remisiones.id')
                        ->join('order_purchase_products', 'order_purchase_products.id', 'product_remission.order_purchase_product_id')
                        ->where('order_purchase_products.odoo_product_id', $product->odoo_product_id)
                        ->sum('product_remission.delivered_quantity');
                    // return [$cantidad_entregada, $product->amount, $cantidad_entregada <= $product->amount];
                    if ($cantidad_entregada < $product->amount) {
                        $entregaCompleta = "Entrega Parcial";
                        break;
                    }
                }
                if ($entregaCompleta == "Entrega Parcial") {
                    break;
                }
            }
            // Actualizar el estado de ese pedido(Ordenes Deliveries)
            return $entregaCompleta;
        }
        // Revisar a que codigo de orden y pedido de compra pertenecen los products
        // Revisar si hay mas productos en esa orden de esa ruta
        // Revisar si hay mas ordenes en ese pedido
        // Revisar si ese pedido se completo correctamente o no
        // Actualizar el estatus del pedido en especifico
        // Actualizar el estado de la ruta de entrega
        return $deliveryRoute->remissions->join;
        return response()->json(['msg' => 'Remision creada exitosamente', 'data' => ["remision" => $remision]], Response::HTTP_CREATED);
        return response()->json(['msg' =>  'Se creo una remsion con status cancelado']);
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

        $remision = $deliveryRoute->remissions()->where('code_remission', $id)->first();
        //return $remision->id;
        if (!$remision) {
            return response()->json(['msg' =>  'Remision no encontrada o no pertenece a esta ruta de entrega.'], response::HTTP_NOT_FOUND); //404
        }

        DB::statement("SET SQL_MODE=''");
        $pedidos = Sale::join('code_order_delivery_routes', 'code_order_delivery_routes.code_sale', 'sales.code_sale')
            ->join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->join("order_purchases", "sales.code_sale", "order_purchases.code_sale")
            ->join('order_purchase_products', 'order_purchase_products.order_purchase_id', 'order_purchases.id')
            ->join('product_remission', 'product_remission.order_purchase_product_id', 'order_purchase_products.id')
            ->where("product_remission.remission_id", $remision->id)
            ->select('sales.*', 'code_order_delivery_routes.*', 'additional_sale_information.*')
            ->groupBy('sales.id')
            ->get();

        foreach ($pedidos as $pedido) {
            $pedido->ordersProduct = $pedido->orders()
                ->join('order_purchase_products', 'order_purchase_products.order_purchase_id', 'order_purchases.id')
                ->join('product_remission', 'product_remission.order_purchase_product_id', 'order_purchase_products.id')
                ->where("product_remission.remission_id", $remision->id)
                ->select("order_purchases.*")
                ->get();

            foreach ($pedido->ordersProduct as $order) {
                $order->productRemision = $order->products()
                    ->join('product_remission', 'product_remission.order_purchase_product_id', 'order_purchase_products.id')
                    ->where('product_remission.remission_id', $remision->id)
                    ->where('order_purchase_products.order_purchase_id', $order->id)
                    ->select("order_purchase_products.*", "product_remission.*")
                    ->get();
                foreach ($order->productRemision as $productRem) {
                    $data = $deliveryRoute->codeOrderDeliveryRoute()
                        ->join('product_delivery_routes', 'product_delivery_routes.code_order_route_id', 'code_order_delivery_routes.id')
                        ->where('product_delivery_routes.odoo_product_id', $productRem->odoo_product_id)
                        ->select('product_delivery_routes.*')
                        ->first();
                    $productRem->expected_delivery_quantity = $data->amount;
                    # code...
                }
            }
        }




        // Devolvemos la información encontrada.

        $remision->pedidos = $pedidos;

        return response()->json(['msg' =>  'Remision encontrada.', 'data' => ["remision" => $remision]], response::HTTP_OK); //200
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
