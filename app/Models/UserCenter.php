<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCenter extends Model
{
    use HasFactory;

    public $table = "user_has_center";

    protected $fillable = [
        'user_id',
        'center_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
        
}
