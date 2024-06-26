<?php

namespace App\Http\Controllers;

use App\Models\EstimationSmallBox;
use App\Models\Eventuales;
use App\Models\EventualesMaquila;
use App\Models\ExchangeReturn;
use App\Models\HistoryDevolution;
use App\Models\LackOfMoneyEventuals;
use App\Models\PaymentMethodInformation;
use App\Models\PurchaseRequest;
use App\Models\ReturnMoneyFromEventualities;
use App\Models\Role;
use App\Models\Spent;
use App\Models\spent_money;
use App\Models\User;
use App\Models\UserCenter;
use App\Models\UserRole;
use App\Notifications\BuyersRequestNotification;
use App\Notifications\ConfirmedReceipt;
use App\Notifications\CreatePurchaseRequest;
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

        if ($user == null) {
            return response()->json([
                'message' => "Sesión de usuario expirada"
            ], 400);
        }

        $data = [];
        //$spents = PurchaseRequest::whereIn('center_id', $user_center)->get();
        $spents = PurchaseRequest::all();

        foreach ($spents as $spent) {
            $company_data = [];
            $spent_data = [];
            $center_data = [];
            $status_data = [];
            array_push($company_data, (object) [
                'company_id' =>  $spent->company_id,
                'company_name' =>  $spent->company->name
            ]);

            array_push($spent_data, (object) [
                'spent_id' =>  $spent->spent_id,
                'spent_name' =>  $spent->spent->concept,
                'spent_outgo_type' =>  $spent->spent->outgo_type,
                'spent_expense_type' =>  $spent->spent->expense_type,
                'spent_product_type' =>  $spent->spent->product_type,
            ]);
            array_push($center_data, (object) [
                'center_id' => $spent->center_id,
                'center_name' =>  $spent->center->name,
            ]);

            array_push($status_data, (object) [
                'id' => $spent->purchase_status->id,
                'name' =>  $spent->purchase_status->name,
                'table_name' =>  $spent->purchase_status->table_name,
                'type' =>  $spent->purchase_status->type,
                'status' =>  $spent->purchase_status->status,
            ]);

            $approved_by = '';

            if ($spent->approved_by != null || $spent->approved_by != '') {
                $user_approved = User::where('id', intval($spent->approved_by))->get()->last();

                $approved_by =  $user_approved->name;
            }

            $admin_approved = '';

            if ($spent->admin_approved != null || $spent->admin_approved != '') {
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
                'total' => $spent->total,
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
        } else {
            $spents = PurchaseRequest::whereIn('department_id', $department_ids)->orWhere('user_id', $user->id)->get();
        }

        /////VERIFICAMOS SI EL USUARIO LOGUEADO TIENE SOLICITUDES/////////
        if ($spents->isEmpty()) {
            return response()->json(['message' => 'No hay solicitudes disponibles'], 404);
        }

        $data = [];

        foreach ($spents as $spent) {
            $company_data = [];
            $spent_data = [];
            $center_data = [];
            $status_data = [];
            array_push($company_data, (object) [
                'company_id' =>  $spent->company_id,
                'company_name' =>  $spent->company->name
            ]);

            array_push($spent_data, (object) [
                'spent_id' =>  $spent->spent_id,
                'spent_name' =>  $spent->spent->concept,
                'spent_outgo_type' =>  $spent->spent->outgo_type,
                'spent_expense_type' =>  $spent->spent->expense_type,
                'spent_product_type' =>  $spent->spent->product_type,
            ]);
            array_push($center_data, (object) [
                'center_id' => $spent->center_id,
                'center_name' =>  $spent->center->name,
            ]);

            array_push($status_data, (object) [
                'id' => $spent->purchase_status->id,
                'name' =>  $spent->purchase_status->name,
                'table_name' =>  $spent->purchase_status->table_name,
                'type' =>  $spent->purchase_status->type,
                'status' =>  $spent->purchase_status->status,
            ]);

            $approved_by = '';

            if ($spent->approved_by != null || $spent->approved_by != '') {
                $user_approved = User::where('id', intval($spent->approved_by))->get()->last();

                $approved_by =  $user_approved->name;
            }

            $admin_approved = '';

            if ($spent->admin_approved != null || $spent->admin_approved != '') {
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
                'total' => $spent->total,
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

    public function DepartmentPage($page)
    {
        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener el ID del departamento del usuario autenticado desde la tabla manager_has_departments
        $department_ids = DB::table('manager_has_departments')
            ->where('id_user', $user->id)
            ->pluck('id_department');

        //dd($department_ids);

        $rol = DB::table('role_user')->where('user_id', $user->id)->value('role_id');
        $Adquisiciones = DB::table('roles')->where('id', 15)->value('id');
        $rolcajachi = DB::table('roles')->where('id', 14)->value('id');
        $administrador = DB::table('roles')->where('id', 1)->value('id');
        // Verificar si el usuario autenticado es gerente de algún departamento
        if ($department_ids->isEmpty()) {
            $id = DB::table('purchase_requests')->where('id', $page)->value('user_id');
            if ($id == $user->id) {
                $spent = PurchaseRequest::where('id', $page)->get()->last();
            } elseif ($rol == $rolcajachi || $rol == $Adquisiciones) {
                $status = DB::table('purchase_requests')->where('id', $page)->value('approved_status');
                if (trim($status) != "rechazada" && trim($status) != "en proceso" && trim($status) != "pendiente") {
                    $spent = PurchaseRequest::where('id', $page)->get()->last();
                } else {
                    return response()->json(['message' => 'Esta solicitud no fue aprobada']);
                }
            } elseif ($rol == $administrador) {
                $spent = PurchaseRequest::where('id', $page)->get()->last();
            } else {
                return 0;
            }
        } else {
            $idDepartment = DB::table('purchase_requests')->where('id', $page)->value('department_id');
            $DepartmentManager = DB::table('manager_has_departments')->where('id_user', $user->id)->pluck('id_department')->toArray();
            $managerAdmin = DB::table('manager_has_departments')->where('id_user', $user->id)->value('id_department');
            if (in_array($idDepartment, $DepartmentManager)) {
                $spent = PurchaseRequest::where('id', $page)->get()->last();
            } elseif (($managerAdmin == 1) && $rolcajachi && $Adquisiciones) {
                $status = DB::table('purchase_requests')->where('id', $page)->value('approved_status');
                if (trim($status) != "rechazada" && trim($status) != "en proceso" && trim($status) != "pendiente") {
                    $spent = PurchaseRequest::where('id', $page)->get()->last();
                } else {
                    return response()->json(['message' => 'Esta solicitud no fue aprobada']);
                }
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

        if (isset($spent->spent_id)) {
            $company_data = [];
            $spent_data = [];
            $center_data = [];
            $status_data = [];

            array_push($company_data, (object) [
                'company_id' =>  $spent->company_id,
                'company_name' =>  $spent->company->name
            ]);

            array_push($spent_data, (object) [
                'spent_id' =>  $spent->spent_id,
                'spent_name' =>  $spent->spent->concept,
                'spent_outgo_type' =>  $spent->spent->outgo_type,
                'spent_expense_type' =>  $spent->spent->expense_type,
                'spent_product_type' =>  $spent->spent->product_type,
            ]);

            array_push($center_data, (object) [
                'center_id' => $spent->center_id,
                'center_name' =>  $spent->center->name,
            ]);

            array_push($status_data, (object) [
                'id' => $spent->purchase_status->id,
                'name' =>  $spent->purchase_status->name,
                'table_name' =>  $spent->purchase_status->table_name,
                'type' =>  $spent->purchase_status->type,
                'status' =>  $spent->purchase_status->status,
            ]);

            $approved_by = '';

            if ($spent->approved_by != null || $spent->approved_by != '') {
                $user_approved = User::where('id', intval($spent->approved_by))->get()->last();
                $approved_by =  $user_approved->name;
            }

            $admin_approved = '';

            if ($spent->admin_approved != null || $spent->admin_approved != '') {
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

            $returnmoneyexcess = DB::table('exchange_returns')->where('purchase_id', $page)->select(
                'id',
                'total_return',
                'previous_total',
                'status',
                'confirmation_datetime',
                'confirmation_user_id',
                'description',
                'file_exchange_returns',
                'return_user_id',
                'created_at'
            )->get()->toArray();

            $returnmoney = [];
            foreach ($returnmoneyexcess as $returnmoney) {
                $returnmoney->created_at = date('d-m-Y H:i:s', strtotime($returnmoney->created_at));

                if ($returnmoney->confirmation_datetime != null) {
                    $returnmoney->confirmation_datetime = date('d-m-Y H:i:s', strtotime($returnmoney->confirmation_datetime));
                }

                $user = DB::table('users')->where('id', $returnmoney->confirmation_user_id)->select('name')->first();
                $returnmoney->confirmation_user_id = $user ? $user->name : null;
                $username = DB::table('users')->where('id', $returnmoney->return_user_id)->select('name')->first();
                $returnmoney->return_user_id = $username ? $username->name : null;
            }
    
            $moremoney = DB::table('lack_of_money_eventuals')->where('id_purchase', $page)->select(
                'id',
                'id_applicant_person',
                'id_person_who_delivers',
                'description',
                'file',
                'previous_total',
                'current_total',
                'status',
                'confirmation_datetime',
                'created_at'
            )->get()->toArray();

            $moremoneyeventual = [];
            foreach ($moremoney as $moremoneyeventual) {
                $moremoneyeventual->created_at = date('d-m-Y H:i:s', strtotime($moremoneyeventual->created_at));

                if ($moremoneyeventual->confirmation_datetime != null) {
                    $moremoneyeventual->confirmation_datetime = date('d-m-Y H:i:s', strtotime($moremoneyeventual->confirmation_datetime));
                }
                $user = DB::table('users')->where('id', $moremoneyeventual->id_applicant_person)->select('name')->first();
                $moremoneyeventual->id_applicant_person = $user ? $user->name : null;
                $username = DB::table('users')->where('id', $moremoneyeventual->id_person_who_delivers)->select('name')->first();
                $moremoneyeventual->id_person_who_delivers = $username ? $username->name : null;
            }

            $returnmoneyeventuals = DB::table('return_money_from_eventualities')->where('id_purchase', $page)->select(
                'id',
                'id_applicant_person',
                'id_person_who_delivers',
                'description',
                'file',
                'previous_total',
                'current_total',
                'status',
                'confirmation_datetime',
                'created_at'
            )->get()->toArray();
            $lessmoneyeventual = [];
            foreach ($returnmoneyeventuals as $lessmoneyeventual) {
                $lessmoneyeventual->created_at = date('d-m-Y H:i:s', strtotime($lessmoneyeventual->created_at));

                if ($lessmoneyeventual->confirmation_datetime != null) {
                    $lessmoneyeventual->confirmation_datetime = date('d-m-Y H:i:s', strtotime($lessmoneyeventual->confirmation_datetime));
                }

                $user = DB::table('users')->where('id', $lessmoneyeventual->id_applicant_person)->select('name')->first();
                $lessmoneyeventual->id_applicant_person = $user ? $user->name : null;
                $username = DB::table('users')->where('id', $lessmoneyeventual->id_person_who_delivers)->select('name')->first();
                $lessmoneyeventual->id_person_who_delivers = $username ? $username->name : null;
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
                'total' => $spent->total,
                'approved_status' => $spent->approved_status,
                'approved_by' => $approved_by,
                'admin_approved' => $admin_approved,
                'created_at' => $spent->created_at->format('d-m-Y'),
                'creation_date' => $spent->creation_date ? Carbon::parse($spent->creation_date)->format('d-m-Y') : "Aún no se ha asignado una fecha de creación.",
                'department_name' => $department_name,
                'event' => $event,
                'returnmoney' => $returnmoney,
                'moremoneyeventual' => $moremoneyeventual,
                'lessmoneyeventual' => $lessmoneyeventual
            ]);
        }

        return response()->json(['data' => $data]);
        /*}else{
            return response()->json(['message' => 'No eres Manager de este departamento.', 'status' => 404], 404);
        }*/
    }

    public function approvedDepartment(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();
        $department_id_solicitud = DB::table('purchase_requests')->where('id', $request->id)->value('department_id');
        //dd($department_id_solicitud);
        // Obtener el id del usuario manager del departamento asociado con la solicitud
        $manager_ids = DB::table('manager_has_departments')->where('id_department', $department_id_solicitud)->pluck('id_user');
        

        ///Si el usuario logueado es manager aprueba///
        if ($manager_ids->contains($user->id)) {
            $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();
            DB::table('purchase_requests')->where('id', $request->id)->update([
                'approved_status' => 'en aprobación por administrador',
                'approved_by' => $user->id,
                'purchase_status_id' => 1
            ]);

            $role_buyer = Role::where('name', 'caja_chica')->get()->last();
            $user_role = UserRole::where('role_id', $role_buyer->id)->get();
            $spent = Spent::where('id', $purchase_request->spent_id)->get()->first();

            foreach ($user_role as $role) {
                $users_to_send_mail = User::where('id', $role->user_id)->get()->last();

                $title = 'Nueva solicitud de compra';
                $message = 'Haz recibido una nueva solicitud de compras.';

                /*try {
                    Notification::route('mail', $users_to_send_mail->email)
                    ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
                } catch (\Exception $e) {
                    return $e;
                }*/
            }
            return response()->json(['message' => "Solicitud aprobada satisfactoriamente"], 200);
        } else {
            return response()->json(['message' => 'No eres Manager de este departamento, por lo tanto no puedes autorizar la solicitud.', 'status' => 404], 404);
        }
    }

    //Solicitudes de Administrador
    public function showAdministrador()
    {
        $user = auth()->user();

        if ($user == null) {
            return response()->json([
                'message' => "Sesión de usuario expirada"
            ], 400);
        }

        $data = [];
        $spents = PurchaseRequest::where('approved_status', '<>', 'cancelada')->get();

        foreach ($spents as $spent) {
            $company_data = [];
            $spent_data = [];
            $center_data = [];
            $status_data = [];
            array_push($company_data, (object) [
                'company_id' =>  $spent->company_id,
                'company_name' =>  $spent->company->name
            ]);

            array_push($spent_data, (object) [
                'spent_id' =>  $spent->spent_id,
                'spent_name' =>  $spent->spent->concept,
                'spent_outgo_type' =>  $spent->spent->outgo_type,
                'spent_expense_type' =>  $spent->spent->expense_type,
                'spent_product_type' =>  $spent->spent->product_type,
            ]);

            array_push($center_data, (object) [
                'center_id' => $spent->center_id,
                'center_name' =>  $spent->center->name,
            ]);

            array_push($status_data, (object) [
                'id' => $spent->purchase_status->id,
                'name' =>  $spent->purchase_status->name,
                'table_name' =>  $spent->purchase_status->table_name,
                'type' =>  $spent->purchase_status->type,
                'status' =>  $spent->purchase_status->status,
            ]);

            $approved_by = '';

            if ($spent->approved_by != null || $spent->approved_by != '') {
                $user_approved = User::where('id', intval($spent->approved_by))->get()->last();
                $approved_by =  $user_approved->name;
            }

            $admin_approved = '';

            if ($spent->admin_approved != null || $spent->admin_approved != '') {
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
                'total' => $spent->total,
                'approved_status' => $spent->approved_status,
                'approved_by' => $approved_by,
                'admin_approved' => $admin_approved,
                'created_at' => $spent->created_at->format('d-m-Y'),
                'creation_date' => $spent->creation_date ? Carbon::parse($spent->creation_date)->format('d-m-Y') : "Aún no se ha asignado una fecha de creación.",
                'department_name' => $department_name,
            ]);
        }

        if($data == null)
        {
            return response()->json(['No se han creado solicitudes']);

        }

        return array(
            'spents' => $data,
        );
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user == null) {
            return response()->json([
                'message' => "Sesión de usuario expirada"
            ], 400);
        }
        $request->validate([
            'spent_id' => 'required',
            'type' => 'required',
            'total' => 'required',
        ]);

        $request->validate([
            'eventuales' => 'nullable|array',
            'eventuales.*.name' => 'required|string',
            'eventuales.*.pay' => 'required|numeric',
            'eventuales.*.company' => 'required'
        ]);

        $total = $request->total;

        if($total < 1){
            return response()->json(['message' => 'El importe debe ser mayor a $0'], 405);
        }

        $spent = Spent::where('id', $request->spent_id)->get()->last();
        if ($spent == null) {
            $center_id = 1;
        } else {
            $center_id = $spent->center_id;
        }
        $path = '';
        if ($request->hasFile('file')) {
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->clientExtension();
            $fileNameToStore = time() . $filename . '.' . $extension;
            $path = $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
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
        $create_spent->sign = null;
        $create_spent->approved_status = 'pendiente';
        $create_spent->approved_by = null;
        $create_spent->save();

        ///AQUI SE CREAN LOS EVENTUALES///
        $id = $create_spent->id;

        if ($request->eventuales) {
            $eventuales = $request->eventuales;

            // Genera un ID único para cada eventual combinando los IDs
            foreach ($eventuales as &$eventual) {
                $eventId = uniqid(); // Genera un ID único para el eventual
                $eventual['id'] = $eventId . '_' . $id; // Combina los IDs
            }

            $eventualesData = [
                'eventuales' => json_encode($eventuales),
                'purchase_id' => $id,
            ];

            Eventuales::create($eventualesData);
        }
        ///////////////////////////////////
        //////////PRIMERO VERIFICAMOS QUIEN ES SU JEFE DIRECTO////////////
        $department = DB::table('user_details')->where('id_user', $user->id)->first();
        //dd($department);
        $idDepartment = $department->id_department;
        //dd($idDepartment);
        $InfoDepartmentManager = DB::table('manager_has_departments')->where('id_department', $idDepartment)->get();
        $idUsers = $InfoDepartmentManager->pluck('id_user')->toArray();

        foreach($idUsers as $idUser){
            $Usuario = User::where('id', $idUser)->first();
            try {
                $Usuario->notify(new CreatePurchaseRequest($user->name, $Usuario->name, $id));
                
            } catch (\Exception $e) {
                return $e;
            }
        }
        
        return response()->json(['message' => "Creación de solicitud exitosa."], 200);
    }

    /////ESTA API SE VA ELIMINAR////
    /* public function editdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        if($request->creation_date == null){
            return response()->json(['message' => 'Selecciona una fecha'], 400);
        }
        
        $date = Carbon::parse($request->creation_date)->format('Y-m-d');

        DB::table('purchase_requests')->where('id', $request->id)->update(['creation_date' => $date]);
        return response(['message' => '¡LISTO!'], 200);
    } */

    public function updatemoney(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_purchase' => 'required',
            'total_update' => 'required'
        ]);

        $rolesUsuario = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id')->toArray();
        $rolesPermitidos = [14, 15];
        if (!empty(array_intersect($rolesUsuario, $rolesPermitidos))) {
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

                ///OBTENEMOS UN VALOR PARA REGRESAR EL DINERO SI SOBRA/// 
                /* $devolutionmoney = DB::table('exchange_returns')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
                    $query->where(function ($subquery) {
                        $subquery->where('status', '=', 'Confirmado');
                    });
                })->sum('total_return'); */

                ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
                //gastosmentuales == monthlyexpenses
                $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
                    $query->where(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                    })->orWhere(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                    })->orWhere(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                    })->orWhere(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'en proceso')->where('payment_method_id', '=', 1);
                    })->orWhere(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'rechazada')->where('payment_method_id', '=', 1);
                    });
                })->sum('total');
                //dd($MonthlyExpenses);

                $AvailableBudget = number_format($MonthlyBudget - $MonthlyExpenses, 2, '.', '');

                $restaDelCajaReturn = DB::table('refund_of_money')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                    ->sum('total_returned');
                // Restar total_returned al AvailableBudget si hay valores
                if ($restaDelCajaReturn) {
                    $AvailableBudget -= $restaDelCajaReturn;
                }

                ///REGRESAR  AL PRESUPUESTO EL DINERO///
                /* if ($devolutionmoney) {
                    $AvailableBudget += $devolutionmoney;
                }
                ///RESTARLE EL DINERO A LO EGRESADO///                                                
                if ($devolutionmoney) {
                    $MonthlyExpenses -= $devolutionmoney;
                } */
                //dd($AvailableBudget);

                $purchase = DB::table('purchase_requests')->where('id', $request->id_purchase)->first();
                if ($purchase) {
                    // Obtener el total anterior de la compra
                    $total_anterior = $purchase->total;
                    // Calcular la diferencia para llegar al nuevo total
                    $difference = $request->total_update - $total_anterior;

                    if ($difference > $AvailableBudget) {
                        return response()->json(['message' => 'No tienes fondos suficientes'], 400);
                    } else {
                        if($request->total_update < 1){
                            return response()->json(['message' => 'No puedes ingresar un monto igual a $0']);
                        }

                        DB::table('purchase_requests')->where('id', $request->id_purchase)->update([
                            'total' => $request->total_update
                        ]);
                    }
                }
            } else {
                DB::table('purchase_requests')->where('id', $request->id_purchase)->update([
                    'total' => $request->total_update
                ]);
            }
            return response()->json(['message' => 'Se actualizó con éxito la cantidad'], 200);
        } else {
            return response()->json(['message' => "No tienes permiso."], 404);
        }
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if ($user == null) {
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

        $spent = PurchaseRequest::where('id', $request->id)->get()->last();

        $path = $spent->file;

        if ($request->hasFile('file')) {
            File::delete($spent->file);
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->clientExtension();
            $fileNameToStore = time() . $filename . '.' . $extension;
            $path = $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
        }

        DB::table('purchase_requests')->where('id', $request->id)->update([
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

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request == null) {
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

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();


        DB::table('purchase_requests')->where('id', $request->id)->update([
            'approved_status' => 'en aprobación por administrador',
            'approved_by' => $user->id,
            'purchase_status_id' => 1
        ]);

        $role_buyer = Role::where('name', 'compras')->get()->last();

        $user_role = UserRole::where('role_id', $role_buyer->id)->get();
        $spent = Spent::where('id', $purchase_request->spent_id)->get()->first();

        /*foreach($user_role as $role){
            $users_to_send_mail = User::where('id',$role->user_id)->get()->last();

            $title = 'Nueva solicitud de compra';
            $message = 'Haz recibido una nueva solicitud de compras.';

            try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            }
        }*/

        return response()->json(['message' => "Solicitud aprobada satisfactoriamente"], 200);
    }

    public function approvedByAdmin(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request->approved_status == 'aprobada') {
            return response()->json(['message' => 'Solicitud aprobada']);
        } elseif ($purchase_request->approved_status == 'en aprobación por administrador') {
            DB::table('purchase_requests')->where('id', $request->id)->update([
                'approved_status' => 'aprobada',
                'admin_approved' => $user->id,
                'purchase_status_id' => 2
            ]);
        } else {
            DB::table('purchase_requests')->where('id', $request->id)->update([
                'approved_status' => 'aprobada',
                'admin_approved' => $user->id,
                'approved_by' => $user->id,
                'purchase_status_id' => 2
            ]);
        }

        $role_buyer = Role::where('name', 'compras')->get()->last();

        $user_role = UserRole::where('role_id', $role_buyer->id)->get();
        $spent = Spent::where('id', $purchase_request->spent_id)->get()->first();

        foreach ($user_role as $role) {
            $users_to_send_mail = User::where('id', $role->user_id)->get()->last();

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

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request->approved_status == 'pendiente') {

            DB::table('purchase_requests')->where('id', $request->id)->update([
                'approved_status' => 'rechazada',
                'approved_by' => $user->id,
                'type_status' => 'cancelado',
            ]);
        } else if ($purchase_request->approved_status == 'en aprobación por administrador') {
            DB::table('purchase_requests')->where('id', $request->id)->update([
                'approved_status' => 'rechazada',
                'admin_approved' => $user->id,
                'type_status' => 'cancelado',
            ]);
        };

        $users_to_send_mail = User::where('id', $purchase_request->user_id)->get()->last();

        $spent = Spent::where('id', $purchase_request->spent_id)->get()->first();

        $user = User::where('id', $spent->user_id)->get()->last();

        $title = 'Solicitud rechazada';
        $message = 'Tu solicitud ha sido rechazada, revisa la información e intenta enviarla nuevamente.';

        /*try {
            Notification::route('mail', $users_to_send_mail->email)
            ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
        } catch (\Exception $e) {
            return $e;
        }*/

        return response()->json(['message' => "Solicitud rechazada satisfactoriamente"], 200);
    }

    public function confirmDelivered(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request == null) {
            return response()->json(['message' => "Producto no encontrado"], 400);
        }

        if ($purchase_request->purchase_status_id == 2) {

            DB::table('purchase_requests')->where('id', $request->id)->update([
                'purchase_status_id' => 3,
            ]);

            $Usuario = User::where('id', $user->id)->first();
            $id = $purchase_request->user_id;
            $username = DB::table('users')->where('id', $id)->value('name');
            try {
                $Usuario->notify(new ConfirmedReceipt($user->name, $username, $request->id));
                
            } catch (\Exception $e) {
                return $e;
            }

            $metododepago = DB::table('purchase_requests')->where('id', $request->id)->select('payment_method_id')->first();

            if ($metododepago->payment_method_id == 1) {
                ////// aqui se crea///
                spent_money::create([
                    'id_user' => $user->id,
                    'id_pursache_request' => $request->id,
                ]);
            }
            return response()->json(['message' => "Pedido confirmado"], 200);
        } else {
            return response()->json(['message' => "No se ha podido confirmar el pedido, verifica que haya sido aprobado para compra o no ha sido entregado."], 400);
        }
    }

    public function confirmReceived(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request == null) {
            return response()->json(['message' => "Orden no encontrada"], 400);
        }

        if ($purchase_request->purchase_status_id == 3 &&  $purchase_request->approved_status == 'aprobada') {
            DB::table('purchase_requests')->where('id', $request->id)->update([
                'purchase_status_id' => 4,
            ]);

            $users_to_send_mail = User::where('id', $purchase_request->user_id)->get()->last();

            $spent = Spent::where('id', $purchase_request->spent_id)->get()->first();

            $title = 'Haz recibido el Pedido';
            $message = 'Se ha confirmado que haz recibido el pedido';

            /*try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            }*/

            return response()->json(['message' => "Se ha confirmado que el pedido fue recibido"], 200);
        } else {
            return response()->json(['message' => "No se ha podido realizar la confirmación del pedido, verifica que la orden haya sido aprobada y confirmada de entrega"], 400);
        }
    }

    public function createDevolution(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request == null) {
            return response()->json(['message' => "Orden no encontrada"], 400);
        }

        if ($purchase_request->purchase_status_id == 3 || $purchase_request->purchase_status_id == 4) {
            DB::table('purchase_requests')->where('id', $request->id)->update([
                'purchase_status_id' => 5,
                'type_status' => 'en proceso',
                'approved_status' => 'devolución'
            ]);

            $users_to_send_mail = User::where('id', $purchase_request->user_id)->get()->last();

            $spent = Spent::where('id', $purchase_request->spent_id)->get()->first();

            $title = 'Devolución de Pedido';
            $message = 'Se ha realizado la devolución del pedido';

            /*try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            }*/

            return response()->json(['message' => "Devolución en proceso"], 200);
        }
    }

    public function confirmationDevolution(Request $request)
    {

        $user = auth()->user();

        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request == null) {
            return response()->json(['message' => "Orden no encontrada"], 400);
        }

        if ($purchase_request->purchase_status_id == 5) {
            DB::table('purchase_requests')->where('id', $request->id)->update([
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

            $mes = Carbon::now()->month;
            $solicitud = DB::table('purchase_requests')->where('id', $request->id)->first();
            $fechasoli = Carbon::parse($solicitud->created_at)->month;

            if ($mes != $fechasoli) {
                EstimationSmallBox::create([
                    'total' => $total_return,
                    'id_user' => $user->id,
                ]);
            }

            return response()->json(['message' => "Devolución realizada"], 200);
        }
    }

    public function cancelationDevolution(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request == null) {
            return response()->json(['message' => "Orden no encontrada"], 400);
        }

        if ($purchase_request->purchase_status_id == 5) {
            DB::table('purchase_requests')->where('id', $request->id)->update([
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

        $purchase_request = PurchaseRequest::where('id', $request->id)->get()->last();

        if ($purchase_request == null) {
            return response()->json(['message' => "Producto no encontrado"], 400);
        }

        if ($purchase_request->purchase_status_id == 2) {
            DB::table('purchase_requests')->where('id', $request->id)->update([
                'type_status' => 'cancelado',
            ]);

            $users_to_send_mail = User::where('id', $purchase_request->user_id)->get()->last();

            $spent = Spent::where('id', $purchase_request->spent_id)->get()->first();

            $title = 'Cancelación de Pedido';
            $message = 'Se ha realizado la cancelación del pedido';

            /*try {
                Notification::route('mail', $users_to_send_mail->email)
                ->notify(new BuyersRequestNotification($title, $message, $spent->concept, $spent->center->name, $purchase_request->total));
            } catch (\Exception $e) {
                return $e;
            }*/
            return response()->json(['message' => "Cancelación realizada"], 200);
        } else {
            return response()->json(['message' => "No es posible realizar una cancelación una vez que recibas el producto; se debe realizar una devolución"], 400);
        }
    }

    public function showPage($page)
    {
        $spent = PurchaseRequest::where('id', $page)->get()->last();

        $data = [];
        if (isset($spent->spent_id)) {

            $company_data = [];
            $spent_data = [];
            $center_data = [];
            $status_data = [];
            array_push($company_data, (object) [
                'company_id' =>  $spent->company_id,
                'company_name' =>  $spent->company->name
            ]);

            array_push($spent_data, (object) [
                'spent_id' =>  $spent->spent_id,
                'spent_name' =>  $spent->spent->concept,
                'spent_outgo_type' =>  $spent->spent->outgo_type,
                'spent_expense_type' =>  $spent->spent->expense_type,
                'spent_product_type' =>  $spent->spent->product_type,

            ]);
            array_push($center_data, (object) [
                'center_id' => $spent->center_id,
                'center_name' =>  $spent->center->name,
            ]);

            array_push($status_data, (object) [
                'id' => $spent->purchase_status->id,
                'name' =>  $spent->purchase_status->name,
                'table_name' =>  $spent->purchase_status->table_name,
                'type' =>  $spent->purchase_status->type,
                'status' =>  $spent->purchase_status->status,
            ]);

            $approved_by = '';

            if ($spent->approved_by != null || $spent->approved_by != '') {
                $user_approved = User::where('id', intval($spent->approved_by))->get()->last();

                $approved_by =  $user_approved->name;
            }

            $admin_approved = '';

            if ($spent->admin_approved != null || $spent->admin_approved != '') {
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

            $returnmoneyexcess = DB::table('exchange_returns')->where('purchase_id', $page)->select(
                'id',
                'total_return',
                'previous_total',
                'status',
                'confirmation_datetime',
                'confirmation_user_id',
                'description',
                'file_exchange_returns',
                'return_user_id',
                'created_at'
            )->get()->toArray();

            $returnmoney = [];

            foreach ($returnmoneyexcess as $returnmoney) {
                $returnmoney->created_at = date('d-m-Y H:i:s', strtotime($returnmoney->created_at));

                if ($returnmoney->confirmation_datetime != null) {
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
                'total' => $spent->total,
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

        if ($user == null) {
            return response()->json([
                'message' => "Sesión de usuario expirada"
            ], 400);
        }

        $request->validate([
            'id' => 'required',
            'payment_method_id' => 'required',
        ]);

        ////14 ES ROL DE CAJA CHICA
        ////15 ES ROL DE ADQUISISION

        $rolesUsuario = DB::table('role_user')->where('user_id', $user->id)->pluck('role_id')->toArray();
        $rolesPermitidos = [14, 15];
        if (!empty(array_intersect($rolesUsuario, $rolesPermitidos))) {
            ///VERIFICAMOS SI EL METODO DE PAGO QUE SE USUARA ES EFECTIVO///
            if ($request->payment_method_id == 1) {
                $pago = DB::table('purchase_requests')->where('id', $request->id)->select('total')->first();
                $total = $pago->total;
                ///OBTENEMOS EL PRIMER DÍA DEL MES Y EL ÚLTIMO///        
                $primerDiaDelMes = Carbon::now()->startOfMonth();
                $ultimoDiaDelMes = Carbon::now()->endOfMonth();

                // Verificar si la fecha actual está dentro del mes
                if (Carbon::now()->between($primerDiaDelMes, $ultimoDiaDelMes)) {
                    // Si estamos en el mes actual, realizar la suma
                    //presupuestomensual == MonthlyBudget
                    $MonthlyBudget = DB::table('estimation_small_box')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->sum('total');
                }
                ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
                ///gastosmentuales == monthlyexpenses///

                ///OBTENEMOS UN VALOR PARA REGRESAR EL DINERO SI SOBRA/// 
                /* $devolutionmoney = DB::table('exchange_returns')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
                    $query->where(function ($subquery) {
                        $subquery->where('status', '=', 'Confirmado');
                    });
                })->sum('total_return'); */
                $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
                    $query->where(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                    })->orWhere(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                    })->orWhere(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                    })->orWhere(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'en proceso')->where('payment_method_id', '=', 1);
                    })->orWhere(function ($subquery) {
                        $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'rechazada')->where('payment_method_id', '=', 1);
                    });
                })->sum('total');

                ///presupuestodisponible == AvailableBudget                                        
                $AvailableBudget = number_format($MonthlyBudget - $MonthlyExpenses, 2, '.', '');

                $restaDelCajaReturn = DB::table('refund_of_money')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                    ->sum('total_returned');
                // Restar total_returned al AvailableBudget si hay valores
                if ($restaDelCajaReturn) {
                    $AvailableBudget -= $restaDelCajaReturn;
                }

                ///REGRESAR  AL PRESUPUESTO EL DINERO///
                /* if ($devolutionmoney) {
                    $AvailableBudget += $devolutionmoney;
                }
                ///RESTARLE EL DINERO A LO EGRESADO///                                                
                if ($devolutionmoney) {
                    $MonthlyExpenses -= $devolutionmoney;
                } */
                //dd($MonthlyExpenses);

                if ($pago) {
                    if ($total > $AvailableBudget) {
                        return response()->json(['message' => '¡No tienes fondos suficientes!'], 400);
                    } else {
                        DB::table('purchase_requests')->where('id', $request->id)->update([
                            'payment_method_id' => $request->payment_method_id,
                        ]);
                    }
                } else {
                    return response()->json(['message' => '¡No se encontró el pago correspondiente!'], 400);
                }
            } else {
                return response()->json(['message' => 'Selecciona un método de pago válido'], 400);
            }
            return response()->json(['message' => "Método de pago actualizado correctamente"], 200);
        } else {
            return response()->json(['message' => "No tienes permiso."], 404);
        }
    }

    public function updateEventuales(Request $request)
    {

        $this->validate($request, [
            'purchase_id' => 'required',
            'id_eventual' => 'required|array',
            'id_company' => 'required|array',
            'new_pay' => 'required|array'
        ]);
        //////////////////////////////////////////////////////////VERIFIVACIÓN DEL PRESUPUESTO////////////////////////////
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

        ///OBTENEMOS UN VALOR PARA REGRESAR EL DINERO SI SOBRA/// 
        /* $devolutionmoney = DB::table('exchange_returns')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
            $query->where(function ($subquery) {
                $subquery->where('status', '=', 'Confirmado');
            });
        })->sum('total_return'); */

        ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
        //gastosmentuales == monthlyexpenses
        $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
            $query->where(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'en proceso')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'rechazada')->where('payment_method_id', '=', 1);
            });
        })->sum('total');

        $AvailableBudget = number_format($MonthlyBudget - $MonthlyExpenses, 2, '.', '');

        $restaDelCajaReturn = DB::table('refund_of_money')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
            ->sum('total_returned');
        // Restar total_returned al AvailableBudget si hay valores
        if ($restaDelCajaReturn) {
            $AvailableBudget -= $restaDelCajaReturn;
        }

        ///REGRESAR  AL PRESUPUESTO EL DINERO///
        /* if ($devolutionmoney) {
            $AvailableBudget += $devolutionmoney;
        }
        ///RESTARLE EL DINERO A LO EGRESADO///                                                
        if ($devolutionmoney) {
            $MonthlyExpenses -= $devolutionmoney;
        } */

        ///////////////////////////////////////////////////////FIN DE LA VERIFICACION DEL PRESUPUESTO/////////////////////
        //dd($MonthlyExpenses);
        $purchase_id = $request->purchase_id;
        $id_eventuales = $request->id_eventual; // Ahora id_eventual es un array

        //Obtenemos los eventuales
        $eventuales = DB::table('eventuales')->where('purchase_id', $purchase_id)->first();
        $eventualArray = json_decode($eventuales->eventuales, true);

        $pago = 0; // Inicializar la variable para almacenar la suma de los pagos

        foreach ($id_eventuales as $key => $id_eventual) {
            $new_pay_amount = $request->new_pay[$key]; // Obtener el pago correspondiente al ID actual
            $new_company = $request->id_company[$key];

            foreach ($eventualArray as &$item) {
                $id = $item['id'];
                if ($id === $id_eventual) {
                    $item['pay'] = $new_pay_amount;
                    $item['company'] = $new_company;
                    $pago += $new_pay_amount; // Sumar el valor de pay
                    break; // Detener el bucle una vez que se ha actualizado el pago
                }
            }
        }

        $purchase = DB::table('purchase_requests')->where('id', $purchase_id)->first();
        $total_anterior = $purchase->total;
        $difference = $pago - $total_anterior;
        if ($difference > $AvailableBudget) {
            return response()->json(['message' => 'No tienes fondos suficientes'], 400);
        } else {
            $updatedEventualJSON = json_encode($eventualArray);
            DB::table('eventuales')->where('purchase_id', $purchase_id)->update(['eventuales' => $updatedEventualJSON]);
            // Recalculamos el total de pago
            $eventuales = DB::table('eventuales')->where('purchase_id', $purchase_id)->get();
            $pays = [];
            foreach ($eventuales as $eventual) {
                $eventualArray = json_decode($eventual->eventuales, true);
                foreach ($eventualArray as $item) {
                    $pays[] = $item['pay'];
                }
            }
            // Sumar todos los valores de 'pay' en $pays
            $total_pay = array_sum($pays);
            DB::table('purchase_requests')->where('id', $purchase_id)->update([
                'total' => $total_pay,
            ]);
        }
        return response()->json(['message' => 'Pagos actualizados', 'status' => 200], 200);
    }

    public function EventualesFinde(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'id_purchase' => 'required',
            'eventuales' => 'nullable|array',
            'eventuales.*.name' => 'required|string',
            'eventuales.*.pay' => 'required|numeric',
            'eventuales.*.company' => 'required'
        ]);

        //////////////////////////////////////////////////////////VERIFIVACIÓN DEL PRESUPUESTO////////////////////////////
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

        ///OBTENEMOS UN VALOR PARA REGRESAR EL DINERO SI SOBRA/// 
        /* $devolutionmoney = DB::table('exchange_returns')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
            $query->where(function ($subquery) {
                $subquery->where('status', '=', 'Confirmado');
            });
        })->sum('total_return'); */

        ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
        //gastosmentuales == monthlyexpenses
        $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])->where(function ($query) {
            $query->where(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'en proceso')->where('payment_method_id', '=', 1);
            })->orWhere(function ($subquery) {
                $subquery->where('purchase_status_id', '=', 5)->where('type_status', '=', 'rechazada')->where('payment_method_id', '=', 1);
            });
        })->sum('total');

        $AvailableBudget = number_format($MonthlyBudget - $MonthlyExpenses, 2, '.', '');

        $restaDelCajaReturn = DB::table('refund_of_money')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
            ->sum('total_returned');
        // Restar total_returned al AvailableBudget si hay valores
        if ($restaDelCajaReturn) {
            $AvailableBudget -= $restaDelCajaReturn;
        }

        ///REGRESAR  AL PRESUPUESTO EL DINERO///
        /* if ($devolutionmoney) {
            $AvailableBudget += $devolutionmoney;
        }
        ///RESTARLE EL DINERO A LO EGRESADO///                                                
        if ($devolutionmoney) {
            $MonthlyExpenses -= $devolutionmoney;
        } */

        ///////////////////////////////////////////////////////FIN DE LA VERIFICACION DEL PRESUPUESTO/////////////////////
        //dd($MonthlyExpenses);
        $eventuales = $request->eventuales;
        $total_pay = 0;

        // Genera un ID único para cada eventual combinando los IDs
        foreach ($eventuales as &$eventual) {
            $eventId = uniqid(); // Genera un ID único para el eventual
            $eventual['id'] = $eventId . '_' . $request->id_purchase; // Combina los IDs
            $total_pay += $eventual['pay'];
        }
        // Agrega el total de pagos al arreglo de datos
        $eventualesData = [
            'eventuales' => json_encode($eventuales),
            'purchase_id' => $request->id_purchase,
            'total_pay' => $total_pay, // Aquí se agrega el total de pagos
        ];

        $purchase = DB::table('purchase_requests')->where('id', $request->id_purchase)->first();
        $total_anterior = $purchase->total;
        $difference = $total_pay - $total_anterior;
        ////////////EL TOTAL DE LOS EVENTUALES Y EL TOTAL DE LA SOLICITUD ES IGUAL//////////////
        if ($total_pay == $total_anterior) {
            Eventuales::create($eventualesData);
        }
        /////////////EL TOTAL DE LOS EVENTUALES ES MAYOR AL TOTAL DE LA SOLICITUD//////////////
        elseif ($total_pay > $total_anterior) {
            if ($difference > $AvailableBudget) {
                return response()->json(['message' => 'No tienes fondos suficientes'], 400);
            } else {
                $request->validate([
                    'description' => 'required',
                ]);

                if($request->file == null){
                    return response()->json(['message' => 'No has cargado un comprobante'], 400);
                }
                ////GUARDAMOS EL EVENTUAL////
                $eventual = Eventuales::create($eventualesData);

                $path = '';
                if ($request->hasFile('file')) {
                    $filenameWithExt = $request->file('file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('file')->clientExtension();
                    $fileNameToStore = time(). $filename . '.' . $extension;
                    $path= $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
                }

                LackOfMoneyEventuals::create([
                    'id_applicant_person' => $user->id,
                    'description' => $request->description,
                    'file' => $path,
                    'previous_total' => $total_anterior,
                    'current_total' => $total_pay,
                    'status' => "Sin confirmar",
                    'id_eventual' => $eventual->id,
                    'id_purchase' => $request->id_purchase
                ]);
            }
        }
        ////////////EL TOTAL DEL PAGO DE LOS EVENTUALES ES MENOR AL DE LA SOLICITUD////////////
        elseif($total_pay < $total_anterior){
            if ($difference > $AvailableBudget) {
                return response()->json(['message' => 'No tienes fondos suficientes'], 400);
            }else {
                $request->validate([
                    'description' => 'required',
                ]);

                if($request->file == null){
                    return response()->json(['message' => 'No has cargado un comprobante'], 400);
                }
            
                ////GUARDAMOS EL EVENTUAL////
                $eventual = Eventuales::create($eventualesData);

                $path = '';
                if ($request->hasFile('file')) {
                    $filenameWithExt = $request->file('file')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('file')->clientExtension();
                    $fileNameToStore = time(). $filename . '.' . $extension;
                    $path= $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
                }

                ReturnMoneyFromEventualities::create([
                    'id_applicant_person' => $user->id,
                    'description' => $request->description,
                    'file' => $path,
                    'previous_total' => $total_anterior,
                    'current_total' => $total_pay,
                    'status' => "Sin confirmar",
                    'id_eventual' => $eventual->id,
                    'id_purchase' => $request->id_purchase
                ]);
                return response()->json(['message' => 'Comenzaste el proceso de regresar el sobrante'], 200);
            }
        }

        return response()->json(['message' => 'Se agregaron correctamente los usuarios eventuales de fin de semana.'], 200);
    }
}
