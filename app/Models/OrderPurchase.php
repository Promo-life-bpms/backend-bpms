<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPurchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'code_order',
        'code_sale',
        'provider_name',
        'sequence',
        'order_date',
        'planned_date',
        'deliver_in',
    ];
}
