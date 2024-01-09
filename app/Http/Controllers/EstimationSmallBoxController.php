<?php

namespace App\Http\Controllers;

use App\Models\EstimationSmallBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimationSmallBoxController extends Controller
{
    public function index()
    {
        $information = DB::table('estimation_small_box')->select('total','id_user',
            DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as created_date'))->get()->toArray();
        
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
