<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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

        $user =  User::create([
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
        return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where("email", "=", $request->email)->first();


        if (isset($user->id)) {
            if (Hash::check($request->password, $user->password)) {
                //se crea token
                $token = $user->createToken('auth_token')->plainTextToken;
                $cookie = cookie('cookie_token', $token, 60 * 24);
                return response()->json(["token" => $token],)->withoutCookie($cookie);
                return response()->json([
                    "msg" => "Acceso correcto",
                    "access_token" => $token
                ]);                    //si todo sale bien

            } else {
                return response()->json([
                    "status" => 1,
                    "msg" => "La password es incorrecta",
                ], Response::HTTP_UNAUTHORIZED);
            }
        } else {
            return response()->json([
                "status" => 2,
                "msg" => "Correo incorrecto o no registrado"
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function userProfile(Request $request)
    {
        return response()->json([
            "message" => "Perfil de usuario",
            "userData" => Auth()->user()
        ], Response::HTTP_OK);
    }

    public function logout()
    {
        $cookie = Cookie::forget('cookie_token');
        return response(["message" => "Se cerro sesion correctamente"], Response::HTTP_OK)->withCookie($cookie);
    }

    public function allUsers()
    {
        $users = User::with('whatRoles')->where('active', true)->get();
        foreach ($users as $user) {
            $user->photo = env("URL_INTRANET", "https://intranet.promolife.lat") . '/' . $user->photo;
        }
        return response()->json([
            "users" => $users
        ]);
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
                    return response()->json(['msg' => 'Actualizacion Completa']);
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
