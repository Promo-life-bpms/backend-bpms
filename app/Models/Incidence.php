<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidence extends Model
{
    use HasFactory;

    protected $fillable = [
        "code_incidence",
        "code_sale",
        'area',
        'reason',
        'product_type',
        'evidence',
        'solution',
        'responsible',
        'solution_date',
        'comments',
        'elaborated',
        'signature_elaborated',
        'reviewed',
        'signature_reviewed',
        "description",
        'type_of_technique',
        'user_solution',
        'creation_date',
        'status',
        'commitment_date',
        'user_department',
        'sale_id'
    ];

    public function productsIncidence()
    {
        return $this->hasMany(IncidenceProduct::class, "incidence_id");
    }
}
