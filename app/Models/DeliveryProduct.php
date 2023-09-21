<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'reception_id',
        'code_delivery',
        'odoo_product_id',
        'product',
        'initial_demand',
        'done',
    ];

}
