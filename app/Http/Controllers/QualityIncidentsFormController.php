<?php

namespace App\Http\Controllers;

use App\Models\IncidentClosureForm;
use App\Models\QualityIncidentsForm;
use App\Models\SolutionOfTheIncidentForm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class QualityIncidentsFormController extends Controller
{
    public function FirstPartOfTheIncidentForm(Request $request, $code_sale)
    {
        $request->validate([
            'incidence_folio' => 'required',
            'days_in_warehouse' => 'required',
            'id_sale_product' => 'required',
            'sale_product_quantity' => 'required',
            'logo' => 'required',
            'id_order_products' => 'required',
            'order_product_quantity' => 'required',
            'correct_parts' => 'required',
            'defective_parts' => 'required',
            'defect_percentage' => 'required',
            'responsible' => 'required',
            'general_cause' => 'required',
        ]);
    
        $date = Carbon::now()->format('Y-m-d H:i:s');
        QualityIncidentsForm::create([
            'code_sale' => $code_sale,
            'incidence_folio' => $request->incidence_folio,
            'days_in_warehouse' => $request->days_in_warehouse,
            'incident_date' => $date,
            'id_sale_product' => $request->id_sale_product,
            'sale_product_quantity' => $request->sale_product_quantity,
            'logo' => $request->logo,
            'id_order_products' => $request->id_order_products,
            'order_product_quantity' => $request->order_product_quantity,
            'maquilador' => $request->maquilador ?? null,
            'distributor' => $request->distributor ?? null,
            'correct_parts' => $request->correct_parts,
            'defective_parts' => $request->defective_parts,
            'defect_percentage' => $request->defect_percentage,
            'responsible' => $request->responsible,
            'general_cause' => $request->general_cause,
            'return_description' => $request->return_description,
        ]);
        return response()->json(['message' => 'Se creo correctamente la incidencia.'], 200);
    }  

    public function SecondPartOfTheIncidentForm(Request $request, $code_sale)
    {
        $request->validate([
            'id_quality_incidents' => 'required'
        ]);

        $verificar = DB::table('quality_incidents_forms')->where('id', $request->id_quality_incidents)
                                                    ->where('code_sale', $code_sale)->exists();
        if(!$verificar){
            return response()->json(['message' => 'No existe la primer parte del formulario de la incidencia.'], 409);
        }

        SolutionOfTheIncidentForm::create([
            'proposed_solution' => $request->proposed_solution,
            'monitoring_manager' => $request->monitoring_manager,
            'replacement_out_of_time' => $request->replacement_out_of_time,
            'incident_delivery_date' => $request->incident_delivery_date,
            'days_of_incident_processing' => $request->days_of_incident_processing,
            'odc_mat_clean' => $request->odc_mat_clean,
            'cu_prod_clean' => $request->cu_prod_clean,
            'final_cost_of_clean_material' => $request->final_cost_of_clean_material,
            'odc_impression' => $request->odc_impression,
            'printing_cost_per_piece' => $request->printing_cost_per_piece,
            'cu_prod_impression' => $request->cu_prod_impression,
            'total_cost' => $request->total_cost,
            'id_quality_incidents' => $request->id_quality_incidents
        ]);
        
        return response()->json(['message' => 'Se guardo exitosamente la segunda fase del formulario.'], 200);
    }

    public function ThirdPartOfTheIncidentForm(Request $request, $code_sale)
    {
        $request->validate([
            'id_solution_incident' => 'required',
        ]);

        $verificar = DB::table('solution_of_the_incident_forms')->where('id', $request->id_solution_incident)->exists();

        if(!$verificar)
        {
            return response()->json(['message' => 'Aún no se crea la segunda parte del formulario'], 409);
        }

        IncidentClosureForm::create([
            'status' => $request->status,
            'application' => $request->application,
            'note_of_application' => $request->note_of_application,
            'responsible_for_final_monitoring' => $request->responsible_for_final_monitoring,
            'final_status' => $request->final_status,
            'final_closing_date' => $request->final_closing_date,
            'credit_note' => $request->credit_note,
            'days_of_incident_process' => $request->days_of_incident_process,
            'id_solution_incident' => $request->id_solution_incident,
        ]);

        return response()->json(['message' => 'Se guardo exitosamente la finalización de la incidencia.'], 200);
    }

    public function IncidentForm($idform)
    {
        $info = DB::table('quality_incidents_forms')->where('id', $idform)->first();
        if($info == null){
            $informacion = ['Aún no se crea esta incidencia.'];
        }else{
            $id_s_p = $info->id_sale_product;
            $DescriptionProductSale = DB::table('sales_products')->where('id', $id_s_p)->value('description');
            $id_o_p = $info->id_order_products;
            $DescriptionOrderProducts = DB::table('order_purchase_products')->where('id', $id_o_p)->value('description');
            $informacion = [
                'id_first_part' => $info->id,
                'code_sale' => $info->code_sale,
                'incidence_folio' => $info->incidence_folio,
                'days_in_warehouse' => $info->days_in_warehouse,
                'id_sale_product' => $id_s_p,
                'description_sale_product' => $DescriptionProductSale,
                'sale_product_quantity' => $info->sale_product_quantity,
                'logo' => $info->logo,
                'id_order_products' => $info->id_order_products,
                'description_order_products' =>$DescriptionOrderProducts,
                'order_product_quantity' => $info->order_product_quantity,
                'correct_parts' => $info->correct_parts,
                'defective_parts' => $info->defective_parts,
                'defect_percentage' => $info->defect_percentage,
                'responsible' => $info->responsible,
                'general_cause' => $info->general_cause,
                'return_description' => $info->return_description,
            ];
        }

        if ($info !== null) {
            $id = $info->id;
            $SecondInfo = DB::table('solution_of_the_incident_forms')->where('id_quality_incidents', $id)->first();
            //dd($SecondInfo);
            if($SecondInfo){
                $SecondInc = [
                    'id' => $SecondInfo->id,
                    'proposed_solution' => $SecondInfo->proposed_solution,
                    'monitoring_manager' => $SecondInfo->monitoring_manager,
                    'replacement_out_of_time' => $SecondInfo->replacement_out_of_time,
                    'incident_delivery_date' => $SecondInfo->incident_delivery_date,
                    'days_of_incident_processing' => $SecondInfo->days_of_incident_processing,
                    'odc_mat_clean' => $SecondInfo->odc_mat_clean,
                    'cu_prod_clean' => $SecondInfo->cu_prod_clean,
                    'final_cost_of_clean_material' => $SecondInfo->final_cost_of_clean_material,
                    'odc_impression' => $SecondInfo->odc_impression,
                    'printing_cost_per_piece' => $SecondInfo->printing_cost_per_piece,
                    'cu_prod_impression' => $SecondInfo->cu_prod_impression,
                    'total_cost' => $SecondInfo->total_cost,
                    'id_quality_incidents' => $SecondInfo->id_quality_incidents
                ];

                $Second = DB::table('incident_closure_forms')->where('id_solution_incident', $SecondInfo->id)->first();
                if($Second !== null){
                    $thirdinfo =[
                        'id' => $Second->id,
                        'status' => $Second->status,
                        'application' => $Second->application,
                        'note_of_application' => $Second->note_of_application,
                        'responsible_for_final_monitoring' => $Second->responsible_for_final_monitoring,
                        'final_status' => $Second->final_status,
                        'final_closing_date' => $Second->final_closing_date,
                        'credit_note' => $Second->credit_note,
                        'days_of_incident_process' => $Second->days_of_incident_process,
                        'id_solution_incident' => $Second->id_solution_incident,
                    ];
                }else{
                    $thirdinfo =['Aún no se crea la tercera parte de la incidencia.'];
                }
            }else{
                $SecondInc = ['Aún no se crea la segunda parte de la incidencia.'];
                $thirdinfo =['Aún no se crea la tercera parte de la incidencia.'];
            }   
        } else {
            $SecondInc = ['Aún no se crea la segunda parte de la incidencia.'];
            $thirdinfo =['Aún no se crea la tercera parte de la incidencia.'];
        }
        return response()->json(['first_part' => $informacion, 'second_part' => $SecondInc, 'third_part' =>$thirdinfo]);
    }

    public function UpdateFormInc(Request $request, $idform)
    {
        $FirstPart = DB::table('quality_incidents_forms')->where('id', $idform)->first();
        /////////////DEBE EXISTIR LA PARTE UNO PARA PODER EDITAR////////////////
        if($FirstPart !== null){
            $idQuality = $FirstPart->id;    
            DB::table('quality_incidents_forms')->where('id', $idQuality)->update([
                'code_sale' => $request->has('code_sale') && !empty($request->code_sale) ? $request->code_sale : $FirstPart->code_sale,
                'incidence_folio' => $request->has('incidence_folio') && !empty($request->incidence_folio) ? $request->incidence_folio : $FirstPart->incidence_folio,
                'days_in_warehouse' => $request->has('days_in_warehouse') && !empty($request->days_in_warehouse) ? $request->days_in_warehouse : $FirstPart->days_in_warehouse,
                'incident_date' => $request->has('incident_date') && !empty($request->incident_date) ? $request->incident_date : $FirstPart->incident_date,
                'id_sale_product' => $request->has('id_sale_product') && !empty($request->id_sale_product) ? $request->id_sale_product : $FirstPart->id_sale_product,
                'sale_product_quantity' => $request->has('sale_product_quantity') && !empty($request->sale_product_quantity) ? $request->sale_product_quantity : $FirstPart->sale_product_quantity,
                'logo' => $request->has('logo') && !empty($request->logo) ? $request->logo : $FirstPart->logo,
                'id_order_products' => $request->has('id_order_products') && !empty($request->id_order_products) ? $request->id_order_products : $FirstPart->id_order_products,
                'order_product_quantity' => $request->has('order_product_quantity') && !empty($request->order_product_quantity) ? $request->order_product_quantity : $FirstPart->order_product_quantity,
                'maquilador' => $request->has('maquilador') && !empty($request->maquilador) ? $request->maquilador : $FirstPart->maquilador,
                'distributor' => $request->has('distributor') && !empty($request->distributor) ? $request->distributor : $FirstPart->distributor,
                'correct_parts' => $request->has('correct_parts') && !empty($request->correct_parts) ? $request->correct_parts : $FirstPart->correct_parts,
                'defective_parts' => $request->has('defective_parts') && !empty($request->defective_parts) ? $request->defective_parts : $FirstPart->defective_parts,
                'defect_percentage' => $request->has('defect_percentage') && !empty($request->defect_percentage) ? $request->defect_percentage : $FirstPart->defect_percentage,
                'responsible' => $request->has('responsible') && !empty($request->responsible) ? $request->responsible : $FirstPart->responsible,
                'general_cause' => $request->has('general_cause') && !empty($request->general_cause) ? $request->general_cause : $FirstPart->general_cause,
                'return_description' => $request->has('return_description') && !empty($request->return_description) ? $request->return_description : $FirstPart->return_description
            ]);

            ///////////VERIFICAMOS QUE EXISTA LA PARTE DOS//////////////
            $Second = DB::table('solution_of_the_incident_forms')->where('id_quality_incidents', $idQuality)->first();
            if($Second !== null){
                DB::table('solution_of_the_incident_forms')->where('id_quality_incidents', $Second->id)->update([
                    'proposed_solution' => $request->has('proposed_solution') && !empty($request->proposed_solution) ? $request->proposed_solution : $Second->proposed_solution,
                    'monitoring_manager' => $request->has('monitoring_manager') && !empty($request->monitoring_manager) ? $request->monitoring_manager : $Second->monitoring_manager,
                    'replacement_out_of_time' => $request->has('replacement_out_of_time') && !empty($request->replacement_out_of_time) ? $request->replacement_out_of_time : $Second->replacement_out_of_time,
                    'incident_delivery_date' => $request->has('incident_delivery_date') && !empty($request->incident_delivery_date) ? $request->incident_delivery_date : $Second->incident_delivery_date,
                    'days_of_incident_processing' => $request->has('days_of_incident_processing') && !empty($request->days_of_incident_processing) ? $request->days_of_incident_processing : $Second->days_of_incident_processing,
                    'odc_mat_clean' => $request->has('odc_mat_clean') && !empty($request->odc_mat_clean) ? $request->odc_mat_clean : $Second->odc_mat_clean,
                    'cu_prod_clean' => $request->has('cu_prod_clean') && !empty($request->cu_prod_clean) ? $request->cu_prod_clean : $Second->cu_prod_clean,
                    'final_cost_of_clean_material' => $request->has('final_cost_of_clean_material') && !empty($request->final_cost_of_clean_material) ? $request->final_cost_of_clean_material : $Second->final_cost_of_clean_material,
                    'odc_impression' => $request->has('odc_impression') && !empty($request->odc_impression) ? $request->odc_impression : $Second->odc_impression,
                    'printing_cost_per_piece' => $request->has('printing_cost_per_piece') && !empty($request->printing_cost_per_piece) ? $request->printing_cost_per_piece : $Second->printing_cost_per_piece,
                    'cu_prod_impression' => $request->has('cu_prod_impression') && !empty($request->cu_prod_impression) ? $request->cu_prod_impression : $Second->cu_prod_impression,
                    'total_cost' => $request->has('total_cost') && !empty($request->total_cost) ? $request->total_cost : $Second->total_cost,
                ]);
                $PartsOfTheFormAreMissing = 'Se actualizó correctamente la parte dos del formulario.';

                /////////Si existe el formulario tres lo actualiza/////////////
                $thirdpart = DB::table('incident_closure_forms')->where('id_solution_incident', $Second->id)->first();
                if($thirdpart !== null){
                    $three= DB::table('incident_closure_forms')->where('id_solution_incident', $Second->id)->first();
                    DB::table('incident_closure_forms')->where('id_solution_incident', $three->id)->update([
                        'status' => $request->has('status') && !empty($request->status) ? $request->status : $three->status,
                        'application' => $request->has('application') && !empty($request->application) ? $request->application : $three->application,
                        'note_of_application' => $request->has('note_of_application') && !empty($request->note_of_application) ? $request->note_of_application : $three->note_of_application,
                        'responsible_for_final_monitoring' => $request->has('responsible_for_final_monitoring') && !empty($request->responsible_for_final_monitoring) ? $request->responsible_for_final_monitoring : $three->responsible_for_final_monitoring,
                        'final_status' => $request->has('final_status') && !empty($request->final_status) ? $request->final_status : $three->final_status,
                        'final_closing_date' => $request->has('final_closing_date') && !empty($request->final_closing_date) ? $request->final_closing_date : $three->final_closing_date,
                        'credit_note' => $request->has('credit_note') && !empty($request->credit_note) ? $request->credit_note : $three->credit_note,
                        'days_of_incident_process' => $request->has('days_of_incident_process') && !empty($request->days_of_incident_process) ? $request->days_of_incident_process : $three->days_of_incident_process,
                    ]);
                    $PartThreeOfTheFormIsMissing = 'Se actualizó correctamente la parte tres del forulario.';
                }else{
                    IncidentClosureForm::create([
                        'status' => $request->status,
                        'application' => $request->application,
                        'note_of_application' => $request->note_of_application,
                        'responsible_for_final_monitoring' => $request->responsible_for_final_monitoring,
                        'final_status' => $request->final_status,
                        'final_closing_date' => $request->final_closing_date,
                        'credit_note' => $request->credit_note,
                        'days_of_incident_process' => $request->days_of_incident_process,
                        'id_solution_incident' => $Second->id,
                    ]);
                    $PartThreeOfTheFormIsMissing = 'Se creo la parte tres del formulario';
                }
            ////EN CASO DE NO EXISTIR LA PARTE DOS LA CREAMOS Y DENTRO DE ESTA IGUAL LA TRES////
            }else{
                $partSecond = SolutionOfTheIncidentForm::create([
                    'proposed_solution' => $request->proposed_solution,
                    'monitoring_manager' => $request->monitoring_manager,
                    'replacement_out_of_time' => $request->replacement_out_of_time,
                    'incident_delivery_date' => $request->incident_delivery_date,
                    'days_of_incident_processing' => $request->days_of_incident_processing,
                    'odc_mat_clean' => $request->odc_mat_clean,
                    'cu_prod_clean' => $request->cu_prod_clean,
                    'final_cost_of_clean_material' => $request->final_cost_of_clean_material,
                    'odc_impression' => $request->odc_impression,
                    'printing_cost_per_piece' => $request->printing_cost_per_piece,
                    'cu_prod_impression' => $request->cu_prod_impression,
                    'total_cost' => $request->total_cost,
                    'id_quality_incidents' => $idQuality
                ]);
                $PartsOfTheFormAreMissing = 'Se creo la parte dos del formulario';

                $idSolution = $partSecond->id;
                $threee = DB::table('incident_closure_forms')->where('id_solution_incident', $idSolution)->exists();
                if(!$threee){
                    IncidentClosureForm::create([
                        'status' => $request->status,
                        'application' => $request->application,
                        'note_of_application' => $request->note_of_application,
                        'responsible_for_final_monitoring' => $request->responsible_for_final_monitoring,
                        'final_status' => $request->final_status,
                        'final_closing_date' => $request->final_closing_date,
                        'credit_note' => $request->credit_note,
                        'days_of_incident_process' => $request->days_of_incident_process,
                        'id_solution_incident' => $idSolution,
                    ]);
                    $PartThreeOfTheFormIsMissing = 'Se creo la parte tres del formulario';
                }               
            }
            return response()->json(['message' => 'Se modificó la parte uno del formulario.', 'message1' =>$PartsOfTheFormAreMissing, 'message2' => $PartThreeOfTheFormIsMissing], 200); 
        }else{
            return response()->json(['message' => 'Verifica que ya exista la incidencia.'], 409);
        }
    }
}

