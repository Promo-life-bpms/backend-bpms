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
        $validation = Validator::make($request->all(), [
            '*.description' => 'required',
            '*.status_checklist' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'msg' => "Error al validar información de la ruta de entrega",
                'data' => ['errorValidacion' => $validation->getMessageBag()]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $check = ModelsCheckList::where('code_sale', $sale_id)->first();



        $check_lists = [];

        foreach ($request->all() as $item) {
            $check_list = ModelsCheckList::create([
                "code_sale" => $sale_id,
                "description" => $item['description'],
                "status_checklist" => $item['status_checklist'],
            ]);

            $check_lists[] = $check_list;
        }

        return response()->json([
            'msg' => 'Check-lists creadas exitosamente',
            'data' => [
                "check-lists" =>  $check_lists,
            ]
        ], Response::HTTP_CREATED);
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
