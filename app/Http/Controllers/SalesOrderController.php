<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Http\Requests\StoreSalesOrderRequest;
use App\Http\Requests\UpdateSalesOrderRequest;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as ResponseApi;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dato = [
            "codePedido" => "PED1239",
            "cliente" => "Abarrotera BBVA",
            "direcciónFactura" => "ARYADEBA, S.A. DE C.V",
            "direcciónEntrega" => "ARYADEBA, S.A. DE C.V",
            "horarioEntrega" => "ARYADEBA, S.A. DE C.V",
            "fechaPedido" => "ARYADEBA, S.A. DE C.V",
            "fechaConfirmacion" => "ARYADEBA, S.A. DE C.V",
            "instruccionesEntrega" => "ENVIAR A VALLARTA ADVENTURES, Av. paseo de las palmas #39A NUEVO VALLARTA, BAHIA DE BANDERAS NAYARIT CP 63735 CONTACTO EVELIA",
            "lineasDelPedido" => [
                "clave" => "SCE-56",
                "nombre" => "Producto 1",
                "descripcion" => "Producto 1",
                "personalizacion" => "Producto 1",
                "costomaquila" => 54,
                "logo" => "bimbo",
                "cantidaPedida" => 324,
                "cantidadEntregada" => 45,
                "cantidadFacturada" => 435
            ]
        ];

        $data = array($dato, $dato, $dato, $dato, $dato);

        return response()->json($data, ResponseApi::HTTP_OK);
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
     * @param  \App\Http\Requests\StoreSalesOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSalesOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SalesOrder  $salesOrder
     * @return \Illuminate\Http\Response
     */
    public function show($salesOrder)
    {
        $dato = [
            "codePedido" => "PED1239",
            "cliente" => "Abarrotera BBVA",
            "direcciónFactura" => "ARYADEBA, S.A. DE C.V",
            "direcciónEntrega" => "ARYADEBA, S.A. DE C.V",
            "horarioEntrega" => "ARYADEBA, S.A. DE C.V",
            "fechaPedido" => "ARYADEBA, S.A. DE C.V",
            "fechaConfirmacion" => "ARYADEBA, S.A. DE C.V",
            "instruccionesEntrega" => "ENVIAR A VALLARTA ADVENTURES, Av. paseo de las palmas #39A NUEVO VALLARTA, BAHIA DE BANDERAS NAYARIT CP 63735 CONTACTO EVELIA",
            "lineasDelPedido" => [
                "clave" => "SCE-56",
                "nombre" => "Producto 1",
                "descripcion" => "Producto 1",
                "personalizacion" => "Producto 1",
                "costomaquila" => 54,
                "logo" => "bimbo",
                "cantidaPedida" => 324,
                "cantidadEntregada" => 45,
                "cantidadFacturada" => 435
            ]
        ];
        return response()->json($dato, ResponseApi::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SalesOrder  $salesOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(SalesOrder $salesOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSalesOrderRequest  $request
     * @param  \App\Models\SalesOrder  $salesOrder
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSalesOrderRequest $request, SalesOrder $salesOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalesOrder  $salesOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesOrder $salesOrder)
    {
        //
    }
}
