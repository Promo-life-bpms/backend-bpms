<?php

namespace App\Http\Controllers;

use App\Models\EstimationSmallBox;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimationSmallBoxController extends Controller
{
    public function index()
    {
        ///OBTGENEMOS LA INFORMACION PARA LA TABLA///
        $information = DB::table('estimation_small_box')
        ->select('estimation_small_box.total', 'estimation_small_box.id_user', 'users.name',
                  DB::raw('DATE_FORMAT(estimation_small_box.created_at, "%d-%m-%Y") as created_date'))
        ->join('users', 'estimation_small_box.id_user', '=', 'users.id')
        ->get()
        ->toArray();

        ///OBTENEMOS EL PRIMER DÍA DEL MES Y EL ÚLTIMO///        
        $primerDiaDelMes = Carbon::now()->startOfMonth();
        $ultimoDiaDelMes = Carbon::now()->endOfMonth();

        // Verificar si la fecha actual está dentro del mes
        if (Carbon::now()->between($primerDiaDelMes, $ultimoDiaDelMes)) {
            // Si estamos en el mes actual, realizar la suma
            $presupuestomensual = DB::table('estimation_small_box')
                ->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                ->sum('total');
        }

        ///CONDICIONES PARA PODER SUMAR EL CAMPO "total"///
        $gastosmentuales = DB::table('purchase_requests')->whereBetween('created_at', [$primerDiaDelMes, $ultimoDiaDelMes])
                                                        ->where(function ($query) {
                                                            $query->where(function ($subquery) {
                                                                $subquery->where('purchase_status_id', '=', 4)->where('type_status', '=', 'normal');
                                                            })->orWhere(function ($subquery) {
                                                                $subquery->where('purchase_status_id', '=', 2)->where('type_status', '=', 'normal');
                                                            });
                                                        })->sum('total');
                                                            
        $presupuestodisponible = $presupuestomensual - $gastosmentuales;

        return response()->json(['information' => $information, 'presupuestomensual' => $presupuestomensual, 'gastosmentuales' => $gastosmentuales, 'presupuestodisponible' => $presupuestodisponible]);   
    }

    public function create(Request $request)
    {
        $user= auth()->user();

        $this->validate($request,[
            'total' => 'required'
        ]);

        $presupuesto = EstimationSmallBox::create([
            'total' => $request->total,
            'id_user' => $user->id,
        ]);

        if($presupuesto){
            return response()->json(['message' => 'exito', 'status' => 200], 200);
        }
        else
        return response()->json(['message' => 'error', 'status' => 400], 400);
    }
}
