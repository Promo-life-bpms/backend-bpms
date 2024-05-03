<?php

namespace App\Http\Controllers;

use App\Models\CodeOrderDeliveryRoute;
use App\Models\OrderPurchase;
use App\Models\Reception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderPurchaseProduct;
use App\Models\ProductDeliveryRoute;
use App\Models\ReceptionConfirmationMaquilado;
use App\Models\Sale;
use App\Models\SaleStatusChange;
use Dflydev\DotAccessData\Data;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ReceptionController extends Controller
{
    public $CantidadRecibida = array();
    public function saveReception(Request $request, $code_order)
    {

        // Obtener la recepcion del los productos
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|bail',
            //modificacion
            'products.*.type' => 'required',
            'products.*.date_of_reception' => 'required',
            'products.*.detiny' => 'required'

        ]);


        if ($validator->fails()) {
            return response()->json(($validator->getMessageBag()));
        }
        $order = OrderPurchase::where('code_sale', $sale_id)->get();
        return $request;
        if (!$order) {
            // Retornar mensaje
            return response()->json([
                'msg' => "Orden de compra no encontrada"
            ], Response::HTTP_NOT_FOUND);
        }
        $recep = Reception::create([]);
        //return $pedidos;
        //return $productRecep->quantity_delivered;
        /*  if ($pedidos > null) {

            SaleStatusChange::create([
                'sale_id' => $sale->id,
                "status_id" => 9
            ]);
        }
 */

        return response()->json(['message' => 'Creacion de la recepcion de inventario', 'data' => $receptionDB], 200);
    }

    public function getReception($order, $reception)
    {
        $orderPurchase = OrderPurchase::where('code_order', $order)->first();

        if (!$orderPurchase) {
            return response()->json(['errors' => (['msg' => 'Ruta de entrega no encontrada.'])], 404);
        }

        $reception = $orderPurchase->receptions->where('code_reception', $reception)->first();

        if (!$reception) {
            return response()->json(['errors' => (['msg' => 'Recepcion no encontrada.'])], 404);
        }
        //Inventario contabilizado:

        $reception->productsReception;

        return response()->json(['data' => $reception], 200);
    }

    public function receptionAccept(Request $request, $code_order_route_id)
    {
        $user =  auth()->user();

        foreach ($user->whatRoles as $rol) {
            switch ($rol->name) {

                case ("compras" == $rol->name):

                    break;
                case ("administrator" == $rol->name):
                    break;
                default:
                    return response()->json(
                        [
                            'msg' => "No tienes autorizacion para subir la evidencia",
                        ],

                    );
                    break;
            }
        }
        $validation = Validator::make($request->all(), [
            'files_reception_accepted' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json(
                [
                    'msg' => "Error al validar informacion de la recepecion entregada",
                    'data' => ['errorValidacion' => $validation->getMessageBag()]
                ],
                response::HTTP_UNPROCESSABLE_ENTITY
            ); // 422
        }
        //$request->files_reception_accepted;
        $productDeliveryRoute = ProductDeliveryRoute::where('code_order_route_id', $code_order_route_id)->first();
        if ($productDeliveryRoute->files_reception_accepted == null) {
            $productDeliveryRoute->files_reception_accepted = $request->files_reception_accepted;
        }

        $productDeliveryRoute->save();
        /*   $productDeliveryRoute->files_reception_accepted = $dataFiles; */
        /*    $productDeliveryRoute->save(); */
        return response()->json(['message' => 'Se confirmo que el pedido llego a almacen', 'data' => $productDeliveryRoute], 200);
    }
    public function confirmation_manufactured_product(Request $request, $order, $odoo_product)
    {
        $orderPurchase = OrderPurchase::where('code_order', $order)->first();
        /*   foreach ($orderPurchase->products as $product) {
            $odoo_id = $product->odoo_product_id;
        } */
        $validation = Validator::make($request->all(), [
            'quantity_maquilada' => 'required',

        ]);
        if ($validation->fails()) {
            return response()->json(['msg' => "Error al crear confirmacion de producto maqulado", 'data' => ["errorValidacion" => $validation->getMessageBag()]], response::HTTP_BAD_REQUEST); //400
        }
        $dataConfirmation = [
            'code_order' => $order,
            'odoo_product_id' => $odoo_product,
            'quantity_maquilada' => $request->quantity_maquilada,
            'decrease' => $request->decrease,
            'product_clean' => $request->product_clean,
            'observations' => $request->observations
        ];

        $recepcion_Confirmation = ReceptionConfirmationMaquilado::create($dataConfirmation);

        return response()->json(['message' => 'Creacion de la confirmacion de los productos maquilados', 'data' => $recepcion_Confirmation], 200);
    }
    public function getReceptionConfirmed($order, $odoo_product)
    {
        $codeOrder = CodeOrderDeliveryRoute::where('code_order', $order)->first();

        if (!$codeOrder) {
            return response()->json(['errors' => (['msg' => 'Orden no encontrada.'])], 404);
        }
        $products =  $codeOrder->productDeliveryRoute[0]->odoo_product_id;

        $recepciones = ReceptionConfirmationMaquilado::where('odoo_product_id', $products)->get();


        if (!$recepciones) {
            return response()->json(['errors' => (['msg' => 'Recepcion no encontrada.'])], 404);
        }
        return response()->json(['Recepcion_maquilada_confirmada' => $recepciones], 200);
    }
}
