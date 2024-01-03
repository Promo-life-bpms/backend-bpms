<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\Remission;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use App\Models\ProductDeliveryRoute;
use Exception;
use Illuminate\Notifications\Notifiable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderPurchaseProduct;
use App\Models\SaleStatusChange;
use Illuminate\Support\Facades\DB;

/*
    Controlador para la gestión de las rutas de entrega.
 */

class DeliveryRouteController extends Controller
{
    use Notifiable;

    /**
     * Obtiene la lista de rutas de entrega.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        DB::statement("SET SQL_MODE=''");
        $isChofer =  auth()->user()->whatRoles()->where('id', 4)->first();
        $rutas = [];
        if ($isChofer) {
            DB::statement("SET SQL_MODE=''");
            $rutas = DeliveryRoute::join('code_order_delivery_routes', 'code_order_delivery_routes.delivery_route_id', 'delivery_routes.id')->where("delivery_routes.is_active", true)
                ->where("code_order_delivery_routes.user_chofer_id", auth()->user()->id)->select("delivery_routes.*")
                ->groupBy("delivery_routes.code_route")
                ->get();
            // return $rutas;
            foreach ($rutas as $ruta) {
                $ruta->count_sales = count($ruta->codeOrderDeliveryRoute()
                    ->where("code_order_delivery_routes.user_chofer_id", auth()->user()->id)
                    ->groupBy("code_sale")
                    ->get());
            }
        } else {
            $rutas = DeliveryRoute::where("is_active", true)->get();
            foreach ($rutas as $ruta) {
                $ruta->count_sales = count($ruta->codeOrderDeliveryRoute()->groupBy("code_sale")->get());
            }
        }

        return response()->json([
            'msg' => "Acceso de rutas correcto",
            'data' => ["rutas" => $rutas],
        ], Response::HTTP_OK); //200
    }


    /**
     * Prepara la creacion de una ruta de entrega.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
     * Método para almacenar una nueva ruta de entrega.
     *
     * @param Request $request La solicitud HTTP con los datos de la ruta de entrega.
     * @return JsonResponse La respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        //validar que la informacion este correcta si no no se puede registrar
        // utilizar validator

        $user =  auth()->user();

        foreach ($user->whatRoles as $rol) {
            switch ($rol->name) {

                case ("logistica-y-mesa-de-control" == $rol->name):

                    break;
                case ("administrator" == $rol->name):

                    break;
                case ("compras" == $rol->name):

                    break;

                default:
                    return response()->json(
                        [
                            'msg' => "No tienes autorizacion para generar una ruta de entrega",
                        ],

                    );
                    break;
            }
        }
        //switch con casos de true para logistica, administrador y compras


        $validation = Validator::make($request->all(), [
            'date_of_delivery' => 'required',
            'code_orders' => 'required|array',
            'code_orders.*.code_sale' => 'required|exists:sales,code_sale',
            'code_orders.*.type_of_origin' => 'required',
            'code_orders.*.origin_address' => 'required',
            'code_orders.*.type_of_destiny' => 'required',
            'code_orders.*.destiny_address' => 'required',
            'code_orders.*.hour' => 'required|date_format:H:i:s',
            'code_orders.*.attention_to' => 'required',
            'code_orders.*.action' => 'required',
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
                'user_chofer_id' => null,
                'type_of_product' => null,
                'type_of_chofer' => null,
                'num_guide' => null,
                'hour' => $codeOrder->hour,
                'attention_to' => $codeOrder->attention_to,
                'action' => $codeOrder->action,
                'observations' => $codeOrder->observations,
                'status' => 'Pendiente',
            ];

            // Agendado en ruta de entrega (Material maquilado):
            //Agendado en ruta de entrega (Material maquilado):

            foreach ($codeOrder->orders as $order) {
                $saleOrderBD = Sale::where('code_sale', $codeOrder->code_sale)->first();
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
                $type_of_product = $request->type_of_product;
                $type_of_destiny =  $codeOrder->type_of_destiny;
                if ($type_of_destiny == 'Cliente') {
                    if ($saleOrderBD->lastStatus) {
                        if ($saleOrderBD->lastStatus->status_id < 10) {
                            SaleStatusChange::create([
                                'sale_id' => $saleOrderBD->id,
                                "status_id" => 10
                            ]);
                        }
                    }
                } else {
                    if ($type_of_product == "Limpio") {
                        if ($saleOrderBD->lastStatus) {
                            if ($saleOrderBD->lastStatus->status_id < 3) {
                                SaleStatusChange::create([
                                    'sale_id' => $saleOrderBD->id,
                                    "status_id" => 3
                                ]);
                            }
                        }
                    } else {
                        if ($saleOrderBD->lastStatus) {
                            if ($saleOrderBD->lastStatus->status_id < 5) {
                                SaleStatusChange::create([
                                    'sale_id' => $saleOrderBD->id,
                                    "status_id" => 5
                                ]);
                            }
                        }
                    }
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
     * Actualiza la información del chofer de una ruta de entrega.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ruta
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInfoChofer(Request $request, $ruta, $pedido)
    {
        $validation = Validator::make($request->all(), [
            'type_of_product' => 'required|in:Limpio,Maquilado',
            'type_of_chofer' => 'required',
            'user_chofer_id' => 'required_if:type_of_chofer,==,Interno',
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
        $isAuthToUpdate =  auth()->user()->hasRole([
            'logistica-y-mesa-de-control',
            'administrator',
            'jefe-de-logistica',
            'gerente-de-operaciones',
            'almacen'
        ]);

        if (!$isAuthToUpdate) {
            return response()->json(
                ['msg' => "No tienes autorizacion para modificar los choferes",],
            );
        }
        $rutaDB = DeliveryRoute::where('code_route', $ruta)->first();
        // Chequeaos si encontró o no la ruta
        if (!$rutaDB) {
            // Se devuelve un array errors con los errores detectados y código 404
            return response()->json(['msg'  => 'No se encuentra esa ruta de entrega.'], response::HTTP_NOT_FOUND); //404
        }
        $pedidosRuta = $rutaDB->codeOrderDeliveryRoute()->where('code_sale', $pedido)->get();
        if ($pedidosRuta->count() <= 0) {
            return response()->json(['msg'  => 'No se encuentra ese pedido en la ruta.'], response::HTTP_NOT_FOUND); //404
        }
        foreach ($pedidosRuta as $codeOrder) {
            $codeOrder = (object)$codeOrder;
            $dataSale = [
                'user_chofer_id' => $request->user_chofer_id,
                'type_of_product' => $request->type_of_product,
                'type_of_chofer' => $request->type_of_chofer,
                'num_guide' => $request->num_guide,
                'observations' => $request->observations,
            ];
            $codeOrder->update($dataSale);
        }
        return response()->json(['msg'  => 'Actualizacion completa.'], response::HTTP_ACCEPTED);
    }


    /**
     * Muestra los detalles de una ruta de entrega.
     *
     * @param int $id El ID de la ruta de entrega.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con los detalles de la ruta de entrega.
     */
    public function show($id)
    {

        // Corresponde con la ruta  rutas-de-entrega
        // Buscamos un study por el ID.
        $ruta = DeliveryRoute::where('code_route', $id)->first();
        //return $ruta;
        // Chequeaos si encontró o no la ruta
        if (!$ruta) {
            // Se devuelve un array errors con los errores detectados y código 404
            return response()->json(['msg'  => 'No se encuentra esa ruta de entrega.'], response::HTTP_NOT_FOUND); //404
        }
        // $ruta->user_chofer_name = $ruta->user->name;
        // unset($ruta->user);
        $isChofer =  auth()->user()->whatRoles()->where('id', 4)->first();

        // Buscar los productos de esta ruta
        $products = ProductDeliveryRoute::join('code_order_delivery_routes', 'code_order_delivery_routes.id', 'product_delivery_routes.code_order_route_id')
            ->where('code_order_delivery_routes.delivery_route_id', $ruta->id)
            ->select('product_delivery_routes.*')
            ->get();
        // Obtener las ordenes de compra de estos productos
        $orders = [];
        foreach ($products as $product) {
            $order = $product->codeOrderRoute->orderPurchase;
            // revisar si la orden ya esta en el array
            $orderExist = false;
            foreach ($orders as $orderInArray) {
                if ($orderInArray->code_order == $order->code_order) {
                    $orderExist = true;
                    break;
                }
            }
            if (!$orderExist) {
                array_push($orders, $order);
            }
        }

        // Obtener los productos de cada orden de compra
        $dataOrders = [];
        foreach ($orders as $order) {
            $productsInThisOrder = [];
            foreach ($products as $product) {
                $orderPurchase = $product->codeOrderRoute->orderPurchase;
                if ($orderPurchase->code_order == $order->code_order) {
                    $infoProduct = $product->codeOrderRoute->orderPurchase->products()->where('odoo_product_id', $product->odoo_product_id)->first()->toArray();
                    $product = $product->toArray();
                    unset($product['code_order_route']);
                    $product['id'] = $infoProduct['id'];
                    $productsInThisOrder[] = array_merge($product, $infoProduct);
                }
            }
            $data = $order->toArray();
            $data['products'] = $productsInThisOrder;
            array_push($dataOrders, $data);
        }

        // Obtener los pedidos de cada orden de compra
        $sales = [];
        foreach ($dataOrders as $order) {
            $sale = Sale::join('code_order_delivery_routes', 'code_order_delivery_routes.code_sale', 'sales.code_sale')
                ->join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
                ->where('sales.code_sale', $order['code_sale'])
                ->where('code_order_delivery_routes.delivery_route_id', $ruta->id)
                ->when($isChofer, function ($query) {
                    $query->where("code_order_delivery_routes.user_chofer_id", auth()->user()->id);
                })
                ->select(
                    'sales.*',
                    'code_order_delivery_routes.type_of_origin',
                    'code_order_delivery_routes.origin_address',
                    'code_order_delivery_routes.type_of_destiny',
                    'code_order_delivery_routes.destiny_address',
                    'code_order_delivery_routes.hour',
                    'code_order_delivery_routes.attention_to',
                    'code_order_delivery_routes.action',
                    'code_order_delivery_routes.user_chofer_id',
                    'code_order_delivery_routes.type_of_product',
                    'code_order_delivery_routes.type_of_chofer',
                    'code_order_delivery_routes.num_guide',
                    'code_order_delivery_routes.observations',
                    'code_order_delivery_routes.status',
                    'additional_sale_information.client_name',
                    'additional_sale_information.client_contact',
                    'additional_sale_information.warehouse_company',
                    'additional_sale_information.planned_date',
                    'additional_sale_information.company'
                )
                ->first();
            // No se encontró el pedido y se continua con la siguiente orden.
            if (!$sale) {
                continue;
            }
            if ($sale->user_chofer_id) {
                $sale->chofer_name = User::find($sale->user_chofer_id)->name;
            } else {
                $sale->chofer_name = "Sin Chofer Asignado";
            }


            $sale->lastStatus->slug = $sale->lastStatus->status->slug;
            $sale->lastStatus->last_status = $sale->lastStatus->status->status;
            unset($sale->lastStatus->status);
            unset($sale->lastStatus->id);
            unset($sale->lastStatus->sale_id);
            unset($sale->lastStatus->status_id);
            unset($sale->lastStatus->updated_at);

            $remission =  $sale->remissions()->where('remisiones.delivery_route_id', $ruta->id)->first();
            if ($remission) {
                $sale->remission_id = $remission->code_remission;
            } else {
                $sale->remission_id = null;
            }
            unset($sale->ordersDeliveryRoute);

            // revisar si el pedido ya esta en el array
            $saleExist = false;
            foreach ($sales as $saleInArray) {
                if ($saleInArray->code_sale == $sale->code_sale) {
                    $saleExist = true;
                    break;
                }
            }
            if (!$saleExist) {
                array_push($sales, $sale);
            }
        }

        $dataSales = [];
        foreach ($sales as $sale) {
            $ordersInThisSale = [];
            foreach ($dataOrders as $order) {
                if ($order['code_sale'] == $sale->code_sale) {
                    $ordersInThisSale[] = $order;
                }
            }
            $data = $sale->toArray();
            unset($data['ordersDeliveryRoute']);
            $data['details_orders'] = $ordersInThisSale;
            array_push($dataSales, $data);
        }
        $ruta->pedidos = $dataSales;
        // Devolvemos la información encontrada.
        return response()->json(['msg' => 'Detalle de ruta de entrega',  'data' => ['ruta' => $ruta]], response::HTTP_OK);
    }

