<?php

namespace App\Http\Controllers;

use App\Models\Eventuales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventualesController extends Controller
{
    public function Eventuales(Request $request)
    {
        $this->validate($request,[
            'eventuales' => 'required',
        ]);

        $eventuales = Eventuales::create([
            'eventuales' => $request->eventuales,
            'purchase_id' => $request->purchase_id,
        ]);

        if($eventuales)
        {
            return response()->json(['message' => 'se guardaron los eventuales', 'status' => 200], 200);
        }
        else{
            return response()->json(['message' => 'error', 'status' => 400], 400);
        }
    }
}
