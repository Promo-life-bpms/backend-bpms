<?php

namespace App\Http\Controllers;

use App\Models\TemporyCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;

class TemporyCompanyController extends Controller
{
    public function index()
    {
        $company = DB::table('tempory_company')->where('status', '=', 1)->get()->toArray();
        return response()->json(['company' => $company]);

    }

    public function store(Request $request)
    {
        $user= auth()->user();

        $this->validate($request, [
            'name' => 'required'
        ]);

        $newcompany = TemporyCompany::create([
            'name' => $request->name,
            'status' => 1,
            'id_user' => $user->id
        ]);

        if($newcompany){
            return response()->json(['message' => 'Empresa agregada correctamente', 'status' => 200], 200);
        }
        else
        return response()->json(['message' => 'Error al agregar empresa', 'status' => 400], 400);
    }

    public function delete(Request $request)
    {
        $user = auth()->user();

        $deletecompany = DB::table('tempory_company')->where('id', $request->id_comapny)->update(['status' => 0]);

        if($deletecompany){
            return response()->json(['message' => 'Se elimimo correctamente', 'status' => 200], 200);
        }
        else{
            return response()->json(['message' => 'No se pudo eliminar', 'status' => 400], 400);
        }
    }
}
