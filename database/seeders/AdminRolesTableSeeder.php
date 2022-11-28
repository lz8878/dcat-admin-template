<?php

namespace Database\Seeders;

use App\Admin\Models\Role;
use Illuminate\Database\Seeder;

class AdminRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (is_null(Role::find(Role::ADMINISTRATOR_ID))) {
            Role::create([
                'id' => Role::ADMINISTRATOR_ID,
                'name' => '超级管理员',
                'slug' => Role::ADMINISTRATOR,
            ]);
        }
    }
}
