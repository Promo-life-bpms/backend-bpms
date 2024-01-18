<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodInformation extends Model
{
    use HasFactory;

    public $table = 'paymentmethodinformation';

    protected $fillable =[
        'id_user',
        'id_pursache_request'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function purchaserequest()
    {
        return $this->belongsTo(PurchaseRequest::class, 'id_pursache_request');
    }

}
