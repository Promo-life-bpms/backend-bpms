<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetails;
use App\Models\UserRole;
use App\Notifications\RegisteredUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with("whatRoles")->where('active', true)->get();
    
        foreach ($users as $user) {
            $details = DB::table('user_details')->where('id_user', $user->id)->first();
    
            if ($details) {
                $department = DB::table('departments')->where('id', $details->id_department)->value('name_department');
                $department_id = DB::table('departments')->where('id', $details->id_department)->value('id');
                $company = DB::table('companies')->where('id', $details->id_company)->value('name');
                $company_id = DB::table('companies')->where('id', $details->id_company)->value('id');
            } else {
                $department = null;
                $company = null;
            }
            $user->department = $department;
            $user->company = $company;
            $user->department_id = $department_id;
            $user->company_id = $company_id;
            
        }
    
        return response()->json([$users, 'status' => 200], 200);
    }
    

    // Crear el metodo create con el name, email, active
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'id_department' => 'required',
            'id_company' => 'required',
            'role_id' => 'required',
            ////'roles' => 'required|array',
        ]);
        if ($validation->fails()) {
            return response()->json(
                [
                    'msg' => "Error de validacion",
                    'data' => ['errorValidacion' => $validation->getMessageBag()]
                ],
                response::HTTP_UNPROCESSABLE_ENTITY
            ); // 422
        }

        $pass = Str::random(8);
        $password = Hash::make($pass);
        $user = new User();
        $user->name = $request->name;
        $user->active = 1;
        $user->email = $request->email;
        $user->password = $password;
        $user->save();

        $iduser = $user->id;

        $usersDetails = new UserDetails();
        $usersDetails->id_user = $iduser;
        $usersDetails->id_department = $request->id_department;
        $usersDetails->id_company = $request->id_company;
        $usersDetails->save();

        $UserRol = new UserRole();
        $UserRol->role_id = $request->role_id;
        $UserRol->user_id = $iduser;
        $UserRol->user_type = "App\Models\User";
        $UserRol->save();

        // Asignar roles que vienen en el request en formato de array
        //$user->syncRoles($request->roles);
        /*try {
            $dataNotification = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $pass,
                'url' => env("URL_FRONTEND", "https://bpms.promolife.lat")
            ];
            // Enviar email con la contraseña
            $user->notify(new RegisteredUser($dataNotification));
        } catch (Exception $th) {
            return response()->json(["usuario" => $user, 'message' => 'Usuario creado correctamente, pero no se pudo enviar el correo']);
        }*/
        return response()->json(["usuario" => $user, "Detalles del usuario" => $usersDetails, "Rol asignado" => $UserRol ,'message' => 'Usuario creado correctamente', "status" => 200], 200);
    }

    // Metodo para actualizar el usuario
    public function update(Request $request)
    {
        $this->validate($request, [
            'id_user' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'id_department' => 'required',
            'id_company' => 'required',
            'role_id' => 'required',
        ]);

        $user = User::find($request->id_user);
        if (!$user) {
            return response()->json(["message" => "El usuario no existe"], Response::HTTP_NOT_FOUND);
        }

        DB::table('users')->where('id', $request->id_user)->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        //ACTUALIZAMOS LOS DETALLES DEL USUARIO//
        $busqueda = DB::table('user_details')->where('id_user', $request->id_user)->value('id_user');
        //dd($busqueda);

        if(!$busqueda){
            UserDetails::create([
                'id_user' => $request->id_user,
                'id_department' => $request->id_department,
                'id_company' => $request->id_company,
            ]);
        }else{
            DB::table('user_details')->where('id_user', $request->id_user)->update([
                'id_department' => $request->id_department,
                'id_company' => $request->id_company,
            ]);
        }

        ///ACTUALIZAR EL ROL///
        DB::table('role_user')->where('user_id', $request->id_user)->update([
            'role_id' => $request->role_id,
        ]);
        $newValues = [
            'name' => $request->name,
            'email' => $request->email,
            'id_department' => $request->id_department,
            'id_company' => $request->id_company,
            'role_id' => [$request->role_id], // Convertir el ID del rol en un array para ser consistente con $oldValues
        ];
    
        // Comparar los valores y enviar la respuesta JSON
        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'status' => 200,
            'new_values' => $newValues,
        ], 200);
    }

    // Metodo para eliminar el usuario que solo desactiva el usuario
    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "El usuario no existe"], Response::HTTP_NOT_FOUND);
        }
        $user->active = false;
        $user->email = $user->email . "-" . Str::random(5);
        $user->save();
        return response()->json(["usuario" => $user, 'message' => 'Usuario eliminado correctamente']);
    }
    // Enviar nuevo acceso por email
    public function sendNewAccess($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "El usuario no existe"], Response::HTTP_NOT_FOUND);
        }
        $pass = Str::random(8);
        $password = Hash::make($pass);
        $user->password = $password;
        $user->save();
        try {
            // Enviar email con la contraseña
            $dataNotification = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $pass,
                'url' => env("URL_FRONTEND", "https://bpms.promolife.lat")
            ];
            // Enviar email con la contraseña
            $user->notify(new RegisteredUser($dataNotification));
        } catch (Exception $th) {
            return response()->json(["usuario" => $user, 'message' => 'Usuario creado correctamente, pero no se pudo enviar el correo']);
        }
        return response()->json(["usuario" => $user, 'message' => 'Usuario creado correctamente']);
    }

    // Sincronizar usuarios de la intranet
    public function syncUsers()
    {
        try {
            $urlIntranet = env("URL_INTRANET", "https://intranet.promolife.lat");
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlIntranet . "/api/getUsers");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'token: r8349ru894ruc3ruc39rde3wcdx',
            ]);
            $res = curl_exec($ch);
            $errores = [];
            if (!curl_errno($ch)) {
                $info = curl_getinfo($ch);
                if ($info['http_code'] >= 400) {
                    return response()->json(["msg" => json_decode($res)]);
                } else {
                    curl_close($ch);
                    $res = json_decode($res);
                    // return ($res);
                    foreach ($res as $user) {
                        $searchUser = User::where('intranet_id', $user->id)->first();
                        if ($searchUser === null) {
                            // Revisar que el correo no se haya usado o guradar ese dato en un array
                            if (User::where('email', $user->email)->first()) {
                                $errores[] = "Este email, " .  $user->email . ", ya esta en uso";
                            } else {
                                User::create([
                                    'name' => $user->name . " " . $user->lastname,
                                    'email' => $user->email,
                                    'email_verified_at' => now(),
                                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                                    "photo" => $user->image,
                                    "intranet_id" => $user->id,
                                    'remember_token' => Str::random(10),
                                ]);
                            }
                        } else {
                            $searchUser->update([
                                'name' => $user->name . " " . $user->lastname,
                                'email' => $user->email,
                                "photo" => $user->image,
                                "intranet_id" => $user->id,
                            ]);
                        }
                    }
                    foreach (User::where('active', true)->whereNotNull('intranet_id')->get() as $user) {
                        $active = false;
                        foreach ($res as $userRes) {
                            if ($userRes->email == $user->email) {
                                $active = true;
                            }
                        }
                        if (!$active) {
                            if ($user->intranet_id != null) {
                                $user->active = false;
                                $user->save();
                            }
                        }
                    }
                    return response()->json(['msg' => 'Actualizacion Completa', "errores" => $errores], Response::HTTP_OK);
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
