<?php

namespace App\Http\Controllers;

use App\Models\Incidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidenceController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Incidencia = Incidence::all();

        return response()->json([
            "Incidencia" => $Incidencia,
            "mensaje" => "OK",
            "user" => "Marlene",
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validar que la informacion este correcta si no no se puede registrar
        // utilizar validator
        $validation = Validator::make($request->all(), [
            'motivo_area' => 'required',
            'motivo' => 'required',
            'tipo_de_producto' => 'required',
            'tipo_de_tecnica' => 'required',
            'solucion_de_incidencia' => 'required',
            'responsable' => 'required',
            'status' => 'required',
            'evidencia' => 'required',
            'fecha_compromiso' => 'required',
            'elaboro' => 'required',
            'firma_elaboro' => 'required',
            'reviso' => 'required',
            'firma_reviso' => 'required',
            'comentarios_generales' => 'required',
            'id_sales' => 'required'
        ]);
        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 422);
        }
        $incidencia = null;
        $incidencia = new Incidence();
        $incidencia->motivo_area = $request->motivo_area;
        $incidencia->save();
    /*      $incidencia = Incidencia::create([
            'motivo_area' => $request->motivo_area,
                       'motivo' => $request->motivo,
            'tipo_de_producto' => $request->tipo_de_producto,
            'tipo_de_tecnica' => $request->tipo_de_tecnica,
            'solucion_de_incidencia' => $request->solucion_de_incidencia,
            'responsable' => $request->responsable,
            'status' => $request->status,
            'evidencia'=> $request->evidencia,
            'fecha_compromiso' => $request->fecha_compromiso,
            'elaboro' => $request->elaboro,
            'firma_elaboro' => $request->firma_elaboro,
            'reviso' => $request->reviso,
            'firma_reviso' => $request->firma_reviso,
            'comentarios_generales' => $request->comentarios_generales,
            'id_sales' => $request->id_sales,
        ]);*/
        return $incidencia;

        /* return response()->json(["msg"=>'Incidencia creada exitosamente'], 201); */
        //return ('Se inserta la incidencia');

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
        $Incidencia = Incidence::destroy($request->id);
        return response()->json([
            "Incidencia" => $Incidencia,
            "mensaje" => "Borrando registro",
            "display_message" => "La incidencia se ha eliminado corectamente",
            "user" => "Marlene",
        ], 201);
    }
}
