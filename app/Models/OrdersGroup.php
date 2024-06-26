<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'code_order_oc',
        'code_order_ot',
        'code_sale',
        'description',
        'product_id_oc',
        'product_id_ot',
        'planned_date'
    ];
    protected $casts = [
        'code_order_ot' => 'array',
        'product_id_ot' => 'array',
    ];
}
