<?php

namespace App\Http\Controllers;

use App\Models\LackOfMoneyEventuals;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LackOfMoneyEventualsController extends Controller
{
    public function ConfirmationReturnMoneyEventuales(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'purchase_id' => 'required'
        ]);
        
        ////////////////////PARA CONFIRMAR CUANDO ES MENOR EL DINERO/////////////////////////////
        $infoReturn  = DB::table('return_money_from_eventualities')->where('id_purchase', $request->purchase_id)->where('status', 'Sin confirmar')->exists();
        if($infoReturn){
            $eventuales = DB::table('eventuales')->where('purchase_id', $request->purchase_id)->get();
            $pays = [];
            foreach ($eventuales as $eventual) {
                $eventualArray = json_decode($eventual->eventuales, true);
                foreach ($eventualArray as $item) {
                    $pays[] = $item['pay'];
                }
            }
            $total_pay = array_sum($pays);
            DB::table('purchase_requests')->where('id', $request->purchase_id)->update([
                'total' => $total_pay,
            ]);
            
            $hora = Carbon::now();
            DB::table('return_money_from_eventualities')->where('id_purchase', $request->purchase_id)->update([
                'status' => 'Confirmado',
                'confirmation_datetime' => $hora,
                'id_person_who_delivers' => $user->id,
            ]);
        }

        ////////////////PARA CONFIRMAR CUANDO ES MAYOR EL DINERO///////////////////////////
        $infoMore = DB::table('lack_of_money_eventuals')->where('id_purchase', $request->purchase_id)->where('status','Sin confirmar')->exists();
        if($infoMore){
            $eventuales = DB::table('eventuales')->where('purchase_id', $request->purchase_id)->get();
            $pays = [];
            foreach ($eventuales as $eventual) {
                $eventualArray = json_decode($eventual->eventuales, true);
                foreach ($eventualArray as $item) {
                    $pays[] = $item['pay'];
                }
            }
            $total_pay = array_sum($pays);
            DB::table('purchase_requests')->where('id', $request->purchase_id)->update([
                'total' => $total_pay,
            ]);
        
            $hora = Carbon::now();
            DB::table('lack_of_money_eventuals')->where('id_purchase', $request->purchase_id)->update([
                'status' => 'Confirmado',
                'confirmation_datetime' => $hora,
                'id_person_who_delivers' => $user->id,
            ]);
        }

        return response()->json(['message' => 'Se confirmo el regreso del dinero'], 200);

    }

}
