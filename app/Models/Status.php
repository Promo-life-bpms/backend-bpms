<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    // Modelos de Estados
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'status',
        'slug'
    ];
}
