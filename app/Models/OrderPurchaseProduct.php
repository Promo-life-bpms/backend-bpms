<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPurchaseProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_purchase_id',
        "odoo_product_id",
        "product",
        "description",
        "planned_date",
        "company",
        "quantity",
        "quantity_invoiced",
        "quantity_delivered",
        "unit_price",
        "measurement_unit",
        "subtotal",
    ];

    public function orderPurchase()
    {
        return $this->belongsTo(OrderPurchase::class);
    }
}
