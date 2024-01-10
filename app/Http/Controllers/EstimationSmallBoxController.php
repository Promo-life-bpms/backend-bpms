<?php

namespace App\Http\Controllers;

use App\Models\EstimationSmallBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimationSmallBoxController extends Controller
{
    public function index()
    {
        $information = DB::table('estimation_small_box')
        ->select('estimation_small_box.total', 'estimation_small_box.id_user', 'users.name',
                  DB::raw('DATE_FORMAT(estimation_small_box.created_at, "%d-%m-%Y") as created_date'))
        ->join('users', 'estimation_small_box.id_user', '=', 'users.id')
        ->get()
        ->toArray();
        
        $sumatoria = DB::table('estimation_small_box')->sum('total');

        return response()->json(['information' => $information, 'sumatoria' => $sumatoria]);
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
