<?php

namespace App\Http\Controllers;

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
                'Name of the manager'=> $nameUser,
                'Name of the Department' => $NameDepartment,
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
        
        ManagerHasDepartment::create([
            'id_user' => $request->id_user,
            'id_department' => $request->id_department
        ]);
        
        return response()->json(['message' => 'Agregaste un nuevo manager', 'status' => 200], 200);
    }

    public function UpdateManager(Request $request){
        $this->validate($request,[
            'id_manager' => 'required',
            'id_user' => 'required',
            'id_department' => 'required',
        ]);

        $editManager = DB::table('manager_has_departments')->where('id', $request->id_manager)->update([
            'id_user'=> $request->id_user,
            'id_department'=> $request->id_department,
        ]);

        if($editManager){
            return response()->json(['message' => 'Manager editado correctamente', 'status' => 200], 200);
        }else{

            return response()->json(['message' => 'No se pudo editar el manager', 'status' => 404], 404);
        }
    }

}
