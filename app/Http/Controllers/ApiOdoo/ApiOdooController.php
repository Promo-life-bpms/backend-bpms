<?php

namespace App\Http\Controllers\ApiOdoo;

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
                    'sale.invoice_address' => 'required',
                    'sale.delivery_address' => 'required',
                    'sale.delivery_instructions' => 'required',
                    'sale.delivery_time' => 'required|date',
                    'sale.order_date' => 'required|date',
                    'sale.confirmation_date' => 'required|date',
                    'sale.additional_information' => 'required',
                    'sale.client.name' => 'required',
                    'sale.client.address' => 'required',
                    'sale.client.contact' => 'required',
                    'sale.shipping_information.warehouse.company' => 'required',
                    'sale.shipping_information.warehouse.address' => 'required',
                    'sale.shipping_information.planned_date' => 'required',
                    'sale.shipping_information.commitment_date' => 'required',
                    'sale.commercial.odoo_id' => 'required',
                    'sale.commercial.name' => 'required',
                    'sale.commercial.email' => 'required',
                    // 'sale.products' => 'bail|required|array',
                    // 'sale.products.*.product' => 'required',
                    // 'sale.products.*.description' => 'required',
                    // 'sale.products.*.planned_date' => 'required|date',
                    // 'sale.products.*.quantity' => 'required|numeric',
                 ]);

                if ($validator->fails()) {
                    return response()->json(($validator->getMessageBag()), 201);
                }

                $requestData = (object) $request->sale;
                $delivery_time = Carbon::parse($requestData->delivery_time);
                $order_date = Carbon::parse($requestData->order_date);
                $confirmation_date = Carbon::parse($requestData->confirmation_date);

                $planned_date = Carbon::parse($requestData->shipping_information['planned_date']);
                $commitment_date = Carbon::parse($requestData->shipping_information['commitment_date']);

                $dataSale = [
                    'code_sale' => $requestData->code_sale,
                    'name_sale' => $requestData->name_sale,
                    'directed_to' => $requestData->directed_to,
                    'invoice_address' => $requestData->invoice_address,
                    'delivery_address' => $requestData->delivery_address,
                    'delivery_instructions' => $requestData->delivery_instructions,
                    'delivery_time' => $delivery_time,
                    'order_date' => $order_date,
                    'confirmation_date' => $confirmation_date,
                    'confirmation_date' => $confirmation_date,
                    'additional_information' => $requestData->additional_information,
                    'commercial_name' => $requestData->commercial['name'],
                    'commercial_email' => $requestData->commercial['email'],
                    'commercial_odoo_id' => $requestData->commercial['odoo_id']
                ];
                $dataAdditionalInfo =  [
                    'client_name' => $requestData->client['name'],
                    'client_address' => $requestData->client['address'],
                    'client_contact' => $requestData->client['contact'],
                    'warehouse_company' => $requestData->shipping_information['warehouse']['company'],
                    'warehouse_address' => $requestData->shipping_information['warehouse']['address'],
                    'planned_date' => $planned_date,
                    'commitment_date' => $commitment_date
                ];
                try {
                    $sale = Sale::where("code_sale", $requestData->code_sale)->first();
                    if ($sale) {
                        $sale->update($dataSale);
                        if ($sale->aditionalInformation) {
                            $sale->aditionalInformation()->update($dataAdditionalInfo);
                        } else {
                            $sale->aditionalInformation()->create($dataAdditionalInfo);
                        }
                    } else {
                        $sale = Sale::create($dataSale);
                        $sale->aditionalInformation()->create($dataAdditionalInfo);
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
                    'purchase.sequence' => 'required',
                    'purchase.order_date' => 'required|date',
                    'purchase.planned_date' => 'required|date',
                    'purchase.deliver_in' => 'required',
                    'purchase.products' => 'required|array',
                    'purchase.products.*.product' => 'required',
                    'purchase.products.*.description' => 'required',
                    'purchase.products.*.planned_date' => 'required|date',
                    'purchase.products.*.quantity' => 'required|numeric',
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
                       /*  foreach ($dataProducts as $product) {
                            $product = Produc
                        } */
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
