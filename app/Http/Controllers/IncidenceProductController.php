<?php

namespace App\Http\Controllers;

use App\Models\Incidence;
use Illuminate\Http\Request;
use App\Models\Incidencia;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class IncidenceProductController extends Controller
{

    /**
     * Obtiene todas las incidencias.
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Crea una nuevos productos de incidencia.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //validar que la informacion este correcta si no no se puede registrar
        // utilizar validator
        $validation = Validator::make($request->all(), [
            'id_order_purchase_products' => 'required',
            'cantidad_seleccionada' => 'required',
            'id_incidence' => 'required'
        ]);
        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 422);
        };



        $ProductIncidence = IncidenceProductController::create([
            'id_order_purchase_products' => $request->id_order_purchase_products,
            'cantidad_seleccionada' => $request->cantidad_seleccionada,
            'id_incidence' => $request->id_incidence
        ]);

        return response()->json('Incidencia de productos creada exitosamente', Response::HTTP_CREATED);
        //return ('Se inserta la incidencia');

    }

    /**
     * Elimina una incidencia de producto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        //
        $ProductIncidence = Incidence::destroy($request->id);
        return response()->json([
            "msg" => "La incidencia se ha eliminado correctamente",
            "data"  =>  ['ProductIncidence', $ProductIncidence],
        ], response::HTTP_OK); //201
    }
}
