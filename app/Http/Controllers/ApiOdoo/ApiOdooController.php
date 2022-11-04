<?php

namespace App\Http\Controllers\ApiOdoo;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ApiOdooController extends Controller
{
    public function setSale(Request $request)
    {
        try {
            if ($request->header('token') == 'YA8FHVMEWISONRUBVVMEW') {
                $requestData = $request->sale;
                if ($requestData) {
                    $errors = array();
                    /* if (!$requestData['code_sale'] || !$requestData['id'] || !$requestData['name'] || !$requestData['email'] || !$requestData['tradename'] || !$requestData['phone']) {
                        array_push($errors, [$requestData, 'Falta informacion del usuario']);
                    } */
                    if (count($errors) > 0) {
                        return response()->json(['errors' => 'Informacion Incompleta', 'data' => $errors]);
                    } else {
                        $requestData = (object) $requestData;
                        $delivery_time = Carbon::parse($requestData->delivery_time);
                        $order_date = Carbon::parse($requestData->order_date);
                        $confirmation_date = Carbon::parse($requestData->confirmation_date);

                        try {
                            $sale = Sale::where("code_sale", $requestData->code_sale)->first();
                            if (!$sale) {
                                $sale->update([
                                    'name_sale' => $requestData->name_sale,
                                    'directed_to' => $requestData->directed_to,
                                    'invoice_address' => $requestData->invoice_address,
                                    'delivery_address' => $requestData->delivery_address,
                                    'delivery_instructions' => $requestData->delivery_instructions,
                                    'delivery_time' => $delivery_time,
                                    'order_date' => $order_date,
                                    'confirmation_date' => $confirmation_date,
                                    'additional_information' => $requestData->additional_information,
                                    'commercial_name' => $requestData->commercial['name'],
                                    'commercial_email' => $requestData->commercial['email'],
                                    'commercial_odoo_id' => $requestData->commercial['odoo_id']
                                ]);
                            } else {
                                Sale::create([
                                    'code_sale' => $requestData->code_sale,
                                    'name_sale' => $requestData->name_sale,
                                    'directed_to' => $requestData->directed_to,
                                    'invoice_address' => $requestData->invoice_address,
                                    'delivery_address' => $requestData->delivery_address,
                                    'delivery_instructions' => $requestData->delivery_instructions,
                                    'delivery_time' => $delivery_time,
                                    'order_date' => $order_date,
                                    'confirmation_date' => $confirmation_date,
                                    'additional_information' => $requestData->additional_information,
                                    'commercial_name' => $requestData->commercial['name'],
                                    'commercial_email' => $requestData->commercial['email'],
                                    'commercial_odoo_id' => $requestData->commercial['odoo_id']
                                ]);
                            }
                        } catch (Exception $th) {
                            return  $th;
                        }
                        return $requestData;
                        return response()->json(['message' => 'Actualizacion Completa', 'data' => ($request->orderSale)]);
                    }
                } else {
                    return response()->json(['errors' => 'Informacion Incompleta']);
                }
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
            }
        } catch (Exception $th) {
            return 1;
            Storage::put('/public/dataErrorClients.txt',   json_encode($th->getMessage()));
            // Mail::to('adminportales@promolife.com.mx')->send(new SendDataOdoo('adminportales@promolife.com.mx', '/storage/dataErrorClients.txt'));
            return response()->json($th->getMessage());
        }
    }
    public function setPurchase(Request $request)
    {
        try {
            if ($request->header('token') == 'YA8FHVMEWISONRUBVVMEW') {
                $requestData = $request->purchase;
                return $requestData;
                if ($requestData) {
                    return response()->json(['message' => 'Actualizacion Completa', 'data' => ($request->orderSale)]);
                } else {
                    return response()->json(['errors' => 'Informacion Incompleta']);
                }
            } else {
                return response()->json(['message' => 'No Tienes autorizacion']);
            }
        } catch (Exception $th) {
            Storage::put('/public/dataErrorClients.txt',   json_encode($th->getMessage()));
            // Mail::to('adminportales@promolife.com.mx')->send(new SendDataOdoo('adminportales@promolife.com.mx', '/storage/dataErrorClients.txt'));
            return  $th->getMessage();
        }
    }
}
