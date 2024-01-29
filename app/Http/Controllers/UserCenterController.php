<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\User;
use App\Models\UserCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCenterController extends Controller
{
    public function show()
    {       
        $data = [];

        $users = User::where('active',1)->get();

        foreach($users as $user){
            $user_center_data = [];
            
            if($user->userCenter != null){
                foreach($user->userCenter as $user_center){
                    array_push($user_center_data, (object)[
                        'id' => $user_center ->id,
                        'center_name' => $user_center->center->name,
                        'center_id'=> $user_center->id,
                    ]);
                }
            }
            
            array_push($data, (object)[
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'centers' =>  $user_center_data,
            ]);
        }

        return $data;
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'=> 'required',
            'center_id' => 'required',
        ]);

        $create_user_center = new UserCenter();
        $create_user_center->user_id = $request->user_id;
        $create_user_center->center_id = $request->center_id;
        $create_user_center->save();

        return response()->json(['message' => "Registro guardado satisfactoriamente"], 200);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id'=> 'required',
            'user_id'=> 'required',
            'center_id' => 'required',
        ]);

        DB::table('user_has_center')->where('id',$request->id)->where('user_id',$request->user_id)->where('center_id',$request->center_id)->delete();

        return response()->json(['message' => "Registro eliminado satisfactoriamente"], 200);
    }
}
