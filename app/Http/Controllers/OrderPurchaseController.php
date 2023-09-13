<?php

namespace App\Http\Controllers;

use App\Models\OrderPurchase;
use App\Models\OrderPurchaseProduct;
use App\Models\Sale;
use App\Models\Status;
use App\Models\StatusOT;
use App\Models\StatusProductsOT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Return_;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\HttpFoundation\Response;

class OrderPurchaseController extends Controller
{

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
    public function store(Request $request, $compra_id)
    {
        $validation = Validator::make($request->all(), [
            'hora' => 'required',
            'status' => 'required',
            'status_purchase_products' => 'required|array',
            'status_purchase_products.*.odoo_product_id' => 'required|exists:order_purchase_products,odoo_product_id',
            'status_purchase_products.*.cantidad_seleccionada' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'msg' => "Error al ingresar los datos",
                'data' =>
                ["errorValidacion", $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY); //422
        }
        $compra = OrderPurchase::where('code_order', $compra_id)->first();
        if (!$compra) {
            return response()->json(["errors" => "No se ha encontrado la OT"], 404);
        }
        //entrar a el array y seleccionar el status purchase product /y de ahi la cantidad
        $errors = [];
        foreach ($request->status_purchase_products as $updateCantidad) {
            $cantidadSeleccionada = $updateCantidad['cantidad_seleccionada'];
            $productSearch = OrderPurchaseProduct::where('odoo_product_id', $updateCantidad['odoo_product_id'])->first()->product;
            $quantity = OrderPurchaseProduct::where('odoo_product_id', $updateCantidad['odoo_product_id'])->first()->quantity;
            if (($cantidadSeleccionada) <= ($quantity)) {
            } else {
                array_push($errors, ["msg" => "Cantidad superada", "product" => $productSearch]);
            }
        }
        if (count($errors) > 0) {
            return response()->json($errors, 400);
        }

        //Revisamos si hay errores
        // rEGISTRO
        $newStatus = StatusOT::create([
            "hora" => $request->hora,
            "id_order_purchases" => $request->id_order_purchases,
            "status" => $request->status,
        ]);


        foreach ($request->status_purchase_products as $newProductStatus) {
            $newProductStatus = (object)$newProductStatus;
            $product = OrderPurchaseProduct::where("odoo_product_id", $newProductStatus->odoo_product_id)->first();
            $statusOT = StatusProductsOT::create([
                'id_status_o_t_s' => $newStatus->id,
                'id_order_purchase_products' => $product->id,
                'cantidad_seleccionada' => $newProductStatus->cantidad_seleccionada,
            ]);
        }
        $newStatus->StatusProductsOT;
        return response()->json(["msg" => "Orden de Compra Creada", 'data' => ["statusOT", $newStatus]], response::HTTP_CREATED); //201
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderPurchase  $orderPurchase
     * @return \Illuminate\Http\Response
     */
    public function show($pedido, $order)
    {
        $sale = Sale::where('code_sale', $pedido)->first();
        if (!$sale) {
            return response()->json(["msg" => "No se ha encontrado el pedido"], response::HTTP_NOT_FOUND);
        }
        $orderPurchase = $sale->orders()->where('code_order', $order)->first();
        if (!$orderPurchase) {
            return response()->json(
                ["msg" => "No se ha encontrado la orden de compra, o no pertenece al pedido especificado"],
                response::HTTP_NOT_FOUND
            );
        }
        $orderPurchase->products;
        $orderPurchase->receptionsWithTheirProducts;
        // Maquiladores: Mostrar unicamente sus recepciones
        // Otra area: Mostrar cantidad del maquiador en otro atributo (total_amount_tagger)
        // Otra area: Colocar el nombre del maquilador en otro atributo (tagger_name)

        $isMaquilador = auth()->user()->whatRoles()->where('id', 2)->get();

        if ($isMaquilador->isEmpty()) {
            $isMaquilador = false;
        } else {
            $isMaquilador = true;
        }
        if (!$isMaquilador) {
            // Obtener las recepciones del el que no es maquilador
            $recepcionsAllAreas = $orderPurchase->receptionsWithTheirProducts()->where("maquilador", 0)->get();
            $quantityReceived = [];
            foreach ($recepcionsAllAreas as $key => $OrderP) {
                foreach ($OrderP->productsReception as $productRec) {
                    if (array_key_exists($productRec->odoo_product_id, $quantityReceived) == null) {
                        $quantityReceived[$productRec->odoo_product_id] =  $productRec->done;
                    } else {
                        $quantityReceived[$productRec->odoo_product_id] =   $quantityReceived[$productRec->odoo_product_id] + $productRec->done;
                    }

                    $productRec->total_amount_received = $quantityReceived[$productRec->odoo_product_id];

                    $productRec->completeInformation;
                    $productRec->measurement_unit = $productRec->completeInformation->measurement_unit;
                    unset($productRec->completeInformation);
                }
            }
            $orderPurchase->receptionsWithProducts = array_reverse($recepcionsAllAreas->toArray());
            unset($orderPurchase->receptionsWithTheirProducts);
        }
        // Obtener las recepciones del maquilador
        $recepcionsTagger = $orderPurchase->receptionsWithTheirProducts()->where("maquilador", 1)->get();
        $quantityReceived = [];
        foreach ($recepcionsTagger as $key => $OrderP) {
            foreach ($OrderP->productsReception as $productRec) {
                if (array_key_exists($productRec->odoo_product_id, $quantityReceived) == null) {
                    $quantityReceived[$productRec->odoo_product_id] =  $productRec->done;
                } else {
                    $quantityReceived[$productRec->odoo_product_id] =   $quantityReceived[$productRec->odoo_product_id] + $productRec->done;
                }
                $productRec->total_amount_received = $quantityReceived[$productRec->odoo_product_id];
                $productRec->completeInformation;
                $productRec->measurement_unit = $productRec->completeInformation->measurement_unit;
                unset($productRec->completeInformation);
            }
        }
        $orderPurchase->receptionsWithProductsTagger = array_reverse($recepcionsTagger->toArray());
        unset($orderPurchase->receptionsWithTheirProducts);
        //Se crea el campo de last status con el valor de i retornando el mismo
        $orderPurchase->theirHistoryStatus;

        for ($i = 0; $i < count($orderPurchase->theirHistoryStatus); $i++) {
            if ($i > 0) {
                $orderPurchase->theirHistoryStatus[$i]->last_status = $orderPurchase->theirHistoryStatus[$i - 1]->status;
            } else {
                $orderPurchase->theirHistoryStatus[$i]->last_status = 'Pendiente';
            }
        }
        foreach ($orderPurchase->theirHistoryStatus as $statusRegistered) {
            foreach ($statusRegistered->StatusProductsOT as $productStatus) {
                $productStatus->completeInformation;
                $productStatus->product = $productStatus->completeInformation->product;
                $productStatus->quantity = $productStatus->completeInformation->quantity;
                $productStatus->measurement_unit = $productStatus->completeInformation->measurement_unit;
                unset($productStatus->completeInformation);
            }
        }

        $orderPurchase->historyStatus = array_reverse($orderPurchase->theirHistoryStatus->toArray());
        unset($orderPurchase->theirHistoryStatus);
        return response()->json(["msg" => "Orden de compra encontrada", 'data' => ["orderPurchase", $orderPurchase]], response::HTTP_OK);
    }
}
