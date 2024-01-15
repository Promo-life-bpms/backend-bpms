<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporyCompany extends Model
{
    use HasFactory;

    public $table = 'tempory_company';

    protected $fillable = ([
        'name',
        'status',
        'id_user'
    ]);

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
