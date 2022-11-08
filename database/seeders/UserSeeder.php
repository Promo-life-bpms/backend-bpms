<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users =  User::factory(10)->create();
        $roles =  Role::all();
        foreach ($users as $user) {
            $user->attachRole($roles[rand(0, count($roles) - 1)]);
        }
    }
}
