<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentClosureForm extends Model
{
    use HasFactory;

    public $table = 'incident_closure_forms';

    protected $fillable = [
        'status',
        'application',
        'note_of_application',
        'responsible_for_final_monitoring',
        'final_status',
        'final_closing_date',
        'credit_note',
        'days_of_incident_process',
        'id_solution_incident',
    ];

    public function SolutionIncident()
    {
        return $this->belongsTo(SolutionOfTheIncidentForm::class, 'id_solution_incident');
    }
}
