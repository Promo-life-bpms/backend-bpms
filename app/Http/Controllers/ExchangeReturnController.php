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

        return response()->json(['message' => 'Comenzaste el proceso de regresar el sobrante'], 200);
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

        return response()->json(['message' => 'Se confirmo el regreso del dinero'], 200);
    }
}
