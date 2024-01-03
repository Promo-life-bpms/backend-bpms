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
     * Almacena un nuevo estado de orden de compra en la base de datos.
     *
     * @param Request $request La solicitud HTTP recibida.
     * @param int $compra_id El ID de la compra asociada.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request, $compra_id)
    {
        // Validar los datos de la solicitud
        $validation = Validator::make($request->all(), [
            'hora' => 'required',
            'status' => 'required',
            'status_purchase_products' => 'required|array',
            'status_purchase_products.*.odoo_product_id' => 'required|exists:order_purchase_products,odoo_product_id',
            'status_purchase_products.*.cantidad_seleccionada' => 'required'
        ]);

        if ($validation->fails()) {
            // Si la validación falla, devolver un error de entidad no procesable
            return response()->json([
                'msg' => "Error al ingresar los datos",
                'data' => ["errorValidacion", $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY); //422
        }

        // Buscar la compra asociada
        $compra = OrderPurchase::where('code_order', $compra_id)->first();
        if (!$compra) {
            // Si no se encuentra la compra, devolver un error 404
            return response()->json(["errors" => "No se ha encontrado la OT"], 404);
        }

        $errors = [];
        foreach ($request->status_purchase_products as $updateCantidad) {
            // Obtener la cantidad seleccionada y buscar el producto asociado
            $cantidadSeleccionada = $updateCantidad['cantidad_seleccionada'];
            $productSearch = OrderPurchaseProduct::where('odoo_product_id', $updateCantidad['odoo_product_id'])->first()->product;
            $quantity = OrderPurchaseProduct::where('odoo_product_id', $updateCantidad['odoo_product_id'])->first()->quantity;
            if (($cantidadSeleccionada) <= ($quantity)) {
                // Si la cantidad seleccionada es menor o igual a la cantidad disponible, continuar
            } else {
                // Si la cantidad seleccionada es mayor a la cantidad disponible, agregar un error
                array_push($errors, ["msg" => "Cantidad superada", "product" => $productSearch]);
            }
        }
        if (count($errors) > 0) {
            // Si hay errores, devolver un error 400 con los errores encontrados
            return response()->json($errors, 400);
        }

        // Crear un nuevo estado de la orden de trabajo
        $newStatus = StatusOT::create([
            "hora" => $request->hora,
            "id_order_purchases" => $request->id_order_purchases,
            "status" => $request->status,
        ]);

        // Crear los nuevos estados de los productos de la orden de compra
        foreach ($request->status_purchase_products as $newProductStatus) {
            $newProductStatus = (object)$newProductStatus;
            $product = OrderPurchaseProduct::where("odoo_product_id", $newProductStatus->odoo_product_id)->first();
            $statusOT = StatusProductsOT::create([
                'id_status_o_t_s' => $newStatus->id,
                'id_order_purchase_products' => $product->id,
                'cantidad_seleccionada' => $newProductStatus->cantidad_seleccionada,
            ]);
        }

        // Obtener los productos de la orden de trabajo
        $newStatus->StatusProductsOT;

        // Devolver una respuesta JSON con el resultado de la operación
        return response()->json(["msg" => "Orden de Compra Creada", 'data' => ["statusOT", $newStatus]], response::HTTP_CREATED); //201
    }

    /**
     * Muestra una orden de compra específica.
     *
     * @param int $pedido El código del pedido.
     * @param int $order El código de la orden de compra.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($pedido, $order)
    {
        // Obtiene la venta correspondiente al código de venta proporcionado
        $sale = Sale::where('code_sale', $pedido)->first();
        if (!$sale) {
            return response()->json(["msg" => "No se ha encontrado el pedido"], response::HTTP_NOT_FOUND);
        }

        // Obtiene la orden de compra correspondiente al código de orden proporcionado
        $orderPurchase = $sale->orders()->where('code_order', $order)->first();
        if (!$orderPurchase) {
            return response()->json(
                ["msg" => "No se ha encontrado la orden de compra, o no pertenece al pedido especificado"],
                response::HTTP_NOT_FOUND
            );
        }

        // Obtiene los productos de la orden de compra
        $orderPurchase->products;
        $orderPurchase->receptionsWithTheirProducts;
        // Maquiladores: Mostrar unicamente sus recepciones
        // Otra area: Mostrar cantidad del maquiador en otro atributo (total_amount_tagger)
        // Otra area: Colocar el nombre del maquilador en otro atributo (tagger_name)

        // Verifica si el usuario autenticado es un maquilador
        $isMaquilador = auth()->user()->whatRoles()->where('id', 2)->get();
        if ($isMaquilador->isEmpty()) {
            $isMaquilador = false;
        } else {
            $isMaquilador = true;
        }

        if (!$isMaquilador) {
            // Obtener las recepciones del que no es maquilador
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

        // Crea el campo "last_status" en el historial de estados y asigna el valor correspondiente
        $orderPurchase->theirHistoryStatus;
        for ($i = 0; $i < count($orderPurchase->theirHistoryStatus); $i++) {
            if ($i > 0) {
                $orderPurchase->theirHistoryStatus[$i]->last_status = $orderPurchase->theirHistoryStatus[$i - 1]->status;
            } else {
                $orderPurchase->theirHistoryStatus[$i]->last_status = 'Pendiente';
            }
        }

        // Asigna información completa a los productos en el historial de estados
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
