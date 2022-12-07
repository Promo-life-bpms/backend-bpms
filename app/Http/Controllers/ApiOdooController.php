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
                    'purchases' => 'required|array|bail',
                    'purchases.*.code_sale' => 'required',
                    'purchases.*.type_purchase' => 'required',
                    'purchases.*.sequence' => 'required',
                    'purchases.*.company' => 'required',
                    'purchases.*.code_purchase' => 'required',
                    'purchases.*.order_date' => 'required|date:d-m-Y h:i:s',
                    'purchases.*.provider_address' => 'required',
                    'purchases.*.provider_name' => 'required',
                    'purchases.*.supplier_representative' => 'required',
                    'purchases.*.total' => 'required|numeric',
                    'purchases.*.status' => 'required',
                    'purchases.*.products' => 'required|array|bail',
                    'purchases.*.products.*.odoo_product_id' => 'required',
                    'purchases.*.products.*.product' => 'required',
                    'purchases.*.products.*.description' => 'required',
                    'purchases.*.products.*.planned_date' => 'required|date:d-m-Y h:i:s',
                    'purchases.*.products.*.company' => 'required',
                    'purchases.*.products.*.quantity' => 'required|numeric',
                    'purchases.*.products.*.quantity_delivered' => 'required|numeric',
                    'purchases.*.products.*.quantity_invoiced' => 'required|numeric',
                    'purchases.*.products.*.unit_price' => 'required|numeric',
                    'purchases.*.products.*.subtotal' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
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
                    'receptions' => 'required|array|bail',
                    'receptions.code_reception' => 'required',
                    'receptions.code_order' => 'required',
                    'receptions.company' => 'required',
                    'receptions.type_operation' => 'required',
                    'receptions.planned_date' => 'required|date:d-m-Y h:i:s',
                    'receptions.effective_date' => 'required|date:d-m-Y h:i:s',
                    'receptions.operations' => 'required|array|bail',
                    'receptions.operations.*.code_reception' => 'required',
                    'receptions.operations.*.odoo_product_id' => 'required',
                    'receptions.operations.*.product' => 'required',
                    'receptions.operations.*.initial_demand' => 'required|numeric',
                    'receptions.operations.*.done' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }
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
                    'incidences' => 'required|array|bail',
                    'incidences.code_incidence' => 'required',
                    'incidences.code_sale' => 'required',
                    'incidences.client' => 'required',
                    'incidences.requested_by' => 'required',
                    'incidences.description' => 'required',
                    'incidences.date_request' => 'required',
                    'incidences.company' => 'required',
                    'incidences.products' => 'required|array',
                    'incidences.products.*.code_incidence' => 'required',
                    'incidences.products.*.request' => 'required',
                    'incidences.products.*.notes' => 'required',
                    'incidences.products.*.product' => 'required',
                    'incidences.products.*.quantity' => 'required|numeric',
                    'incidences.products.*.cost' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }
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
                    'deliveries' => 'required|array|bail',
                    'deliveries.code_delivery' => 'required',
                    'deliveries.code_sale' => 'required',
                    'deliveries.company' => 'required',
                    'deliveries.type_operation' => 'required',
                    'deliveries.planned_date' => 'required|date:d-m-Y h:i:s',
                    'deliveries.effective_date' => 'required|date:d-m-Y h:i:s',
                    'deliveries.operations' => 'required|array|bail',
                    'deliveries.operations.*.code_delivery' => 'required',
                    'deliveries.operations.*.odoo_product_id' => 'required',
                    'deliveries.operations.*.product' => 'required',
                    'deliveries.operations.*.initial_demand' => 'required|numeric',
                    'deliveries.operations.*.done' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()));
                }
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
            }
        } catch (Exception $th) {
            return  response()->json(["Server Error Validate: " => $th->getMessage()], 400);
        }
    }
}
