<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionFiles extends Model
{
    use HasFactory;

    public $table = 'inspection_files';
    
    protected $fillable = [
        'files',
        'id_ins'
    ];

    public function Inspections(){
        return $this->belongsTo(Inspection::class, 'id_ins');
    }

}
