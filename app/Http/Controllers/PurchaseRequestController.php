<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseStatus;
use App\Models\Spent;
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

        $purchase_request = PurchaseRequest::where('id',$request->id)->get();
        
        $status = PurchaseStatus::where('id',$request->id)->get()->last();
       
        if($status == )

    }
}
