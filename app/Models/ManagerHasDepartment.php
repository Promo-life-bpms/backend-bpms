<?php

namespace App\Models;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerHasDepartment extends Model
{
    use HasFactory;

    public $table = 'manager_has_departments';
    
    protected $fillable =
    [
        'id_user',
        'id_department',
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

