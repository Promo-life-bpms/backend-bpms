<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{

    public function AllDepartments(){
        $departments = DB::table('departments')->get()->toArray();
        return response()->json(['departments'=> $departments, 'status' => 200], 200);
    }

    public function AddDepartment(Request $request){

        $this->validate($request,[
            'name_department' => 'required',
        ]);
        
        $department = Department::create([
            'name_department' => $request->name_department,
            'status' => 1
        ]);

        if($department){
            return response()->json(['message' => 'Departamento creado con éxtio', 'status' => 200], 200);
        }
    }

    public function UpdatedDepartment(Request $request){
        
        $this->validate($request,[
            'id_department' => 'required',
            'name_department' => 'required',
        ]);

        $updatedDepartment = DB::table('departments')->where('id', $request->id_department)->update([
            'name_department' => $request->name_department,
        ]);

        if($updatedDepartment){
            return response()->json(['message' => 'Se edito correctamente el departamento', 'status' => 200], 200);
        }
    }
}
