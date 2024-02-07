<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CenterController extends Controller
{
    public function show()
    {       
        $spents = Center::where('status',1)->get();

        return response()->json(['spents' => $spents], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $create_spent = new Center();
        $create_spent->name = $request->name;
        $create_spent->description = $request->description;
        $create_spent->status = 1;
        $create_spent->save();

        return response()->json(['message' => "Registro guardado satisfactoriamente"],200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required',
        ]);

        DB::table('centers')->where('id',$request->id)->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['message' => "Registro actualizado satisfactoriamente"],200);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        DB::table('centers')->where('id',$request->id)->update([
            'status' => 0,
        ]);

        return response()->json(['message' => "Registro eliminado satisfactoriamente"],200);
    }
}
