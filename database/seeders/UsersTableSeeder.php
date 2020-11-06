<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'dni' => '41234590',
            'name' => 'Federico Ruhl',
            'role' => 'admin',
            'email' => 'federicoruhl@gmail.com',
            'password' => bcrypt('federico')
        ]);

        User::factory()
            ->times(50)
            ->create();
    }
}
