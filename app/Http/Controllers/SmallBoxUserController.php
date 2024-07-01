<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Company;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodInformation;
use App\Models\PurchaseRequest;
use App\Models\Role;
use App\Models\Spent;
use App\Models\User;
use App\Notifications\CreateRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SmallBoxUserController extends Controller
{
    public function showUserRequests()
    {
        $user= auth()->user();
        
        if($user !=null){

            $data = [];
            $spents =  $user->purchaseRequest;
        
            foreach($spents as $spent){
                $company_data = [];
                $spent_data = [];
                $center_data = [];
                $status_data = [];
                array_push($company_data ,(object) [
                    'company_id' =>  $spent->company_id,
                    'company_name' =>  $spent->company->name
                ]);

                array_push($spent_data ,(object) [
                    'spent_id' =>  $spent->spent_id,
                    'spent_name' =>  $spent->spent->concept,
                    'spent_outgo_type' =>  $spent->spent->outgo_type,
                    'spent_expense_type' =>  $spent->spent->expense_type,
                    'spent_product_type' =>  $spent->spent->product_type,
                ]);
                array_push($center_data ,(object) [
                    'center_id' => $spent->center_id,
                    'center_name' =>  $spent->center->name,
                ]);

                array_push($status_data ,(object) [
                    'id' => $spent->purchase_status->id,
                    'name' =>  $spent->purchase_status->name,
                    'table_name' =>  $spent->purchase_status->table_name,
                    'type' =>  $spent->purchase_status->type,
                    'status' =>  $spent->purchase_status->status,
                ]);

                $approved_by = '';
            
                if($spent->approved_by != null || $spent->approved_by != '' ){
                    $user_approved = User::where('id', intval($spent->approved_by))->get()->last();
                    $approved_by =  $user_approved->name;
                }
                    
                $admin_approved = '';

                if($spent->admin_approved != null || $spent->admin_approved != '' ){
                    $admin_app = User::where('id', intval($spent->admin_approved))->get()->last();

                    $admin_approved =  $admin_app->name;
                }
                
                array_push($data, (object)[
                    'id' => $spent->id,
                    'user_id' => $spent->user_id,
                    'user_name' => $spent->user->name,
                    'company' =>  $company_data,
                    'spent' => $spent_data,
                    'center'  =>  $center_data,
                    'description' => $spent->description,
                    'file' => $spent->file,
                    'commentary' => $spent->commentary,
                    'purchase_status' => $spent->purchase_status->name,
                    'purchase_table_name' => $spent->purchase_status->table_name,
                    'type' => $spent->type,
                    'type_status' => $spent->type_status,
                    'payment_method_id' => $spent->payment_method->id,
                    'payment_method' => $spent->payment_method->name,  
                    'total' =>$spent->total, 
                    'approved_status' => $spent->approved_status,
                    'approved_by' => $approved_by,
                    'admin_approved' => $admin_approved,
                    'created_at' => $spent->created_at->format('d-m-Y'),
                    'creation_date' => $spent->creation_date ? Carbon::parse($spent->creation_date)->format('d-m-Y') : "Aún no se ha asignado una fecha de creación.",
                ]);
            }

            if($data == null){
                return response()->json(['message' => 'No has creado solicitudes de gasto.'], 409);
            }

            return array(
                'spents' => $data, 
            );

        }else{
            return response()->json(['Usuario no encontrado'],400);
        }   
    }

    public function showUserPageRequests($page)
    {
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
                        'spent_product_type' =>  $spents[$i]->spent->product_type,
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
                    $approved_by = '';
              
                    if($spents[$i]->approved_by != null || $spents[$i]->approved_by != '' ){
                        $user_approved = User::where('id', intval($spents[$i]->approved_by))->get()->last();
        
                        $approved_by =  $user_approved->name;
                    }                

                    array_push($data, (object)[
                        'id' => $spents[$i]->id,
                        'user_id' => $spents[$i]->user_id,
                        'user_name' => $spents[$i]->user->name,
                        'company' =>  $company_data,
                        'spent' => $spent_data,
                        'center'  =>  $center_data,
                        'description' => $spents[$i]->description,
                        'file' => $spents[$i]->file,
                        'commentary' => $spents[$i]->commentary,
                        'purchase_status' => $spents[$i]->purchase_status->name,
                        'purchase_table_name' => $spents[$i]->purchase_status->table_name,
                        'type' => $spents[$i]->type,
                        'type_status' => $spents[$i]->type_status,
                        'payment_method_id' => $spents[$i]->payment_method->id,
                        'payment_method' => $spents[$i]->payment_method->name,
                        'total' =>$spents[$i]->total, 
                        'approved_status' => $spents[$i]->approved_status,
                        'approved_by' => $approved_by,
                        'created_at' => $spents[$i]->created_at->format('d-m-Y'),
                    ]);
                }  
            }

            return array(
                'spents' => $data, 
                'pages' => [
                    'actual_page' => $actual_page,
                    'max_pages' => $max_pages,
                    'next_page' => 'caja-chica/mis-ordenes/'. $next_page,
                    'previus_page'  =>  'caja-chica/mis-ordenes/'. $previus_page,
                ]
            );

        }else{
            return response()->json(['Usuario no encontrado'],400);
        }
    }

    public function createRequest(Request $request)
    {

        $request->validate([
            'spent_id' =>'required',
            'description' =>'required',
            'file' =>'required',
            'purchase_status_id' =>'required',
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

        return response()->json(['message' => 'Se creo con éxito la solicitud'],200);
      
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
            return response()->json(['message' => "Método de pago no encontrado, verifica la información e intenta nuevamente."],400);
        }

        if($company  == null){
            return response()->json(['message' => "Empresa no encontrada, verifica la información e intenta nuevamente"],400);
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
        $user = auth()->user();
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
        
        /////ESTOS SON LOS SPENTS QUE TODOS PUEDEN VER 115, 26, 10////
        $rol = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id')->toArray();
        $selectedIds = [];
        foreach ($rol as $role_id) {
            switch ($role_id) {
                case 1: // Administrador
                    $selectedIds = [];
                    break;
                
                case 24: // Recursos Humanos
                    $selectedIds = [112, 21, 22, 123, 14, 29, 127, 92, 88, 89, 15, 91, 19, 20, 76, 9, 
                                111, 87, 11, 122, 136, 8, 137, 115, 26, 10];
                    break;
                case 26:
                    $selectedIds = [12, 30, 58, 64, 110, 137, 115, 26, 10];
                    break;
                case 27: // Equipo de Ventas
                    $selectedIds = [12, 30, 58, 64, 110, 137, 115, 26, 10];
                    break;
                    
                case 14: // Caja Chica,
                    $selectedIds = [134, 42, 106, 41, 40, 43, 109, 45, 135, 44, 102, 118, 24, 31, 53, 50, 129, 131, 132, 17, 47, 48, 49, 52, 124, 13, 28, 115, 26, 10];
                    break;
                case 15: //Adquisiciones 
                    $selectedIds = [134, 42, 106, 41, 40, 43, 109, 45, 135, 44, 102, 118, 24, 31, 53, 50, 129, 131, 132, 17, 47, 48, 49, 52, 124, 13, 28, 115, 26, 10];
                    break;
                case 16: //Equipo Administración
                    $selectedIds = [134, 42, 106, 41, 40, 43, 109, 45, 135, 44, 102, 118, 24, 31, 53, 50, 129, 131, 132, 17, 47, 48, 49, 52, 124, 13, 28, 115, 26, 10];
                    break;
                case 25: // Equipo de TI
                    $selectedIds = [115, 26, 10];
                    break;
                case 28: // Equipo de Sistemas
                    $selectedIds = [65, 86, 51, 81, 80, 82, 83, 79, 78, 115, 26, 10];
                    break;
                case 21: // Equipo de Importaciones
                    $selectedIds = [39, 55, 115, 26, 10];
                    break;

                /* ///////EQUIPO DE DIRECCION PL///////
                ///PENDIENTE
                elseif($role_id == 20){
                    $selectedIds = [68, 93, 61, 52, 124, 8, 115, 26, 10];
                }
            
                /////AQUI ESTOY AGREGANDO LOS SPENTS QUE CREO SON DE ALMACEN O OPERACIONES O LOGISTICA
                ///PENDIENTE////////
                elseif($role_id == 22 || $role_id == 23){
                    $selectedIds = [18, 27, 74, 16, 99, 97, 96, 6, 71, 54, 
                            75, 73, 72, 115, 26, 10];
                }
                ////ALEJANDRO VALENCIA///////
                ////COMPRAS PENDIENTE
                elseif($user->id == 95 || $user->id == 268){
                    $selectedIds = [2, 101, 56, 54, 115, 26, 10];
                } */
                
                default:
                    $selectedIds = [115, 26, 10];
                    // Manejo para roles no especificados
                    break;
            }
        }

        if (!empty($selectedIds)) {
            $spents = DB::table('spents')->whereIn('id', $selectedIds)->get();
        } else {
            //SI EL ARREGLO ESTA VACIO TRAE TODO;
            $spents = DB::table('spents')->get();
        }

        //////ESTO ES SOLO PARA MANAGERS///////
        $managers = DB::table('manager_has_departments')->where('id_user', $user->id)->pluck('id_department')->toArray();
        $managersids = [];
        ///ESTOS SON BASICOS PARA LOS MANAGERS 59, 133, 137, 58
        foreach ($managers as $manager) {
            switch ($manager) {
                case 1:
                    //return 1;
                    $managersids=[59, 133, 137, 58, 134, 42, 106, 41, 40, 43, 109, 45, 135, 44, 102, 118, 24, 31, 53, 50, 129, 131, 132, 17, 47, 48, 49, 52, 124, 13, 28, 115, 26, 10];
                    break;
                case 2:
                    //return 2;
                    $managersids=[115, 26, 10];
                    break;
                case 3:
                    //return 3;
                    $managersids=[115, 26, 10 ];
                    break;
                case 4:
                    //return 4;
                    $managersids=[59, 133, 137, 58, 115, 26, 10];
                    break;
                case 5:
                    //return 5;
                    $managersids=[115, 26, 10];
                    break;
                case 6:
                    ////IMPORTACIONES////
                    //return 6;
                    $managersids=[59, 133, 137, 58, 39, 55, 115, 26, 10];
                    break;
                case 7:
                    ///RECURSOS HUMANOS///
                    //return 7;
                    $managersids=[59, 133, 137, 58, 112, 21, 22, 123, 14, 29, 127, 92, 88, 89, 15, 91, 19, 20, 76, 9, 
                    111, 87, 11, 122, 136, 8, 115, 26, 10 ];
                    break;
                case 8:
                    /////TI
                    //return 8;
                    $managersids=[69, 59, 133, 137, 70, 58, 115, 26, 10];
                    break;
                case 9:
                    ///VENTAS BH
                    //return 9;
                    $managersids=[59, 133, 12, 30, 58, 64, 110, 137, 115, 26, 10];
                    break;
                case 10:
                    ////VENTAS PL
                    //return 10;
                    $managersids=[59, 133, 12, 30, 58, 64, 110, 137, 115, 26, 10 ];
                    break;
                case 11:
                    //return 11;
                    $managersids=[115, 26, 10];
                    break;
            }
        }
    
        if (!empty($managersids)) {
            $spents = DB::table('spents')->whereIn('id', $managersids)->get();
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
            'companies' => $companies->isEmpty() ? ['message' => 'No hay compañias disponibles.'] : $companies_data,
            'centers' => $centers->isEmpty() ? ['message' => 'No hay centros de gastos disponibles.'] : $centers_data,
            'spents' => $spents->isEmpty() ? ['message' => 'No hay tipos de gastos disponibles.'] : $spents,
            'payments' => $payments->isEmpty() ? ['message' => 'No hay métodos de pagos disponibles.'] : $payments_data,
            'roles' => $roles->isEmpty() ? ['message' => 'No hay roles disponibles.'] : $roles_data,
        ];
        
        return $data;
    }

    public function showBuyerRequests()
    {
        $user= auth()->user();
        
        if($user !=null){
     
            $data = [];
            $spents = PurchaseRequest::where('purchase_status_id', '<>', 1)->where(function ($query) {
                $query->where('approved_status', 'aprobada')->orWhere('approved_status', 'devolución');
            })->get();
            foreach($spents as $spent){
                $company_data = [];
                $spent_data = [];
                $center_data = [];
                $status_data = [];
                array_push($company_data ,(object) [
                    'company_id' =>  $spent->company_id,
                    'company_name' =>  $spent->company->name
                ]);
    
                array_push($spent_data ,(object) [
                    'spent_id' =>  $spent->spent_id,
                    'spent_name' =>  $spent->spent->concept,
                    'spent_outgo_type' =>  $spent->spent->outgo_type,
                    'spent_expense_type' =>  $spent->spent->expense_type,
                    'spent_product_type' =>  $spent->spent->product_type,
                ]);
                array_push($center_data ,(object) [
                    'center_id' => $spent->center_id,
                    'center_name' =>  $spent->center->name,
                ]);
    
                array_push($status_data ,(object) [
                    'id' => $spent->purchase_status->id,
                    'name' =>  $spent->purchase_status->name,
                    'table_name' =>  $spent->purchase_status->table_name,
                    'type' =>  $spent->purchase_status->type,
                    'status' =>  $spent->purchase_status->status,
                ]);
    
                $approved_by = '';
            
                if($spent->approved_by != null || $spent->approved_by != '' ){
                    $user_approved = User::where('id', intval($spent->approved_by))->get()->last();
    
                    $approved_by =  $user_approved->name;
                }
                
                $admin_approved = '';

                if($spent->admin_approved != null || $spent->admin_approved != '' ){
                    $admin_app = User::where('id', intval($spent->admin_approved))->get()->last();

                    $admin_approved =  $admin_app->name;
                }  

                array_push($data, (object)[
                    'id' => $spent->id,
                    'user_id' => $spent->user_id,
                    'user_name' => $spent->user->name,
                    'company' =>  $company_data,
                    'spent' => $spent_data,
                    'center'  =>  $center_data,
                    'description' => $spent->description,
                    'file' => $spent->file,
                    'commentary' => $spent->commentary,
                    'purchase_status' => $spent->purchase_status->name,
                    'purchase_table_name' => $spent->purchase_status->table_name,
                    'type' => $spent->type,
                    'type_status' => $spent ->type_status,
                    'payment_method_id' => $spent->payment_method->id,
                    'payment_method' => $spent->payment_method->name,  
                    'total' =>$spent->total, 
                    'approved_status' => $spent->approved_status,
                    'approved_by' => $approved_by,
                    'admin_approved' => $admin_approved,
                    'created_at' => $spent->created_at->format('d-m-Y'),
                    'creation_date' => $spent->creation_date ? Carbon::parse($spent->creation_date)->format('d-m-Y') : "Aún no se ha asignado una fecha de creación.",
                ]);
            }

            if($data == null){
                return response()->json(['message' => 'No tienes solicitudes de gasto.'], 409);
            }

            return array(
                'spents' => $data, 
            );

        }else{
            return response()->json([ 'message' => 'Usuario no encontrado'],400);
        }
    }

    public function showBuyerPageRequests($page)
    {        
        $user= auth()->user();

        if($user != null){
            $total_page = 15;
            $data = [];
            $spents = PurchaseRequest::where('purchase_status_id', '<>', 1)->where(function ($query) {
                $query->where('approved_status', 'aprobada')->orWhere('approved_status', 'devolución');
            })->get();

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
                        'spent_product_type' =>  $spents[$i]->spent->product_type,
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
                    $approved_by = '';
              
                    if($spents[$i]->approved_by != null || $spents[$i]->approved_by != '' ){
                        $user_approved = User::where('id', intval($spents[$i]->approved_by))->get()->last();
        
                        $approved_by =  $user_approved->name;
                    }                

                    array_push($data, (object)[
                        'id' => $spents[$i]->id,
                        'user_id' => $spents[$i]->user_id,
                        'user_name' => $spents[$i]->user->name,
                        'company' =>  $company_data,
                        'spent' => $spent_data,
                        'center'  =>  $center_data,
                        'description' => $spents[$i]->description,
                        'file' => $spents[$i]->file,
                        'commentary' => $spents[$i]->commentary,
                        'purchase_status' => $spents[$i]->purchase_status->name,
                        'purchase_table_name' => $spents[$i]->purchase_status->table_name,
                        'type' => $spents[$i]->type,
                        'type_status' => $spents[$i]->type_status,
                        'payment_method_id' => $spents[$i]->payment_method->id,
                        'payment_method' => $spents[$i]->payment_method->name,
                        'total' =>$spents[$i]->total, 
                        'approved_status' => $spents[$i]->approved_status,
                        'approved_by' => $approved_by,
                        'created_at' => $spents[$i]->created_at->format('d-m-Y'),
                    ]);
                }  
            }

            return array(
                'spents' => $data, 
                'pages' => [
                    'actual_page' => $actual_page,
                    'max_pages' => $max_pages,
                    'next_page' => 'caja-chica/ordenes-comprador/'. $next_page,
                    'previus_page'  =>  'caja-chica/ordenes-comprador/'. $previus_page,
                ]
            );

        }else{
            return response()->json(['message' => 'Usuario no encontrado'],400);
        }

    }
}
