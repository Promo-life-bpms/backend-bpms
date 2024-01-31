<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionConfirmationMaquilado extends Model
{
    use HasFactory;
    protected $fillable = [
        'code_order',
        'odoo_product_id',
        'quantity_maquilada',
        'decrease',
        'product_clean',
        'observations',
    ];

}
