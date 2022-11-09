<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        //validación de los datos
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
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
                ],);
            }
        } else {
            return response()->json([
                "status" => 2,
                "msg" => "Correo incorrecto o no registrado"
            ]);
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
        $users = User::with('whatRoles')->get();
        return response()->json([
            "users" => $users
        ]);
    }
}
