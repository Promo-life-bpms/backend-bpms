<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesProduct extends Model
{
    // Productos de la orden de compra
    use HasFactory;

    protected $fillable = [
        "sale_id",
        "odoo_product_id",
        "product",
        "description",
        "customization",
        "provider",
        "logo",
        "key_product",
        "type_sale",
        "cost_labeling",
        "clean_product_cost",
        "quantity_ordered",
        "quantity_delivered",
        "quantity_invoiced",
        "unit_price",
        "subtotal"
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
