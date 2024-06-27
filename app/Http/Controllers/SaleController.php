<?php

namespace App\Http\Controllers;

use App\Models\CheckList;
use App\Models\DeliveryRoute;
use App\Models\Incidence;
use App\Models\OrderPurchase;
use App\Models\OrdersGroup;
use App\Models\Sale;
use App\Models\SaleStatusChange;
use App\Models\StatusDeliveryRouteChange;
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

        $per_page = 15;
        if ($request->per_page) {
            //Asignarle el valor al var per_page
            $per_page = $request->per_page;
        }

        // Filtros de buscador

        $idPedidos = $request->idPedidos ?? ""; // Sale.code_sale
        $fechaCreacion = $request->fechaCreacion ?? ""; // Pendiente
        /*    $horariodeentrega = $request->horariodeentrega ?? ""; // Pendiente
        $empresa = $request->empresa ?? null; // AdditionalSaleInformation.warehouse_company
        $cliente = $request->cliente ?? null; // additional_sale_information.client_name
        $comercial = $request->comercial ?? ""; // Sale.commercial_name
        $total = $request->total ?? null; // sale.total */


        $sales = Sale::where("sales.code_sale", "LIKE", "%" . $idPedidos . "%")
            ->with('lastStatus')
            ->where("sales.created_at", "LIKE", "%" . $fechaCreacion . "%")
            ->paginate($per_page);
        return response()->json([
            'msg' => 'Lista de los pedidos', 'data' => ["sales" => $sales]
            // 'ordenes' => $ordenes
        ], response::HTTP_OK); //200
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
        $id = $sale->id;
        $Company = DB::table('additional_sale_information')->where('sale_id', $id)->first();
        if ($sale) {
            ////////////////DETALLES DEL PEDIDO//////////////////////
            $InfoAditional = [
                'id' => $sale->id,
                'code_sale' => $sale->code_sale,
                'commercial_email' => $sale->commercial_email,
                'commercial_name' => $sale->commercial_name,
                'Company' => $Company->company ?? 'No hay info',
                'commercial_odoo_id' => $sale->commercial_odoo_id,
                'incidence' => $sale->incidence,
                'name_sale' => $sale->name_sale,
                'status_id' => $sale->status_id,
                'created_at' => $sale->created_at,
                'updated_at' => $sale->updated_at,
            ];
            /////ORDENES////////////////
            $ordenes = DB::table('order_purchases')->where('code_sale', $sale_id)->where(function ($query) {
                $query->where('code_order', 'like', 'OC-%')->orWhere('code_order', 'like', 'OT-%')->orWhere('code_order', 'like', 'OT%')->orWhere('code_order', 'like', 'OC%');
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

                $idsProducts = [];
                if ($estado_confirmacion === 'Parcial') {
                    foreach ($registros as $registro) {
                        $idsProducts[] = $registro->id_order_products;
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
                    'idProductsOrders' => $idsProducts,
                    'last_confirmation_created_at' => $ultima_creacion, // Aquí agregamos la última fecha de creación
                    'created_at' => $orden->created_at,
                    'updated_at' => $orden->updated_at,
                ];
                $orders[] = $Orden;
            }
            /////////////PRODUCTOS/////////////////
            $idOrdenes = DB::table('order_purchases')->where('code_sale', $sale_id)->where(function ($query) {
                $query->where('code_order', 'like', 'OC-%')->orWhere('code_order', 'like', 'OT-%')->orWhere('code_order', 'like', 'OT%')->orWhere('code_order', 'like', 'OC%');
            })->pluck('id', 'code_order');
            $products = [];
            ////VERIFICAMOS QUE LOS PRODUCTOS ESTEN COMPLETADOS/////////////
            foreach ($idOrdenes as $id => $idOrden) {
                $ordenCompra = DB::table('order_purchases')->where('id', $idOrden)->first();
                if ($ordenCompra) {
                    $productosOrden = DB::table('order_purchase_products')->where('order_purchase_id', $idOrden)->get();
                    foreach ($productosOrden as $producto) {
                        $status = 0;
                        $statusChanges = DB::table('order_confirmations')->where('id_order_products', $producto->id)->first();
                        if ($statusChanges) {
                            $status = $statusChanges->status;
                        }

                        $statusesDelivery = StatusDeliveryRouteChange::where('order_purchase_product_id', $producto->id)->get();

                        // Crear un nuevo array que contenga datos de ambas tablas
                        $product = [
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
                            'updated_at' => $producto->updated_at,
                            'status_product' => collect()
                        ];

                        // Agregar los datos de StatusDeliveryRouteChange al nuevo array
                        foreach ($statusesDelivery as $statusDelivery) {
                            $product['status_product']->push([
                                'id' => $statusDelivery->id,
                                'order_purchase_product_id' => $producto->id,
                                'code_order' => $ordenCompra->code_order,
                                'status' => $statusDelivery->status,
                                'visible' => $statusDelivery->visible,
                            ]);
                        }

                        // Agregar el producto al arreglo de productos
                        $products[] = $product;
                    }
                }
            }
            ////////////////////////////////////////COMFIRMACION DE LAS RUTAS/////////////////////////////////////
            $ConfirmationOrder = [];
            //dd($idOrdenes);
            foreach ($idOrdenes as $code_order => $idOrden) {
                $ConfirmationRoute = DB::table('order_purchase_products')->where('order_purchase_id', $idOrden)->get();
                //dd($ConfirmationRoute);
                foreach ($ConfirmationRoute as $Confirma) {
                    //dd($Confirma);
                    $DatosConfirmate = DB::table('confirm_routes')->where('id_product_order', $Confirma->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(1)
                        ->get();

                    $Insp = DB::table('inspection_products')->where('id_order_purchase_products', $Confirma->id)->get();
                    $inspectionsInfo = [];
                    foreach ($Insp as $Ins) {
                        $idInspeccion = DB::table('inspections')->where('id', $Ins->inspection_id)->first();
                        $code = $idInspeccion->code_inspection;
                        $inspectionsInfo[] = [
                            'inspection_id' => $Ins->inspection_id,
                            'created_at' => $Ins->created_at,
                            'code_inspection' => $code,
                        ];
                    }
                    $Deliverys = DB::table('confirm_deliveries')->where('id_order_purchase_product', $Confirma->id)->get();

                    foreach ($DatosConfirmate as $confirmados) {
                        $ProductsCounts = DB::table('confirm_product_counts')->where('id_product', $Confirma->id)->exists();
                        $HistoryProductsCounts = 0;
                        if ($ProductsCounts) {
                            $HistoryProductsCounts = 1;
                        }
                        if ($confirmados) {
                            $deliveryProducts = [];
                            foreach ($Deliverys as $Delivery) {
                                $deliveryProducts[] = [
                                    'id_order_purchase_product' => $Delivery->id_order_purchase_product,
                                    'delivery_type' => $Delivery->delivery_type,
                                    'created_at' => $Delivery->created_at
                                ];
                            }

                            $info = [
                                'reference' => $code_order,
                                'id_product' => $Confirma->id,
                                'description' => $Confirma->description,
                                'Products_Counts_History' => $HistoryProductsCounts,
                                'Inspections' => $inspectionsInfo,
                                'Delivery' => $deliveryProducts
                            ];
                            $ConfirmationOrder[] = $info;
                        }
                    }
                }
            }

            /////////////////////////MANDA LA INFORMACION EN EL ARREGLO DE PRODUCTS////////////
            foreach ($products as &$product) {
                foreach ($ConfirmationOrder as $confirmation) {
                    if ($product['id'] === $confirmation['id_product']) {
                        $product['ConfirmationOrder'] = [
                            'reference' => $confirmation['reference'],
                            'description' => $confirmation['description'],
                            'Products_Counts_History' => $confirmation['Products_Counts_History'],
                            'Inspections' => $confirmation['Inspections'],
                            'Delivery' => $confirmation['Delivery']
                        ];
                    }
                }
            }
            ////////MÁS INFORMACIÓN//////////////////////////
            $idSale = $sale->id;
            $Information = DB::table('additional_sale_information')->where('sale_id', $idSale)->first();
            if (!$Information) {
                $MoreInformation = [
                    'message' => 'aun no hay información'
                ];
            } else {
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
            }
            //////////////////Last status///////////////////////
            $LastStatus = DB::table('sale_status_changes')->where('sale_id', $idSale)->orderBy('created_at', 'desc')->first();
            if ($LastStatus) {
                $idstatus = $LastStatus->status_id;
                $NombreStatus = DB::table('statuses')->where('id', $idstatus)->first();
                $lastStatus = [
                    "created_at" => $LastStatus->created_at,
                    "slug" => $NombreStatus->slug,
                    "last_status" => $NombreStatus->status,
                ];
            } else {
                $LastStatus = 0;
            }
            /////////////ORDENES_AGRUPADAS////////////////
            $orders_groups = DB::table('orders_groups')->where('code_sale', $sale_id)->get();
            $new_orders = [];
            foreach ($orders_groups as $group) {
                $statusesDelivery = StatusDeliveryRouteChange::where('order_purchase_product_id', $group->product_id_oc)->get();
                $ConfirmProductsCounts = DB::table('confirm_routes')->where("id_product_order", $group->product_id_oc)->exists();
                $statusproduct_id_oc = '';
                if (!$ConfirmProductsCounts) {
                    $statusproduct_id_oc = 0;
                } else {
                    $statusproduct_id_oc = 1;
                }
                $ProductsCounts = DB::table('confirm_product_counts')->where('id_product', $group->product_id_oc)->exists();
                $HistoryProductsCounts = 0;
                if ($ProductsCounts) {
                    $HistoryProductsCounts = 1;
                }
                $Insp = DB::table('inspection_products')->where('id_order_purchase_products', $group->product_id_oc)->get();
                $inspectionsInfo = [];
                foreach ($Insp as $Ins) {
                    $idInspeccion = DB::table('inspections')->where('id', $Ins->inspection_id)->first();
                    $code = $idInspeccion->code_inspection;
                    $inspectionsInfo[] = [
                        'inspection_id' => $Ins->inspection_id,
                        'created_at' => $Ins->created_at,
                        'code_inspection' => $code,
                    ];
                }
                ///////////////////////////////////// rutas
                // $devs = DB::table('delivery_routes')->where('code_sale', $sale_id)->where('product_id', $group->product_id_oc)->whereIn('type_of_destiny', ['Almacen PL','Almacen PM'])->where('status_delivery', ['Completo'])->get();
                $devs = DB::table('delivery_routes')->where('code_sale', $sale_id)->where('product_id', $group->product_id_oc)->where('status_delivery', ['Completo'])->get();

                $devInfo = [];
                foreach ($devs as $dev) {
                    $devInfo[] = [
                        'code_sale' => $dev->code_sale,
                        'code_order' => $dev->code_order,
                        'product_id' => $dev->product_id,
                        'type' => $dev->type,
                        'type_of_destiny' => $dev->type_of_destiny,
                        'date_of_delivery' => $dev->date_of_delivery,
                        'status_delivery' => $dev->status_delivery,
                        'shipping_type' => $dev->shipping_type,
                        'color' => $dev->color,
                        'visible' => $dev->visible,
                    ];
                }
                //////////////////////////////7
                $confirms = DB::table('confirm_routes')->where('id_product_order', $group->product_id_oc)->get();
                $confirmsInfo = [];
                foreach ($confirms as $confirm) {
                    $confirmsInfo[] = [
                        'id_product_order' => $confirm->id_product_order,
                        'id_delivery_routes' => $confirm->id_delivery_routes,
                        'reception_type' => $confirm->reception_type,
                        'destination' => $confirm->destination,
                    ];
                }
                $group_new = [
                    'code_order_oc' => $group->code_order_oc,
                    'code_order_ot' => $group->code_order_ot,
                    'code_sale' => $group->code_sale,
                    'description' => $group->description,
                    'product_id_oc' => $group->product_id_oc,
                    'product_id_ot' => $group->product_id_ot,
                    'planned_date' => $group->planned_date,
                    'reception_oc' => $statusproduct_id_oc,
                    'Products_Counts_History' => $HistoryProductsCounts,
                    'Inspection' => $inspectionsInfo,
                    'deliverys_route' => $devInfo,
                    'confirm_routes' => $confirmsInfo,
                    'status_orders' => collect()
                ];
                foreach ($statusesDelivery as $statusDelivery) {
                    $group_new['status_orders']->push([
                        'id' => $statusDelivery->id,
                        'order_purchase_product_id' => $group->product_id_oc,
                        'code_order' => $group->code_order_oc,
                        'status' => $statusDelivery->status,
                        'visible' => $statusDelivery->visible,
                    ]);
                }
                $new_orders[] = $group_new;
            }
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
                    'logo' => $saleProduct->logo ?? 'No hay logo',
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
            //  return $orders;
            /////////////check list/////////
            $conceptos = ['OC', 'Virtual', 'Logo', 'AI', 'Cotización proveedor', 'Distribución', 'Dirección de entrega', 'Contacto', 'Datos de facturación'];
            $ultimosChecklistsDes = CheckList::where('code_sale', $sale->code_sale)
                ->whereIn('description', $conceptos)
                // ->whereIn('status_checklist', ['Listo', 'No aplica'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->unique('description');

            $ultimosChecklists = $ultimosChecklistsDes->whereIn('status_checklist', ['Listo', 'No aplica']);
            // return $ultimosChecklists;
            $historys_actuales = count($ultimosChecklists);
            // return $historys_actuales;
            $orderConfirmado = [];
            $orderPendient = [];
            foreach ($orders as $orderconf) {
                if ($orderconf['Confirmation'] == 'Confirmado') {
                    $orderConfirmado[] = $orderconf['Confirmation'] = 'Confirmado';
                } else if ($orderconf['Confirmation'] == 'Parcial') {

                    $orderPendient[] = $orderconf['Confirmation'] = 'Parcial';
                }
            }

            $orders_confirmations = DB::table('order_confirmations')->where('code_sale', $sale_id)->get();
            if (empty($orders_confirmations)) {
                return response()->json(['no hay ordenes confirmadas']);
            }
            $NumOrders = [];
            foreach ($ordenes as $Order) {
                //return $ordenes;
                $idOrder = $Order->id;
                $productos_totales = DB::table('order_purchase_products')->where('order_purchase_id', $idOrder)->count();
                $NumOrders[] = $productos_totales;
            }

            $newOrderCom = [];
            $newOrderPen = [];
            foreach ($orders_confirmations as $order_confirmation) {

                if ($order_confirmation->description == 'COMPLETADO') {
                    $newOrderCom[] = $orders_confirmations->where('description', 'COMPLETADO')->count();
                } else if ($order_confirmation->description == 'PARCIAL') {
                    $newOrderPen[] = $orders_confirmations->where('description', 'PARCIAL')->count();
                }
            }
            $orderconfirmationCom = array_sum($newOrderCom);
            $orderconfirmationPen = array_sum($newOrderPen);
            $orders_products = array_sum($NumOrders);
            if ($orders_products == 0) {
                DB::table('sale_status_changes')->where('status_id', 15)->update([
                    'sale_id' => $idSale,
                    'status_id' => 15,
                    'status' => 0,
                    'visible' => 2
                ]);
            }
            $status_order = SaleStatusChange::where('sale_id', $idSale)->where('status_id', 15)->first();
            $status_order_ped = SaleStatusChange::where('sale_id', $idSale)->first();
            $status_sales = DB::table('statuses')->where('id', 15)->first();
            if ($status_order) {
                // $orderConfirmado
                // $orderPendient
                if (count($orderConfirmado) ===  0  && count($orderPendient) === 0 && $historys_actuales === 0) {
                    //return 1;
                    DB::table('sale_status_changes')->where('status_id', 15)->update([
                        'sale_id' => $idSale,
                        'status_id' => 15,
                        'status' => 0,
                        'visible' => 2,
                    ]);
                } else if (count($orderConfirmado) < count($ordenes) && count($orderConfirmado) != 0) {
                    //return 2;
                    //return [count($orderConfirmado) , count($ordenes) ];
                    DB::table('sale_status_changes')->where('status_id', 15)->update([
                        'sale_id' => $idSale,
                        'status_id' => 15,
                        'status' => 0,
                        'visible' => 0
                    ]);
                } else if (count($orderPendient) < count($ordenes) && count($orderPendient) != 0) {
                    //return 3;
                    DB::table('sale_status_changes')->where('status_id', 15)->update([
                        'sale_id' => $idSale,
                        'status_id' => 15,
                        'status' => 0,
                        'visible' => 0
                    ]);
                } else if ($historys_actuales < 9 && $historys_actuales != 0) {
                    //return 4;
                    DB::table('sale_status_changes')->where('status_id', 15)->update([
                        'sale_id' => $idSale,
                        'status_id' => 15,
                        'status' => 0,
                        'visible' => 0
                    ]);
                } else {
                    //return 12;

                    DB::table('sale_status_changes')->where('status_id', 15)->update([
                        'sale_id' => $idSale,
                        'status_id' => 15,
                        'status' => 0,
                        'visible' => 1,
                        'status_name' => $status_sales->status,
                        'slug' => $status_sales->slug
                    ]);
                }
            } else {

                SaleStatusChange::create([
                    'sale_id' => $idSale,
                    'status_id' => 15,
                    'status' => 0,
                    'visible' => 2,
                    'status_name' => $status_sales->status,
                    'slug' => $status_sales->slug
                ]);
            }
            $statusOrders = SaleStatusChange::where('sale_id', $idSale)->get();

            ///////////////////////////// STATUS 3//////////////////////////////////////////////77
            $ordenes_pedidos = DB::table('order_purchases')->where('code_sale', $sale_id)->where(function ($query) {
                $query->where('code_order', 'like', 'OC-%')->orWhere('code_order', 'like', 'OC%');
            })->get();
            $new_orders3 = [];
            $total_productos = [];
            foreach ($ordenes_pedidos as $orden_pedido) {
                $ordenes_productos = DB::table('order_purchase_products')->where('order_purchase_id', $orden_pedido->id)->get();
                $new_orders3[] = $ordenes_productos;
            }
            foreach ($new_orders3 as $orden_productos) {
                foreach ($orden_productos as $producto) {
                    $total_productos[] = $producto->order_purchase_id;
                }
            }
            $total_product_orders = count($total_productos);
            $status_order_new = SaleStatusChange::where('sale_id', $idSale)->where('status_id', 16)->first();
            $status_sales = DB::table('statuses')->where('id', 16)->first();
            /////////////////////agregar estado 3 o amarillo ////////////////
            $subQuery = DB::table('status_delivery_route_changes')
                ->select('status_delivery_route_changes.id')
                ->distinct()
                ->join('delivery_routes', 'delivery_routes.product_id', '=', 'status_delivery_route_changes.order_purchase_product_id')
                ->where('status_delivery_route_changes.status', '=', 'Almacen PL')
                ->where('delivery_routes.code_sale', $sale_id);

            // Consulta principal utilizando la subconsulta
            $rutas_status3 = DB::table('status_delivery_route_changes')
                ->joinSub($subQuery, 'unique_changes', function ($join) {
                    $join->on('status_delivery_route_changes.id', '=', 'unique_changes.id');
                })
                ->get();
            // return $rutas_status3;
            // $rutas_status3 = DeliveryRoute::where('code_sale', $sale_id)->where('type_of_destiny', 'Almacen PL')->get();
            $orders3vis2 = [];
            $orders3vis1 = [];
            $orders3vis3 = [];
            $count_rutas_status3 = count($rutas_status3);
            $rutasCom = [];
            foreach ($rutas_status3 as $ruta_status3) {

                if ($ruta_status3->visible == 2) {
                    $orders3vis2[] = $ruta_status3->visible;
                } elseif ($ruta_status3->visible == 1) {
                    $orders3vis1[] = $ruta_status3->visible;
                } elseif ($ruta_status3->visible == 0) {
                    $orders3vis3[] = $ruta_status3->visible;
                }
            }


            $ordervisible2num3 = count($orders3vis2);
            $ordervisible1num3 = count($orders3vis1);
            $ordervisible0num3 = count($orders3vis3);
            $suma1 = $ordervisible2num3 + $ordervisible1num3;
            if (empty($status_order_new)) {
                if ($suma1 == $total_product_orders && $ordervisible1num3 !== $total_product_orders && $ordervisible0num3 !== $total_product_orders && $ordervisible2num3 !== $total_product_orders) {
                    if ($suma1 == $total_product_orders) {
                        SaleStatusChange::create([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 3,
                            'status_name' => $status_sales->status,
                            'slug' => $status_sales->slug
                        ]);
                    } else if ($ordervisible1num3 == $total_product_orders) {
                        SaleStatusChange::create([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 1,
                            'status_name' => $status_sales->status,
                            'slug' => $status_sales->slug
                        ]);
                    } else if ($ordervisible0num3 > 0 || $ordervisible1num3 > 0 && $ordervisible0num3 > 0 || $ordervisible1num3 > 0 && $ordervisible1num3 < $total_product_orders) {
                        SaleStatusChange::create([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 0,
                            'status_name' => $status_sales->status,
                            'slug' => $status_sales->slug
                        ]);
                    } else if (empty($rutas_totales)) {
                        SaleStatusChange::create([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 2,
                            'status_name' => $status_sales->status,
                            'slug' => $status_sales->slug
                        ]);
                    }
                } else {
                    if ($ordervisible1num3 == $total_product_orders) {
                        SaleStatusChange::create([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 1,
                            'status_name' => $status_sales->status,
                            'slug' => $status_sales->slug
                        ]);
                    } else if ($ordervisible0num3 > 0 || $ordervisible1num3 > 0 && $ordervisible0num3 > 0 || $ordervisible1num3 > 0 && $ordervisible1num3 < $total_product_orders) {
                        SaleStatusChange::create([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 0,
                            'status_name' => $status_sales->status,
                            'slug' => $status_sales->slug
                        ]);
                    } else if (empty($rutas_totales)) {
                        SaleStatusChange::create([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 2,
                            'status_name' => $status_sales->status,
                            'slug' => $status_sales->slug
                        ]);
                    }
                }
            } else {
                if ($count_rutas_status3 == $total_product_orders) {
                    if ($suma1 == $total_product_orders && $ordervisible1num3 !== $total_product_orders && $ordervisible0num3 !== $total_product_orders && $ordervisible2num3 !== $total_product_orders) {
                        DB::table('sale_status_changes')->where('status_id', 16)->update([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 3
                        ]);
                    } else if ($ordervisible1num3 == $total_product_orders) {
                        DB::table('sale_status_changes')->where('status_id', 16)->update([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 1
                        ]);
                    } else if ($ordervisible0num3 > 0 || $ordervisible1num3 > 0 && $ordervisible0num3 > 0 || $ordervisible1num3 > 0 && $ordervisible1num3 < $total_product_orders) {
                        DB::table('sale_status_changes')->where('status_id', 16)->update([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 0
                        ]);
                    } else if (empty($rutas_totales)) {
                        DB::table('sale_status_changes')->where('status_id', 16)->update([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 2
                        ]);
                    }
                } else {
                    if ($ordervisible1num3 == $total_product_orders) {
                        DB::table('sale_status_changes')->where('status_id', 16)->update([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 1
                        ]);
                    } else if ($ordervisible0num3 > 0 || $ordervisible1num3 > 0 && $ordervisible0num3 > 0 || $ordervisible1num3 > 0 && $ordervisible1num3 < $total_product_orders) {
                        DB::table('sale_status_changes')->where('status_id', 16)->update([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 0
                        ]);
                    } else if (empty($rutas_totales)) {
                        DB::table('sale_status_changes')->where('status_id', 16)->update([
                            'sale_id' => $idSale,
                            'status_id' => 16,
                            'status' => 0,
                            'visible' => 2
                        ]);
                    }
                }
            }
            //////////////////////////// STATUS 4//////////////////////////////////////////

            $status_order4 = SaleStatusChange::where('sale_id', $idSale)
                ->where('status_id', 17)
                ->first();

            $status_sales4 = DB::table('statuses')->where('id', 17)->first();

            $subQuery = DB::table('status_delivery_route_changes')
                ->select('status_delivery_route_changes.id')
                ->distinct()
                ->join('delivery_routes', 'delivery_routes.product_id', '=', 'status_delivery_route_changes.order_purchase_product_id')
                ->where('status_delivery_route_changes.status', '=', 'Maquila')
                ->where('delivery_routes.code_sale', $sale_id);

            $rutas_status4 = DB::table('status_delivery_route_changes')
                ->joinSub($subQuery, 'unique_changes', function ($join) {
                    $join->on('status_delivery_route_changes.id', '=', 'unique_changes.id');
                })->get();
            //return $rutas_status4;
            $count_rutas_status4 = count($rutas_status4);

            $orders4vis = [
                2 => 0,
                1 => 0,
                0 => 0
            ];

            foreach ($rutas_status4 as $ruta_status4) {
                if (isset($orders4vis[$ruta_status4->visible])) {
                    $orders4vis[$ruta_status4->visible]++;
                }
            }

            $ordervisible2num4 = $orders4vis[2];
            $ordervisible1num4 = $orders4vis[1];
            $ordervisible0num4 = $orders4vis[0];
            $suma2 = $ordervisible2num4 + $ordervisible1num4;

            $dataToUpdate = [
                'sale_id' => $idSale,
                'status_id' => 17,
                'status' => 0,
                'status_name' => $status_sales4->status,
                'slug' => $status_sales4->slug
            ];

            if (empty($status_order4)) {
                if ($suma2 == $total_product_orders && $ordervisible1num4 != $total_product_orders && $ordervisible0num4 != $total_product_orders && $ordervisible2num4 != $total_product_orders) {
                    return 12;
                    $dataToUpdate['visible'] = 3;
                } else if ($ordervisible1num4 == $total_product_orders) {
                    $dataToUpdate['visible'] = 1;
                } else if ($ordervisible0num4 > 0 || ($ordervisible1num4 > 0 && $ordervisible1num4 < $total_product_orders)) {
                    $dataToUpdate['visible'] = 0;
                } else if (empty($rutas_totales)) {
                    $dataToUpdate['visible'] = 2;
                }

                SaleStatusChange::create($dataToUpdate);
            } else {
                if ($count_rutas_status4 == $total_product_orders) {

                    if ($suma2 == $total_product_orders && $ordervisible1num4 != $total_product_orders && $ordervisible0num4 != $total_product_orders && $ordervisible2num4 != $total_product_orders) {
                        $dataToUpdate['visible'] = 3;
                    } else if ($ordervisible1num4 == $total_product_orders) {
                        $dataToUpdate['visible'] = 1;
                    } else if ($ordervisible0num4 > 0 || ($ordervisible1num4 > 0 && $ordervisible1num4 < $total_product_orders)) {
                        $dataToUpdate['visible'] = 0;
                    } else if (empty($rutas_totales)) {
                        $dataToUpdate['visible'] = 2;
                    }

                    DB::table('sale_status_changes')
                        ->where('status_id', 17)
                        ->update($dataToUpdate);
                } else {
                    if ($suma2 == $total_product_orders && $ordervisible1num4 != $total_product_orders && $ordervisible0num4 != $total_product_orders && $ordervisible2num4 != $total_product_orders) {
                        $dataToUpdate['visible'] = 3;
                    } else if ($ordervisible1num4 == $total_product_orders) {
                        $dataToUpdate['visible'] = 1;
                    } else if ($ordervisible0num4 > 0 || ($ordervisible1num4 > 0 && $ordervisible1num4 < $total_product_orders)) {
                        $dataToUpdate['visible'] = 0;
                    } else if (empty($rutas_totales)) {
                        $dataToUpdate['visible'] = 2;
                    }

                    DB::table('sale_status_changes')
                        ->where('status_id', 17)
                        ->update($dataToUpdate);
                }
            }
            ///////////////////////////// STATUS 5.1////////////////////////////
            $status_order5 = SaleStatusChange::where('sale_id', $idSale)
                ->where('status_id', 33)
                ->first();

            $status_sales5 = DB::table('statuses')->where('id', 33)->first();

            $subQuery = DB::table('status_delivery_route_changes')
                ->select('status_delivery_route_changes.id')
                ->distinct()
                ->join('delivery_routes', 'delivery_routes.product_id', '=', 'status_delivery_route_changes.order_purchase_product_id')
                ->where('status_delivery_route_changes.status', '=', 'Almacen PM')
                ->where('delivery_routes.code_sale', $sale_id);

            $rutas_status5 = DB::table('status_delivery_route_changes')
                ->joinSub($subQuery, 'unique_changes', function ($join) {
                    $join->on('status_delivery_route_changes.id', '=', 'unique_changes.id');
                })->get();
            //return $rutas_status4;
            $count_rutas_status5 = count($rutas_status5);

            $orders5vis = [
                2 => 0,
                1 => 0,
                0 => 0
            ];

            foreach ($rutas_status5 as $ruta_status5) {
                if (isset($orders5vis[$ruta_status5->visible])) {
                    $orders5vis[$ruta_status5->visible]++;
                }
            }

            $ordervisible2num5 = $orders5vis[2];
            $ordervisible1num5 = $orders5vis[1];
            $ordervisible0num5 = $orders5vis[0];
            $suma5 = $ordervisible2num5 + $ordervisible1num5;

            $dataToUpdate = [
                'sale_id' => $idSale,
                'status_id' => 33,
                'status' => 0,
                'status_name' => $status_sales5->status,
                'slug' => $status_sales5->slug
            ];

            if (empty($status_order5)) {
                if ($suma5 == $total_product_orders && $ordervisible1num5 != $total_product_orders && $ordervisible0num5 != $total_product_orders && $ordervisible2num5 != $total_product_orders) {
                    $dataToUpdate['visible'] = 3;
                } else if ($ordervisible1num5 == $total_product_orders) {
                    $dataToUpdate['visible'] = 1;
                } else if ($ordervisible0num5 > 0 || ($ordervisible1num5 > 0 && $ordervisible1num5 < $total_product_orders)) {
                    $dataToUpdate['visible'] = 0;
                } else if (empty($rutas_totales)) {
                    $dataToUpdate['visible'] = 2;
                }

                SaleStatusChange::create($dataToUpdate);
            } else {
                if ($count_rutas_status5 == $total_product_orders) {

                    if ($suma5 == $total_product_orders && $ordervisible1num5 != $total_product_orders && $ordervisible0num5 != $total_product_orders && $ordervisible2num5 != $total_product_orders) {
                        $dataToUpdate['visible'] = 3;
                    } else if ($ordervisible1num5 == $total_product_orders) {
                        $dataToUpdate['visible'] = 1;
                    } else if ($ordervisible0num5 > 0 || ($ordervisible1num5 > 0 && $ordervisible1num5 < $total_product_orders)) {
                        $dataToUpdate['visible'] = 0;
                    } else if (empty($rutas_totales)) {
                        $dataToUpdate['visible'] = 2;
                    }

                    DB::table('sale_status_changes')
                        ->where('status_id', 33)
                        ->update($dataToUpdate);
                } else {
                    if ($suma5 == $total_product_orders && $ordervisible1num5 != $total_product_orders && $ordervisible0num5 != $total_product_orders && $ordervisible2num5 != $total_product_orders) {
                        $dataToUpdate['visible'] = 3;
                    } else if ($ordervisible1num5 == $total_product_orders) {
                        $dataToUpdate['visible'] = 1;
                    } else if ($ordervisible0num5 > 0 || ($ordervisible1num5 > 0 && $ordervisible1num5 < $total_product_orders)) {
                        $dataToUpdate['visible'] = 0; 
                    } else if (empty($rutas_totales)) {
                        $dataToUpdate['visible'] = 2;
                    }

                    DB::table('sale_status_changes')
                        ->where('status_id', 33)
                        ->update($dataToUpdate);
                }
            }
            ///////////////// STATUS 5/////////////////////////
            $sale_recepcions = SaleStatusChange::where('sale_id', $idSale)
                ->where('status_id', 34)
                ->first();
            $sale_rutas = SaleStatusChange::where('sale_id', $idSale)
                ->where('status_id', 33)
                ->first();
            $statuses_recep = DB::table('statuses')->where('id', 18)->first();
            $sale_new_recep = SaleStatusChange::where('sale_id', $idSale)
                ->where('status_id', 18)
                ->first();
            if (empty($sale_new_recep)) {
                if ($sale_recepcions->visible == 1 && $sale_rutas->visible == 1) {
                    SaleStatusChange::create([
                        'sale_id' => $idSale,
                        'status_id' => 18,
                        'status' => 0,
                        'visible' => 1,
                        'status_name' => $statuses_recep->status,
                        'slug' => $statuses_recep->slug
                    ]);
                } elseif ($sale_recepcions->visible == 1 && $sale_rutas->visible == 0 || $sale_recepcions->visible == 0 && $sale_rutas->visible == 3 || $sale_recepcions->visible == 0 && $sale_rutas->visible == 0) {
                    SaleStatusChange::create([
                        'sale_id' => $idSale,
                        'status_id' => 18,
                        'status' => 0,
                        'visible' => 0,
                        'status_name' => $status_sales->status,
                        'slug' => $status_sales->slug
                    ]);
                } else if ($sale_recepcions->visible == 1 && $sale_rutas->visible == 3) {
                    SaleStatusChange::create([
                        'sale_id' => $idSale,
                        'status_id' => 18,
                        'status' => 0,
                        'visible' => 3,
                        'status_name' => $status_sales->status,
                        'slug' => $status_sales->slug
                    ]);
                } else {
                    SaleStatusChange::create([
                        'sale_id' => $idSale,
                        'status_id' => 18,
                        'status' => 0,
                        'visible' => 0,
                        'status_name' => $status_sales->status,
                        'slug' => $status_sales->slug
                    ]);
                }
            } else {
                if ($sale_recepcions->visible == 1 && $sale_rutas->visible == 1) {
                    DB::table('sale_status_changes')->where('status_id', 18)->update([
                        'sale_id' => $idSale,
                        'status_id' => 18,
                        'status' => 0,
                        'visible' => 1
                    ]);
                } elseif ($sale_recepcions->visible == 1 && $sale_rutas->visible == 0 || $sale_recepcions->visible == 0 && $sale_rutas->visible == 3 || $sale_recepcions->visible == 0 && $sale_rutas->visible == 0) {

                    DB::table('sale_status_changes')->where('status_id', 18)->update([
                        'sale_id' => $idSale,
                        'status_id' => 18,
                        'status' => 0,
                        'visible' => 0
                    ]);
                } else if ($sale_recepcions->visible == 1 && $sale_rutas->visible == 3) {

                    DB::table('sale_status_changes')->where('status_id', 18)->update([
                        'sale_id' => $idSale,
                        'status_id' => 18,
                        'status' => 0,
                        'visible' => 3
                    ]);
                }
            }
            ////////////////////////////////////////////////////////////////
            $statusOrders = SaleStatusChange::where('sale_id', $idSale)->get();
            return response()->json([
                'additional_information' => $InfoAditional, 'orders'  => $orders, 'products_orders' => $products, 'more_information' => $MoreInformation,
                'last_status' => $lastStatus, 'incidences' => $incidences, 'inspections'  => $inspections, 'sales_products' => $Sale, 'check_list' => $check_list,
                'status' => $combinedResults, 'status_sale' => $statusOrders, 'HistoryConfirmationOrder' => $ConfirmationOrder, 'orders_groups' => $new_orders
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
