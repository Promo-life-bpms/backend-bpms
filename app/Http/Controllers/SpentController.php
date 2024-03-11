<?php

namespace App\Http\Controllers;

use App\Models\Spent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpentController extends Controller
{
    public function show()
    {
        $spents = DB::table('spents')->get();
        foreach ($spents as $spent) {
            $spent->center_id =DB::table('centers')->where('id', $spent->center_id)->select('name')->first();
            $spent->created_at = date('d-m-Y', strtotime($spent->created_at));
        }

        return response()->json(['spents' => $spents], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'concept' => 'required',
            'center_id' => 'required',
            'outgo_type' => 'required',
            'expense_type' => 'required',
        ]);

        $create_spent = new Spent();
        $create_spent->concept = $request->concept;
        $create_spent->center_id = $request->center_id;
        $create_spent->outgo_type = $request->outgo_type;
        $create_spent->expense_type = $request->expense_type;
        $create_spent->product_type = $request->product_type;
        $create_spent->status = 1;
        $create_spent->save();

        return response()->json(['message' => "Registro guardado satisfactoriamente"],200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'concept' => 'required',
            'center_id' => 'required',
            'outgo_type' => 'required',
            'expense_type' => 'required',
        ]);

        DB::table('spents')->where('id',$request->id)->update([
            'concept' => $request->concept,
            'center_id' => $request->center_id,
            'outgo_type' => $request->outgo_type,
            'expense_type' => $request->expense_type,
        ]);

        return response()->json(['message' => "Registro actualizado satisfactoriamente"], 200);
    }

    public function deactivateSpents(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        DB::table('spents')->where('id',$request->id)->update([
            'status' => 0,
        ]);

        return response()->json(['message' => "Registro desactivado satisfactoriamente"], 200);
    }

    public function activateSpents(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        DB::table('spents')->where('id',$request->id)->update([
            'status' => 1,
        ]);

        return response()->json(['message' => "Registro activado satisfactoriamente"], 200);

    }
}
