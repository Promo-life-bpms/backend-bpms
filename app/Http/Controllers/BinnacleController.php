<?php

namespace App\Http\Controllers;

use App\Models\Binnacle;
use App\Models\Sale;
use Illuminate\Http\Request;

/**
 * Clase BinnacleController
 *
 * Controlador para la gestión de la bitácora.
 */
class BinnacleController extends Controller
{
    /**
     * Guarda un registro de la bitácora con validación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $pedido
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $pedido)
    {
        $request->validate([
            'comment' => 'required',
        ]);

        // Buscar el pedido por de code sale
        $sale = Sale::where('code_sale', $pedido)->first();
        // Agregar el sale_id y el user_id al request
        $request->merge([
            'sale_id' => $sale->id,
            'user_id' => auth()->user()->id
        ]);
        $binnacle = $sale->binnacles()->create($request->all());

        return response()->json([
            'message' => 'Bitácora creada correctamente',
            'binnacle' => $binnacle
        ], 201);
    }
}
