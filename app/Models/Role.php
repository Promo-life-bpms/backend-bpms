<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    protected $fillable = ['name','display_name'];
    public $guarded = [];

    //Funcion para saber que usuarios tienen este role
    public function whatUsers()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

}
