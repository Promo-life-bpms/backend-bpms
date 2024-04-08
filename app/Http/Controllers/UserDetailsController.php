<?php

namespace App\Http\Controllers;

use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDetailsController extends Controller
{
    public function UserforDepartment($id_department){
        $informacion = DB::table('user_details')->where('id_department', $id_department)->get();

        $ids = [];
        foreach($informacion as $id){
            $id_users = $id->id_user;
            $ids[] = $id_users;
        }

        $users = [];
        foreach($ids as $id_user){
            $info = DB::table('users')->where('id', $id_user)->first();
            $name = $info->name;
            $users[] = $name;
        }

        $ManagerDepartment = DB::table('manager_has_departments')->where('id_department', $id_department)->first();
        $Managerid =  $ManagerDepartment->id_user;
        $nameManager = DB::table('users')->where('id', $Managerid)->select('name')->get();

        
        return response()->json(['Usuarios de cada departamento' => $users, 'El manager del departamento es' => $nameManager, 'status' => 200], 200);

    }
}
