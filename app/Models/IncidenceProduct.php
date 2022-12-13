<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class IncidenceProduct extends Model
{
    use HasFactory;
    public $table = "incidence_products";
    protected $fillable = [
        'request',
        'notes',
        'product',
        'cost',
        'order_purchase_product_id',
        'quantity_selected',
        'incidence_id'
    ];
}
