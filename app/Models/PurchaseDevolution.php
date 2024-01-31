<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDevolution extends Model
{
    use HasFactory;

    public $table = "purchase_devolution";

    public function purchase_request()
    {
        return $this->hasOne(PurchaseRequest::class, 'purchase_request_id');
    }

    public function payment_method()
    {
        return $this->hasOne(PaymentMethod::class, 'payment_method_id');
    }
}
