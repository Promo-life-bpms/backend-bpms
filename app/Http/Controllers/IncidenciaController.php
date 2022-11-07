<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;

class IncidenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Incidencia = Incidencia::all();

        return response()->json([
            "Incidencia" => $Incidencia,
            "mensaje" => "OK",
            "display_message" => "La peticion se ejecuto correctamente",
            "user" => "Antonio",
            "token" => "MDJEIOHDNCS",
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        request()->validate([
            "empresa"=>"required",
            "cliente"=>"required",
        ]);

        $Incidencia = new Incidencia();
        $Incidencia->empresa = $request->empresa;
        $Incidencia->cliente = $request->cliente;

        $Incidencia->save();
        return response()->json([
            "Incidencia" => $Incidencia,
            "mensaje" => "OK",
            "display_message" => "La peticion se ejecuto",
            "user" => "Antonio",
            "token" => "MDJEIOHDNCS",
        ], 201);
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
        // request()->validate([
        //     "empresa"=>"required",
        //     "cliente"=>"required",
        // ]);


        //
        $Incidencia = Incidencia::findOrFail($request->id);
        $Incidencia->empresa = $request->empresa;
        $Incidencia->cliente = $request->cliente;
        $Incidencia->save();
        return response()->json([
            "Incidencia" => $Incidencia,
            "mensaje" => "Borrando registro",
            "display_message" => "El registro se ha eliminado corectamente",
            "user" => "Antonio",
        ], 201);
        }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $Incidencia = Incidencia::destroy($request->id);
        return response()->json([
            "Incidencia" => $Incidencia,
            "mensaje" => "Borrando registro",
            "display_message" => "El registro se ha eliminado corectamente",
            "user" => "Antonio",
        ], 201);
    }
}
