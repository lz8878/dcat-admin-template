<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminRolesTableSeeder::class,
            AdminPermissionsTableSeeder::class,
            AdminAdministratorsTableSeeder::class,
        ]);
    }
}
