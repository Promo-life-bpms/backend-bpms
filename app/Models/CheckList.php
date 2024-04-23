<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    use HasFactory;
    protected $fillable = [
        'code_sale',
        'order_com',
        'virtual',
        'arte',
        'logo',
        'quote_pro',
        'distribution',
        'delivery_address',
        'data_invoicing',
        'contact'

    ];
}
