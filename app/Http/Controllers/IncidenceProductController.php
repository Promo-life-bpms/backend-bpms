<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class IncidenceProductController extends Controller
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
            "user" => "Marlene",
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
        //validar que la informacion este correcta si no no se puede registrar
        // utilizar validator
        $validation = Validator::make($request->all(), [
            'id_order_purchase_products'=>'required',
            'cantidad_seleccionada'=>'required',
        ]);
        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 422);
        };



        $ProductIncidence = IncidenceProductController::create([
            'id_order_purchase_products'=>$request->id_order_purchase_products,
            'cantidad_seleccionada'=>$request->cantidad_seleccionada,
        ]);

        return response()->json('Incidencia de productos creada exitosamente', Response::HTTP_CREATED);
        //return ('Se inserta la incidencia');

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
        $ProductIncidence = Incidencia::destroy($request->id);
        return response()->json([
            "ProductIncidence" => $ProductIncidence,
            "mensaje" => "Borrando registro",
            "display_message" => "La incidencia se ha eliminado corectamente",
            "user" => "Marlene",
        ], 201);
    }
}


