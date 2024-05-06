<?php

namespace App\Http\Controllers;

use App\Models\Incidence;
use App\Models\OrderPurchase;
use App\Models\Sale;
use App\Models\SaleStatusChange;
use App\Models\StatusOT;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Vista de tabla de Pedidos
        // crear una var que se llame per_page = 10
        // Vista de tabla de Pedidos
        // crear una var que se llame per_page = 10
        $per_page = 15;
        if ($request->per_page) {
            //Asignarle el valor al var per_page
            $per_page = $request->per_page;
        }

        // Filtros de buscador

        $idPedidos = $request->idPedidos ?? ""; // Sale.code_sale
        /*  $fechaCreacion = $request->fechaCreacion ?? ""; // Pendiente
         $horariodeentrega = $request->horariodeentrega ?? ""; // Pendiente
        $empresa = $request->empresa ?? null; // AdditionalSaleInformation.warehouse_company
        $cliente = $request->cliente ?? null; // additional_sale_information.client_name
        $comercial = $request->comercial ?? ""; // Sale.commercial_name
        $total = $request->total ?? null; // sale.total */


        $sales = Sale::where("sales.code_sale", "LIKE", "%" . $idPedidos . "%")->paginate($per_page);
        return response()->json([
            'msg' => 'Lista de los pedidos', 'data' => ["sales" => $sales]
            // 'ordenes' => $ordenes
        ], response::HTTP_OK); //200
        /*    $sales = null;
        $isSeller =  auth()->user()->whatRoles()->whereIn('name', ['ventas', 'gerente', 'asistente_de_gerente'])->first();
        $isMaquilador = auth()->user()->whatRoles()->whereIn('name', ['maquilador'])->first();
        DB::statement("SET SQL_MODE=''");
        if ($request->ordenes_proximas) {
            $sales =  Sale::with('moreInformation', 'lastStatus', "detailsOrders")->join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
                ->join('order_purchases', 'order_purchases.code_sale', '=', 'sales.code_sale')
                ->when($isSeller !== null, function ($query) {
                    $user =  auth()->user();
                    // $query->where('additional_sale_information.company', $user->company);
                    $query->where('sales.commercial_email', $user->email);
                })->when($isMaquilador !== null, function ($query) {
                    $user =  auth()->user();
                    $query->where('order_purchases.tagger_user_id', $user->id);
                })->where("sales.code_sale", "LIKE", "%" . $idPedidos . "%")
                ->where("additional_sale_information.creation_date", "LIKE", "%" . $fechaCreacion . "%")
                ->where("additional_sale_information.planned_date", "LIKE", "%" . $horariodeentrega . "%")
                ->when($empresa !== null, function ($query) use ($empresa) {
                    $query->where("additional_sale_information.warehouse_company", "LIKE", "%" . $empresa . "%");
                })->when($cliente !== null, function ($query) use ($cliente) {
                    $query->where("additional_sale_information.client_name", "LIKE", "%" . $cliente . "%");
                })->where("sales.commercial_name", "LIKE", "%" . $comercial . "%")
                ->when($total !== null, function ($query) use ($total) {
                    $query->where("sales.total", "LIKE", "%" . $total . "%");
                })->groupBy('sales.id')->orderby('order_purchases.planned_date', 'ASC')->select(
                    'sales.*',
                    "additional_sale_information.client_name as client_name",
                    "additional_sale_information.company as company"
                )->paginate($per_page);
        } else {

            $sales = Sale::with('lastStatus', "detailsOrders", "moreInformation")->join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
                ->join('order_purchases', 'order_purchases.code_sale', '=', 'sales.code_sale')->when($isSeller !== null, function ($query) {
                    $user =  auth()->user();
                    // $query->where('additional_sale_information.company', $user->company);
                    $query->where('sales.commercial_email', $user->email);
                })->when($isMaquilador !== null, function ($query) {
                    $user =  auth()->user();
                    $query->where('order_purchases.tagger_user_id', $user->id);
                })->where("sales.code_sale", "LIKE", "%" . $idPedidos . "%")

                ->where("additional_sale_information.creation_date", "LIKE", "%" . $fechaCreacion . "%")
                ->where("additional_sale_information.planned_date", "LIKE", "%" . $horariodeentrega . "%")
                ->when($empresa !== null, function ($query) use ($empresa) {
                    $query->where("additional_sale_information.warehouse_company", "LIKE", "%" . $empresa . "%");
                })->when($cliente !== null, function ($query) use ($cliente) {
                    $query->where("additional_sale_information.client_name", "LIKE", "%" . $cliente . "%");
                })
                ->where("sales.commercial_name", "LIKE", "%" . $comercial . "%")->when($total !== null, function ($query) use ($total) {
                    $query->where("sales.total", "LIKE", "%" . $total . "%");
                })->groupBy('sales.id')->select(
                    'sales.*',
                    "additional_sale_information.client_name as client_name",
                    "additional_sale_information.company as company"
                )->paginate($per_page);
        }

        // TODO: Pedido 153 muestra mal el status
        foreach ($sales as $sale) {
            if ($sale->lastStatus !== null) {
                $sale->lastStatus->slug = $sale->lastStatus->status->slug;
                $sale->lastStatus->last_status = $sale->lastStatus->status->status;
                //unset($sale->lastStatus->status);
                // unset($sale->lastStatus->id);
                unset($sale->lastStatus->sale_id);
                unset($sale->lastStatus->status_id);
                unset($sale->lastStatus->updated_at);
            }
            $detailsOrdersReindex = $sale->detailsOrders->toArray();
            foreach ($detailsOrdersReindex as $key => $detailOrder) {
                // Revisar si se encuentra la  palabra OT en el codigo de la orden
                if (strpos($detailOrder['code_order'], 'MUE') !== false) {
                    unset($detailsOrdersReindex[$key]);
                }
                if (strpos($detailOrder['code_order'], 'LOG') !== false) {
                    unset($detailsOrdersReindex[$key]);
                }
            }
            unset($sale->detailsOrders);
            $sale->details_orders = array_values($detailsOrdersReindex);
        }

        if ($isMaquilador) {
            // mostrar la cantidad recibida de cada producto en las recepciones del maquilador
            // Array para el quantityTagger
            // $quantityTaggerArray = [];
            foreach ($sales as $sale) {
                foreach ($sale->detailsOrders as $detailOrder) {
                    foreach ($detailOrder->products as $product) {
                        $quantityTagger = $product
                            ->join("reception_products", "order_purchase_products.odoo_product_id", "reception_products.odoo_product_id")
                            ->join("receptions", "reception_products.reception_id", "receptions.id")->where("receptions.maquilador", 1)
                            ->where("order_purchase_products.odoo_product_id", $product->odoo_product_id)
                            ->where("order_purchase_products.order_purchase_id", $detailOrder->id)
                            ->where("receptions.code_order", $detailOrder->code_order)
                            ->sum(
                                "reception_products.done"
                            );
                        // array_push($quantityTaggerArray, [$product,$quantityTagger, $product->odoo_product_id, $detailOrder->id]);
                        $product->quantity_delivered = $quantityTagger;
                        $quantityTagger = 0;
                    }
                }
            }
        }

        return response()->json([
            'msg' => 'Lista de pedidos', 'data' => ["sales" => $sales]
            // 'ordenes' => $ordenes
        ], response::HTTP_OK); //200 */
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show($sale_id)
    {
        // Vista de Detalle de los pedidos
        /*
        # Generales
        # OT, OC relacionadas
        # //TODO: Incidencias Relacionadas, Datos generales
        # //TODO: Inspecciones Relacionadas, Datos generales
        # //TODO: Historial de Cambios BPMS y Odoo
        # //TODO: Entregas Relacionadas, Datos generales
        */
        $sale = Sale::with([
            'moreInformation',
            'lastStatus',
            'saleProducts',
            'detailsOrders',
            'routeDeliveries',
            'inspections',
            'incidences',
            "ordersDeliveryRoute",
            "binnacles"
        ])->where('code_sale', $sale_id)->first();
        //Detalle del pedido seleccionado

        if ($sale) {
            /*  foreach ($sale->routeDeliveries as $routeDelivery) {
                $routeDelivery->deliveryRoute->name_chofer = $routeDelivery->deliveryRoute->user->name;
                unset($routeDelivery->deliveryRoute->user);
            } */

            $sale->lastStatus->slug = $sale->lastStatus->status->slug;
            $sale->lastStatus->last_status = $sale->lastStatus->status->status;
            unset($sale->lastStatus->status);
            unset($sale->lastStatus->id);
            unset($sale->lastStatus->sale_id);
            unset($sale->lastStatus->status_id);
            unset($sale->lastStatus->updated_at);
            foreach ($sale->binnacles as $binnacle) {
                $binnacle->user_name = $binnacle->user->name;
                unset($binnacle->user);
                unset($binnacle->user_id);
            }
            if (auth()->user()->hasRole('maquilador')) {
                $incidendeTagger = [];
                foreach ($sale->incidences as $key => $value) {
                    if ($value->user_id == auth()->user()->id) {
                        array_push($incidendeTagger, $value);
                    }
                }
                unset($sale->incidences);
                $sale->incidences = $incidendeTagger;
            }

            $detailsOrdersReindex = $sale->detailsOrders->toArray();
            foreach ($detailsOrdersReindex as $key => $detailOrder) {
                // Revisar si se encuentra la  palabra OT en el codigo de la orden
                if (strpos($detailOrder['code_order'], 'MUE') !== false) {
                    unset($detailsOrdersReindex[$key]);
                }
                if (strpos($detailOrder['code_order'], 'LOG') !== false) {
                    unset($detailsOrdersReindex[$key]);
                }
            }
            unset($sale->detailsOrders);
            $sale->details_orders = array_values($detailsOrdersReindex);

            return response()->json(['msg' => 'Detalle del pedido', 'data' => ["sale", $sale]], response::HTTP_OK); //200
        }

        return response()->json(['msg' => "No hay informacion acerca de este pedido"], response::HTTP_OK); //200
    }

    //////////////////////////////ENDPOINT DE PRUEBA PARA DETALLES DE LOS PEDIDOS/////
    public function infoSales($sale_id)
    {

        $sale = Sale::where('code_sale', $sale_id)->first();
        $idSale = $sale->id;
            
        $Company = DB::table('additional_sale_information')->where('sale_id', $idSale)->first();
        if ($sale) {
            ////////////////DETALLES DEL PEDIDO//////////////////////
            $InfoAditional = [
                'id' => $sale->id,
                'code_sale' => $sale->code_sale,
                'commercial_email' => $sale->commercial_email,
                'commercial_name' => $sale->commercial_name,
                'Company' => $Company->company,
                'commercial_odoo_id' => $sale->commercial_odoo_id,
                'incidence' => $sale->incidence,
                'name_sale' => $sale->name_sale,
                'status_id' => $sale->status_id,
                'created_at' => $sale->created_at,
                'updated_at' => $sale->updated_at,
            ];

            /////ORDENES////////////////
            $ordenes = DB::table('order_purchases')->where('code_sale', $sale_id)->where(function ($query) {
                $query->where('code_order', 'like', 'OC-%')->orWhere('code_order', 'like', 'OT-%');
            })->get();
            
            $orders = [];
            foreach ($ordenes as $orden) {
                $product = DB::table('order_purchase_products')->where('order_purchase_id', $orden->id)->first();
                $idOrden = $product->order_purchase_id;
                $registros = DB::table('order_confirmations')->where('order_purchase_id', $idOrden)->get();
    
                // Obtener la última fecha de creación de los registros de confirmación
                $ultima_creacion = null;
                foreach ($registros as $registro) {
                    if ($registro->created_at > $ultima_creacion) {
                        $ultima_creacion = $registro->created_at;
                    }
                }
                
                $productos_confirmados = count($registros);
                $productos_totales = DB::table('order_purchase_products')->where('order_purchase_id', $idOrden)->count();
                $estado_confirmacion = ''; 
                if ($registros) {
                    if ($productos_confirmados == 0) {
                        $estado_confirmacion = 'Sin confirmar'; 
                    } elseif ($productos_confirmados == $productos_totales) {
                        $estado_confirmacion = 'Confirmado';
                    } elseif ($productos_confirmados < $productos_totales) {
                        $estado_confirmacion = 'Parcial';   
                    }
                }

                $Orden = [
                    'id' => $orden->id,
                    'code_order' => $orden->code_order,
                    'code_sale' => $orden->code_sale,
                    'provider_name' => $orden->provider_name,
                    'order_date' => $orden->order_date,
                    'planned_date' => $orden->planned_date,
                    'status' => $orden->status,
                    'status_bpm' => $orden->status_bpm,
                    'supplier_representative' => $orden->supplier_representative,
                    'Confirmation' => $estado_confirmacion,
                    'last_confirmation_created_at' => $ultima_creacion, // Aquí agregamos la última fecha de creación
                    'created_at' => $orden->created_at,
                    'updated_at' => $orden->updated_at,
                ];
                $orders[] = $Orden;
            }

            /////////////PRODUCTOS/////////////////
            $idOrdenes = DB::table('order_purchases')->where('code_sale', $sale_id)->where(function ($query) {
                $query->where('code_order', 'like', 'OC-%')->orWhere('code_order', 'like', 'OT-%');
            })->pluck('id');
            $products = [];

            ////VERIFICAMOS QUE LOS PRODUCTOS ESTEN COMPLETADOS/////////////
            foreach ($idOrdenes as $idOrden) {
                $ordenCompra = DB::table('order_purchases')->where('id', $idOrden)->first();
                if ($ordenCompra) {
                    $productosOrden = DB::table('order_purchase_products')->where('order_purchase_id', $idOrden)->get();
                    foreach ($productosOrden as $producto) {
                        $status = 0; 
                        $estado = DB::table('order_confirmations')->where('id_order_products', $producto->id)->first();
                        if ($estado) {
                            $status = $estado->status;
                        }
                        $products[] = [
                            'id' => $producto->id,
                            'status'  => $status, 
                            'code_order' => $ordenCompra->code_order,
                            'provider_name' => $ordenCompra->provider_name,
                            'description' => $producto->description,
                            'odoo_product_id' => $producto->odoo_product_id,
                            'order_purchase_id' => $producto->order_purchase_id,
                            'planned_date' => $producto->planned_date,
                            'product' => $producto->product,
                            'quantity' => $producto->quantity,
                            'quantity_delivered' => $producto->quantity_delivered,
                            'quantity_invoiced' => $producto->quantity_invoiced,
                            'created_at' => $producto->created_at,
                            'updated_at' => $producto->updated_at
                        ];
                    }
                }
            }

            ////////MÁS INFORMACIÓN//////////////////////////
            $idSale = $sale->id;
            $Information = DB::table('additional_sale_information')->where('sale_id', $idSale)->first();
            $MoreInformation = [
                'id'  => $Information->id,
                'sale_id' => $idSale,
                'client_contact' => $Information->client_contact ?? 'Aún no hay un contacto del cliente',
                'client_name'  => $Information->client_name ?? 'Aún no hay un nombre del cliente.',
                'commitment_date'  => $Information->commitment_date ?? 'Aún no hay información.',
                'effective_date'  => $Information->effective_date ?? 'Aún no hay información.',
                'planned_date'  => $Information->planned_date ?? 'Aún no hay una fecha de entrega.',
                'created_at'  => $Information->created_at,
                'updated_at'  => $Information->updated_at
            ];

            //////////////////Last status///////////////////////
            $LastStatus = DB::table('sale_status_changes')->where('sale_id', $idSale)->orderBy('created_at', 'desc')->first();
            $idstatus = $LastStatus->status_id;
            $NombreStatus = DB::table('statuses')->where('id', $idstatus)->first();
            $lastStatus = [
                "created_at" => $LastStatus->created_at,
                "slug" => $NombreStatus->slug,
                "last_status" => $NombreStatus->status,

            ];

            ///////////INCIDENCIAS///////////////
            $incidences = DB::table('incidences')->where('code_sale', $sale_id)->get();

            /////INSPECTIONS////////////////////////
            $inspections = DB::table('inspections')->where('sale_id', $idSale)->get();
            //////////////CHECK-LIST//////////////////////
            $check_list = DB::table('check_lists')->where('code_sale', $sale_id)->get();

            ////////////////INFORMACIÓN DE LOS PRODUCTS SALE//////////////////
            $SalesProducts = DB::table('sales_products')->where('sale_id', $idSale)->get()->toArray();
            $Sale = [];
            foreach ($SalesProducts as $saleProduct) {
                $SaleProducts = [
                    'sale_id' => $sale_id,
                    'odoo_product_id' => $saleProduct->odoo_product_id,
                    'description' => $saleProduct->description,
                    'product' => $saleProduct->product,
                    'quantity_ordered' => $saleProduct->quantity_ordered,
                    'quantity_delivered' => $saleProduct->quantity_delivered,
                    'quantity_invoiced' => $saleProduct->quantity_invoiced,

                ];
                $Sale[] = $SaleProducts;
            }

            //////////////////PRODUCTOS QUE YA ESTAN EN STATUS 1/////////////////////////////////
            $status = DB::table('order_confirmations')->select('order_purchase_id', 'status', 'id', 'id_order_products')->where('code_sale', $sale_id)
                                                    ->groupBy('order_purchase_id', 'status', 'id', 'id_order_products')
                                                    ->get();
                                                    
            $combinedResults = [];

            foreach ($status as $item) {
                $orderPurchaseId = $item->order_purchase_id;
                                                    
                $codeOrder = DB::table('order_purchases')->where('id', $orderPurchaseId)->value('code_order');
        
                if (!isset($combinedResults[$orderPurchaseId])) {
                    $combinedResults[$orderPurchaseId] = [];
                }
                                                    
                // Agrega el elemento actual al arreglo correspondiente al 'order_purchase_id'
                $combinedResults[$orderPurchaseId][] = [
                    'id' => $item->id,
                    'code_order' => $codeOrder,
                    'status' => $item->status,
                    'order_purchase_id' => $item->order_purchase_id,
                    'id_order_products' => $item->id_order_products
                ];
            }  
            
            
            ///////////////STATUS 2////////////
            $NumOrders = [];
            foreach ($ordenes as $Order){
                $idOrder = $Order->id;
                $productos_totales = DB::table('order_purchase_products')->where('order_purchase_id',$idOrder)->count();
                $NumOrders[] = $productos_totales;   
            }
            
            $OrdersFinales = array_sum($NumOrders);
            
            $registros = [];
            foreach ($ordenes as $order){
                $idPurchase = $order->id;
                $ya = DB::table('order_confirmations')->where('order_purchase_id', $idPurchase)->count();
                $registros[] = $ya;
            }
            $ConfirmationOrders = array_sum($registros);
            $statusOrders = '';
            $registros = DB::table('sale_status_changes')->where('sale_id',$idSale)->first();
            $idSaleStatusChange = $registros->id;
            $status = $registros->status;
            if($OrdersFinales != $ConfirmationOrders)
            {
                if(!$registros){
                    SaleStatusChange::create([
                        'sale_id' => $idSale,
                        'status_id' => 15,
                        'status' => 0,
                    ]);
                    $statusOrders = 0;
                }
                $statusOrders = 0;
               
            }elseif ($OrdersFinales == $ConfirmationOrders) {
                if($registros){
                    DB::table('sale_status_changes')->where('id',$idSaleStatusChange)->update([
                        'sale_id' => $idSale,
                        'status_id' => 15,
                        'status' => 1,
                    ]);
                    $statusOrders = 1;
                }elseif($status == 1){
                    $statusOrders = 1;
                }
            }

            return response()->json([
                'additional_information' => $InfoAditional, 'orders'  => $orders, 'products_orders' => $products, 'more_information' => $MoreInformation,
                'last_status' => $lastStatus, 'incidences' => $incidences, 'inspections'  => $inspections, 'sales_products' => $Sale, 'check_list' => $check_list,
                'status' => $combinedResults, 'status_two' => $statusOrders,
            ], 200);
        } else {
            return response()->json(['message' => 'No existe este pedido', 'status' => 404], 404);
        }
    }

    //updateDeliveryAddressCustom
    public function updateDeliveryAddressCustom(Request $request, $sale_id)
    {
        // Actualizar la ruta de entrega
        $validation = Validator::make($request->all(), [
            'delivery_custom_address' => 'required',
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
        $sale = Sale::where('code_sale', $sale_id)->first();
        if ($sale) {
            $sale->delivery_custom_address = $request->delivery_custom_address;
            $sale->save();
            return response()->json(['msg' => 'Ruta de entrega actualizada', 'data' => ["sale", $sale]], response::HTTP_OK); //200
        }
        return response()->json(['msg' => "No hay informacion acerca de este pedido"], response::HTTP_OK); //200
    }

    //Ver pedidos de cada vendedor
    public function viewPedidosPorVendedor()
    {
        $pedidos = auth()->user()->sales;
        return response()->json([
            'msg' => "Vizualizar mis pedidos",
            'data' => ["pedidos" => $pedidos],
        ], Response::HTTP_OK); //200
    }

    public function estadisticas(Request $request)
    {


        $validation = Validator::make($request->all(), [
            'date_end' => 'required|date_format:Y-m-d',
            'date_initial' => 'required|date_format:Y-m-d',
            'company' => '',
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

        $date_end = date($request->date_end);
        $date_initial = date($request->date_initial);
        $company = $request->company;

        $sales = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->where('additional_sale_information.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('additional_sale_information.planned_date', [$date_initial, $date_end])->get()
            ->count();

        $fechaExpiracion = Carbon::parse($date_initial);
        $diasDiferencia = $fechaExpiracion->diffInDays($date_end);

        $salesAnterior = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->where('additional_sale_information.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('additional_sale_information.planned_date', [$fechaExpiracion->subDays($diasDiferencia), Carbon::parse($date_end)->subDays($diasDiferencia)])
            ->count();
        // return [$sales, $salesAnterior];
        $porcentajePedido = 0;
        if ($salesAnterior > 0) {
            $porcentajePedido = round(((($sales - $salesAnterior) / $salesAnterior) * 100), 2);
        }
        $incidencia = Incidence::where('incidences.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('incidences.creation_date', [$date_initial, $date_end])
            ->count();

        $incidenciaAnterior = Incidence::where('incidences.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('incidences.creation_date', [$fechaExpiracion->subDays($diasDiferencia), Carbon::parse($date_end)->subDays($diasDiferencia)])
            ->count();


        $porcentajeIncidencia = 0;
        if ($incidenciaAnterior > 0) {
            $porcentajeIncidencia = round(((($incidencia - $incidenciaAnterior) / $incidenciaAnterior) * 100), 2);
        } else {
            /*   return response()->json(
                [
                    'msg' => "Sin incidencias en el periodo seleccionado",
                ],

            ); */
        }



        $sale = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            ->where('additional_sale_information.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('additional_sale_information.planned_date', [$date_initial, $date_end])
            ->select('additional_sale_information.planned_date')
            ->select(\DB::raw('SUBSTRING_INDEX(additional_sale_information.planned_date, " ", 1) as planned_date'))
            ->get();
        $incidenciaPer = Incidence::where('incidences.company', 'LIKE', '%' . $company . '%')
            ->whereBetween('incidences.creation_date', [$date_initial, $date_end])
            ->select('incidences.creation_date')
            ->select(\DB::raw('SUBSTRING_INDEX(incidences.creation_date, " ", 1) as creation_date'))
            ->get();

        $tiempoInicio = strtotime($date_initial);
        $tiempoFin = strtotime($date_end);
        $dia = 86400;
        $dos_dias = 172800;
        $semana = 604800;
        $mes = 2419200;

        $datos = [];
        while ($tiempoInicio <= $tiempoFin) {

            switch ($diasDiferencia) {

                case ($diasDiferencia <= 7):

                    $fechaActual = date("Y-m-d", $tiempoInicio);
                    $ped = $sale->where('planned_date', $fechaActual)->count();
                    $inc = $incidenciaPer->where('creation_date', $fechaActual)->count();
                    $tiempoInicio += $dia;
                    $datos[] = ['Fecha_dentro_del_periodo' => $fechaActual, 'Pedidos' => $ped, "Incidencias" => $inc];
                    break;
                case ($diasDiferencia > 7 && $diasDiferencia <= 31):
                    $fechaActual = date("Y-m-d", $tiempoInicio);
                    $fechaSiguiente = date("Y-m-d", $tiempoInicio + $dia);
                    $ped = $sale->whereBetween('planned_date', [$fechaActual, $fechaSiguiente])->count();
                    $inc = $incidenciaPer->whereBetween('creation_date', [$fechaActual, $fechaSiguiente])->count();
                    $tiempoInicio += $dos_dias;
                    $datos[] = ['Fecha_dentro_del_periodo' => $fechaActual, 'Pedidos' => $ped, "Incidencias" => $inc];
                    break;
                case ($diasDiferencia > 31 && $diasDiferencia <= 182):

                    $fechaActual = date("Y-m-d", $tiempoInicio);
                    $fechaSiguiente = date("Y-m-d", $tiempoInicio + $semana);
                    $ped = $sale->whereBetween('planned_date', [$fechaActual, $fechaSiguiente])->count();
                    $inc = $incidenciaPer->whereBetween('creation_date', [$fechaActual, $fechaSiguiente])->count();
                    $tiempoInicio += $semana;
                    $datos[] = ['Fecha_dentro_del_periodo' => $fechaActual, 'Pedidos' => $ped, "Incidencias" => $inc];
                    break;
                case ($diasDiferencia > 182 && $diasDiferencia <= 365):
                    $fechaActual = date("Y-m-d", $tiempoInicio);
                    $fechaSiguiente = date("Y-m-d", $tiempoInicio + $mes);
                    $ped = $sale->whereBetween('planned_date', [$fechaActual, $fechaSiguiente])->count();
                    $inc = $incidenciaPer->whereBetween('creation_date', [$fechaActual, $fechaSiguiente])->count();
                    $tiempoInicio += $mes;
                    $datos[] = ['Fecha_dentro_del_periodo' => $fechaActual, 'Pedidos' => $ped, "Incidencias" => $inc];
                    break;


                default:
                    $msg = 'no cumple';
            }
        }

        $pendientes = OrderPurchase::join('status_o_t_s', 'status_o_t_s.id_order_purchases', 'order_purchases.id')
            ->where('order_purchases.code_order', 'LIKE', '%' . 'OT' . '%')
            ->where('order_purchases.company', 'LIKE', '%' . $company . '%')
            ->whereIn('status_o_t_s.status', ["Pendiente", "Retrasado", "En espera de entrega de maquilador"])
            ->whereBetween('status_o_t_s.created_at', [$date_initial, $date_end])
            ->count();

        $completado = OrderPurchase::join('status_o_t_s', 'status_o_t_s.id_order_purchases', 'order_purchases.id')
            ->where('order_purchases.code_order', 'LIKE', '%' . 'OT' . '%')
            ->where('order_purchases.company', 'LIKE', '%' . $company . '%')
            ->whereIn('status_o_t_s.status', ["Listo para recoger", "RIP", "Recepcion inventario Completo"])
            ->whereBetween('status_o_t_s.created_at', [$date_initial, $date_end])
            //->select('order_purchases.status')
            ->count();

        $porcentaje = 0;
        $totalMaquilador = $pendientes + $completado;
        if ($totalMaquilador > 0) {
            $porcentaje = 100 / $totalMaquilador;
        }

        $porcentajePendiente = $porcentaje * $pendientes;
        $porcentajeCompletado = $porcentaje * $completado;
        $total = $pendientes + $completado;

        return [
            "tarjetas" =>  $tarjetas = [
                "pedidos" => $sales, "porcentaje" => round($porcentajePedido, 2) . "%",
                "incidencias" => $incidencia, "porcentaje2" => round($porcentajeIncidencia, 2) . "%"
            ],
            "grafica" => $datos,
            "grafica_de_pastel" => $grafica = [
                "pedidos_pendientes_del_maquilador" => round($porcentajePendiente, 1),
                "pedidos_completados_del_maquilador" => round($porcentajeCompletado, 1),
                "total" => $total
            ],

        ];
    }

    public function calendario(Request $request)
    {

        $isSeller =  auth()->user()->whatRoles()->whereIn('name', ['ventas', 'gerente', 'asistente_de_gerente'])->first();
        $isMaquilador = auth()->user()->whatRoles()->whereIn('name', ['maquilador'])->first();
        $fecha = Sale::join('additional_sale_information', 'additional_sale_information.sale_id', 'sales.id')
            //->orderby('additional_sale_information.planned_date')
            ->where("sales.code_sale", "NOT LIKE", "P-MUE%")
            ->where("sales.code_sale", "NOT LIKE", "MUE%")
            ->where("sales.code_sale", "NOT LIKE", "CONSUM%")
            ->when($isSeller !== null, function ($query) {
                $user =  auth()->user();
                // $query->where('additional_sale_information.company', $user->company);
                $query->where('sales.commercial_email', $user->email);
            })
            ->when($isMaquilador !== null, function ($query) {
                $user =  auth()->user();
                $query->where('order_purchases.tagger_user_id', $user->id);
            })
            ->select(
                \DB::raw('SUBSTRING_INDEX(additional_sale_information.commitment_date, " ", 1) as planned_date'),
                'sales.code_sale'
            )
            ->get();

        return response()->json(['fecha' => $fecha]);
    }
}
