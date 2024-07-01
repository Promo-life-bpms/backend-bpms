<?php

namespace App\Http\Controllers;


use App\Models\CodeOrderDeliveryRoute;
use App\Models\DeliveryRoute;
use App\Models\HistoryDeliveryRoute;
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
use App\Models\OrdersGroup;
use App\Models\SaleStatusChange;
use App\Models\StatusDeliveryRoute;
use App\Models\StatusDeliveryRouteChange;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $sale)
    {
        $validation = Validator::make($request->all(), [
            'delivery_route' => 'required|array',
            'delivery_route.*.code_order' => 'required',
            'delivery_route.*.product_id' => 'required',
            'delivery_route.*.type_of_destiny' => 'required',
            'delivery_route.*.type' => 'required',
            'delivery_route.*.date_of_delivery' => 'required',
            'delivery_route.*.status_delivery' => 'required',
            'delivery_route.*.shipping_type' => 'required',
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
        if (count($errores) > 0) {
            return response()->json($errores, 400);
        }
        //$sale = Sale::where('code_sale', $sale)->first();
        $routes = [];
        $productIdsWithStates = [];
        $color = null;

        foreach ($request['delivery_route'] as $deliveryRouteData) {

            $order = OrdersGroup::where('code_order_oc', $deliveryRouteData['code_order'])->where('code_sale', $sale)->first();
            if (!$order) {
                return response()->json(
                    [
                        'msg' => "no existe esta orden",
                    ],
                    response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            if (
                $deliveryRouteData['type'] == "Parcial" && $deliveryRouteData['status_delivery'] == "Completo" ||
                $deliveryRouteData['type'] == "Parcial" && $deliveryRouteData['status_delivery'] == "Reprogramado" ||
                $deliveryRouteData['type'] == "Parcial" && $deliveryRouteData['status_delivery'] == "Pendiente" ||
                $deliveryRouteData['type'] == "Total" && $deliveryRouteData['status_delivery'] == "Pendiente" ||
                $deliveryRouteData['type'] == "Total" && $deliveryRouteData['status_delivery'] == "Reprogramado"
            ) {
                $color = 1;
            } elseif ($deliveryRouteData['status_delivery'] == "Completo" && $deliveryRouteData['type'] == "Total") {
                $color = 2;
            } else {
                $color = 0; // Establecer un valor predeterminado para $color si no se cumple ninguna condición
            }

            $visible = null;

            if ($color == 2) {
                $visible = 1; //El visible 1 es de que ya esta completo y total
            } else if ($color == 1) {
                $visible = 0; //El visible 0 es de que status sea diferente a completo y a total puede ser que sea parcial y que sea reprogramado o pendiente
            } else {
                $visible = 2; //El visible 2 es que no tiene ningun dato
            }
            //$visible = ($color == 2) ? 1 : 0;
            $ruta_ant = DeliveryRoute::where('product_id', $deliveryRouteData['product_id'])->where('type_of_destiny', $deliveryRouteData['type_of_destiny'])->first();
            if (!$ruta_ant) {
                HistoryDeliveryRoute::create([
                    'code_sale' => $sale,
                    'code_order' => $order->code_order_oc,
                    'product_id' => $deliveryRouteData['product_id'],
                    'type_of_destiny' => $deliveryRouteData['type_of_destiny'],
                    'type' => $deliveryRouteData['type'],
                    'date_of_delivery' => $deliveryRouteData['date_of_delivery'],
                    'status_delivery' => $deliveryRouteData['status_delivery'],
                    'shipping_type' => $deliveryRouteData['shipping_type'],
                    'color' => $color,
                    'visible' => $visible,
                ]);
                $ruta = DeliveryRoute::create([
                    'code_sale' => $sale,
                    'code_order' => $order->code_order_oc,
                    'product_id' => $deliveryRouteData['product_id'],
                    'type' => $deliveryRouteData['type'],
                    'type_of_destiny' => $deliveryRouteData['type_of_destiny'],
                    'date_of_delivery' => $deliveryRouteData['date_of_delivery'],
                    'status_delivery' => $deliveryRouteData['status_delivery'],
                    'shipping_type' => $deliveryRouteData['shipping_type'],
                    'color' => $color,
                    'visible' => $visible
                ]);
                $routes[] = $ruta;
            } else {
                return response()->json(['ya existe una ruta para ese destino']);
            }
        }
        $existingStatuses = StatusDeliveryRouteChange::where('order_purchase_product_id', $deliveryRouteData['product_id'])
            ->where('code_order', $order->code_order)
            ->get();

        $statuses = StatusDeliveryRoute::all();

        $status_deliverys = [];

        // Si no existen registros, crea nuevos
        if ($existingStatuses->isEmpty()) {
            foreach ($routes as $ruta) {
                // Busca el estado correspondiente al tipo_de_destino de la ruta actual
                $status = collect($statuses)->firstWhere('status', $ruta['type_of_destiny']);

                if ($status) {
                    // Crea el registro de estado utilizando el valor "visible" de la ruta
                    $statuses_Delivery = StatusDeliveryRouteChange::create([
                        'order_purchase_product_id' => $ruta['product_id'],
                        'code_order' => $ruta['code_order'],
                        'status' => $status['status'],
                        'visible' => $ruta['visible'], // Utiliza la visibilidad de la ruta
                    ]);

                    $status_deliverys[] = $statuses_Delivery;
                }
            }

            $status_order = [
                'Almacen PL',
                'Maquila',
                'Almacen PM',
                'Cliente'
            ];

            $current_statuses = collect($status_deliverys)->pluck('status')->toArray();

            // return $current_statuses;

            foreach ($status_deliverys as $status) {
                // Determinar el índice del estado actual en el orden
                $status_index = array_search($status['status'], $status_order);
                if ($status_index !== false) {
                    // Crear todos los estados anteriores al actual si no están presentes en $current_statuses
                    for ($i = 0; $i < $status_index; $i++) {
                        $previous_status = $status_order[$i];

                        if (!in_array($previous_status, $current_statuses)) {
                            // Crear el registro de estado con visibilidad 2
                            $statuses_Delivery = StatusDeliveryRouteChange::create([
                                'order_purchase_product_id' => $status_deliverys[0]['order_purchase_product_id'], // Usa el primer product_id de $status_deliverys, asumiendo que es el mismo
                                'code_order' => $status_deliverys[0]['code_order'], // Usa el primer code_order de $status_deliverys, asumiendo que es el mismo
                                'status' => $previous_status,
                                'visible' => 2, // Asigna visibilidad 2
                            ]);

                            // Agregar el nuevo estado a $current_statuses para que no se duplique
                            $current_statuses[] = $previous_status;
                        }
                    }
                }
            }
        }
        return response()->json([
            'msg' => 'Ruta Creada Existosamente',
            'data' => [
                "ruta" => $routes,
                "status" => $status_deliverys
            ]
        ], Response::HTTP_CREATED);
    }

    public function excelCompras($ruta)
    {

        // Crea un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Obtiene tu conjunto de datos de la base de datos o de donde sea necesario
        $code_route = DeliveryRoute::join('code_order_delivery_routes', 'code_order_delivery_routes.delivery_route_id', 'delivery_routes.id')
            ->where('code_route', $ruta)
            ->first();

        if ($code_route->type_of_product == 'Limpio') {


            $data = [
                ['id' => $code_route->id],
                [''],

            ];
            return $data;
        } else {
            $data = [
                ['Nombre', 'Correo'],
                ['Ejemplo 1', 'ejemplo1@email.com'],
                ['Ejemplo 2', 'ejemplo2@email.com'],
                // ... más datos
            ];
        }

        // Selecciona la hoja activa
        $sheet = $spreadsheet->getActiveSheet();

        // Llena la hoja con tus datos
        $sheet->fromArray($data, NULL, 'A1');

        // Crea un objeto de escritura (Writer) y exporta a formato Xlsx
        $writer = new Xlsx($spreadsheet);

        // Guarda el archivo en el sistema de archivos o devuelve una respuesta de descarga
        $filename = 'archivo_excel.xlsx';
        $writer->save($filename);

        // Puedes devolver una respuesta aquí si lo necesitas
        return response()->download($filename)->deleteFileAfterSend();
    }

    public function show($product_id)
    {

        // Corresponde con la ruta  rutas-de-entrega
        // Buscamos un study por el ID.
        $ruta = HistoryDeliveryRoute::where('product_id', $product_id)->orderBy('created_at', 'desc')->get();
        // Chequeaos si encontró o no la ruta
        if (!$ruta) {
            // Se devuelve un array errors con los errores detectados y código 404
            return response()->json(['msg'  => 'No existe una ruta de entrega.'], response::HTTP_NOT_FOUND); //404
        }


        return response()->json(['msg' => 'Detalle de ruta de entrega',  'data' => ['ruta' => $ruta]], response::HTTP_OK);
    }


    public function updateRuta(Request $request, $product_id)
    {
        $orders_products = OrdersGroup::where('product_id_oc', $product_id)->get();
        $statuses_Delivery = StatusDeliveryRoute::all()->sortBy('id');  // Ordenar por 'id'

        foreach ($orders_products as $order) {
            $rutas = DeliveryRoute::where('product_id', $order->product_id_oc)->get();

            foreach ($request->all() as $rutaRequest) {
                foreach ($rutas as $ruta) {
                    $type = $rutaRequest['type'] ?? $ruta->type ?? null;
                    $status_delivery = $rutaRequest['status_delivery'] ?? $ruta->status_delivery ?? null;

                    if ($type && $status_delivery) {
                        if ($type == "Parcial" && in_array($status_delivery, ["Completo", "Reprogramado", "Pendiente"])) {
                            $color = 1;
                        } elseif ($type == "Total" && in_array($status_delivery, ["Pendiente", "Reprogramado"])) {
                            $color = 1;
                        } elseif ($type == "Total" && $status_delivery == "Completo") {
                            $color = 2;
                        } else {
                            $color = 0;
                        }
                    } else {
                        $color = 0;
                    }

                    if ($color == 2) {
                        $visible = 1;
                    } elseif ($color == 1) {
                        $visible = 0;
                    } else {
                        $visible = 2;
                    }
                }

                $ruta_ant = DeliveryRoute::where('product_id', $product_id)->where('type_of_destiny', $rutaRequest['type_of_destiny'])->first();
                $newrut = DeliveryRoute::where('product_id', $product_id)->first();

                if ($ruta_ant) {
                    DB::table('delivery_routes')->where('type_of_destiny', $rutaRequest['type_of_destiny'])->where('product_id', $product_id)->update([
                        'type' => $rutaRequest['type'] ?? $ruta_ant->type,
                        'date_of_delivery' => $rutaRequest['date_of_delivery'] ?? $ruta_ant->date_of_delivery,
                        'status_delivery' => $rutaRequest['status_delivery'] ?? $ruta_ant->status_delivery,
                        'shipping_type' => $rutaRequest['shipping_type'] ?? $ruta_ant->shipping_type,
                        'color' => $color,
                        'visible' =>  $visible
                    ]);
                    HistoryDeliveryRoute::create([
                        'code_sale' => $ruta_ant->code_sale,
                        'code_order' => $ruta_ant->code_order,
                        'product_id' => $ruta_ant->product_id,
                        'type_of_destiny' => $ruta_ant->type_of_destiny,
                        'type' => $rutaRequest['type'] ?? $ruta_ant->type,
                        'date_of_delivery' => $rutaRequest['date_of_delivery'] ?? $ruta_ant->date_of_delivery,
                        'status_delivery' => $rutaRequest['status_delivery'] ?? $ruta_ant->status_delivery,
                        'shipping_type' => $rutaRequest['shipping_type'] ?? $ruta_ant->shipping_type,
                        'color' => $color,
                        'visible' => $visible,
                    ]);
                } else {
                    DeliveryRoute::create([
                        'code_sale' => $newrut->code_sale,
                        'code_order' => $newrut->code_order,
                        'product_id' => $product_id,
                        'type' => $rutaRequest['type'] ?? $newrut->type,
                        'type_of_destiny' => $rutaRequest['type_of_destiny'],
                        'date_of_delivery' => $rutaRequest['date_of_delivery'] ?? $newrut->date_of_delivery,
                        'status_delivery' => $rutaRequest['status_delivery'] ?? $newrut->status_delivery,
                        'shipping_type' => $rutaRequest['shipping_type'] ?? $newrut->shipping_type,
                        'color' => $color,
                        'visible' =>  $visible
                    ]);
                    HistoryDeliveryRoute::create([
                        'code_sale' => $newrut->code_sale,
                        'code_order' => $newrut->code_order,
                        'product_id' => $product_id,
                        'type_of_destiny' =>  $rutaRequest['type_of_destiny'],
                        'type' => $rutaRequest['type'] ?? $newrut->type,
                        'date_of_delivery' => $rutaRequest['date_of_delivery'] ?? $newrut->date_of_delivery,
                        'status_delivery' => $rutaRequest['status_delivery'] ?? $newrut->status_delivery,
                        'shipping_type' => $rutaRequest['shipping_type'] ?? $newrut->shipping_type,
                        'color' => $color,
                        'visible' => $visible,
                    ]);
                }

                // Verificar y crear el registro en StatusDeliveryRouteChange si no existe
                $existingStatusChange = StatusDeliveryRouteChange::where('order_purchase_product_id', $product_id)
                    ->where('status', $rutaRequest['type_of_destiny'])
                    ->first();

                if (!$existingStatusChange) {
                    StatusDeliveryRouteChange::create([
                        'order_purchase_product_id' => $product_id,
                        'status' => $rutaRequest['type_of_destiny'],
                        'code_order' => $rutaRequest['code_order'],
                        'visible' => $visible
                    ]);
                }

                // Obtener el status_id del type_of_destiny actual
                $current_status_id = $statuses_Delivery->where('status', $rutaRequest['type_of_destiny'])->first()->id;

                // Crear registros para todos los status_id anteriores si el status_id es mayor que 1
                if ($current_status_id > 1) {
                    $previous_statuses = $statuses_Delivery->where('id', '<', $current_status_id);
                    foreach ($previous_statuses as $previous_status) {
                        $existingStatusChange = StatusDeliveryRouteChange::where('order_purchase_product_id', $product_id)
                            ->where('status', $previous_status->status)
                            ->first();

                        if (!$existingStatusChange) {
                            StatusDeliveryRouteChange::create([
                                'order_purchase_product_id' => $product_id,
                                'status' => $previous_status->status,
                                'code_order' => $rutaRequest['code_order'],
                                'visible' => 2  // Asignar un valor por defecto o según la lógica necesaria
                            ]);
                        }
                    }
                }
            }

            $rutas_update = DeliveryRoute::where('product_id', $order->product_id_oc)->get();
            $statuschanges = StatusDeliveryRouteChange::where('order_purchase_product_id', $product_id)->get();

            foreach ($statuschanges as $status_change) {
                foreach ($rutas_update as $ruta_update) {
                    if ($status_change->status == $ruta_update->type_of_destiny) {
                        $status_change->status = $ruta_update->type_of_destiny;
                        $status_change->visible = $ruta_update->visible;
                        $status_change->save();
                    }
                }
            }
        }

        $statuschange = StatusDeliveryRouteChange::where('order_purchase_product_id', $product_id)->get();
        $delivery_update = DeliveryRoute::where('product_id', $order->product_id_oc)->get();

        return response()->json(['ruta actualizada' => $delivery_update, 'status_Actuales' => $statuschange]);
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
    public function DeliveryRoutePurchaseCompletas(Request $request)
    {
        $date = $request->input('date');
        $type = $request->input('type');
        $status = $request->input('status_delivery');
        $destiny = $request->input('destiny');
        $user =  auth()->user();
        foreach ($user->whatRoles as $rol) {
            switch ($rol->id) {
                case 6: //compras
                    $query = DeliveryRoute::join('order_purchase_products', 'order_purchase_products.id', '=', 'delivery_routes.product_id')
                        ->whereIn('delivery_routes.type_of_destiny', ['Almacen PL', 'Maquila', 'ALmacen PM'])
                        ->where('status_delivery', 'Completo')
                        ->where('type', 'Total')
                        ->select('delivery_routes.*', 'order_purchase_products.description');
                    break;
                case 18: //mesa_de_control

                    $query = DeliveryRoute::join('order_purchase_products', 'order_purchase_products.id', '=', 'delivery_routes.product_id')
                        ->whereIn('delivery_routes.type_of_destiny', ['Cliente'])
                        ->where('status_delivery', 'Completo')
                        ->where('type', 'Total')
                        ->select('delivery_routes.*', 'order_purchase_products.description');
                    break;
                case 17 || 1: //logistica
                    $query = DeliveryRoute::join('order_purchase_products', 'order_purchase_products.id', '=', 'delivery_routes.product_id')
                        ->where('status_delivery', 'Completo')
                        ->where('type', 'Total')
                        ->select('delivery_routes.*', 'order_purchase_products.description');
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
        if ($date) {
            // Assuming you have a column like 'delivery_date' in 'delivery_routes' table
            $query->whereDate('delivery_routes.date_of_delivery', '=', $date);
        }
        if ($type) {
            $query->where('delivery_routes.type', $type);
        }
        if ($status) {
            $query->where('delivery_routes.status_delivery', '=', $status);
        }
        if ($destiny) {
            $query->where('delivery_routes.type_of_destiny', '=', $destiny);
        }

        $rutasRPCom = $query->get();
        return response()->json(['Rutas_Completas' => $rutasRPCom]);
    }
    public function DeliveryRoutePurchasePendientes(Request $request)
    {
        $date = $request->input('date');
        $type = $request->input('type');
        $status = $request->input('status_delivery');
        $destiny = $request->input('destiny');
        $user =  auth()->user();
        foreach ($user->whatRoles as $rol) {
            switch ($rol->id) {
                case 6: //compras
                    $query = DeliveryRoute::join('order_purchase_products', 'order_purchase_products.id', 'delivery_routes.product_id')
                        ->whereIn('delivery_routes.type_of_destiny', ['Almacen PL', 'Maquila', 'ALmacen PM'])
                        ->select('delivery_routes.*', 'order_purchase_products.description');
                    // Filtrar por tipo y estado según la lógica proporcionada
                    $query->where(function ($query) {
                        $query->where('delivery_routes.type', 'Total')
                            ->whereIn('delivery_routes.status_delivery', ['Pendiente', 'Reprogramado'])
                            ->orWhere(function ($query) {
                                $query->where('delivery_routes.type', 'Parcial')
                                    ->whereIn('delivery_routes.status_delivery', ['Pendiente', 'Reprogramado', 'Completo']);
                            });
                    });
                    break;
                case 18: //mesa_de_control
                    $query = DeliveryRoute::join('order_purchase_products', 'order_purchase_products.id', 'delivery_routes.product_id')
                        ->whereIn('delivery_routes.type_of_destiny', ['Cliente'])
                        ->select('delivery_routes.*', 'order_purchase_products.description');
                    $query->where(function ($query) {
                        $query->where('delivery_routes.type', 'Total')
                            ->whereIn('delivery_routes.status_delivery', ['Pendiente', 'Reprogramado'])
                            ->orWhere(function ($query) {
                                $query->where('delivery_routes.type', 'Parcial')
                                    ->whereIn('delivery_routes.status_delivery', ['Pendiente', 'Reprogramado', 'Completo']);
                            });
                    });
                    break;
                case 17 || 1: //logistica
                    $query = DeliveryRoute::join('order_purchase_products', 'order_purchase_products.id', 'delivery_routes.product_id')
                        ->select('delivery_routes.*', 'order_purchase_products.description');
                    $query->where(function ($query) {
                        $query->where('delivery_routes.type', 'Total')
                            ->whereIn('delivery_routes.status_delivery', ['Pendiente', 'Reprogramado'])
                            ->orWhere(function ($query) {
                                $query->where('delivery_routes.type', 'Parcial')
                                    ->whereIn('delivery_routes.status_delivery', ['Pendiente', 'Reprogramado', 'Completo']);
                            });
                    });
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
        // Aplicar filtros adicionales si se proporcionan
        if ($date) {
            $query->whereDate('delivery_routes.date_of_delivery', '=', $date);
        }
        if ($type) {
            $query->where('delivery_routes.type', $type);
        }
        if ($status) {
            $query->where('delivery_routes.status_delivery', '=', $status);
        }
        if ($destiny) {
            $query->where('delivery_routes.type_of_destiny', '=', $destiny);
        }
        $rutasRPPen = $query->get();
        return response()->json(['Rutas_Pendientes' => $rutasRPPen]);
    }

    public function updateDeliveryPurchasePendientes(Request $request)
    {

        /*  $querys = DeliveryRoute::join('order_purchase_products', 'order_purchase_products.id', 'delivery_routes.product_id')
            ->whereIn('delivery_routes.type_of_destiny', ['Almacen PL', 'Maquila', 'ALmacen PM'])
            ->whereIn('status_delivery', ['Pendiente', 'Reprogramado'])
            ->select('delivery_routes.*', 'order_purchase_products.description')->get(); */
        foreach ($request->all() as $rutaPenRequest) {
            $rutasPurPed = DeliveryRoute::where('id', $rutaPenRequest['id'])->get();
            foreach ($rutasPurPed as $rutaPurPed) {
                $type = $rutaPenRequest['type'] ?? $rutaPurPed->type ?? null;
                $status_delivery = $rutaPenRequest['status_delivery'] ?? $rutaPurPed->status_delivery ?? null;
                // $product_id = $rutaPenRequest['product_id'] ?? $query->product_id ?? null;

                if ($type && $status_delivery) {
                    if ($type == "Parcial" && in_array($status_delivery, ["Completo", "Reprogramado", "Pendiente"])) {
                        $color = 1;
                    } elseif ($type == "Total" && in_array($status_delivery, ["Pendiente", "Reprogramado"])) {
                        $color = 1;
                    } elseif ($type == "Total" && $status_delivery == "Completo") {
                        $color = 2;
                    } else {
                        $color = 0;
                    }
                } else {
                    $color = 0;
                }

                if ($color == 2) {
                    $visible = 1;
                } elseif ($color == 1) {
                    $visible = 0;
                } else {
                    $visible = 2;
                }
            }
            $rutaPen = DeliveryRoute::where('id', $rutaPenRequest['id'])->first();

            if ($rutaPen) {
                DB::table('delivery_routes')->Where('product_id', $rutaPenRequest['product_id'])
                    ->where('type_of_destiny', $rutaPenRequest['type_of_destiny'])->update([
                        'type' => $rutaPenRequest['type'] ?? $rutaPen->type,
                        'date_of_delivery' => $rutaPenRequest['date_of_delivery'] ?? $rutaPen->date_of_delivery,
                        'status_delivery' => $rutaPenRequest['status_delivery'] ?? $rutaPen->status_delivery,
                        'shipping_type' => $rutaPenRequest['shipping_type'] ?? $rutaPen->shipping_type,
                        'color' => $color,
                        'visible' =>  $visible
                    ]);
            }
            $rutas_updatePed = DeliveryRoute::where('id', $rutaPenRequest['id'])->get();
            $statuschanges = StatusDeliveryRouteChange::all()->where('order_purchase_product_id', $rutaPenRequest['product_id']);
            foreach ($statuschanges as $status_change) {

                foreach ($rutas_updatePed as $ruta_updatePed) {
                    if ($status_change->status == $ruta_updatePed->type_of_destiny) {
                        $status_change->status = $ruta_updatePed->type_of_destiny;
                        $status_change->visible = $ruta_updatePed->visible;
                        $status_change->save();
                    }
                }
            }
            foreach ($rutas_updatePed as $rutaupdate) {
                HistoryDeliveryRoute::create([
                    'code_sale' => $rutaupdate->code_sale,
                    'code_order' => $rutaupdate->code_order,
                    'product_id' => $rutaupdate->product_id,
                    'type_of_destiny' => $rutaupdate->type_of_destiny,
                    'type' => $rutaupdate->type,
                    'date_of_delivery' => $rutaupdate->date_of_delivery,
                    'status_delivery' => $rutaupdate->status_delivery,
                    'shipping_type' => $rutaupdate->shipping_type,
                    'color' => $rutaupdate->color,
                    'visible' => $rutaupdate->visible,
                ]);
            }
        }
    }
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
    /*  public function deleteSaleDelyvery($ruta, $pedido)
    {

        $delivery = DeliveryRoute::where('code_route', $ruta)->first();

        if (!$delivery) {
            return response()->json(["message" => "El pedido no existe en la ruta de entrega"], Response::HTTP_NOT_FOUND);
        }
        try {
            foreach ($delivery->codeOrderDeliveryRoute as $codr) {
                $sale = $codr->orderPurchase;
            }

            $sale->sale->delete();
        } catch (Exception $e) {
            return response()->json([
                ([
                    'msg' => 'Error al eliminar esta ruta.', 'data' =>
                    ['error' => $e->getMessage()]
                ])
            ], response::HTTP_NOT_FOUND);
            //throw $th;
        }
    } */

    /*  if (!$user) {
            return response()->json(["message" => "El usuario no existe"], Response::HTTP_NOT_FOUND);
        }
        $user->active = false;
        $user->email = $user->email . "-" . Str::random(5);
        $user->save();
        return response()->json(["usuario" => $user, 'message' => 'Usuario eliminado correctamente']); */

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
        $sale = Sale::where('code_sale', $remision->code_sale)->first();

        $products = $remision->productRemission;
        // Obtener los productos de esa ruta y de ese pedido
        $productsToRoute = ProductDeliveryRoute::join('code_order_delivery_routes', 'code_order_delivery_routes.id', 'product_delivery_routes.code_order_route_id')
            ->where('code_order_delivery_routes.delivery_route_id', $deliveryRoute->id)
            ->where('code_order_delivery_routes.code_sale', $sale->code_sale)
            ->select('product_delivery_routes.*', 'code_order_delivery_routes.code_order')
            ->get();

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

        $orders = [];
        foreach ($products as $product) {
            $order = OrderPurchaseProduct::find($product->order_purchase_product_id)->orderPurchase;
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


        $sale = $sale->toArray();
        $sale['ordersProduct'] = $dataOrders;
        $remision->pedidos =  $sale;

        return response()->json(['msg' =>  'Remision encontrada.', 'data' => ["remision" => $remision]], response::HTTP_OK); //200
    }
}
