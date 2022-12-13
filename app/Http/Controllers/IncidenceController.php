<?php

namespace App\Http\Controllers;

use App\Models\Incidence;
use App\Models\OrderPurchaseProduct;
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
        $incidencia = Incidence::where('internal_code_incidence', $incidencia_id)->first();
        if (!$incidencia) {
            return response()->json(["errors" => "No se ha encontrado la incidencia"], 404);
        }
        return response()->json(["msj" => $incidencia]);
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
            'incidence_products.*.id_order_purchase_products' => 'required|exists:order_purchase_products,odoo_product_id',
            'incidence_products.*.cantidad_seleccionada' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json(["errors" => $validation->getMessageBag()], 422);
        }
        $sale = Sale::with('moreInformation')->where('code_sale', $sale_id)->first();
        if (!$sale) {
            return response()->json(["errors" => "No se ha encontrado el pedido"], 404);
        }

        //Crea codigo de incidencia
        $maxINC = Incidence::max('internal_code_incidence');
        $idinc = null;
        if (!$maxINC) {
            $idinc = 1;
        } else {
            $idinc = (int) explode('-', $maxINC)[1];
            $idinc++;
        }

        $incidencia = Incidence::create([
            "code_incidence" => 'No Definido',
            "code_sale" => $sale->code_sale,
            "client" => $sale->moreInformation->client_name,
            "requested_by" => '',
            "description" => $request->comentarios_generales,
            "date_request" => $request->fecha_creacion,
            "company" => $sale->moreInformation->warehouse_company,
            "status" => '',

            'internal_code_incidence' => "INCD-" . str_pad($idinc, 5, "0", STR_PAD_LEFT),
            'area' => $request->area,
            'reason' => $request->motivo,
            'product_type' => $request->tipo_de_producto,
            'type_of_technique' => $request->tipo_de_tecnica,
            'solution_of_incidence' => $request->solucion_de_incidencia,
            'responsible' => $request->responsable,
            'creation_date' => $request->fecha_creacion,
            'internal_status' => $request->status,
            'evidence' => $request->evidencia,
            'commitment_date' => $request->fecha_compromiso,
            'solution' => $request->solucion,
            'solution_date' => $request->fecha_solucion,
            'user_id' => $request->id_user,
            'elaborated' => $request->elaboro,
            'signature_elaborated' => $request->firma_elaboro,
            'reviewed' => $request->reviso,
            'signature_reviewed' => $request->firma_reviso,
            'sale_id' => $sale->id
        ]);

        $dataProducts = [];

        foreach ($request->incidence_products as $incidence_product) {
            $incidence_product = (object)$incidence_product;
            $productOrder = OrderPurchaseProduct::where("odoo_product_id", $incidence_product->id_order_purchase_products)->first();
            $productOdoo = [
                "pro_name" => '',
                "pro_product_id" => $productOrder->product,
                "pro_qty" => $incidence_product->cantidad_seleccionada,
                "pro_currency_id" => "MXN",
                "pro_price" => $productOrder->unit_price
            ];
            $incidencia->productsIncidence()->create([
                'order_purchase_product_id' =>  $productOrder->id,
                'quantity_selected' => $incidence_product->cantidad_seleccionada,
                'request' => '',
                'notes' => '',
                'product' => $productOrder->product,
                'cost' => $productOrder->unit_price,
            ]);
            array_push($dataProducts, $productOdoo);
        }

        $keyOdoo = '';
        $company = "Promo Life";

        switch ($company) {
            case 'Promo Life':
                $keyOdoo = 'c002a44464a3cbe6bd49344fcd99d06d';
                # code...
                break;
            case 'BH Trademarket':
                $keyOdoo = 'b1bf4adf8d00ccec169d66fcce0b22ca';
                # code...
                break;

            default:
                return response()->json(['msg' => 'No se pudo asignar el key para enviar la incidencia a Odoo correctamente'], 400);
                break;
        }
        try {
            $url = 'https://api-promolife.vde-suite.com:5030/custom/Promolife/V2/incidences/create';
            $data =  [
                'incidencias' => [
                    [
                        "name" => false,
                        "sale_id" => $sale->code_sale,
                        "description" => $incidencia->description,
                        "date_incidence" => $incidencia->date_request,
                        // "supplier_id" => "ALMACENES ÁNFORA SA DE CV",
                        "line_ids" => $dataProducts,
                        "po_ids" => [],
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
                if (isset($dataResponse->error)) {
                    $message = $dataResponse->detail;
                    $errors = true;
                }

                if (!$errors && $dataResponse[0]->success) {
                    if ($dataResponse[0]->success) {
                        $folio = $dataResponse[0]->Folio;
                        //    Actualizar Folio de la Incidencia
                    } else {
                        $errors = true;
                        $message = $dataResponse[0]->message;
                    }
                }
            } else {
                $errors = true;
                $message = "Error al enviar el lead a odoo";
            }

            if ($errors) {
                return response()->json(['msg' => 'No se pudo crear la incidencia correctamente', 'error' => $message], 400);
            }
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $errors = true;
            return response()->json(['msg' => 'No se pudo crear la incidencia correctamente', 'error' => $message], 400);
        }

        return response()->json(["msg" => 'Incidencia creada exitosamente'], 201);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $Incidencia = Incidence::destroy($request->id);
        return response()->json([
            "Incidencia" => $Incidencia,
            "mensaje" => "Borrando registro",
            "display_message" => "La incidencia se ha eliminado corectamente",
            "user" => "Marlene",
        ], 201);
    }
}
