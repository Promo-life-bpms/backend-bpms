<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\InspectionFiles;
use App\Models\Sale;
use App\Models\InspectionProduct;
use App\Models\OrderPurchaseProduct;
use App\Models\SaleStatusChange;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class InspectionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $sale_id, $product_id)
    {
        $ConfirmProducts = DB::table('confirm_product_counts')->where('id_product', $product_id)->exists();
    
        if($ConfirmProducts){
            $validation = Validator::make($request->all(), [
                'date_inspeccion' => 'required|date:Y-m-d h:i:s',
                'type_product' => 'required|in:limpio,maquilado',
                'observations' => 'required|string',
                'user_signature_created' => 'required',
                'user_reviewed' => 'required',
                'files' => 'required',
                'user_signature_reviewed' => 'required',
                'quantity_revised' => 'required|numeric',
                'quantity_denied' => 'required|numeric',
                'sample' => 'required|numeric',
                'features_quantity' => 'required|array',
                'features_quantity.wrong_pantone_color' => 'required|numeric',
                'features_quantity.damage_logo' => 'required|numeric',
                'features_quantity.incorrect_logo' => 'required|numeric',
                'features_quantity.incomplete_pieces' => 'required|numeric',
                'features_quantity.merchandise_not_cut' => 'required|numeric',
                'features_quantity.different_dimensions' => 'required|numeric',
                'features_quantity.damaged_products' => 'required|numeric',
                'features_quantity.product_does_not_perform_its_function' => 'required|numeric',
                'features_quantity.wrong_product_code' => 'required|numeric',
                'features_quantity.total' => 'required|numeric',
                'odoo_product_id' => 'required|exists:order_purchase_products,odoo_product_id',
                'code_order' => 'required|exists:order_purchases,code_order',
                'quantity_selected' => 'required'
            ]);
    
            if ($validation->fails()) {
                return response()->json(['msg' => "Error al crear la inspeccion de calidad", 'data' => ["errorValidacion" => $validation->getMessageBag()]], response::HTTP_BAD_REQUEST); //400
            } 
    
            $sale = Sale::where('code_sale', $sale_id)->first();
            if (!$sale) {
                return response()->json(["msg" => "No se ha encontrado el pedido"], response::HTTP_NOT_FOUND);
            } 
    
            $maxINSP = Inspection::max('code_inspection');
            $idInsp = null;
            if (!$maxINSP) {
                $idInsp = 1;
            } else {
                $idInsp = (int) explode('-', $maxINSP)[1];
                $idInsp++;
            }
    
            //$jsonData = $request->input('files'); // Acceder a la cadena JSON enviada en la solicitud
            $jsonData = json_encode($request->input('files'), JSON_UNESCAPED_SLASHES);
            $dataInspection = [
                'sale_id' => $sale->id,
                'code_inspection' => "INSP-" . str_pad($idInsp, 5, "0", STR_PAD_LEFT),
                'user_created_id' => auth()->user()->id,
                'date_inspection' => $request->date_inspeccion,
                'type_product' => $request->type_product,
                'observations' => $request->observations,
                'user_created' => auth()->user()->name,
                'user_signature_created' => $request->user_signature_created,
                'user_reviewed' => $request->user_reviewed,
                'user_signature_reviewed' => $request->user_signature_reviewed,
                'quantity_revised' => $request->quantity_revised,
                'quantity_denied' => $request->quantity_denied,
                'sample' => $request->sample,
                'files' => $jsonData,
            ];
    
            try {
                $inspection = Inspection::create($dataInspection);
    
                $request->features_quantity = (object) $request->features_quantity;
                $dataFeaturesQuantity = [
                    'wrong_pantone_color' =>  $request->features_quantity->wrong_pantone_color,
                    'damage_logo' => $request->features_quantity->damage_logo,
                    'incorrect_logo' => $request->features_quantity->incorrect_logo,
                    'incomplete_pieces' => $request->features_quantity->incomplete_pieces,
                    'merchandise_not_cut' => $request->features_quantity->merchandise_not_cut,
                    'different_dimensions' => $request->features_quantity->different_dimensions,
                    'damaged_products' => $request->features_quantity->damaged_products,
                    'product_does_not_perform_its_function' => $request->features_quantity->product_does_not_perform_its_function,
                    'wrong_product_code' => $request->features_quantity->wrong_product_code,
                    'total' => $request->features_quantity->total,
    
                ];
    
                $inspection->featuresQuantity()->create($dataFeaturesQuantity);
                $dataProductSelected = [
                    "odoo_product_id" => $request->odoo_product_id,
                    "code_order" => $request->code_order,
                    "quantity_selected" => $request->quantity_selected,
                    "id_order_purchase_products" => $product_id,
                ];
                $inspection->productsSelected()->create($dataProductSelected);
                /* foreach ($request->products_selected as $productSelected) {
                    $dataProductSelected = [
                        "odoo_product_id" => $productSelected['odoo_product_id'],
                        "code_order" => $productSelected['code_order'],
                        "quantity_selected" => $productSelected['quantity_selected'],
                    ];
                    $inspection->productsSelected()->create($dataProductSelected);
                } */
    
                //Inspección de calidad liberada
                $quantity_denied = $request->quantity_denied;
                $total = $request->features_quantity->total;
                if ($total < $quantity_denied) {
                    if ($sale->lastStatus) {
                        if ($sale->lastStatus->status_id < 9) {
                            SaleStatusChange::create([
                                'sale_id' => $sale->id,
                                "status_id" => 9
                            ]);
                        }
                    }
                }
                return response()->json([
                    "msg" => "Inspeccion Creada Correctamente",
                    'data' =>
                    ["inspection" => $inspection]
                ], response::HTTP_CREATED);
            } catch (Exception $e) {
                return response()->json([
                    'msg' => "Inspeccion No Creada",
                    'data' => ["error", $e->getMessage()]
                ], response::HTTP_BAD_REQUEST); //400
            }
        }else{
            return response()->json(['message' => 'Aún no se confirma el conteo de los productos.'], 409);
        }
        
    }

    public function show($inspection_id)
    {
        $inspection = Inspection::with('productsSelected')->where('code_inspection', $inspection_id)->first();
        if (!$inspection) {
            return response()->json(["msg" => "No se ha encontrado la inspeccion"], response::HTTP_NOT_FOUND); //404
        }
        $inspection = DB::table('inspections')->where('code_inspection', $inspection_id)->first();
        $filesJson = $inspection->files;
        $filesArray = json_decode($filesJson);
        
        $inspections = [
            'id' => $inspection->id,
            'code_inspection' => $inspection->code_inspection,
            'sale_id' => $inspection->sale_id,
            'user_created_id' => $inspection->user_created_id,
            'date_inspection' => $inspection->date_inspection,
            'files' => $filesArray,
            'type_product' => $inspection->type_product,
            'observations' => $inspection->observations,
            'user_created' => $inspection->user_created,
            'user_signature_created'=> $inspection->user_signature_created,
            'user_reviewed' => $inspection->user_reviewed,
            'user_signature_reviewed'=> $inspection->user_signature_reviewed,
            'quantity_revised' => $inspection->quantity_revised,
            'quantity_denied' => $inspection->quantity_denied,
            'sample' =>$inspection->sample,
            'created_at' => $inspection->created_at,
        ];

        $inspection_products = DB::table('inspection_products')->where('inspection_id', $inspection->id)->first();
        $orden= DB::table('order_purchases')->where('code_order', $inspection_products->code_order)->first();
        $productDescription = DB::table('order_purchase_products')->where('order_purchase_id', $orden->id)->first();
    
        $inspection_product = [
            'id' => $inspection_products->id,
            'inspection_id' => $inspection_products->inspection_id,
            'id_order_purchase_products' => $inspection_products->id_order_purchase_products,
            'odoo_product_id' => $inspection_products->odoo_product_id,
            'description_product' => $productDescription->description,
            'total' => $productDescription->quantity,
            'code_order' => $inspection_products->code_order,
            'quantity_selected' => $inspection_products->quantity_selected,
            'created_at' => $inspection_products->created_at,
            'updated_at' => $inspection_products->updated_at
        ];

        $features_quantity = DB::table('features_quantity')->where('inspection_id', $inspection_products->id)->first();

        return response()->json(['inspections' => $inspections, 'products_inspection' => $inspection_product, 'features_quantity' => $features_quantity]);
        /* DB::statement("SET SQL_MODE=''");
        $pedidoIns = Sale::join("additional_sale_information", "sales.id", "additional_sale_information.sale_id")
            ->join("inspections", "inspections.sale_id", "additional_sale_information.sale_id")
            ->where("code_inspection", $inspection_id)
            ->select(
                "sales.id",
                "inspections.sale_id",
                "sales.code_sale",
                "inspections.code_inspection",
                "sales.code_sale",
                "additional_sale_information.client_name",
                "additional_sale_information.warehouse_company",
                'inspections.user_created_id',
                'inspections.date_inspection',
                'inspections.type_product',
                'inspections.observations',
                'inspections.user_created',
                'inspections.user_signature_created',
                'inspections.user_reviewed',
                'inspections.user_signature_reviewed',
                'inspections.quantity_revised',
                'inspections.quantity_denied',
                'inspections.files',
                "additional_sale_information.sale_id"
            )
            ->first();

        return $pedidoIns;
        $ins = $pedidoIns->inspections()->where("code_inspection", $inspection_id)->first();

        $pedidoIns->features_quantity = $ins->featuresQuantity;

        // return $pedidoIns->details_orders;
        $inspectionsOrder = InspectionProduct::join('order_purchases', 'inspection_products.code_order', 'order_purchases.code_order')
            ->select('inspection_products.code_order')
            ->where("inspection_products.inspection_id", $inspection->id)
            ->groupBy('order_purchases.id')
            ->get();

        $ordenesnueva = [];

        foreach ($inspectionsOrder as $orden) {
            $productsSelected = [];
            $nuevo = $pedidoIns->detailsOrders->where('code_order', $orden->code_order)->first();

            foreach ($nuevo->products as $product) {
                foreach ($inspection->productsSelected as $pInspection) {
                    //  return $pInspection;
                    if ($product->odoo_product_id == $pInspection->odoo_product_id && $pInspection->code_order == $orden->code_order) {

                        $product->quantity_selected = $pInspection->quantity_selected;

                        array_push($productsSelected, $product);
                    }
                }
                //return $inspection->productsSelected;
            };

            $nuevo->productsInspection = $productsSelected;


            unset($nuevo->products);

            array_push($ordenesnueva, $nuevo);
        }

        unset($pedidoIns->detailsOrders);
        $pedidoIns->detailsOrders = $ordenesnueva;
 */
        return response()->json([
            'msg' => "Inspeccion de calidad solicitada correctamente",
            'data' =>
            ["inspection" => $pedidoIns]
        ], response::HTTP_OK); //200

        // Detalle de la inspeccion
    }

    public function files(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'files' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                "msg" => 'No se registro correctamente la informacion',
                "errorValidacion" => $validation->getMessageBag()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $imagenes = $request->file('files');
        $namesImagenes = [];
        foreach ($imagenes as $imagen) {
            $n =  $imagen->getClientOriginalName();
            $nombreImagen = time() . ' ' . Str::slug($n) . '.' . $imagen->getClientOriginalExtension();
            $imagen->move(public_path('storage/images/'), $nombreImagen);
            array_push($namesImagenes, 'storage/images/' . $nombreImagen);
        }

        return response()->json(['images' => $namesImagenes]);
    
    }
}
