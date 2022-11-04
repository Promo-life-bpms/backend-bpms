<?php

namespace App\Http\Controllers\ApiOdoo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiOdooController extends Controller
{
    public function setSale(Request $request)
    {
        try {
            if ($request->header('token') == 'YA8FHVMEWISONRUBVVMEW') {
                $requestData = $request->sale;
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
