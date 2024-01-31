<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{
    public function show()
    {       
        $companies = Company::all();

        return $companies;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=> 'required',
        ]);

        $create_company = new Company();
        $create_company->name = $request->name;
        $create_company->description = $request->description;
        $create_company->save();

        return response()->json(['message' => "Registro guardado satisfactoriamente"],200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'=> 'required',
            'name' => 'required',
        ]);

        DB::table('companies')->where('id',$request->id)->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['message' => "Registro actualizado satisfactoriamente"],200);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id'=> 'required',
        ]);

        DB::table('companies')->where('id',$request->id)->delete();

        return response()->json(['message' => "Registro eliminado satisfactoriamente"], 200);
    }
}
