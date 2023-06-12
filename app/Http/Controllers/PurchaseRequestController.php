<?php

namespace App\Http\Controllers;

use App\Models\PurchaseDevolution;
use App\Models\PurchaseRequest;
use App\Models\PurchaseStatus;
use App\Models\Spent;
use App\Models\User;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PurchaseRequestController extends Controller
{
    public function show()
    {
        $total_page = 15;
        $data = [];
        $spents = PurchaseRequest::all();

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

                array_push($data, (object)[
                    'id' => $spents[$i]->id,
                    'user_id' => $spents[$i]->user_id,
                    'company' =>  $company_data,
                    'spent' => $spent_data,
                    'center'  =>  $center_data,
                    'description' => $spents[$i]->description,
                    'file' => $spents[$i]->file,
                    'commentary' => $spents[$i]->commentary,
                    'purchase_status' => $spents[$i]->purchase_status->name,
                    'type' => $spents[$i]->type,
                    'type_status' => $spents[$i]->type_status,
                    'payment_method' => $spents[$i]->payment_method->name,
                    'total' =>$spents[$i]->total, 
                    'approved_status' => $spents[$i]->approved_status,
                    'approved_by' => $approved_by,
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
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'company_id' => 'required',
            'spent_id' => 'required',
            'center_id' => 'required',
            'description' => 'required',
            'payment_method_id' => 'required',
            'total' => 'required',
        ]);

        $path = "";

        if ($request->hasFile('file')) {
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->clientExtension();
            $fileNameToStore = time(). $filename . '.' . $extension;
            $path= $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
        }

        $create_spent = new PurchaseRequest();
        $create_spent->user_id = $request->user_id;
        $create_spent->company_id = $request->company_id;
        $create_spent->spent_id = $request->spent_id;
        $create_spent->center_id = $request->center_id;
        $create_spent->description = $request->description;
        $create_spent->file = $path;
        $create_spent->commentary = '';
        $create_spent->purchase_status_id = 1;
        $create_spent->payment_method_id = $request->payment_method_id;
        $create_spent->total = $request->total;
        $create_spent->approved_by = null;
        $create_spent->status = 1;
        $create_spent->save();

        return response()->json(['msg' => "Registro guardado satisfactoriamente"]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
            'spent_id' => 'required',
            'center_id' => 'required',
            'description' => 'required',
            'payment_method_id' => 'required',
            'total' => 'required',
            'purchase_status_id'=> 'required',
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
            'purchase_status_id' => $request->purchase_status_id,
            'payment_method_id' => $request->payment_method_id,
            'total' => $request->total,
        ]);

        return response()->json(['msg' => "Registro actualizado satisfactoriamente"]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

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
    
        DB::table('purchase_requests')->where('id',$request->id)->update([
            'status' => 1,
            'approved_by' => $user->id
        ]);

        return response()->json(['msg' => "Solicitud aprobada satisfactoriamente"]);
    }

    public function rejected(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $commentary = "";

        if($request->commentary <> null){
            $commentary = $request->commentary;
        }

        $user = Auth::user();

        DB::table('purchase_requests')->where('id',$request->id)->update([
            'status' => 2,
            'commentary' => $commentary,
            'approved_by' => $user->id
        ]);

        return response()->json(['msg' => "Solicitud rechazada satisfactoriamente"]);

    }


    public function confirmDelivered(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['msg' => "Producto no encontrado"]);
        }
        
        $status = PurchaseStatus::where('id',$purchase_request->purchase_status_id)->get()->last();
       
        if($status->type == 'producto'){
            $payment_status = PurchaseStatus::where('name','Recibido')->where('type','producto')->where('description', 'normal')->get()->last(); 
            
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'purchase_status_id' => $payment_status->id,
            ]);

            return response()->json(['msg' => "Producto actualizado satisfactoriamente"]);

        }


        if($status->type == 'servicio'){
            $payment_status = PurchaseStatus::where('name','Pagado')->where('type','servicio')->where('description', 'normal')->get()->last();

            DB::table('purchase_requests')->where('id',$request->id)->update([
                'purchase_status_id' => $payment_status->id,
            ]);

            return response()->json(['msg' => "Servicio actualizado satisfactoriamente"]);

        }

    }

    public function createDevolution(Request $request)
    {   
        $request->validate([
            'id' => 'required',
            'motive' => 'required',
            'payment_method_id' => 'required',
        ]);

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if( $purchase_request == null){
            return response()->json(['msg' => "Producto no encontrado"]);
        }
        
        $status = PurchaseStatus::where('id',$purchase_request->purchase_status_id)->get()->last();
        
        if($status->name == 'En proceso' || $status->name == 'Compra' || $status->name == 'En proceso' ){
            return response()->json(['msg' => "Solo se puede hacer devoluciones en pedidos cuyo status sea 'Entregado', 'Recibido' o 'Pagado'."]);
        }
       
        if($status->type == 'producto'){
            $payment_status = PurchaseStatus::where('name','Recibido')->where('type','producto')->where('description', 'devolucion')->get()->last(); 
            
            DB::table('purchase_requests')->where('id',$request->id)->update([
                'purchase_status_id' => $payment_status->id,
            ]);
            
        }

        if($status->type == 'servicio'){
            $payment_status = PurchaseStatus::where('name','Pagado')->where('type','servicio')->where('description', 'devolucion')->get()->last();

            DB::table('purchase_requests')->where('id',$request->id)->update([
                'purchase_status_id' => $payment_status->id,
            ]);

        }


        if(isset($purchase_request->purchase_devolution)){
            DB::table('purchase_devolution')->where('purchase_request_id',$request->id)->update([
                'motive' => $request->motive,
                'payment_method_id' => $request->payment_method_id,
            ]);
        }else{
            $create_purchase_devolution = new PurchaseDevolution();
            $create_purchase_devolution->purchase_request_id = $purchase_request->id;
            $create_purchase_devolution->motive = $request->motive;
            $create_purchase_devolution->payment_method_id = $request->payment_method_id;
            $create_purchase_devolution->description = $request->description;
            $create_purchase_devolution->save();
        }

        return response()->json(['msg' => "Devolucion realizada"]);

    }
    
    public function showPage($page)
    {
        $total_page = 15;
        $data = [];
        $spents = PurchaseRequest::all();

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

                $approved_by = '';
              
                if($spents[$i]->approved_by != null || $spents[$i]->approved_by != '' ){
                    $user_approved = User::where('id', intval($spents[$i]->approved_by))->get()->last();
    
                    $approved_by =  $user_approved->name;
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
                    'purchase_status' => $spents[$i]->purchase_status->name,
                    'type' => $spents[$i]->type,
                    'type_status' => $spents[$i]->type_status,
                    'payment_method' => $spents[$i]->payment_method->name,
                    'total' =>$spents[$i]->total, 
                    'approved_status' => $spents[$i]->approved_status,
                    'approved_by' => $approved_by,
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
    }
}
