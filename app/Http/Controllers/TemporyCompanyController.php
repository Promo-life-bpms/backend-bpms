<?php

namespace App\Http\Controllers;

use App\Models\TemporyCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;
use Psr\Http\Message\ResponseInterface;

class TemporyCompanyController extends Controller
{
    public function index()
    {
        $companies = DB::table('tempory_company')->get()->toArray();
        // Recorrer cada empresa y formatear la fecha
        foreach ($companies as $company) {
            $company->created_at = date('d-m-Y', strtotime($company->created_at));
        }

        return response()->json(['companies' => $companies]);

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
        $deletecompany = DB::table('tempory_company')->where('id', $request->id_company)->update(['status' => 0]);

        if($deletecompany){
            return response()->json(['message' => 'Se eliminó correctamente.', 'status' => 200], 200);
        }
        else{
            return response()->json(['message' => 'No se pudo eliminar', 'status' => 400], 400);
        }
    }

    public function restore(Request $request)
    {
        DB::table('tempory_company')->where('id', $request->id_company)->update(['status' => 1]);
        return response()->json(['message' => 'Se restableció correctamente la empresa', 'status' => 200], 200);
    }
}