    /**
     * Actualiza la información del estado de una ruta de entrega.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id El ID de la ruta de entrega.
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * Elimina una ruta de entrega.
     *
     * @param  int  $deliveryRoute  El código de la ruta de entrega a eliminar.
     * @return \Illuminate\Http\JsonResponse  La respuesta JSON con el resultado de la eliminación.
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

    /**
     * Método para establecer las remisiones de una ruta de entrega.
     *
     * @param Request $request La solicitud HTTP recibida.
     * @param string $ruta El código de la ruta de entrega.
     * @return JsonResponse La respuesta JSON con los resultados de la validación y creación de las remisiones.
     */
    public function setRemisiones(Request $request, $ruta)
    {
        $validation = Validator::make($request->all(), [
            'comments' => 'required_if:status,Cancelada',
            'satisfaction' => 'required_if:status,Liberada',
            'delivered' => 'required_if:status,Liberada',
            'delivery_signature' => 'required_if:status,Liberada',
            'received' => 'required_if:status,Liberada',
            'signature_received' => 'required_if:status,Liberada',
            // 'user_chofer_id' => 'required',
            'status' => 'required|in:Liberada,Cancelada',
            'evidence' => 'required_if:status,Liberada',
            'code_sale' => 'required',
            'product_remission' => 'required_if:status,Liberada|array',
            'product_remission.*.delivered_quantity' => 'required_if:status,Liberada',
            'product_remission.*.order_purchase_product_id' => 'required_if:status,Liberada|exists:order_purchase_products,id',
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
                    array_push($errores, "El producto con el order_purchase_product_id: '" . $productRemision["order_purchase_product_id"] . "' no pertecene a esa ruta de entrega");
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
            'received' => $request->received ?: null,
            'signature_received' => $request->signature_received,
            'delivery_route_id' => $deliveryRoute->id,
            'user_chofer_id' => $request->user_chofer_id ?? null,
            'status' => $request->status,
            'evidence' => $request->evidence,
            'code_sale' => $request->code_sale,
        ]);

        //crear los productos de esa remision de entrega
        //    En bodega del maquilador:


        //En bodega de PL (Material maquilado):
        //Agendado en ruta de entrega de cliente:

        $newStatus = $request->status;
        if ($newStatus == 'Liberada') {
            foreach ($request->product_remission as $product) {
                $product = (object)$product;
                $remision->productRemission()->create([
                    'delivered_quantity' => $product->delivered_quantity,
                    'order_purchase_product_id' => $product->order_purchase_product_id,
                ]);
                $orderPurchaseProduct = OrderPurchaseProduct::find($product->order_purchase_product_id);
                $sale =  $orderPurchaseProduct->orderPurchase->sale;
                $saleProduct = $sale->saleProducts()->where('odoo_product_id', $orderPurchaseProduct->odoo_product_id)->first();
                if ($saleProduct) {
                    $saleProduct->quantity_delivered = $saleProduct->quantity_delivered + $product->delivered_quantity;
                    $saleProduct->save();
                }
            }
        }

        DB::statement("SET SQL_MODE=''");
        foreach ($deliveryRoute->codeOrderDeliveryRoute()->groupBy('code_sale')->get() as $pedido) {
            DB::statement("SET SQL_MODE=''");
            $haveRemissions = $deliveryRoute->remissions()
                ->join('product_remission', 'product_remission.remission_id', 'remisiones.id')
                ->join('order_purchase_products', 'order_purchase_products.id', 'product_remission.order_purchase_product_id')
                ->join('order_purchases', 'order_purchases.id', 'order_purchase_products.order_purchase_id')
                ->where('order_purchases.code_sale', $pedido->code_sale)
                ->select('remisiones.*')
                ->groupBy('remisiones.id')
                ->get();
            //  Entrega completa al cliente:
            $status_General_pedido = 'Pendiente';
            if (count($haveRemissions) > 0) {
                $statusPedido = "Entrega Completa";
                foreach ($deliveryRoute->codeOrderDeliveryRoute()->where('code_sale', $pedido->code_sale)->get() as $orderDR) {
                    foreach ($orderDR->productDeliveryRoute as $product) {
                        // return  $deliveryRoute->remissions;
                        $cantidad_entregada = $deliveryRoute->remissions()
                            ->join('product_remission', 'product_remission.remission_id', 'remisiones.id')
                            ->join('order_purchase_products', 'order_purchase_products.id', 'product_remission.order_purchase_product_id')
                            ->where('order_purchase_products.odoo_product_id', $product->odoo_product_id)
                            ->sum('product_remission.delivered_quantity');
                        if ($cantidad_entregada < $product->amount) {
                            $statusPedido = "Entrega Parcial";
                            break;
                        }
                    }

                    if ($statusPedido == "Entrega Parcial") {
                        break;
                    }
                }
                foreach ($deliveryRoute->codeOrderDeliveryRoute()->where('code_sale', $pedido->code_sale)->get() as $orderDR) {
                    $orderDR->status =  $statusPedido;
                    $orderDR->save();
                }

                $statusPedido;
                //revisar que las remisiones se hayan hecho correctamente y cuando  sea una entrega parcial y comleta se cambia el status
                $type_of_destiny = $pedido->type_of_destiny;

                if ($type_of_destiny == 'Maquilador') {
                    if ($pedido->lastStatus) {
                        if ($pedido->lastStatus->status_id < 4) {
                            SaleStatusChange::create([
                                'sale_id' => $pedido->id,
                                "status_id" => 4
                            ]);
                        }
                    }
                } else if ($type_of_destiny == 'Almacen') {
                    if ($pedido->lastStatus) {
                        if ($pedido->lastStatus->status_id < 6) {
                            SaleStatusChange::create([
                                'sale_id' => $pedido->id,
                                "status_id" => 6
                            ]);
                        }
                    }
                }
                /*     if ($type_of_destiny == 'Cliente') {
                    if ($pedido->lastStatus) {
                        if ($pedido->lastStatus->status_id < 10) {
                            SaleStatusChange::create([
                                'sale_id' => $pedido->id,
                                "status_id" => 10
                            ]);
                        }
                    }
                }
                */
                $status_General_pedido = 'Entrega Completa';

                $productos_Del_pedido = OrderPurchaseProduct::join("order_purchases", "order_purchases.id", "order_purchase_products.order_purchase_id")
                    ->join("sales", "sales.code_sale", "order_purchases.code_sale")
                    //->join("sales_products", "sales_products.sale_id", "sales.id")
                    ->where('sales.code_sale', $pedido->code_sale)
                    ->select("order_purchase_products.*")
                    ->get();
                foreach ($productos_Del_pedido as $producto) {
                    //return $pedido->type_of_destiny;
                    $quantity = $producto->quantity;
                    $remisiones = Remission::join('product_remission', 'product_remission.remission_id', 'remisiones.id')
                        ->join('order_purchase_products', 'order_purchase_products.id', 'product_remission.order_purchase_product_id')
                        ->join('order_purchases', 'order_purchases.id', 'order_purchase_products.order_purchase_id')
                        ->join('code_order_delivery_routes', 'code_order_delivery_routes.code_order', 'order_purchases.code_order')
                        ->where('product_remission.order_purchase_product_id', $producto->id)
                        ->where("code_order_delivery_routes.type_of_destiny", $pedido->type_of_destiny == 'Cliente')
                        //->select('product_remission.delivered_quantity')
                        ->sum("product_remission.delivered_quantity");

                    if ($remisiones < $quantity) {
                        $status_General_pedido = "Entrega Parcial";
                    }
                }
            }

            $sale = Sale::where('code_sale', $pedido->code_sale)->first();

            if ($status_General_pedido == 'Pendiente') {
            } else if ($status_General_pedido == 'Entrega Parcial') {

                if ($sale->lastStatus) {

                    if ($sale->lastStatus->status_id < 11) {

                        SaleStatusChange::create([
                            'sale_id' => $sale->id,
                            "status_id" => 11
                        ]);
                    }
                }
            }

            if ($status_General_pedido == 'Entrega Completa') {
                if ($sale->lastStatus) {
                    if ($sale->lastStatus->status_id < 12) {
                        SaleStatusChange::create([
                            'sale_id' => $sale->id,
                            "status_id" => 12
                        ]);
                    }
                }
            }


            $statuses = [
                "Pendiente" => 0,
                "Entrega Parcial" => 0,
                "Entrega Completa" => 0
            ];
            foreach ($deliveryRoute->codeOrderDeliveryRoute()->groupBy('code_sale')->get() as $ped) {
                switch ($ped->status) {
                    case 'Pendiente':
                        $statuses["Pendiente"]++;
                        break;
                    case 'Entrega Parcial':
                        $statuses["Entrega Parcial"]++;
                        break;
                    case 'Entrega Completa':
                        $statuses["Entrega Completa"]++;
                        break;

                    default:
                        break;
                }
            }
            if ($statuses["Pendiente"] > 0) {
                $deliveryRoute->status =  'En Proceso';
                $deliveryRoute->save();
            } else if ($statuses["Entrega Parcial"] > 0) {
                $deliveryRoute->status = 'Entrega Parcial';
                $deliveryRoute->save();
            } else {
                $deliveryRoute->status = 'Entrega Completa';
                $deliveryRoute->save();
            }
        }

        return response()->json(['msg' => 'Remision creada exitosamente', 'data' => ["remision" => $remision]], Response::HTTP_CREATED);
        // return response()->json(['msg' =>  'Se creo una remsion con status cancelado']);
    }

