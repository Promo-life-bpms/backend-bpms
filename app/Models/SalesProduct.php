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
        "provider",
        "logo",
        "quantity_ordered",
        "quantity_delivered",
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
