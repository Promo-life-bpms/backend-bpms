<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Eventuales extends Model
{
    use HasFactory;

    public $table  = 'eventuales';

    protected $fillable = ([
        'eventuales',
        'purchase_id'
    ]);

    public function purchase()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_id');
    }
}
