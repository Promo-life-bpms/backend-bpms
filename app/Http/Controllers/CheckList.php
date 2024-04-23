<?php

namespace App\Http\Controllers;

use App\Models\CheckList as ModelsCheckList;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CheckList extends Controller
{
    public function create(Request $request, $sale_id)
    {
        $dataValidation = [
            'description' => 'required',
            'status_checklist' => 'required',

        ];
        $validation = Validator::make($request->all(), $dataValidation);

        if ($validation->fails()) {
            return response()->json([
                "msg" => 'No se registro correctamente la informacion',
                'data' =>
                ["errorValidacion" => $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $check = ModelsCheckList::where('code_sale', $sale_id)->first();
        if ($check->status_checklist == 'Creado') {
            # code...

            $check_list = ModelsCheckList::create([
                "code_sale" => $sale_id,
                "description" => $request->description,
                "status_checklist" => $request->status_checklist,
            ]);
        } else {
            return response()->json([
                "msg" => 'No existe una check-list'
            ], response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if ($check_list) {
            return response()->json([
                "msg" => 'Check-list creado exitosamente',
                'data' =>
                [

                    "checklist" => $check_list,

                ]
            ], response::HTTP_CREATED);
        }
        return response()->json([
            "msg" => 'Check-list no se pudo crear correctamente',
        ], response::HTTP_BAD_REQUEST);
    }

    public function show($sale_id)
    {
        $check_li = ModelsCheckList::where('code_sale', $sale_id)->get();

        if (!$check_li) {
            return response()->json([
                "msg" => 'No hay un check-list aun del pedido',
            ], response::HTTP_BAD_REQUEST);
        }

        return response()->json(["msg" => "Detalle del check-list", 'data' => ["check-list" => $check_li]], response::HTTP_OK);
    }
}
