<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_sale',
        'name_sale',
        'invoice_address',
        'delivery_address',
        'delivery_instructions',
        'delivery_time',
        'confirmation_date',
        'order_date',
        'additional_information',
        'commercial_name',
        'commercial_email',
        'commercial_odoo_id'
    ];

    public function aditionalInformation()
    {
        return $this->hasOne(AdditionalSaleInformation::class);
    }
}
