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

        $path = '';
        if ($request->hasFile('file')) {
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->clientExtension();
            $fileNameToStore = time(). $filename . '.' . $extension;
            $path= $request->file('file')->move('storage/smallbox/files/', $fileNameToStore);
        }
        
        ExchangeReturn::create([
            'total_return' => $request->total_return,
            'description' => $request->description,
            'purchase_id' => $request->purchase_id,
            'file' => $path,
            'return_user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Comenzaste el proceso de regresar el sobrante']);
    }

    public function ConfirmationReturnMoney(Request $request)
    {
        $user = auth()->user();

        $this->validate($request,[
            'id' => 'required',
        ]);

        $hora = Carbon::now();

        DB::table('exchange_returns')->where('id', $request->id)->update([
            'status' => 'Confirmado',
            'confirmation_datetime' => $hora,
            'confirmation_user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Se confirmo el regreso del dinero']);
    }
}
