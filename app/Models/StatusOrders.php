<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusOrders extends Model
{
    use HasFactory;

    public $table = 'status_orders';

    protected $fillable = [
        'status_id',
        'user_id',
        'code_order',
        'code_sale'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function order(){
        return $this->belongsTo(OrderPurchase::class, 'code_order');
    }
}
