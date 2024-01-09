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
        'id_spents'
    ]);

    public function user()
    {
        return $this->belongsTo(Spent::class, 'id_spents');
    }
}
