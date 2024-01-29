<?php

namespace App\Http\Controllers;

use App\Models\Eventuales;
use App\Models\EventualesMaquila;
use App\Models\HistoryDevolution;
use App\Models\PaymentMethodInformation;
use App\Models\PurchaseRequest;
use App\Models\Role;
use App\Models\Spent;
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
                'msg' => "Sesión de usuario expirada"
            ]);
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
            ]);
        }

        return array(
            'spents' => $data, 
        );
    }

      //Solicitudes de Administrador
      public function showAdministrador()
      {
        $user = auth()->user();
        
        if($user == null){
            return response()->json([
                'msg' => "Sesión de usuario expirada"
            ]);
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
                'msg' => "Sesión de usuario expirada"
            ]);
        }
        $request->validate([
            'company_id' => 'required',
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

        $product_type = Spent::where('id', $request->spent_id)->get()->last();

        $create_spent = new PurchaseRequest();
        $create_spent->user_id = $user->id;
        $create_spent->company_id = $request->company_id;
        $create_spent->spent_id = $request->spent_id;
        $create_spent->center_id = $center_id;
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

        return response()->json(['msg' => "Registro guardado satisfactoriamente"]);
    }

    public function updatemoney(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'id_purchase' => 'required',
            'total_update' => 'required'
        ]);

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
            $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                                                            ->where(function ($query) {
                                                                $query->where(function ($subquery) {
                                                                    $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                                })->orWhere(function ($subquery) {
                                                                    $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                                })->orWhere(function ($subquery) {
                                                                    $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
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
                    return response()->json(['message' => 'No tienes fondos suficientes']);
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
        return response()->json(['message' => 'Se actualizó con éxito la cantidad', 'status' => 200], 200);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if($user == null){
            return response()->json([
                'msg' => "Sesión de usuario expirada"
            ]);
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

        return response()->json(['msg' => "Registro actualizado satisfactoriamente"]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['msg' => "Producto no encontrado"]);
        }

           
        File::delete($purchase_request->file);

        $purchase_request->delete();
        
        return response()->json(['msg' => "Registro eliminado satisfactoriamente"]);
        
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

        return response()->json(['msg' => "Solicitud aprobada satisfactoriamente"]);
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

        return response()->json(['msg' => "Solicitud aprobada satisfactoriamente"]);
       
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
    
        return response()->json(['msg' => "Solicitud rechazada satisfactoriamente"]);
        
    }

    public function confirmDelivered(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null ){
            return response()->json(['msg' => "Producto no encontrado"]);
        }

        if($purchase_request->purchase_status_id == 2){
           
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'purchase_status_id' => 3,
            ]);
            
            return response()->json(['msg' => "Pedido confirmado"]);
        }else{
            return response()->json(['msg' => "No se ha podido confirmar el pedido, verifica que haya sido aprobado para compra o no ha sido entregado."]);
        }
        
    }


    public function confirmReceived(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if($purchase_request == null){
            return response()->json(['msg' => "Orden no encontrada"]);
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
    
            return response()->json(['msg' => "Se ha confirmado que el pedido fue recibido"]);
        }else{
            return response()->json(['msg' => "No se ha podido realizar la confirmación del pedido, verifica que la orden haya sido aprobada y confirmada de entrega"]);
        }       
    }

    public function createDevolution(Request $request)
    {   
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['msg' => "Orden no encontrada"]);
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
    
            return response()->json(['msg' => "Devolución en proceso"]);
        }
    }

    public function confirmationDevolution(Request $request){

        $user = auth()->user();

        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['msg' => "Orden no encontrada"]);
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

            return response()->json(['msg' => "Devolución realizada"]);
        }
    }

    public function cancelationDevolution(Request $request){
        $user = auth()->user();

        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['msg' => "Orden no encontrada"]);
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
            
            return response()->json(['msg' => "Devolución rechazada"]);
        }
    }

    public function createCancellation(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['msg' => "Producto no encontrado"]);
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
            return response()->json(['msg' => "Cancelación realizada"]);
        }else{
            return response()->json(['msg' => "No es posible realizar una cancelación una vez que recibas el producto; se debe realizar una devolución"]);
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

            // Obtener información de los eventuales
            $eventuales = DB::table('eventuales')->where('purchase_id', $page)->pluck('eventuales')->toArray();

            // Inicializar el array resultante
            $event = [];

            foreach ($eventuales as $jsonString) {
                $datos = json_decode($jsonString, true);

                foreach ($datos as $item) {
                    // Obtener el ID de la compañía desde los eventuales
                    $companyId = $item['company'];
                    // Buscar el nombre de la compañía en la tabla tempory_company
                    $companyName = DB::table('tempory_company')->where('id', $companyId)->value('name');
                    // Agregar el nombre de la compañía al array original
                    $item['company_name'] = $companyName;
                    // Agregar cada objeto al resultado
                    $event[] = $item;
                }
            }

            $returnmoneyexcess = DB::table('exchange_returns')->where('purchase_id', $page)->select('total_return', 'status', 'confirmation_datetime', 
                                                                                        'confirmation_user_id', 'description','file', 
                                                                                        'return_user_id','created_at')->get()->toArray();
            
            $returnmoney = [];

            foreach ($returnmoneyexcess as $returnmoney){
                $returnmoney->created_at = date('d-m-Y', strtotime($returnmoney->created_at));
                $returnmoney->confirmation_datetime = date('d-m-Y H:i:s', strtotime($returnmoney->confirmation_datetime));

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
            //gastosmentuales == monthlyexpenses

            $MonthlyExpenses = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                                                                ->where(function ($query) {
                                                                    $query->where(function ($subquery) {
                                                                        $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                                    })->orWhere(function ($subquery) {
                                                                        $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
                                                                    })->orWhere(function ($subquery) {
                                                                        $subquery->where('purchase_status_id', '=', 3)->where('type_status', '=', 'normal')->where('payment_method_id', '=', 1);
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

        PaymentMethodInformation::create([
            'id_user' => $user->id,
            'id_pursache_request' => $request->id,
        ]);
        return response()->json(['message' => "Método de pago actualizado correctamente"],200);
    }
}
