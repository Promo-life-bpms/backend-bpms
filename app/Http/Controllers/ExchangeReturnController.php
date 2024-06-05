<?php

namespace App\Http\Controllers;

use App\Models\ExchangeReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeReturnController extends Controller
{
    public function ReturnExcessMoney(Request $request)
    {
        $user = auth()->user();

        $this->validate($request,[
            'total_return' => 'required',
            'description' => 'required',
            'purchase_id' => 'required'
        ]);
        $statusConfirmado = DB::table('exchange_returns')->where('purchase_id', $request->purchase_id)->where('status','Confirmado')->exists();
        $ThereIsAlreadyAReturn = DB::table('exchange_returns')->where('purchase_id', $request->purchase_id)->exists();

        if(!$ThereIsAlreadyAReturn){
            $path = '';
            if ($request->hasFile('file_exchange_returns')) {
                $filenameWithExt = $request->file('file_exchange_returns')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('file_exchange_returns')->clientExtension();
                $fileNameToStore = time(). $filename . '.' . $extension;
                $path= $request->file('file_exchange_returns')->move('storage/smallbox/files/', $fileNameToStore);
            }
            
            ExchangeReturn::create([
                'total_return' => $request->total_return,
                'description' => $request->description,
                'purchase_id' => $request->purchase_id,
                'file_exchange_returns' => $path,
                'return_user_id' => $user->id,
            ]);
            return response()->json(['message' => 'Has iniciado el proceso para devolver el excedente de efectivo de tu solicitud.'], 200);
        }elseif($statusConfirmado){
            return response()->json(['message' => 'Ya se ha confirmado tu devolución de efectivo. Ya no puedes comenzar de nuevo el flujo; acércate con el departamento de Tecnología e Innovación.'], 409);
        }
        else{
            $path = '';
            if ($request->hasFile('file_exchange_returns')) {
                $filenameWithExt = $request->file('file_exchange_returns')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('file_exchange_returns')->clientExtension();
                $fileNameToStore = time(). $filename . '.' . $extension;
                $path= $request->file('file_exchange_returns')->move('storage/smallbox/files/', $fileNameToStore);
            }

            DB::table('exchange_returns')->where('purchase_id', $request->purchase_id)->where('status','Sin confirmar')->update([
                'total_return' => $request->total_return,
                'description' => $request->description,
                'file_exchange_returns' => $path,
                'return_user_id' => $user->id,
            ]);
            return response()->json(['message' => 'Has iniciado el proceso para devolver el excedente de efectivo de tu solicitud; sin embargo, este proceso ya estaba en curso. Se eliminará el registro anterior.'], 200);           
        }
    }

    public function ConfirmationReturnMoney(Request $request)
    {
        $user = auth()->user();

        $this->validate($request,[
            'purchase_id' => 'required',
        ]);

        $hora = Carbon::now();

        DB::table('exchange_returns')->where('purchase_id', $request->purchase_id)->update([
            'status' => 'Confirmado',
            'confirmation_datetime' => $hora,
            'confirmation_user_id' => $user->id,
        ]);        
        return response()->json(['message' => 'Se confirmó el regreso del excedente de efectivo de tu solicitud.'], 200);
    }
}
