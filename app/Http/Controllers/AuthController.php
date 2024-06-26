<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Notifications\Acces;
use App\Notifications\NotificationAccesUser;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'allUsers', 'syncUsers', "userAccess"]]);
    }

    public function register(Request $request)
    {
        //validación de los datos
        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users',
            'id' => 'required',
            'photo' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name . " " . $request->lastname,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            "photo" => $request->image,
            "intranet_id" => $request->id,
            'remember_token' => Str::random(10),
        ]);

        //alta del usuario
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'msg' => 'Usuario dado de alta correctamente',
            'data' => ['user' => $user]
        ], Response::HTTP_CREATED);
    }
    public function userAccess()
    {
        $users = User::all();
        $data =[];
        foreach ($users as $user) {
            $email =  $user->email;
            $password = str::random(10);
            $user->password = bcrypt($password);
            $user->save();
            Notification::route('mail', $email)
                ->notify(new NotificationAccesUser($password, $email));
           try {
                Notification::route('mail', $email)
                    ->notify(new NotificationAccesUser($password, $email));
            } catch (\Exception $e) {
                array_push($data, [$email,$e->getMessage()]);

            }
        }



        return response()->json(['Correos enviados correctamente']);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        $credentials = request(['email', 'password']);
        $user = User::where("email", "=", $request->email)->first();
        $UserDerails = DB::table('user_details')->where('id_user', $user->id)->first();
        $idDepartment = $UserDerails->id_department;
        $idComapny = $UserDerails->id_company;
        $idArea = $UserDerails->id_area;
        $UserRol = DB::table('role_user')->where('user_id', $user->id)->exists();
        if($idDepartment == 13){
            return response()->json(['message' => 'Aún no tienes asignado un departamento. Acercate con el administrador del sistema en el departamento TI.', 'status'  => 400], 400);
        }elseif($idComapny == 5){
            return response()->json(['message' => 'Aún no tienes asignada una compañia. Acercate con el administrador del sistema en el departamento TI.', 'status'  => 400], 400);
        }elseif($idArea == 34){
            return response()->json(['message' => 'Aún no tienes asignada una área. Acercate con el administrador del sistema en el departamento TI.', 'status'  => 400], 400);
        }elseif (!$UserRol) {
            return response()->json(['message' => 'Aún no tienes asignado un rol. Acercate con el administrador del sistema en el departamento TI.', 'status'  => 400], 400);
        }else{
            if (isset($user->id)) {
                $role = [];
                if (count($user->whatRoles) > 0) {
                    $role = [
                        "id" => $user->whatRoles[0]->id,
                        "name" => $user->whatRoles[0]->name,
                        "display_name" => $user->whatRoles[0]->display_name
                    ];
                }
                if (!$token = auth()->claims([
                    'role' => $role,
                    'user' => [
                        "name" => $user->name,
                        "email" => $user->email,
                        "photo" => $user->photo ? env("URL_INTRANET", "https://intranet.promolife.lat") . '/' . str_replace(' ', '%20', $user->photo) : null],
                        ])->attempt($credentials)) {
                            return response()->json(['msg' => 'No autorizado'], response::HTTP_UNAUTHORIZED); //401
                        }
                        return $this->respondWithToken($token);
                    } else {
                        return response()->json(["msg" => "Correo incorrecto o no registrado"],Response::HTTP_UNAUTHORIZED);
                    }
        }
    }

    public function userProfile(Request $request)
    {
        $user = User::find(auth()->user()->id);
        return response()->json([
            "message" => "Perfil de usuario",
            "userData" => ['user' => $user]
        ], Response::HTTP_OK);
    }

    public function logout()
    {
        auth()->logout();
        return response(["msg" => "Se cerro sesion correctamente"], Response::HTTP_OK);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function allUsers()
    {
        $users = User::with('whatRoles')->where('active', true)->get();
        foreach ($users as $user) {
            $user->photo = $user->photo ? env("URL_INTRANET", "https://intranet.promolife.lat") . '/' . str_replace(' ', '%20', $user->photo) : null;
        }
        return response()->json([
            'msg' => 'Lista de usuarios',
            'data' => ["users" => $users]
        ], response::HTTP_OK);
    }

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
            if (!curl_errno($ch)) {
                $info = curl_getinfo($ch);
                if ($info['http_code'] >= 400) {
                    return response()->json(["msg" => json_decode($res)]);
                } else {
                    curl_close($ch);
                    $res = json_decode($res);
                    foreach ($res as $user) {
                        $searchUser = User::where('intranet_id', $user->id)->first();
                        if ($searchUser === null) {
                            User::create([
                                'name' => $user->name . " " . $user->lastname,
                                'email' => $user->email,
                                'email_verified_at' => now(),
                                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                                "photo" => $user->image,
                                "intranet_id" => $user->id,
                                'remember_token' => Str::random(10),
                            ]);
                        } else {
                            $searchUser->update([
                                'name' => $user->name . " " . $user->lastname,
                                'email' => $user->email,
                                "photo" => $user->image,
                            ]);
                        }
                    }
                    foreach (User::where('active', true)->get() as $user) {
                        $active = false;
                        foreach ($res as $userRes) {
                            if ($userRes->email == $user->email) {
                                $active = true;
                            }
                        }
                        if (!$active) {
                            $user->active = false;
                            $user->save();
                        }
                    }
                    return response()->json(['msg' => 'Actualizacion Completa'], response::HTTP_OK);
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
