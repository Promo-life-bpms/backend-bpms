<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventualesMaquila extends Model
{
    use HasFactory;

    public $table = 'eventualesmaquila';

    protected $fillable = ([
        'eventualesmaquila',
        'purchase_id'
    ]);

    public function user()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_id');
    }
}
