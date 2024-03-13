<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryDevolution extends Model
{
    use HasFactory;

    public  $table = 'history_devolution';

    protected $fillable = [
        'total_return',
        'status',
        'id_user',
        'id_purchase',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function purchase_request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'id_purchase');
    }
}
