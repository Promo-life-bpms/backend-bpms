<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolutionOfTheIncidentForm extends Model
{
    use HasFactory;

    public $table = 'solution_of_the_incident_forms';

    protected $fillable = [
        'proposed_solution',
        'monitoring_manager',
        'replacement_out_of_time',
        'incident_delivery_date',
        'days_of_incident_processing',
        'odc_mat_clean',
        'cu_prod_clean',
        'final_cost_of_clean_material',
        'odc_impression',
        'printing_cost_per_piece',
        'cu_prod_impression',
        'total_cost',
        'id_quality_incidents',
    ];
    
    public function QualityIncidents()
    {
        return $this->belongsTo(QualityIncidentsForm::class, 'id_quality_incidents');
    }
}
