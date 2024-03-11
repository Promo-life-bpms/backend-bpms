<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CenterController extends Controller
{
    public function show()
    {       
        //$centers = Center::where('status',1)->get();
        $centers = DB::table('centers')->get();
        foreach ($centers as $center) {
            $center->created_at = date('d-m-Y', strtotime($center->created_at));
        }
        return response()->json(['centers' => $centers], 200);
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

    public function deactivateCenters(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        DB::table('centers')->where('id',$request->id)->update([
            'status' => 0,
        ]);

        return response()->json(['message' => "Registro desactivado satisfactoriamente"],200);
    }

    public function activateCenters(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        DB::table('centers')->where('id',$request->id)->update([
            'status' => 1,
        ]);

        return response()->json(['message' => "Registro activado satisfactoriamente"],200);
    }
}