    /**
     * Método para ver las remisiones.
     *
     * Este método obtiene las remisiones con estado 1 y las devuelve en formato JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewRemision()
    {
        $remision = Remission::where("status", 1)->get();

        return response()->json([
            "msg" =>  "Acceso de remisiones correcto", 'data' => ["remision" => $remision]
        ], response::HTTP_OK); //200
    }

    /**
     * Muestra la información de una remisión específica en una ruta de entrega.
     *
     * @param string $ruta El código de la ruta de entrega.
     * @param int $id El código de la remisión.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con la información de la remisión encontrada.
     */
    public function showRemision($ruta, $id)
    {
        // Obtener la ruta de entrega
        $deliveryRoute = DeliveryRoute::where('code_route', $ruta)->first();

        if (!$deliveryRoute) {
            // Si la ruta de entrega no existe, retornar un error 404
            return response()->json(['msg' =>  'Ruta de entrega no encontrada.'], response::HTTP_NOT_FOUND);
        }

        // Obtener la remisión en la ruta de entrega
        $remision = $deliveryRoute->remissions()->where('code_remission', $id)->first();

        if (!$remision) {
            // Si la remisión no existe o no pertenece a esta ruta de entrega, retornar un error 404
            return response()->json(['msg' =>  'Remision no encontrada o no pertenece a esta ruta de entrega.'], response::HTTP_NOT_FOUND);
        }

        // Obtener la venta asociada a la remisión
        $sale = Sale::where('code_sale', $remision->code_sale)->first();

        // Obtener los productos de la remisión
        $products = $remision->productRemission;

        // Obtener los productos de esa ruta y de ese pedido
        $productsToRoute = ProductDeliveryRoute::join('code_order_delivery_routes', 'code_order_delivery_routes.id', 'product_delivery_routes.code_order_route_id')
            ->where('code_order_delivery_routes.delivery_route_id', $deliveryRoute->id)
            ->where('code_order_delivery_routes.code_sale', $sale->code_sale)
            ->select('product_delivery_routes.*', 'code_order_delivery_routes.code_order')
            ->get();

        // Asignar la cantidad de entrega esperada a cada producto de la remisión
        foreach ($products as $product) {
            $productOPP = OrderPurchaseProduct::find($product->order_purchase_product_id);
            $order = $productOPP->orderPurchase;
            foreach ($productsToRoute as $productToRoute) {
                if ($productToRoute->code_order == $order->code_order && $productToRoute->odoo_product_id == $productOPP->odoo_product_id) {
                    $product->expected_delivery_quantity = $productToRoute->amount;
                    break;
                }
            }
        }

        // Obtener las órdenes de compra únicas de los productos de la remisión
        $orders = [];
        foreach ($products as $product) {
            $order = OrderPurchaseProduct::find($product->order_purchase_product_id)->orderPurchase;
            // Revisar si la orden ya está en el array
            $orderExist = false;
            foreach ($orders as $orderInArray) {
                if ($orderInArray->code_order == $order->code_order) {
                    $orderExist = true;
                    break;
                }
            }
            if (!$orderExist) {
                array_push($orders, $order);
            }
        }

        // Organizar los productos por orden de compra
        $dataOrders = [];
        foreach ($orders as $order) {
            $productsInThisOrder = [];
            foreach ($products as $product) {
                $productOPP = OrderPurchaseProduct::find($product->order_purchase_product_id);
                $orderPurchase = $productOPP->orderPurchase;
                if ($orderPurchase->code_order == $order->code_order) {
                    $infoProduct = $productOPP->toArray();
                    $product = $product->toArray();
                    unset($product['code_order_route']);
                    unset($product['orderPurchase']);
                    $product['id'] = $infoProduct['id'];
                    $productsInThisOrder[] = array_merge($product, $infoProduct);
                }
            }
            $data = $order->toArray();
            $data['productRemision'] = $productsInThisOrder;
            array_push($dataOrders, $data);
        }

        // Agregar la información de las órdenes de compra a la venta
        $sale = $sale->toArray();
        $sale['ordersProduct'] = $dataOrders;
        $remision->pedidos =  $sale;

        // Retornar la respuesta JSON con la información de la remisión encontrada
        return response()->json(['msg' =>  'Remision encontrada.', 'data' => ["remision" => $remision]], response::HTTP_OK);
    }

    /**
     * Cancela una remisión en una ruta de entrega.
     *
     * @param string $ruta El código de la ruta de entrega.
     * @param int $id El código de la remisión.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con el mensaje de éxito o error.
     */
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
