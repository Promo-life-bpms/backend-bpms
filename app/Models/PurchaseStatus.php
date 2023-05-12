<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseStatus extends Model
{
    use HasFactory;

    public $table = "purchase_status";

    protected $fillable = [
        'name',
        'type',
        'position',
        'description',
    ];

  
}
