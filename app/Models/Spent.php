<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spent extends Model
{
    use HasFactory;

    public $table = "spents";

    protected $fillable = [
        'concept',
        'center_id',
        'outgo_type',
        'expense_type'
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
