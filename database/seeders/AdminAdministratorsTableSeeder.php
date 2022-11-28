<?php

namespace Database\Seeders;

use App\Admin\Models\Administrator;
use App\Admin\Models\Role;
use Illuminate\Database\Seeder;

class AdminAdministratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 初始化超级管理员
        if (is_null(Administrator::find(Administrator::DEFAULT_ID))) {
            $administrator = Administrator::create([
                'id' => Administrator::DEFAULT_ID,
                'username' => 'admin',
                'password' => '$2y$10$.GGPaENWxSLC0/WTOhxWJuJHaWf1Uvv0cOX7WmTUVL3OtN.RKMfbO', // admin
                'name' => '超级管理员',
                'avatar' => null,
            ]);

            if ($role = Role::find(Role::ADMINISTRATOR_ID)) {
                $administrator->roles()->attach($role);
            }
        }
    }
}
