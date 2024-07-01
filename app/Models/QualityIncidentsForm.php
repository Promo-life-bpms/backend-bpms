<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityIncidentsForm extends Model
{
    use HasFactory;

    public $table = 'quality_incidents_forms';

    protected $fillable = [
        'code_sale',
        'incidence_folio',
        'days_in_warehouse',
        'incident_date',
        'id_sale_product',
        'sale_product_quantity',
        'logo',
        'id_order_products',
        'order_product_quantity',
        'maquilador',
        'distributor',
        'correct_parts',
        'defective_parts',
        'defect_percentage',
        'responsible',
        'general_cause',
        'return_description'
    ];

    public function SaleProduct()
    {
        return $this->belongsTo(SalesProduct::class, 'id_sale_product');
    }

    public function OrderProduct()
    {
        return $this->belongsTo(OrderPurchaseProduct::class, 'id_order_products');
    }
}

