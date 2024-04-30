<?php

namespace App\Http\Controllers;

use App\Models\ExManagerDepartment;
use App\Models\ManagerHasDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerHasDepartmentController extends Controller
{

    public function ViewManager()
    {
        $InfoManager = DB::table('manager_has_departments')->get()->toArray();
        $Managers = [];
        foreach($InfoManager as $Manager){
            $nameUser = DB::table('users')->where('id',$Manager->id_user)->value('name');
            $NameDepartment = DB::table('departments')->where('id', $Manager->id_department)->value('name_department');
            $Info = [
                'id' => $Manager->id,
                'Name_of_the_Manager'=> $nameUser,
                'Name_of_the_Department' => $NameDepartment,
            ];
            $Managers[] = $Info;
        }
        return response()->json(['Managers' => $Managers]);
    }
    public function CrearManager(Request $request)
    {
        $this->validate($request,[
            'id_user' => 'required',
            'id_department' => 'required',
        ]);

        $restricción = DB::table('manager_has_departments')->where('id_user', $request->id_user)->first();
        $idUser = $restricción->id_user;
        $idDepartment =$restricción->id_department;

        if(($request->id_user == $idUser) && ($request->id_department == $idDepartment)){

            return response()->json(['message' => 'Este registro ya existe', 'status' => 409], 409);

        }else{
            ManagerHasDepartment::create([
                'id_user' => $request->id_user,
                'id_department' => $request->id_department
            ]);

        }
        return response()->json(['message' => 'Agregaste un nuevo manager', 'status' => 200], 200);
    }

    public function DeleteManager(Request $request)
    {
        $user = auth()->user();

        $this->validate($request,[
            'id' => 'required',
        ]);

        $exManager = DB::table('manager_has_departments')->where('id', $request->id)->first();

        ExManagerDepartment::create([
            'id_manager_has_department' => $request->id,
            'user_who_deleted' => $user->id,
            'ex_manager' => $exManager->id_user,
            'id_department' => $exManager->id_department,
        ]);

        DB::table('manager_has_departments')->where('id', $request->id)->delete();

        return response()->json(['message' => 'Registro eliminado correctamente', 'status'=> 200], 200);

    }

}
