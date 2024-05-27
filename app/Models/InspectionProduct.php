<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionProduct extends Model
{
    use HasFactory;

    public $table = 'inspection_products';

    protected $fillable = [
        "inspection_id",
        "id_order_purchase_products",
        "odoo_product_id",
        "code_order",
        "quantity_selected"
    ];

    public function Inspection()
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function OrderPurchaseProducts()
    {
        return $this->belongsTo(OrderPurchaseProduct::class, 'id_order_purchase_products');
    }
}
