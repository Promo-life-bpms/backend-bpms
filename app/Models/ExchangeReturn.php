<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeReturn extends Model
{
    use HasFactory;

    public $table = 'exchange_returns';

    protected $fillable = ([
        'total_return',
        'status',
        'confirmation_datetime',
        'confirmation_user_id',
        'description',
        'return_user_id',
        'purchase_id',
        'file_exchange_returns'
    ]);

    public function purchase()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'return_user_id');
    }
}
