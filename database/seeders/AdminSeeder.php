<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{

    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'nafis@gmail.com'],
            [
                'name' => 'Nafis Chonchol',
                'phone' => '01641377742',
                'password' => Hash::make('nafis@gmail.com'),
            ]
        );
    }
}
