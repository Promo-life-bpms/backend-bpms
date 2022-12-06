<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderPurchase;
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
                        foreach ($sale->saleProducts as $productRegistered) {
                            if ($product['odoo_product_id'] == $productRegistered->odoo_product_id) {
                                $registered = true;
                            }
                        }
                        if ($registered) {
                            $productRegistered->update($dataProduct);
                        } else {
                            $sale->saleProducts()->create($dataProduct);
                        }
                    }
                } catch (Exception $th) {
                    return  response()->json(["Server Error Insert: " => $th->getMessage()], 400);
                }
                return response()->json(['message' => 'Actualizacion Completa', 'data' => ($request->sale)]);
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
                    'purchase' => 'bail|required|array',
                    'purchase.code_order' => 'required',
                    'purchase.code_sale' => 'required',
                    'purchase.provider.name' => 'required',
                    'purchase.provider.representante' => 'required',
                    'purchase.sequence' => 'required',
                    'purchase.order_date' => 'required|date',
                    'purchase.planned_date' => 'required|date',
                    'purchase.deliver_in' => 'required',
                    'purchase.products' => 'required|array',
                    'purchase.products.*.product' => 'required',
                    'purchase.products.*.description' => 'required',
                    'purchase.products.*.planned_date' => 'required|date',
                    'purchase.products.*.quantity' => 'required|numeric'
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()), 201);
                }

                $requestData = (object) $request->purchase;
                $planned_date = Carbon::parse($requestData->planned_date);
                $order_date = Carbon::parse($requestData->order_date);

                $dataOrder = [
                    'code_order' => $requestData->code_order,
                    'code_sale' => $requestData->code_sale,
                    'provider_name' => $requestData->provider['name'],
                    'provider_address' => $requestData->provider['address'],
                    'sequence' => $requestData->sequence,
                    'order_date' => $order_date,
                    'planned_date' => $planned_date,
                    'deliver_in' => $requestData->deliver_in,
                ];

                $dataProducts = $requestData->products;
                try {
                    $orderPurchase = OrderPurchase::where("code_order", $requestData->code_order)->first();
                    if ($orderPurchase) {
                        $orderPurchase->update($dataOrder);
                        foreach ($dataProducts as $product) {
                        }
                    } else {
                        $orderPurchase = OrderPurchase::create($dataOrder);
                        foreach ($dataProducts as $product) {
                            // $product = Produc-
                        }
                    }
                } catch (Exception $th) {
                    return  response()->json(["Server Error Insert: " => $th->getMessage()], 400);
                }
                return response()->json(['message' => 'Actualizacion Completa', 'data' => ($request->purchase)]);
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
            }
        } catch (Exception $th) {
            return  response()->json(["Server Error Validate: " => $th->getMessage()], 400);
        }
    }
}
