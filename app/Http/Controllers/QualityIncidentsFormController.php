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
            'return_description' => 'required',
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
        return response()->json(['message' => 'prueba pasada']);
    }  

    public function SecondPartOfTheIncidentForm(Request $request, $code_sale)
    {
        $request->validate([
            'proposed_solution' => 'required',
            'monitoring_manager' => 'required',
            'replacement_out_of_time' => 'required',
            'incident_delivery_date' => 'required',
            'days_of_incident_processing' => 'required',
            'odc_mat_clean' => 'required',
            'cu_prod_clean' => 'required',
            'final_cost_of_clean_material' => 'required',
            'odc_impression' => 'required',
            'printing_cost_per_piece' => 'required',
            'cu_prod_impression' => 'required',
            'total_cost' => 'required',
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
            'status' => 'required',
            'application' => 'required',
            'note_of_application' => 'required',
            'responsible_for_final_monitoring' => 'required',
            'final_status' => 'required',
            'final_closing_date' => 'required',
            'credit_note' => 'required',
            'days_of_incident_process' => 'required',
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
        //dd($info);


        $informacion = [
            'code_sale' => $info->code_sale,
            'incidence_folio' => $info->incidence_folio,
            'days_in_warehouse' => $info->days_in_warehouse,
            'id_sale_product' => $info->id_sale_product,
            'sale_product_quantity' => $info->sale_product_quantity,
            'logo' => $info->logo,
            'id_order_products' => $info->id_order_products,
            'order_product_quantity' => $info->order_product_quantity,
            'correct_parts' => $info->correct_parts,
            'defective_parts' => $info->defective_parts,
            'defect_percentage' => $info->defect_percentage,
            'responsible' => $info->responsible,
            'general_cause' => $info->general_cause,
            'return_description' => $info->return_description,
        ];

        dd($informacion);

        /* $formIncidencia = [
            'proposed_solution' => $info->proposed_solution,
            'monitoring_manager' => $info->monitoring_manager,
            'replacement_out_of_time' => $info->replacement_out_of_time,
            'incident_delivery_date' => $info->incident_delivery_date,
            'days_of_incident_processing' => $info->days_of_incident_processing,
            'odc_mat_clean' => $info->odc_mat_clean,
            'cu_prod_clean' => $info->cu_prod_clean,
            'final_cost_of_clean_material' => $info->final_cost_of_clean_material,
            'odc_impression' => $info->odc_impression,
            'printing_cost_per_piece' => $info->printing_cost_per_piece,
            'cu_prod_impression' => $info->cu_prod_impression,
            'total_cost' => $info->total_cost,
            'id_quality_incidents' => $info->id_quality_incidents
        ]; */

        //dd($formIncidencia);
    }


}
