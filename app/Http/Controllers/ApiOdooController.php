<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Incidence;
use App\Models\OrderPurchase;
use App\Models\Reception;
use App\Models\Sale;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiOdooController extends Controller
{
    public function setSale(Request $request)
    {
        try {
            if ($request->header('token') == 'YA8FHVMEWISONRUBVVMEW') {
                $validator = Validator::make($request->all(), [
                    'sale' => 'bail|required|array',
                    'sale.code_sale' => 'required',
                    'sale.name_sale' => 'required',
                    'sale.sequence' => 'required',
                    'sale.invoice_address' => 'required',
                    'sale.delivery_address' => 'required',
                    'sale.delivery_time' => 'required|date:Y-m-d h:i:s',
                    'sale.delivery_instructions' => 'required',
                    'sale.order_date' => 'required|date:d-m-Y h:i:s',
                    'sale.incidence' => 'required',
                    'sale.sample_required' => 'required',
                    'sale.labeling' => 'required',
                    'sale.additional_information' => 'required',
                    'sale.tariff' => 'required',
                    'sale.commercial.odoo_id' => 'required',
                    'sale.commercial.name' => 'required',
                    'sale.commercial.email' => 'required',
                    'sale.client.name' => 'required',
                    'sale.client.contact' => 'required',
                    'sale.other_information.delivery_policy' => 'required',
                    'sale.other_information.schedule_change' => 'required',
                    'sale.other_information.reason_for_change' => 'required',
                    'sale.other_information.warehouse_company' => 'required',
                    'sale.other_information.warehouse_address' => 'required',
                    'sale.other_information.planned_date' => 'required|date:d-m-Y h:i:s',
                    'sale.other_information.commitment_date' => 'required|date:d-m-Y h:i:s',
                    'sale.other_information.effective_date' => 'required|date:d-m-Y h:i:s',
                    'sale.products' => 'bail|required|array',
                    'sale.products.*.odoo_product_id' => 'required',
                    'sale.products.*.product' => 'required',
                    'sale.products.*.description' => 'required',
                    'sale.products.*.provider' => 'required',
                    'sale.products.*.logo' => 'required',
                    'sale.products.*.key_product' => 'required',
                    'sale.products.*.type_sale' => 'required',
                    'sale.products.*.cost_labeling' => 'required|numeric',
                    'sale.products.*.clean_product_cost' => 'required|numeric',
                    'sale.products.*.quantity' => 'required|numeric',
                    'sale.products.*.quantity_delivered' => 'required|numeric',
                    'sale.products.*.quantity_invoiced' => 'required|numeric',
                    'sale.products.*.unit_price' => 'required|numeric',
                    'sale.products.*.subtotal' => 'required|numeric',
                    'sale.total' => 'required|numeric',
                    'sale.status' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }
                // Obtener el pedido
                $requestData = (object) $request->sale;

                // Obtener datos principales
                $delivery_time = Carbon::parse($requestData->delivery_time);
                $order_date = Carbon::parse($requestData->order_date);
                $dataSale = [
                    'code_sale' => $requestData->code_sale,
                    'name_sale' => $requestData->name_sale,
                    'sequence' => $requestData->sequence,
                    'invoice_address' => $requestData->invoice_address,
                    'delivery_address' => $requestData->delivery_address,
                    'delivery_time' => $delivery_time,
                    'delivery_instructions' => $requestData->delivery_instructions,
                    'order_date' => $order_date,
                    "incidence" => $requestData->incidence,
                    'sample_required' => $requestData->sample_required,
                    'labeling' => $requestData->labeling,
                    'additional_information' => $requestData->additional_information,
                    'tariff' => $requestData->tariff,
                    'commercial_odoo_id' => $requestData->commercial['odoo_id'],
                    'commercial_name' => $requestData->commercial['name'],
                    'commercial_email' => $requestData->commercial['email'],
                    'total' => $requestData->total,
                    'status_id' => 1,
                ];

                // Obtener datos secundarios
                $planned_date = Carbon::parse($requestData->other_information['planned_date']);
                $commitment_date = Carbon::parse($requestData->other_information['commitment_date']);
                $effective_date = Carbon::parse($requestData->other_information['effective_date']);
                $dataAdditionalInfo =  [
                    'client_name' => $requestData->client['name'],
                    'client_contact' => $requestData->client['contact'],
                    'warehouse_company' => $requestData->other_information['warehouse_company'],
                    'warehouse_address' => $requestData->other_information['warehouse_address'],
                    'delivery_policy' => $requestData->other_information['delivery_policy'],
                    'schedule_change' => $requestData->other_information['schedule_change'],
                    'reason_for_change' => $requestData->other_information['reason_for_change'],
                    'planned_date' => $planned_date,
                    'commitment_date' => $commitment_date,
                    'effective_date' => $effective_date
                ];

                $dataProducts = $requestData->products;

                try {
                    $sale = Sale::where("code_sale", $requestData->code_sale)->first();
                    if ($sale) {
                        $sale->update($dataSale);
                        if ($sale->moreInformation) {
                            $sale->moreInformation()->update($dataAdditionalInfo);
                        } else {
                            $sale->moreInformation()->create($dataAdditionalInfo);
                        }
                    } else {
                        $sale = Sale::create($dataSale);
                        $sale->moreInformation()->create($dataAdditionalInfo);
                    }
                    foreach ($dataProducts as $product) {
                        $registered = false;
                        $dataProduct = [
                            "odoo_product_id" => $product['odoo_product_id'],
                            "product" => $product['product'],
                            "description" => $product['description'],
                            "provider" => $product['provider'],
                            "logo" => $product['logo'],
                            "key_product" => $product['key_product'],
                            "type_sale" => $product['type_sale'],
                            "cost_labeling" => $product['cost_labeling'],
                            "clean_product_cost" => $product['clean_product_cost'],
                            "quantity_ordered" => $product['quantity'],
                            "quantity_delivered" => $product['quantity_delivered'],
                            "quantity_invoiced" => $product['quantity_invoiced'],
                            "unit_price" => $product['unit_price'],
                            "subtotal" => $product['subtotal'],
                        ];

                        $sale->saleProducts()->updateOrCreate(
                            [
                                "odoo_product_id" => $product['odoo_product_id'],
                                'sale_id' => $sale->id
                            ],
                            $dataProduct
                        );
                    }

                    foreach ($sale->saleProducts as $productDB) {
                        $existProduct = false;
                        foreach ($dataProducts as $productRQ) {
                            if ($productDB->odoo_product_id == $productRQ['odoo_product_id']) {
                                $existProduct = true;
                            }
                        }
                        if (!$existProduct) {
                            $productDB->delete();
                        }
                    }
                } catch (Exception $th) {
                    return  response()->json(["Server Error Insert: " => $th->getMessage()], 400);
                }
                return response()->json(['message' => 'Actualizacion Completa']);
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
            }
        } catch (Exception $th) {
            return  response()->json(["Server Error Validate: " => $th->getMessage()], 400);
        }
    }

    public function setPurchase(Request $request)
    {
        try {
            if ($request->header('token') == 'YA8FHVMEWISONRUBVVMEW') {
                $validator = Validator::make($request->all(), [
                    'purchase' => 'required|array|bail',
                    'purchase.code_sale' => 'required',
                    'purchase.code_purchase' => 'required',
                    'purchase.type_purchase' => 'required',
                    'purchase.sequence' => 'required',
                    'purchase.company' => 'required',
                    'purchase.order_date' => 'required|date:d-m-Y h:i:s',
                    'purchase.planned_date' => 'required|date:d-m-Y h:i:s',
                    'purchase.provider_address' => 'required',
                    'purchase.provider_name' => 'required',
                    'purchase.supplier_representative' => 'required',
                    'purchase.total' => 'required|numeric',
                    'purchase.status' => 'required',
                    'purchase.products' => 'required|array|bail',
                    'purchase.products.*.odoo_product_id' => 'required',
                    'purchase.products.*.product' => 'required',
                    'purchase.products.*.description' => 'required',
                    'purchase.products.*.planned_date' => 'required|date:d-m-Y h:i:s',
                    'purchase.products.*.company' => 'required',
                    'purchase.products.*.quantity' => 'required|numeric',
                    'purchase.products.*.quantity_delivered' => 'required|numeric',
                    'purchase.products.*.quantity_invoiced' => 'required|numeric',
                    'purchase.products.*.unit_price' => 'required|numeric',
                    'purchase.products.*.subtotal' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }
                $purchase = (object)$request->purchase;
                $dataPurchase = [
                    'code_order' => $purchase->code_purchase,
                    'code_sale' => $purchase->code_sale,
                    'provider_name' => $purchase->provider_name,
                    'provider_address' => $purchase->provider_address,
                    'supplier_representative' => $purchase->supplier_representative,
                    'sequence' => $purchase->sequence,
                    'order_date' => $purchase->order_date,
                    'planned_date' => $purchase->planned_date,
                    'company' => $purchase->company,
                    'total' => $purchase->total,
                    'status' => $purchase->status,
                    'type_purchase' => $purchase->type_purchase,
                ];

                $orderPurchase = null;
                try {
                    $orderPurchase = OrderPurchase::updateOrCreate(['code_order' => $purchase->code_purchase,], $dataPurchase);
                } catch (Exception $th) {
                    //throw $th;
                    return response()->json(['message' => 'Error al crear la orden de compra', 'error' => $th->getMessage()], 400);
                }

                if ($orderPurchase) {
                    $errors = [];
                    foreach ($purchase->products as $productRequest) {
                        $productRequest = (object)$productRequest;
                        $dataProduct =  [
                            "odoo_product_id" => $productRequest->odoo_product_id,
                            "product" => $productRequest->product,
                            "description" => $productRequest->description,
                            "planned_date" => $productRequest->planned_date,
                            "company" => $productRequest->company,
                            "quantity" => $productRequest->quantity,
                            "quantity_delivered" => $productRequest->quantity_delivered,
                            "quantity_invoiced" => $productRequest->quantity_invoiced,
                            "unit_price" => $productRequest->unit_price,
                            "subtotal" => $productRequest->subtotal,
                        ];
                        try {
                            $orderPurchase->products()->updateOrCreate(
                                [
                                    "odoo_product_id" => $productRequest->odoo_product_id,
                                    'order_purchase_id' => $orderPurchase->id
                                ],
                                $dataProduct
                            );
                        } catch (Exception $th) {
                            array_push($errors, ['msg' => "Error al insertar el producto", 'error' => $th->getMessage()]);
                        }
                    }
                    foreach ($orderPurchase->products as $productDB) {
                        $existProduct = false;
                        foreach ($purchase->products as $productRQ) {
                            if ($productDB->odoo_product_id == $productRQ['odoo_product_id']) {
                                $existProduct = true;
                            }
                        }
                        if (!$existProduct) {
                            $productDB->delete();
                        }
                    }
                    if (count($errors) > 0) {
                        return response()->json(['message' => 'Error al insertar los productos', 'error' => json_encode($errors)], 400);
                    }
                }
                return response()->json(['message' => 'Actualizacion Completa'], 200);
            } else {
                return response()->json(['message' => 'No Tienes autorizacion'], 403);
            }
        } catch (Exception $th) {
            return  response()->json(["Server Error Validate: " => $th->getMessage()], 400);
        }
    }

    public function setReception(Request $request)
    {
        try {
            if ($request->header('token') == 'YA8FHVMEWISONRUBVVMEW') {
                $validator = Validator::make($request->all(), [
                    'reception' => 'required|array|bail',
                    'reception.code_reception' => 'required',
                    'reception.code_order' => 'required',
                    'reception.company' => 'required',
                    'reception.type_operation' => 'required',
                    'reception.planned_date' => 'required|date:d-m-Y h:i:s',
                    'reception.effective_date' => 'required|date:d-m-Y h:i:s',
                    'reception.operations' => 'required|array|bail',
                    'reception.operations.*.code_reception' => 'required',
                    'reception.operations.*.odoo_product_id' => 'required',
                    'reception.operations.*.product' => 'required',
                    'reception.operations.*.initial_demand' => 'required|numeric',
                    'reception.operations.*.done' => 'required|numeric',
                    'reception.status' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }

                $reception = (object)$request->reception;
                $dataReception = [
                    'code_reception' => $reception->code_reception,
                    'code_order' => $reception->code_order,
                    'company' => $reception->company,
                    'type_operation' => $reception->type_operation,
                    'planned_date' => $reception->planned_date,
                    'effective_date' => $reception->effective_date,
                    'status' => $reception->status,
                ];
                $receptionDB = null;
                try {
                    $receptionDB = Reception::updateOrCreate(['code_reception' => $reception->code_reception], $dataReception);
                } catch (Exception $th) {
                    return response()->json(['message' => 'Error al crear la orden de compra', 'error' => $th->getMessage()], 400);
                }

                if ($receptionDB) {
                    $errors = [];
                    foreach ($reception->operations as $productRequest) {
                        $productRequest = (object)$productRequest;
                        $dataProduct =  [
                            "odoo_product_id" => $productRequest->odoo_product_id,
                            "product" => $productRequest->product,
                            "code_reception" => $productRequest->code_reception,
                            "initial_demand" => $productRequest->initial_demand,
                            "done" => $productRequest->done,
                        ];
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
                    foreach ($receptionDB->productsReception as $productDB) {
                        $existProduct = false;
                        foreach ($reception->operations as $productRQ) {
                            if ($productDB->odoo_product_id == $productRQ['odoo_product_id']) {
                                $existProduct = true;
                            }
                        }
                        if (!$existProduct) {
                            $productDB->delete();
                        }
                    }
                    if (count($errors) > 0) {
                        return response()->json(['message' => 'Error al insertar los productos', 'error' => json_encode($errors)], 400);
                    }
                }
                return response()->json(['message' => 'Actualizacion Completa'], 200);
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
            }
        } catch (Exception $th) {
            return  response()->json(["Server Error Validate: " => $th->getMessage()], 400);
        }
    }

    public function setIncidence(Request $request)
    {
        try {
            if ($request->header('token') == 'YA8FHVMEWISONRUBVVMEW') {
                $validator = Validator::make($request->all(), [
                    'incidence' => 'required|array|bail',
                    'incidence.code_incidence' => 'required',
                    'incidence.code_sale' => 'required',
                    'incidence.client' => 'required',
                    'incidence.requested_by' => 'required',
                    'incidence.description' => 'required',
                    'incidence.date_request' => 'required',
                    'incidence.company' => 'required',
                    'incidence.products' => 'required|array',
                    'incidence.products.*.code_incidence' => 'required',
                    'incidence.products.*.request' => 'required',
                    'incidence.products.*.notes' => 'required',
                    'incidence.products.*.product' => 'required',
                    'incidence.products.*.quantity' => 'required|numeric',
                    'incidence.products.*.cost' => 'required|numeric',
                    'incidence.status' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }

                /*                 $incidence = (object)$request->incidence;
                $dataIncidence = [
                    'code_incidence' => $incidence->code_incidence,
                    'code_sale' => $incidence->code_sale,
                    'client' => $incidence->client,
                    'requested_by' => $incidence->requested_by,
                    'description' => $incidence->description,
                    'date_request' => $incidence->date_request,
                    'company' => $incidence->company,
                    'status' => $incidence->status,
                ];
                $receptionDB = null;
                try {
                    $receptionDB = Incidence::updateOrCreate(['code_incidence' => $incidence->code_incidence], $dataIncidence);
                } catch (Exception $th) {
                    return response()->json(['message' => 'Error al crear la orden de compra', 'error' => $th->getMessage()], 400);
                }

                if ($receptionDB) {
                    $errors = [];
                    foreach ($reception->operations as $productRequest) {
                        $productRequest = (object)$productRequest;
                        $dataProduct =  [
                            "odoo_product_id" => $productRequest->odoo_product_id,
                            "product" => $productRequest->product,
                            "code_reception" => $productRequest->code_reception,
                            "initial_demand" => $productRequest->initial_demand,
                            "done" => $productRequest->done,
                        ];
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
                    foreach ($receptionDB->productsReception as $productDB) {
                        $existProduct = false;
                        foreach ($reception->operations as $productRQ) {
                            if ($productDB->odoo_product_id == $productRQ['odoo_product_id']) {
                                $existProduct = true;
                            }
                        }
                        if (!$existProduct) {
                            $productDB->delete();
                        }
                    }
                    if (count($errors) > 0) {
                        return response()->json(['message' => 'Error al insertar los productos', 'error' => json_encode($errors)], 400);
                    }
                }
                return response()->json(['message' => 'Actualizacion Completa'], 200); */
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
            }
        } catch (Exception $th) {
            return  response()->json(["Server Error Validate: " => $th->getMessage()], 400);
        }
    }

    public function setDelivery(Request $request)
    {
        try {
            if ($request->header('token') == 'YA8FHVMEWISONRUBVVMEW') {
                $validator = Validator::make($request->all(), [
                    'delivery' => 'required|array|bail',
                    'delivery.code_delivery' => 'required',
                    'delivery.code_sale' => 'required',
                    'delivery.company' => 'required',
                    'delivery.type_operation' => 'required',
                    'delivery.planned_date' => 'required|date:d-m-Y h:i:s',
                    'delivery.effective_date' => 'required|date:d-m-Y h:i:s',
                    'delivery.operations' => 'required|array|bail',
                    'delivery.operations.*.code_delivery' => 'required',
                    'delivery.operations.*.odoo_product_id' => 'required',
                    'delivery.operations.*.product' => 'required',
                    'delivery.operations.*.initial_demand' => 'required|numeric',
                    'delivery.operations.*.done' => 'required|numeric',
                    'delivery.status' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }
                $delivery = (object)$request->delivery;
                $dataDelivery = [
                    'code_delivery' => $delivery->code_delivery,
                    'code_sale' => $delivery->code_sale,
                    'company' => $delivery->company,
                    'type_operation' => $delivery->type_operation,
                    'planned_date' => $delivery->planned_date,
                    'effective_date' => $delivery->effective_date,
                    'status' => $delivery->status,
                ];
                $deliveryDB = null;
                try {
                    $deliveryDB = Delivery::updateOrCreate(['code_delivery' => $delivery->code_delivery], $dataDelivery);
                } catch (Exception $th) {
                    return response()->json(['message' => 'Error al crear la orden de compra', 'error' => $th->getMessage()], 400);
                }

                if ($deliveryDB) {
                    $errors = [];
                    foreach ($delivery->operations as $productRequest) {
                        $productRequest = (object)$productRequest;
                        $dataProduct =  [
                            "odoo_product_id" => $productRequest->odoo_product_id,
                            "product" => $productRequest->product,
                            "code_delivery" => $productRequest->code_delivery,
                            "initial_demand" => $productRequest->initial_demand,
                            "done" => $productRequest->done,
                        ];
                        try {
                            $deliveryDB->productsDelivery()->updateOrCreate(
                                [
                                    "odoo_product_id" => $productRequest->odoo_product_id,
                                    'delivery_id' => $deliveryDB->id
                                ],
                                $dataProduct
                            );
                        } catch (Exception $th) {
                            array_push($errors, ['msg' => "Error al insertar el producto", 'error' => $th->getMessage()]);
                        }
                    }
                    foreach ($deliveryDB->productsdelivery as $productDB) {
                        $existProduct = false;
                        foreach ($delivery->operations as $productRQ) {
                            if ($productDB->odoo_product_id == $productRQ['odoo_product_id']) {
                                $existProduct = true;
                            }
                        }
                        if (!$existProduct) {
                            $productDB->delete();
                        }
                    }
                    if (count($errors) > 0) {
                        return response()->json(['message' => 'Error al insertar los productos', 'error' => json_encode($errors)], 400);
                    }
                    return response()->json(['message' => 'Actualizacion Completa'], 200);
                }
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
            }
        } catch (Exception $th) {
            return  response()->json(["Server Error Validate: " => $th->getMessage()], 400);
        }
    }
}
