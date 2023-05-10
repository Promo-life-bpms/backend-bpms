<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Http\Request;

class SmallBoxUserController extends Controller
{
    public function showUserRequests(Request $request)
    {
        $user_id = auth()->user()->id;

        if($user_id !=null){
            $data = [];
            $purchases = PurchaseRequest::where('user_id', $user_id)->get();
            array_push($data, $purchases);
    
            return $purchases;
        }else{
            return response()->json(['Usuario no encontrado']);
        }
        
    }

    public function createRequest(Request $request)
    {

        $request->validate([
            'spent_id' =>'required',
            'description' =>'required',
            'file' =>'required',
            'purchase_status_id' =>'required',
            'payment_method_id' =>'required',
        ]);

        $user = auth()->user();

        if($user != null){
            $create_request = new PurchaseRequest();
            $create_request->user_id = $request->user_id;
            $create_request->company_id = $request->company_id;
            $create_request->spent_id = $request->spent_id;
            $create_request->description = $request->description;
            $create_request->file = $request->file;
            $create_request->commentary = $request->user_id;
            $create_request->purchase_status_id = $request->user_id;
            $create_request->payment_method_id = $request->user_id;
            $create_request->total = $request->user_id;
            $create_request->save();
        }
      
    }
}
