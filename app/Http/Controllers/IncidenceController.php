<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Incidence;
use App\Models\IncidenceProduct;
use App\Models\OrderPurchase;
use App\Models\OrderPurchaseProduct;
use App\Models\Sale;
use App\Models\UserDetails;
use Database\Seeders\DepartmentSmallBox;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncidenceController extends Controller
{

    public function show($incidencia)
    {

        $incidencia = Incidence::where('code_incidence', $incidencia)->first();
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
                    $productIncidence->unit_price
                );
            }
        }
        $incidencia->orderDetails = $dataIncidencia;
        return response()->json(["msg" => "Detalle de la incidencia", 'data' => ["incidencia" => $incidencia]], response::HTTP_OK);
    }

    public function store(Request $request, $sale_id)
    {

        $incidencia = '';
        $data = $request->all();
        $dataValidation = [
            'area' => 'required',
            'motivo_de_incidencia' => 'required',
            'tipo_de_producto' => 'required',
            'evidencia' => 'required',
            'fecha_creacion' => 'required|date',
            'solution' => 'required',
            /*          'solucion_de_incidencia' => 'required', */
            'fecha_solucion' => 'nullable|date',
            'fecha_compromiso' => 'required|date',
            'responsable' => 'required',
            'firma_elaboro' => 'required',
            'firma_de_revision' => 'required',
            'comments' => 'required',
            'incidence_products' => 'required|array',
            'incidence_products.*.odoo_product_id' => 'required|exists:order_purchase_products,odoo_product_id',
            'incidence_products.*.order_purchase_product_id' => 'required|exists:order_purchase_products,id',
            'incidence_products.*.quantity_selected' => 'required|integer|min:1'
        ];

        $validator = Validator::make($data, $dataValidation);

        if ($validator->fails()) {
            return response()->json([
                "msg" => 'No se registró correctamente la información',
                'data' => [
                    "errorValidacion" => $validator->errors()
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sale = Sale::where('code_sale', $sale_id)->first();

        if (!$sale) {
            return response()->json(["msg" => "No se ha encontrado el pedido"], response::HTTP_NOT_FOUND);
        }
        $user = auth()->user()->id;
        $user_details = UserDetails::where('id_user', $user)->first();
        $user_department = Department::where('id', $user_details->id_department)->first();
        $maxINC = Incidence::max('code_incidence');
        $user_name = auth()->user()->name;
        $idinc = null;
        if (!$maxINC) {
            $idinc = 1;
        } else {
            $idinc = (int) explode('-', $maxINC)[1];
            $idinc++;
        }
        $incidencia = Incidence::create([
            "code_incidence" => "INC-" . str_pad($idinc, 5, "0", STR_PAD_LEFT),
            "code_sale" => $sale->code_sale,
            "comments" => $request->comments,
            "creation_date" => $request->fecha_creacion,
            'area' => $request->area,
            'reason' => $request->motivo_de_incidencia,
            'product_type' => $request->tipo_de_producto,
            'type_of_technique' => $request->tipo_de_tecnica ?? null,
            'responsible' => $request->responsable ?? null,
            'creation_date' => $request->fecha_creacion,
            'evidence' => $request->evidencia,
            'solution' => $request->solution ?? null,
            'solution_date' => null,
            'elaborated' => $user_name, ///////meter quein elabroo
            'signature_elaborated' => $request->firma_elaboro,
            'reviewed' => $request->reviso ?? null,
            'signature_reviewed' => $request->firma_de_revision ?? null,
            'status' => 'Creada',
            'commitment_date' => $request->fecha_compromiso,
            "user_department" => $user_department->name_department,
            'sale_id' => $sale->id
        ]);
        $response = null;

        $dataProducts = [];
        $orderpurchase_id = null;
        foreach ($request->incidence_products as $incidence_product) {


            $incidence_product = (object)$incidence_product;
            $productOrder = OrderPurchaseProduct::where("id", $incidence_product->order_purchase_product_id)->where('odoo_product_id', $incidence_product->odoo_product_id)->first();
            if (!$productOrder) {
                return response()->json(["msg" => "No se ha encontrado el OT/OC"], response::HTTP_NOT_FOUND);
            };
            $orderpurchase_id = $productOrder->order_purchase_id;
            $productOdoo = [
                "pro_name" => $productOrder->product,
                "pro_product_id" => (int) $productOrder->odoo_product_id,
                "pro_qty" => (int) $incidence_product->quantity_selected,
                "pro_currency_id" => "MXN",
                "pro_price" => floatval($productOrder->unit_price)
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
        $productOrder = OrderPurchaseProduct::where("id", $incidence_product->order_purchase_product_id)->first();
        $orderpurchase_id = $productOrder->order_purchase_id;
        $orderpurchase = OrderPurchase::find($orderpurchase_id);
        if (!$productOrder) {
            return response()->json(["msg" => "No se ha encontrado el OT/OC"], response::HTTP_NOT_FOUND);
        };
        return response()->json([
            "msg" => 'Incidencia creada exitosamente',
            'data' =>
            [
                "incidencia" => $incidencia,
                "incidencia_products" => $incidence_product
            ]
        ], response::HTTP_CREATED);
    }

    public function updateSolution(Request $request, $incidencia)
    {
        $validation = Validator::make($request->all(), [
            'solution_date' => 'required',
        ]);
        if ($validation->fails()) {
            return response()->json([
                "msg" => 'No se registro correctamente la informacion',
                'data' => ["errorValidacion" => $validation->getMessageBag()]
            ], response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $incidencia = Incidence::where('code_incidence', $incidencia)->first();

        if (!$incidencia) {
            return response()->json(["msg" => "No se ha encontrado la incidencia"], response::HTTP_NOT_FOUND); //404
        }


        $incidencia->solution_date = $request->solution_date;
        $incidencia->status = "Liberda";
        $incidencia->save();
        /*  foreach ($request->incidence_products as $incidence_product) {
            $incidence_product = (object)$incidence_product;
            // Revisar si exite el atributo id en el objeto incidence_producto
            if (isset($incidence_product->incidence_product_id)) {
                $incidenceProduct = IncidenceProduct::find($incidence_product->incidence_product_id);
                $incidenceProduct->quantity_selected = $incidence_product->quantity_selected ?? $incidenceProduct->quantity_selected;
                $incidenceProduct->save();
            }
        } */

        return response()->json([
            "msg" => "Se actualizo la fecha de solucion correctamente",
            "incidencia" =>
            $incidencia
        ], response::HTTP_ACCEPTED);
    }

    public function updateIncidenceComplete(Request $request, $incidencia)
    {
        $incidence = Incidence::where("code_incidence", $incidencia)->first();
        if (!$incidence) {
            return response()->json(["msg" => "No se ha encontrado la incidencia"], response::HTTP_NOT_FOUND); //404
        }



        DB::beginTransaction();
        try {
            // Manejo de la evidencia nueva
            $newEvidence = $request->input('evidencia', null); // Aquí cambiamos 'evidence' por 'evidencia'

            // Actualiza los campos necesarios de la incidencia
            $incidence->update([
                "code_incidence" => $incidence->code_incidence,
                "code_sale" => $incidence->code_sale,
                "description" => null,
                'area' => $incidence->area,
                'reason' => $incidence->reason,
                'product_type' => $incidence->product_type,
                'type_of_technique' => $incidence->type_of_technique,
                'responsible' => $incidence->responsible,
                'creation_date' => $incidence->creation_date,
                'evidence' => $newEvidence,
                'commitment_date' => $incidence->commitment_date,
                'solution' => $incidence->solution,
                'solution_date' => $incidence->solution_date,
                'elaborated' => $incidence->elaborated,
                'signature_elaborated' => $incidence->signature_elaborated,
                'reviewed' => $incidence->reviewed,
                'signature_reviewed' => $incidence->signature_reviewed,
                'sale_id' => $incidence->sale_id
            ]);

            // Encuentra el producto de incidencia relacionado
            $incidenceProduct = IncidenceProduct::where('incidence_id', $incidence->id)->first();

            if ($incidenceProduct) {
                // Actualiza la cantidad seleccionada del producto de incidencia
                if ($request->has('quantity_selected')) {
                    $incidenceProduct->quantity_selected = $request->quantity_selected;
                    $incidenceProduct->save();
                }
            }

            // Confirma la transacción
            DB::commit();

            return response()->json([
                "msg" => 'Incidencia editada exitosamente',
                'data' => [
                    "incidencia" => $incidence,
                    "incidencia_products" => $incidenceProduct
                ]
            ], response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Revierte la transacción en caso de error
            DB::rollBack();
            return response()->json(["msg" => "Error al actualizar la incidencia: " . $e->getMessage()], response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
