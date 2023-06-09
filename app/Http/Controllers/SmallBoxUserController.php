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
        //Status
        //0: PENDIENTE
        //1: APROBADA
        //2: RECHAZADA
        //3: ELIMINADA

        $total_page = 15;
        $data = [];
        $spents =  $user->purchaseRequest;
        

        $total_elements = count($spents);

        $total_pages =intval($total_elements / $total_page);   
        $module =  intval($total_elements % $total_page);  
       
        $max_pages = 0;
        $next_page = 0;
        $previus_page = 1;
        $actual_page = 1;

        $contador = 0;
        $last_contador = 15;

        if($total_pages == 0){
            $max_pages = 1;
            $next_page = 1;
        }else{
            $max_pages = $module == 0? $total_pages: ($total_pages +1);
            $next_page = 2;
        }

        for($i = $contador; $i <= $last_contador ; $i ++ ){ 

            if(isset($spents[$i]->spent_id )){

                $company_data = [];
                $spent_data = [];
                $center_data = [];
                $status_data = [];
                array_push($company_data ,(object) [
                    'company_id' =>  $spents[$i]->company_id,
                    'company_name' =>  $spents[$i]->company->name
                ]);
    
                array_push($spent_data ,(object) [
                    'spent_id' =>  $spents[$i]->spent_id,
                    'spent_name' =>  $spents[$i]->spent->concept,
                    'spent_outgo_type' =>  $spents[$i]->spent->outgo_type,
                    'spent_expense_type' =>  $spents[$i]->spent->expense_type,
                ]);
                array_push($center_data ,(object) [
                    'center_id' => $spents[$i]->center_id,
                    'center_name' =>  $spents[$i]->center->name,
                ]);
    
                array_push($status_data ,(object) [
                    'id' => $spents[$i]->purchase_status->id,
                    'name' =>  $spents[$i]->purchase_status->name,
                    'table_name' =>  $spents[$i]->purchase_status->table_name,
                    'type' =>  $spents[$i]->purchase_status->type,
                    'status' =>  $spents[$i]->purchase_status->status,
                ]);
    
                $approved_by = '';
              
                if($spents[$i]->approved_by != null || $spents[$i]->approved_by != '' ){
                    $user_approved = User::where('id', intval($spents[$i]->approved_by))->get()->last();
    
                    $approved_by =  $user_approved->name;
                }
                
                $approved_status = '';

                if($spents[$i]->status == 0){
                    $approved_status = 'pendiente';
                }

                if($spents[$i]->status == 1){
                    $approved_status = 'aprobada';
                }

                if($spents[$i]->status == 2){
                    $approved_status = 'rechazada';
                }

                array_push($data, (object)[
                    'id' => $spents[$i]->id,
                    'user_id' => $spents[$i]->user_id,
                    'company' =>  $company_data,
                    'spent' => $spent_data,
                    'center'  =>  $center_data,
                    'description' => $spents[$i]->description,
                    'file' => $spents[$i]->file,
                    'commentary' => $spents[$i]->commentary,
                    'status' => $status_data,
                    'approved_status' => $approved_status,
                    'approved_by' => $approved_by,
                    'payment_method' =>$spents[$i]->payment_method->name, 
                    'total' => $spents[$i]->total,
                    'created_at' => $spents[$i]->created_at,
                ]);
            }
            
        }

        return array(
            'spents' => $data, 
            'pages' => [
                'actual_page' => $actual_page,
                'max_pages' => $max_pages,
                'next_page' => 'caja-chica/solicitudes-de-compra/ver/'. $next_page,
                'previus_page'  =>  'caja-chica/solicitudes-de-compra/ver/'. $previus_page,
            ]
        );


        }else{
            return response()->json(['Usuario no encontrado']);
        }
        
    }

    public function showUserPageRequests($page)
    {
        //Status
        //0: PENDIENTE
        //1: APROBADA
        //2: RECHAZADA
        //3: ELIMINADA

        $user= auth()->user();

        if($user != null){
            $total_page = 15;
            $data = [];
            $spents = $user->purchaseRequest;

            $total_elements = count($spents);

            $total_pages =intval($total_elements / $total_page);      
            $module =  intval($total_elements % $total_page);  

            $max_pages = 0;
            $next_page = 0;
            $previus_page = 1;
            $actual_page = 1;

            $contador = 0;
            $last_contador = 15;

            if($total_pages == 0){
                $max_pages = 1;
                $next_page = 1;
            }else{
                $max_pages = $module == 0? $total_pages: ($total_pages +1);
                $next_page = $max_pages > $page? intval($page)+1 : $page;
                $contador = (intval($page) *15 )-15;
                $last_contador = (intval($page) *15 );
                $actual_page = intval($page);
                $previus_page = intval($page) == 1? 1 : (intval($page)-1);
            }

            for($i = $contador; $i <= $last_contador ; $i ++ ){ 

                if(isset($spents[$i]->spent_id )){

                    $company_data = [];
                    $spent_data = [];
                    $center_data = [];
                    $status_data = [];
                    array_push($company_data ,(object) [
                        'company_id' =>  $spents[$i]->company_id,
                        'company_name' =>  $spents[$i]->company->name
                    ]);
        
                    array_push($spent_data ,(object) [
                        'spent_id' =>  $spents[$i]->spent_id,
                        'spent_name' =>  $spents[$i]->spent->concept,
                        'spent_outgo_type' =>  $spents[$i]->spent->outgo_type,
                        'spent_expense_type' =>  $spents[$i]->spent->expense_type,
                    ]);
                    array_push($center_data ,(object) [
                        'center_id' => $spents[$i]->center_id,
                        'center_name' =>  $spents[$i]->center->name,
                    ]);
        
                    array_push($status_data ,(object) [
                        'id' => $spents[$i]->purchase_status->id,
                        'name' =>  $spents[$i]->purchase_status->name,
                        'table_name' =>  $spents[$i]->purchase_status->table_name,
                        'type' =>  $spents[$i]->purchase_status->type,
                        'status' =>  $spents[$i]->purchase_status->status,
                    ]);
        
                    $approved_by = '';
                
                    if($spents[$i]->approved_by != null || $spents[$i]->approved_by != '' ){
                        $user_approved = User::where('id', intval($spents[$i]->approved_by))->get()->last();
        
                        $approved_by =  $user_approved->name;
                    }
                    $approved_status = '';

                    if($spents[$i]->status == 0){
                        $approved_status = 'pendiente';
                    }

                    if($spents[$i]->status == 1){
                        $approved_status = 'aprobada';
                    }

                    if($spents[$i]->status == 2){
                        $approved_status = 'rechazada';
                    }

                    array_push($data, (object)[
                        'id' => $spents[$i]->id,
                        'user_id' => $spents[$i]->user_id,
                        'company' =>  $company_data,
                        'spent' => $spent_data,
                        'center'  =>  $center_data,
                        'description' => $spents[$i]->description,
                        'file' => $spents[$i]->file,
                        'commentary' => $spents[$i]->commentary,
                        'status' => $status_data,
                        'approved_status' => $approved_status,
                        'approved_by' => $approved_by,
                        'payment_method' =>$spents[$i]->payment_method->name, 
                        'total' => $spents[$i]->total,
                        'created_at' => $spents[$i]->created_at,
                    ]);
                }  
            }

            return array(
                'spents' => $data, 
                'pages' => [
                    'actual_page' => $actual_page,
                    'max_pages' => $max_pages,
                    'next_page' => 'caja-chica/solicitudes-de-compra/ver/'. $next_page,
                    'previus_page'  =>  'caja-chica/solicitudes-de-compra/ver/'. $previus_page,
                ]
            );

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
