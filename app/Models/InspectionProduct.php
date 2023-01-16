<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        "inspection_id",
        "odoo_product_id",
        "code_order",
        "quantity_selected"
    ];
}
