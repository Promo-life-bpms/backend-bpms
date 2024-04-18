<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Pruebas extends Controller
{
    public function PruebasServidor()
    {
        try {
            return response()->json(['message' => 'El servidor está arriba', 'status' => 200]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'El servidor no está arriba', 'status' => 500]);
        }
    }
}
