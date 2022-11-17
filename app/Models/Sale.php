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
        'delivery_instructions',
        'delivery_time',
        'order_date',
        'additional_information',
        'sample_required',
        'tariff',
        "incidence",
        'commercial_name',
        'commercial_email',
        'commercial_odoo_id',
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
}
