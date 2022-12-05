<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        "inspection_id",
        "product_id",
        "order_purchase_id",
        "quantity_selected"
    ];
}
