<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimationSmallBox extends Model
{
    use HasFactory;

    public $table = 'estimation_small_box';

    protected $fillable = [
        'total',
        'id_user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
