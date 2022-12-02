<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InspectionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'date_inspeccion' => 'required|date:Y-m-d h:i:s',
            'type_product' => 'required|in:limpio,maquilado',
            'observations' => 'required|string',
            'user_signature_created' => 'required',
            'user_reviewed' => 'required',
            'user_signature_reviewed' => 'required',
            'quantity_revised' => 'required|numeric',
            'quantity_denied' => 'required|numeric',
            'features_quantity' => 'required|array'
        ]);
        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 400);
        }

        $dataInspection = [
            'sale_id' => $request->date_inspeccion,
            'user_created_id' => auth()->user(),
            'date_inspection' => $request->date_inspeccion,
            'type_product' => $request->type_product,
            'observations' => $request->observations,
            'user_signature_created' => $request->user_signature_created,
            'user_reviewed' => $request->user_reviewed,
            'user_signature_reviewed' => $request->user_signature_reviewed,
            'quantity_revised' => $request->quantity_revised,
            'quantity_denied' => $request->quantity_denied,
            'features_quantity' => json_encode($request->features_quantity)
        ];
        return $dataInspection;
        try {
            Inspection::create($dataInspection);
        } catch (Exception $e) {
            return response()->json(["errors" => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inspection  $inspection
     * @return \Illuminate\Http\Response
     */
    public function show(Inspection $inspection)
    {
        //
    }
}
