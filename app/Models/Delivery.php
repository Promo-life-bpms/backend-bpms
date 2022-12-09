<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_delivery',
        'code_sale',
        'company',
        'type_operation',
        'planned_date',
        'effective_date',
        'status',
    ];

    public function productsDelivery()
    {
        return $this->hasMany(DeliveryProduct::class);
    }
}
