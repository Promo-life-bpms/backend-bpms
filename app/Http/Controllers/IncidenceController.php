<?php

namespace App\Http\Controllers;

use App\Models\Incidence;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Contracts\Service\Attribute\Required;

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



    public function show($incidencia_id)
    {
        $incidencia = Incidence::where('num_incidencia', $incidencia_id)->first();
        if (!$incidencia) {
            return response()->json(["errors" => "No se ha encontrado la incidencia"], 404);
        }
        return response()->json(["msj"=>$incidencia]);
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $sale_id)
    {
        //validar que la informacion este correcta si no no se puede registrar
        // utilizar validator
        $validation = Validator::make($request->all(), [
            'area' => 'required',
            'motivo' => 'required',
            'tipo_de_producto' => 'required',
            'tipo_de_tecnica' => 'required',
            'solucion_de_incidencia' => 'required',
            'responsable' => 'required',
            'fecha_creacion' => 'required',
            'status' => 'required',
            'evidencia' => 'required',
            'fecha_compromiso' => 'required',
            'solucion' => 'required',
            'fecha_solucion' => 'required',
            'id_user' => 'required',
            'elaboro' => 'required',
            'firma_elaboro' => 'required',
            'reviso' => 'required',
            'firma_reviso' => 'required',
            'comentarios_generales' => 'required',


            'incidence_products' => 'required|array',
            'incidence_products.*.id_order_purchase_products' => 'required',
            'incidence_products.*.cantidad_seleccionada' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 422);
        }
        $sale = Sale::where('code_sale', $sale_id)->first();
        if (!$sale) {
            return response()->json(["errors" => "No se ha encontrado el pedido"], 404);
        }

        //Crea codigo de incidencia
        $maxINC = Incidence::max('num_incidencia');
        $idinc = null;
        if (!$maxINC) {
            $idinc = 1;
        } else {
            $idinc = (int) explode('-', $maxINC)[1];
            $idinc++;
        }

        $incidencia = Incidence::create([
            'num_incidencia' => "INC-" . str_pad($idinc, 5, "0", STR_PAD_LEFT),
            'area' => $request->area,
            'motivo' => $request->motivo,
            'tipo_de_producto' => $request->tipo_de_producto,
            'tipo_de_tecnica' => $request->tipo_de_tecnica,
            'solucion_de_incidencia' => $request->solucion_de_incidencia,
            'responsable' => $request->responsable,
            'fecha_creacion' => $request->fecha_creacion,
            'status' => $request->status,
            'evidencia' => $request->evidencia,
            'fecha_compromiso' => $request->fecha_compromiso,
            'solucion' => $request->solucion,
            'fecha_solucion' => $request->fecha_solucion,
            'id_user' => $request->id_user,
            'elaboro' => $request->elaboro,
            'firma_elaboro' => $request->firma_elaboro,
            'reviso' => $request->reviso,
            'firma_reviso' => $request->firma_reviso,
            'comentarios_generales' => $request->comentarios_generales,
            'id_sales' => $sale->id
        ]);
        foreach ($request->incidence_products as $incidence_product) {
            $incidence_product = (object)$incidence_product;

            $IncidenceProducts =  $incidencia->incidenciaProducto()->create([
                'id_order_purchase_products' => $incidence_product->id_order_purchase_products,
                'cantidad_seleccionada' => $incidence_product->cantidad_seleccionada
            ]);

        }

        //
        return response()->json(["msg" => 'Este es el Status'], 201);
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
