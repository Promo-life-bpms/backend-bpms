<?php

namespace App\Http\Controllers;

use App\Models\Incidence;
use App\Models\OrderPurchase;
use App\Models\OrderPurchaseProduct;
use App\Models\Remission;
use App\Models\Sale;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

class IncidenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function show($incidencia)
    {
        $incidencia = Incidence::where('internal_code_incidence', $incidencia)->first();
        if (!$incidencia) {
            return response()->json(["msg" => "No se ha encontrado la incidencia"], response::HTTP_NOT_FOUND); //404
        }
        unset($incidencia->requested_by);
        DB::statement("SET SQL_MODE=''");
        $dataIncidencia = OrderPurchase::join('order_purchase_products', 'order_purchase_products.order_purchase_id', 'order_purchases.id')
            ->join('incidence_products', 'incidence_products.order_purchase_product_id', 'order_purchase_products.id')
            ->where('incidence_products.incidence_id', $incidencia->id)
            ->select('order_purchases.*')
            ->groupBy('order_purchases.id')
            ->get();
        foreach ($dataIncidencia as $codeOrder) {
            $codeOrder->incidenceProducts = $codeOrder->products()
                ->join('incidence_products', 'incidence_products.order_purchase_product_id', 'order_purchase_products.id')
                ->where('incidence_products.incidence_id', $incidencia->id)
                ->where('order_purchase_products.order_purchase_id', $codeOrder->id)
                ->get();
            foreach ($codeOrder->incidenceProducts as $productIncidence) {
                unset(
                    $productIncidence->planned_date,
                    $productIncidence->company,
                    $productIncidence->quantity_invoiced,
                    $productIncidence->quantity_delivered,
                    $productIncidence->company,
                    $productIncidence->unit_price,
                );
            }
        }
        $incidencia->orderDetails = $dataIncidencia;
        return response()->json(["msg" => "Detalle de la incidencia", 'data' => ["incidencia" => $incidencia]], response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $sale_id)
    {

        // TODO: Calidad y ventas puede generar incidencias hasta 30 dias de entregado el producto, despues solo calidad.
        //return $sale_id;
        //validar que la informacion este correcta si no no se puede registrar
        // utilizar validator
        $incidencia = '';
        $validation = Validator::make($request->all(), [
            'area' => 'required',
            'motivo' => 'required',
            'tipo_de_producto' => 'required',
            'tipo_de_tecnica' => 'required',
            'responsable' => 'required',
            'fecha_creacion' => 'required',
            'evidencia' => 'required',
            'fecha_compromiso' => 'required',
            'solucion' => 'required',
            'id_user' => 'required',
            'elaboro' => 'required',
            'firma_elaboro' => 'required',
            'reviso' => 'required',
            'firma_reviso' => 'required',
            'comentarios_generales' => 'required',

            'incidence_products' => 'required|array',
            'incidence_products.*.odoo_product_id' => 'required|exists:order_purchase_products,odoo_product_id',
            'incidence_products.*.quantity_selected' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                "msg" => 'No se registro correctamente la informacion',
                'data' =>
                ["errorValidacion" => $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $sale = Sale::with('moreInformation')->where('code_sale', $sale_id)->first();
        //return $sale;
        if (!$sale) {
            return response()->json(["msg" => "No se ha encontrado el pedido"], response::HTTP_NOT_FOUND);
        }
        $remision = Sale::join('code_order_delivery_routes', 'code_order_delivery_routes.code_sale', 'sales.code_sale')
            ->join('delivery_routes', 'delivery_routes.id', 'code_order_delivery_routes.delivery_route_id')
            ->join('remisiones', 'remisiones.delivery_route_id', 'delivery_routes.id')
            ->where('sales.code_sale', $sale_id)
            ->get();


        if (count($remision) < 0) {
            return response()->json([
                "msg" => 'No hay remisiones'
            ]);
        }
        $rem = Sale::join('code_order_delivery_routes', 'code_order_delivery_routes.code_sale', 'sales.code_sale')
            ->join('delivery_routes', 'delivery_routes.id', 'code_order_delivery_routes.delivery_route_id')
            ->join('remisiones', 'remisiones.delivery_route_id', 'delivery_routes.id')
            ->where('sales.code_sale', $sale_id)
            ->select('remisiones.*')
            ->first();

        if (!$rem) {
            return response()->json(["msg" => "No se han encontrado remisiones del pedido"], response::HTTP_NOT_FOUND);
        }

        $diasDiferencia = $rem->created_at->diffInDays(now());

        //return $date;
        //return $date;
        $user =  auth()->user();
        $aux = false;
        // return $user;
        foreach ($user->whatRoles as $rol) {
            switch ($rol->name) {
                case "control_calidad":
                    $aux = true;
                    break;
                case "ventas":
                    // return $rol->name;
                    if ($diasDiferencia <= 30) {
                        $aux = true;
                    }
                    break;
                default:
                    return response()->json(['No tienes permiso de crear una incidencia']);
                    break;
            }
        }
        if ($aux == false) {
            return response()->json(['No tienes permiso de crear una incidencia']);
        }
        $maxINC = Incidence::max('internal_code_incidence');
        $idinc = null;
        if (!$maxINC) {
            $idinc = 1;
        } else {
            $idinc = (int) explode('-', $maxINC)[1];
            $idinc++;
        }
        $response = null;



        $incidencia = Incidence::create([
            "code_incidence" => 'No Definido',
            "code_sale" => $sale->code_sale,
            "client" => $sale->moreInformation->client_name,
            "requested_by" => '',
            "description" => $request->comentarios_generales,
            "date_request" => $request->fecha_creacion,
            "company" => $sale->moreInformation->warehouse_company,
            "odoo_status" => 'Confirmado', // TODO: Cambiarlo a odoo_status

            'internal_code_incidence' => "INCD-" . str_pad($idinc, 5, "0", STR_PAD_LEFT),
            'area' => $request->area,
            'reason' => $request->motivo,
            'product_type' => $request->tipo_de_producto,
            'type_of_technique' => $request->tipo_de_tecnica,
            'responsible' => $request->responsable,
            'creation_date' => $request->fecha_creacion,
            'bpm_status' => "Creada",
            'evidence' => $request->evidencia,
            'commitment_date' => $request->fecha_compromiso,
            'solution' => $request->solucion,
            'solution_date' => null,
            'user_id' => $request->id_user,
            'elaborated' => $request->elaboro,
            'signature_elaborated' => $request->firma_elaboro,
            'reviewed' => $request->reviso,
            'signature_reviewed' => $request->firma_reviso,
            'sale_id' => $sale->id
        ]);
        $response = null;

        $dataProducts = [];
        $orderpurchase_id = null;
        foreach ($request->incidence_products as $incidence_product) {
            $incidence_product = (object)$incidence_product;

            $productOrder = OrderPurchaseProduct::where("odoo_product_id", $incidence_product->odoo_product_id)->first();

            $orderpurchase_id = $productOrder->order_purchase_id;
            $productOdoo = [
                "pro_name" => '',
                "pro_product_id" => $productOrder->product,
                "pro_qty" => $incidence_product->quantity_selected,
                "pro_currency_id" => "MXN",
                "pro_price" => $productOrder->unit_price
            ];

            $incidencia->productsIncidence()->create([
                'order_purchase_product_id' =>  $productOrder->id,
                'quantity_selected' => $incidence_product->quantity_selected,
                'request' => '',
                'notes' => '',
                'product' => $productOrder->product,
                'cost' => $productOrder->unit_price,
            ]);
            array_push($dataProducts, $productOdoo);
        }


        $keyOdoo = '';
        $company = $sale->moreInformation->warehouse_company;

        switch ($company) {
            case 'PROMO LIFE':
                $keyOdoo = config('key_odoo.key_pl');
                break;
            case 'BH':
                $keyOdoo = config('key_odoo.key_bh');
                break;
            default:
                return response()->json(['msg' => 'No se pudo asignar el key para enviar la incidencia a Odoo correctamente'], response::HTTP_BAD_REQUEST); //400
                break;
        }
        $orderpurchase = OrderPurchase::find($orderpurchase_id);
        if (!$orderpurchase) {
            return response()->json(["msg" => "No se ha encontrado el OT/OC"], response::HTTP_NOT_FOUND);
        };
        try {
            $url = config('key_odoo.endpoint_incidence');
            $data =  [
                'incidencias' => [
                    [
                        "name" => false,
                        "sale_id" => $sale->code_sale,
                        "description" => $incidencia->description,
                        "date_incidence" => $incidencia->date_request,
                        "supplier_id" => $orderpurchase->provider_name,
                        "line_ids" => $dataProducts,
                        "po_ids" => [
                            [
                                "com_id" => $orderpurchase->code_order
                            ]
                        ],
                    ]
                ]
            ];
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-VDE-APIKEY: ' . $keyOdoo,
                'X-VDE-TYPE: Ambos',
            ]);
            $response = curl_exec($curl);
            $responseOdoo = $response;
            $errors = false;
            $message = '';
            if ($response !== false) {
                $dataResponse = json_decode($response);
                if ($dataResponse) {
                    if (isset($dataResponse->error)) {
                        $message = $dataResponse->detail;
                        $errors = true;
                    }
                    if (!$errors && $dataResponse[0]->success) {
                        if ($dataResponse[0]->success) {
                            $folio = $dataResponse[0]->Folio;
                            //Actualizar Folio de la Incidencia
                            $incidencia->code_incidence = $folio;
                            $incidencia->save();
                        } else {
                            $errors = true;
                            $message = $dataResponse[0]->message;
                        }
                    }
                } else {

                    $errors = true;
                    $message = "Error de Conexion a odoo";
                }
            } else {
                $errors = true;
                $message = "Error al enviar el lead a odoo";
            }

            if ($errors) {
                return response()->json([
                    'msg' => 'No se pudo crear la incidencia correctamente',
                    'data' =>
                    [
                        "messageOdoo" => $message,
                        "incidencia" => $incidencia
                    ]
                ], response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $errors = true;
            return response()->json(
                [
                    'msg' => 'No se pudo crear la incidencia correctamente',
                    'data' => ["message" => $message,  "incidencia" => $incidencia]
                ],
                response::HTTP_BAD_REQUEST
            );
        }



        return response()->json([
            "msg" => 'Incidencia creada exitosamente',
            'data' =>
            [

                "incidencia" => $incidencia,
                'responseOdoo' => json_decode($response),
            ]
        ], response::HTTP_CREATED);
    }

    public function update(Request $request, $incidencia)
    {
        //return $request;
        $validation = Validator::make($request->all(), [
            'status' => 'required|in:Liberada,Cancelada',
            'solution_date' => 'required_if:status,Liberada',
        ]);
        if ($validation->fails()) {
            return response()->json([
                "msg" => 'No se registro correctamente la informacion',
                'data' => ["errorValidacion" => $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $incidencia = Incidence::where('internal_code_incidence', $incidencia)->first();

        if (!$incidencia) {
            return response()->json(["msg" => "No se ha encontrado la incidencia"], response::HTTP_NOT_FOUND); //404
        }
        $incidencia->bpm_status = $request->status;
        $incidencia->solution_date = $request->solution_date;
        $incidencia->user_solution =  auth()->user()->name;
        $incidencia->save();
        return response()->json(["msg" => "Se actualizo la incidencia"], response::HTTP_ACCEPTED);
    }

    public function destroy(Request $request)
    {
        $Incidencia = Incidence::destroy($request->id);
        return response()->json([
            "msg" => "La incidencia se ha eliminado correctamente",
            "data" => [
                'incidencia' => $Incidencia,
            ],
        ], response::HTTP_OK); //201
    }
}
