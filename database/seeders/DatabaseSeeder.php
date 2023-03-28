<?php

namespace Database\Seeders;

use App\Models\Sale;
use Illuminate\Database\Seeder;
use Laratrust\Laratrust;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       // $this->call(StatusSeeder::class);
        //$this->call(LaratrustSeeder::class);
      /*   $this->call(SaleSeeder::class); */
        $this->call(UserSeeder::class);
    }
}
