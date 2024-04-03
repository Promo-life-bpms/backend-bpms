<?php

namespace App\Http\Controllers;

use App\Models\Eventuales;
use App\Models\EventualesMaquila;
use App\Models\HistoryDevolution;
use App\Models\PaymentMethodInformation;
use App\Models\PurchaseRequest;
use App\Models\Role;
use App\Models\Spent;
use App\Models\spent_money;
use App\Models\User;
use App\Models\UserCenter;
use App\Models\UserRole;
use App\Notifications\BuyersRequestNotification;
use App\Notifications\CreateRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

class PurchaseRequestController extends Controller
{
    //Solicitudes de Jefe directo
    public function show()
    {
        $user = auth()->user();

        $user_center =  UserCenter::where('user_id', $user->id)->get('center_id');
        
        if($user == null){
            return response()->json([
                'message' => "Sesión de usuario expirada"],400);
        }

        $data = [];
        //$spents = PurchaseRequest::whereIn('center_id', $user_center)->get();
        $spents = PurchaseRequest::all();

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
            
            //Obtenemos el id del departamento///
            $department_id = DB::table('purchase_requests')->where('id', $spent->id)->value('department_id'); 
            // Obtener el nombre del departamento
            $department_name = DB::table('departments')->where('id', $department_id)->value('name_department');
            
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
                'department_name' => $department_name,
                
            ]);
        }

        return array(
            'spents' => $data, 
        );
    }

    public function DepartmentPurchase()
    {
        // Obtener el usuario autenticado
        $user = auth()->user();
    
        // Obtener el ID del departamento del usuario autenticado desde la tabla manager_has_departments
        $department_ids = DB::table('manager_has_departments')
                            ->where('id_user', $user->id)
                            ->pluck('id_department');
    
        // Verificar si el usuario autenticado es gerente de algún departamento
        if ($department_ids->isEmpty()) {
            $spents = PurchaseRequest::where('user_id', $user->id)->get();

        }else{
            $spents = PurchaseRequest::whereIn('department_id', $department_ids)->orWhere('user_id', $user->id)->get();
        }
        $data = [];

        foreach ($spents as $spent) {
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

            //Obtenemos el id del departamento///
            $department_id = DB::table('purchase_requests')->where('id', $spent->id)->value('department_id'); 
            // Obtener el nombre del departamento
            $department_name = DB::table('departments')->where('id', $department_id)->value('name_department');
        
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
                'department_name' => $department_name,
            ]);
        }
    
        return ['spents' => $data];    
    }

    public function DepartmentPage($page){
        // Obtener el usuario autenticado
        $user = auth()->user();
    
        // Obtener el ID del departamento del usuario autenticado desde la tabla manager_has_departments
        $department_ids = DB::table('manager_has_departments')
                            ->where('id_user', $user->id)
                            ->pluck('id_department');
        
        //dd($department_ids);

        $rolcajachica = DB::table('role_user')->where('user_id', $user->id)->value('role_id');
        $rolcajachi = DB::table('roles')->where('id', 32)->value('id');
    
        // Verificar si el usuario autenticado es gerente de algún departamento
        if ($department_ids->isEmpty()) {
            $id = DB::table('purchase_requests')->where('id', $page)->value('user_id');
            // Si el usuario autenticado no es gerente de ningún departamento, retornar un mensaje de error o algo apropiado
            if($id == $user->id){

            $spent = PurchaseRequest::where('id',$page)->get()->last();

            }elseif($rolcajachica == $rolcajachi){
                $status = DB::table('purchase_requests')->where('id', $page)->value('approved_status');
                if($status == "aprobada"){
                    $spent = PurchaseRequest::where('id', $page)->get()->last();
                }else{
                    return response()->json(['message' => 'Esta solicitud no fue aprobada']);
                }   
            }else{
                return 0;
            }
        }
        else{
            $idDepartment = DB::table('purchase_requests')->where('id', $page)->value('department_id');
            $DepartmentManager = DB::table('manager_has_departments')->where('id_user', $user->id)->pluck('id_department')->toArray();
            if (in_array($idDepartment, $DepartmentManager)) {
                $spent = PurchaseRequest::where('id', $page)->get()->last();
            } else {
                return response()->json(['message' => 'No eres Manager de este departamento.', 'status' => 404], 404);
            }
        }

        /*$department_id_solicitud = DB::table('purchase_requests')->where('id', $page)->value('department_id');

        // Obtener el id del usuario manager del departamento asociado con la solicitud
        $manager_id = DB::table('manager_has_departments')
            ->where('id_department', $department_id_solicitud)
            ->value('id_user');
        
        $userdetail = DB::table('user_details')->where('id_user', $user->id)->value('id_department');*/

        //if($department_id_solicitud  == $userdetail){
            
            
            $data = [];
            
            if(isset($spent->spent_id )){
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

                //Obtenemos el id del departamento///
                $department_id = DB::table('purchase_requests')->where('id', $spent->id)->value('department_id'); 
                // Obtener el nombre del departamento
                $department_name = DB::table('departments')->where('id', $department_id)->value('name_department');
                // Obtener información de los eventuales
                $eventuales = DB::table('eventuales')->where('purchase_id', $page)->pluck('eventuales')->toArray();

                // Inicializar el array resultante
                $event = [];
                
                foreach ($eventuales as $jsonString) {
                    $datos = json_decode($jsonString, true);
                    
                    foreach ($datos as $item) {
                        // Verificar si 'company' es "undefined"
                        if ($item['company'] === "undefined") {
                            $item['company_name'] = $spent->company->name;
                        } else {
                            // Obtener el ID de la compañía desde los eventuales
                            $companyId = $item['company'];
                            // Buscar el nombre de la compañía en la tabla tempory_company
                            $companyName = DB::table('tempory_company')->where('id', $companyId)->value('name');
                            // Agregar el nombre de la compañía al array original
                            $item['company_name'] = $companyName;
                        }
                        // Agregar cada objeto al resultado
                        $event[] = $item;
                    }
                }

                $returnmoneyexcess = DB::table('exchange_returns')->where('purchase_id', $page)->select('id','total_return', 'status', 'confirmation_datetime', 
                                                                                        'confirmation_user_id', 'description','file_exchange_returns', 
                                                                                        'return_user_id','created_at')->get()->toArray();
                
                $returnmoney = [];

                foreach ($returnmoneyexcess as $returnmoney){
                    $returnmoney->created_at = date('d-m-Y H:i:s', strtotime($returnmoney->created_at));
                    
                    if($returnmoney->confirmation_datetime != null){
                        $returnmoney->confirmation_datetime = date('d-m-Y H:i:s', strtotime($returnmoney->confirmation_datetime));
                    }

                    $user = DB::table('users')->where('id', $returnmoney->confirmation_user_id)->select('name')->first();
                    $returnmoney->confirmation_user_id = $user ? $user->name : null;
                    $username = DB::table('users')->where('id', $returnmoney->return_user_id)->select('name')->first();
                    $returnmoney->return_user_id = $username ? $username->name : null;
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
                    'department_name' => $department_name,
                    'event' => $event,
                    'returnmoney' => $returnmoney,
                ]);
            }
            
            return response()->json(['data' => $data]);
        /*}else{
            return response()->json(['message' => 'No eres Manager de este departamento.', 'status' => 404], 404);
        }*/
    }

    public function approvedDepartment(Request $request){
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();
        $department_id_solicitud = DB::table('purchase_requests')->where('id', $request->id)->value('department_id');

        // Obtener el id del usuario manager del departamento asociado con la solicitud
        $manager_id = DB::table('manager_has_departments')
            ->where('id_department', $department_id_solicitud)
            ->value('id_user');
        
        ///Si el usuario logueado es manager aprueba///
        if($user->id == $manager_id){
            $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'approved_status' => 'en aprobación por administrador',
                'approved_by' => $user->id,
                'purchase_status_id' => 1
            ]);
            
            $role_buyer = Role::where('name', 'caja_chica')->get()->last();
            $user_role = UserRole::where('role_id', $role_buyer->id)->get();
            $spent = Spent::where('id',$purchase_request->spent_id)->get()->first();
            
            foreach($user_role as $role){
                $users_to_send_mail = User::where('id',$role->user_id)->get()->last();
                
                $title = 'Nueva solicitud de compra';
                $message = 'Haz recibido una nueva solicitud de compras.';
                
                try {
                    Notification::route('mail', $users_to_send_mail->email)
                    ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
                } catch (\Exception $e) {
                    return $e;
                }
            }
            return response()->json(['message' => "Solicitud aprobada satisfactoriamente"], 200);
        }
        else{
            return response()->json(['message' => 'No eres Manager de este departamento, por lo tanto no puedes autorizar la solicitud.', 'status' => 404], 404);
        }

    }
    
      //Solicitudes de Administrador
      public function showAdministrador()
      {
        $user = auth()->user();
        
        if($user == null){
            return response()->json([
                'message' => "Sesión de usuario expirada"
            ],400);
        }
  
        $data = [];
        $spents = PurchaseRequest::where('approved_status', '<>','cancelada')->get();
          
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

            //Obtenemos el id del departamento///
            $department_id = DB::table('purchase_requests')->where('id', $spent->id)->value('department_id'); 
            // Obtener el nombre del departamento
            $department_name = DB::table('departments')->where('id', $department_id)->value('name_department');


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
                'department_name' => $department_name,
            ]);
        }
                
        return array(
            'spents' => $data, 
        );
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if($user == null){
            return response()->json([
                'message' => "Sesión de usuario expirada"
            ],400);
        }
        $request->validate([
            'spent_id' => 'required',
            'type'=> 'required',
            'total' => 'required',
        ]);
        
        $request->validate([
            'eventuales' => 'nullable|array',
            'eventuales.*.name' => 'required|string',
            'eventuales.*.pay' => 'required|numeric',
            'eventuales.*.company' => 'required'
        ]);

        $spent = Spent::where('id',$request->spent_id)->get()->last();
        if($spent == null){
            $center_id = 1;
        }else{
            $center_id = $spent->center_id;
        }
        $path = '';
        if ($request->hasFile('file')) {
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->clientExtension();
            $fileNameToStore = time(). $filename . '.' . $extension;
            $path= $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
        }

        ///OBTENEMOS LA COMPAÑIA DEL USUARIO LOGUEADO///
        $info = DB::table('user_details')->where('id_user', $user->id)->first();
        $id_company = $info->id_company;
        $id_department = $info->id_department;


        $product_type = Spent::where('id', $request->spent_id)->get()->last();

        $create_spent = new PurchaseRequest();
        $create_spent->user_id = $user->id;
        $create_spent->company_id = $id_company;
        $create_spent->spent_id = $request->spent_id;
        $create_spent->center_id = $center_id;
        $create_spent->department_id = $id_department;
        $create_spent->description = $request->description;
        $create_spent->file = $path;
        $create_spent->commentary = '';
        $create_spent->purchase_status_id = 1;
        $create_spent->type = strtolower($product_type->product_type); 
        $create_spent->type_status = 'normal';
        $create_spent->payment_method_id = 4;
        $create_spent->total = $request->total;
        $create_spent->sign= null;
        $create_spent->approved_status = 'pendiente';
        $create_spent->approved_by = null;
        $create_spent->save();

        ///AQUI SE CREAN LOS EVENTUALES///
        $id = $create_spent->id;

        if ($request->eventuales) {
            $eventualesData = [
                'eventuales' => json_encode($request->eventuales),
                'purchase_id' => $id,
            ];
            Eventuales::create($eventualesData);
        }        
        ///////////////////////////////////
        
        $users_to_send_mail = UserCenter::where('center_id',$center_id)->get();

        if(count($users_to_send_mail) != 0){
            $spent = Spent::where('id',$request->spent_id)->get()->first();

            foreach($users_to_send_mail as $user_mail){
                $user = User::where('id', $user_mail->user_id)->get()->last();

                try {
                    Notification::route('mail', $user->email)
                    ->notify(new CreateRequestNotification($spent->concept, $spent->center->name, $request->total));
                } catch (\Exception $e) {
                  return $e;
                }
            }
        
        }

        return response()->json(['message' => "Registro guardado satisfactoriamente"],200);
    }

    public function editdate(Request $request)
    {
        $this->validate($request,[
            'id' => 'required',
            'creation_date' => 'required',
        ]);

        $date = Carbon::parse($request->creation_date)->format('Y-m-d');

        DB::table('purchase_requests')->where('id', $request->id)->update(['creation_date' => $date]);
        return response(['message' => '¡LISTO!'],200);
    }

    public function updatemoney(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_purchase' => 'required',
            'total_update' => 'required'
        ]);

        $rolcajachica = DB::table('role_user')->where('user_id', $user->id)->value('role_id');
        $rolcajachi = DB::table('roles')->where('id', 32)->value('id');
        
        if ($rolcajachica == $rolcajachi) {
            $method = DB::table('purchase_requests')->where('id', $request->id_purchase)->select('payment_method_id')->first();

        if ($method->payment_method_id == 1) {
            ///OBTENEMOS EL PRIMER DÍA DEL MES Y EL ÚLTIMO///        
            $primerDiaDelMes = Carbon::now()->startOfMonth();
            $ultimoDiaDelMes = Carbon::now()->endOfMonth();
    
            // Verificar si la fecha actual está dentro del mes
            if (Carbon::now()->between($primerDiaDelMes, $ultimoDiaDelMes)) {
                // Si estamos en el mes actual, realizar la suma
                //presupuestomensual == MonthlyBudget
                $MonthlyBudget = DB::table('estimation_small_box')
                    ->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                    ->sum('total');
            }
            
            ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
            //gastosmentuales == monthlyexpenses
            $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
                $query->where(function ($subquery) {
                    $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                })->orWhere(function ($subquery) {
                    $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                })->orWhere(function ($subquery) {
                    $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                })->orWhere(function ($subquery){
                    $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'en proceso')->where('payment_method_id', '=', 1);
                })->orWhere(function($subquery){
                    $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'rechazada')->where('payment_method_id', '=', 1);
                });
            })->sum('total');
            
            $AvailableBudget =number_format($MonthlyBudget - $MonthlyExpenses, 2, '.', '' );
            
            $purchase = DB::table('purchase_requests')->where('id', $request->id_purchase)->first();
            if ($purchase) {
                // Obtener el total anterior de la compra
                $total_anterior = $purchase->total;
                // Calcular la diferencia para llegar al nuevo total
                $difference = $request->total_update - $total_anterior;

                if($difference > $AvailableBudget){
                    return response()->json(['message' => 'No tienes fondos suficientes'], 400);
                }
                else{
                    DB::table('purchase_requests')->where('id', $request->id_purchase)->update([
                        'total' => $request->total_update
                    ]);
                }    
            }
        }else{
            DB::table('purchase_requests')->where('id', $request->id_purchase)->update([
                'total' => $request->total_update
            ]);
        }
        return response()->json(['message' => 'Se actualizó con éxito la cantidad'], 200);

        }else{
            return response()->json(['message' => "No tienes permiso."],404);
        }   
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if($user == null){
            return response()->json([
                'message' => "Sesión de usuario expirada"
            ], 400);
        }

        $request->validate([
            'id' => 'required',
            'company_id' => 'required',
            'spent_id' => 'required',
            'center_id' => 'required',
            'description' => 'required',
            'payment_method_id' => 'required',
            'type' => 'required',
            'total' => 'required',
        ]);

        $spent = PurchaseRequest::where('id',$request->id)->get()->last();

        $path = $spent->file;

        if ($request->hasFile('file')) {
            File::delete($spent->file);
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->clientExtension();
            $fileNameToStore = time(). $filename . '.' . $extension;
            $path= $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
        }

        DB::table('purchase_requests')->where('id',$request->id)->update([
            'company_id' => $request->company_id,
            'spent_id' => $request->spent_id,
            'center_id' => $request->center_id,
            'description' => $request->description,
            'file' => $path,
            'commentary' => $request->commentary,
            'type' => $request->type,
            'payment_method_id' => $request->payment_method_id,
            'total' => $request->total,
            'approved_by' => $user->id
        ]);

        return response()->json(['message' => "Registro actualizado satisfactoriamente"], 200);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['message' => "Producto no encontrado"], 400);
        }

           
        File::delete($purchase_request->file);

        $purchase_request->delete();
        
        return response()->json(['message' => "Registro eliminado satisfactoriamente"], 200);
        
    }

    public function approved(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();


        DB::table('purchase_requests')->where('id',$request->id)->update([
            'approved_status' => 'en aprobación por administrador',
            'approved_by' => $user->id,
            'purchase_status_id' => 1
        ]);
        
        $role_buyer = Role::where('name', 'compras')->get()->last();

        $user_role = UserRole::where('role_id', $role_buyer->id)->get();
        $spent = Spent::where('id',$purchase_request->spent_id)->get()->first();

        foreach($user_role as $role){
            $users_to_send_mail = User::where('id',$role->user_id)->get()->last();

            $title = 'Nueva solicitud de compra';
            $message = 'Haz recibido una nueva solicitud de compras.';

            try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            }
        }

        return response()->json(['message' => "Solicitud aprobada satisfactoriamente"], 200);
    }

    public function approvedByAdmin(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if($purchase_request->approved_status == 'aprobada'){
            return response()->json(['message' => 'Solicitud aprobada']);        
        }
        elseif($purchase_request->approved_status == 'en aprobación por administrador'){
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'approved_status' => 'aprobada',
                'admin_approved' => $user->id,
                'purchase_status_id' => 2
            ]);
        }else{
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'approved_status' => 'aprobada',
                'admin_approved' => $user->id,
                'approved_by' => $user->id,
                'purchase_status_id' => 2
            ]);
        }

        $role_buyer = Role::where('name', 'compras')->get()->last();

        $user_role = UserRole::where('role_id', $role_buyer->id)->get();
        $spent = Spent::where('id',$purchase_request->spent_id)->get()->first();

        foreach($user_role as $role){
            $users_to_send_mail = User::where('id',$role->user_id)->get()->last();

            $title = 'Nueva solicitud de compra';
            $message = 'Haz recibido una nueva solicitud de compras.';

            /* try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            } */
        }

        return response()->json(['message' => "Solicitud aprobada satisfactoriamente"], 200);
       
    }

    public function rejected(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();
        
       if($purchase_request->approved_status == 'pendiente'){

            DB::table('purchase_requests')->where('id',$request->id)->update([
                'approved_status' => 'rechazada',
                'approved_by' => $user->id,
                'type_status' => 'cancelado',
            ]);
        }else if($purchase_request->approved_status == 'en aprobación por administrador'){
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'approved_status' => 'rechazada',
                'admin_approved' => $user->id,
                'type_status' => 'cancelado',
            ]);
        };
        
        $users_to_send_mail = User::where('id',$purchase_request->user_id)->get()->last();

        $spent = Spent::where('id',$purchase_request->spent_id)->get()->first();

        $user = User::where('id', $spent->user_id)->get()->last();
            
        $title = 'Solicitud rechazada';
        $message = 'Tu solicitud ha sido rechazada, revisa la información e intenta enviarla nuevamente.';

        try {
            Notification::route('mail', $users_to_send_mail->email)
            ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
        } catch (\Exception $e) {
            return $e;
        }
    
        return response()->json(['message' => "Solicitud rechazada satisfactoriamente"], 200);
        
    }

    public function confirmDelivered(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null ){
            return response()->json(['message' => "Producto no encontrado"], 400);
        }

        if($purchase_request->purchase_status_id == 2){
           
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'purchase_status_id' => 3,
            ]);

            $metododepago = DB::table('purchase_requests')->where('id', $request->id)->select('payment_method_id')->first();

            if($metododepago->payment_method_id == 1){
                ////// aqui se crea///
                spent_money::create([
                    'id_user' => $user->id,
                    'id_pursache_request' => $request->id,
                ]);
            }   
            return response()->json(['message' => "Pedido confirmado"], 200);
        }else{
            return response()->json(['message' => "No se ha podido confirmar el pedido, verifica que haya sido aprobado para compra o no ha sido entregado."], 400);
        }
    }

    public function confirmReceived(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if($purchase_request == null){
            return response()->json(['message' => "Orden no encontrada"], 400);
        }

        if($purchase_request->purchase_status_id == 3 &&  $purchase_request->approved_status == 'aprobada'){
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'purchase_status_id' => 4,
            ]);

            $users_to_send_mail = User::where('id',$purchase_request->user_id)->get()->last();

            $spent = Spent::where('id',$purchase_request->spent_id)->get()->first();
                
            $title = 'Haz recibido el Pedido';
            $message = 'Se ha confirmado que haz recibido el pedido';

            try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            }
    
            return response()->json(['message' => "Se ha confirmado que el pedido fue recibido"], 200);
        }else{
            return response()->json(['message' => "No se ha podido realizar la confirmación del pedido, verifica que la orden haya sido aprobada y confirmada de entrega"], 400);
        }       
    }

    public function createDevolution(Request $request)
    {   
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['message' => "Orden no encontrada"], 400);
        }

        if($purchase_request->purchase_status_id == 3 || $purchase_request->purchase_status_id == 4){
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'purchase_status_id' => 5,
                'type_status' => 'en proceso',
                'approved_status' => 'devolución'
            ]);
            
            $users_to_send_mail = User::where('id',$purchase_request->user_id)->get()->last();

            $spent = Spent::where('id',$purchase_request->spent_id)->get()->first();
                
            $title = 'Devolución de Pedido';
            $message = 'Se ha realizado la devolución del pedido';

            try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            }
    
            return response()->json(['message' => "Devolución en proceso"], 200);
        }
    }

    public function confirmationDevolution(Request $request){

        $user = auth()->user();

        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['message' => "Orden no encontrada"], 400);
        }

        if($purchase_request->purchase_status_id == 5){
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'type_status' => 'normal',
            ]);
            

            ////CREAR HISTORIAL DE LA DEVOLUCIÓN DEL HISTORIAL////
            $obtener_total = DB::table('purchase_requests')->where('id', $request->id)->select('total')->get();
            $total_return = $obtener_total->first()->total;
            HistoryDevolution::create([
                'total_return' => $total_return,
                'status' => 'Devolución completada',
                'id_user' => $user->id,
                'id_purchase' => $request->id, 
            ]);

            return response()->json(['message' => "Devolución realizada"], 200);
        }
    }

    public function cancelationDevolution(Request $request){
        $user = auth()->user();

        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['message' => "Orden no encontrada"], 400);
        }

        if($purchase_request->purchase_status_id == 5){
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'type_status' => 'rechazada',
            ]);

            ////CREAR HISTORIAL DE LA DEVOLUCIÓN DEL HISTORIAL////
            $obtener_total = DB::table('purchase_requests')->where('id', $request->id)->select('total')->get();
            $total_return = $obtener_total->first()->total;
            HistoryDevolution::create([
                'total_return' => $total_return,
                'status' => 'Devolución rechazada',
                'id_user' => $user->id,
                'id_purchase' => $request->id, 
            ]);
            
            return response()->json(['message' => "Devolución rechazada"], 200);
        }
    }

    public function createCancellation(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['message' => "Producto no encontrado"], 400);
        }
        
        if($purchase_request->purchase_status_id == 2){
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'type_status' => 'cancelado',
            ]);

            $users_to_send_mail = User::where('id',$purchase_request->user_id)->get()->last();

            $spent = Spent::where('id',$purchase_request->spent_id)->get()->first();
                
            $title = 'Cancelación de Pedido';
            $message = 'Se ha realizado la cancelación del pedido';

            try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            }
            return response()->json(['message' => "Cancelación realizada"], 200);
        }else{
            return response()->json(['message' => "No es posible realizar una cancelación una vez que recibas el producto; se debe realizar una devolución"], 400);
        }
    }
    
    public function showPage($page)
    {
        $spent = PurchaseRequest::where('id',$page)->get()->last();

        $data = [];
        if(isset($spent->spent_id )){

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
            
            //Obtenemos el id del departamento///
            $department_id = DB::table('purchase_requests')->where('id', $spent->id)->value('department_id'); 
            // Obtener el nombre del departamento
            $department_name = DB::table('departments')->where('id', $department_id)->value('name_department');

            // Obtener información de los eventuales
            $eventuales = DB::table('eventuales')->where('purchase_id', $page)->pluck('eventuales')->toArray();

            // Inicializar el array resultante
            $event = [];

            foreach ($eventuales as $jsonString) {
                $datos = json_decode($jsonString, true);
            
                foreach ($datos as $item) {
                    // Verificar si 'company' es "undefined"
                    if ($item['company'] === "undefined") {
                        $item['company_name'] = $spent->company->name;
                    } else {
                        // Obtener el ID de la compañía desde los eventuales
                        $companyId = $item['company'];
                        // Buscar el nombre de la compañía en la tabla tempory_company
                        $companyName = DB::table('tempory_company')->where('id', $companyId)->value('name');
                        // Agregar el nombre de la compañía al array original
                        $item['company_name'] = $companyName;
                    }
                    // Agregar cada objeto al resultado
                    $event[] = $item;
                }
            }

            $returnmoneyexcess = DB::table('exchange_returns')->where('purchase_id', $page)->select('id','total_return', 'status', 'confirmation_datetime', 
                                                                                        'confirmation_user_id', 'description','file_exchange_returns', 
                                                                                        'return_user_id','created_at')->get()->toArray();
            
            $returnmoney = [];

            foreach ($returnmoneyexcess as $returnmoney){
                $returnmoney->created_at = date('d-m-Y H:i:s', strtotime($returnmoney->created_at));

                if($returnmoney->confirmation_datetime != null){
                    $returnmoney->confirmation_datetime = date('d-m-Y H:i:s', strtotime($returnmoney->confirmation_datetime));
                }

                $user = DB::table('users')->where('id', $returnmoney->confirmation_user_id)->select('name')->first();
                $returnmoney->confirmation_user_id = $user ? $user->name : null;

                $username = DB::table('users')->where('id', $returnmoney->return_user_id)->select('name')->first();
                $returnmoney->return_user_id = $username ? $username->name : null;
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
                'department_name' => $department_name,
                'event' => $event,
                'returnmoney' => $returnmoney,
            ]);
        }

        return response()->json(['data' => $data]);
    }
    
    public function updatePaymentMethod(Request $request)
    {
        $user = auth()->user();
    
        if($user == null){
            return response()->json([
                'message' => "Sesión de usuario expirada"
            ],400);
        }
            
        $request->validate([
            'id' => 'required',
            'payment_method_id' => 'required',
        ]);

        $rolcajachica = DB::table('role_user')->where('user_id', $user->id)->value('role_id');
        $rolcajachi = DB::table('roles')->where('id', 32)->value('id');
        
        if ($rolcajachica == $rolcajachi) {
             ///VERIFICAMOS SI EL METODO DE PAGO QUE SE USUARA ES EFECTIVO///
        if($request->payment_method_id == 1){
            $pago = DB::table('purchase_requests')->where('id', $request->id)->select('total')->first();
    
            ///OBTENEMOS EL PRIMER DÍA DEL MES Y EL ÚLTIMO///        
            $primerDiaDelMes = Carbon::now()->startOfMonth();
            $ultimoDiaDelMes = Carbon::now()->endOfMonth();
    

            // Verificar si la fecha actual está dentro del mes
            if (Carbon::now()->between($primerDiaDelMes, $ultimoDiaDelMes)) {
                // Si estamos en el mes actual, realizar la suma
                //presupuestomensual == MonthlyBudget
                $MonthlyBudget = DB::table('estimation_small_box')
                                    ->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->sum('total');
            }
                    
            ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
            ///gastosmentuales == monthlyexpenses///
            $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
                $query->where(function ($subquery) {
                    $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                })->orWhere(function ($subquery) {
                    $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                })->orWhere(function ($subquery) {
                    $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                })->orWhere(function ($subquery){
                    $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'en proceso')->where('payment_method_id', '=', 1);
                })->orWhere(function($subquery){
                    $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'rechazada')->where('payment_method_id', '=', 1);
                });
            })->sum('total');

            $AvailableBudget =number_format($MonthlyBudget - $MonthlyExpenses, 2, '.', '' );

            if ($pago) {
                if($pago->total > $AvailableBudget){
                    return response()->json(['message' => 'No tienes fondos suficientes'], 400);
                }
                else{
                    DB::table('purchase_requests')->where('id',$request->id)->update([
                        'payment_method_id' => $request->payment_method_id,
                    ]);
                }
            } else {
                return response()->json(['message' =>'No se encontró el pago correspondiente'], 400); 
            }
        }else{
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'payment_method_id' => $request->payment_method_id,
            ]);
        }
        return response()->json(['message' => "Método de pago actualizado correctamente"],200);
        }else{
            return response()->json(['message' => "No tienes permiso."],404);
        }
    

       
    }
}
