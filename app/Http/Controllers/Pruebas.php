<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Pruebas extends Controller
{
    public function PruebasServidor(){
        $user = DB::table('users')->where('id', 130)->exists();
        if ($user) {
            return response()->json(['message' => 'El servidor está arriba', 'status' => 200]);

        }else{
            return response()->json(['error' => 'El servidor no está disponible', 'status' => 400]);
        }
    }
}
