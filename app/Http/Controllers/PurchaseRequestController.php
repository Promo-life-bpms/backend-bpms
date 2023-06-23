<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Spent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PurchaseRequestController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        if($user == null){
            return response()->json([
                'msg' => "Sesión de usuario expirada"
            ]);
        }

        $data = [];
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
                'payment_method' => $spent->payment_method->name,
                'total' =>$spent->total, 
                'approved_status' => $spent->approved_status,
                'approved_by' => $approved_by,
                'created_at' => $spent->created_at,
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

        $create_spent = new PurchaseRequest();
        $create_spent->user_id = $user->id;
        $create_spent->company_id = $request->company_id;
        $create_spent->spent_id = $request->spent_id;
        $create_spent->center_id = $center_id;
        $create_spent->description = $request->description;
        $create_spent->file = $path;
        $create_spent->commentary = '';
        $create_spent->purchase_status_id = 1;
        $create_spent->type = $request->type;
        $create_spent->type_status = 'normal';
        $create_spent->payment_method_id = 4;
        $create_spent->total = $request->total;
        $create_spent->sign= null;
        $create_spent->approved_status = 'pendiente';
        $create_spent->approved_by = null;
        $create_spent->save();

        return response()->json(['msg' => "Registro guardado satisfactoriamente"]);
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

        if($purchase_request->approved_status == 'pendiente' && $purchase_request->approved_by == null){
           
            File::delete($purchase_request->file);

            $purchase_request->delete();
            
            return response()->json(['msg' => "Registro eliminado satisfactoriamente"]);
        }else{
            return response()->json(['msg' => "No ha sido posible eliminar el registro"]);
        }
    }

    public function approved(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if($purchase_request->approved_status == 'pendiente' && $purchase_request->approved_by == null){ 

            DB::table('purchase_requests')->where('id',$request->id)->update([
                'approved_status' => 'aprobada',
                'approved_by' => $user->id,
                'purchase_status_id' => 2
            ]);
    
            return response()->json(['msg' => "Solicitud aprobada satisfactoriamente"]);
        }else{
            return response()->json(['msg' => "No es posible aprobar solicitudes que previamente han sido aprobadas o rechazadas, en caso de requerirlo, cancela la solicitud e intenta nuevamente"]);
        }
    }

    public function rejected(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();

        $purchase_request = PurchaseRequest::where('id',$request->id)->get()->last();

        if($purchase_request->approved_status == 'pendiente' && $purchase_request->purchase_status_id == 1){

            DB::table('purchase_requests')->where('id',$request->id)->update([
                'approved_status' => 'rechazada',
                'approved_by' => $user->id
            ]);
    
            return response()->json(['msg' => "Solicitud rechazada satisfactoriamente"]);
        }else{
            return response()->json(['msg' => "No es posible rechazar solicitudes que previamente han sido aprobadas o rechazadas, en caso de requerirlo, cancela la solicitud e intenta nuevamente"]);
        }

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
            return response()->json(['msg' => "No se ha podido confirmar el pedido, verfica que haya sido aprobado para compra o no ha sido entregado"]);
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
    
            return response()->json(['msg' => "Se ha confirmado que el pedido fue recibido"]);
        }else{
            return response()->json(['msg' => "Nose ha podido realizar la confirmacion del pedido, verifica que la orden haya sido aprobada y confirmada de entrega"]);
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
            DB::table('type_status')->where('id',$request->id)->update([
                'approved_status' => 'devolucion',
            ]);
    
            return response()->json(['msg' => "Devolucion realizada"]);
        }else{
            return response()->json(['msg' => "No ha sido posible realizar la devolucion, verifica que la solicitud haya sido aprobada"]);
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

            return response()->json(['msg' => "Cancelacion realizada"]);
        }else{
            return response()->json(['msg' => "No es posible realizar una cancelacion una vez recibas el producto, se debe realizar una devolucion"]);
        }
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
    
    public function updatePaymentMethod(Request $request)
    {
        $user = auth()->user();

        if($user == null){
            return response()->json([
                'msg' => "Sesión de usuario expirada"
            ]);
        }

        $request->validate([
            'id' => 'required',
            'payment_method_id' => 'required',
        ]);

        DB::table('purchase_requests')->where('id',$request->id)->update([
            'payment_method_id' => $request->payment_method_id,
        ]);

        return response()->json(['msg' => "Método de pago actualizado correctamente"]);
    }
}
