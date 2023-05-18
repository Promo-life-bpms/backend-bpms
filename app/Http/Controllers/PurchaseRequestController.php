<?php

namespace App\Http\Controllers;

use App\Models\PurchaseDevolution;
use App\Models\PurchaseRequest;
use App\Models\PurchaseStatus;
use App\Models\Spent;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PurchaseRequestController extends Controller
{
    public function show()
    {
        //Status
        //0: PENDIENTE
        //1: APROBADA
        //2: RECHAZADA
        //3: ELIMINADA
        $spents = PurchaseRequest::where('status','<>',3)->get();

        return $spents;
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'company_id' => 'required',
            'spent_id' => 'required',
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
        $create_spent->description = $request->description;
        $create_spent->file = $path;
        $create_spent->commentary = '';
        $create_spent->purchase_status_id = 1;
        $create_spent->payment_method_id = $request->payment_method_id;
        $create_spent->total = $request->total;
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
            'description' => 'required',
            'file	' => 'required',
            'commentary' => 'required',

            'purchase_status_id' => 'required',
            'payment_method_id' => 'required',
            'total' => 'required',
        ]);

        $spent = Spent::where('id',$request->id)->last()->get();

        $path = $spent->file;

        if ($request->hasFile('file')) {
            File::delete($spent->file);
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->clientExtension();
            $fileNameToStore = time(). $filename . '.' . $extension;
            $path= $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
        }

        DB::table('spents')->where('id',$request->id)->update([
            'company_id' => $request->company_id,
            'spent_id' => $request->spent_id,
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

        DB::table('spents')->where('id',$request->id)->update([
            'status' => 4,
        ]);

        return response()->json(['msg' => "Registro eliminado satisfactoriamente"]);
    }

    public function approved(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

    
        DB::table('purchase_requests')->where('id',$request->id)->update([
            'status' => 1,
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
        DB::table('purchase_requests')->where('id',$request->id)->update([
            'status' => 2,
            'commentary' => $commentary
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
}
