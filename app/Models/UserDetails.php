<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    public $table = 'user_details';

    protected $fillable = [
        'id_user',
        'id_department',
        'id_company'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }
}
