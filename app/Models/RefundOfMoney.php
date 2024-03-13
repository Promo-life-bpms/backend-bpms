<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundOfMoney extends Model
{
    use HasFactory;

    public $table = 'refund_of_money';

    protected $fillable = [
        'total_returned',
        'total_spent',
        'period',
        'was_returned_to',
        'file',
        'id_user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
