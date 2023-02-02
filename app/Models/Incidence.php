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
        "client",
        "requested_by",
        "description",
        "date_request",
        "company",
        "odoo_status",

        'internal_code_incidence',
        'area',
        'reason',
        'product_type',
        'type_of_technique',
        'responsible',
        'creation_date',
        'bpm_status',
        'evidence',
        'commitment_date',
        'solution',
        'solution_date',
        'user_id',
        'elaborated',
        'signature_elaborated',
        'reviewed',
        'signature_reviewed',
        "sale_id"
    ];

    public function productsIncidence()
    {
        return $this->hasMany(IncidenceProduct::class, "incidence_id");
    }
}
