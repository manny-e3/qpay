<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => \Illuminate\Support\Facades\Hash::make('password')]
        );
    }
}
