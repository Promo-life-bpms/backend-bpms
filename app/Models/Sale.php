<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    // Pedido de Venta
    use HasFactory;

    protected $fillable = [
        'code_sale',
        'name_sale',
        'sequence',
        'invoice_address',
        'delivery_address',
        'delivery_time',
        'delivery_instructions',
        'order_date',
        "incidence",
        'sample_required',
        'labeling',
        'additional_information',
        'tariff',
        'commercial_odoo_id',
        'commercial_name',
        'commercial_email',
        'total',
        'status_id',
    ];

    public function moreInformation()
    {
        return $this->hasOne(AdditionalSaleInformation::class);
    }

    public function saleProducts()
    {
        return $this->hasMany(SalesProduct::class);
    }

    public function orders()
    {
        return $this->hasMany(OrderPurchase::class, "code_sale", "code_sale");
    }

    public function currentStatus()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function incidences()
    {
        return $this->hasMany(Incidence::class, 'sale_id');
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'sale_id');
    }

    public function routeDeliveries()
    {
        return $this->hasMany(CodeOrderDeliveryRoute::class, 'code_sale', 'code_sale')->with('deliveryRoute', 'productDeliveryRoute');
    }
}
