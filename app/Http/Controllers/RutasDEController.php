<?php

namespace App\Http\Controllers;

use App\Models\RutasEntrega;
use App\Http\Controllers\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RutasDEController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('RutasdeEntrega');
    }


    public function index()
    {
        //
        $rutas = RutasEntrega::all();


        return response()->json([
            "RutasdeEntrega" => $rutas,
            "mensaje" => "OK",
            "display_message" => "Acceso de rutas correcto",

        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $rutas= RutasEntrega::findOrFail($request->id);

            $rutas->tipoOrigen = $request->tipoOrigen;
            $rutas->tipoDestino = $request-> tipoDestino;
            $rutas->direccionOrigen = $request-> direccionOrigen;
            $rutas->direccionDestino = $request-> direccionDestino;
            $rutas->hora = $request-> hora;
            $rutas->atencion_a = $request->atencion_a;
            $rutas->accion = $request->accion;
            $rutas->num_guia = $request->num_guia;
            $rutas->observaciones = $request->observaciones;
            return response()->json([
                "Rutas_de_Entrega" => $rutas,
                "mensaje" => "Borrando ruta",
                "display_message" => "La ruta se ha eliminado o modificado correctamente",]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rutas= RutasDEController::destroy($request->id);
        return response()->json([
            "Rutas_de_Entrega" => $rutas,
            "mensaje" => "Borrando ruta",
            "display_message" => "La ruta se ha eliminado corectamente",
        ], 201);
    }
}
