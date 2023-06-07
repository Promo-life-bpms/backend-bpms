<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Company;
use App\Models\PaymentMethod;
use App\Models\PurchaseRequest;
use App\Models\Role;
use App\Models\Spent;
use App\Models\User;
use Illuminate\Http\Request;

class SmallBoxUserController extends Controller
{
    public function showUserRequests()
    {
        $user= auth()->user();
        
        if($user !=null){
            return $user->purchaseRequest;
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

    public function report(Request $request)
    {
        $request->validate([
            'start' => 'required',
            'end' => 'required',
            'payment_method_id' => 'required',
            'company_id' => 'required',
        ]);

        $filter_data = [];

        $format_start =date('Y-m-d', strtotime($request->start));
        $format_end =date('Y-m-d', strtotime($request->end));

        $payment_method = PaymentMethod::where('id', $request->payment_method_id)->get()->last();
        $company = Company::where('id',$request->company_id)->get()->last();

        if($payment_method  == null){
            return response()->json(['msg' => "Metodo de pago no encontrado, verifica la información e intenta nuevamente"]);
        }

        if($company  == null){
            return response()->json(['msg' => "Empresa no encontrada, verifica la información e intenta nuevamente"]);
        }

        array_push($filter_data, (object)[
            'start' => $format_start, 
            'end' => $format_start,
            'payment_method' => $payment_method->name,
            'company' => $company->name
        ]);

        $purchases = PurchaseRequest::whereDate('created_at','>=',$format_start)->whereDate('created_at','<=',$format_end)->where('company_id',$request->company_id)->where('payment_method_id',$request->payment_method_id)->get();

        $reporte = new SmallBoxReport();
        $reporte->smallBoxReport($purchases, $filter_data);
    }

    public function dataRequest()
    {
        $data = [];

        $companies_data = [];
        $spents_data = [];
        $centers_data = [];
        $roles_data = [];
        $payments_data = [];

        $companies = Company::all();
        $centers = Center::all();
        $spents = Spent::all();
        $roles = Role::all();
        $payments = PaymentMethod::all();

        foreach($companies as $company){

            array_push($companies_data, (object)[
                'id' => $company->id,
                'name' => $company->name,
            ]);
        }

        foreach($spents as $spent){
            array_push($spents_data, (object)[
                'id' => $spent->id,
                'concept' => $spent->concept,
                'center' => $spent->center->name,
                'outgo_type' => $spent->outgo_type,
                'expense_type' => $spent->expense_type,
            ]);
        }

        foreach($centers as $center){
            array_push($centers_data, (object)[
                'id' => $center->id,
                'name' => $center->name,
            ]);
        }

        foreach($roles as $role){
            array_push($roles_data, (object)[
                'id' => $role->id,
                'name' => $role->display_name,
            ]);
        }

        foreach($payments as $payment){
            array_push($payments_data, (object)[
                'id' => $payment->id,
                'name' => $payment->name,
            ]);
        }

        $data = [
            'companies' => $companies_data,
            'centers' => $centers_data,
            'spents' => $spents_data,
            'payments' => $payments_data, 
            'roles' => $roles_data,
        ];
        
        return $data;
    }
}
